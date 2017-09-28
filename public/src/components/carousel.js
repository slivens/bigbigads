import template from './carousel.html'

angular.module('MetronicApp').directive('carousel', ['TIMESTAMP', function(TIMESTAMP) {
    return {
        restrict: 'E',
        template,
        replace: false,
        scope: {
            card: '='
        },
        controller: ['$scope', 'settings', function($scope, settings) {
            $scope.settings = settings
        }]
    }
}])
