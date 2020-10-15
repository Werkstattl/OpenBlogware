<?php declare(strict_types=1);
namespace Sas\BlogModule;

use Doctrine\DBAL\Connection;
use Sas\BlogModule\Util\Lifecycle;
use Sas\BlogModule\Util\Update;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SasBlogModule extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->createBlogMediaFolder($installContext);

        $this->getLifeCycle()->install($installContext->getContext());
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0;');
        $connection->executeQuery('DROP TABLE IF EXISTS `sas_blog_entries`');
        $connection->executeQuery('DROP TABLE IF EXISTS `sas_blog_entries_translation`');
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function update(UpdateContext $updateContext): void
    {
        (new Update())->update($this->container, $updateContext);

        parent::update($updateContext);
    }

    /**
     * We need to create a folder for the blog media with it's,
     * own configuration to generate thumbnails for the teaser image.
     *
     * @param $installContext
     */
    public function createBlogMediaFolder(InstallContext $installContext): void
    {
        /** @var EntityRepositoryInterface $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_folder.repository');

        $folderId = Uuid::randomHex();
        $configurationId = Uuid::randomHex();

        $mediaFolderRepository->create([
            [
                'entity' => 'sas_blog_entries',
                'name' => 'Blog Media',
                'associationFields' => ['media'],
                'folder' => [
                    'id' => $folderId,
                    'name' => 'Blog Media',
                    'configuration' => [
                        'id' => $configurationId,
                        'private' => false,
                        'createThumbnails'=> true,
                        'thumbnailQuality' => 80
                    ],
                ],
            ],
        ], $installContext->getContext());
    }

    private function getLifeCycle(): Lifecycle
    {
        /** @var SystemConfigService $systemConfig */
        $systemConfig = $this->container->get(SystemConfigService::class);

        /** @var EntityRepositoryInterface $cmsPageRepository */
        $cmsPageRepository = $this->container->get('cms_page.repository');

        return new Lifecycle(
            $systemConfig,
            $cmsPageRepository
        );
    }
}
