import template from './werkl-blog-list.twig';
import './werkl-blog-list.scss';

const { Component, Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.register('werkl-blog-list', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('salutation'),
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            categoryId: null,
            blogEntries: null,
            total: 0,
            isLoading: true,
            currentLanguageId: Shopware.Context.api.languageId,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    created() {
        this.getList();
    },

    computed: {
        blogEntriesRepository() {
            return this.repositoryFactory.create('werkl_blog_entries');
        },

        blogCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },

        columns() {
            return [
                {
                    property: 'title',
                    dataIndex: 'title',
                    label: this.$tc('werkl-blog.list.table.title'),
                    routerLink: 'blog.module.detail',
                    primary: true,
                    inlineEdit: 'string',
                },
                {
                    property: 'author',
                    label: this.$tc('werkl-blog.list.table.author'),
                    inlineEdit: false,
                },
                {
                    property: 'publishedAt',
                    label: this.$tc('werkl-blog.list.table.publishedAt'),
                    inlineEdit: false,
                },
                {
                    property: 'active',
                    label: this.$tc('werkl-blog.list.table.active'),
                    inlineEdit: 'boolean',
                },
            ];
        },
    },

    methods: {
        changeLanguage(newLanguageId) {
            this.currentLanguageId = newLanguageId;
            this.getList();
        },

        changeCategoryId(categoryId) {
            if (categoryId && categoryId !== this.categoryId) {
                this.categoryId = categoryId;
                this.getList();
            }
        },

        getList() {
            this.isLoading = true;
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('blogAuthor');
            criteria.addAssociation('blogCategories');

            criteria.addSorting(Criteria.sort('publishedAt', 'DESC', false));

            if (this.categoryId) {
                criteria.addFilter(Criteria.equals('blogCategories.id', this.categoryId));
            }
            return this.blogEntriesRepository.search(criteria, Shopware.Context.api).then((result) => {
                this.total = result.total;
                this.blogEntries = result;
                this.isLoading = false;
            });
        },
    },
});
