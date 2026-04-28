<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Cms\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\DataResolver\CmsSlotsDataResolver;
use Shopware\Core\Content\Cms\Extension\CmsSlotsDataResolveExtension;
use Shopware\Core\Framework\Extensions\ExtensionDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Content\Blog\DataResolver\BlogDetailCmsElementResolver;

class CmsSlotsDataResolverExtension implements EventSubscriberInterface
{
    public function __construct(private readonly CmsSlotsDataResolver $resolver)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExtensionDispatcher::post(CmsSlotsDataResolveExtension::NAME) => 'postResolve',
        ];
    }

    public function postResolve(CmsSlotsDataResolveExtension $extension): void
    {
        $slots = $extension->result;
        $resolverContext = $extension->resolverContext;

        foreach ($slots as $slotId => $slot) {
            \assert(is_string($slotId));

            if ($slot->getType() !== BlogDetailCmsElementResolver::TYPE) {
                continue;
            }

            $blog = $slot->getData();
            if (!$blog instanceof BlogEntryEntity) {
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
            $contentSlots = $this->resolver->resolve($contentSlots, $resolverContext);

            $cmsSections->getBlocks()->setSlots($contentSlots);
            $slot->setData($blog);

            $slots->set($slotId, $slot);
        }
    }
}
