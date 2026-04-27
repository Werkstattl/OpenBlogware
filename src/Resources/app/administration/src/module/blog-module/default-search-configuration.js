const { searchRankingPoint } = Shopware.Service('searchRankingService');

const defaultSearchConfiguration = {
    _searchable: true,
    title: {
        _searchable: true,
        _score: searchRankingPoint.HIGH_SEARCH_RANKING,
    },
    slug: {
        _searchable: true,
        _score: searchRankingPoint.HIGH_SEARCH_RANKING,
    },
};

export default defaultSearchConfiguration;
