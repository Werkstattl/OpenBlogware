import template from './sas-blog-category-tree.html.twig';

const { Component } = Shopware;

Component.extend('sas-blog-category-tree', 'sw-category-tree', {
    template,

    data() {
        return {
            blogCategory: null,
            translationContext: 'sas-blog-category',
        };
    },

    methods: {
        changeCategory(category) {
            this.$emit('change-category-id', category.id);
        },
    },

    computed: {
        category() {
            return this.blogCategory;
        },

        categoryRepository() {
            return this.repositoryFactory.create('sas_blog_category');
        },

        disableContextMenu() {
            if (!this.allowEdit) {
                return true;
            }

            return this.currentLanguageId !== Shopware.Context.api.systemLanguageId;
        },
        syncProducts() {
            return;
        },
    },
});
