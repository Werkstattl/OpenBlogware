import template from './blog-tree-item.html.twig';

const { Component } = Shopware;

Component.extend('sas-blog-tree-item', 'sw-tree-item', {
    template,

    computed: {
        parentScope() {
            let parentNode = this.$parent;
            // eslint-disable-next-line
            while (parentNode.$options.name !== 'sas-blog-tree') {
                parentNode = parentNode.$parent;
            }

            return parentNode;
        },
    },

    data() {
        return {
            editingCategory: null,
        };
    },
    methods: {
        onEditCategory(category) {
            this.editingCategory = category;
            this.currentEditElement = category.id;
            this.editElementName();
        },

        onBlurTreeItemInput(item) {
            this.abortCreateElement(item);
        },

        onCancelSubmit(item) {
            this.abortCreateElement(item);
        },

        abortCreateElement(item) {
            this.currentEditElement = null;
            this.editingCategory = null;
            this.$super('abortCreateElement', item);
        },
    },
});
