Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'werkl-blog-author',
    roles: {
        viewer: {
            privileges: [
                'werkl_blog_author:read',
                'werkl_blog_author_translation:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'werkl_blog_author:update',
                'werkl_blog_author_translation:update',
            ],
            dependencies: [],
        },
        creator: {
            privileges: [
                'werkl_blog_author:create',
                'werkl_blog_author_translation:create',
            ],
            dependencies: [],
        },
        deleter: {
            privileges: [
                'werkl_blog_author:delete',
                'werkl_blog_author_translation:delete',
            ],
            dependencies: [],
        },
    },
});
