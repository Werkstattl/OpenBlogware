import template from './sw-cms-el-config-blog-single-select.html.twig';

const { Component, Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;

Component.register('sw-cms-el-config-blog-single-select', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            blogEntry: null,
            selectedEntry: null
        }
    },
    computed: {
        blogEntryRepository() {
            return this.repositoryFactory.create('sas_blog_entries');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-single-select');
        }
    }
});
