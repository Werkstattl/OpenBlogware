<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller\StoreApi;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesCollection;

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
