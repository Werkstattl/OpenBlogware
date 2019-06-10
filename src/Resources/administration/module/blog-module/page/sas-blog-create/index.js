import { Component, Mixin } from 'src/core/shopware';
import template from './sas-blog-create.html.twig';

import slugify from 'slugify';

Component.register('sas-blog-create', {
    template,

    inject: ['repositoryFactory', 'context'],

    mixins: [Mixin.getByName('notification')],

    data() {
        return {
            blog: {
                title: 'Undefined',
                slug: 'undefined',
                teaser: null,
                content: null,
                active: 0
            },
            isLoading: false,
            repository: null,
            processSuccess: false
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('sas_blog_entries');
        this.getBlog();
    },

    watch: {
        'blog.active': function(value) {
            return this.blog.active ? 1 : 0;
        },
        'blog.title': function(value) {
            console.log(value);
            if (value != 'undefined') {
                this.blog.slug = slugify(value, {
                    lower: true
                });
            }
        }
    },

    methods: {
        getBlog() {
            this.blog = this.repository.create(this.context);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.blog, this.context)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'blog.module.index' });
                })
                .catch(exception => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: 'TODO // ERROR',
                        message: exception
                    });
                });
        }
    }
});
