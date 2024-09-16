import template from './werkl-cms-preview-blog-categories.html.twig';
import './werkl-cms-preview-blog-categories.scss';

Shopware.Component.register('werkl-cms-preview-blog-categories', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        },
    },
});
