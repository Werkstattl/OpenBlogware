const { Component } = Shopware;

Component.override('sw-cms-list', {
    computed: {
        sortPageTypes() {
            return [
                { value: '', name: this.$tc('sw-cms.sorting.labelSortByAllPages'), active: true },
                { value: 'page', name: this.$tc('sw-cms.detail.label.pageType.page') },
                { value: 'landingpage', name: this.$tc('sw-cms.detail.label.pageType.landingpage') },
                { value: 'product_list', name: this.$tc('sw-cms.detail.label.pageType.productList') },
                { value: 'product_detail', name: this.$tc('sw-cms.detail.label.pageType.productDetail') },
                { value: 'blog_detail', name: this.$tc('sw-cms.sorting.labelSortByBlogPages') },
            ];
        },
    },
});
