<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller;

use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Werkl\OpenBlogware\Page\Search\BlogSearchPageLoader;

/**
 * Blog search controllers
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class BlogSearchController extends StorefrontController
{
    private BlogSearchPageLoader $blogSearchPageLoader;

    public function __construct(
        BlogSearchPageLoader $blogSearchPageLoader
    ) {
        $this->blogSearchPageLoader = $blogSearchPageLoader;
    }

    #[Route(path: '/werkl_blog_search', name: 'werkl.frontend.blog.search', methods: ['GET'])]
    public function search(Request $request, SalesChannelContext $context): Response
    {
        try {
            $page = $this->blogSearchPageLoader->load($request, $context);
        } catch (RoutingException $routingException) {
            return $this->forwardToRoute('frontend.home.page');
        }

        return $this->renderStorefront('@Storefront/storefront/page/blog-search/index.html.twig', ['page' => $page]);
    }

    /**
     * @throws RoutingException
     */
    #[Route(path: '/widgets/blog-search', name: 'widgets.blog.search.pagelet', methods: ['GET', 'POST'], defaults: ['XmlHttpRequest' => true])]
    public function ajax(Request $request, SalesChannelContext $context): Response
    {
        $request->request->set('no-aggregations', true);

        $page = $this->blogSearchPageLoader->load($request, $context);

        $response = $this->renderStorefront('@Storefront/storefront/page/blog-search/search-pagelet.html.twig', ['page' => $page]);
        $response->headers->set('x-robots-tag', 'noindex');

        return $response;
    }
}
