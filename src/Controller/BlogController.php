<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Werkl\OpenBlogware\Page\Blog\BlogPageLoader;

/**
 * Blog detail page controller
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class BlogController extends StorefrontController
{
    private BlogPageLoader $blogPageLoader;

    public function __construct(BlogPageLoader $blogPageLoader)
    {
        $this->blogPageLoader = $blogPageLoader;
    }

    #[Route(path: '/werkl_blog/{articleId}', name: 'werkl.frontend.blog.detail', methods: ['GET'])]
    public function detailAction(Request $request, SalesChannelContext $context): Response
    {
        $page = $this->blogPageLoader->load($request, $context);

        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', ['page' => $page]);
    }
}
