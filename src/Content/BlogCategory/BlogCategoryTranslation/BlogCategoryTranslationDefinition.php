<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation;

use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BlogCategoryTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'werkl_blog_category_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return BlogCategoryTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return BlogCategoryTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return BlogCategoryDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
            new CustomFields(),
        ]);
    }
}
