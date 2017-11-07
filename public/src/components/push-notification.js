import template from './push-notification.html'

const controller = function($scope, $uibModalInstance, User) {
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }
    $scope.User = User
}

controller.$inject = ['$scope', '$uibModalInstance', 'User']

export {template, controller}
