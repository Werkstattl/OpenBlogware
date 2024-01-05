<?php declare(strict_types=1);

namespace Sas\BlogModule\Subscriber;

use Sas\BlogModule\Content\Blog\BlogSeoUrlRoute;
use Sas\BlogModule\Controller\CachedBlogController;
use Sas\BlogModule\Controller\CachedBlogRssController;
use Sas\BlogModule\Controller\CachedBlogSearchController;
use Shopware\Core\Content\Category\SalesChannel\CachedCategoryRoute;
use Shopware\Core\Content\Cms\CmsPageEvents;
use Shopware\Core\Content\Seo\Event\SeoEvents;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * After you change the SEO Template within the SEO settings, we need to re-generate all existing URLs.
 * All old URL's should match the new saved SEO Template pattern.
 */
class BlogCacheInvalidSubscriber implements EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;

    private EntityRepository $categoryRepository;

    private EntityRepository $blogRepository;

    private CacheInvalidator $cacheInvalidator;

    private SystemConfigService $systemConfigService;

    public function __construct(
        SeoUrlUpdater $seoUrlUpdater,
        EntityRepository $categoryRepository,
        EntityRepository $blogRepository,
        CacheInvalidator $cacheInvalidator,
        SystemConfigService $systemConfigService
    ) {
        $this->seoUrlUpdater = $seoUrlUpdater;
        $this->categoryRepository = $categoryRepository;
        $this->blogRepository = $blogRepository;
        $this->cacheInvalidator = $cacheInvalidator;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageEvents::PAGE_WRITTEN_EVENT => [
                ['onUpdateSeoUrlCmsPage', 10],
                ['onUpdateInvalidateCacheCmsPage', 11],
            ],
            'sas_blog_entries.written' => [
                ['onUpdateSeoUrl', 10],
                ['onUpdateInvalidateCache', 11],
            ],
            'sas_blog_entries.deleted' => [
                ['onDeleteSeoUrl', 10],
                ['onDeleteInvalidateCache', 11],
            ],
            SeoEvents::SEO_URL_TEMPLATE_WRITTEN_EVENT => [
                ['updateSeoUrlForAllArticles', 10],
            ],
        ];
    }

    public function onUpdateSeoUrlCmsPage(EntityWrittenEvent $event): void
    {
        $blogIds = $this->getBlogIds($event);

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $blogIds);
    }

    public function onUpdateInvalidateCacheCmsPage(EntityWrittenEvent $event): void
    {
        $blogIds = $this->getBlogIds($event);

        $this->invalidateCache($blogIds);

        $this->invalidateCacheCategory($event->getContext());
    }

    /**
     * When a blog article created or updated we will generate the SeoUrl for it
     */
    public function onUpdateSeoUrl(EntityWrittenEvent $event): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    /**
     * When a blog article deleted we will mark as deleted the SeoUrl
     */
    public function onDeleteSeoUrl(EntityDeletedEvent $event): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }

    /**
     * Invalidate blog cms cache when create or update
     */
    public function onUpdateInvalidateCache(EntityWrittenEvent $event): void
    {
        $this->invalidateCache($event->getIds());

        $this->invalidateCacheCategory($event->getContext());
    }

    /**
     * Invalidate blog cms cache when delete article
     */
    public function onDeleteInvalidateCache(EntityDeletedEvent $event): void
    {
        $this->invalidateCache($event->getIds());

        $this->invalidateCacheCategory($event->getContext());
    }

    /**
     * When update SEO template in the settings, we will update all SEO URLs for the blog articles
     */
    public function updateSeoUrlForAllArticles(): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, []);
    }

    /**
     * Invalidate blog category cache
     */
    private function invalidateCacheCategory(Context $context): void
    {
        $catIds = $this->getBlogCategoryIds($context);

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

    /**
     * Invalidate cache
     */
    private function invalidateCache(array $articleIds): void
    {
        $this->cacheInvalidator->invalidate(
            array_map([CachedBlogController::class, 'buildName'], $articleIds)
        );

        $this->cacheInvalidator->invalidate([
            'product-suggest-route',
            'product-search-route',
            CachedBlogSearchController::SEARCH_TAG,
            CachedBlogRssController::RSS_TAG,
        ]);

        $cmsBlogDetailPageId = $this->systemConfigService->get('SasBlogModule.config.cmsBlogDetailPage');
        if (!\is_string($cmsBlogDetailPageId)) {
            return;
        }

        $this->cacheInvalidator->invalidate(
            array_map([EntityCacheKeyGenerator::class, 'buildCmsTag'], [$cmsBlogDetailPageId])
        );
    }

    private function getBlogIds(EntityWrittenEvent $event): array
    {
        return $this->blogRepository->searchIds(
            (new Criteria())->addFilter(new EqualsAnyFilter('cmsPageId', $event->getIds())),
            $event->getContext()
        )->getIds();
    }
}
