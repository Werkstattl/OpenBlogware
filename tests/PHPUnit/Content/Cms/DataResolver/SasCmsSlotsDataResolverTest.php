<?php declare(strict_types=1);

namespace BlogModule\Tests\Content\Cms\DataResolver;

use BlogModule\Tests\Traits\ContextTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Sas\BlogModule\Content\Blog\DataResolver\BlogDetailCmsElementResolver;
use Sas\BlogModule\Content\Cms\DataResolver\SasCmsSlotsDataResolver;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\DataResolver\CmsSlotsDataResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;

class SasCmsSlotsDataResolverTest extends TestCase
{
    use ContextTrait;

    private CmsSlotsDataResolver $decorated;

    private CmsSlotCollection $slots;

    private ResolverContext $resolverContext;

    private array $resolvers;

    private SasCmsSlotsDataResolver $sasCmsSlotsDataResolver;

    public function setUp(): void
    {
        $this->decorated = $this->createMock(CmsSlotsDataResolver::class);

        $blogDetailResolver = $this->createMock(BlogDetailCmsElementResolver::class);
        $blogDetailResolver->method('getType')->willReturn('blog-detail');
        $this->resolvers[$blogDetailResolver->getType()] = $blogDetailResolver;

        $normalResolver = $this->createMock(CmsElementResolverInterface::class);
        $normalResolver->method('getType')->willReturn('normal-type');
        $this->resolvers[$normalResolver->getType()] = $normalResolver;

        $this->slots = new CmsSlotCollection();
        $this->resolverContext = $this->createMock(ResolverContext::class);

        $this->sasCmsSlotsDataResolver = new SasCmsSlotsDataResolver(
            $this->decorated,
            $this->resolvers,
        );
    }

    /**
     * This test verifies that the resolver method is correctly called
     * with various of slot types.
     *
     * @dataProvider getTestData
     */
    public function testResolve(
        string $slotType,
        bool $hasBlogEntry,
        bool $hasCmsPage,
        bool $hasCmsSections,
        int $setDataExpectedCalls,
        int $resolveExpectedCalls
    ): void {
        $cmsSections = $hasCmsSections ? $this->createCmsPageSections() : null;
        $cmsPage = $hasCmsPage ? $this->createCmsPage($cmsSections) : null;
        $blogEntry = $hasBlogEntry ? $this->createBlogEntry($cmsPage) : null;
        $cmsSlot = $this->createCmsSlot($slotType, $setDataExpectedCalls, $blogEntry);

        $this->slots->add($cmsSlot);
        $this->decorated
            ->expects(static::exactly($resolveExpectedCalls))
            ->method('resolve')
            ->willReturn($this->slots);

        $this->sasCmsSlotsDataResolver->resolve($this->slots, $this->resolverContext);
    }

    /**
     * Get test data with array structure:
     * - slot type
     * - has blog entry
     * - has cms page
     * - has cms sections
     * - expected number of calls for "setData" method
     * - expected number of calls for "resolve" method
     */
    public function getTestData(): array
    {
        return [
            'normal slot is skipped' => [
                'normal-type',
                false,
                false,
                false,
                0,
                1,
            ],
            'blog detail slot (with empty data( is skipped' => [
                'blog-detail',
                false,
                false,
                false,
                0,
                1,
            ],
            'blog detail slot (with empty cmsPage) is skipped' => [
                'blog-detail',
                true,
                false,
                false,
                0,
                1,
            ],
            'blog detail slot (with empty section) is skipped' => [
                'blog-detail',
                true,
                true,
                false,
                0,
                1,
            ],
            'blog detail slot is correctly resolved' => [
                'blog-detail',
                true,
                true,
                true,
                1,
                2,
            ],
        ];
    }

    /**
     * Create cms page section collection for testing.
     * It creates cms slots.
     * It creates cms blocks and config getSlots method to return above created cms slots.
     * It creates cms page section and configs getBlocks method to return above created cms blocks.
     */
    private function createCmsPageSections(): CmsSectionCollection
    {
        $cmsSlots = $this->createMock(CmsSlotCollection::class);

        $cmsBlocks = $this->createMock(CmsBlockCollection::class);
        $cmsBlocks->method('getSlots')->willReturn($cmsSlots);

        $cmsSections = $this->createMock(CmsSectionCollection::class);
        $cmsSections->expects(static::atLeast(2))->method('getBlocks')->willReturn($cmsBlocks);

        return $cmsSections;
    }

    /**
     * Create cms page for testing.
     * It also configs getSections method to return given cms sections.
     */
    private function createCmsPage(?CmsSectionCollection $cmsSections): CmsPageEntity
    {
        $cmsPage = $this->createMock(CmsPageEntity::class);
        $cmsPage->method('getSections')->willReturn($cmsSections);

        return $cmsPage;
    }

    /**
     * Create blog entry for testing.
     * It also configs getCmsPage method to return given cms page.
     */
    private function createBlogEntry(?CmsPageEntity $cmsPage): BlogEntriesEntity
    {
        $blogEntry = $this->createMock(BlogEntriesEntity::class);
        $blogEntry->method('getCmsPage')->willReturn($cmsPage);

        return $blogEntry;
    }

    /**
     * Create cms slot for testing.
     * It configs getType method to return "blog-detail".
     * It also configs getData method to return given blog entry.
     *
     * @return MockObject|CmsSlotEntity
     */
    private function createCmsSlot(string $slotType, int $expectedCalls, ?BlogEntriesEntity $blog): MockObject
    {
        $blogDetailSlot = $this->createMock(CmsSlotEntity::class);
        $blogDetailSlot
            ->method('getType')
            ->willReturn($slotType);
        $blogDetailSlot
            ->method('getData')
            ->willReturn($blog);
        $blogDetailSlot
            ->expects(static::exactly($expectedCalls))
            ->method('setData')
            ->with(static::isInstanceOf(BlogEntriesEntity::class));

        return $blogDetailSlot;
    }
}
