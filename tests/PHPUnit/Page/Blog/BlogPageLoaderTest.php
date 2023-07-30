<?php declare(strict_types=1);

namespace BlogModule\Tests\Page\Blog;

use BlogModule\Tests\Fakes\FakeEntityRepository;
use BlogModule\Tests\Traits\ContextTrait;
use PHPUnit\Framework\TestCase;
use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorEntity;
use Sas\BlogModule\Page\Blog\BlogPage;
use Sas\BlogModule\Page\Blog\BlogPageLoader;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Tree\Tree;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\Exception\ConfigurationNotFoundException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Storefront\Page\Page;
use Shopware\Storefront\Pagelet\Header\HeaderPagelet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class BlogPageLoaderTest extends TestCase
{
    use ContextTrait;

    private SystemConfigService $systemConfigService;

    private GenericPageLoaderInterface $genericLoader;

    private SalesChannelCmsPageLoaderInterface $cmsPageLoader;

    private EventDispatcherInterface $eventDispatcher;

    private EntityRepository $blogRepository;

    private BlogPageLoader $blogPageLoader;

    private SalesChannelContext $salesChannelContext;

    public function setUp(): void
    {
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
        $this->genericLoader = $this->createMock(GenericPageLoaderInterface::class);
        $this->cmsPageLoader = $this->createMock(SalesChannelCmsPageLoaderInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->blogRepository = new FakeEntityRepository(new BlogEntriesDefinition());

        $this->salesChannelContext = $this->getSaleChannelContext($this);

        $this->blogPageLoader = new BlogPageLoader(
            $this->systemConfigService,
            $this->genericLoader,
            $this->eventDispatcher,
            $this->cmsPageLoader,
            $this->blogRepository,
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testLoadData(
        ?string $requestArticleId,
        bool $hasBlogEntry,
        ?string $detailCmsPageId,
        bool $hasCmsPage,
        ?string $exceptionClass,
        int $expectedDispatches,
        array $metaInformation
    ): void {
        $request = new Request([], [], ['articleId' => $requestArticleId]);

        $blogSearchResults = $this->createBlogSearchResult($hasBlogEntry, $requestArticleId, $metaInformation);
        $this->blogRepository->entitySearchResults = [$blogSearchResults];

        if ($detailCmsPageId) {
            $this->systemConfigService->expects(static::once())->method('getString')->willReturn($detailCmsPageId);
        }

        if ($hasCmsPage) {
            $cmsPageSearchResults = $this->createCmsPageLoaderResult($detailCmsPageId);
            $this->cmsPageLoader->expects(static::once())->method('load')->willReturn($cmsPageSearchResults);
        }

        if ($exceptionClass) {
            $this->expectException($exceptionClass);
        }

        $this->eventDispatcher->expects(static::exactly($expectedDispatches))->method('dispatch');

        $genericPage = $this->createGenericPage();
        $this->genericLoader->method('load')->willReturn($genericPage);

        $actualPage = $this->blogPageLoader->load($request, $this->salesChannelContext);

        static::assertInstanceOf(BlogPage::class, $actualPage);
        static::assertSame($detailCmsPageId, $actualPage->getCmsPage()->getId());

        static::assertIsObject($actualPage);
        static::assertTrue(property_exists($actualPage, 'blogEntry'));
        static::assertInstanceOf(BlogEntriesEntity::class, $actualPage->getBlogEntry());

        static::assertTrue(property_exists($actualPage, 'metaInformation'));
        static::assertInstanceOf(MetaInformation::class, $actualPage->getMetaInformation());
        static::assertSame($metaInformation['metaTitle'], $actualPage->getMetaInformation()->getMetaTitle());
        static::assertSame($metaInformation['metaDescription'], $actualPage->getMetaInformation()->getMetaDescription());
        static::assertSame($metaInformation['metaAuthor'], $actualPage->getMetaInformation()->getAuthor());

        static::assertNotNull($actualPage->getNavigationId());
    }

    /**
     * Get test data with array structure:
     * - article id
     * - has blog entry that matches above article id
     * - detail cms page id
     * - has cms page matches above page id
     * - exception class to be thrown
     * - expected number of event dispatches
     * - meta information
     */
    public function getTestData(): array
    {
        return [
            'test the article id is not set in the request' => [
                null,
                false,
                null,
                false,
                MissingRequestParameterException::class,
                0,
                [],
            ],
            'test the article id not match to any existing article' => [
                'bl-100',
                false,
                null,
                false,
                PageNotFoundException::class,
                1,
                [],
            ],
            'test there is no configuration for the blog page' => [
                'bl-100',
                true,
                null,
                false,
                ConfigurationNotFoundException::class,
                1,
                [],
            ],
            'test there is no cms pages matching the configured blog page id' => [
                'bl-100',
                true,
                'cms-111',
                false,
                PageNotFoundException::class,
                1,
                [],
            ],
            'test the loader is able to load the blog page' => [
                'bl-100',
                true,
                'cms-111',
                true,
                null,
                2,
                [
                    'metaTitle' => 'blog title',
                    'metaDescription' => 'blog teaser',
                    'metaAuthor' => 'meta author',
                ],
            ],
        ];
    }

    /**
     * Create search result for the blog repository.
     * It also creates blog entry entity and config first method to return it.
     */
    private function createBlogSearchResult(
        bool $hasBlogEntry,
        ?string $articleId,
        ?array $metaInformation
    ): ?EntitySearchResult {
        $searchResults = $this->createMock(EntitySearchResult::class);

        if ($hasBlogEntry) {
            $blogEntry = $this->createConfiguredMock(BlogEntriesEntity::class, [
                'getId' => $articleId,
                'getTitle' => 'blog title',
                'getTeaser' => 'blog teaser',
                'getMetaTitle' => $metaInformation['metaTitle'] ?? null,
                'getMetaDescription' => $metaInformation['metaDescription'] ?? null,
                'getBlogAuthor' => $this->createConfiguredMock(BlogAuthorEntity::class, [
                    'getFullName' => $metaInformation['metaAuthor'] ?? null,
                ]),
            ]);

            $searchResults->method('first')->willReturn($blogEntry);
        }

        return $searchResults;
    }

    /**
     * Create search result with the given cms page id.
     * It also creates cms page entity and config first method to return it.
     */
    private function createCmsPageLoaderResult(?string $cmsPageId = null): ?EntitySearchResult
    {
        $searchResults = $this->createMock(EntitySearchResult::class);

        if ($cmsPageId) {
            $cmsPage = $this->createConfiguredMock(CmsPageEntity::class, [
                'getId' => $cmsPageId,
            ]);

            $searchResults->method('first')->willReturn($cmsPage);
        }

        return $searchResults;
    }

    /**
     * Create generic page for testing.
     * It creates category.
     * It creates tree and config getActive method to return above created category.
     * It creates header pagelet and config getNavigation method to return above created tree.
     * It creates generic pagelet and setups its header and meta information.
     */
    private function createGenericPage(): Page
    {
        $category = $this->createMock(CategoryEntity::class);

        $tree = $this->createMock(Tree::class);
        $tree->method('getActive')->willReturn($category);

        $headerPagelet = $this->createMock(HeaderPagelet::class);
        $headerPagelet->method('getNavigation')->willReturn($tree);

        $metaInformation = new MetaInformation();

        $page = new Page();
        $page->setHeader($headerPagelet);
        $page->setMetaInformation($metaInformation);

        return $page;
    }
}
