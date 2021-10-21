const { Component, Mixin } = Shopware;
import template from './sas-blog-detail.html.twig';
import './sas-blog-detail.scss';

import slugify from '@slugify';

const Criteria = Shopware.Data.Criteria;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('sas-blog-detail', {
    template,

    inject: ['repositoryFactory', 'systemConfigApiService'],

    mixins: [Mixin.getByName('notification')],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    props: {
        blogId: {
            type: String,
            required: false,
            default() {
                return null;
            },
        },
    },

    data() {
        return {
            blog: null,
            maximumMetaTitleCharacter: 160,
            maximumMetaDescriptionCharacter: 160,
            configOptions: {},
            isLoading: true,
            processSuccess: false,
            fileAccept: 'image/*',
            moduleData: this.$route.meta.$module,
            isProVersion: false,
        };
    },

    created() {
        this.createdComponent();
    },

    watch: {
        'blog.active': function () {
            return this.blog.active ? 1 : 0;
        },
        'blog.title': function (value) {
            if (typeof value !== 'undefined') {
                this.blog.slug = slugify(value, {
                    lower: true
                });
            }
        },
        blogId() {
            this.createdComponent();
        },
    },

    computed: {
        repository() {
            return this.repositoryFactory.create('sas_blog_entries');
        },

        mediaItem() {
            return this.blog !== null ? this.blog.media : null;
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        backPath() {
            if (this.$route.query.ids && this.$route.query.ids.length > 0) {
                return {
                    name: 'blog.module.index',
                    query: {
                        ids: this.$route.query.ids,
                        limit: this.$route.query.limit,
                        page: this.$route.query.page
                    }
                };
            }
            return { name: 'blog.module.index' };
        },

        isCreateMode() {
            return this.$route.name === 'blog.module.create';
        },

        ...mapPropertyErrors(
            'blog', [
                'title',
                'slug',
                'teaser',
                'authorId',
                'publishedAt'
            ]
        )
    },

    methods: {
        async createdComponent() {
            if(this.isCreateMode) {
                if (Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                    Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.languageId)
                }

                if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                    Shopware.State.commit('context/resetLanguageToDefault');
                }
            }

            await Promise.all([
                this.getPluginConfig(),
                this.getBlog()
            ]);

            this.isLoading = false;
        },

        async getPluginConfig() {
            const config = await this.systemConfigApiService.getValues('SasBlogModule.config');

            this.maximumMetaTitleCharacter = config['SasBlogModule.config.maximumMetaTitleCharacter'];
            this.maximumMetaDescriptionCharacter = config['SasBlogModule.config.maximumMetaDescriptionCharacter'];
        },

        async getBlog() {
            if(!this.blogId) {
                this.blog = this.repository.create(Shopware.Context.api);

                return;
            }

            const criteria = new Criteria();
            criteria.addAssociation('blogCategories');

            return this.repository
                .get(this.blogId, Shopware.Context.api, criteria)
                .then((entity) => {
                    this.blog = entity;

                    return Promise.resolve();
                });
        },

        async changeLanguage() {
            await this.getBlog();
        },

        onClickSave() {
            if (!this.blog.blogCategories || this.blog.blogCategories.length === 0) {
                this.createNotificationError({
                    message: this.$tc('sas-blog.detail.notification.error.missingCategory')
                });

                return;
            }

            this.isLoading = true;

            this.repository
                .save(this.blog, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'blog.module.detail', params: {id: this.blog.id} });

                    this.createNotificationSuccess({
                        title: this.$tc('sas-blog.detail.notification.save-success.title'),
                        message: this.$tc('sas-blog.detail.notification.save-success.text')
                    });
                })
                .catch(exception => {
                    this.isLoading = false;
                });
        },

        onCancel() {
            this.$router.push({ name: 'blog.module.index' });
        },

        onSetMediaItem({ targetId }) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.blog.mediaId = targetId;
                this.blog.media = updatedMedia;
            });
        },

        setMedia([mediaItem], mediaAssoc) {
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

        openMediaSidebar() {
            this.$parent.$parent.$parent.$parent.$refs.mediaSidebarItem.openContent();
        },
    }
});
