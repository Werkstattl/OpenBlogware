<?php declare(strict_types=1);

namespace Sas\BlogModule\Content\SalesChannel\Suggest;

use Shopware\Core\Content\Product\SalesChannel\Suggest\AbstractProductSuggestRoute;
use Shopware\Core\Content\Product\SalesChannel\Suggest\ProductSuggestRouteResponse;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class ProductSuggestDecorated extends AbstractProductSuggestRoute
{
    private AbstractProductSuggestRoute $decorated;

    private EntityRepository $blogRepository;

    private SystemConfigService $systemConfigService;

    public function __construct(
        AbstractProductSuggestRoute $decorated,
        EntityRepository $blogRepository,
        SystemConfigService $systemConfigService
    ) {
        $this->decorated = $decorated;
        $this->blogRepository = $blogRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public function getDecorated(): AbstractProductSuggestRoute
    {
        return $this->decorated;
    }

    /**
     * Loads the product suggestions for the given search term.
     * It calls the decorated method to get the suggestions.
     * It checks for the enableSearchBox config and if it is disabled, it returns above suggestions.
     * It gets the blog posts and adds them to the suggestions.
     */
    public function load(
        Request $request,
        SalesChannelContext $context,
        Criteria $criteria
    ): ProductSuggestRouteResponse {
        $response = $this->getDecorated()->load($request, $context, $criteria);

        if (!$this->systemConfigService->get('SasBlogModule.config.enableSearchBox')) {
            return $response;
        }

        $limit = $response->getListingResult()->getCriteria()->getLimit() ?? 1;
        $blogResult = $this->getBlogs($request->get('search'), (int) $limit, $context->getContext());
        $response->getListingResult()->addExtension('blogResult', $blogResult);

        return $response;
    }

    /**
     * Get blogs from the blog repository with the given search term and limit.
     * It creates a new criteria and adds the search term and limit to it.
     * It sets filters for the following fields:
     * - published: true
     * - publishedAt: before now
     * It then executes the criteria and returns the result.
     */
    private function getBlogs(string $term, int $limit, Context $context): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->setTerm($term);
        $criteria->setLimit($limit);
        $criteria->addAssociation('media');
        $criteria->addAssociation('blogCategories');
        $criteria->getAssociation('blogCategories')->addSorting(new FieldSorting('level', FieldSorting::ASCENDING));

        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => (new \DateTime())->format(\DATE_ATOM)])
        );

        return $this->blogRepository->search($criteria, $context);
    }
}
