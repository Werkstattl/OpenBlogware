<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\DataResolver;

use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Content\Blog\Events\NewestListingCriteriaEvent;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlogNewestListingCmsElementResolver extends AbstractCmsElementResolver
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns the definition of the element.
     * Usually it's name of the element component.
     */
    public function getType(): string
    {
        return 'blog-newest-listing';
    }

    /**
     * Prepares the criteria object
     * It gets element configuration
     * It creates a new criteria instance based on the configuration
     * It dispatches an event to modify the criteria object
     * It creates criteria collection based on the criteria
     * It returns the criteria collection
     */
    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $request = $resolverContext->getRequest();
        $context = $resolverContext->getSalesChannelContext();

        $criteria = $this->createCriteria($config, $context);
        $this->eventDispatcher->dispatch(new NewestListingCriteriaEvent($request, $criteria, $context));

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add(
            BlogEntriesDefinition::ENTITY_NAME,
            BlogEntriesDefinition::class,
            $criteria
        );

        return $criteriaCollection;
    }

    /**
     * Perform additional logic on the data that has been resolved
     * It sets the resolved data to the cms slot
     */
    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $sasBlog = $result->get(BlogEntriesDefinition::ENTITY_NAME);

        if (!$sasBlog instanceof EntitySearchResult) {
            return;
        }

        $slot->setData($sasBlog);
    }

    /**
     * Create criteria based on the configuration
     * It creates an instance of the criteria class
     * It sets filter to get only active entries
     * It sets filter to get only entries with published date in the past
     * It sets sorting to get the newest entries first
     * It sets associations to get the blog author and the blog category
     * It checks if the configuration has categories and sets filter to get only entries with the given category
     * It checks if the configuration has a limit then it sets the limit
     * It returns the criteria
     */
    private function createCriteria(FieldConfigCollection $config, SalesChannelContext $salesChannelContext): Criteria
    {
        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new RangeFilter('publishedAt', [
            RangeFilter::LTE => (new \DateTime())->format(\DATE_ATOM),
        ]));
        $criteria->addFilter(new OrFilter([
            new ContainsFilter('customFields.salesChannelIds', $salesChannelContext->getSalesChannelId()),
            new EqualsFilter('customFields.salesChannelIds', null),
        ]));

        $criteria->addSorting(new FieldSorting('publishedAt', FieldSorting::DESCENDING));

        $criteria->addAssociations([
            'blogAuthor',
            'blogAuthor.media',
            'blogAuthor.blogEntries',
            'blogCategories',
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

        $criteria->setLimit($limit);

        return $criteria;
    }
}
