/**
 * @sw-package inventory
 *
 * Initialization that decorates the sw-search-bar component to include blog entries
 * in the global admin search. Works without Elasticsearch.
 */
import BlogSearchMixin from '../mixin/blog-search.mixin';

/**
 * Override sw-search-bar to include blog search results
 */
Shopware.Component.override('sw-search-bar', {
    mixins: [
        ...(Shopware.Mixin.getByName('blogSearchMixin') ? [Shopware.Mixin.getByName('blogSearchMixin')] : []),
        BlogSearchMixin,
    ],

    inject: ['blogSearchService'],

    data() {
        return {
            blogSearchResults: [],
            isBlogSearchLoading: false,
        };
    },

    methods: {
        async onSearch(searchTerm) {
            // Call original method
            await this.$super('onSearch', searchTerm);

            // Extend with blog search results
            if (searchTerm && searchTerm.length >= 2) {
                const originalResults = this.searchResults || [];
                const extendedResults = await this.extendedSearch(searchTerm, originalResults);

                if (this.blogSearchResults && this.blogSearchResults.length > 0) {
                    this.searchResults = extendedResults;
                }
            }
        },
    },
});