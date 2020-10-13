const { Component } = Shopware;

Component.extend('sas-blog-create', 'sas-blog-detail', {

    created() {
        if (Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
            Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.languageId)
        }
    },

    methods: {
        getBlog() {
            this.blog = this.repository.create(Shopware.Context.api);
            this.editorPro();
        }
    }
});
