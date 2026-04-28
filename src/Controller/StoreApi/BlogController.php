<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller\StoreApi;

use OpenApi\Attributes as OAT;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Werkl\OpenBlogware\Content\Blog\BlogEntryCollection;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class BlogController extends AbstractBlogController
{
    /**
     * @param EntityRepository<BlogEntryCollection> $blogRepository
     */
    public function __construct(private readonly EntityRepository $blogRepository)
    {
    }

    public function getDecorated(): AbstractBlogController
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/blog', name: 'store-api.werkl_blog.load', methods: ['GET', 'POST'], defaults: ['_entity' => 'werkl_blog_entry'])]
    #[OAT\Get(
        path: '/store-api/blog',
        summary: 'This route can be used to load the werkl_blog_entry by specific filters',
        operationId: 'listBlog',
        tags: ['Store API', 'Blog'],
        parameters: [
            new OAT\Parameter(name: 'Api-Basic-Parameters'),
        ],
        responses: [
            new OAT\Response(
                response: '200',
                description: '',
                content: new OAT\JsonContent(
                    type: 'object',
                    properties: [
                        new OAT\Property(
                            property: 'total',
                            type: 'integer',
                            description: 'Total amount',
                        ),
                        new OAT\Property(
                            property: 'aggregations',
                            type: 'object',
                            description: 'aggregation result',
                        ),
                        new OAT\Property(
                            property: 'elements',
                            type: 'array',
                            items: new OAT\Items(ref: '#/components/schemas/blog_entities_flat'),
                        ),
                    ]
                ),
            ),
        ],
    )]
    public function load(Request $request, Criteria $criteria, SalesChannelContext $context): BlogControllerResponse
    {
        $criteria = $this->buildCriteria($request, $criteria);

        $blogEntries = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        return new BlogControllerResponse($blogEntries);
    }

    protected function buildCriteria(Request $request, Criteria $criteria): Criteria
    {
        /** @var string|null $search */
        $search = $request->get('search');

        if ($search !== null) {
            if (Uuid::isValid($search)) {
                $criteria->setIds([$search]);
            } else {
                $criteria->addFilter(new EqualsFilter('slug', $search));
            }
        }

        $criteria->addAssociations(['blogAuthor.salutation', 'blogCategories', 'tags']);

        return $criteria;
    }
}
