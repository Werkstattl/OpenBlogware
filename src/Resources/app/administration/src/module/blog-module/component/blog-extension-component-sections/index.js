import template from './blog-extension-component-sections.html.twig';

const {
    Component,
    State,
} = Shopware;

Component.register('werkl-blog-extension-component-sections', {
    template,

    props: {
        positionIdentifier: {
            type: String,
            required: true,
        },
    },

    computed: {
        componentSections() {
            return State.get('extensionComponentSections').identifier[this.positionIdentifier] ?? [];
        },
    },
});
