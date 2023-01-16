<?php declare(strict_types=1);

namespace Sas\BlogModule\Subscriber;

use Sas\BlogModule\Content\Blog\BlogSeoUrlRoute;
use Shopware\Core\Content\Category\SalesChannel\CachedCategoryRoute;
use Shopware\Core\Content\Seo\Event\SeoEvents;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * After you change the SEO Template within the SEO settings, we need to re-generate all existing URLs.
 * All old URL's should match the new saved SEO Template pattern.
 */
class BlogCacheInvalidSubscriber implements EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;

    private EntityRepository $categoryRepository;

    private CacheInvalidator $cacheInvalidator;

    public function __construct(
        SeoUrlUpdater $seoUrlUpdater,
        EntityRepository $categoryRepository,
        CacheInvalidator $cacheInvalidator
    ) {
        $this->seoUrlUpdater = $seoUrlUpdater;
        $this->categoryRepository = $categoryRepository;
        $this->cacheInvalidator = $cacheInvalidator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sas_blog_entries.written' => [
                ['updateSeoUrl', 10],
                ['invalidateCacheCategory', 11],
            ],
            'sas_blog_entries.deleted' => [
                ['updateSeoUrl', 10],
                ['invalidateCacheCategory', 11],
            ],
            SeoEvents::SEO_URL_TEMPLATE_WRITTEN_EVENT => [
                ['updateSeoUrlForAllArticles', 10],
                ['invalidateCacheCategory', 11],
            ],
            SeoEvents::SEO_URL_TEMPLATE_DELETED_EVENT => [
                ['updateSeoUrlForAllArticles', 10],
                ['invalidateCacheCategory', 11],
            ],
        ];
    }

    public function updateSeoUrl(EntityWrittenEvent $event): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    public function updateSeoUrlForAllArticles(EntityWrittenEvent $event): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, []);
    }

    /**
     * Invalidate blog category cache
     */
    public function invalidateCacheCategory(EntityWrittenEvent $event): void
    {
        $catIds = $this->getBlogCategoryIds($event->getContext());

        // invalidates the category route cache when a category changed
        $this->cacheInvalidator->invalidate(
            array_map([CachedCategoryRoute::class, 'buildName'], $catIds)
        );
    }

    private function getBlogCategoryIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('cmsPage.sections.blocks.type', 'blog-listing'));
        $criteria->addAssociation('cmsPage.sections.blocks');

        return $this->categoryRepository->search($criteria, $context)->getIds();
    }
}
