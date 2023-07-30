import errorConfig from '../../../../error-config.json';
import template from './sas-cms-sidebar.html.twig';
import './sas-cms-sidebar.scss';

const { Component } = Shopware;
const {
    mapPageErrors,
    mapPropertyErrors,
} = Component.getComponentHelper();

Component.extend('sas-cms-sidebar', 'sw-cms-sidebar', {
    template,

    inject: [
        'repositoryFactory',
        'systemConfigApiService',
    ],

    props: {
        blog: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            fileAccept: 'image/*',
            maximumMetaTitleCharacter: 160,
            maximumMetaDescriptionCharacter: 160,
        };
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.openBlogDetailSideBar();
    },

    computed: {
        blogSalesChannelIds: {
            get() {
                return this.blog.customFields?.salesChannelIds || []
            },
            set(value) {
                let salesChannelIds = null;
                if (value && value.length > 0) {
                    salesChannelIds = value;
                }

                this.blog.customFields = {
                    ...this.blog.customFields,
                    salesChannelIds,
                }
            },
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        positionIdentifierExtension() {
            return 'sas-cms-sidebar-extension';
        },

        mediaItem() {
            return this.blog && this.blog.media;
        },

        ...mapPageErrors(errorConfig),
        ...mapPropertyErrors('blog', ['title', 'slug', 'teaser', 'authorId', 'publishedAt', 'blogCategories']),
    },

    methods: {
        createdComponent() {
            this.systemConfigApiService.getValues('SasBlogModule.config').then(config => {
                this.maximumMetaTitleCharacter = config['SasBlogModule.config.maximumMetaTitleCharacter'];
                this.maximumMetaDescriptionCharacter = config['SasBlogModule.config.maximumMetaDescriptionCharacter'];
            });
        },

        onSetMediaItem({ targetId }) {
            return this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.blog.mediaId = targetId;
                this.blog.media = updatedMedia;
            });
        },

        setMedia([mediaItem]) {
            this.blog.mediaId = mediaItem.id;
            this.blog.media = mediaItem;
        },

        onRemoveMediaItem() {
            this.blog.mediaId = null;
            this.blog.media = null;
        },

        onMediaDropped(dropItem) {
            this.onSetMediaItem({ targetId: dropItem.id });
        },

        openBlogDetailSideBar() {
            this.$nextTick(() => {
                if (!this.$refs.blogDetailSidebar) {
                    return;
                }

                if (typeof this.$refs.blogDetailSidebar.openContent !== 'function') {
                    return;
                }

                this.$refs.blogDetailSidebar.openContent();
            });
        },
    },
});
