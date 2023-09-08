<?php declare(strict_types=1);

namespace Sas\BlogModule;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Result;
use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Content\Blog\BlogSeoUrlRoute;
use Sas\BlogModule\Content\Blog\Events\BlogIndexerEvent;
use Sas\BlogModule\Util\Lifecycle;
use Sas\BlogModule\Util\Update;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeEntity;
use Shopware\Core\Content\Seo\SeoUrlTemplate\SeoUrlTemplateCollection;
use Shopware\Core\Content\Seo\SeoUrlTemplate\SeoUrlTemplateEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Kernel;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SasBlogModule extends Plugin
{
    public const ANONYMOUS_AUTHOR_ID = '64f4c60194634128b9b85d9299797c45';

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->createBlogMediaFolder($installContext->getContext());

        $this->getLifeCycle()->install($installContext->getContext());
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        // Always remove the SEO url template to avoid error that can't be changed afterward.
        $this->deleteSeoUrlTemplate($context->getContext());

        if ($context->keepUserData()) {
            return;
        }

        /*
         * We need to uninstall our default media folder,
         * the media folder and the thumbnail sizes.
         * However, we have to clean this up within a next update :)
         */
        $this->deleteMediaFolder($context->getContext());
        $this->deleteDefaultMediaFolder($context->getContext());

        /**
         * And of course we need to drop our tables
         */
        $connection = Kernel::getConnection();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_entries`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_entries_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_blog_category`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_category_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_category`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_author_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sas_blog_author`');

        /** @var EntityRepository $cmsBlockRepo */
        $cmsBlockRepo = $this->container->get('cms_block.repository');

        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('type', ['blog-detail', 'blog-listing']));

        $cmsBlocks = $cmsBlockRepo->searchIds($criteria, $context);

        $cmsBlockRepo->delete(array_values($cmsBlocks->getData()), $context);

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);

        (new Update())->update($this->container, $updateContext);

        if (version_compare($updateContext->getCurrentPluginVersion(), '1.1.0', '<')) {
            $this->createBlogMediaFolder($updateContext->getContext());
        }

        if (version_compare($updateContext->getCurrentPluginVersion(), '1.5.10', '<')) {
            $this->fixSeoUrlTemplate($updateContext->getContext());
            $this->updateSeoUrls($updateContext->getContext());
        }
    }

    /**
     * We need to create a folder for the blog media with it's,
     * own configuration to generate thumbnails for the teaser image.
     */
    public function createBlogMediaFolder(Context $context): void
    {
        $this->deleteDefaultMediaFolder($context);
        $thumbnailSizes = $this->getThumbnailSizes($context);

        /** @var EntityRepository $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_default_folder.repository');

        $data = [
            [
                'entity' => BlogEntriesDefinition::ENTITY_NAME,
                'associationFields' => ['media'],
                'folder' => [
                    'name' => 'Blog Images',
                    'useParentConfiguration' => false,
                    'configuration' => [
                        'createThumbnails' => true,
                        'keepAspectRatio' => true,
                        'thumbnailQuality' => 90,
                        'mediaThumbnailSizes' => $thumbnailSizes,
                    ],
                ],
            ],
        ];

        $mediaFolderRepository->create($data, $context);
    }

    private function deleteDefaultMediaFolder(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsAnyFilter('entity', [
                BlogEntriesDefinition::ENTITY_NAME,
            ])
        );

        /** @var EntityRepository $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_default_folder.repository');

        $mediaFolderIds = $mediaFolderRepository->searchIds($criteria, $context)->getIds();

        if (!empty($mediaFolderIds)) {
            $ids = array_map(static function ($id) {
                return ['id' => $id];
            }, $mediaFolderIds);
            $mediaFolderRepository->delete($ids, $context);
        }
    }

    private function deleteMediaFolder(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('name', 'Blog Images')
        );

        /** @var EntityRepository $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_folder.repository');

        $mediaFolderRepository->search($criteria, $context);

        $mediaFolderIds = $mediaFolderRepository->searchIds($criteria, $context)->getIds();

        if (!empty($mediaFolderIds)) {
            $ids = array_map(static function ($id) {
                return ['id' => $id];
            }, $mediaFolderIds);
            $mediaFolderRepository->delete($ids, $context);
        }
    }

    private function deleteSeoUrlTemplate(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('entityName', 'sas_blog_entries')
        );

        /** @var EntityRepository $seoUrlTemplateRepository */
        $seoUrlTemplateRepository = $this->container->get('seo_url_template.repository');

        $seoUrlTemplateRepository->search($criteria, $context);

        $seoUrlTemplateIds = $seoUrlTemplateRepository->searchIds($criteria, $context)->getIds();

        if (!empty($seoUrlTemplateIds)) {
            $ids = array_map(static function ($id) {
                return ['id' => $id];
            }, $seoUrlTemplateIds);
            $seoUrlTemplateRepository->delete($ids, $context);
        }
    }

    private function getThumbnailSizes(Context $context): array
    {
        $mediaThumbnailSizes = [
            '330x185' => [
                'width' => 330,
                'height' => 185,
            ],
            '650x365' => [
                'width' => 650,
                'height' => 365,
            ],
            '900x506' => [
                'width' => 900,
                'height' => 506,
            ],
            '1280x720' => [
                'width' => 1280,
                'height' => 720,
            ],
        ];

        $criteria = new Criteria();

        /** @var EntityRepository $thumbnailSizeRepository */
        $thumbnailSizeRepository = $this->container->get('media_thumbnail_size.repository');

        $thumbnailSizes = $thumbnailSizeRepository->search($criteria, $context)->getEntities();

        $mediaThumbnailSizesAddedIds = [];
        /** @var MediaThumbnailSizeEntity $thumbnailSize */
        foreach ($thumbnailSizes as $thumbnailSize) {
            $key = $thumbnailSize->getWidth() . 'x' . $thumbnailSize->getHeight();
            if (\array_key_exists($key, $mediaThumbnailSizes)) {
                $mediaThumbnailSize = $mediaThumbnailSizes[$key];
                $mediaThumbnailSizesAddedIds[$key] = array_merge(
                    ['id' => $thumbnailSize->getId()],
                    $mediaThumbnailSize,
                );
                unset($mediaThumbnailSizes[$key]);
            }
        }

        $mediaThumbnailSizesCreateData = [];
        foreach ($mediaThumbnailSizes as $key => $mediaThumbnailSize) {
            $data = array_merge(
                ['id' => Uuid::randomHex()],
                $mediaThumbnailSize,
            );

            $mediaThumbnailSizesCreateData[$key] = $data;
            $mediaThumbnailSizesAddedIds[$key] = $data;
        }

        if (\count($mediaThumbnailSizesCreateData) > 0) {
            $thumbnailSizeRepository->create(array_values($mediaThumbnailSizesCreateData), $context);
        }

        return array_values($mediaThumbnailSizesAddedIds);
    }

    private function getLifeCycle(): Lifecycle
    {
        /** @var SystemConfigService $systemConfig */
        $systemConfig = $this->container->get(SystemConfigService::class);

        /** @var EntityRepository $cmsPageRepository */
        $cmsPageRepository = $this->container->get('cms_page.repository');

        return new Lifecycle(
            $systemConfig,
            $cmsPageRepository
        );
    }

    private function fixSeoUrlTemplate(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('routeName', BlogSeoUrlRoute::ROUTE_NAME));
        $criteria->addFilter(new EqualsFilter('entityName', BlogEntriesDefinition::ENTITY_NAME));
        $criteria->addFilter(new NotFilter(
            NotFilter::CONNECTION_AND,
            [new EqualsFilter('template', null)]
        ));

        /** @var EntityRepository $seoUrlTemplateRepository */
        $seoUrlTemplateRepository = $this->container->get('seo_url_template.repository');

        /** @var SeoUrlTemplateCollection $seoUrlTemplates */
        $seoUrlTemplates = $seoUrlTemplateRepository->search($criteria, $context)->getEntities();

        $update = [];
        /** @var SeoUrlTemplateEntity $seoUrlTemplate */
        foreach ($seoUrlTemplates as $seoUrlTemplate) {
            // If the clients fixed it in SEO template we don't have to do again
            if (strpos($seoUrlTemplate->getTemplate(), 'entry.translated')) {
                continue;
            }

            // We found the issue
            if (!strpos($seoUrlTemplate->getTemplate(), 'entry.title')) {
                continue;
            }

            $templateReplaced = str_replace('entry.title', 'entry.translated.title', $seoUrlTemplate->getTemplate());
            if (!\is_string($templateReplaced)) {
                continue;
            }

            $update[] = [
                'id' => $seoUrlTemplate->getId(),
                'template' => $templateReplaced,
            ];
        }

        if (\count($update) === 0) {
            return;
        }

        $seoUrlTemplateRepository->update($update, $context);
    }

    private function updateSeoUrls(Context $context): void
    {
        $blogArticlesIds = $this->getBlogArticlesIds();
        if (\count($blogArticlesIds) === 0) {
            return;
        }

        if ($this->container->get('event_dispatcher') instanceof EventDispatcherInterface) {
            $eventDispatcher = $this->container->get('event_dispatcher');
            $eventDispatcher->dispatch(new BlogIndexerEvent($blogArticlesIds, $context));
        }
    }

    private function getBlogArticlesIds(): array
    {
        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        if (!$connection->getSchemaManager()->tablesExist([BlogEntriesDefinition::ENTITY_NAME])) {
            return [];
        }

        $now = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $query = $connection->createQueryBuilder();
        $query->select([
            'LOWER(HEX(id)) as id',
        ]);
        $query->where('active = true')->andWhere('published_at <= :now');
        $query->setParameter('now', $now);
        $query->from(BlogEntriesDefinition::ENTITY_NAME);
        if (!$query->execute() instanceof Result) {
            return [];
        }
        $results = $query->execute()->fetchAllAssociative();

        if (empty($results)) {
            return [];
        }

        return array_column($results, 'id');
    }
}
