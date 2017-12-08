import template from './remind-active-email.html'
import './remind-active-email.scss'

const MODULE_NAME = 'bba.ui.active.email'

const controller = function($scope, $uibModalInstance, $state) {
    $scope.goProfile = function() {
        $state.go('profile')
        $uibModalInstance.dismiss('success')
    }
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }
}
controller.$inject = ['$scope', '$uibModalInstance', '$state']

const module = angular.module(MODULE_NAME, ['bba.core'])

module.factory('activeEmailReminder', ['$uibModal', 'User', '$window', function($uibModal, User, $window) {
    let activeEmailReminder = {
        open: function() {
            return $uibModal.open({
                template,
                size: 'md',
                animation: true,
                controller
            })
        },
        check: function() {
            /*
            * 返回false条件
            *  1）2017.07.31 之前注册的用户
            *  2）没有验证过的用户 User.info.user.is_check == 0
            *  3）用户等级是Free
            */
            if (moment(User.info.user.created_at).isBefore('2017-07-31', 'day') && !User.info.user.is_check && User.info.user.role.name == 'Free')
                return false
            return true
        }
    }
    return activeEmailReminder
}])

export { template, controller, MODULE_NAME }
export default module
