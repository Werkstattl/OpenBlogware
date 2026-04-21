<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Core\Content\Sitemap\Provider;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Sitemap\Provider\AbstractUrlProvider;
use Shopware\Core\Content\Sitemap\Struct\Url;
use Shopware\Core\Content\Sitemap\Struct\UrlResult;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntryCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntryEntity;
use Werkl\OpenBlogware\Content\Blog\Events\BlogIndexerEvent;
use Werkl\OpenBlogware\Content\Blog\SalesChannel\BlogEntryActiveFilter;

class BlogUrlProvider extends AbstractUrlProvider
{
    public const CHANGE_FREQ = 'daily';
    public const PRIORITY = 1.0;

    /**
     * @param EntityRepository<BlogEntryCollection> $blogRepository
     */
    public function __construct(
        private readonly EntityRepository $blogRepository,
        private readonly Connection $connection,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getDecorated(): AbstractUrlProvider
    {
        throw new DecorationPatternException(self::class);
    }

    public function getName(): string
    {
        return 'werklBlog';
    }

    public function getUrls(SalesChannelContext $context, int $limit, ?int $offset = null): UrlResult
    {
        $criteria = new Criteria();

        $criteria->setLimit($limit);
        $criteria->setOffset($offset);

        $criteria->addFilter(new BlogEntryActiveFilter($context->getSalesChannelId()));

        /** @var BlogEntryCollection $blogEntries */
        $blogEntries = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        if ($blogEntries->count() === 0) {
            return new UrlResult([], null);
        }
        $this->eventDispatcher->dispatch(new BlogIndexerEvent($blogEntries->getIds(), $context->getContext()));
        $seoUrls = $this->getSeoUrls($blogEntries->getIds(), 'werkl.frontend.blog.detail', $context, $this->connection);

        $seoUrls = FetchModeHelper::groupUnique($seoUrls);
        $urls = [];

        foreach ($blogEntries as $blogEntry) {
            if (!\array_key_exists($blogEntry->getId(), $seoUrls)) {
                continue;
            }

            $seoUrl = $seoUrls[$blogEntry->getId()];
            if (!\array_key_exists('seo_path_info', $seoUrl)) {
                continue;
            }

            if (!\is_string($seoUrl['seo_path_info'])) {
                continue;
            }

            $blogUrl = new Url();
            $blogUrl->setLastmod($blogEntry->getUpdatedAt() ?? new \DateTime());
            $blogUrl->setChangefreq(self::CHANGE_FREQ);
            $blogUrl->setPriority(self::PRIORITY);
            $blogUrl->setResource(BlogEntryEntity::class);
            $blogUrl->setIdentifier($blogEntry->getId());
            $blogUrl->setLoc($seoUrl['seo_path_info']);

            $urls[] = $blogUrl;
        }

        return new UrlResult($urls, null);
    }
}
