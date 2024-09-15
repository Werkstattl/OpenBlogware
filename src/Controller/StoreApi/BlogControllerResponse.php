<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller\StoreApi;

use Werkl\OpenBlogware\Content\Blog\BlogEntriesCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class BlogControllerResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getBlogEntries(): BlogEntriesCollection
    {
        /** @var BlogEntriesCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
