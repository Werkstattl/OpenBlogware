import BLOG from '../../../../constant/open-blogware.constant';

const { Data: { Criteria } } = Shopware;

export default {
    computed: {
        listCriteria() {
            const criteria = this.$super('listCriteria');

            if (this.currentPageType === null) {
                criteria.addFilter(Criteria.not('AND', [
                    Criteria.equals('type', BLOG.PAGE_TYPES.BLOG_DETAIL),
                ]));
            }

            return criteria;
        },
    },
};
