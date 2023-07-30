<?php declare(strict_types=1);

namespace Sas\BlogModule\Page\Blog;

use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\Exception\ConfigurationNotFoundException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\MetaInformation;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class BlogPageLoader
{
    private SystemConfigService $systemConfigService;

    private GenericPageLoaderInterface $genericLoader;

    private EventDispatcherInterface $eventDispatcher;

    private SalesChannelCmsPageLoaderInterface $cmsPageLoader;

    private EntityRepository $blogRepository;

    public function __construct(
        SystemConfigService $systemConfigService,
        GenericPageLoaderInterface $genericLoader,
        EventDispatcherInterface $eventDispatcher,
        SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        EntityRepository $blogRepository
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->blogRepository = $blogRepository;
    }

    /**
     * Loads the blog page data
     * It gets article id from request
     * It get Storefront Page's instance for given request
     * It assigns metadata to page instance
     * It dispatches an event to allow other extensions to modify the page instance
     *
     * @throws PageNotFoundException
     * @throws InconsistentCriteriaIdsException
     * @throws MissingRequestParameterException
     * @throws ConfigurationNotFoundException
     */
    public function load(Request $request, SalesChannelContext $context): BlogPage
    {
        $articleId = $request->attributes->get('articleId');
        if (!$articleId) {
            throw RoutingException::missingRequestParameter('articleId', '/articleId');
        }

        $blogEntry = $this->loadBlogEntry($articleId, $context);
        $detailCmsPage = $this->loadBlogDetailCmsPage($request, $context);

        $page = $this->genericLoader->load($request, $context);
        $page = BlogPage::createFrom($page);

        $page->setBlogEntry($blogEntry);
        $page->setCmsPage($detailCmsPage);

        if (
            $page->getHeader()
            && $page->getHeader()->getNavigation()
            && $page->getHeader()->getNavigation()->getActive()
        ) {
            $navigationId = $page->getHeader()->getNavigation()->getActive()->getId();
            $page->setNavigationId($navigationId);
        }

        $metaInformation = $page->getMetaInformation();
        if ($metaInformation instanceof MetaInformation) {
            $metaTitle = $blogEntry->getTranslation('metaTitle') ?? $blogEntry->getTitle();
            $metaDescription = $blogEntry->getTranslation('metaDescription') ?? $blogEntry->getTeaser();
            $metaAuthor = $blogEntry->getBlogAuthor() ? $blogEntry->getBlogAuthor()->getFullName() : '';
            $metaInformation->setMetaTitle($metaTitle ?? '');
            $metaInformation->setMetaDescription($metaDescription ?? '');
            $metaInformation->setAuthor($metaAuthor ?? '');
            $page->setMetaInformation($metaInformation);
        }

        $this->eventDispatcher->dispatch(new BlogPageLoadedEvent($page, $context, $request));

        return $page;
    }

    /**
     * Loads the Blog Entry for the given article id
     * It creates a criteria with the given article id
     *   then associates the author's salutation and blog categories
     * It dispatches an event to allow other extensions to modify the criteria
     * It gets and returns the Blog Entry's instance for the given criteria
     *
     * @throws PageNotFoundException
     */
    private function loadBlogEntry(string $articleId, SalesChannelContext $context): BlogEntriesEntity
    {
        $criteria = (new Criteria([$articleId]))
            ->addAssociation('author.salutation')
            ->addAssociation('blogCategories');

        $this->eventDispatcher->dispatch(new BlogPageCriteriaEvent($articleId, $criteria, $context));

        $blogEntry = $this->blogRepository
            ->search($criteria, $context->getContext())
            ->first();

        if (!$blogEntry instanceof BlogEntriesEntity) {
            throw new PageNotFoundException($articleId);
        }

        return $blogEntry;
    }

    /**
     * Loads the CMS Page for the blog detail page
     * It gets the CMS Page's id from the plugin configuration
     * It gets and returns the CMS Page's instance for the given id
     *
     * @throws PageNotFoundException
     * @throws ConfigurationNotFoundException
     */
    private function loadBlogDetailCmsPage(Request $request, SalesChannelContext $context): CmsPageEntity
    {
        $detailCmsPageId = $this->systemConfigService->getString('SasBlogModule.config.cmsBlogDetailPage');
        if (!$detailCmsPageId) {
            throw new ConfigurationNotFoundException('SasBlogModule');
        }

        $detailCmsPage = $this->cmsPageLoader->load($request, new Criteria([$detailCmsPageId]), $context)->first();
        if (!$detailCmsPage instanceof CmsPageEntity) {
            throw new PageNotFoundException($detailCmsPageId);
        }

        return $detailCmsPage;
    }
}
