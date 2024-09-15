<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Util;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class Lifecycle
{
    private SystemConfigService $systemConfig;

    private EntityRepository $cmsPageRepository;

    public function __construct(
        SystemConfigService $systemConfig,
        EntityRepository $cmsPageRepository
    ) {
        $this->systemConfig = $systemConfig;
        $this->cmsPageRepository = $cmsPageRepository;
    }

    public function install(Context $context): void
    {
        $this->createBlogCmsListingPage($context);
    }

    private function createBlogCmsListingPage(Context $context): void
    {
        $blogListingCmsPageId = Uuid::randomHex();
        $blogDetailCmsPageId = Uuid::randomHex();

        $cmsPage = $this->createCmsPagesData($blogListingCmsPageId, $blogDetailCmsPageId);

        $this->cmsPageRepository->create($cmsPage, $context);
        $this->systemConfig->set('WerklOpenBlogware.config.cmsBlogDetailPage', $blogDetailCmsPageId);
    }

    private function createCmsPagesData(string $blogListingCmsPageId, string $blogDetailCmsPageId): array
    {
        $blogListingCmsPageData = $this->createBlogListingCmsPageData($blogListingCmsPageId);
        $blogDetailCmsPageData = $this->createBlogDetailCmsPageData($blogDetailCmsPageId);

        return [
            $blogListingCmsPageData,
            $blogDetailCmsPageData,
        ];
    }

    private function createBlogListingCmsPageData(string $blogListingCmsPageId): array
    {
        return [
            'id' => $blogListingCmsPageId,
            'type' => 'page',
            'name' => 'Blog Listing',
            'sections' => [
                [
                    'id' => Uuid::randomHex(),
                    'type' => 'default',
                    'position' => 0,
                    'blocks' => [
                        [
                            'position' => 1,
                            'type' => 'blog-listing',
                            'slots' => [
                                ['type' => 'blog', 'slot' => 'listing'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function createBlogDetailCmsPageData(string $blogDetailCmsPageId): array
    {
        return [
            'id' => $blogDetailCmsPageId,
            'type' => 'page',
            'name' => 'Blog Detail',
            'sections' => [
                [
                    'id' => Uuid::randomHex(),
                    'type' => 'default',
                    'position' => 0,
                    'blocks' => [
                        [
                            'position' => 1,
                            'type' => 'blog-detail',
                            'slots' => [
                                ['type' => 'blog-detail', 'slot' => 'blogDetail'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
