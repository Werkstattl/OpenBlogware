<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                             add(BlogAuthorTranslationEntity $entity)
 * @method void                             set(string $key, BlogAuthorTranslationEntity $entity)
 * @method BlogAuthorTranslationEntity[]    getIterator()
 * @method BlogAuthorTranslationEntity[]    getElements()
 * @method BlogAuthorTranslationEntity|null get(string $key)
 * @method BlogAuthorTranslationEntity|null first()
 * @method BlogAuthorTranslationEntity|null last()
 */
class BlogAuthorTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'werkl_blog_author_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return BlogAuthorTranslationEntity::class;
    }
}
