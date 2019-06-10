import { Component, Mixin } from 'src/core/shopware';
import Criteria from 'src/core/data-new/criteria.data';
import template from './sas-blog-detail.html.twig';

Component.register('sas-blog-detail', {
    template,

    inject: ['repositoryFactory', 'context'],

    beforeRouteLeave(to, from, next) {
        this.blogEditMode = false;
        next();
    },

    data() {
        return {
            isLoading: false,
            blog: null,
            blogId: null,
            blogEditMode: false,
            languages: [],
            language: {},
            isSaveSuccessful: false
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.blog.title)
        };
    },

    created() {
        this.createdComponent();
    },

    watch: {
        '$route.params.id'() {
            this.createdComponent();
        }
    },

    computed: {
        blogRepository() {
            return this.repositoryFactory.create('sas_blog_entries');
        },
        createMode() {
            return this.$route.name.includes('create');
        }
    },

    methods: {
        createdComponent() {
            this.isLoading = true;
            if (this.$route.params.id) {
                this.blogId = this.$route.params.id;

                if (!this.createMode) {
                    this.blogRepository.get(this.blogId, this.context).then(blog => {
                        this.blog = blog;
                    });
                }
            }
        }
    }
});
