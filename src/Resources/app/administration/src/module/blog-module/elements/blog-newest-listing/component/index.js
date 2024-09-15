import template from './werkl-cms-el-newest-listing.html.twig';
import './werkl-cms-el-newest-listing.scss';

const { Component, Mixin } = Shopware;

Component.register('werkl-cms-el-newest-listing', {
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
