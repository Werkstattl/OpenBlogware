<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Werkl\OpenBlogware\Util\EntityIds;

class BlogSeoUrlListener implements EventSubscriberInterface
{
    public function __construct(private readonly SeoUrlUpdater $seoUrlUpdater)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'werkl_blog_entry.written' => 'onBlogUpdated',
        ];
    }

    public function onBlogUpdated(EntityWrittenEvent $event): void
    {
        $this->seoUrlUpdater->update(BlogSeoUrlRoute::ROUTE_NAME, EntityIds::normalize($event->getIds()));
    }
}
