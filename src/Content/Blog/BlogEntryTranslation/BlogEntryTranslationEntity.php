<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\BlogEntryTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;

class BlogEntryTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected string $werklBlogEntryId;

    protected ?BlogEntryEntity $werklBlogEntry = null;

    protected ?string $mediaId = null;

    protected string $title;

    protected string $slug;

    protected string $content;

    protected string $teaser;

    protected ?string $metaTitle = null;

    protected ?string $metaDescription = null;

    public function getWerklBlogEntryId(): string
    {
        return $this->werklBlogEntryId;
    }

    public function setWerklBlogEntryId(string $werklBlogEntryId): void
    {
        $this->werklBlogEntryId = $werklBlogEntryId;
    }

    public function getWerklBlogEntry(): ?BlogEntryEntity
    {
        return $this->werklBlogEntry;
    }

    public function setWerklBlogEntry(BlogEntryEntity $werklBlogEntry): void
    {
        $this->werklBlogEntry = $werklBlogEntry;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
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
