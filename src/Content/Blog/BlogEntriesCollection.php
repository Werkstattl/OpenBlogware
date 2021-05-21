<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                   add(BlogEntriesEntity $entity)
 * @method void                   set(string $key, BlogEntriesEntity $entity)
 * @method BlogEntriesEntity[]    getIterator()
 * @method BlogEntriesEntity[]    getElements()
 * @method BlogEntriesEntity|null get(string $key)
 * @method BlogEntriesEntity|null first()
 * @method BlogEntriesEntity|null last()
 */
class BlogEntriesCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BlogEntriesEntity::class;
    }
}
