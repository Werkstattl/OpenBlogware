<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Storefront\Framework\Seo\SeoUrlRoute;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Werkl\OpenBlogware\Content\Blog\BlogSeoUrlRoute;
use Werkl\OpenBlogware\Content\Blog\Events\BlogIndexerEvent;

class SeoUrlUpdateListener implements EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;

    private EntityRepository $blogRepository;

    /**
     * @internal
     */
    public function __construct(
        SeoUrlUpdater $seoUrlUpdater,
        EntityRepository $blogRepository
    ) {
        $this->seoUrlUpdater = $seoUrlUpdater;
        $this->blogRepository = $blogRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlogIndexerEvent::class => 'updateBlogUrls',
            'sales_channel.written' => 'onCreateNewSalesChannel',
        ];
    }

    public function updateBlogUrls(BlogIndexerEvent $event): void
    {
        if (\count($event->getIds()) === 0) {
            return;
        }

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    public function onCreateNewSalesChannel(EntityWrittenEvent $event): void
    {
        if (\count($event->getIds()) === 0) {
            return;
        }

        $blogArticlesIds = $this->getBlogArticlesIds($event->getContext());
        if (\count($blogArticlesIds) === 0) {
            return;
        }

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $blogArticlesIds);
    }

    private function getBlogArticlesIds(Context $context): array
    {
        $criteria = new Criteria();

        $dateTime = new \DateTime();
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );

        return $this->blogRepository->searchIds($criteria, $context)->getIds();
    }
}
