<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryEntity;

class BlogCategoryTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected ?string $name = null;

    protected string $werklBlogCategoryId;

    protected string $werklBlogCategoryVersionId;

    protected ?BlogCategoryEntity $werklBlogCategory = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getWerklBlogCategoryId(): string
    {
        return $this->werklBlogCategoryId;
    }

    public function setWerklBlogCategoryId(string $werklBlogCategoryId): void
    {
        $this->werklBlogCategoryId = $werklBlogCategoryId;
    }

    public function getWerklBlogCategoryVersionId(): string
    {
        return $this->werklBlogCategoryVersionId;
    }

    public function setWerklBlogCategoryVersionId(string $werklBlogCategoryVersionId): void
    {
        $this->werklBlogCategoryVersionId = $werklBlogCategoryVersionId;
    }

    public function getWerklBlogCategory(): ?BlogCategoryEntity
    {
        return $this->werklBlogCategory;
    }

    public function setWerklBlogCategory(BlogCategoryEntity $werklBlogCategory): void
    {
        $this->werklBlogCategory = $werklBlogCategory;
    }
}
