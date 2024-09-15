Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'werkl-blog-category',
    roles: {
        viewer: {
            privileges: [
                'werkl_blog_category:read',
                'werkl_blog_category_translation:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'werkl_blog_category:update',
                'werkl_blog_category_translation:update',
            ],
            dependencies: [],
        },
        creator: {
            privileges: [
                'werkl_blog_category:create',
                'werkl_blog_category_translation:create',
            ],
            dependencies: [],
        },
        deleter: {
            privileges: [
                'werkl_blog_category:delete',
                'werkl_blog_category_translation:delete',
            ],
            dependencies: [],
        },
    },
});
