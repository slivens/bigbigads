import template from './adsearch-card.html'

angular.module('MetronicApp').directive('adsearchCard', ['$templateCache', function($templateCache) {
    // popover组件导致了强依赖，暂时无解
    $templateCache.put("ad-bookmark-popover.html", '<bookmark-popover card="$parent.card"/>')
    return {
        restrict: 'E',
        template,
        replace: true
    }
}])
