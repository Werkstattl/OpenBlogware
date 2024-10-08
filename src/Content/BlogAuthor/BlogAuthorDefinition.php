<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogAuthor;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Salutation\SalutationDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;
use Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorTranslation\BlogAuthorTranslationDefinition;

class BlogAuthorDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'werkl_blog_author';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return BlogAuthorEntity::class;
    }

    public function getCollectionClass(): string
    {
        return BlogAuthorCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),

            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new FkField('salutation_id', 'salutationId', SalutationDefinition::class))->addFlags(new ApiAware()),

            (new StringField('first_name', 'firstName'))->addFlags(new Required(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING), new ApiAware()),
            (new StringField('last_name', 'lastName'))->addFlags(new Required(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING), new ApiAware()),
            (new StringField('email', 'email'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING, false), new ApiAware()),
            (new StringField('display_name', 'displayName'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new ApiAware()),

            (new TranslatedField('description'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),

            new TranslationsAssociationField(BlogAuthorTranslationDefinition::class, 'werkl_blog_author_id'),

            (new OneToOneAssociationField('media', 'media_id', 'id', MediaDefinition::class, true))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('blogEntries', BlogEntriesDefinition::class, 'author_id', 'id'))->addFlags(new CascadeDelete(), new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING)),
            new ManyToOneAssociationField('salutation', 'salutation_id', SalutationDefinition::class, 'id', false),
        ]);
    }
}
