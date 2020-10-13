import template from './sas-cms-preview-blog-detail.html.twig';
import './sas-cms-preview-blog-detail.scss';

Shopware.Component.register('sas-cms-preview-blog-detail', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        }
    }
});
