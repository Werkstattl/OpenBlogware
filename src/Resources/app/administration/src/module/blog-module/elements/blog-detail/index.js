const { Component } = Shopware;

Component.register('werkl-blog-el-blog-detail', () => import('./component'));
Component.register('sw-cms-el-config-blog-detail', () => import('./config'));
Component.register(
    'werkl-blog-el-blog-detail-preview',
    () => import('./preview')
);

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-detail',
    label: 'werkl-blog.elements.blogDetail.label',
    component: 'werkl-blog-el-blog-detail',
    configComponent: 'sw-cms-el-config-blog-detail',
    previewComponent: 'werkl-blog-el-blog-detail-preview',
    defaultConfig: {
        showCategory: {
            source: 'static',
            value: true,
        },
        showAuthor: {
            source: 'static',
            value: true,
        },
        fullWidth: {
            source: 'static',
            value: false,
        },
    },
});
