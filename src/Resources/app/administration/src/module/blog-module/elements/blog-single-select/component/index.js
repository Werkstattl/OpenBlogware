import template from './sw-cms-el-blog-single-select.html.twig';
import './sw-cms-el-blog-single-select.scss';

const { Component, Mixin } = Shopware;

Shopware.Component.register('sw-cms-el-blog-single-select', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    computed: {
        thumbnail() {
            return `${Shopware.Context.api.assetsPath}/administration/static/img/cms/preview_mountain_small.jpg`;
        }
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-single-select');
            this.initElementData('blog-single-select');
        }
    }
});
