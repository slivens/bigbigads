import './header.js'
import './ng-spinner-bar.js'
import template from './app.html'
import tr from '../lib/intl.js'

/* Setup App Main Controller */
angular.module('bba.app', ['ui.router', 'bba.header', 'bba.ng-spinner-bar'])
    .controller('AppController', ['$state', '$rootScope', function($state, $rootScope) {
        let ctrl = this
        $rootScope.tr = tr
        $rootScope.$on('$stateChangeSuccess', () => {
            ctrl.serverRendered = true
        })

        ctrl.state = $state
    }])
    .component('app', {
        template,
        controller: 'AppController'
    })
