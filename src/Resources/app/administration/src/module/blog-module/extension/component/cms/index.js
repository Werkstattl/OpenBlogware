const { Component } = Shopware;

Component.extend(
    'werkl-cms-sidebar',
    'sw-cms-sidebar',
    () => import('./werkl-cms-sidebar')
);
Component.extend(
    'werkl-cms-slot',
    'sw-cms-slot',
    () => import('./werkl-cms-slot')
);
