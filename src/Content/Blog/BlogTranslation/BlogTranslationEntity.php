<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\BlogTranslation;

use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class BlogTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $blogId;

    /**
     * @var BlogEntriesEntity
     */
    protected $blog;

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

    public function getBlogId(): string
    {
        return $this->blogId;
    }

    public function setBlogId(string $blogId): void
    {
        $this->blogId = $blogId;
    }

    public function getBlog(): BlogEntriesEntity
    {
        return $this->blog;
    }

    public function setBlog(BlogEntriesEntity $blog): void
    {
        $this->blog = $blog;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getTeaser(): string
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
