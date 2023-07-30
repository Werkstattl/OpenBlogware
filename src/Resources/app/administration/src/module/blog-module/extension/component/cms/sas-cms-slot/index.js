import template from './sas-cms-slot.html.twig';
import './sas-cms-slot.scss';

const { Component } = Shopware;

Component.extend('sas-cms-slot', 'sw-cms-slot', {
    template,
});
