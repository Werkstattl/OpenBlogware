<?php

declare(strict_types=1);

namespace Sas\BlogModule\Controller\StoreApi;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBlogController
{
    abstract public function getDecorated(): AbstractBlogController;

    abstract public function load(Request $request, Criteria $criteria, SalesChannelContext $context): BlogControllerResponse;
}
