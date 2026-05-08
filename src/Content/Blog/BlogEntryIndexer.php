<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog;

use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\Events\BlogIndexerEvent;

class BlogEntryIndexer extends EntityIndexer
{
    /**
     * @param EntityRepository<BlogEntryCollection> $repository
     */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $repository
    ) {
    }

    public function getName(): string
    {
        return 'werkl_blog.entry.indexer';
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $blogEntryUpdates = $event->getPrimaryKeys(BlogEntryDefinition::ENTITY_NAME);

        if ($blogEntryUpdates === []) {
            return null;
        }

        return new BlogEntryIndexingMessage($blogEntryUpdates, null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();

        if (!\is_array($ids)) {
            return;
        }

        $ids = array_values(array_unique(array_filter($ids)));

        if ($ids === []) {
            return;
        }

        $this->eventDispatcher->dispatch(new BlogIndexerEvent($ids, $message->getContext(), $message->getSkip()));
    }

    public function iterate($offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);

        /** @var array<string> $ids */
        $ids = $iterator->fetch();

        if ($ids === []) {
            return null;
        }

        return new BlogEntryIndexingMessage(array_values($ids), $iterator->getOffset());
    }

    public function getTotal(): int
    {
        return $this->iteratorFactory->createIterator($this->repository->getDefinition())->fetchCount();
    }

    public function getDecorated(): EntityIndexer
    {
        throw new DecorationPatternException(static::class);
    }
}
