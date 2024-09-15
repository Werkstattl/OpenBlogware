<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog;

use Werkl\OpenBlogware\Content\Blog\Events\BlogMainFilterEvent;
use Shopware\Core\Framework\Event\Annotation\Event;

class BlogListingFilterBuildEvent
{
    /**
     * @deprecated tag:v1.7.0 - we will use BLOG_MAIN_FILTER_EVENT instead
     *
     * @Event("Werkl\OpenBlogware\Content\Blog\Events\BlogMainFilterEvent")
     */
    public const BLOG_MAIIN_FILTER_EVENT = BlogMainFilterEvent::class;

    /**
     * @Event("Werkl\OpenBlogware\Content\Blog\Events\BlogMainFilterEvent")
     */
    public const BLOG_MAIN_FILTER_EVENT = BlogMainFilterEvent::class;
}
