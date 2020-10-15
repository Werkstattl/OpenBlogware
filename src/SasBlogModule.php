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
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SasBlogModule extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

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
