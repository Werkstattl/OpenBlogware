<?php declare(strict_types=1);

namespace BlogModule\Tests\Page\Blog;

use PHPUnit\Framework\TestCase;
use Sas\BlogModule\Page\Blog\BlogPageCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class BlogPageCriteriaEventTest extends TestCase
{
    /**
     * This function tests that its possible to assign criteria correctly to our event.
     * This can be used by 3rd party developers, and the core system of
     * the plugin will then use that criteria to load more information
     * for blog detail page.
     */
    public function testCriteria(): void
    {
        $articleId = 'art-123';
        $fakeSalesChannelContext = $this->getMockBuilder(SalesChannelContext::class)->disableOriginalConstructor()->getMock();

        $criteria = new Criteria();
        $event = new BlogPageCriteriaEvent(
            $articleId,
            $criteria,
            $fakeSalesChannelContext
        );

        static::assertEquals($criteria, $event->getCriteria());
        static::assertEquals($fakeSalesChannelContext, $event->getSalesChannelContext());
        static::assertEquals($fakeSalesChannelContext->getContext(), $event->getContext());
        static::assertInstanceOf(Criteria::class, $event->getCriteria());
        static::assertSame($articleId, $event->getArticleId());
    }
}
