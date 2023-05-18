<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Sas\BlogModule\Content\Blog\BlogEntriesCollection;
use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Sas\BlogModule\Content\BlogAuthor\BlogAuthorEntity;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Blog detail page controller
 *
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class BlogController extends StorefrontController
{
    private SystemConfigService $systemConfigService;

    private GenericPageLoaderInterface $genericPageLoader;

    private SalesChannelCmsPageLoaderInterface $cmsPageLoader;

    private EntityRepositoryInterface $blogRepository;

    public function __construct(
        SystemConfigService $systemConfigService,
        GenericPageLoaderInterface $genericPageLoader,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        EntityRepositoryInterface $blogRepository
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->genericPageLoader = $genericPageLoader;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @Route("/sas_blog/{articleId}", name="sas.frontend.blog.detail", methods={"GET"})
     */
    public function detailAction(string $articleId, Request $request, SalesChannelContext $context): Response
    {
        $page = $this->genericPageLoader->load($request, $context);
        $page = NavigationPage::createFrom($page);

        $criteria = new Criteria([$articleId]);

        $criteria->addAssociations(['blogAuthor.salutation', 'blogCategories']);

        /** @var BlogEntriesCollection $results */
        $results = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        $cmsBlogDetailPageId = $this->systemConfigService->get('SasBlogModule.config.cmsBlogDetailPage');
        if (!\is_string($cmsBlogDetailPageId)) {
            throw new PageNotFoundException($articleId);
        }

        if (!$results->first()) {
            throw new PageNotFoundException($articleId);
        }

        $entry = $results->first();
        if (!$entry instanceof BlogEntriesEntity) {
            throw new PageNotFoundException($articleId);
        }

        $pages = $this->cmsPageLoader->load(
            $request,
            new Criteria([$cmsBlogDetailPageId]),
            $context
        );

        $page->setCmsPage($pages->first());

        $blogAuthor = $entry->getBlogAuthor();
        if (!$blogAuthor instanceof BlogAuthorEntity) {
            throw new PageNotFoundException($articleId);
        }

        $metaInformation = $page->getMetaInformation();
        if ($metaInformation instanceof MetaInformation) {
            $metaTitle = $entry->getTranslated()['metaTitle'] ?? $entry->getTitle();
            $metaDescription = $entry->getTranslated()['metaDescription'] ?? $entry->getTeaser();
            $metaAuthor = $blogAuthor->getTranslated()['name'];
            $metaInformation->setMetaTitle($metaTitle ?? '');
            $metaInformation->setMetaDescription($metaDescription ?? '');
            $metaInformation->setAuthor($metaAuthor ?? '');
            $page->setMetaInformation($metaInformation);
        }

        $page->setNavigationId($page->getHeader()->getNavigation()->getActive()->getId());

        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', [
            'page' => $page,
            'entry' => $entry,
        ]);
    }
}
