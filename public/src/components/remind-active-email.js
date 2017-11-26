import template from './remind-active-email.html'
import './remind-active-email.scss'

const controller = function($scope, $uibModalInstance, $state) {
    $scope.goProfile = function() {
        $state.go('profile')
        $uibModalInstance.dismiss('success')
    }
}
controller.$inject = ['$scope', '$uibModalInstance', '$state']
export {template, controller}
