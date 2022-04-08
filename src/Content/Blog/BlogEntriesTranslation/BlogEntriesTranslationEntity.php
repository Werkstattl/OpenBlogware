<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\BlogEntriesTranslation;

use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class BlogEntriesTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $sasBlogEntriesId;

    /**
     * @var BlogEntriesEntity
     */
    protected $sasBlogEntries;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $teaser;

    /**
     * @var string|null
     */
    protected $metaTitle;

    /**
     * @var string|null
     */
    protected $metaDescription;

    public function getSasBlogEntriesId(): string
    {
        return $this->sasBlogEntriesId;
    }

    public function setSasBlogEntriesId(string $sasBlogEntriesId): void
    {
        $this->sasBlogEntriesId = $sasBlogEntriesId;
    }

    public function getSasBlogEntries(): BlogEntriesEntity
    {
        return $this->sasBlogEntries;
    }

    public function setSasBlogEntries(BlogEntriesEntity $sasBlogEntries): void
    {
        $this->sasBlogEntries = $sasBlogEntries;
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
