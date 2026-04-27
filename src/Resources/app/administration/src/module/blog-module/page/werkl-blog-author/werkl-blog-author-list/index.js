import template from './werkl-blog-author-list.html.twig';
import './werkl-blog-author-list.scss';

const { Context, Mixin } = Shopware;

const Criteria = Shopware.Data.Criteria;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('salutation'),
        Mixin.getByName('listing'),
        Mixin.getByName('version-compare'),
    ],

    data() {
        return {
            blogAuthors: null,
            total: 0,
            isLoading: true,
            currentLanguageId: Context.api.languageId,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    created() {
        this.getList();
    },

    computed: {
        blogAuthorRepository() {
            return this.repositoryFactory.create('werkl_blog_author');
        },

        columns() {
            return [
                {
                    property: 'salutation.displayName',
                    label: 'werkl-blog-author.list.table.salutation',
                    width: '100px',
                    allowResize: true,
                }, {
                    property: 'fullName',
                    dataIndex: 'firstName,lastName',
                    inlineEdit: 'string',
                    label: 'werkl-blog-author.list.table.fullName',
                    routerLink: 'sw.blog.author.detail',
                    allowResize: true,
                    primary: true,
                }, {
                    property: 'displayName',
                    label: 'werkl-blog-author.list.table.displayName',
                    allowResize: true,
                    inlineEdit: 'string',
                }, {
                    property: 'email',
                    label: 'werkl-blog-author.list.table.email',
                    align: 'right',
                    inlineEdit: 'string',
                    allowResize: true,
                },
            ];
        },
    },

    methods: {
        changeLanguage(newLanguageId) {
            this.currentLanguageId = newLanguageId;
            this.getList();
        },

        getList() {
            this.isLoading = true;
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('media');
            criteria.addAssociation('salutation');

            return this.blogAuthorRepository.search(criteria).then((result) => {
                this.total = result.total;
                this.blogAuthors = result;
                this.isLoading = false;
            });
        },

        updateTotal({ total }) {
            this.total = total;
        },
    },
};
