<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\Aggregate;

use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class BlogCategoryMappingDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'werkl_blog_blog_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('werkl_blog_entries_id', 'blogId', BlogEntriesDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('werkl_blog_category_id', 'blogCategoryId', BlogCategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(BlogCategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),

            new ManyToOneAssociationField('blog', 'werkl_blog_entries_id', BlogEntriesDefinition::class, 'id', false),
            new ManyToOneAssociationField('blogCategory', 'werkl_blog_category_id', BlogCategoryDefinition::class, 'id', false),
        ]);
    }
}
