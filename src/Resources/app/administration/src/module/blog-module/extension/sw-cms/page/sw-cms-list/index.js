import BLOG from '../../../../constant/open-blogware.constant';
import template from './sw-cms-list.html.twig';

const { Data: { Criteria } } = Shopware;

export default {
    template,

    computed: {
        listCriteria() {
            const criteria = this.$super('listCriteria');

            criteria.getAssociation('blogEntries').addSorting(Criteria.sort('title', 'ASC')).setLimit(this.associationLimit);

            if (this.currentPageType === null) {
                criteria.addFilter(Criteria.not('AND', [
                    Criteria.equals('type', BLOG.PAGE_TYPES.BLOG_DETAIL),
                ]));
            }

            return criteria;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.assignablePageTypes.includes('blogEntries')) {
                return;
            }

            this.assignablePageTypes.push('blogEntries');
        },

        onListItemClick(page) {
            if (!this.isBlogPage(page)) {
                this.$super('onListItemClick', page);

                return;
            }

            const blogEntry = this.getLinkedBlogEntry(page);

            if (!blogEntry) {
                return;
            }

            this.$router.push({
                name: 'blog.module.detail',
                params: { id: blogEntry.id },
            });
        },

        deleteDisabledToolTip(page) {
            const disabledToolTip = this.$super('deleteDisabledToolTip', page);

            if (page.type === BLOG.PAGE_TYPES.BLOG_DETAIL) {
                disabledToolTip.message = this.$tc('sw-cms.general.deleteDisabledBlogToolTip');
            }

            return disabledToolTip;
        },

        getPageBlogCount(page) {
            return page.extensions.blogEntries.length;
        },

        getPageCount(page) {
            const originalPageCount = this.$super('getPageCount', page);
            let pageCount = Number.isInteger(originalPageCount) ? originalPageCount : 0;

            pageCount += this.getPageBlogCount(page);

            return pageCount ? pageCount : originalPageCount;
        },

        getPages(page) {
            const pages = this.$super('getPages', page);

            return [
                ...pages,
                ...page.extensions.blogEntries.map((item) => item.title),
            ];
        },

        optionContextDeleteDisabled(page) {
            const deleteDisabled = this.$super('optionContextDeleteDisabled', page);

            return deleteDisabled || this.getPageBlogCount(page) > 0;
        },

        isBlogPage(page) {
            return page.type === BLOG.PAGE_TYPES.BLOG_DETAIL;
        },

        getLinkedBlogEntry(page) {
            if (!this.isBlogPage(page)) {
                return null;
            }

            return page.extensions.blogEntries.at(0);
        },
    },
};
