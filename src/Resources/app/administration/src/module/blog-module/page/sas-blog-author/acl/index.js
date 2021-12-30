Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'sas-blog-author',
    roles: {
        viewer: {
            privileges: [
                'sas_blog_author:read',
                'sas_blog_author_translation:read',
            ],
            dependencies: []
        },
        editor: {
            privileges: [
                'sas_blog_author:update',
                'sas_blog_author_translation:update',
            ],
            dependencies: []
        },
        creator: {
            privileges: [
                'sas_blog_author:create',
                'sas_blog_author_translation:create',
            ],
            dependencies: []
        },
        deleter: {
            privileges: [
                'sas_blog_author:delete',
                'sas_blog_author_translation:delete',
            ],
            dependencies: []
        }
    }
});
