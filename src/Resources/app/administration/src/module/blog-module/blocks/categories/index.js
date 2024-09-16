import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-categories',
    label: 'werkl-blog.blocks.blog.categories.label',
    category: 'werkl-blog',
    component: 'werkl-cms-block-categories',
    previewComponent: 'werkl-cms-preview-blog-categories',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        categories: 'blog-categories',
    },
});
