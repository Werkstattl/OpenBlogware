import template from './sas-cms-preview-blog-listing.html.twig';
import './sas-cms-preview-blog-listing.scss';

Shopware.Component.register('sas-cms-preview-blog-listing', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        }
    }
});
