import template from './werkl-cms-slot.html.twig';
import './werkl-cms-slot.scss';

const { Component } = Shopware;

Component.extend('werkl-cms-slot', 'sw-cms-slot', {
    template,
});
