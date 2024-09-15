import './werkl-blog-author-detail.scss';
import template from './werkl-blog-author-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('werkl-blog-author-detail', {
    template,

    inject: [
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('salutation'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onCancel',
    },

    data() {
        return {
            isLoading: false,
            isSaveSuccessful: false,
            blogAuthor: null,
            blogAuthorCustomFieldSets: null,
            processSuccess: false,
            availableTags: null,
            fileAccept: 'image/*',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.blogAuthor !== null ? this.salutation(this.blogAuthor) : '';
        },

        blogAuthorRepository() {
            return this.repositoryFactory.create('werkl_blog_author');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        defaultCriteria() {
            const criteria = new Criteria();
            criteria
                .addAssociation('media')
                .addAssociation('salutation');

            return criteria;
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        customFieldSetCriteria() {
            const criteria = new Criteria();

            criteria
                .addFilter(Criteria.equals('relations.entityName', 'werkl_blog_author'));

            criteria.getAssociation('customFields')
                .addSorting(Criteria.sort('config.customFieldPosition'));

            return criteria;
        },

        ...mapPropertyErrors('blogAuthor', [
            'firstName',
            'lastName',
            'displayName',
            'email',
            'salutationId',
            'description',
        ]),
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.isLoading = true;

            this.blogAuthorRepository.get(
                this.$route.params.id,
                Shopware.Context.api,
                this.defaultCriteria,
            ).then((blogAuthor) => {
                this.blogAuthor = blogAuthor;
                this.isLoading = false;
            });
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        },

        async onSave() {
            this.isLoading = true;
            this.isSaveSuccessful = false;

            return this.blogAuthorRepository.save(this.blogAuthor, Shopware.Context.api).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
                this.createNotificationSuccess({
                    message: this.$tc('werkl-blog-author.detail.messageSaveSuccess', 0, {
                        name: `${this.blogAuthor.firstName} ${this.blogAuthor.lastName}`,
                    }),
                });
                this.$router.push({ name: 'blog.module.author.detail', params: { id: this.blogAuthor.id } });
            }).catch((exception) => {
                this.createNotificationError({
                    message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                });
                this.isLoading = false;
                throw exception;
            });
        },

        onCancel() {
            this.$router.push({ name: 'werkl_blog.author.index' });
        },

        onSetMediaItem({ targetId }) {
            this.mediaRepository.get(targetId, Shopware.Context.api).then((updatedMedia) => {
                this.blogAuthor.mediaId = targetId;
                this.blogAuthor.media = updatedMedia;
            });
        },

        onRemoveMediaItem() {
            this.blogAuthor.mediaId = null;
            this.blogAuthor.media = null;
        },

        onMediaDropped(dropItem) {
            this.onSetMediaItem({ targetId: dropItem.id });
        },
    },
});
