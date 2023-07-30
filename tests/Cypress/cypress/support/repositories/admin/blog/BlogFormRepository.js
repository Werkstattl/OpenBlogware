class BlogFormRepository {

    /**
     * Get field's parent element of the given element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getFieldParentOf(element) {
        return element.parents('.sw-field');
    }

    /**
     * Get title input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getTitleInput() {
        return cy.get('input[name="sw-field--blog-title"]');
    }

    /**
     * Get slug input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getSlugInput() {
        return cy.get('input[name="sw-field--blog-slug"]');
    }

    /**
     * Get teaser input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getTeaserInput() {
        return cy.get('input[name="sw-field--blog-teaser"]');
    }

    /**
     * Get publish date input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getPublishedAtInput() {
        return cy.get('input[name="sw-field--blog-publishedAt"]');
    }

    /**
     * Get author input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getAuthorInput() {
        return cy.get('.sas-field--author .sw-entity-single-select__selection-input');
    }

    /**
     * Get first author option
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getFirstAuthor() {
        return cy.get('.sw-popover__wrapper .sw-select-result');
    }

    /**
     * Get category input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getCategoriesInput() {
        return cy.get('.sas-field--category .sw-category-tree__input-field');
    }

    /**
     * Get first category option
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getFirstCategory() {
        return cy.get('.sw-tree-item input[type="checkbox"]');
    }

    /**
     * Get active input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getActiveInput() {
        return cy.get('input[name="sw-field--blog-active"]');
    }

    /**
     * Get show teaser input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getShowTeaserImageInput() {
        return cy.get('input[name="sw-field--blog-detailTeaserImage"]');
    }

    /**
     * Get meta title input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getMetaTitleInput() {
        return cy.get('input[name="sw-field--blog-metaTitle"]');
    }

    /**
     * Get meta description input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getMetaDescriptionInput() {
        return cy.get('textarea[name="sw-field--blog-metaDescription"]');
    }

    /**
     * Get serp preview container
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getSerpPreviewContainer() {
        return cy.get('.serp-preview');
    }

    /**
     * Get meta description input element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getSidebarItemContainer() {
        return cy.get('.sw-sidebar-item__scrollable-container');
    }

}

export default new BlogFormRepository();
