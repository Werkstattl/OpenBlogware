class CmsPageRepository {
    /**
     * Get button to add full width section
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getBlockSidebarNavigationItem() {
        return cy.get('button[title="Blocks"].sw-sidebar-navigation-item');
    }

    /**
     * Get button to add full width section
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getFullWidthSectionButton() {
        return cy.get('.sw-cms-stage-section-selection__default-preview');
    }

    /**
     * Get empty section se
     * @returns {string}
     */
    getEmptySectionSelector() {
        return '.sw-cms-section__empty-stage';
    }

    /**
     * Get empty section in cms page
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getEmptySection() {
        return cy.get(this.getEmptySectionSelector());
    }

    /**
     * Get block category dropdown element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getBlockCategoryDropdown() {
        return cy.get('#sw-field--currentBlockCategory');
    }

    /**
     * Get block category dropdown element
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getBlockCategorySasBlogOption() {
        return this.getBlockCategoryDropdown().children('option[value="sas-blog"]');
    }

    /**
     * Get cms text block
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getCmsTextBlock() {
        return cy.get('.sw-cms-preview-text');
    }

    /**
     * Get cms block config overlay
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getCmsBlockConfigOverlay() {
        return cy.get('.sw-cms-block__config-overlay');
    }
}

export default new CmsPageRepository();
