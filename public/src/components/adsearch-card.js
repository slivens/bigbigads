import template from './adsearch-card.html'

angular.module('MetronicApp').directive('adsearchCard', function() {
    return {
        restrict: 'E',
        template,
        replace: true
    }
})
