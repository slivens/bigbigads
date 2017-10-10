import './upgrade-dlg.scss'
import template from './upgrade-dlg.html'

const controller = function($scope, $uibModalInstance, $state, $location) {
    $scope.currIlleageOption = $scope.$resolve.data.currIlleageOption
    $scope.goIndex = function() {
        window.open('/home', "_self")
        $uibModalInstance.dismiss('success')
    }
    $scope.goPlans = function() {
        $state.go("plans")
        $uibModalInstance.dismiss('success')
    }
    $scope.clearAll = function() {
        $location.search({})
        $state.reload()
        $uibModalInstance.dismiss('cancle')
    }
}

controller.$inject = ['$scope', '$uibModalInstance', '$state', '$location']

export {template, controller}
