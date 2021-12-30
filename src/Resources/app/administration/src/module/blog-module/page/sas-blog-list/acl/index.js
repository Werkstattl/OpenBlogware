Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'sas-blog-category',
    roles: {
        viewer: {
            privileges: [
                'sas_blog_category:read',
                'sas_blog_category_translation:read',
            ],
            dependencies: []
        },
        editor: {
            privileges: [
                'sas_blog_category:update',
                'sas_blog_category_translation:update',
            ],
            dependencies: []
        },
        creator: {
            privileges: [
                'sas_blog_category:create',
                'sas_blog_category_translation:create',
            ],
            dependencies: []
        },
        deleter: {
            privileges: [
                'sas_blog_category:delete',
                'sas_blog_category_translation:delete',
            ],
            dependencies: []
        }
    }
});
