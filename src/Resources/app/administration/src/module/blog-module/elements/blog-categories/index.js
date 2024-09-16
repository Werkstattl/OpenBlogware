import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-categories',
    label: 'werkl-blog.elements.categories.preview.label',
    component: 'sw-cms-el-categories',
    configComponent: 'sw-cms-el-config-categories',
    previewComponent: 'sw-cms-el-preview-categories',
    defaultConfig: {
    },
});
