export default {
    methods: {
        createdComponent() {
            Shopware.Store.get('context').resetLanguageToDefault();

            this.blogAuthor = this.blogAuthorRepository.create();
        },
    },
};
