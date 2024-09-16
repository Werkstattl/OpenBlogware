import template from './sw-cms-el-categories.html.twig';
import './sw-cms-el-categories.scss';

const { Mixin } = Shopware;

Shopware.Component.register('sw-cms-el-categories', {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-categories');
        },
    },
});
