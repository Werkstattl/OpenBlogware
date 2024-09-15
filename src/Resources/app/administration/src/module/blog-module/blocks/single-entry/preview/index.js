import template from './werkl-cms-preview-blog-single-entry.html.twig';
import './werkl-cms-preview-blog-single-entry.scss';

Shopware.Component.register('werkl-cms-preview-blog-single-entry', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        },
    },
});
