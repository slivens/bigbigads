import template from './search-result-upgrade-dlg.html'

const controller = function($scope, $uibModalInstance, $state) {
    $scope.goPlans = function() {
        $state.go("plans")
        $uibModalInstance.dismiss('success')
    }
}
controller.$inject = ['$scope', '$uibModalInstance', '$state']
export {template, controller}
