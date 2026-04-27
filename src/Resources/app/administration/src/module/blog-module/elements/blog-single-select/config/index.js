import template from './werkl-cms-el-config-blog-single-select.html.twig';

const { Context, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: ['repositoryFactory'],

    emits: ['element-update'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        blogEntryRepository() {
            return this.repositoryFactory.create('werkl_blog_entry');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-single-select');
        },

        onBlogEntryChange(blogEntryId) {
            if (!blogEntryId) {
                this.element.config.blogEntry.value = null;
                this.element.data.blogEntry = null;
            } else {
                const criteria = new Criteria();
                criteria.addAssociation('blogCategories');

                this.blogEntryRepository.get(blogEntryId, Context.api, criteria).then((blogEntry) => {
                    this.element.config.blogEntry.value = blogEntryId;

                    if (!blogEntry.translated.mediaId) {
                        this.element.data.blogEntry = blogEntry;

                        return;
                    }

                    this.mediaRepository.get(blogEntry.translated.mediaId).then((media) => {
                        blogEntry.media = media;
                        this.element.data.blogEntry = blogEntry;
                    });
                });
            }

            this.$emit('element-update', this.element);
        },
    },
};
