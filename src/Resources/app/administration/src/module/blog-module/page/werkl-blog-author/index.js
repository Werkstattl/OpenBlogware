import './acl';

const { Component } = Shopware;

Component.extend(
    'werkl-blog-author-create',
    'werkl-blog-author-detail',
    () => import('./werkl-blog-author-create')
);
Component.register(
    'werkl-blog-author-detail',
    () => import('./werkl-blog-author-detail')
);
Component.register(
    'werkl-blog-author-list',
    () => import('./werkl-blog-author-list')
);
