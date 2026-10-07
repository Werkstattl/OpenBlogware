<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1791367644NullableTranslatedBlogFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1791367644;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `werkl_blog_entry_translation`
            MODIFY COLUMN `title` VARCHAR(255) NULL,
            MODIFY COLUMN `slug` VARCHAR(255) NULL;
        ');

        $connection->executeStatement('
            ALTER TABLE `werkl_blog_category_translation`
            MODIFY COLUMN `name` VARCHAR(255) NULL;
        ');
    }
}
