<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Werkl\OpenBlogware\WerklOpenBlogware;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1605031477CreateWerklBlogAuthorTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1605031477;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `werkl_blog_author` (
                `id` BINARY(16) NOT NULL,
                `salutation_id` BINARY(16) NOT NULL,
                `media_id` BINARY(16) NULL,
                `first_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `last_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `display_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
                `email` VARCHAR(254) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.werkl_blog_author.salutation_id` (`salutation_id`),
                KEY `fk.werkl_blog_author.media_id` (`media_id`),
                CONSTRAINT `fk.werkl_blog_author.salutation_id` FOREIGN KEY (`salutation_id`)
                    REFERENCES `salutation` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT `fk.werkl_blog_author.media_id` FOREIGN KEY (`media_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `werkl_blog_author_translation` (
                `description` LONGTEXT NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                `werkl_blog_author_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`werkl_blog_author_id`,`language_id`),
                CONSTRAINT `json.werkl_blog_author_translation.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                KEY `fk.werkl_blog_author_translation.werkl_blog_author_id` (`werkl_blog_author_id`),
                KEY `fk.werkl_blog_author_translation.language_id` (`language_id`),
                CONSTRAINT `fk.werkl_blog_author_translation.werkl_blog_author_id` FOREIGN KEY (`werkl_blog_author_id`) REFERENCES `werkl_blog_author` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.werkl_blog_author_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $notSpecified = $connection->fetchOne('SELECT id from salutation LIMIT 1');

        $defaultAuthorId = $connection->fetchOne('SELECT id from werkl_blog_author LIMIT 1');

        if (empty($defaultAuthorId)) {
            $defaultAuthorId = Uuid::fromHexToBytes(WerklOpenBlogware::ANONYMOUS_AUTHOR_ID);

            $connection->insert('werkl_blog_author', [
                'id' => $defaultAuthorId,
                'first_name' => 'N/A',
                'last_name' => 'N/A',
                'salutation_id' => $notSpecified,
                'display_name' => 'Anonymous',
                'email' => 'N/A',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        }

        $connection->executeStatement('
            ALTER TABLE werkl_blog_entries ADD COLUMN author_id BINARY(16) NOT NULL DEFAULT \'' . $defaultAuthorId . '\' AFTER active;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
