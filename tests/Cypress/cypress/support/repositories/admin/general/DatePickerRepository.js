class DatePickerRepository {

    /**
     * Get today's date
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getDatePickerToday() {
        return cy.get('.flatpickr-day.today');
    }

    /**
     * Get first day of the month in the date picker
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getDatePickerStartOfMonth() {
        return cy.get('.flatpickr-day:first-child');
    }

    /**
     * Get last day of the month in the date picker
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getDatePickerEndOfMonth() {
        return cy.get('.flatpickr-day:last-child');
    }

    /**
     * Get the previous month button in the date picker
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getDatePickerPrevMonth() {
        return cy.get('.flatpickr-prev-month');
    }

    /**
     * Get the next month button in the date picker
     * @returns {Cypress.Chainable<JQuery<HTMLElement>>}
     */
    getDatePickerNextMonth() {
        return cy.get('.flatpickr-next-month');
    }
}

export default new DatePickerRepository();
