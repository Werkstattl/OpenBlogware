import slugify from 'slugify';
import template from './werkl-blog-detail.html.twig';
import BLOG from '../../constant/open-blogware.constant';

const {
    Component,
    Data,
    Utils,
    Classes,
    ExtensionAPI,
    State,
    Context,
} = Shopware;
const { Criteria } = Data;
const { debounce } = Utils;
const { cloneDeep } = Utils.object;
const { ShopwareError } = Classes;
const debounceTimeout = 300;

Component.extend('werkl-blog-detail', 'sw-cms-detail', {
    template,

    data() {
        return {
            blogId: null,
            blog: null,
            originalSlug: null,
            isLoading: false,
            localeLanguage: null,
            showSectionModal: false,
            sectionDontRemind: false,
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.blog, 'title');
        },

        blogRepository() {
            return this.repositoryFactory.create('werkl_blog_entries');
        },

        localeRepository() {
            return this.repositoryFactory.create('locale');
        },

        loadBlogCriteria() {
            const criteria = new Criteria(1, 1);
            const sortCriteria = Criteria.sort('position', 'ASC', true);

            criteria
                .addAssociation('blogCategories')

                .getAssociation('cmsPage')
                .getAssociation('sections')
                .addSorting(sortCriteria)
                .addAssociation('backgroundMedia')

                .getAssociation('blocks')
                .addSorting(sortCriteria)
                .addAssociation('backgroundMedia')
                .addAssociation('slots');

            return criteria;
        },

        backPath() {
            if (this.$route.query.ids && this.$route.query.ids.length > 0) {
                return {
                    name: 'blog.module.index',
                    query: {
                        ids: this.$route.query.ids,
                        limit: this.$route.query.limit,
                        page: this.$route.query.page,
                    },
                };
            }
            return { name: 'blog.module.index' };
        },

        isCreateMode() {
            return this.$route.name === 'blog.module.create';
        },
    },

    watch: {
        'blog.title': function (blogTitle) {
            this.onBlogTitleChanged(blogTitle);
        },
    },

    methods: {
        createdComponent() {
            this.publishExtensionData();
            State.commit('adminMenu/collapseSidebar');

            const isSystemDefaultLanguage = State.getters['context/isSystemDefaultLanguage'];
            this.$store.commit('cmsPageState/setIsSystemDefaultLanguage', isSystemDefaultLanguage);

            this.resetCmsPageState();

            if (this.$route.params.id) {
                this.isLoading = true;
                this.blogId = this.$route.params.id;

                const defaultStorefrontId = '8A243080F92E4C719546314B577CF82B';

                Shopware.State.commit('shopwareApps/setSelectedIds', [this.pageId]);

                const criteria = new Criteria(1, 25);
                criteria.addFilter(
                    Criteria.equals('typeId', defaultStorefrontId),
                );

                this.salesChannelRepository.search(criteria).then((response) => {
                    this.salesChannels = response;

                    if (this.salesChannels.length > 0) {
                        this.currentSalesChannelKey = this.salesChannels[0].id;
                        this.loadBlog(this.blogId);
                    }
                });
            }

            if (this.acl.can('system_config.read')) {
                this.getDefaultLayouts();
            }

            this.setPageContext();
        },

        publishExtensionData() {
            ExtensionAPI.publishData({
                id: 'werkl-blog-detail__page',
                path: 'page',
                scope: this,
            });

            ExtensionAPI.publishData({
                id: 'werkl-blog-detail__blog',
                path: 'blog',
                scope: this,
            });
        },

        /**
         * Debounced wrapper for the savePage function
         */
        debouncedPageSave: debounce(function debouncedOnSave() {
            this.onSave();
        }, debounceTimeout),

        loadBlog(blogId) {
            this.isLoading = true;

            return this.blogRepository.get(blogId, Context.api, this.loadBlogCriteria).then((entity) => {
                this.blog = entity;
                this.originalSlug = entity.slug;

                if (entity.cmsPageId) {
                    this.page = entity.cmsPage;
                    this.pageId = entity.cmsPageId;
                    delete this.blog.cmsPage;
                    return this.loadCMSDataResolver();
                } else {
                    this.isLoading = false;
                    this.createPage(entity.title);
                    this.blog.cmsPageId = this.page.id;
                    this.blogId = entity.id;
                    return this.loadCMSDataResolver();
                }
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: exception.message,
                    message: exception.response,
                });
            });
        },

        onPageSave(debounced = false) {
            this.onPageUpdate();

            if (debounced) {
                this.debouncedPageSave();
                return;
            }

            this.onSaveBlog();
        },

        addAdditionalSection(type, index) {
            this.onAddSection(type, index);
            this.onSaveBlog();
        },

        async onChangeLanguage() {
            this.isLoading = true;

            return this.salesChannelRepository.search(new Criteria()).then((response) => {
                this.salesChannels = response;
                const isSystemDefaultLanguage = State.getters['context/isSystemDefaultLanguage'];
                this.$store.commit('cmsPageState/setIsSystemDefaultLanguage', isSystemDefaultLanguage);
                return this.loadBlog(this.blogId);
            });
        },

        saveOnLanguageChange() {
            return this.onSaveBlog();
        },

        loadCMSDataResolver() {
            this.isLoading = true;

            return this.cmsDataResolverService.resolve(this.page).then(() => {
                this.updateSectionAndBlockPositions();
                State.commit('cmsPageState/setCurrentPage', this.page);

                this.updateDataMapping();
                this.pageOrigin = cloneDeep(this.page);

                if (this.selectedBlock) {
                    const blockId = this.selectedBlock.id;
                    const blockSectionId = this.selectedBlock.sectionId;
                    this.page.sections.forEach((section) => {
                        if (section.id === blockSectionId) {
                            section.blocks.forEach((block) => {
                                if (block.id === blockId) {
                                    this.setSelectedBlock(blockSectionId, block);
                                }
                            });
                        }
                    });
                }

                this.isLoading = false;
            }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: exception.message,
                    message: exception.response,
                });

                warn(this._name, exception.message, exception.response);
            });
        },

        onSaveBlog() {
            if (!this.blogIsValid()) {
                this.createNotificationError({
                    message: this.$tc('werkl-blog.detail.notification.error.pageInvalid'),
                });

                return Promise.reject();
            }

            if (!this.pageIsValid()) {
                this.createNotificationError({
                    message: this.$tc('werkl-blog.detail.notification.error.pageInvalid'),
                });

                return Promise.reject();
            }

            return this.onSavePageEntity()
                .then(() => this.onSaveBlogEntity())
                .then(() => this.loadBlog(this.blogId))
                .catch(exception => {
                    this.isLoading = false;

                    this.createNotificationError({
                        message: exception.message,
                    });

                    return Promise.reject(exception);
                });
        },

        onSaveBlogEntity() {
            this.isLoading = true;

            return this.blogRepository.save(this.blog, Context.api)
                .catch(exception => {
                    this.createNotificationError({
                        message: exception.message,
                    });

                    return Promise.reject(exception);
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        onSavePageEntity() {
            this.isLoading = true;
            this.deleteEntityAndRequiredConfigKey(this.page.sections);

            return this.pageRepository.save(this.page, Context.api, false).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;

                return Promise.resolve();
            }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    message: exception.message,
                });

                return Promise.reject(exception);
            });
        },

        blogIsValid() {
            State.dispatch('error/resetApiErrors');

            return [
                this.missingTitleValidation(),
                this.missingPublishedAtValidation(),
                this.missingAuthorIdValidation(),
                this.missingCategoriesValidation(),
            ].every(validation => validation);
        },

        missingTitleValidation() {
            if (!this.isSystemDefaultLanguage || this.blog.title) {
                return true;
            }

            this.addBlogError({
                property: 'title',
                message: this.$tc('sw-cms.detail.notification.messageMissingFields'),
            });

            return false;
        },

        missingPublishedAtValidation() {
            if (this.blog.publishedAt) {
                return true;
            }

            this.addBlogError({
                property: 'publishedAt',
                message: this.$tc('sw-cms.detail.notification.messageMissingFields'),
            });

            return false;
        },

        missingAuthorIdValidation() {
            if (this.blog.authorId) {
                return true;
            }

            this.addBlogError({
                property: 'authorId',
                message: this.$tc('sw-cms.detail.notification.messageMissingFields'),
            });

            return false;
        },

        missingCategoriesValidation() {
            if (this.blog.blogCategories && this.blog.blogCategories.length) {
                return true;
            }

            this.addBlogError({
                property: 'blogCategories',
                message: this.$tc('sw-cms.detail.notification.messageMissingFields'),
            });

            return false;
        },

        pageSectionCountValidation() {
            return true;
        },

        onBlogTitleChanged: debounce(function (blogTitle) {
            if (!blogTitle) {
                return;
            }

            this.page.name = blogTitle;
            this.getLocaleLanguage();
            this.generateSlug(blogTitle);
        }, debounceTimeout),

        addBlogError({
            property = null,
            payload = {},
            code = BLOG.REQUIRED_FIELD_ERROR_CODE,
            message = '',
        } = {}) {
            const expression = `werkl_blog_entries.${this.blog.id}.${property}`;
            const error = new ShopwareError({
                code,
                detail: message,
                meta: { parameters: payload },
            });

            State.commit('error/addApiError', {
                expression,
                error,
            });
        },

        getLocaleLanguage() {
            return this.localeRepository.get(Context.api.language.localeId, Context.api).then((result) => {
                this.localeLanguage = result.code.substr(0, result.code.length - 3).toLowerCase();
                return Promise.resolve(this.localeLanguage);
            });
        },

        generateSlug(value) {
            if (!value) {
                return;
            }

            const slug = slugify(value, {
                locale: this.localeLanguage,
                lower: true,
            });

            if (!this.localeLanguage) {
                this.blog.slug = slug;
                return;
            }

            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('slug', slug));

            this.blogRepository.search(criteria, Context.api).then((blogs) => {
                const articlesWithSameSlugCount = blogs.length;
                const isSlugUpdated = this.originalSlug !== slug;

                if (articlesWithSameSlugCount && isSlugUpdated) {
                    this.blog.slug = slug + '-' + '1';
                } else {
                    this.blog.slug = slug;
                }
            }).catch(() => {
                this.blog.slug = slug;
            });
        },

        createPage(name) {
            this.page = this.pageRepository.create();
            this.page.name = name;
            this.page.type = BLOG.PAGE_TYPES.BLOG_DETAIL;
            this.page.sections = [];
            this.pageId = this.page.id;
        },
    },
});
