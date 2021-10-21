<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Sas\BlogModule\Page\Search\BlogSearchPageLoader;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class BlogController extends StorefrontController
{
    private GenericPageLoaderInterface $genericPageLoader;
    private SalesChannelCmsPageLoaderInterface $cmsPageLoader;
    private SystemConfigService $systemConfigService;
    private EntityRepositoryInterface $blogRepository;
    private BlogSearchPageLoader $blogSearchPageLoader;

    public function __construct(
        SystemConfigService $systemConfigService,
        GenericPageLoaderInterface $genericPageLoader,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        EntityRepositoryInterface $blogRepository,
        BlogSearchPageLoader $blogSearchPageLoader
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->genericPageLoader = $genericPageLoader;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->blogRepository = $blogRepository;
        $this->blogSearchPageLoader = $blogSearchPageLoader;
    }

    /**
     * @HttpCache()
     * @Route("/sas_blog/search", name="sas.frontend.blog.search", methods={"GET"})
     */
    public function search(Request $request, SalesChannelContext $context): Response
    {
        try {
            $page = $this->blogSearchPageLoader->load($request, $context);
        } catch (MissingRequestParameterException $missingRequestParameterException) {
            return $this->forwardToRoute('frontend.home.page');
        }

        return $this->renderStorefront('@Storefront/storefront/page/blog-search/index.html.twig', ['page' => $page]);
    }

    /**
     * @HttpCache()
     * @Route("/widgets/blog-search", name="widgets.blog.search.pagelet", methods={"GET", "POST"}, defaults={"XmlHttpRequest"=true})
     *
     * @throws MissingRequestParameterException
     */
    public function ajax(Request $request, SalesChannelContext $context): Response
    {
        $request->request->set('no-aggregations', true);

        $page = $this->blogSearchPageLoader->load($request, $context);

        $response = $this->renderStorefront('@Storefront/storefront/page/blog-search/search-pagelet.html.twig', ['page' => $page]);
        $response->headers->set('x-robots-tag', 'noindex');

        return $response;
    }

    /**
     * @HttpCache()
     * @Route("/sas_blog/{articleId}", name="sas.frontend.blog.detail", methods={"GET"})
     */
    public function detailAction(string $articleId, Request $request, SalesChannelContext $context): Response
    {
        $page = $this->genericPageLoader->load($request, $context);
        $page = NavigationPage::createFrom($page);

        $criteria = new Criteria([$articleId]);

        $criteria->addAssociations(['author.salutation', 'blogCategories']);

        $results = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        /** @var BlogEntriesEntity $entry */
        $entry = $results->first();

        if (!$entry) {
            throw new PageNotFoundException($articleId);
        }

        $pages = $this->cmsPageLoader->load(
            $request,
            new Criteria([$this->systemConfigService->get('SasBlogModule.config.cmsBlogDetailPage')]),
            $context
        );

        $page->setCmsPage($pages->first());
        $metaInformation = $page->getMetaInformation();

        $metaInformation->setAuthor($entry->getAuthor()->getTranslated()['name']);

        $page->setMetaInformation($metaInformation);

        $page->setNavigationId($page->getHeader()->getNavigation()->getActive()->getId());

        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', [
            'page' => $page,
            'entry' => $entry,
        ]);
    }
}
