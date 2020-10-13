<?php declare(strict_types=1);
namespace Sas\BlogModule\Content\Blog;

use Sas\BlogModule\Content\Blog\BlogTranslation\BlogTranslationCollection;
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
     * @var BlogTranslationCollection|null
     */
    protected $translations;

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
}
