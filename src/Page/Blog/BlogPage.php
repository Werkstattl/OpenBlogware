<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Page\Blog;

use Shopware\Storefront\Page\Navigation\NavigationPage;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesEntity;

class BlogPage extends NavigationPage
{
    protected BlogEntriesEntity $blogEntry;

    protected ?string $navigationId;

    public function getBlogEntry(): BlogEntriesEntity
    {
        return $this->blogEntry;
    }

    public function setBlogEntry(BlogEntriesEntity $blogEntry): void
    {
        $this->blogEntry = $blogEntry;
    }

    public function getNavigationId(): ?string
    {
        return $this->navigationId;
    }

    public function setNavigationId(?string $navigationId): void
    {
        $this->navigationId = $navigationId;
    }

    public function getEntityName(): string
    {
        return BlogEntriesDefinition::ENTITY_NAME;
    }
}
