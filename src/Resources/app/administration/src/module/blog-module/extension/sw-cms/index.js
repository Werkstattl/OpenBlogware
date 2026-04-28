const { Component } = Shopware;

Component.override(
    'sw-cms-sidebar',
    () => import('./component/sw-cms-sidebar')
);
Component.override('sw-cms-list', () => import('./page/sw-cms-list'));
