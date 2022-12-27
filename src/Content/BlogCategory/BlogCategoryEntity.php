<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\BlogCategory;

use Sas\BlogModule\Content\Blog\BlogEntriesCollection;
use Sas\BlogModule\Content\BlogCategory\BlogCategoryTranslation\BlogCategoryTranslationCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;

class BlogCategoryEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $parentId;

    protected ?string $afterCategoryId;

    protected ?int $level;

    protected ?string $path;

    protected ?int $childCount;

    protected string $name;

    protected ?array $customFields;

    /**
     * @var self|null
     */
    protected $parent;

    /**
     * @var BlogCategoryCollection|null
     */
    protected $children;

    /**
     * @var BlogCategoryTranslationCollection|null
     */
    protected $translations;

    /**
     * @var BlogEntriesCollection|null
     */
    protected $blogEntries;

    /**
     * @var SalesChannelCollection|null
     */
    protected $navigationSalesChannels;

    /**
     * @var SalesChannelCollection|null
     */
    protected $footerSalesChannels;

    /**
     * @var SalesChannelCollection|null
     */
    protected $serviceSalesChannels;

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
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

    public function getName(): string
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

    public function getParent(): ?Entity
    {
        return $this->parent;
    }

    public function setParent(self $parent): void
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

    public function getBlogEntries(): ?BlogEntriesCollection
    {
        return $this->blogEntries;
    }

    public function setBlogEntries(BlogEntriesCollection $blogEntries): void
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
