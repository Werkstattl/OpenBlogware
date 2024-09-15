<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Page\Blog;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class BlogPageCriteriaEvent extends Event implements ShopwareSalesChannelEvent
{
    protected string $articleId;

    protected Criteria $criteria;

    protected SalesChannelContext $salesChannelContext;

    public function __construct(string $articleId, Criteria $criteria, SalesChannelContext $context)
    {
        $this->articleId = $articleId;
        $this->criteria = $criteria;
        $this->salesChannelContext = $context;
    }

    public function getArticleId(): string
    {
        return $this->articleId;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
