<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Tests\Unit\Content\Blog;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Tag\TagDefinition;
use Werkl\OpenBlogware\Content\Blog\Aggregate\BlogEntryTagMappingDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Content\Extension\CmsPageExtension;
use Werkl\OpenBlogware\Content\Extension\TagExtension;

/**
 * Regression tests for the definitions reported as errors by `dal:validate`,
 * see https://github.com/Werkstattl/OpenBlogware/issues/87
 */
class BlogEntryDefinitionTest extends TestCase
{
    /**
     * @var array<string, \Shopware\Core\Framework\DataAbstractionLayer\Field\Field>
     */
    private array $fields;

    protected function setUp(): void
    {
        $this->fields = $this->defineFieldsByName(new BlogEntryDefinition());
    }

    /**
     * The table has PRIMARY KEY (id) only, so no other field may carry the PrimaryKey flag.
     */
    public function testOnlyIdFieldIsFlaggedAsPrimaryKey(): void
    {
        $propertyNames = [];

        foreach ($this->fields as $propertyName => $field) {
            if ($field->is(PrimaryKey::class)) {
                $propertyNames[] = $propertyName;
            }
        }

        static::assertSame(['id'], $propertyNames);
    }

    public function testCmsPageVersionReferenceFieldIsNotPrimaryKeyButRequired(): void
    {
        $field = $this->fields['cmsPageVersionId'];

        static::assertInstanceOf(ReferenceVersionField::class, $field);
        static::assertTrue($field->is(Required::class));
        static::assertFalse($field->is(PrimaryKey::class));
    }

    public function testCmsPageAssociationIsManyToOne(): void
    {
        $association = $this->fields['cmsPage'];

        static::assertInstanceOf(ManyToOneAssociationField::class, $association);
        static::assertSame(CmsPageDefinition::class, $association->getReferenceClass());
    }

    /**
     * The CmsPageExtension provides the reverse one-to-many side the DAL validator requires.
     */
    public function testCmsPageExtensionProvidesReverseAssociation(): void
    {
        $extensionFields = new FieldCollection();
        (new CmsPageExtension())->extendFields($extensionFields);

        $reverse = $this->fieldsByName($extensionFields)['blogEntries'] ?? null;

        static::assertNotNull($reverse);
        static::assertInstanceOf(OneToManyAssociationField::class, $reverse);
        static::assertSame(BlogEntryDefinition::class, $reverse->getReferenceClass());
    }

    public function testTagsAssociationUsesTagMappingDefinition(): void
    {
        $association = $this->fields['tags'];

        static::assertInstanceOf(ManyToManyAssociationField::class, $association);
        // for many-to-many fields the reference class is the mapping definition
        static::assertSame(BlogEntryTagMappingDefinition::class, $association->getReferenceClass());
        static::assertSame(TagDefinition::class, $this->getToManyReferenceClass($association));
    }

    /**
     * The TagExtension provides the reverse many-to-many side the DAL validator requires.
     */
    public function testTagExtensionProvidesReverseAssociation(): void
    {
        $extensionFields = new FieldCollection();
        (new TagExtension())->extendFields($extensionFields);

        $reverse = $this->fieldsByName($extensionFields)['blogEntries'] ?? null;

        static::assertNotNull($reverse);
        static::assertInstanceOf(ManyToManyAssociationField::class, $reverse);
        static::assertSame(BlogEntryDefinition::class, $this->getToManyReferenceClass($reverse));
        static::assertSame(BlogEntryTagMappingDefinition::class, $reverse->getReferenceClass());
        static::assertTrue($reverse->is(CascadeDelete::class));
    }

    /**
     * The teaser image lives in the translations; the media association on the entity is
     * hydrated manually by the BlogSubscriber and must therefore stay out of the DAL
     * field collection, but be documented as internal for the definition validator.
     */
    public function testMediaIsNotADalFieldButAnInternalHydratedAssociation(): void
    {
        static::assertArrayNotHasKey('media', $this->fields);
        static::assertInstanceOf(TranslatedField::class, $this->fields['mediaId']);

        $docComment = (new \ReflectionProperty(BlogEntryEntity::class, 'media'))->getDocComment();

        static::assertIsString($docComment);
        static::assertStringContainsString('@internal', $docComment);
    }

    public function testSetTagsDoesNotAcceptNull(): void
    {
        $parameter = (new \ReflectionMethod(BlogEntryEntity::class, 'setTags'))->getParameters()[0];

        static::assertFalse($parameter->allowsNull());
    }

    /**
     * @return array<string, \Shopware\Core\Framework\DataAbstractionLayer\Field\Field>
     */
    private function defineFieldsByName(BlogEntryDefinition $definition): array
    {
        $method = new \ReflectionMethod(BlogEntryDefinition::class, 'defineFields');

        return $this->fieldsByName($method->invoke($definition));
    }

    /**
     * @return array<string, \Shopware\Core\Framework\DataAbstractionLayer\Field\Field>
     */
    private function fieldsByName(FieldCollection $collection): array
    {
        $fields = [];

        foreach ($collection as $field) {
            $fields[$field->getPropertyName()] = $field;
        }

        return $fields;
    }

    /**
     * Reads the (uncompiled) to-many reference class of a many-to-many association.
     */
    private function getToManyReferenceClass(object $association): string
    {
        $property = new \ReflectionProperty(ManyToManyAssociationField::class, 'toManyDefinitionClass');

        return $property->getValue($association);
    }
}
