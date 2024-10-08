<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogCategory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ChildCountField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ChildrenAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ParentAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ParentFkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TreeLevelField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TreePathField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Werkl\OpenBlogware\Content\Blog\Aggregate\BlogCategoryMappingDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation\BlogCategoryTranslationDefinition;

class BlogCategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'werkl_blog_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return BlogCategoryCollection::class;
    }

    public function getEntityClass(): string
    {
        return BlogCategoryEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required(), new ApiAware()),
            (new VersionField())->addFlags(new ApiAware()),

            new ParentFkField(self::class),
            new ReferenceVersionField(self::class, 'parent_version_id'),

            new FkField('after_category_id', 'afterCategoryId', self::class),

            new TreeLevelField('level', 'level'),
            new TreePathField('path', 'path'),
            new ChildCountField(),

            (new TranslatedField('name'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),

            new ParentAssociationField(self::class, 'id'),
            new ChildrenAssociationField(self::class),

            (new TranslationsAssociationField(BlogCategoryTranslationDefinition::class, 'werkl_blog_category_id'))->addFlags(new Required()),

            (new ManyToManyAssociationField('blogEntries', BlogEntriesDefinition::class, BlogCategoryMappingDefinition::class, 'werkl_blog_category_id', 'werkl_blog_entries_id'))->addFlags(new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING), new CascadeDelete()),
            new OneToManyAssociationField('navigationSalesChannels', SalesChannelDefinition::class, 'navigation_category_id'),
            new OneToManyAssociationField('footerSalesChannels', SalesChannelDefinition::class, 'footer_category_id'),
            new OneToManyAssociationField('serviceSalesChannels', SalesChannelDefinition::class, 'service_category_id'),
        ]);
    }
}
