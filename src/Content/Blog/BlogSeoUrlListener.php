<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlogSeoUrlListener implements EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;

    public function __construct(SeoUrlUpdater $seoUrlUpdater)
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'werkl_blog_entries.written' => 'onBlogUpdated',
        ];
    }

    public function onBlogUpdated(EntityWrittenEvent $event): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }
}
