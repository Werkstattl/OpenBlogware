<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Extension;

use Sas\BlogModule\Content\BlogAuthor\BlogAuthorDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Salutation\SalutationDefinition;

class SalutationExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField('blogAuthors', BlogAuthorDefinition::class, 'salutation_id', 'id'))->addFlags(new RestrictDelete()),
        );
    }

    public function getDefinitionClass(): string
    {
        return SalutationDefinition::class;
    }
}
