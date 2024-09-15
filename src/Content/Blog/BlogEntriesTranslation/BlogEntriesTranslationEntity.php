<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\BlogEntriesTranslation;

use Werkl\OpenBlogware\Content\Blog\BlogEntriesEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class BlogEntriesTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected string $werklBlogEntriesId;

    protected BlogEntriesEntity $werklBlogEntries;

    protected string $title;

    protected string $slug;

    protected string $content;

    protected string $teaser;

    protected ?string $metaTitle;

    protected ?string $metaDescription;

    public function getWerklBlogEntriesId(): string
    {
        return $this->werklBlogEntriesId;
    }

    public function setWerklBlogEntriesId(string $werklBlogEntriesId): void
    {
        $this->werklBlogEntriesId = $werklBlogEntriesId;
    }

    public function getWerklBlogEntries(): BlogEntriesEntity
    {
        return $this->werklBlogEntries;
    }

    public function setWerklBlogEntries(BlogEntriesEntity $werklBlogEntries): void
    {
        $this->werklBlogEntries = $werklBlogEntries;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}
