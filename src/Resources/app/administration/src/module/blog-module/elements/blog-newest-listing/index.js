import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-newest-listing',
    label: 'werkl-blog.elements.newestListing.preview.label',
    component: 'werkl-cms-el-newest-listing',
    configComponent: 'werkl-cms-el-config-newest-listing',
    previewComponent: 'werkl-cms-el-preview-newest-listing',
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
                name: 'werkl_blog_categories',
            },
        },
    },
});
