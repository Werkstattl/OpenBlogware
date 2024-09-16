const { Module } = Shopware;

/**
 * Components
 */
import './component/blog-extension-component-sections';

/**
 * Extensions
 */
import './extension/sw-cms/component/sw-cms-sidebar';
import './extension/sw-cms/page/sw-cms-list';
import './extension/component/cms/werkl-cms-sidebar';

/**
 * privileges
 */
import './page/werkl-blog-detail/acl';
import './page/werkl-blog-author/acl';
import './page/werkl-blog-list/acl';

/**
 * Pages
 */
import './page/werkl-blog-list';
import './page/werkl-blog-create';
import './page/werkl-blog-detail';

/**
 * Language Snippets
 */
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

/**
 * CMS Blocks
 */
import './blocks/listing';
import './blocks/detail';
import './blocks/single-entry';
import './blocks/newest-listing';
import './blocks/categories';

/**
 * CMS Elements
 */
import './elements/blog-detail';
import './elements/blog';
import './elements/blog-single-select';
import './elements/blog-newest-listing';
import './elements/blog-categories';

/**
 * Blog Category
 */
import './component/blog-tree-item';
import './component/blog-category-tree';
import './component/blog-category-tree-field';

/**
 * Blog author
 */
import './page/werkl-blog-author/werkl-blog-author-list';
import './page/werkl-blog-author/werkl-blog-author-detail';
import './page/werkl-blog-author/werkl-blog-author-create';

import './component/blog-vertical-tabs';

Module.register('blog-module', {
    type: 'plugin',
    name: 'Blog',
    title: 'werkl-blog.general.mainMenuItemGeneral',
    description: 'werkl-blog.general.descriptionTextModule',
    color: '#F965AF',
    icon: 'regular-content',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB,
    },

    routes: {
        index: {
            components: {
                default: 'werkl-blog-list',
            },
            path: 'index',
        },
        create: {
            components: {
                default: 'werkl-blog-create',
            },
            path: 'create',
        },
        detail: {
            component: 'werkl-blog-detail',
            path: 'detail/:id',
        },
        author: {
            path: 'author',
            component: 'werkl-blog-author-list',
            meta: {
                parentPath: 'blog.module.index',
            },
            redirect: {
                name: 'blog.module.author.index',
            },
        },
        'author.index': {
            path: 'author/index',
            component: 'werkl-blog-author-list',
        },
        'author.create': {
            path: 'author/new',
            component: 'werkl-blog-author-create',
            meta: {
                parentPath: 'blog.module.author.index',
            },
        },
        'author.detail': {
            path: 'author/detail/:id',
            component: 'werkl-blog-author-detail',
            meta: {
                parentPath: 'blog.module.author.index',
            },
        },
    },

    navigation: [
        {
            id: 'werkl-blog',
            label: 'werkl-blog.general.mainMenuItemGeneral',
            path: 'blog.module.index',
            parent: 'sw-content',
            meta: {
                privilege: [
                    'werkl-blog-category:read',
                    'werkl_blog_author:read',
                    'werkl_blog_entries:read',
                ],
            },
        },
    ],
});
