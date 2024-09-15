import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-newest-listing',
    label: 'werkl-blog.blocks.blog.newestListing.label',
    category: 'werkl-blog',
    component: 'werkl-cms-block-newest-listing',
    previewComponent: 'werkl-cms-preview-newest-listing',
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
