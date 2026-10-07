<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Tag\TagDefinition;
use Werkl\OpenBlogware\Content\Blog\Aggregate\BlogEntryTagMappingDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;

class TagExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField('blogEntries', BlogEntryDefinition::class, BlogEntryTagMappingDefinition::class, 'tag_id', 'werkl_blog_entry_id'))->addFlags(new CascadeDelete()),
        );
    }

    public function getEntityName(): string
    {
        return TagDefinition::ENTITY_NAME;
    }
}
