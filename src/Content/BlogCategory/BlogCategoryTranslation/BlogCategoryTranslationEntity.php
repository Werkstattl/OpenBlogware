<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryEntity;

class BlogCategoryTranslationEntity extends TranslationEntity
{
    protected string $name;

    protected ?array $customFields;

    protected string $werklBlogCategoryId;

    protected ?BlogCategoryEntity $werklBlogCategory;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    public function setCustomFields(?array $customFields): void
    {
        $this->customFields = $customFields;
    }

    public function getWerklBlogCategoryId(): string
    {
        return $this->werklBlogCategoryId;
    }

    public function setWerklBlogCategoryId(string $werklBlogCategoryId): void
    {
        $this->werklBlogCategoryId = $werklBlogCategoryId;
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
