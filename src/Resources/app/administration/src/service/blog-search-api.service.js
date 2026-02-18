/**
 * @sw-package inventory
 */

import ApiService from '../../service/api.service';

/**
 * Blog Search API Service for searching blog entries in the admin
 * This service provides an interface to search blog entries and categories
 * without requiring Elasticsearch
 */
class BlogSearchApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '_admin') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'blogSearchService';
    }

    /**
     * Search for blog entries and categories
     *
     * @param {string} term - The search term
     * @param {number} limit - Maximum number of results (default: 10)
     * @param {object} additionalHeaders - Optional additional headers
     * @returns {Promise<object>} Search results containing blog_entries and blog_categories
     */
    search(term, limit = 10, additionalHeaders = {}) {
        const headers = this.getBasicHeaders(additionalHeaders);

        return this.httpClient
            .post(
                `${this.getApiBasePath()}/blog-search`,
                { term, limit },
                { headers }
            )
            .then((response) => {
                return ApiService.handleResponse(response);
            })
            .catch((error) => {
                console.error('Blog search error:', error);
                return { data: [], total: 0 };
            });
    }

    /**
     * Search only blog entries
     *
     * @param {string} term - The search term
     * @param {number} limit - Maximum number of results
     * @returns {Promise<object>} Blog entries search result
     */
    searchEntries(term, limit = 10) {
        return this.search(term, limit).then(result => ({
            entries: result.data?.blog_entries || [],
            total: result.total?.blog_entries || 0
        }));
    }

    /**
     * Search only blog categories
     *
     * @param {string} term - The search term
     * @param {number} limit - Maximum number of results
     * @returns {Promise<object>} Blog categories search result
     */
    searchCategories(term, limit = 10) {
        return this.search(term, limit).then(result => ({
            categories: result.data?.blog_categories || [],
            total: result.total?.blog_categories || 0
        }));
    }
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default BlogSearchApiService;