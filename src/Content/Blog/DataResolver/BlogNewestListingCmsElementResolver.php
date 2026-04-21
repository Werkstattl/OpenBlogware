<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\Events\NewestListingCriteriaEvent;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

class BlogNewestListingCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function getType(): string
    {
        return 'blog-newest-listing';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $request = $resolverContext->getRequest();
        $context = $resolverContext->getSalesChannelContext();

        $criteria = $this->createCriteria($config, $context);
        $this->eventDispatcher->dispatch(new NewestListingCriteriaEvent($request, $criteria, $context));

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

    private function createCriteria(FieldConfigCollection $config, SalesChannelContext $salesChannelContext): Criteria
    {
        $criteria = new Criteria();

        $criteria->addFilter(new BlogEntryActiveFilter($salesChannelContext->getSalesChannelId()));

        $criteria->addSorting(new FieldSorting('publishedAt', FieldSorting::DESCENDING));

        $criteria->addAssociations([
            'blogAuthor',
            'blogAuthor.media',
            'blogAuthor.blogEntries',
            'blogCategories',
            'tags',
        ]);

        $showTypeConfig = $config->get('showType') ?? null;
        $blogCategoriesConfig = null;

        if ($showTypeConfig !== null && $showTypeConfig->getValue() === 'select') {
            $blogCategoriesConfig = $config->get('blogCategories') ?? null;
        }

        if ($blogCategoriesConfig !== null && \is_array($blogCategoriesConfig->getValue())) {
            $criteria->addFilter(new EqualsAnyFilter('blogCategories.id', $blogCategoriesConfig->getValue()));
        }

        $limit = 1;
        $itemCountConfig = $config->get('itemCount') ?? null;

        if ($itemCountConfig !== null && $itemCountConfig->getValue()) {
            $limit = (int) $itemCountConfig->getValue();
        }

        $offset = 0;
        $offsetCountConfig = $config->get('offsetCount') ?? null;

        if ($offsetCountConfig !== null && $offsetCountConfig->getValue()) {
            $offset = (int) $offsetCountConfig->getValue();
        }

        $criteria->setLimit($limit);
        $criteria->setOffset($offset);

        return $criteria;
    }
}
