<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Extension;

use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;

class CmsPageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('blogEntries', BlogEntryDefinition::class, 'cms_page_id'),
        );
    }

    public function getEntityName(): string
    {
        return CmsPageDefinition::ENTITY_NAME;
    }
}
