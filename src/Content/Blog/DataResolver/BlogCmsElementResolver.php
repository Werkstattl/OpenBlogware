<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\Events\BlogListingFilterBuildEvent;
use Werkl\OpenBlogware\Content\Blog\Events\BlogMainFilterEvent;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

class BlogCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function getType(): string
    {
        return 'blog';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $context = $resolverContext->getSalesChannelContext();
        $criteria = new Criteria();

        $criteria->addFilter(new BlogEntryActiveFilter($context->getSalesChannelId()));

        $criteria->addAssociations([
            'blogAuthor',
            'blogAuthor.media',
            'blogAuthor.blogEntries',
            'blogCategories',
            'tags',
        ]);

        $criteria->addSorting(
            new FieldSorting('publishedAt', FieldSorting::DESCENDING)
        );

        $showTypeConfig = $config->get('showType') ?? null;
        $blogCategoriesConfig = null;
        $showTagsConfig = $config->get('showTags') ?? null;
        $blogTagsConfig = null;

        if ($showTypeConfig !== null && $showTypeConfig->getValue() === 'select') {
            $blogCategoriesConfig = $config->get('blogCategories') ?? null;
        }

        if ($showTagsConfig !== null && $showTagsConfig->getValue() === 'select') {
            $blogTagsConfig = $config->get('blogTags') ?? null;
        }

        if ($blogCategoriesConfig !== null && \is_array($blogCategoriesConfig->getValue())) {
            $criteria->addFilter(new EqualsAnyFilter('blogCategories.id', $blogCategoriesConfig->getValue()));
        }

        if ($blogTagsConfig !== null && \is_array($blogTagsConfig->getValue())) {
            $criteria->addFilter(new EqualsAnyFilter('tags.id', $blogTagsConfig->getValue()));
        }

        $request = $resolverContext->getRequest();
        $limit = 1;

        $paginationCountConfig = $config->get('paginationCount') ?? null;

        if ($paginationCountConfig !== null && $paginationCountConfig->getValue()) {
            $limit = (int) $paginationCountConfig->getValue();
        }

        $this->handlePagination($limit, $request, $criteria);

        $this->eventDispatcher->dispatch(
            new BlogMainFilterEvent($request, $criteria, $context),
            BlogListingFilterBuildEvent::BLOG_MAIN_FILTER_EVENT
        );

        $criteriaCollection = new CriteriaCollection();

        $criteriaCollection->add(
            BlogEntryDefinition::ENTITY_NAME . '_' . $slot->getUniqueIdentifier(),
            BlogEntryDefinition::class,
            $criteria
        );

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $werklBlog = $result->get(BlogEntryDefinition::ENTITY_NAME . '_' . $slot->getUniqueIdentifier());

        if (!$werklBlog instanceof EntitySearchResult) {
            return;
        }

        $slot->setData($werklBlog);
    }

    private function handlePagination(int $limit, Request $request, Criteria $criteria): void
    {
        $page = $this->getPage($request);

        $criteria->setOffset(($page - 1) * $limit);
        $criteria->setLimit($limit);
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
    }

    private function getPage(Request $request): int
    {
        $page = $request->query->getInt('p', 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('p', $page);
        }

        return $page <= 0 ? 1 : $page;
    }
}
