const {
    Data: { Criteria },
} = Shopware;

export default {
    data() {
        return {
            isBlogPageLookupCancelled: false,
        };
    },

    beforeUnmount() {
        this.isBlogPageLookupCancelled = true;
    },

    methods: {
        async createdComponent() {
            const route = this.$route;
            const blogEntry = await this.findBlogEntryForPage(route.params.id);

            // A late response must not redirect or reset stores after navigation.
            if (this.isBlogPageLookupCancelled || this.$route !== route) {
                return;
            }

            if (blogEntry) {
                // blog pages belong to the blog module, the core editor has no blog sidebar
                this.$router.replace({
                    name: 'blog.module.detail',
                    params: { id: blogEntry.id },
                });

                return;
            }

            this.$super('createdComponent');
        },

        async findBlogEntryForPage(pageId) {
            if (!pageId) {
                return null;
            }

            const criteria = new Criteria(1, 1);
            criteria.addFilter(Criteria.equals('cmsPageId', pageId));

            try {
                const blogEntries = await this.repositoryFactory
                    .create('werkl_blog_entry')
                    .search(criteria, Shopware.Context.api);

                return blogEntries.first();
            } catch {
                // a failing lookup must not block the core editor
                return null;
            }
        },
    },
};
