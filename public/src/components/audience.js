import template from './audience.html'

angular.module('MetronicApp').directive('audience', ['$uibModal', 'TIMESTAMP', function($uibModal, TIMESTAMP) {
    return {
        link: function(scope, element, attrs) {
            element.bind('click', function() {
                if (attrs.title) {
                    var whySee = attrs.title.split("\n")
                    return $uibModal.open({
                        template,
                        size: 'customer',
                        animation: true,
                        controller: ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
                            $scope.whySee = whySee
                            $scope.audienceLength = whySee.length
                            $scope.close = function() {
                                $uibModalInstance.dismiss('cancle')
                            }
                        }]
                    })
                }
            })
        }
    }
}])
