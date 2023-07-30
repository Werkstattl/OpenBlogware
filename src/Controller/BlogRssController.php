<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Rss controller
 *
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class BlogRssController extends StorefrontController
{
    private GenericPageLoaderInterface $genericPageLoader;

    private EntityRepository $blogRepository;

    public function __construct(
        GenericPageLoaderInterface $genericPageLoader,
        EntityRepository $blogRepository
    ) {
        $this->genericPageLoader = $genericPageLoader;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @Route("/blog/rss", name="frontend.sas.blog.rss", methods={"GET"})
     */
    public function rss(Request $request, SalesChannelContext $context): Response
    {
        $dateTime = new \DateTime();

        $criteria = new Criteria();
        $criteria->addAssociations(['blogAuthor.salutation']);
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );

        $results = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        $page = $this->genericPageLoader->load($request, $context);
        $page = NavigationPage::createFrom($page);

        $response = $this->renderStorefront('@SasBlogModule/storefront/page/rss.html.twig', [
            'results' => $results,
            'page' => $page,
        ]);
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }
}
