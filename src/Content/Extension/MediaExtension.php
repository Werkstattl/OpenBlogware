<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Extension;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;
use Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorDefinition;

class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('blogEntries', 'id', 'media_id', BlogEntriesDefinition::class, false),
        );
        $collection->add(
            new OneToOneAssociationField('blogAuthor', 'id', 'media_id', BlogAuthorDefinition::class, false),
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
