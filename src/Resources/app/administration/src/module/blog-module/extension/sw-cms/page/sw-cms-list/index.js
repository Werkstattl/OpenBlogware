const { Component } = Shopware;

Component.override('sw-cms-list', {
    computed: {
        sortPageTypes() {
            return [
                { value: '', name: this.$tc('sw-cms.sorting.labelSortByAllPages'), active: true },
                { value: 'page', name: this.$tc('sw-cms.sorting.labelSortByShopPages') },
                { value: 'landingpage', name: this.$tc('sw-cms.sorting.labelSortByLandingPages') },
                { value: 'product_list', name: this.$tc('sw-cms.sorting.labelSortByCategoryPages') },
                { value: 'product_detail', name: this.$tc('sw-cms.sorting.labelSortByProductPages') },
                { value: 'blog_detail', name: this.$tc('sw-cms.sorting.labelSortByBlogPages') },
            ];
        },
    },
});
