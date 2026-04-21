<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

class BlogSingleSelectCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'blog-single-select';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $blogEntryConfig = $config->get('blogEntry') ?? null;

        if ($blogEntryConfig === null) {
            return null;
        }

        $blogId = $blogEntryConfig->getStringValue();
        $criteria = new Criteria([$blogId]);

        $criteria->addFilter(new BlogEntryActiveFilter($resolverContext->getSalesChannelContext()->getSalesChannelId()));

        $criteria->addAssociations([
            'blogAuthor',
            'blogAuthor.media',
            'blogAuthor.blogEntries',
            'blogCategories',
            'tags',
        ]);

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
        $werklBlogs = $result->get(BlogEntryDefinition::ENTITY_NAME . '_' . $slot->getUniqueIdentifier());

        if ($werklBlogs === null || $werklBlogs->first() === null) {
            return;
        }

        /** @var BlogEntryEntity $werklBlog */
        $werklBlog = $werklBlogs->first();

        $slot->setData($werklBlog);
    }
}
