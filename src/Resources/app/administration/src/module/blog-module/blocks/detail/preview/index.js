import template from './werkl-cms-preview-blog-detail.html.twig';
import './werkl-cms-preview-blog-detail.scss';

Shopware.Component.register('werkl-cms-preview-blog-detail', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        },
    },
});
