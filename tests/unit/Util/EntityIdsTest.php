<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Tests\Unit\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Werkl\OpenBlogware\Util\EntityIds;

class EntityIdsTest extends TestCase
{
    /**
     * This test verifies that plain identifiers are returned as a list of strings.
     *
     * @param array<array-key, mixed> $identifiers
     * @param list<string> $expected
     */
    #[DataProvider('identifierProvider')]
    public function testNormalize(array $identifiers, array $expected): void
    {
        $actual = EntityIds::normalize($identifiers);

        static::assertSame($expected, $actual);
        static::assertSame(\array_keys($expected), \array_keys($actual));
    }

    /**
     * @return array<string, array{array<array-key, mixed>, list<string>}>
     */
    public static function identifierProvider(): array
    {
        return [
            'empty' => [[], []],
            'plain identifiers' => [['id-1', 'id-2'], ['id-1', 'id-2']],
            'keys are reindexed' => [['id-2' => 'id-2', 'id-1' => 'id-1'], ['id-2', 'id-1']],
            'duplicates are removed' => [['id-1', 'id-1', 'id-2'], ['id-1', 'id-2']],
            'empty identifiers are dropped' => [['', 'id-1'], ['id-1']],
            'composite primary keys are dropped' => [[['id' => 'id-1'], 'id-2'], ['id-2']],
            'null values are dropped' => [[null, 'id-1'], ['id-1']],
        ];
    }
}
