import template from './terms_of_service.html'
import './terms_of_service.scss'

const MODULE_NAME = 'bba.ui.terms'

const controller = function($scope, $uibModalInstance, User) {
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }
    $scope.User = User
}

controller.$inject = ['$scope', '$uibModalInstance', 'User']

const module = angular.module(MODULE_NAME, ['bba.core'])

// 服务协议弹出框体,如果点击确认，post一个路由，然后重置整个页面
module.factory('Terms', ['$uibModal', function($uibModal) {
    let terms = {
        open: function() {
            return $uibModal.open({
                template,
                size: 'xs',
                animation: true,
                controller: ['$scope', '$http', '$uibModalInstance', function($scope, $http, $uibModalInstance) {
                    $scope.enter = function() {
                        $uibModalInstance.dismiss("cancel")
                        $http.post('/service_term').then(function() {
                            window.location.reload()
                        })
                    }
                }]
            })
        }
    }
    return terms
}])

export { template, controller, MODULE_NAME }
export default module
