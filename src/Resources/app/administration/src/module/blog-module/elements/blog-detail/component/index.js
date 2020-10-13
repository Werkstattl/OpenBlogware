import template from './sas-blog-element-blog-detail.html.twig';
import './sas-blog-element-blog-detail.scss';

const { Component, Mixin } = Shopware;

Shopware.Component.register('sas-blog-el-blog-detail', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-detail');
        },
    }
});
