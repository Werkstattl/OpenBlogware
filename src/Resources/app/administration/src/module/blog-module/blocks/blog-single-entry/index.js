const { Component } = Shopware;

Component.register(
    'sw-cms-block-blog-single-entry',
    () => import('./component')
);
Component.register(
    'werkl-cms-preview-blog-single-entry',
    () => import('./preview')
);

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-single-entry',
    label: 'werkl-blog.blocks.blogSingleEntry.label',
    category: 'werkl-blog',
    component: 'sw-cms-block-blog-single-entry',
    previewComponent: 'werkl-cms-preview-blog-single-entry',
    defaultConfig: {
        marginBottom: null,
        marginTop: null,
        marginLeft: null,
        marginRight: null,
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
