<?php declare(strict_types=1);

namespace BlogModule\Tests\Util;

use BlogModule\Tests\Traits\ContextTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Sas\BlogModule\Util\Update;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class UpdateTest extends TestCase
{
    use ContextTrait;

    private AbstractSchemaManager $schemaManager;

    private ContainerInterface $container;

    private UpdateContext $updateContext;

    private Update $update;

    public function setUp(): void
    {
        $this->schemaManager = $this
            ->getMockBuilder(AbstractSchemaManager::class)
            ->onlyMethods(['tablesExist'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $connection = $this->createMock(Connection::class);
        $connection->expects(static::any())->method('getSchemaManager')->willReturn($this->schemaManager);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects(static::any())->method('get')->willReturn($connection);

        $this->updateContext = $this->createMock(UpdateContext::class);

        $this->update = new Update();
    }

    /**
     * This test verifies that the update method is correctly called
     * with various of plugin versions.
     *
     * @dataProvider getVersionTestData
     */
    public function testUpdate(
        string $version,
        int $getCurrentPluginVersionExpectedCalls,
        int $tablesExistExpectedCalls
    ): void {
        $this->updateContext->expects(static::exactly($getCurrentPluginVersionExpectedCalls))
            ->method('getCurrentPluginVersion')
            ->willReturn($version);
        $this->schemaManager->expects(static::exactly($tablesExistExpectedCalls))
            ->method('tablesExist')
            ->with(static::callback(function ($object): bool {
                static::assertCount(1, $object);

                return true;
            }))
            ->willReturn(false);
        $this->update->update($this->container, $this->updateContext);
    }

    /**
     * Get test data with array structure:
     * - version
     * - expected number of calls for getCurrentPluginVersion method
     * - expected number of calls for tablesExist method
     */
    public function getVersionTestData(): array
    {
        return [
            'Plugin version lower than 1.1.0' => ['1.0.0', 2, 2],
            'Plugin version 1.1.0 and higher' => ['1.1.0', 2, 1],
            'Plugin version 1.3.0 and higher' => ['1.3.0', 2, 0],
        ];
    }
}
