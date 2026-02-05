import template from './werkl-blog-category-tree.html.twig';

export default {
    template,

    emits: ['change-category-id'],

    data() {
        return {
            blogCategory: null,
            translationContext: 'werkl-blog-category',
            categories: [],
        };
    },

    computed: {
        categoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        category() {
            return this.blogCategory;
        },
    },

    methods: {
        changeCategory(category) {
            this.$emit('change-category-id', category.id);
        },

        syncProducts() {},

        async onGetTreeItems({ parentId }) {
            const criteria = new Shopware.Data.Criteria(1, 500);

            // Filter by parent ID if provided (for nested categories)
            // If no parentId, get root categories
            if (parentId) {
                criteria.addFilter(Shopware.Data.Criteria.equals('parentId', parentId));
            }

            try {
                return await this.categoryRepository.search(criteria, Shopware.Context.api);
            } catch (error) {
                console.error('Failed to load blog categories:', error);
                return null;
            }
        },
    },
};
