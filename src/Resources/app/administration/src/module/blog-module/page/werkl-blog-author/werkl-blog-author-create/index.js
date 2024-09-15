const { Component } = Shopware;

Component.extend('werkl-blog-author-create', 'werkl-blog-author-detail', {
    methods: {
        createdComponent() {
            Shopware.State.commit('context/resetLanguageToDefault');

            this.blogAuthor = this.blogAuthorRepository.create(Shopware.Context.api);
        },
    },
});
