import template from './werkl-cms-el-blog-single-select.html.twig';
import './werkl-cms-el-blog-single-select.scss';

const { Filter, Mixin } = Shopware;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [Mixin.getByName('cms-element')],

    computed: {
        assetFilter() {
            return Filter.getByName('asset');
        },

        blogEntry() {
            if (!this.element?.data?.blogEntry) {
                return {
                    translated: {
                        title: 'Article title',
                        teaser: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus maximus velit, dictum mollis erat finibus quis. Ut dictum ornare dolor, sed mattis tellus gravida vel.',
                    },
                    blogCategories: [
                        {
                            translated: {
                                name: 'Blog category',
                            },
                        },
                    ],
                    media: {
                        url: this.assetFilter(
                            '/administration/administration/static/img/cms/preview_mountain_small.jpg'
                        ),
                    },
                };
            }

            return this.element.data.blogEntry;
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
            this.initElementData('blog-single-select');

            this.loadBlogEntryMedia();
        },

        loadBlogEntryMedia() {
            const blogEntry = this.element?.data?.blogEntry;

            if (
                !blogEntry ||
                blogEntry.media ||
                !blogEntry.translated.mediaId
            ) {
                return;
            }

            this.mediaRepository
                .get(blogEntry.translated.mediaId)
                .then((media) => {
                    this.element.data.blogEntry.media = media;
                });
        },
    },
};
