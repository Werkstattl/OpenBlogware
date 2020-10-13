import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-listing',
    label: 'sas-blog.blocks.blog.listing.label',
    category: 'sas-blog',
    component: 'sas-cms-block-blog',
    previewComponent: 'sas-cms-preview-blog-listing',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed'
    },
    slots: {
        listing: 'blog'
    }
});
