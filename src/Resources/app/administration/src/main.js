import './init/cms-page-type.init';
import './module/blog-module';

/**
 * Register blog search service and types
 * This adds blog entries to the global admin search (without Elasticsearch)
 */
import BlogSearchApiService from './service/blog-search-api.service';

// Register the Blog Search API Service
Shopware.Service().register('blogSearchService', (serviceContainer) => {
    const httpClient = serviceContainer.httpClient;
    const loginService = serviceContainer.loginService;

    return new BlogSearchApiService(httpClient, loginService);
});

// Register search types for blog entries and categories
const searchTypeService = Shopware.Service('searchTypeService');
if (searchTypeService) {
    searchTypeService.registerType('werkl_blog_entry', {
        entityName: 'werkl_blog_entry',
        placeholderSnippet: 'open-blogware.search.placeholder',
        labelSnippet: 'open-blogware.search.label',
        entityLabel: 'Blog Entry',
        iconName: 'regular-file-text',
    });

    searchTypeService.registerType('werkl_blog_category', {
        entityName: 'werkl_blog_category',
        placeholderSnippet: 'open-blogware.search.categoryPlaceholder',
        labelSnippet: 'open-blogware.search.categoryLabel',
        entityLabel: 'Blog Category',
        iconName: 'regular-folder',
    });
}
