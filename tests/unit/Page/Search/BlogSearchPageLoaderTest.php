<?php
declare(strict_types=1);

namespace OpenBlogware\Tests\Page\Search;

use OpenBlogware\Tests\Fakes\FakeEntityRepository;
use OpenBlogware\Tests\Traits\ContextTrait;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Page;
use Symfony\Component\HttpFoundation\Request;
use Werkl\OpenBlogware\Content\Blog\BlogEntryDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Page\Search\BlogSearchPage;
use Werkl\OpenBlogware\Page\Search\BlogSearchPageLoader;

class BlogSearchPageLoaderTest extends TestCase
{
    use ContextTrait;

    private GenericPageLoaderInterface $genericLoader;

    private EntityRepository $blogRepository;

    private SalesChannelContext $salesChannelContext;

    private BlogSearchPageLoader $blogSearchPageLoader;

    protected function setUp(): void
    {
        $this->genericLoader = $this->createMock(GenericPageLoaderInterface::class);
        $this->blogRepository = new FakeEntityRepository(new BlogEntryDefinition());

        $this->salesChannelContext = $this->getSaleChannelContext($this);

        $this->blogSearchPageLoader = new BlogSearchPageLoader(
            $this->genericLoader,
            $this->blogRepository,
        );
    }

    /**
     * This test verifies that the search page loader will throw an exception
     * when no search term is provided in the request.
     */
    public function testLoadWithoutSearchQuery(): void
    {
        $this->expectException(RoutingException::class);
        $this->blogSearchPageLoader->load(new Request(), $this->salesChannelContext);
    }

    /**
     * This test verifies that the search page is loaded correctly with given search term
     */
    public function testLoad(): void
    {
        $searchResults = $this->createConfiguredMock(EntitySearchResult::class, [
            'first' => $this->createMock(BlogEntryEntity::class),
        ]);
        $request = new Request(['search' => 'foo'], [], []);
        $this->blogRepository->entitySearchResults = [$searchResults];

        $this->genericLoader->method('load')->willReturn($this->createMock(Page::class));
        $actualResult = $this->blogSearchPageLoader->load($request, $this->salesChannelContext);

        static::assertInstanceOf(BlogSearchPage::class, $actualResult);
    }
}
