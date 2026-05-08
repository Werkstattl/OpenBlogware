<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogCategory;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntryCollection;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation\BlogCategoryTranslationCollection;

class BlogCategoryEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected ?string $parentId = null;

    protected ?string $parentVersionId = null;

    protected ?string $afterCategoryId = null;

    protected ?int $level = null;

    protected ?string $path = null;

    protected ?int $childCount = null;

    protected ?string $name = null;

    protected ?BlogCategoryEntity $parent = null;

    protected ?BlogCategoryCollection $children = null;

    protected ?BlogCategoryTranslationCollection $translations = null;

    protected ?BlogEntryCollection $blogEntries = null;

    protected ?SalesChannelCollection $navigationSalesChannels = null;

    protected ?SalesChannelCollection $footerSalesChannels = null;

    protected ?SalesChannelCollection $serviceSalesChannels = null;

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getParentVersionId(): ?string
    {
        return $this->parentVersionId;
    }

    public function setParentVersionId(?string $parentVersionId): void
    {
        $this->parentVersionId = $parentVersionId;
    }

    public function getAfterCategoryId(): ?string
    {
        return $this->afterCategoryId;
    }

    public function setAfterCategoryId(?string $afterCategoryId): void
    {
        $this->afterCategoryId = $afterCategoryId;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getChildCount(): ?int
    {
        return $this->childCount;
    }

    public function setChildCount(?int $childCount): void
    {
        $this->childCount = $childCount;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getParent(): ?BlogCategoryEntity
    {
        return $this->parent;
    }

    public function setParent(?BlogCategoryEntity $parent): void
    {
        $this->parent = $parent;
    }

    public function getChildren(): ?BlogCategoryCollection
    {
        return $this->children;
    }

    public function setChildren(BlogCategoryCollection $children): void
    {
        $this->children = $children;
    }

    public function getTranslations(): ?BlogCategoryTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(BlogCategoryTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getBlogEntries(): ?BlogEntryCollection
    {
        return $this->blogEntries;
    }

    public function setBlogEntries(BlogEntryCollection $blogEntries): void
    {
        $this->blogEntries = $blogEntries;
    }

    public function getNavigationSalesChannels(): ?SalesChannelCollection
    {
        return $this->navigationSalesChannels;
    }

    public function setNavigationSalesChannels(SalesChannelCollection $navigationSalesChannels): void
    {
        $this->navigationSalesChannels = $navigationSalesChannels;
    }

    public function getFooterSalesChannels(): ?SalesChannelCollection
    {
        return $this->footerSalesChannels;
    }

    public function setFooterSalesChannels(SalesChannelCollection $footerSalesChannels): void
    {
        $this->footerSalesChannels = $footerSalesChannels;
    }

    public function getServiceSalesChannels(): ?SalesChannelCollection
    {
        return $this->serviceSalesChannels;
    }

    public function setServiceSalesChannels(SalesChannelCollection $serviceSalesChannels): void
    {
        $this->serviceSalesChannels = $serviceSalesChannels;
    }
}
