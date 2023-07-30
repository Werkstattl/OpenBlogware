import blogDetailAction from '../../support/actions/admin/BlogDetailAction';
import repoBlogDetails from '../../support/repositories/admin/blog/BlogDetailsRepository';
import repoBlogForm from '../../support/repositories/admin/blog/BlogFormRepository';

const blogTitle = 'Blog Entry Title';
const blogMetaTitle = 'blog meta title';
const blogMetaDescription = 'blog meta description';
const expectedBlogSlug = 'blog-entry-title';

describe('Test blog form on sidebar at blog detail page', () => {
    beforeEach(() => {
        cy.loginViaApi().then(() => {
            cy.viewport(1920, 1080);
            cy.openInitialPage(`${Cypress.env('admin')}#/blog/module/create`);
        });
    });

    describe('Test fields validations', () => {
        it('@content: show display correct blog detail page', () => {

            cy.get('.sw-cms-detail').should('be.visible');

            repoBlogDetails.getPageHeadline().contains('Blog detail');
        });

        it('@content: show error if all fields are empty', () => {

            blogDetailAction.saveBlogDetail();

            blogDetailAction.assertNavItemErrorBadgeIsShown();
        });

        it('@content: show error if title is empty', () => {

            blogDetailAction.fillBlogForm(null, true, true, true);
            blogDetailAction.saveBlogDetail();

            blogDetailAction.assertNavItemErrorBadgeIsShown();
            repoBlogForm.getFieldParentOf(repoBlogForm.getTitleInput()).should('have.class', 'has--error');
        });

        it('@content: show error if published at is empty', () => {

            blogDetailAction.fillBlogForm(blogTitle, false, true, true);
            blogDetailAction.saveBlogDetail();

            blogDetailAction.assertNavItemErrorBadgeIsShown();
            repoBlogForm.getFieldParentOf(repoBlogForm.getPublishedAtInput()).should('have.class', 'has--error');
        });

        it('@content: show error if author is empty', () => {

            blogDetailAction.fillBlogForm(blogTitle, true, false, true);
            blogDetailAction.saveBlogDetail();

            blogDetailAction.assertNavItemErrorBadgeIsShown();
            repoBlogForm.getFieldParentOf(repoBlogForm.getAuthorInput()).should('have.class', 'has--error');
        });

        it('@content: show error if category is empty', () => {

            blogDetailAction.fillBlogForm(blogTitle, true, true, false);
            blogDetailAction.saveBlogDetail();

            blogDetailAction.assertNavItemErrorBadgeIsShown();
            repoBlogForm.getFieldParentOf(repoBlogForm.getCategoriesInput()).should('have.class', 'has--error');
        });

        it('@content: can create blog with valid value', () => {

            blogDetailAction.fillBlogForm(blogTitle, true, true, true);
            blogDetailAction.saveBlogDetail();

            blogDetailAction.assertNavItemErrorBadgeExists();
            repoBlogForm.getFieldParentOf(repoBlogForm.getTitleInput()).should('not.have.class', 'has--error');
            repoBlogForm.getFieldParentOf(repoBlogForm.getPublishedAtInput()).should('not.have.class', 'has--error');
            repoBlogForm.getFieldParentOf(repoBlogForm.getAuthorInput()).should('not.have.class', 'has--error');
            repoBlogForm.getFieldParentOf(repoBlogForm.getCategoriesInput()).should('not.have.class', 'has--error');
        });
    });

    describe('Test slug generator', () => {
        it('@content: generate correct slug', () => {

            blogDetailAction.fillBlogForm(blogTitle, true, true, true);

            repoBlogForm.getSlugInput().should('contain.value', expectedBlogSlug);
        });
    });

    describe('Test meta information', () => {
        it('@content: show meta information preview', () => {

            repoBlogForm.getSidebarItemContainer().scrollTo('bottom');

            blogDetailAction.fillMetaTitle(blogMetaTitle);
            blogDetailAction.fillMetaDescription(blogMetaDescription);

            repoBlogForm.getSerpPreviewContainer().should('contain.text', blogMetaTitle);
            repoBlogForm.getSerpPreviewContainer().should('contain.text', blogMetaDescription);
        });
    });

});
