<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog;

use Sas\BlogModule\Content\Blog\Events\BlogIndexerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexingMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BlogEntitiesIndexer extends EntityIndexer
{
    private EventDispatcherInterface $eventDispatcher;

    private IteratorFactory $iteratorFactory;

    private EntityRepositoryInterface $repository;

    public function getName(): string
    {
        return 'sas.blog.entities.indexer';
    }

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        IteratorFactory $iteratorFactory,
        EntityRepositoryInterface $repository
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->iteratorFactory = $iteratorFactory;
        $this->repository = $repository;
    }

    public function update(EntityWrittenContainerEvent $event): ?EntityIndexingMessage
    {
        $blogEntriesUpdates = $event->getPrimaryKeys(BlogEntriesDefinition::ENTITY_NAME);
        if (count($blogEntriesUpdates) === 0) {
            return null;
        }

        return new BlogEntriesIndexingMessage(array_values($blogEntriesUpdates), null, $event->getContext());
    }

    public function handle(EntityIndexingMessage $message): void
    {
        $ids = $message->getData();

        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return;
        }

       $this->eventDispatcher->dispatch(new BlogIndexerEvent($ids, $message->getContext(), $message->getSkip()));
    }

    public function iterate($offset): ?EntityIndexingMessage
    {
        $iterator = $this->iteratorFactory->createIterator($this->repository->getDefinition(), $offset);

        $ids = $iterator->fetch();

        if (empty($ids)) {
            return null;
        }

        return new BlogEntriesIndexingMessage(array_values($ids), $iterator->getOffset());
    }
}
