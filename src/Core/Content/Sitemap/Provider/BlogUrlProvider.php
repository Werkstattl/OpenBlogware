<?php declare(strict_types=1);

namespace Sas\BlogModule\Core\Content\Sitemap\Provider;

use Doctrine\DBAL\Connection;
use Sas\BlogModule\Content\Blog\BlogEntriesEntity;
use Shopware\Core\Content\Sitemap\Provider\AbstractUrlProvider;
use Shopware\Core\Content\Sitemap\Struct\Url;
use Shopware\Core\Content\Sitemap\Struct\UrlResult;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class BlogUrlProvider extends AbstractUrlProvider
{
    public const CHANGE_FREQ = 'daily';
    public const PRIORITY = 1.0;

    private EntityRepositoryInterface $blogRepository;

    private Connection $connection;

    public function __construct(
        EntityRepositoryInterface $blogRepository,
        Connection $connection
    ) {
        $this->blogRepository = $blogRepository;
        $this->connection = $connection;
    }

    public function getDecorated(): AbstractUrlProvider
    {
        throw new DecorationPatternException(self::class);
    }

    public function getName(): string
    {
        return 'sasBlog';
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

        $blogEntities = $this->blogRepository->search($criteria, $context->getContext());

        if ($blogEntities->count() === 0) {
            return new UrlResult([], null);
        }

        $seoUrls = $this->getSeoUrls($blogEntities->getIds(), 'sas.frontend.blog.detail', $context, $this->connection);
        $seoUrls = FetchModeHelper::groupUnique($seoUrls);

        $urls = [];

        foreach ($blogEntities as $blogEntity) {
            $blogUrl = new Url();
            $blogUrl->setLastmod($blogEntity->getUpdatedAt() ?? new \DateTime());
            $blogUrl->setChangefreq(self::CHANGE_FREQ);
            $blogUrl->setPriority(self::PRIORITY);
            $blogUrl->setResource(BlogEntriesEntity::class);
            $blogUrl->setIdentifier($blogEntity->getId());

            if (isset($seoUrls[$blogEntity->getId()])) {
                $blogUrl->setLoc($seoUrls[$blogEntity->getId()]['seo_path_info']);
            }

            $urls[] = $blogUrl;
        }

        return new UrlResult($urls, null);
    }
}
