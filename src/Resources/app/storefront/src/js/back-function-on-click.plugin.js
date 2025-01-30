import Plugin from 'src/plugin-system/plugin.class';

export default class BackFunctionOnClick extends Plugin {
    init() {
        this.registerEventListeners();
    }

    registerEventListeners() {
        const backButton = this.el.querySelector('[data-back]');
        if (backButton) {
            backButton.addEventListener('click', this.handleBackClick.bind(this));
        }
    }

    handleBackClick() {
        window.history.back();
    }
}
