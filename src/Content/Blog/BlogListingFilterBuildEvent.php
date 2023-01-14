<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog;

use Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent;

class BlogListingFilterBuildEvent
{
    /**
     * @deprecated tag:v1.7.0 - we will use BLOG_MAIN_FILTER_EVENT instead
     *
     * @Event("Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent")
     */
    public const BLOG_MAIIN_FILTER_EVENT = BlogMainFilterEvent::class;

    /**
     * @Event("Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent")
     */
    public const BLOG_MAIN_FILTER_EVENT = BlogMainFilterEvent::class;
}
