<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\Blog\Events;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Event\NestedEvent;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class NewestListingCriteriaEvent extends NestedEvent implements ShopwareSalesChannelEvent
{
    protected Request $request;

    protected Criteria $criteria;

    protected SalesChannelContext $context;

    public function __construct(Request $request, Criteria $criteria, SalesChannelContext $context)
    {
        $this->request = $request;
        $this->criteria = $criteria;
        $this->context = $context;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }
}
