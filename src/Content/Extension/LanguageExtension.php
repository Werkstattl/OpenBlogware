<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesTranslation\BlogEntriesTranslationDefinition;
use Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorTranslation\BlogAuthorTranslationDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation\BlogCategoryTranslationDefinition;

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
