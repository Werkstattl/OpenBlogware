const { Component } = Shopware;

Component.register(
    'werkl-cms-el-blog-single-select',
    () => import('./component')
);
Component.register(
    'werkl-cms-el-config-blog-single-select',
    () => import('./config')
);
Component.register(
    'werkl-cms-el-preview-blog-single-select',
    () => import('./preview')
);

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-single-select',
    label: 'werkl-blog.elements.blogSingleSelect.label',
    component: 'werkl-cms-el-blog-single-select',
    configComponent: 'werkl-cms-el-config-blog-single-select',
    previewComponent: 'werkl-cms-el-preview-blog-single-select',
    defaultConfig: {
        blogEntry: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'werkl_blog_entry',
                criteria: new Shopware.Data.Criteria(1, 25).addAssociation(
                    'blogCategories'
                ),
            },
        },
    },
    collect: Shopware.Service('cmsService').getCollectFunction(),
});
