<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogCategory;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableTransaction;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\ChildCountUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\TreeUpdater;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlogCategoryIndexer extends EntityIndexer
{
    final public const CHILD_COUNT_UPDATER = 'werkl_blog.category.child-count';
    final public const TREE_UPDATER = 'werkl_blog.category.tree';
    private const UPDATE_IDS_CHUNK_SIZE = 50;

    /**
     * @param EntityRepository<BlogCategoryCollection> $repository
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $repository,
        private readonly ChildCountUpdater $childCountUpdater,
        private readonly TreeUpdater $treeUpdater,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function getName(): string
    {
        return 'werkl_blog.category.indexer';
    }

    public function getTotal(): int
    {
        return $this->getIterator(null)->fetchCount();
    }

    public function iterate($offset): ?EntityIndexingMessage
    {
        $iterator = $this->getIterator($offset);

        /** @var array<string> $ids */
        $ids = $iterator->fetch();

        if ($ids === []) {
            return null;
        }

        return new BlogCategoryIndexingMessage(
            data: array_values($ids),
            offset: $iterator->getOffset()
        );
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $categoryEvent = $event->getEventByEntityName(BlogCategoryDefinition::ENTITY_NAME);

        if (!$categoryEvent) {
            return null;
        }

        $ids = $categoryEvent->getIds();
        $idsWithChangedParentIds = [];
        foreach ($categoryEvent->getWriteResults() as $result) {
            if (!$result->getExistence()) {
                continue;
            }
            $state = $result->getExistence()->getState();

            if (isset($state['parent_id']) && is_string($state['parent_id'])) {
                $ids[] = Uuid::fromBytesToHex($state['parent_id']);
            }

            $payload = $result->getPayload();
            if (\array_key_exists('parentId', $payload) && (is_string($payload['parentId']) || $payload['parentId'] === null) && \array_key_exists('id', $payload) && is_string($payload['id'])) {
                if ($payload['parentId'] !== null) {
                    $ids[] = $payload['parentId'];
                }
                $idsWithChangedParentIds[] = $payload['id'];
            }
        }

        if ($ids === []) {
            return null;
        }

        if ($idsWithChangedParentIds !== []) {
            $this->treeUpdater->batchUpdate(
                $idsWithChangedParentIds,
                BlogCategoryDefinition::ENTITY_NAME,
                $event->getContext(),
                true
            );
        }

        $children = $this->fetchChildren($ids, $event->getContext()->getVersionId());
        $ids = array_unique(array_merge($ids, $children));

        $chunks = \array_chunk($ids, self::UPDATE_IDS_CHUNK_SIZE);
        $idsForReturnedMessage = array_shift($chunks);

        foreach ($chunks as $chunk) {
            $childrenIndexingMessage = new BlogCategoryIndexingMessage($chunk, null, $event->getContext());
            $childrenIndexingMessage->setIndexer($this->getName());
            EntityIndexerRegistry::addSkips($childrenIndexingMessage, $event->getContext());

            $this->messageBus->dispatch($childrenIndexingMessage);
        }

        return new BlogCategoryIndexingMessage($idsForReturnedMessage, null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();
        if (!\is_array($ids)) {
            return;
        }

        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) {
            return;
        }

        $context = $message->getContext();

        RetryableTransaction::retryable($this->connection, function () use ($message, $ids, $context): void {
            if ($message->allow(self::CHILD_COUNT_UPDATER)) {
                // listen to parent id changes
                $this->childCountUpdater->update(BlogCategoryDefinition::ENTITY_NAME, $ids, $context);
            }

            if ($message->allow(self::TREE_UPDATER)) {
                $this->treeUpdater->batchUpdate(
                    $ids,
                    BlogCategoryDefinition::ENTITY_NAME,
                    $context,
                    !$message->isFullIndexing
                );
            }
        });

        $this->eventDispatcher->dispatch(new BlogCategoryIndexerEvent($ids, $context, $message->getSkip(), $message->isFullIndexing));
    }

    public function getOptions(): array
    {
        return [
            self::CHILD_COUNT_UPDATER,
            self::TREE_UPDATER,
        ];
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(static::class);
    }

    /**
     * @param array<string> $categoryIds
     *
     * @return list<string>
     */
    private function fetchChildren(array $categoryIds, string $versionId): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT LOWER(HEX(category.id))');
        $query->from('werkl_blog_category', 'category');

        $wheres = [];
        foreach ($categoryIds as $id) {
            $key = 'path' . $id;
            $wheres[] = 'category.path LIKE :' . $key;
            $query->setParameter($key, '%|' . $id . '|%');
        }

        $query->andWhere('(' . implode(' OR ', $wheres) . ')');
        $query->andWhere('category.version_id = :version');
        $query->setParameter('version', Uuid::fromHexToBytes($versionId));

        /** @var list<string> $result */
        $result = $query->executeQuery()->fetchFirstColumn();

        return $result;
    }

    /**
     * @param array{offset: int|null}|null $offset
     */
    private function getIterator(?array $offset): IterableQuery
    {
        return $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);
    }
}
