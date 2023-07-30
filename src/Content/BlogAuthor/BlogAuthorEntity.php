<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\BlogAuthor;

use Sas\BlogModule\Content\Blog\BlogEntriesCollection;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorTranslation\BlogAuthorTranslationCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\Salutation\SalutationEntity;

class BlogAuthorEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    protected string $firstName;

    protected string $lastName;

    protected string $email;

    protected ?string $displayName;

    protected string $salutationId;

    protected ?SalutationEntity $salutation;

    protected ?string $description;

    /**
     * @var BlogAuthorTranslationCollection|null
     */
    protected $translations;

    /**
     * @var BlogEntriesCollection|null
     */
    protected $blogEntries;

    protected string $mediaId;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    public function getTranslations(): ?BlogAuthorTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(BlogAuthorTranslationCollection $translations): void
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

    public function getSalutation(): ?SalutationEntity
    {
        return $this->salutation;
    }

    public function setSalutation(SalutationEntity $salutation): void
    {
        $this->salutation = $salutation;
    }

    public function getSalutationId(): string
    {
        return $this->salutationId;
    }

    public function setSalutationId(string $salutationId): void
    {
        $this->salutationId = $salutationId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getFullName(): ?string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getTranslated(): array
    {
        $translated = parent::getTranslated();

        $translated['name'] = $this->getFirstName() . ' ' . $this->getLastName();

        return $translated;
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
}
