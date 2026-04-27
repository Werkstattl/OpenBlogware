const { Criteria } = Shopware.Data;

export default {
    computed: {
        globalCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },
    },

    methods: {
        searchCategories(term) {
            // create criteria
            const categorySearchCriteria = new Criteria(1, 500);
            categorySearchCriteria.setTerm(term);

            // search for categories
            return this.globalCategoryRepository.search(categorySearchCriteria);
        },
    },
};
