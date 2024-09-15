import template from './blog-vertical-tabs.html.twig';

const { Component } = Shopware;

Component.register('werkl-blog-vertical-tabs', {
    template,

    props: {
        defaultItem: {
            type: String,
            default: 'blog',
        },
    },

    methods: {
        onChangeTab(name) {
            this.currentTab = name;
        },
    },
});
