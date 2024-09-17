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
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\Events\CategoriesCriteriaEvent;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryDefinition;

class BlogCategoriesCmsElementResolver extends AbstractCmsElementResolver
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
        return 'blog-categories';
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
        $this->eventDispatcher->dispatch(new CategoriesCriteriaEvent($request, $criteria, $context));

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add(
            BlogCategoryDefinition::ENTITY_NAME,
            BlogCategoryDefinition::class,
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
        $werklBlog = $result->get(BlogCategoryDefinition::ENTITY_NAME);

        if (!$werklBlog instanceof EntitySearchResult) {
            return;
        }

        $slot->setData($werklBlog);
    }

    /**
     * Create criteria based on the configuration
     * It returns the criteria
     */
    private function createCriteria(FieldConfigCollection $config, SalesChannelContext $salesChannelContext): Criteria
    {
        $criteria = new Criteria();

        return $criteria;
    }
}
