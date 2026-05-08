<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Storefront\Framework\Seo\SeoUrlRoute;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\System\SalesChannel\SalesChannelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntryCollection;
use Werkl\OpenBlogware\Content\Blog\BlogSeoUrlRoute;
use Werkl\OpenBlogware\Content\Blog\Events\BlogIndexerEvent;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

class SeoUrlUpdateListener implements EventSubscriberInterface
{
    /**
     * @param EntityRepository<BlogEntryCollection> $blogRepository
     */
    public function __construct(
        private readonly SeoUrlUpdater $seoUrlUpdater,
        private readonly EntityRepository $blogRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlogIndexerEvent::class => 'updateBlogUrls',
            SalesChannelEvents::SALES_CHANNEL_WRITTEN => 'onSalesChannelWritten',
        ];
    }

    public function updateBlogUrls(BlogIndexerEvent $event): void
    {
        if (\count($event->getIds()) === 0) {
            return;
        }

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    public function onSalesChannelWritten(EntityWrittenEvent $event): void
    {
        $salesChannelIds = array_filter(array_map(
            fn (EntityWriteResult $writeResult): ?string => $writeResult->getOperation() === EntityWriteResult::OPERATION_INSERT ? $writeResult->getPrimaryKey() : null,
            $event->getWriteResults()
        ));

        if ($salesChannelIds === []) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, array_map(
            fn (string $salesChannelId) => new BlogEntryActiveFilter($salesChannelId),
            $salesChannelIds
        )));

        $blogEntryIds = $this->blogRepository->searchIds($criteria, $event->getContext())->getIds();

        if ($blogEntryIds === []) {
            return;
        }

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, array_values(array_unique($blogEntryIds)));
    }
}
