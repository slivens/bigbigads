import template from './single-video.html'

angular.module('MetronicApp').directive('singleVideo', ['TIMESTAMP', function(TIMESTAMP) {
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
