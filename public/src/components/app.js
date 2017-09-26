import './header.js'
import './ng-spinner-bar.js'
import template from './app.html'

/* Setup App Main Controller */
angular.module('bba.app', ['ui.router', 'bba.header', 'bba.ng-spinner-bar'])
    .controller('AppController', function() {
    })
    .component('app', {
        template,
        controller: 'AppController'
    })
