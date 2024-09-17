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
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesCollection;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesEntity;
use Werkl\OpenBlogware\Content\Blog\Events\BlogIndexerEvent;

class BlogUrlProvider extends AbstractUrlProvider
{
    public const CHANGE_FREQ = 'daily';
    public const PRIORITY = 1.0;

    private EntityRepository $blogRepository;

    private Connection $connection;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityRepository $blogRepository,
        Connection $connection,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->blogRepository = $blogRepository;
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
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

        $dateTime = new \DateTime();

        $criteria->setLimit($limit);
        $criteria->setOffset($offset);

        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );

        /** @var BlogEntriesCollection $blogEntities */
        $blogEntities = $this->blogRepository->search($criteria, $context->getContext())->getEntities();

        if ($blogEntities->count() === 0) {
            return new UrlResult([], null);
        }
        $this->eventDispatcher->dispatch(new BlogIndexerEvent($blogEntities->getIds(), $context->getContext()));
        $seoUrls = $this->getSeoUrls($blogEntities->getIds(), 'werkl.frontend.blog.detail', $context, $this->connection);

        $seoUrls = FetchModeHelper::groupUnique($seoUrls);
        $urls = [];

        /*  @var BlogEntriesEntity  $blogEntity */
        foreach ($blogEntities as $blogEntity) {
            if (!\array_key_exists($blogEntity->getId(), $seoUrls)) {
                continue;
            }

            $seoUrl = $seoUrls[$blogEntity->getId()];
            if (!\array_key_exists('seo_path_info', $seoUrl)) {
                continue;
            }

            if (!\is_string($seoUrl['seo_path_info'])) {
                continue;
            }

            $blogUrl = new Url();
            $blogUrl->setLastmod($blogEntity->getUpdatedAt() ?? new \DateTime());
            $blogUrl->setChangefreq(self::CHANGE_FREQ);
            $blogUrl->setPriority(self::PRIORITY);
            $blogUrl->setResource(BlogEntriesEntity::class);
            $blogUrl->setIdentifier($blogEntity->getId());
            $blogUrl->setLoc($seoUrl['seo_path_info']);

            $urls[] = $blogUrl;
        }

        return new UrlResult($urls, null);
    }
}
