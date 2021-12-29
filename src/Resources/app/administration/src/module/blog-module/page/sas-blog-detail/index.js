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
            slugBlog: null,
            localeLanguage: null,
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

                this.getLocaleLanguage();

                if (this.localeLanguage !== null) {
                    this.slugMaker(value);
                } else {
                    this.blog.slug = value;
                }
            }
        },
        blogId() {
            this.createdComponent();
        },
    },

    computed: {
        blogRepository() {
            return this.repositoryFactory.create('sas_blog_entries');
        },

        localeRepository() {
            return this.repositoryFactory.create('locale');
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
                this.getBlog(),
                this.getLocaleLanguage(),
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
                this.blog = this.blogRepository.create(Shopware.Context.api);

                return;
            }

            const criteria = new Criteria();
            criteria.addAssociation('blogCategories');

            return this.blogRepository
                .get(this.blogId, Shopware.Context.api, criteria)
                .then((entity) => {
                    this.blog = entity;
                    this.slugBlog = this.blog.slug;
                    return Promise.resolve();
                });

        },

        async changeLanguage() {
            await this.getBlog();
            await this.slugMaker(this.slugBlog);
        },

        onClickSave() {
            if (!this.blog.blogCategories || this.blog.blogCategories.length === 0) {
                this.createNotificationError({
                    message: this.$tc('sas-blog.detail.notification.error.missingCategory')
                });

                return;
            }

            this.isLoading = true;

            this.blogRepository
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

        slugDetailsPage(result, value) {
            this.blog.slug = value;
            if (result.length > 0) {
                if (result[0]['slug'] !== this.slugBlog) {
                    this.blog.slug = value + "-" + "1";
                }
            }
        },

        slugCreatePage(result, value) {

            if (result.length > 0) {

                this.blog.slug = value + "-" + "1";

                return;
            }
            this.blog.slug = value;
        },

        async getLocaleLanguage() {
            return this.localeRepository.get(Shopware.Context.api.language.localeId, Shopware.Context.api).then((result) => {
                this.localeLanguage = result.code.substr(0, result.code.length-3).toLowerCase();
                return Promise.resolve(this.localeLanguage);
            });
        },

        slugMaker(value) {

            value = (value === null) ? "" : value;

            if (this.localeLanguage !== "") {

                const criteria = new Criteria();
                const valueSlug = slugify(value, { locale: this.localeLanguage, lower: true });

                criteria.addFilter(
                    Criteria.equals('slug', valueSlug)
                );

                this.blogRepository.search(criteria, Shopware.Context.api).then((result) => {

                    if (this.$route.name === "blog.module.detail") {
                        this.slugDetailsPage(result, slugify(value, { locale: this.localeLanguage, lower: true }));

                        return;
                    }

                    this.slugCreatePage(result, slugify(value, { locale: this.localeLanguage, lower: true }));
                });
            }
        }
    }
});
