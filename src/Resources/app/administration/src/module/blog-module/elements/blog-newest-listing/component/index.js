import template from './sas-cms-el-newest-listing.html.twig';
import './sas-cms-el-newest-listing.scss';

const { Component, Mixin } = Shopware;

Component.register('sas-cms-el-newest-listing', {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-newest-listing');
            this.initElementData('blog-newest-listing');
        },
    },
});
