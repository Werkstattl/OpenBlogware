<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1626760242AddCustomFieldToBlogTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1626760242;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `werkl_blog_entries_translation`
                ADD COLUMN `custom_fields` json DEFAULT NULL AFTER `content`;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
