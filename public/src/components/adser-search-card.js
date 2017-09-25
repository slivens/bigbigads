import template from './adser-search-card.html'

angular.module('MetronicApp').directive('adserSearchCard', function() {
    return {
        restrict: 'E',
        template,
        replace: true
    }
})
