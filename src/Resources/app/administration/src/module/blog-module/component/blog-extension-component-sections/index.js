import template from './blog-extension-component-sections.html.twig';

const { Store } = Shopware;

export default {
    template,

    props: {
        positionIdentifier: {
            type: String,
            required: true,
        },
    },

    computed: {
        componentSections() {
            return (
                Store.get('extensionComponentSections').identifier[
                    this.positionIdentifier
                ] ?? []
            );
        },
    },
};
