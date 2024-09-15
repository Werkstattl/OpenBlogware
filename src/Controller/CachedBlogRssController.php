<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller;

use Shopware\Core\Framework\Adapter\Cache\AbstractCacheTracer;
use Shopware\Core\Framework\Adapter\Cache\CacheValueCompressor;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\Util\Json;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Handle Cache for BlogRssController
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class CachedBlogRssController extends StorefrontController
{
    public const RSS_TAG = 'werkl-blog-rss';

    private BlogRssController $decorated;

    private CacheInterface $cache;

    private EntityCacheKeyGenerator $generator;

    /**
     * @var AbstractCacheTracer<Response>
     */
    private AbstractCacheTracer $tracer;

    public function __construct(
        BlogRssController $decorated,
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
        return 'werkl-blog-rss-' . $salesChannelId;
    }

    #[Route(path: '/blog/rss', name: 'frontend.werkl_blog.rss', methods: ['GET'])]
    public function rss(Request $request, SalesChannelContext $context): Response
    {
        $key = $this->generateKey($request, $context);

        $value = $this->cache->get($key, function (ItemInterface $item) use ($request, $context) {
            $response = $this->decorated->rss($request, $context);

            $item->tag($this->generateTags($context));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    private function generateKey(Request $request, SalesChannelContext $context): string
    {
        $parts = array_merge(
            $request->query->all(),
            $request->request->all(),
            [$this->generator->getSalesChannelContextHash($context)],
        );

        return self::buildName($context->getSalesChannelId()) . '-' . md5(Json::encode($parts));
    }

    /**
     * @return array<string>
     */
    private function generateTags(SalesChannelContext $context): array
    {
        $tags = array_merge(
            $this->tracer->get(self::buildName($context->getSalesChannelId())),
            [self::buildName($context->getSalesChannelId()), self::RSS_TAG]
        );

        return array_unique(array_filter($tags));
    }
}
