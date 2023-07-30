class BlogDetailsRepository {

    /**
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getPageHeadline() {
        return cy.get('.sw-sidebar-item__headline');
    }

    /**
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getSaveButton() {
        return cy.get('.sw-cms-detail__save-action');
    }

    /**
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getBlogDetailNavItem() {
        return cy.get(':nth-child(1) > .sw-sidebar-navigation-item');
    }

    /**
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getBlogDetailNavItemBadge() {
        return this.getBlogDetailNavItem().children('.sidebar-item-badge');
    }
}

export default new BlogDetailsRepository();
