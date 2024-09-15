<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1649580844AddParentVersionId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1649580844;
    }

    public function update(Connection $connection): void
    {
        $version = Defaults::LIVE_VERSION;
        $connection->executeStatement('
            ALTER TABLE `werkl_blog_category`
            ADD `parent_version_id` BINARY(16) NULL AFTER `parent_id`,
            DROP FOREIGN KEY `fk.werkl_blog_category.parent_id`,
            DROP INDEX `fk.werkl_blog_category.parent_id`;
        ');
        $connection->executeStatement('
            ALTER TABLE `werkl_blog_category`
            ADD CONSTRAINT `fk.werkl_blog_category.parent_id`
                FOREIGN KEY (`parent_id`, `parent_version_id`)
                REFERENCES `werkl_blog_category` (`id`, `version_id`)
                ON DELETE CASCADE ON UPDATE CASCADE;
        ');
        $connection->executeStatement(
            '
            UPDATE `werkl_blog_category` SET parent_version_id = unhex(:version)
        ',
            ['version' => $version]
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
