import template from './adser-search-card.html'

angular.module('MetronicApp').directive('adserSearchCard', ['$templateCache', function($templateCache) {
    $templateCache.put("adser-bookmark-popover.html", '<bookmark-popover card="$parent.card" />')
    return {
        restrict: 'E',
        template,
        replace: true
    }
}])
