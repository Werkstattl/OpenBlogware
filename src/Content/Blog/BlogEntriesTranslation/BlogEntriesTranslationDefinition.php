<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\BlogEntriesTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;

class BlogEntriesTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'werkl_blog_entries_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return BlogEntriesTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return BlogEntriesTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return BlogEntriesDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new Required()),
            (new StringField('slug', 'slug'))->addFlags(new Required()),
            new StringField('teaser', 'teaser'),
            new StringField('meta_title', 'metaTitle'),
            new StringField('meta_description', 'metaDescription'),
            (new LongTextField('content', 'content'))->addFlags(new AllowHtml()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
