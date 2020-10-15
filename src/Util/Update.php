<?php declare(strict_types=1);

namespace Sas\BlogModule\Util;

use Psr\Container\ContainerInterface;
use Sas\BlogModule\Content\Blog\BlogEntriesDefinition;
use Sas\BlogModule\Migration\Migration1602739765AddTeaserImageColumnToBlogEntries;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

use Doctrine\DBAL\Connection;

class Update
{
    public function update(ContainerInterface $container, UpdateContext $updateContext): void
    {
        if (\version_compare($updateContext->getCurrentPluginVersion(), '1.1.0', '<')) {
            $this->updateTo110($container);
        }
    }

    private function updateTo110(ContainerInterface $container): void
    {
        /** @var Connection $connection */
        $connection = $container->get(Connection::class);

        $blogEntriesEntityName = BlogEntriesDefinition::ENTITY_NAME;
        if (!$connection->getSchemaManager()->tablesExist([$blogEntriesEntityName])) {
            $blogEntriesMigration = new Migration1602739765AddTeaserImageColumnToBlogEntries();
            $blogEntriesMigration->update($connection);
        }

    }
}
