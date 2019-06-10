<?php declare(strict_types=1);

namespace Sas\BlogModule\Blog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BlogEntriesDefinition extends EntityDefinition
{
    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return 'sas_blog_entries';
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return BlogEntriesEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return BlogEntriesCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new IntField('active', 'active'),
            new StringField('title', 'title'),
            new StringField('slug', 'slug'),
            new StringField('teaser', 'teaser'),
            new LongTextField('content', 'content')
        ]);
    }
}