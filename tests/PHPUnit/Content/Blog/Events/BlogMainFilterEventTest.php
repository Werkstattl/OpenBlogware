<?php declare(strict_types=1);

namespace BlogModule\Tests\Content\Blog\Events;

use PHPUnit\Framework\TestCase;
use Sas\BlogModule\Content\Blog\Events\BlogMainFilterEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class BlogMainFilterEventTest extends TestCase
{
    /**
     * This function tests that its possible to assign criteria correctly to our event.
     * This can be used by 3rd party developers, and the core system of
     * the plugin will then use that criteria to load more information
     * for blog cms element resolver.
     */
    public function testCriteria(): void
    {
        $request = $this->createMock(Request::class);
        $fakeSalesChannelContext = $this->getMockBuilder(SalesChannelContext::class)->disableOriginalConstructor()->getMock();

        $criteria = new Criteria();
        $event = new BlogMainFilterEvent(
            $request,
            $criteria,
            $fakeSalesChannelContext
        );

        static::assertEquals($criteria, $event->getCriteria());
        static::assertEquals($fakeSalesChannelContext, $event->getSalesChannelContext());
        static::assertEquals($fakeSalesChannelContext->getContext(), $event->getContext());
        static::assertInstanceOf(Criteria::class, $event->getCriteria());
    }
}
