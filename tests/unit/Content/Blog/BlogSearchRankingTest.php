<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Tests\Unit\Content\Blog;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\BlogAuthor\BlogAuthorDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryDefinition;

class BlogSearchRankingTest extends TestCase
{
    public static function associations(): iterable
    {
        yield 'author' => ['blogAuthor', new BlogAuthorDefinition()];
        yield 'category' => ['blogCategories', new BlogCategoryDefinition()];
    }

    #[DataProvider('associations')]
    public function testSearchFollowsAssociationOnlyFromBlogEntry(string $association, EntityDefinition $target): void
    {
        $registry = $this->createStub(DefinitionInstanceRegistry::class);
        $entry = new BlogEntryDefinition();
        $entry->compile($registry);
        $target->compile($registry);

        $forward = $entry->getField($association);
        $inverse = $target->getField('blogEntries');
        static::assertNotNull($forward);
        static::assertNotNull($inverse);
        static::assertTrue($forward->is(SearchRanking::class));
        static::assertFalse($inverse->is(SearchRanking::class));
    }
}
