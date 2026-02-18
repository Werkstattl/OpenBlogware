/**
 * @sw-package inventory
 *
 * Mixin that extends the search bar to include blog entries in global admin search.
 * Works without Elasticsearch by using the custom Blog Search API.
 */
import { debounce } from 'lodash';

const BlogSearchMixin = {
    inject: ['blogSearchService'],

    data() {
        return {
            blogSearchResults: [],
            isBlogSearchLoading: false,
        };
    },

    methods: {
        /**
         * Extend the search to include blog entries
         */
        async extendedSearch(term, originalResults) {
            if (!term || term.length < 2) {
                this.blogSearchResults = [];
                return originalResults;
            }

            // Check if we should include blog entries in global search
            const types = this.searchTypeService?.getTypes() ?? {};
            
            // Always try to add blog results for global search
            if (!this.currentSearchType || this.currentSearchType === 'all') {
                try {
                    this.isBlogSearchLoading = true;
                    
                    const blogResponse = await this.blogSearchService.search(term, 5);
                    
                    if (blogResponse?.data) {
                        const blogResults = [];
                        
                        // Add blog entries
                        if (blogResponse.data.blog_entries && Object.keys(blogResponse.data.blog_entries).length > 0) {
                            blogResults.push({
                                entity: 'werkl_blog_entry',
                                entityLabel: this.$tc('open-blogware.search.label', 2) || 'Blog Entries',
                                total: blogResponse.total?.blog_entries ?? Object.keys(blogResponse.data.blog_entries).length,
                                entities: Object.values(blogResponse.data.blog_entries),
                            });
                        }
                        
                        // Add blog categories
                        if (blogResponse.data.blog_categories && Object.keys(blogResponse.data.blog_categories).length > 0) {
                            blogResults.push({
                                entity: 'werkl_blog_category',
                                entityLabel: this.$tc('open-blogware.search.categoryLabel', 2) || 'Blog Categories',
                                total: blogResponse.total?.blog_categories ?? Object.keys(blogResponse.data.blog_categories).length,
                                entities: Object.values(blogResponse.data.blog_categories),
                            });
                        }
                        
                        this.blogSearchResults = blogResults;
                    } else {
                        this.blogSearchResults = [];
                    }
                } catch (error) {
                    console.warn('Blog search failed:', error);
                    this.blogSearchResults = [];
                } finally {
                    this.isBlogSearchLoading = false;
                }
            }

            // Merge blog results with original results
            if (this.blogSearchResults.length > 0) {
                return [...originalResults, ...this.blogSearchResults];
            }

            return originalResults;
        },

        /**
         * Hook to extend search results
         */
        onSearchComplete(results) {
            if (!this.blogSearchResults.length) {
                return results;
            }
            
            // The blog search is already integrated in extendedSearch
            return results;
        },
    },
};

// Register the mixin globally
Shopware.Mixin.register('blogSearchMixin', BlogSearchMixin);

// Export for use in components
export default BlogSearchMixin;