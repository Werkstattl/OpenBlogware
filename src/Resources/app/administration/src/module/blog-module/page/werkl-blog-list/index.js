import template from './werkl-blog-list.html.twig';
import './werkl-blog-list.scss';

const { Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;
const { cloneDeep } = Shopware.Utils.object;

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
        blogEntryRepository() {
            return this.repositoryFactory.create('werkl_blog_entry');
        },

        blogCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        pageRepository() {
            return this.repositoryFactory.create('cms_page');
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

        openSponsorPage() {
            window.open('https://github.com/sponsors/7underlines', '_blank');
        },

        async duplicateBlogEntry(item) {
            this.isLoading = true;

            try {
                // Get the full blog entry with all associations
                const criteria = new Criteria(1, 1);
                criteria.addAssociation('blogAuthor');
                criteria.addAssociation('blogCategories');
                criteria.addAssociation('tags');
                criteria.addAssociation('cmsPage.sections.blocks.slots');

                const originalBlog = await this.blogEntryRepository.get(item.id, Shopware.Context.api, criteria);

                // Create new blog entry
                const newBlog = this.blogEntryRepository.create();

                // Copy basic fields
                newBlog.title = `${originalBlog.title} (Copy)`;
                newBlog.active = false;
                newBlog.teaser = originalBlog.teaser;
                newBlog.metaTitle = originalBlog.metaTitle;
                newBlog.metaDescription = originalBlog.metaDescription;
                newBlog.content = originalBlog.content;
                newBlog.authorId = originalBlog.authorId;
                newBlog.detailTeaserImage = originalBlog.detailTeaserImage;
                newBlog.publishedAt = new Date();

                // Generate new slug
                newBlog.slug = `${originalBlog.slug}-copy-${Date.now()}`;

                // Copy categories
                if (originalBlog.blogCategories && originalBlog.blogCategories.length > 0) {
                    newBlog.blogCategories = originalBlog.blogCategories.map(cat => cat.id);
                }

                // Copy tags
                if (originalBlog.tags && originalBlog.tags.length > 0) {
                    newBlog.tags = originalBlog.tags.map(tag => tag.id);
                }

                // Clone the CMS page
                if (originalBlog.cmsPageId) {
                    const cmsPage = originalBlog.cmsPage;
                    const newCmsPage = this.pageRepository.create();
                    newCmsPage.name = `${originalBlog.title} (Copy)`;
                    newCmsPage.type = cmsPage.type;

                    // Clone sections and blocks
                    if (cmsPage.sections && cmsPage.sections.length > 0) {
                        newCmsPage.sections = cmsPage.sections.map(section => {
                            const newSection = {
                                id: Shopware.Utils.uuidv4(),
                                type: section.type,
                                position: section.position,
                                blocks: (section.blocks || []).map(block => {
                                    const newBlock = {
                                        id: Shopware.Utils.uuidv4(),
                                        type: block.type,
                                        position: block.position,
                                        slot: block.slot,
                                        slots: (block.slots || []).map(slot => {
                                            return {
                                                id: Shopware.Utils.uuidv4(),
                                                type: slot.type,
                                                slot: slot.slot,
                                                config: cloneDeep(slot.config),
                                                translations: cloneDeep(slot.translations),
                                            };
                                        }),
                                    };
                                    return newBlock;
                                }),
                            };
                            return newSection;
                        });
                    }

                    await this.pageRepository.save(newCmsPage, Shopware.Context.api);
                    newBlog.cmsPageId = newCmsPage.id;
                }

                // Save the new blog entry
                await this.blogEntryRepository.save(newBlog, Shopware.Context.api);

                // Reload the list
                await this.getList();

                this.createNotificationSuccess({
                    title: this.$tc('werkl-blog.list.duplicateSuccess'),
                    message: this.$tc('werkl-blog.list.duplicateSuccessMessage', 0, { title: newBlog.title }),
                });
            } catch (error) {
                this.createNotificationError({
                    title: this.$tc('werkl-blog.list.duplicateError'),
                    message: error.message,
                });
            } finally {
                this.isLoading = false;
            }
        },
    },
};
