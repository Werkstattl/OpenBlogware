<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1621260479AddVersionIdToBlogCategoryTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1612160298;
    }

    public function update(Connection $connection): void
    {
        $version = Defaults::LIVE_VERSION;
        $connection->executeStatement('
            ALTER TABLE `werkl_blog_category` add `version_id` BINARY(16) NOT NULL AFTER `id`,
            DROP PRIMARY KEY , ADD PRIMARY KEY ( `id`, `version_id` );
        ');

        $connection->executeStatement('
            ALTER TABLE `werkl_blog_category_translation`
            DROP FOREIGN KEY `fk.werkl_blog_category_translation.werkl_blog_category_id`,
            DROP KEY `fk.werkl_blog_category_translation.werkl_blog_category_id`;
        ');

        $connection->executeStatement('
            ALTER TABLE `werkl_blog_blog_category`
            DROP FOREIGN KEY `fk.werkl_blog_blog_category.werkl_blog_category_id`,
            DROP KEY `fk.werkl_blog_blog_category.werkl_blog_category_id`;
        ');

        $connection->executeStatement('
            ALTER TABLE `werkl_blog_category_translation`
            ADD `werkl_blog_category_version_id` BINARY(16) NOT NULL AFTER `name`,
            DROP PRIMARY KEY, ADD PRIMARY KEY ( `werkl_blog_category_id`, `language_id`, `werkl_blog_category_version_id` ),
            ADD CONSTRAINT `fk.werkl_blog_category_translation.werkl_blog_category_id` FOREIGN KEY (`werkl_blog_category_id`, `werkl_blog_category_version_id`)
            REFERENCES `werkl_blog_category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $connection->executeStatement('
            ALTER TABLE `werkl_blog_blog_category`
            ADD `werkl_blog_category_version_id` BINARY(16) NOT NULL AFTER `werkl_blog_category_id`,
            DROP PRIMARY KEY, ADD PRIMARY KEY ( `werkl_blog_entries_id`, `werkl_blog_category_id`, `werkl_blog_category_version_id` ),
            ADD CONSTRAINT `fk.werkl_blog_blog_category.werkl_blog_category_id` FOREIGN KEY (`werkl_blog_category_id`, `werkl_blog_category_version_id`)
            REFERENCES `werkl_blog_category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $connection->executeStatement('
            UPDATE `werkl_blog_category` SET version_id = unhex(:version)
        ', [
            'version' => $version,
        ]);
        $connection->executeStatement('
            UPDATE `werkl_blog_category_translation` SET werkl_blog_category_version_id = unhex(:version)
        ', [
            'version' => $version,
        ]);
        $connection->executeStatement('
            UPDATE `werkl_blog_blog_category` SET werkl_blog_category_version_id = unhex(:version)
        ', [
            'version' => $version,
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
