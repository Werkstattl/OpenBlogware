const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('sas-blog-category-tree-field', 'sw-category-tree-field', {
    computed: {
        globalCategoryRepository() {
            return this.repositoryFactory.create('sas_blog_category');
        },
    },
    methods: {
        searchCategories(term) {
            // create criteria
            const categorySearchCriteria = new Criteria(1, 500);
            categorySearchCriteria.setTerm(term);

            // search for categories
            return this.globalCategoryRepository.search(categorySearchCriteria, Shopware.Context.api);
        },
    },
});
