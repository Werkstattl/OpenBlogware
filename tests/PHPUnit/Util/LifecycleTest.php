<?php declare(strict_types=1);

namespace BlogModule\Tests\Util;

use BlogModule\Tests\Traits\ContextTrait;
use PHPUnit\Framework\TestCase;
use Sas\BlogModule\Util\Lifecycle;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\Event\NestedEventCollection;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class LifecycleTest extends TestCase
{
    use ContextTrait;

    private SystemConfigService $systemConfigService;

    private EntityRepository $cmsPageRepository;

    public function setUp(): void
    {
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
        $this->cmsPageRepository = $this->createMock(EntityRepository::class);

        $this->lifeCircle = new Lifecycle(
            $this->systemConfigService,
            $this->cmsPageRepository,
        );
    }

    /**
     * This test verifies that the create cms page methods
     * are returning the correct data array.
     *
     * @dataProvider getCmsPageTestData
     *
     * @throws \ReflectionException
     */
    public function testCmsPageData(string $method, string $blockType, string $slotType, string $slotName): void
    {
        $blogListingCmsPageId = '12345678901234567890123456789012';
        $reflectionClass = new \ReflectionClass(Lifecycle::class);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);
        $actualResult = $reflectionMethod->invokeArgs($this->lifeCircle, [$blogListingCmsPageId]);

        static::assertIsArray($actualResult);

        static::assertArrayHasKey('id', $actualResult);
        static::assertArrayHasKey('type', $actualResult);
        static::assertArrayHasKey('name', $actualResult);
        static::assertArrayHasKey('sections', $actualResult);
        static::assertSame('page', $actualResult['type']);

        $actualSections = $actualResult['sections'];
        static::assertCount(1, $actualSections);
        static::assertArrayHasKey('id', $actualSections[0]);
        static::assertArrayHasKey('type', $actualSections[0]);
        static::assertArrayHasKey('position', $actualSections[0]);
        static::assertArrayHasKey('blocks', $actualSections[0]);
        static::assertSame(0, $actualSections[0]['position']);
        static::assertSame('default', $actualSections[0]['type']);

        $actualBlocks = $actualSections[0]['blocks'];
        static::assertCount(1, $actualBlocks);
        static::assertArrayHasKey('type', $actualBlocks[0]);
        static::assertArrayHasKey('position', $actualBlocks[0]);
        static::assertArrayHasKey('slots', $actualBlocks[0]);
        static::assertSame(1, $actualBlocks[0]['position']);
        static::assertSame($blockType, $actualBlocks[0]['type']);

        $actualSlots = $actualBlocks[0]['slots'];
        static::assertCount(1, $actualSlots);
        static::assertArrayHasKey('type', $actualSlots[0]);
        static::assertArrayHasKey('slot', $actualSlots[0]);
        static::assertSame($slotType, $actualSlots[0]['type']);
        static::assertSame($slotName, $actualSlots[0]['slot']);
    }

    /**
     * This test verifies that the create createCmsPagesData method
     * is returning the correct array with two cms pages.
     *
     * @throws \ReflectionException
     */
    public function testCreateCmsPagesData(): void
    {
        $blogListingCmsPageId = '12345678901234567890123456789012';
        $blogDetailCmsPageId = '12345678901234567890123456789012';

        $reflectionClass = new \ReflectionClass(Lifecycle::class);
        $reflectionMethod = $reflectionClass->getMethod('createCmsPagesData');
        $reflectionMethod->setAccessible(true);
        $actualResult = $reflectionMethod->invokeArgs($this->lifeCircle, [$blogListingCmsPageId, $blogDetailCmsPageId]);

        static::assertIsArray($actualResult);
        static::assertCount(2, $actualResult);
    }

    /**
     * This test verifies that the "install" method is correctly called
     * to create two cms pages and add detail page id to the system config.
     */
    public function testInstall(): void
    {
        $context = $this->getContext($this);
        $createResults = new EntityWrittenContainerEvent($context, new NestedEventCollection(), []);
        $this->systemConfigService->expects(static::once())->method('set');
        $this->cmsPageRepository->expects(static::once())->method('create')->willReturn($createResults);

        $this->lifeCircle->install($context);
    }

    /**
     * Get test data with array structure:
     * - method
     * - block type
     * - slot type
     * - slot name
     */
    public function getCmsPageTestData(): array
    {
        return [
            'Test method createBlogListingCmsPageData' => [
                'createBlogListingCmsPageData',
                'blog-listing',
                'blog',
                'listing',
            ],
            'Test method createBlogDetailCmsPageData' => [
                'createBlogDetailCmsPageData',
                'blog-detail',
                'blog-detail',
                'blogDetail',
            ],
        ];
    }
}
