<?php declare(strict_types=1);

namespace Sas\BlogModule\Page\Search;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Storefront\Page\Page;

class BlogSearchPage extends Page
{
    protected string $searchTerm;

    protected EntitySearchResult $listing;

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }

    public function getListing(): EntitySearchResult
    {
        return $this->listing;
    }

    public function setListing(EntitySearchResult $listing): void
    {
        $this->listing = $listing;
    }
}
