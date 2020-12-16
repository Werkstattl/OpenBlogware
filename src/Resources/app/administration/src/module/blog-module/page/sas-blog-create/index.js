const { Component } = Shopware;

Component.extend('sas-blog-create', 'sas-blog-detail', {

    methods: {
        createdComponent() {
            if (Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.languageId)
            }

            if (!this.blog) {
                if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                    Shopware.State.commit('context/resetLanguageToDefault');
                }
            }

            this.$super('createdComponent');
        },

        getBlog() {
            this.blog = this.repository.create(Shopware.Context.api);
            this.isLoading = false;
        }
    }
});
