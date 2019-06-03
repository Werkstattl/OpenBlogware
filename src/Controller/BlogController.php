<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{
    /**
     * @Route("/sales-channel-api/v1/sas/get-blog-entries", name="sales-channel-api.action.sas.get-blog-entries", methods={"GET"})
     */
    public function getAllBlogEntries(Request $request, Context $context): JsonResponse
    {
        /** @var EntityRepositoryInterface $blogRepository */
        $blogRepository = $this->container->get('sas_blog_entries.repository');

        $criteria = new Criteria();

        $results = $blogRepository->search($criteria, $context);

        dd($results);
    }
}