<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Tests\Unit\Content\Cms\DataResolver;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\DataResolver\CmsSlotsDataResolver;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\Extension\CmsSlotsDataResolveExtension;
use Shopware\Core\Framework\Extensions\ExtensionDispatcher;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Content\Cms\DataResolver\CmsSlotsDataResolverExtension;
use Werkl\OpenBlogware\Tests\Unit\Traits\ContextTrait;

class CmsSlotsDataResolverExtensionTest extends TestCase
{
    use ContextTrait;

    private MockObject $resolver;

    private CmsSlotsDataResolverExtension $extension;

    private MockObject $resolverContext;

    protected function setUp(): void
    {
        $this->resolver = $this->createMock(CmsSlotsDataResolver::class);
        $this->resolverContext = $this->createMock(ResolverContext::class);

        /** @var CmsSlotsDataResolver $resolver */
        $resolver = $this->resolver;
        $this->extension = new CmsSlotsDataResolverExtension($resolver);
    }

    public function testGetSubscribedEvents(): void
    {
        $events = CmsSlotsDataResolverExtension::getSubscribedEvents();
        $eventName = ExtensionDispatcher::post(CmsSlotsDataResolveExtension::NAME);

        static::assertIsArray($events);
        static::assertArrayHasKey($eventName, $events);
        static::assertEquals('postResolve', $events[$eventName]);
    }

    /**
     * This test verifies that the postResolve method is correctly called
     * with various slot types and data configurations.
     */
    #[DataProvider('getTestData')]
    public function testPostResolve(
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

        $slots = new CmsSlotCollection(['test-slot' => $cmsSlot]);

        if ($resolveExpectedCalls > 0) {
            $this->resolver
                ->expects(static::exactly($resolveExpectedCalls))
                ->method('resolve')
                ->willReturn(new CmsSlotCollection());
        } else {
            $this->resolver
                ->expects(static::never())
                ->method('resolve');
        }

        // Create a real extension instance since it's a final class
        /** @var ResolverContext $resolverContext */
        $resolverContext = $this->resolverContext;
        $extensionData = new CmsSlotsDataResolveExtension($slots, $resolverContext);

        // Set up the extension result as would be done by the extension dispatcher
        $extensionData->result = $slots;

        $this->extension->postResolve($extensionData);
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
    public static function getTestData(): array
    {
        return [
            'normal slot is skipped' => [
                'normal-type',
                false,
                false,
                false,
                0,
                0,
            ],
            'blog detail slot (with empty data) is skipped' => [
                'blog-detail',
                false,
                false,
                false,
                0,
                0,
            ],
            'blog detail slot (with empty cmsPage) is skipped' => [
                'blog-detail',
                true,
                false,
                false,
                0,
                0,
            ],
            'blog detail slot (with empty section) is skipped' => [
                'blog-detail',
                true,
                true,
                false,
                0,
                0,
            ],
            'blog detail slot is correctly resolved' => [
                'blog-detail',
                true,
                true,
                true,
                1,
                1,
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
    private function createBlogEntry(?CmsPageEntity $cmsPage): BlogEntryEntity
    {
        $blogEntry = $this->createMock(BlogEntryEntity::class);
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
    private function createCmsSlot(string $slotType, int $expectedCalls, ?BlogEntryEntity $blog): MockObject
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
            ->with(static::isInstanceOf(BlogEntryEntity::class));

        return $blogDetailSlot;
    }
}
