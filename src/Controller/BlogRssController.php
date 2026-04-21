<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller;

use Shopware\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntryCollection;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class BlogRssController extends StorefrontController
{
    final public const ALL_TAG = 'werkl-blog-rss';

    /**
     * @param EntityRepository<BlogEntryCollection> $blogRepository
     */
    public function __construct(
        private readonly GenericPageLoaderInterface $genericPageLoader,
        private readonly EntityRepository $blogRepository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function buildName(string $id): string
    {
        return 'werkl-blog-rss-' . $id;
    }

    #[Route(path: '/blog/rss', name: 'frontend.werkl_blog.rss', methods: ['GET'])]
    public function rss(Request $request, SalesChannelContext $context): Response
    {
        $this->dispatcher->dispatch(new AddCacheTagEvent(
            self::buildName($context->getSalesChannelId()),
            self::ALL_TAG
        ));

        $dateTime = new \DateTime();

        $criteria = new Criteria();
        $criteria->addAssociations(['blogAuthor.salutation', 'tags']);
        $criteria->addFilter(new BlogEntryActiveFilter($context->getSalesChannelId()));

        $results = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        $page = $this->genericPageLoader->load($request, $context);
        $page = NavigationPage::createFrom($page);

        $response = $this->renderStorefront('@WerklOpenBlogware/storefront/page/rss.html.twig', [
            'results' => $results,
            'page' => $page,
        ]);
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }
}
