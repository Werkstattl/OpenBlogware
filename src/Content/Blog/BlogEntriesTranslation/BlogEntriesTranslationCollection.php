<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\BlogEntriesTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                              add(BlogEntriesTranslationEntity $entity)
 * @method void                              set(string $key, BlogEntriesTranslationEntity $entity)
 * @method BlogEntriesTranslationEntity[]    getIterator()
 * @method BlogEntriesTranslationEntity[]    getElements()
 * @method BlogEntriesTranslationEntity|null get(string $key)
 * @method BlogEntriesTranslationEntity|null first()
 * @method BlogEntriesTranslationEntity|null last()
 */
class BlogEntriesTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BlogEntriesTranslationEntity::class;
    }
}
