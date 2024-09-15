<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Werkl\OpenBlogware\Content\Blog\Aggregate\BlogCategoryMappingDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryDefinition;
use Werkl\OpenBlogware\Content\BlogCategory\BlogCategoryTranslation\BlogCategoryTranslationDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Migration\Traits\ImportTranslationsTrait;
use Shopware\Core\Migration\Traits\Translations;

class Migration1604520733DefaultBlogCategorySeeder extends MigrationStep
{
    use ImportTranslationsTrait;

    public function getCreationTimestamp(): int
    {
        return 1604520733;
    }

    public function update(Connection $connection): void
    {
        $categoryId = $this->createRootCategory($connection);

        $translations = new Translations(
            [
                'werkl_blog_category_id' => $categoryId,
                'name' => 'Allgemeines',
            ],
            [
                'werkl_blog_category_id' => $categoryId,
                'name' => 'General',
            ]
        );

        $this->importTranslation(BlogCategoryTranslationDefinition::ENTITY_NAME, $translations, $connection);

        $blogs = $connection->fetchAllAssociative('SELECT id FROM ' . BlogEntriesDefinition::ENTITY_NAME) ? [] : null;

        $queue = new MultiInsertQueryQueue($connection, 50);

        if (empty($blogs)) {
            return;
        }

        foreach ($blogs as $insert) {
            $insert['werkl_blog_category_id'] = $categoryId;
            $insert['werkl_blog_entries_id'] = $insert['id'];
            unset($insert['id']);

            $queue->addInsert(BlogCategoryMappingDefinition::ENTITY_NAME, $insert);
        }

        $queue->execute();
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function createRootCategory(Connection $connection): string
    {
        $id = Uuid::randomBytes();

        $connection->insert(BlogCategoryDefinition::ENTITY_NAME, ['id' => $id, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        return $id;
    }
}
