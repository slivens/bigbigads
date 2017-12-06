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

// 从良好的分层角度考虑，factory, service管逻辑，而不管任何关于UI显示的内容。
// 但是angular中，所有的依赖都需要通过注入完成，所以像弹出对话框这种涉及UI操作的功能，最终也必须以factory的形式导出才有办法让其他模块引用
module.factory('Terms', ['$uibModal', 'User', function($uibModal, User) {
    let terms = {
        open: function() {
            return $uibModal.open({
                template,
                size: 'xs',
                animation: true,
                controller: ['$scope', '$http', 'SweetAlert', '$uibModalInstance', '$location', function($scope, $http, SweetAlert, $uibModalInstance, $location) {
                    $scope.enter = function() {
                        SweetAlert.swal('您同意了条款')
                        $uibModalInstance.dismiss("cancel")
                        $http.get('/service_term/update_version').then(function() {
                            $location.search({})
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
