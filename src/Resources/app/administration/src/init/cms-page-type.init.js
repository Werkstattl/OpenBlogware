import BLOG from '../module/blog-module/constant/open-blogware.constant';

const pageTypeService = Shopware.Service('cmsPageTypeService');

pageTypeService.register({
    name: BLOG.PAGE_TYPES.BLOG_DETAIL,
    icon: 'regular-file-text',
    // blog pages are created in the blog module together with their blog entry
    hideInList: true,
});
