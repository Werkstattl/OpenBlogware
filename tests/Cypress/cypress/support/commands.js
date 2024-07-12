// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

/**
 * Performs a drag and drop operation
 * @memberOf Cypress.Chainable#
 * @name dragTo
 * @param {String} targetEl - The target element to drag source to
 * @function
 */
Cypress.Commands.add(
    "dragTo",
    {prevSubject: 'element'},
    (subject, targetEl) => {
        cy.wrap(subject).trigger("mousedown");
        cy.wait(200);

        cy.get('.is--draggable').should('be.visible');
        cy.get(targetEl)
            .should('be.visible')
            .trigger('mouseenter')
            .trigger('mousemove', 'center')
            .should('have.class', 'is--droppable')
            .trigger('mouseup');
    }
);
