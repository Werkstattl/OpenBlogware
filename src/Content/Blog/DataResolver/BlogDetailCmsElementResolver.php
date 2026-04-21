<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

class BlogDetailCmsElementResolver extends AbstractCmsElementResolver
{
    final public const TYPE = 'blog-detail';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = $this->createCriteria($resolverContext);
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

    private function createCriteria(ResolverContext $resolverContext): Criteria
    {
        $criteria = new Criteria([$resolverContext->getRequest()->get('articleId')]);

        $criteria->addFilter(new BlogEntryActiveFilter(
            $resolverContext->getSalesChannelContext()->getSalesChannelId(),
            false
        ));

        $criteria
            ->addAssociations(['blogAuthor', 'blogCategories', 'tags'])
            ->addAssociation('cmsPage.sections.backgroundMedia')
            ->addAssociation('cmsPage.sections.blocks.backgroundMedia');
        $criteria
            ->getAssociation('cmsPage.sections')
            ->addSorting(new FieldSorting('position', FieldSorting::ASCENDING));
        $criteria
            ->getAssociation('cmsPage.sections.blocks')
            ->addSorting(new FieldSorting('position', FieldSorting::ASCENDING));
        $criteria
            ->getAssociation('cmsPage.sections.blocks.slots')
            ->addSorting(new FieldSorting('slot', FieldSorting::ASCENDING));

        return $criteria;
    }
}
