<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Cms\DataResolver;

use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Sas\BlogModule\Content\Blog\DataResolver\BlogDetailCmsElementResolver;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\DataResolver\CmsSlotsDataResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;

class SasCmsSlotsDataResolver extends CmsSlotsDataResolver
{
    private CmsSlotsDataResolver $decorated;

    /**
     * @var CmsElementResolverInterface[]
     */
    private array $resolvers;

    /**
     * @param CmsElementResolverInterface[] $resolvers
     */
    public function __construct(CmsSlotsDataResolver $decorated, iterable $resolvers)
    {
        $this->decorated = $decorated;

        foreach ($resolvers as $resolver) {
            $this->resolvers[$resolver->getType()] = $resolver;
        }
    }

    /**
     * Resolves the data for the given slots.
     * It calls the decorated resolve method to get resolved slot.
     * Then it checks if there are any slot that needs to be resolved by BlogDetailCmsElementResolver.
     * If so, it calls the decorated resolve method again to resolve the data for the slot.
     * Otherwise, it skips the slot.
     */
    public function resolve(CmsSlotCollection $slots, ResolverContext $resolverContext): CmsSlotCollection
    {
        $slots = $this->decorated->resolve($slots, $resolverContext);

        foreach ($slots as $slotId => $slot) {
            $resolver = $this->resolvers[$slot->getType()] ?? null;
            if (!$resolver instanceof BlogDetailCmsElementResolver) {
                continue;
            }

            $blog = $slot->getData();
            if (!$blog instanceof BlogEntriesEntity) {
                continue;
            }

            $cmsPage = $blog->getCmsPage();
            if (!$cmsPage instanceof CmsPageEntity) {
                continue;
            }

            $cmsSections = $cmsPage->getSections();
            if (!$cmsSections instanceof CmsSectionCollection) {
                continue;
            }

            $contentSlots = $cmsSections->getBlocks()->getSlots();
            $contentSlots = $this->decorated->resolve($contentSlots, $resolverContext);

            $cmsSections->getBlocks()->setSlots($contentSlots);
            $slot->setData($blog);

            $slots->set($slotId, $slot);
        }

        return $slots;
    }
}
