import template from './filter-data-limit-dlg.html'

const controller = function($scope, $uibModalInstance, $state) {
    $scope.goPlans = function() {
        $state.go("plans")
        $uibModalInstance.dismiss('success')
    }
    $scope.goIndex = function() {
        window.open('/home', "_self")
        $uibModalInstance.dismiss('success')
    }
}

controller.$inject = ['$scope', '$uibModalInstance', '$state']

export {template, controller}
