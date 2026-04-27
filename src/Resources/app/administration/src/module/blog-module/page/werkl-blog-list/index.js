import template from './werkl-blog-list.html.twig';
import './werkl-blog-list.scss';

const { Context, Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('salutation'),
        Mixin.getByName('listing'),
        Mixin.getByName('version-compare'),
    ],

    data() {
        return {
            categoryId: null,
            blogEntries: null,
            total: 0,
            isLoading: true,
            currentLanguageId: Context.api.languageId,
            term: '',
            searchConfigEntity: 'werkl_blog_entry',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    created() {
        this.getList();
    },

    computed: {
        cmsPageRepository() {
            return this.repositoryFactory.create('cms_page');
        },

        blogEntryRepository() {
            return this.repositoryFactory.create('werkl_blog_entry');
        },

        blogCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },

        columns() {
            return [
                {
                    property: 'title',
                    dataIndex: 'title',
                    label: this.$tc('werkl-blog.list.table.title'),
                    routerLink: 'blog.module.detail',
                    primary: true,
                    inlineEdit: 'string',
                },
                {
                    property: 'author',
                    label: this.$tc('werkl-blog.list.table.author'),
                    inlineEdit: false,
                },
                {
                    property: 'publishedAt',
                    label: this.$tc('werkl-blog.list.table.publishedAt'),
                    inlineEdit: false,
                },
                {
                    property: 'active',
                    label: this.$tc('werkl-blog.list.table.active'),
                    inlineEdit: 'boolean',
                },
            ];
        },

        listCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('blogAuthor');
            criteria.addAssociation('blogCategories');
            criteria.addAssociation('tags');

            criteria.addSorting(Criteria.sort('publishedAt', 'DESC', false));

            if (this.categoryId) {
                criteria.addFilter(Criteria.equals('blogCategories.id', this.categoryId));
            }

            return criteria;
        },

        adminEsEnable() {
            if (!Shopware.Feature.isActive('ENABLE_OPENSEARCH_FOR_ADMIN_API')) {
                return false;
            }

            return Context.app.adminEsEnable ?? false;
        },
    },

    methods: {
        changeLanguage(newLanguageId) {
            this.currentLanguageId = newLanguageId;
            this.getList();
        },

        changeCategoryId(categoryId) {
            if (categoryId && categoryId !== this.categoryId) {
                this.categoryId = categoryId;
                this.getList();
            }
        },

        async getList() {
            this.isLoading = true;

            let criteria;
            if (this.adminEsEnable) {
                criteria = this.listCriteria;
                criteria.setTerm(this.term);
            } else {
                criteria = await this.addQueryScores(this.term, this.listCriteria);
            }
            if (!this.entitySearchable) {
                this.isLoading = false;
                this.total = 0;

                return false;
            }

            return this.blogEntryRepository.search(criteria).then((result) => {
                this.total = result.total;
                this.blogEntries = result;
                this.isLoading = false;
            });
        },

        resetList() {
            this.page = 1;
            this.pages = [];
            this.updateRoute({
                page: this.page,
                limit: this.limit,
                term: this.term,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection,
            });
            this.getList();
        },

        onDuplicate(blogEntry, behavior = { overwrites: {} }) {
            if (!behavior.overwrites) {
                behavior.overwrites = {};
            }

            if (!behavior.overwrites.title) {
                behavior.overwrites.title = `${blogEntry.title} - ${this.$tc('global.default.copy')}`;
            }

            if (!behavior.overwrites.active) {
                behavior.overwrites.active = false;
            }

            if (!behavior.overwrites.slug) {
                behavior.overwrites.slug = `${blogEntry.slug}-copy`;
            }

            this.isLoading = true;
            this.cmsPageRepository
                .clone(blogEntry.cmsPageId, { overwrites: { name: behavior.overwrites.title } })
                .then((response) => {
                    const cmsPageId = response.id;
                    behavior.overwrites.cmsPageId = cmsPageId;

                    this.blogEntryRepository
                        .clone(blogEntry.id, behavior)
                        .then(() => {
                            this.resetList();
                            this.isLoading = false;
                        })
                        .catch(() => {
                            this.cmsPageRepository.delete(cmsPageId);
                            this.isLoading = false;
                            this.createNotificationError({
                                message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                            });
                        });
                })
                .catch(() => {
                    this.isLoading = false;
                    this.createNotificationError({
                        message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                    });
                });
        },

        onSearch(value = null) {
            if (!value.length || value.length <= 0) {
                this.term = null;
            } else {
                this.term = value;
            }

            this.resetList();
        },

        updateTotal({ total }) {
            this.total = total;
        },

        openSponsorPage() {
            window.open('https://github.com/sponsors/7underlines', '_blank');
        },
    },
};
