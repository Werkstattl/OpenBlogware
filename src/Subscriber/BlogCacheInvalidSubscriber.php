<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Subscriber;

use Shopware\Core\Content\Category\SalesChannel\CategoryRoute;
use Shopware\Core\Content\Cms\CmsPageEvents;
use Shopware\Core\Content\Seo\Event\SeoEvents;
use Shopware\Core\Content\Seo\SeoUrlTemplate\SeoUrlTemplateCollection;
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
use Werkl\OpenBlogware\Content\Blog\BlogEntryCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogSeoUrlRoute;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryCollection;
use Werkl\OpenBlogware\Controller\BlogController;
use Werkl\OpenBlogware\Controller\BlogRssController;
use Werkl\OpenBlogware\Controller\BlogSearchController;

/**
 * After you change the SEO Template within the SEO settings, we need to re-generate all existing URLs.
 * All old URL's should match the new saved SEO Template pattern.
 */
class BlogCacheInvalidSubscriber implements EventSubscriberInterface
{
    /**
     * @param EntityRepository<BlogCategoryCollection> $categoryRepository
     * @param EntityRepository<BlogEntryCollection> $blogRepository
     * @param EntityRepository<SeoUrlTemplateCollection> $seoUrlTemplateRepository
     */
    public function __construct(
        private readonly SeoUrlUpdater $seoUrlUpdater,
        private readonly EntityRepository $categoryRepository,
        private readonly EntityRepository $blogRepository,
        private readonly EntityRepository $seoUrlTemplateRepository,
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageEvents::PAGE_WRITTEN_EVENT => [
                ['onUpdateSeoUrlCmsPage', 10],
                ['onUpdateInvalidateCacheCmsPage', 11],
            ],
            'werkl_blog_entry.written' => [
                ['onUpdateSeoUrl', 10],
                ['onUpdateInvalidateCache', 11],
            ],
            'werkl_blog_entry.deleted' => [
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
        if (empty($blogIds)) {
            return;
        }

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, array_values($blogIds));
    }

    public function onUpdateInvalidateCacheCmsPage(EntityWrittenEvent $event): void
    {
        $blogIds = $this->getBlogIds($event);
        if (empty($blogIds)) {
            return;
        }

        $this->invalidateCache(array_values($blogIds));

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
    public function updateSeoUrlForAllArticles(EntityWrittenEvent $event): void
    {
        $context = $event->getContext();

        $criteria = (new Criteria($event->getIds()))
            ->addFilter(new EqualsFilter('entityName', BlogEntryDefinition::ENTITY_NAME));

        if ($this->seoUrlTemplateRepository->searchIds($criteria, $context)->getTotal() < 1) {
            return;
        }

        /** @var list<string> $ids */
        $ids = $this->blogRepository->searchIds(new Criteria(), $context)->getIds();

        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $ids);
    }

    /**
     * Invalidate blog category cache
     */
    private function invalidateCacheCategory(Context $context): void
    {
        $catIds = $this->getBlogCategoryIds($context);

        // invalidates the category route cache when a category changed
        $this->cacheInvalidator->invalidate(
            array_map([CategoryRoute::class, 'buildName'], $catIds)
        );
    }

    /**
     * @return array<string>
     */
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
     *
     * @param array<string> $articleIds
     */
    private function invalidateCache(array $articleIds): void
    {
        $this->cacheInvalidator->invalidate(
            array_map([BlogController::class, 'buildName'], $articleIds)
        );

        $this->cacheInvalidator->invalidate([
            'product-suggest-route',
            'product-search-route',
            BlogSearchController::ALL_TAG,
            BlogRssController::ALL_TAG,
        ]);

        $cmsBlogDetailPageId = $this->systemConfigService->get('WerklOpenBlogware.config.cmsBlogDetailPage');
        if (!\is_string($cmsBlogDetailPageId)) {
            return;
        }

        $this->cacheInvalidator->invalidate(
            array_map([EntityCacheKeyGenerator::class, 'buildCmsTag'], [$cmsBlogDetailPageId])
        );
    }

    /**
     * @return list<string>
     */
    private function getBlogIds(EntityWrittenEvent $event): array
    {
        /** @var list<string> $ids */
        $ids = $this->blogRepository->searchIds(
            (new Criteria())->addFilter(new EqualsAnyFilter('cmsPageId', $event->getIds())),
            $event->getContext()
        )->getIds();

        return $ids;
    }
}
