<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\BlogTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                       add(BlogTranslationEntity $entity)
 * @method void                       set(string $key, BlogTranslationEntity $entity)
 * @method BlogTranslationEntity[]    getIterator()
 * @method BlogTranslationEntity[]    getElements()
 * @method BlogTranslationEntity|null get(string $key)
 * @method BlogTranslationEntity|null first()
 * @method BlogTranslationEntity|null last()
 */
class BlogTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BlogTranslationEntity::class;
    }
}
