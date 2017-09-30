import template from './adcanvas.html'

angular.module('MetronicApp').directive('adcanvas', ['TIMESTAMP', function(TIMESTAMP) {
    return {
        restrict: 'E',
        template,
        replace: false,
        scope: {
            card: '='
        },
        controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
            $scope.settings = settings
            $scope.Searcher = Searcher
        }]
    }
}])
