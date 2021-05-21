<?php declare(strict_types=1);

namespace Sas\BlogModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1602739765AddTeaserImageColumnToBlogEntries extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1602739765;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE
              sas_blog_entries
            ADD
              COLUMN media_id BINARY(16) DEFAULT NULL
            AFTER
              id
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
