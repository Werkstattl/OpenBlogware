<?php declare(strict_types=1);
namespace Sas\BlogModule\Content\Blog;

use Sas\BlogModule\Content\Blog\BlogTranslation\BlogTranslationCollection;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorEntity;
use Sas\BlogModule\Content\BlogCategory\BlogCategoryCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class BlogEntriesEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var int
     */
    protected $active;

    /**
     * @var bool
     */
    protected $detailTeaserImage;

    /**
     * @var BlogTranslationCollection|null
     */
    protected $translations;

    /**
     * @var BlogCategoryCollection|null
     */
    protected $blogCategories;

    /**
     * @var string
     */
    protected $authorId;

    /**
     * @var BlogAuthorEntity|null
     */
    protected $author;

    /**
     * @return string
     */
    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    /**
     * @param string $authorId
     */
    public function setAuthorId(string $authorId): void
    {
        $this->authorId = $authorId;
    }

    /**
     * @return BlogAuthorEntity|null
     */
    public function getAuthor(): ?BlogAuthorEntity
    {
        return $this->author;
    }

    /**
     * @param BlogAuthorEntity $author
     */
    public function setAuthor(BlogAuthorEntity $author): void
    {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive(int $active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function getDetailTeaserImage(): bool
    {
        return $this->detailTeaserImage;
    }

    /**
     * @param bool $detailTeaserImage
     */
    public function setDetailTeaserImage(bool $detailTeaserImage): void
    {
        $this->detailTeaserImage = $detailTeaserImage;
    }

    /**
     * @return BlogTranslationCollection|null
     */
    public function getTranslations(): ?BlogTranslationCollection
    {
        return $this->translations;
    }

    /**
     * @param BlogTranslationCollection|null $translations
     */
    public function setTranslations(?BlogTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return BlogCategoryCollection|null
     */
    public function getBlogCategories(): ?BlogCategoryCollection
    {
        return $this->blogCategories;
    }

    /**
     * @param BlogCategoryCollection $blogCategories
     */
    public function setBlogCategories(BlogCategoryCollection $blogCategories): void
    {
        $this->blogCategories = $blogCategories;
    }
}
