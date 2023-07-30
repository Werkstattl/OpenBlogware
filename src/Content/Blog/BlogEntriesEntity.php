<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog;

use Sas\BlogModule\Content\Blog\BlogEntriesTranslation\BlogEntriesTranslationCollection;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorEntity;
use Sas\BlogModule\Content\BlogCategory\BlogCategoryCollection;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class BlogEntriesEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    protected ?string $title;

    protected ?string $slug;

    protected ?string $teaser;

    protected ?string $metaTitle;

    protected ?string $metaDescription;

    protected ?string $content;

    protected bool $active;

    protected bool $detailTeaserImage;

    /**
     * @var BlogEntriesTranslationCollection|null
     */
    protected $translations;

    /**
     * @var BlogCategoryCollection|null
     */
    protected $blogCategories;

    protected string $authorId;

    protected ?BlogAuthorEntity $blogAuthor;

    protected string $mediaId;

    protected ?MediaEntity $media;

    protected \DateTimeInterface $publishedAt;

    protected ?string $cmsPageId;

    protected ?CmsPageEntity $cmsPage;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function setTeaser(?string $teaser): void
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function setAuthorId(string $authorId): void
    {
        $this->authorId = $authorId;
    }

    public function getBlogAuthor(): ?BlogAuthorEntity
    {
        return $this->blogAuthor;
    }

    public function setBlogAuthor(BlogAuthorEntity $blogAuthor): void
    {
        $this->blogAuthor = $blogAuthor;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getDetailTeaserImage(): bool
    {
        return $this->detailTeaserImage;
    }

    public function setDetailTeaserImage(bool $detailTeaserImage): void
    {
        $this->detailTeaserImage = $detailTeaserImage;
    }

    public function getTranslations(): ?BlogEntriesTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(BlogEntriesTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getBlogCategories(): ?BlogCategoryCollection
    {
        return $this->blogCategories;
    }

    public function setBlogCategories(BlogCategoryCollection $blogCategories): void
    {
        $this->blogCategories = $blogCategories;
    }

    public function getPublishedAt(): \DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getCmsPage(): ?CmsPageEntity
    {
        return $this->cmsPage;
    }

    public function setCmsPage(CmsPageEntity $cmsPage): void
    {
        $this->cmsPage = $cmsPage;
    }

    public function getCmsPageId(): ?string
    {
        return $this->cmsPageId;
    }

    public function setCmsPageId(string $cmsPageId): void
    {
        $this->cmsPageId = $cmsPageId;
    }
}
