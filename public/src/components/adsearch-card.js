import template from './adsearch-card.html'

angular.module('MetronicApp').directive('adsearchCard', ['$templateCache', function($templateCache) {
    $templateCache.put("ad-bookmark-popover.html", '<bookmark-popover/>')
    return {
        restrict: 'E',
        template,
        replace: true
    }
}])
