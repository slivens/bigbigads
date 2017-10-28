import './header.scss'
import template from './header.html'
import './main-menu.js'

angular
    .module('bba.header', ['bba.settings', 'bba.main-menu'])
    .controller('HeaderController', ['settings', function(settings) {
        var vm = this
        vm.settings = settings
    }])
    .component('header', {
        template,
        controller: 'HeaderController'
    })
