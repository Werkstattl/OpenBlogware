import template from './werkl-cms-el-config-blog.html.twig';
import './werkl-cms-el-config-blog.scss';

const { Context, Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;

export default {
    template,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [Mixin.getByName('cms-element')],

    data() {
        return {
            blogCategoryCollection: null,
            tagCollection: null,
        };
    },
    computed: {
        blogCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        tagRepository() {
            return this.repositoryFactory.create('tag');
        },

        showTypeOptions() {
            return [
                {
                    id: 1,
                    value: 'all',
                    label: this.$tc(
                        'werkl-blog.elements.blog.config.showType.options.all'
                    ),
                },
                {
                    id: 2,
                    value: 'select',
                    label: this.$tc(
                        'werkl-blog.elements.blog.config.showType.options.select'
                    ),
                },
            ];
        },

        showTagsOptions() {
            return [
                {
                    id: 1,
                    value: 'all',
                    label: this.$tc(
                        'werkl-blog.elements.blog.config.showTags.options.all'
                    ),
                },
                {
                    id: 2,
                    value: 'select',
                    label: this.$tc(
                        'werkl-blog.elements.blog.config.showTags.options.select'
                    ),
                },
            ];
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig('blog');
            this.initElementData('blog');

            await this.loadBlogCategories();
            await this.loadTags();
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

            this.blogCategoryCollection =
                await this.blogCategoryRepository.search(criteria);
        },

        async loadTags() {
            this.tagCollection = new EntityCollection(
                this.tagRepository.route,
                this.tagRepository.schema.entity,
                Context.api
            );

            if (this.element.config.blogTags.value.length <= 0) {
                return;
            }

            const criteria = new Criteria();
            criteria.setIds(this.element.config.blogTags.value);

            this.tagCollection = await this.tagRepository.search(criteria);
        },

        onBlogCategoriesChange() {
            this.element.config.blogCategories.value =
                this.blogCategoryCollection.getIds();

            if (this.element.translated?.config?.blogCategories) {
                this.element.translated.config.blogCategories =
                    this.blogCategoryCollection.getIds();
            }

            if (!this.element?.data) {
                return;
            }

            this.element.data.blogCategories = this.blogCategoryCollection;
        },

        onTagsChange() {
            this.element.config.blogTags.value = this.tagCollection.getIds();

            if (this.element.translated?.config?.blogTags) {
                this.element.translated.config.blogTags =
                    this.tagCollection.getIds();
            }

            if (!this.element?.data) {
                return;
            }

            this.element.data.blogTags = this.tagCollection;
        },
    },
};
