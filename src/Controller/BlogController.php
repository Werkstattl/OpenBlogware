<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BlogController
 * @package Sas\BlogModule\Controller
 */
class BlogController extends StorefrontController
{

    /**
     * @var GenericPageLoader
     */
    private $genericPageLoader;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * BlogController constructor.
     * @param GenericPageLoader $genericPageLoader
     * @param AdapterInterface $cache
     */
    public function __construct(GenericPageLoader $genericPageLoader, AdapterInterface $cache)
    {
        $this->genericPageLoader = $genericPageLoader;
        $this->cache = $cache;
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/blog", name="sns.frontend.blog", methods={"GET"})
     */
    public function indexAction(Request $request, SalesChannelContext $salesChannelContext, Context $criteriaContext): Response
    {
        $page = $this->genericPageLoader->load($request, $salesChannelContext);

        /** @var EntityRepositoryInterface $blogRepository */
        $blogRepository = $this->container->get('sas_blog_entries.repository');

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('active', true)
        );

        $results = $blogRepository->search($criteria, $criteriaContext);

        $entries = (array) $results->getEntities()->getElements();

        return $this->renderStorefront('@Storefront/page/blog/index.html.twig', [
            'page' => $page,
            'entries' => $entries
        ]);
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/blog/{slug}", name="sns.frontend.blog.detail", methods={"GET"})
     * @throws PageNotFoundException
     */
    public function detailAction(Request $request, SalesChannelContext $salesChannelContext, Context $criteriaContext, $slug): Response
    {
        $page = $this->genericPageLoader->load($request, $salesChannelContext);

        /** @var EntityRepositoryInterface $blogRepository */
        $blogRepository = $this->container->get('sas_blog_entries.repository');

        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('slug', $slug)
        );

        $results = $blogRepository->search($criteria, $criteriaContext)->getEntities();
        $entry = $results->first();

        $item = $this->cache->getItem('sns_blog_detail_'.md5($entry->getId()));
        if (!$item->isHit())
        {
            $item->set($entry);
            $this->cache->save($item);
        }
        $entry = $item->get();

        return $this->renderStorefront('@Storefront/page/blog/detail.html.twig', [
            'page' => $page,
            'entry' => $entry
        ]);
    }

}
