<?php declare(strict_types=1);

namespace Sas\BlogModule\Blog;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class BlogEntriesEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $title;


    protected $slug;
    
    protected $teaser;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $active;

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
     * @param string $teaser
     */
    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    /**
     * @return string
     */
    public function getTeaser(): string
    {
        return $this->teaser;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
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

}