import template from './werkl-cms-preview-blog-listing.html.twig';
import './werkl-cms-preview-blog-listing.scss';

Shopware.Component.register('werkl-cms-preview-blog-listing', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        },
    },
});
