<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller\StoreApi;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\Annotation\Entity;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RouteScope(scopes={"store-api"})
 */
class BlogController extends AbstractBlogController
{
    public function __construct(protected EntityRepositoryInterface $blogRepository)
    {
    }

    public function getDecorated(): AbstractBlogController
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @Entity("sas_blog_entries")
     * @OA\Get(
     *      path="/store-api/blog",
     *      summary="This route can be used to load the sas_blog_entries by specific filters",
     *      operationId="listBlog",
     *      tags={"Store API", "Blog"},
     *      @OA\Parameter(name="Api-Basic-Parameters"),
     *      @OA\Response(
     *          response="200",
     *          description="",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(
     *                  property="total",
     *                  type="integer",
     *                  description="Total amount"
     *              ),
     *              @OA\Property(
     *                  property="aggregations",
     *                  type="object",
     *                  description="aggregation result"
     *              ),
     *              @OA\Property(
     *                  property="elements",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/blog_entities_flat")
     *              )
     *          )
     *     )
     * )
     * @Route("/store-api/blog", name="store-api.sas.blog.load", methods={"GET","POST"})
     */
    public function load(Request $request, Criteria $criteria, SalesChannelContext $context): BlogControllerResponse
    {
        $criteria = $this->buildCriteria($request, $criteria);

        return new BlogControllerResponse($this->blogRepository->search($criteria, $context->getContext()));
    }

    protected function buildCriteria(Request $request, Criteria $criteria): Criteria
    {
        $search = $request->get('search');
        if ($search) {
            if (Uuid::isValid($search)) {
                $criteria->setIds([$search]);
            } else {
                $criteria->addFilter(new EqualsFilter('slug', $search));
            }
        }

        $criteria->addAssociations(['author.salutation', 'blogCategories']);

        return $criteria;
    }
}
