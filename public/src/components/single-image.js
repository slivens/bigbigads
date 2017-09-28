import template from './single-image.html'

angular.module('MetronicApp').directive('singleImage', ['TIMESTAMP', function(TIMESTAMP) {
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
