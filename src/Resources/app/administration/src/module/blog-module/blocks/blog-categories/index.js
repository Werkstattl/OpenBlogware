const { Component } = Shopware;

Component.register('sw-cms-block-blog-categories', () => import('./component'));
Component.register(
    'werkl-cms-preview-blog-categories',
    () => import('./preview')
);

Shopware.Service('cmsService').registerCmsBlock({
    name: 'blog-categories',
    label: 'werkl-blog.blocks.blogCategories.label',
    category: 'werkl-blog',
    component: 'sw-cms-block-blog-categories',
    previewComponent: 'werkl-cms-preview-blog-categories',
    defaultConfig: {
        marginBottom: null,
        marginTop: null,
        marginLeft: null,
        marginRight: null,
        sizingMode: 'boxed',
    },
    slots: {
        categories: 'blog-categories',
    },
});
