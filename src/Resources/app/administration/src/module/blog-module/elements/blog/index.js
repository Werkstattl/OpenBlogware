const { Component } = Shopware;

Component.register('werkl-cms-el-blog', () => import('./component'));
Component.register('werkl-cms-el-config-blog', () => import('./config'));
Component.register('werkl-cms-el-preview-blog', () => import('./preview'));

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog',
    label: 'werkl-blog.elements.blog.label',
    component: 'werkl-cms-el-blog',
    configComponent: 'werkl-cms-el-config-blog',
    previewComponent: 'werkl-cms-el-preview-blog',
    defaultConfig: {
        paginationCount: {
            source: 'static',
            value: 5,
        },
        showType: {
            source: 'static',
            value: 'all',
        },
        showCategoryFilter: {
            source: 'static',
            value: true,
        },
        showAuthorFilter: {
            source: 'static',
            value: true,
        },
        blogCategories: {
            source: 'static',
            value: [],
            entity: {
                name: 'werkl_blog_categories',
            },
        },
        showTags: {
            source: 'static',
            value: 'all',
        },
        blogTags: {
            source: 'static',
            value: [],
            entity: {
                name: 'tag',
            },
        },
    },
    collect: Shopware.Service('cmsService').getCollectFunction(),
});
