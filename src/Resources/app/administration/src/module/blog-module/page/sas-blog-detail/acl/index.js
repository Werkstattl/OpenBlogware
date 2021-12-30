Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'sas-blog',
    roles: {
        viewer: {
            privileges: [
                'sas_blog_entries:read',
                'sas_blog_entries_translation:read',
                'sas_blog_blog_category:read',
            ],
            dependencies: []
        },
        editor: {
            privileges: [
                'sas_blog_entries:update',
                'sas_blog_entries_translation:update',
                'system_config:read',
            ],
            dependencies: []
        },
        creator: {
            privileges: [
                'sas_blog_entries:create',
                'sas_blog_entries_translation:create',
                'sas_blog_blog_category:create',
                'system_config:read',
            ],
            dependencies: []
        },
        deleter: {
            privileges: [
                'sas_blog_entries:delete',
                'sas_blog_entries_translation:delete',
            ],
            dependencies: []
        }
    }
});
