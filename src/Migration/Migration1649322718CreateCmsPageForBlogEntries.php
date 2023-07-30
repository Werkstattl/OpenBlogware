<?php declare(strict_types=1);

namespace Sas\BlogModule\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsPageTranslation\CmsPageTranslationDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlotTranslation\CmsSlotTranslationDefinition;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1649322718CreateCmsPageForBlogEntries extends MigrationStep
{
    private const TEMPLATE = <<<'SQL'
UPDATE `sas_blog_entries` SET `cms_page_id` = UNHEX("%s"), `cms_page_version_id` = UNHEX("%s") WHERE `id` = UNHEX("%s");

SQL;

    private array $cmsPageQueue = [];

    private array $cmsPageTranslationQueue = [];

    private array $cmsSectionQueue = [];

    private array $cmsBlockQueue = [];

    private array $cmsSlotQueue = [];

    private array $cmsSlotTranslationQueue = [];

    public function getCreationTimestamp(): int
    {
        return 1649322718;
    }

    public function update(Connection $connection): void
    {
        $connection->beginTransaction();

        try {
            $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);
            $createdAt = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            $updateSql = '';

            $blogEntries = $connection->fetchAllAssociative('SELECT id FROM sas_blog_entries WHERE cms_page_id IS NULL');
            foreach ($blogEntries as $blogEntry) {
                $blogTranslations = $connection->fetchAllAssociative(
                    'SELECT language_id, title, content FROM sas_blog_entries_translation WHERE sas_blog_entries_id = :blogEntryId',
                    ['blogEntryId' => $blogEntry['id']]
                );

                $cmsPageId = $this->createCmsPage($blogTranslations, $versionId, $createdAt);
                $updateSql .= sprintf(
                    self::TEMPLATE,
                    Uuid::fromBytesToHex($cmsPageId),
                    Uuid::fromBytesToHex($versionId),
                    Uuid::fromBytesToHex($blogEntry['id']),
                );
            }

            $this->insertCmsPages($connection);

            if ($updateSql !== '') {
                $connection->executeStatement($updateSql);
            }

            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollBack();

            throw $e;
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function createCmsPage(array $blogTranslations, string $versionId, string $createdAt): string
    {
        $cmsPageId = Uuid::randomBytes();
        $this->cmsPageQueue[] = [
            'id' => $cmsPageId,
            'version_id' => $versionId,
            'type' => 'blog_detail',
            'locked' => 0,
            'created_at' => $createdAt,
        ];

        $cmsSectionId = Uuid::randomBytes();
        $this->cmsSectionQueue[] = [
            'id' => $cmsSectionId,
            'version_id' => $versionId,
            'cms_page_id' => $cmsPageId,
            'cms_page_version_id' => $versionId,
            'position' => 0,
            'type' => 'default',
            'locked' => 0,
            'sizing_mode' => 'boxed',
            'mobile_behavior' => 'wrap',
            'created_at' => $createdAt,
        ];

        $cmsBlockId = Uuid::randomBytes();
        $this->cmsBlockQueue[] = [
            'id' => $cmsBlockId,
            'version_id' => $versionId,
            'cms_section_id' => $cmsSectionId,
            'cms_section_version_id' => $versionId,
            'position' => 0,
            'section_position' => 'main',
            'type' => 'text',
            'margin_top' => '20px',
            'margin_bottom' => '20px',
            'margin_left' => '20px',
            'margin_right' => '20px',
            'created_at' => $createdAt,
        ];

        $cmsSlotId = Uuid::randomBytes();
        $this->cmsSlotQueue[] = [
            'id' => $cmsSlotId,
            'version_id' => $versionId,
            'cms_block_id' => $cmsBlockId,
            'cms_block_version_id' => $versionId,
            'type' => 'text',
            'slot' => 'content',
            'created_at' => $createdAt,
        ];

        foreach ($blogTranslations as $blogTranslation) {
            $content = [
                'content' => [
                    'source' => 'static',
                    'value' => $blogTranslation['content'],
                ],
                'verticalAlign' => [
                    'source' => 'static',
                    'value' => null,
                ],
            ];

            $this->cmsPageTranslationQueue[] = [
                'cms_page_id' => $cmsPageId,
                'cms_page_version_id' => $versionId,
                'language_id' => $blogTranslation['language_id'],
                'name' => $blogTranslation['title'],
                'created_at' => $createdAt,
            ];

            $this->cmsSlotTranslationQueue[] = [
                'cms_slot_id' => $cmsSlotId,
                'cms_slot_version_id' => $versionId,
                'language_id' => $blogTranslation['language_id'],
                'config' => json_encode($content),
                'created_at' => $createdAt,
            ];
        }

        return $cmsPageId;
    }

    private function insertCmsPages(Connection $connection): void
    {
        $queue = new MultiInsertQueryQueue($connection, 50);

        foreach ($this->cmsPageQueue as $data) {
            $queue->addInsert(CmsPageDefinition::ENTITY_NAME, $data);
        }

        foreach ($this->cmsPageTranslationQueue as $data) {
            $queue->addInsert(CmsPageTranslationDefinition::ENTITY_NAME, $data);
        }

        foreach ($this->cmsSectionQueue as $data) {
            $queue->addInsert(CmsSectionDefinition::ENTITY_NAME, $data);
        }

        foreach ($this->cmsBlockQueue as $data) {
            $queue->addInsert(CmsBlockDefinition::ENTITY_NAME, $data);
        }

        foreach ($this->cmsSlotQueue as $data) {
            $queue->addInsert(CmsSlotDefinition::ENTITY_NAME, $data);
        }

        foreach ($this->cmsSlotTranslationQueue as $data) {
            $queue->addInsert(CmsSlotTranslationDefinition::ENTITY_NAME, $data);
        }

        $queue->execute();
    }
}
