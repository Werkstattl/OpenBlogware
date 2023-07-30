import Shopware from '../../services/shopware/Shopware';
import repoCmsPage from '../../repositories/admin/general/CmsPageRepository';
import repoDatePicker from '../../repositories/admin/general/DatePickerRepository';
import repoBlogDetails from '../../repositories/admin/blog/BlogDetailsRepository';
import repoBlogForm from '../../repositories/admin/blog/BlogFormRepository';

const shopware = new Shopware();

class BlogDetailAction {

    /**
     * Fill blog title
     * @param title
     */
    fillTitle(title = '') {
        repoBlogForm.getTitleInput().typeAndCheck(title);
    }

    /**
     * Select published at date from date picker
     * @param day
     */
    selectPublishedAt(day = 'past') {
        repoBlogForm.getFieldParentOf(repoBlogForm.getPublishedAtInput()).click();

        if (day === 'today') {
            repoDatePicker.getDatePickerToday().click();

        } else if (day === 'past') {
            repoDatePicker.getDatePickerPrevMonth().click();
            repoDatePicker.getDatePickerStartOfMonth().click();

        } else {
            repoDatePicker.getDatePickerNextMonth().click();
            repoDatePicker.getDatePickerEndOfMonth().click();
        }
    }

    /**
     * Select author
     */
    selectAuthor() {
        repoBlogForm.getFieldParentOf(repoBlogForm.getAuthorInput()).click();
        repoBlogForm.getFirstAuthor().click();
    }

    /**
     * Select category
     */
    selectCategory() {
        repoBlogForm.getFieldParentOf(repoBlogForm.getCategoriesInput()).click();
        repoBlogForm.getFieldParentOf(repoBlogForm.getCategoriesInput())
            .children('.sw-block-field__block')
            .invoke('attr', 'style', 'overflow: visible');
        repoBlogForm.getFirstCategory().click();
    }

    /**
     * Fill blog meta title
     * @param title
     */
    fillMetaTitle(title = '') {
        repoBlogForm.getMetaTitleInput().typeAndCheck(title);
    }

    /**
     * Fill blog meta description
     * @param description
     */
    fillMetaDescription(description = '') {
        repoBlogForm.getMetaDescriptionInput().typeAndCheck(description);
    }

    /**
     * Fill blog information
     * @param title
     * @param publishedAt
     * @param author
     * @param category
     */
    fillBlogForm(title, publishedAt, author, category) {
        if (title) {
            this.fillTitle(title);
        }

        if (publishedAt) {
            this.selectPublishedAt();
        }

        if (author) {
            this.selectAuthor();
        }

        if (category) {
            this.selectCategory();
        }
    }

    /**
     * Open block section
     */
    openBlockSidebar() {
        repoCmsPage.getBlockSidebarNavigationItem().click();
    }

    /**
     * Add full width cms section
     */
    addFullWidthCmsSection() {
        repoCmsPage.getFullWidthSectionButton().should('be.visible');
        repoCmsPage.getFullWidthSectionButton().click();

        repoCmsPage.getEmptySection().should('be.visible');
        repoCmsPage.getEmptySection().click();
    }

    /**
     * Set current block category
     * @param blockCategory
     */
    setBlockCategory(blockCategory) {
        repoCmsPage.getBlockCategoryDropdown().select(blockCategory);
    }

    /**
     * Click save button
     */
    saveBlogDetail() {
        repoBlogDetails.getSaveButton().click();
        cy.wait(1000);
    }

    assertNavItemErrorBadgeIsShown() {
        if (shopware.isVersionGreaterEqual('6.4.8.0')) {
            repoBlogDetails.getBlogDetailNavItemBadge().should('have.class', 'is--error');
        }
    }

    assertNavItemErrorBadgeExists() {
        if (shopware.isVersionGreaterEqual('6.4.8.0')) {
            repoBlogDetails.getBlogDetailNavItemBadge().should('not.exist');
        }
    }
}

export default new BlogDetailAction();
