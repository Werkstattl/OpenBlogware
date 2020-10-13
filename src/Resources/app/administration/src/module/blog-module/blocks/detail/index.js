import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-detail',
    label: 'sas-blog.blocks.blog.detail.label',
    category: 'sas-blog',
    component: 'sas-cms-block-blog-detail',
    previewComponent: 'sas-cms-preview-blog-detail',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed'
    },
    slots: {
        blogDetail: 'blog-detail'
    }
});
