<?php declare(strict_types=1);

namespace Sas\BlogModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1647338771SasBlogEntriesUpdate extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1647338771;
    }

    public function update(Connection $connection): void
    {
        $this->updateSchema($connection);
        $this->createDeleteTrigger($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    public function updateSchema(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `sas_blog_entries`
            ADD `cms_page_id` BINARY(16) NULL AFTER `id`,
            ADD `cms_page_version_id` binary(16) NULL AFTER `cms_page_id`,

            ADD CONSTRAINT `fk.sas_blog_entries.cms_page_id`
                FOREIGN KEY (`cms_page_id`, `cms_page_version_id`)
                REFERENCES `cms_page` (`id`, `version_id`)
                ON DELETE RESTRICT ON UPDATE CASCADE;
        ');
    }

    private function createDeleteTrigger(Connection $connection): void
    {
        $query
            = 'CREATE TRIGGER sas_blog_entries_delete AFTER DELETE ON sas_blog_entries
            FOR EACH ROW
            BEGIN
                IF @TRIGGER_DISABLED IS NULL OR @TRIGGER_DISABLED = 0 THEN
                IF (OLD.cms_page_id IS NOT NULL) THEN
                    DELETE FROM cms_page WHERE id = OLD.cms_page_id;
                END IF;
                END IF;
            END;';

        $this->createTrigger($connection, $query);
    }
}
