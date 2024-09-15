import blogDetailAction from '../../support/actions/admin/BlogDetailAction';
import repoCmsPage from '../../support/repositories/admin/general/CmsPageRepository';

const blogTitle = 'Blog Entry Title';

describe('Blog CMS: content with cms blocks', () => {
    beforeEach(() => {
        cy.authenticate()
            .then(() => {
                cy.viewport(1920, 1080);
                cy.openInitialPage(`${Cypress.env('admin')}#/blog/module/create`);
            });
    });

    it('@content: should not show werkl-blog block category', () => {

        blogDetailAction.openBlockSidebar();

        repoCmsPage.getBlockCategoryWerklBlogOption().should('not.exist');
    });

    it.only('@content: can drag block to stage', () => {
        blogDetailAction.openSidebar();

        blogDetailAction.fillBlogForm(blogTitle, true, true, true);

        blogDetailAction.addFullWidthCmsSection();

        blogDetailAction.setBlockCategory('Text');

        repoCmsPage.getCmsTextBlock().should('be.visible');

        repoCmsPage.getCmsTextBlock().dragTo(repoCmsPage.getEmptySectionSelector());

        repoCmsPage.getCmsBlockConfigOverlay().should('exist');
    });
});
