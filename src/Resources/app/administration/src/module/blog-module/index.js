import './blocks';
import './component';
import './elements';
import './extension';
import './page';
import defaultSearchConfiguration from './default-search-configuration';

const { Module } = Shopware;

Module.register('blog-module', {
    type: 'plugin',
    name: 'Blog',
    title: 'werkl-blog.general.mainMenuItemGeneral',
    description: 'werkl-blog.general.descriptionTextModule',
    color: '#F965AF',
    icon: 'regular-content',
    favicon: 'icon-module-content.png',
    entity: 'werkl_blog_entry',

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
                    'werkl_blog_entry:read',
                ],
            },
        },
    ],

    defaultSearchConfiguration,
});
