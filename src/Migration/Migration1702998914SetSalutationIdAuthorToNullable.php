<?php declare(strict_types=1);

namespace Sas\BlogModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1702998914SetSalutationIdAuthorToNullable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702998914;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `sas_blog_author` MODIFY `salutation_id` BINARY(16) NULL;');
        $connection->executeStatement('ALTER TABLE `sas_blog_author` DROP FOREIGN KEY `fk.sas_blog_author.salutation_id`;');
        $connection->executeStatement('ALTER TABLE `sas_blog_author` ADD CONSTRAINT `fk.sas_blog_author.salutation_id` FOREIGN KEY (`salutation_id`) REFERENCES `salutation` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
