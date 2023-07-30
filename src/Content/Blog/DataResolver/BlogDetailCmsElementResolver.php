<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\DataResolver;

use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class BlogDetailCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'blog-detail';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('id', $resolverContext->getRequest()->get('articleId'))
        );
        $criteria->addFilter(new OrFilter([
            new ContainsFilter('customFields.salesChannelIds', $resolverContext->getSalesChannelContext()->getSalesChannelId()),
            new EqualsFilter('customFields.salesChannelIds', null),
        ]));
        $criteria
            ->addAssociations(['blogAuthor', 'blogCategories'])
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

        $criteriaCollection = new CriteriaCollection();

        $criteriaCollection->add(
            'sas_blog',
            BlogEntriesDefinition::class,
            $criteria
        );

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        /** @var EntitySearchResult $sasBlog */
        $sasBlog = $result->get('sas_blog') ?? null;

        if ($sasBlog !== null && $sasBlog->first() !== null) {
            $slot->setData($sasBlog->first());
        }
    }
}
