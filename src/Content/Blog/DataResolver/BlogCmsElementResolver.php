<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\DataResolver;

use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Content\Blog\BlogListingFilterBuildEvent;
use Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlogCmsElementResolver extends AbstractCmsElementResolver
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getType(): string
    {
        return 'blog';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        /* get the config from the element */
        $config = $slot->getFieldConfig();

        $dateTime = new \DateTime();

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );

        $criteria->addAssociations([
            'blogAuthor',
            'blogAuthor.media',
            'blogAuthor.blogEntries',
            'blogCategories',
        ]);

        $criteria->addSorting(
            new FieldSorting('publishedAt', FieldSorting::DESCENDING)
        );

        if ($config->has('showType') && $config->get('showType')->getValue() === 'select') {
            $blogCategories = $config->get('blogCategories') ? $config->get('blogCategories')->getValue() : [];

            $criteria->addFilter(new EqualsAnyFilter('blogCategories.id', $blogCategories));
        }

        $request = $resolverContext->getRequest();
        $limit = 1;

        if ($config->has('paginationCount') && $config->get('paginationCount')->getValue()) {
            $limit = (int) $config->get('paginationCount')->getValue();
        }

        $context = $resolverContext->getSalesChannelContext();

        $this->handlePagination($limit, $request, $criteria, $context);

        $this->eventDispatcher->dispatch(
            new BlogMainFilterEvent($request, $criteria, $context),
            BlogListingFilterBuildEvent::BLOG_MAIIN_FILTER_EVENT
        );

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
        $slot->setData($result->get('sas_blog'));
    }

    private function handlePagination(int $limit, Request $request, Criteria $criteria, SalesChannelContext $context): void
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
