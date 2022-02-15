<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog;

use Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent;

class BlogListingFilterBuildEvent
{
    /**
     * @Event("Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent")
     */
    public const BLOG_MAIIN_FILTER_EVENT = BlogMainFilterEvent::class;
}
