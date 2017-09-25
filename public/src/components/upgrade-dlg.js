import template from './upgrade-dlg.html'

const controller = function($scope, $uibModalInstance, $state) {
    $scope.goIndex = function() {
        window.open('/home', "_self")
        $uibModalInstance.dismiss('success')
    }
    $scope.goPlans = function() {
        $state.go("plans")
        $uibModalInstance.dismiss('success')
    }
}

controller.$inject = ['$scope', '$uibModalInstance', '$state']

export {template, controller}
