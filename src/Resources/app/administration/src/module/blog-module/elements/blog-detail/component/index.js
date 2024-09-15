import template from './werkl-blog-element-blog-detail.html.twig';
import './werkl-blog-element-blog-detail.scss';

const { Mixin } = Shopware;

Shopware.Component.register('werkl-blog-el-blog-detail', {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-detail');
        },
    },
});
