import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-single-entry',
    label: 'werkl-blog.blocks.blog.singleEntry.label',
    category: 'werkl-blog',
    component: 'werkl-cms-block-blog-single-entry',
    previewComponent: 'werkl-cms-preview-blog-single-entry',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed',
    },
    slots: {
        singleEntry: {
            type: 'blog-single-select',
            default: {
                config: {
                    blogEntry: {
                        source: 'static',
                        value: null,
                    },
                },
            },
        },
    },
});
