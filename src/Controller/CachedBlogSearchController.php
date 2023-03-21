<?php declare(strict_types=1);

namespace Sas\BlogModule\Controller;

use Shopware\Core\Framework\Adapter\Cache\AbstractCacheTracer;
use Shopware\Core\Framework\Adapter\Cache\CacheValueCompressor;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\FieldSerializer\JsonFieldSerializer;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Handle Cache for BlogSearchController
 *
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class CachedBlogSearchController extends StorefrontController
{
    public const SEARCH_TAG = 'sas-blog-search';

    private BlogSearchController $decorated;

    private CacheInterface $cache;

    private EntityCacheKeyGenerator $generator;

    /**
     * @var AbstractCacheTracer<Response>
     */
    private AbstractCacheTracer $tracer;

    public function __construct(
        BlogSearchController $decorated,
        CacheInterface $cache,
        EntityCacheKeyGenerator $generator,
        AbstractCacheTracer $tracer
    ) {
        $this->decorated = $decorated;
        $this->cache = $cache;
        $this->generator = $generator;
        $this->tracer = $tracer;
    }

    public static function buildName(string $salesChannelId): string
    {
        return 'sas-blog-search-' . $salesChannelId;
    }

    /**
     * @Route("/sas_blog_search", name="sas.frontend.blog.search", methods={"GET"})
     */
    public function search(Request $request, SalesChannelContext $context): Response
    {
        $key = $this->generateSearchKey($request, $context);

        $value = $this->cache->get($key, function (ItemInterface $item) use ($request, $context) {
            $response = $this->decorated->search($request, $context);

            $item->tag($this->generateSearchTags($context));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    /**
     * @Route("/widgets/blog-search", name="widgets.blog.search.pagelet", methods={"GET", "POST"}, defaults={"XmlHttpRequest"=true})
     *
     * @throws MissingRequestParameterException
     */
    public function ajax(Request $request, SalesChannelContext $context): Response
    {
        $key = $this->generateSearchKey($request, $context);

        $value = $this->cache->get($key, function (ItemInterface $item) use ($request, $context) {
            $name = self::buildName($context->getSalesChannelId());
            $response = $this->tracer->trace($name, function () use ($request, $context) {
                return $this->decorated->ajax($request, $context);
            });

            $item->tag($this->generateSearchTags($context));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    private function generateSearchKey(Request $request, SalesChannelContext $context): string
    {
        $parts = array_merge(
            $request->query->all(),
            $request->request->all(),
            [$this->generator->getSalesChannelContextHash($context)],
        );

        return self::buildName($context->getSalesChannelId()) . '-' . md5(JsonFieldSerializer::encodeJson($parts));
    }

    /**
     * @return array<string>
     */
    private function generateSearchTags(SalesChannelContext $context): array
    {
        $tags = array_merge(
            $this->tracer->get(self::buildName($context->getSalesChannelId())),
            [self::buildName($context->getSalesChannelId()), self::SEARCH_TAG],
        );

        return array_unique(array_filter($tags));
    }
}
