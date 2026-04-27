import template from './werkl-blog-list.html.twig';
import './werkl-blog-list.scss';

const { Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('salutation'),
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            categoryId: null,
            blogEntries: null,
            total: 0,
            isLoading: true,
            currentLanguageId: Shopware.Context.api.languageId,
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

        getList() {
            this.isLoading = true;
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('blogAuthor');
            criteria.addAssociation('blogCategories');
            criteria.addAssociation('tags');

            criteria.addSorting(Criteria.sort('publishedAt', 'DESC', false));

            if (this.categoryId) {
                criteria.addFilter(Criteria.equals('blogCategories.id', this.categoryId));
            }
            return this.blogEntryRepository.search(criteria, Shopware.Context.api).then((result) => {
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

        openSponsorPage() {
            window.open('https://github.com/sponsors/7underlines', '_blank');
        },
    },
};
