import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-newest-listing',
    label: 'sas-blog.blocks.blog.newestListing.label',
    category: 'sas-blog',
    component: 'sas-cms-block-newest-listing',
    previewComponent: 'sas-cms-preview-newest-listing',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        listing: 'blog-newest-listing',
    },
});
