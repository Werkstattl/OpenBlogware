<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Util;

/**
 * Normalizes entity identifiers that the data abstraction layer hands out.
 *
 * Depending on the Shopware version the DAL announces its identifiers either as plain strings or,
 * because it also supports entities with a composite primary key, as `array|string`. Every entity
 * of this plugin uses a single string identifier, so the array shape only exists inside the type
 * signatures of the supported Shopware versions. Normalizing it once keeps all callers typed for
 * every supported version instead of silencing the difference at each call site.
 */
final class EntityIds
{
    /**
     * @param array<array-key, array<array-key, mixed>|float|int|string|null> $identifiers
     *
     * @return list<string>
     */
    public static function normalize(array $identifiers): array
    {
        $ids = [];

        foreach ($identifiers as $identifier) {
            if (!\is_string($identifier) || $identifier === '') {
                continue;
            }

            $ids[] = $identifier;
        }

        return \array_values(\array_unique($ids));
    }
}
