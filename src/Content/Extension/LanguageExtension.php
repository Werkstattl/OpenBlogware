<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Extension;

use Sas\BlogModule\Content\Blog\BlogEntriesTranslation\BlogEntriesTranslationDefinition;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorTranslation\BlogAuthorTranslationDefinition;
use Sas\BlogModule\Content\BlogCategory\BlogCategoryTranslation\BlogCategoryTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField('blogTranslations', BlogEntriesTranslationDefinition::class, 'language_id'))->addFlags(new CascadeDelete()),
        );
        $collection->add(
            (new OneToManyAssociationField('blogCategoryTranslations', BlogCategoryTranslationDefinition::class, 'language_id'))->addFlags(new CascadeDelete()),
        );
        $collection->add(
            (new OneToManyAssociationField('blogAuthorTranslations', BlogAuthorTranslationDefinition::class, 'language_id'))->addFlags(new CascadeDelete()),
        );
    }

    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;
    }
}
