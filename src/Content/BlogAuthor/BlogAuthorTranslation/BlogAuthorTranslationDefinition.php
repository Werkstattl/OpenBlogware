<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorTranslation;

use Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BlogAuthorTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'werkl_blog_author_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return BlogAuthorTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return BlogAuthorTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return BlogAuthorDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new LongTextField('description', 'description'),
            new CustomFields(),
        ]);
    }
}
