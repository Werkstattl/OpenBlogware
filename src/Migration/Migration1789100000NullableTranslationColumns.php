<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1789100000NullableTranslationColumns extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1789100000;
    }

    public function update(Connection $connection): void
    {
        /**
         * Translated columns must be nullable so values can fall back through the language
         * inheritance, as enforced by `dal:validate` (DefinitionValidator::validateTranslatedColumnsAreNullable).
         */
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

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
