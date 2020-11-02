import template from './sas-editorjs.html.twig';

const { Component, Mixin } = Shopware;
const utils = Shopware.Utils;

import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";
import List from "@editorjs/list";
import Marker from "@editorjs/marker";
import Paragraph from "@editorjs/paragraph";
import Warning from "@editorjs/warning";
import Table from "@editorjs/table";
import Quote from "@editorjs/quote";
import Embed from '@editorjs/embed'
import SimpleImage from '@editorjs/simple-image';
import Delimiter from '@editorjs/delimiter';
import RawTool from '@editorjs/raw';
import InlineCode from '@editorjs/inline-code';

Component.register('sas-editorjs', {
    template,

    mixins: [
        Mixin.getByName('sw-form-field'),
        Mixin.getByName('remove-api-error')
    ],

    props: {
        value: {
            type: String,
            required: false,
            default: ''
        },

        label: {
            type: String,
            required: false,
            default: ''
        },
    },

    data() {
        return {
            editor: {},
            editorId: utils.createId()
        };
    },

    methods: {

    }
});
