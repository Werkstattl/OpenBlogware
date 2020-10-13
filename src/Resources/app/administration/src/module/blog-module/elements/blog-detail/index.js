import './component';
//import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-detail',
    label: 'Blog Detail',
    component: 'sas-blog-el-blog-detail',
    //configComponent: 'sas-blog-el-blog-detail-config',
    previewComponent: 'sas-blog-el-blog-detail-preview',
});
