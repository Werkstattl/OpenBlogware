<?php declare(strict_types=1);

namespace Sas\BlogModule\Page\Search;

use Shopware\Core\Content\Category\Exception\CategoryNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class BlogSearchPageLoader
{
    private GenericPageLoaderInterface $genericLoader;

    private EventDispatcherInterface $eventDispatcher;

    private EntityRepositoryInterface $blogRepository;

    public function __construct(
        GenericPageLoaderInterface $genericLoader,
        EntityRepositoryInterface $blogRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->genericLoader = $genericLoader;
        $this->blogRepository = $blogRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws CategoryNotFoundException
     * @throws InconsistentCriteriaIdsException
     * @throws MissingRequestParameterException
     */
    public function load(Request $request, SalesChannelContext $context): BlogSearchPage
    {
        if (!$request->query->has('search')) {
            throw new MissingRequestParameterException('search');
        }

        $page = $this->genericLoader->load($request, $context);

        /** @var BlogSearchPage $page */
        $page = BlogSearchPage::createFrom($page);

        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('noindex,follow');
        }

        $criteria = new Criteria();

        $criteria->setTerm($request->query->get('search'));

        $this->handlePagination($request, $criteria);

        $criteria->setTitle('blog-search-page');

        $result = $this->blogRepository->search($criteria, $context->getContext());
        $page->setListing($result);

        $page->setSearchTerm(
            (string) $request->query->get('search')
        );

        return $page;
    }

    private function handlePagination(Request $request, Criteria $criteria): void
    {
        $limit = $this->getLimit($request);

        $page = $this->getPage($request);

        $criteria->setOffset(($page - 1) * $limit);
        $criteria->setLimit($limit);
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
    }

    private function getLimit(Request $request): int
    {
        $limit = $request->query->getInt('limit', 0);

        if ($request->isMethod(Request::METHOD_POST)) {
            $limit = $request->request->getInt('limit', $limit);
        }

        $limit = $limit > 0 ? $limit : 24;

        return $limit <= 0 ? 24 : $limit;
    }

    private function getPage(Request $request): int
    {
        $page = $request->query->getInt('p', 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('p', $page);
        }

        return $page <= 0 ? 1 : $page;
    }
}
