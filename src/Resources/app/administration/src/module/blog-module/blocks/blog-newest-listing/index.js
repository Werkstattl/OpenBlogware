const { Component } = Shopware;

Component.register(
    'sw-cms-block-blog-newest-listing',
    () => import('./component')
);
Component.register(
    'werkl-cms-preview-blog-newest-listing',
    () => import('./preview')
);

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-newest-listing',
    label: 'werkl-blog.blocks.blogNewestListing.label',
    category: 'werkl-blog',
    component: 'sw-cms-block-blog-newest-listing',
    previewComponent: 'werkl-cms-preview-blog-newest-listing',
    defaultConfig: {
        marginBottom: null,
        marginTop: null,
        marginLeft: null,
        marginRight: null,
        sizingMode: 'boxed',
    },
    slots: {
        listing: 'blog-newest-listing',
    },
});
