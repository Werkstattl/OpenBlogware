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
     * @var array|null
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

    /**
     * @return string
     */
    public function getBlogId(): string
    {
        return $this->blogId;
    }

    /**
     * @param string $blogId
     */
    public function setBlogId(string $blogId): void
    {
        $this->blogId = $blogId;
    }

    /**
     * @return BlogEntriesEntity
     */
    public function getBlog(): BlogEntriesEntity
    {
        return $this->blog;
    }

    /**
     * @param BlogEntriesEntity $blog
     */
    public function setBlog(BlogEntriesEntity $blog): void
    {
        $this->blog = $blog;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return array|null
     */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /**
     * @param array|null $content
     */
    public function setContent(?array $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getTeaser(): string
    {
        return $this->teaser;
    }

    /**
     * @param string $teaser
     */
    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param string|null $metaTitle
     */
    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string|null $metaDescription
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}
