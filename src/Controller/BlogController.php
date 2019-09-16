<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends StorefrontController
{

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/blog", name="sns.frontend.blog", methods={"GET"})
     */
    public function indexAction(Context $context): Response
    {
        /** @var EntityRepositoryInterface $blogRepository */
        $blogRepository = $this->container->get('sas_blog_entries.repository');

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('active', true)
        );

        $results = $blogRepository->search($criteria, $context);

        $entries = (array) $results->getEntities()->getElements();

        return $this->renderStorefront('@Storefront/page/blog.html.twig', ['entries' => $entries]);
    }

    /**
     * @Route("/sales-channel-api/v1/sas/get-all-blog-entries", name="sales-channel-api.action.sas.get-blog-entries", methods={"GET"})
     * @param Context $context
     * @return JsonResponse
     * @throws InconsistentCriteriaIdsException
     */
    public function getAllBlogEntries(Context $context): JsonResponse
    {
        /** @var EntityRepositoryInterface $blogRepository */
        $blogRepository = $this->container->get('sas_blog_entries.repository');

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('active', true)
        );

        $results = $blogRepository->search($criteria, $context);

        return new JsonResponse($results);
    }

    /**
     * @Route("/sales-channel-api/v1/sas/get-blog-entry/{slug}", name="sales-channel-api.action.sas.get-blog-entry", methods={"GET"})
     * @param Context $context
     * @param $slug
     * @return JsonResponse
     * @throws InconsistentCriteriaIdsException
     */
    public function getBlogEntry(Context $context, $slug): JsonResponse
    {
        /** @var EntityRepositoryInterface $blogRepository */
        $blogRepository = $this->container->get('sas_blog_entries.repository');

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('slug', $slug)
        );

        $results = $blogRepository->search($criteria, $context);

        return new JsonResponse($results);
    }
}
