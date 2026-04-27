import template from './werkl-cms-el-config-blog-newest-listing.html.twig';

const { Context, Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            blogCategoryCollection: null,
        };
    },

    computed: {
        blogCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        showTypeOptions() {
            return [
                {
                    id: 1,
                    value: 'all',
                    label: this.$tc('werkl-blog.elements.blogNewestListing.config.showType.options.all'),
                },
                {
                    id: 2,
                    value: 'select',
                    label: this.$tc('werkl-blog.elements.blogNewestListing.config.showType.options.select'),
                },
            ];
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig('blog-newest-listing');
            this.initElementData('blog-newest-listing');

            await this.loadBlogCategories();
        },

        async loadBlogCategories() {
            this.blogCategoryCollection = new EntityCollection(
                this.blogCategoryRepository.route,
                this.blogCategoryRepository.schema.entity,
                Context.api
            );

            if (this.element.config.blogCategories.value.length <= 0) {
                return;
            }

            const criteria = new Criteria();
            criteria.setIds(this.element.config.blogCategories.value);

            this.blogCategoryCollection = await this.blogCategoryRepository.search(criteria);
        },

        onBlogCategoriesChange() {
            this.element.config.blogCategories.value = this.blogCategoryCollection.getIds();

            if (this.element.translated?.config?.blogCategories) {
                this.element.translated.config.blogCategories = this.blogCategoryCollection.getIds();
            }

            if (!this.element?.data) {
                return;
            }

            this.element.data.blogCategories = this.blogCategoryCollection;
        },
    },
};
