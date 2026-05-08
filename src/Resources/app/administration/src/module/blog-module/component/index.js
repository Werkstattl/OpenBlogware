const { Component } = Shopware;

Component.extend(
    'werkl-blog-category-tree',
    'sw-category-tree',
    () => import('./blog-category-tree')
);
Component.extend(
    'werkl-blog-category-tree-field',
    'sw-category-tree-field',
    () => import('./blog-category-tree-field')
);
Component.register(
    'werkl-blog-extension-component-sections',
    () => import('./blog-extension-component-sections')
);
Component.extend(
    'werkl-blog-tree-item',
    'sw-tree-item',
    () => import('./blog-tree-item')
);
Component.register(
    'werkl-blog-vertical-tabs',
    () => import('./blog-vertical-tabs')
);
