import template from './header.html'

angular
    .module('bba.header', ['bba.settings'])
    .controller('HeaderController', ['settings', function(settings) {
        var vm = this
        vm.settings = settings
    }])
    .component('header', {
        template,
        controller: 'HeaderController'
    })
