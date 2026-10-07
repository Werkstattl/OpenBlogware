Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'werkl-blog',
    roles: {
        viewer: {
            privileges: [
                'werkl_blog_entry:read',
                'werkl_blog_entry_translation:read',
                'werkl_blog_entry_blog_category:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'werkl_blog_entry:update',
                'werkl_blog_entry_translation:update',
                'system_config:read',
            ],
            dependencies: [],
        },
        creator: {
            privileges: [
                'werkl_blog_entry:create',
                'werkl_blog_entry_translation:create',
                'werkl_blog_entry_blog_category:create',
                'system_config:read',
            ],
            dependencies: [],
        },
        deleter: {
            privileges: [
                'werkl_blog_entry:delete',
                'werkl_blog_entry_translation:delete',
            ],
            dependencies: [],
        },
    },
});
