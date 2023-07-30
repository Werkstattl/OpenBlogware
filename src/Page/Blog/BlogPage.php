<?php declare(strict_types=1);

namespace Sas\BlogModule\Page\Blog;

use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Shopware\Storefront\Page\Navigation\NavigationPage;

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
