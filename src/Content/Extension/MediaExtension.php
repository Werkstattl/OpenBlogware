<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Extension;

use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField('blogEntries', 'id', 'media_id', BlogEntriesDefinition::class, false)),
        );
        $collection->add(
            (new OneToOneAssociationField('blogAuthor', 'id', 'media_id', BlogAuthorDefinition::class, false)),
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
