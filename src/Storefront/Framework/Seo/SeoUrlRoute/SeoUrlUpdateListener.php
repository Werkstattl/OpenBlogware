<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Storefront\Framework\Seo\SeoUrlRoute;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
        $blogEntryIds = [];

        foreach ($event->getWriteResults() as $writeResult) {
            if ($writeResult->getOperation() !== EntityWriteResult::OPERATION_INSERT) {
                continue;
            }

            $salesChannelId = $writeResult->getPrimaryKey();

            if (!\is_string($salesChannelId)) {
                continue;
            }

            $criteria = new Criteria();
            $criteria->addFilter(new BlogEntryActiveFilter($salesChannelId));

            $blogEntryIds = [
                ...$blogEntryIds,
                ...$this->blogRepository->searchIds($criteria, $event->getContext())->getIds(),
            ];
        }

        if ($blogEntryIds === []) {
            return;
        }

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, array_values(array_unique($blogEntryIds)));
    }
}
