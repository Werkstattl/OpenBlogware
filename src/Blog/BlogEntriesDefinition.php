<?php declare(strict_types=1);

namespace Sas\BlogModule\Blog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BlogEntriesDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'sas_blog_entries';
    }

    public function getEntityClass(): string
    {
        return BlogEntriesEntity::class;
    }

    public function getCollectionClass(): string
    {
        return BlogEntriesCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new StringField('title', 'title'),
            new LongTextField('content', 'content')
        ]);
    }
}