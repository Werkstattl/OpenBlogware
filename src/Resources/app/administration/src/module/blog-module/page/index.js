import './werkl-blog-author';
import './werkl-blog-detail/acl';
import './werkl-blog-list/acl';

const { Component } = Shopware;

Component.extend(
    'werkl-blog-create',
    'werkl-blog-detail',
    () => import('./werkl-blog-create')
);
Component.extend(
    'werkl-blog-detail',
    'sw-cms-detail',
    () => import('./werkl-blog-detail')
);
Component.register('werkl-blog-list', () => import('./werkl-blog-list'));
