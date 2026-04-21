<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class BlogEntryActiveFilter extends MultiFilter
{
    public function __construct(
        private readonly string $salesChannelId,
        private readonly bool $publishedAtFilter = true
    ) {
        $queries = [
            new EqualsFilter('active', true),
            new OrFilter([
                new ContainsFilter('customFields.salesChannelIds', $salesChannelId),
                new EqualsFilter('customFields.salesChannelIds', null),
            ]),
        ];

        if ($publishedAtFilter) {
            $queries[] = new RangeFilter('publishedAt', [RangeFilter::LTE => (new \DateTime())->format(\DATE_ATOM)]);
        }

        parent::__construct(self::CONNECTION_AND, $queries);
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function isPublishedAtFilterEnabled(): bool
    {
        return $this->publishedAtFilter;
    }
}
