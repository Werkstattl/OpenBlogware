const { Component } = Shopware;

Component.register('werkl-cms-el-blog-categories', () => import('./component'));
Component.register(
    'werkl-cms-el-config-blog-categories',
    () => import('./config')
);
Component.register(
    'werkl-cms-el-preview-blog-categories',
    () => import('./preview')
);

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-categories',
    label: 'werkl-blog.elements.blogCategories.label',
    component: 'werkl-cms-el-blog-categories',
    configComponent: 'werkl-cms-el-config-blog-categories',
    previewComponent: 'werkl-cms-el-preview-blog-categories',
    defaultConfig: {},
});
