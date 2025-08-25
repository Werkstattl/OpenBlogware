<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1756141645UpdateSeoUrlTemplate extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1756141645;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery(
            <<<SQL
                    UPDATE `seo_url_template`
                    SET `entity_name` = 'werkl_blog_entry'
                    WHERE `entity_name` = 'werkl_blog_entries'
                SQL
        );
    }
}
