Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'werkl-blog',
    roles: {
        viewer: {
            privileges: [
                'werkl_blog_entries:read',
                'werkl_blog_entries_translation:read',
                'werkl_blog_blog_category:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'werkl_blog_entries:update',
                'werkl_blog_entries_translation:update',
                'system_config:read',
            ],
            dependencies: [],
        },
        creator: {
            privileges: [
                'werkl_blog_entries:create',
                'werkl_blog_entries_translation:create',
                'werkl_blog_blog_category:create',
                'system_config:read',
            ],
            dependencies: [],
        },
        deleter: {
            privileges: [
                'werkl_blog_entries:delete',
                'werkl_blog_entries_translation:delete',
            ],
            dependencies: [],
        },
    },
});
