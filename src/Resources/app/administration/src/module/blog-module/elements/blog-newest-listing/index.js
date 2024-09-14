import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-newest-listing',
    label: 'sas-blog.elements.newestListing.preview.label',
    component: 'sas-cms-el-newest-listing',
    configComponent: 'sas-cms-el-config-newest-listing',
    previewComponent: 'sas-cms-el-preview-newest-listing',
    defaultConfig: {
        itemCount: {
            source: 'static',
            value: 5,
        },
        offsetCount: {
            source: 'static',
            value: 0,
        },
        showType: {
            source: 'static',
            value: 'all',
        },
        blogCategories: {
            source: 'static',
            value: null,
            entity: {
                name: 'sas_blog_categories',
            },
        },
    },
});
