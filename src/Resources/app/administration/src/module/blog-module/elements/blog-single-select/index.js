import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-single-select',
    label: 'werkl-blog.elements.single-select.label',
    component: 'sw-cms-el-blog-single-select',
    configComponent: 'sw-cms-el-config-blog-single-select',
    previewComponent: 'sw-cms-el-preview-blog-single-select',
    defaultConfig: {
        blogEntry: {
            source: 'static',
            value: null,
            entity: {
                name: 'werkl_blog_entries',
            },
        },
    },
});
