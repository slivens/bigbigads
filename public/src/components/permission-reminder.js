import template from './permission-reminder.html'
import './permission-reminder.scss'

const MODULE_NAME = 'bba.ui.reminder'

const controller = function($scope, $uibModalInstance, User) {
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }
    $scope.User = User
    $scope.filterArr = [{
        'class': 'fa-calendar', // 图标样式
        'name': 'first_time_filter', // filter
        'value': 'First See Time Frame' // filter的name
    }, {
        'class': 'fa-calendar',
        'name': 'last_time_filter',
        'value': 'Last See Time Frame'
    }, {
        'class': 'fa-picture-o',
        'name': 'format_filter',
        'value': 'Ad Type'
    }, {
        'class': 'fa-flag',
        'name': 'country_filter',
        'value': 'Country or Region'
    }, {
        'class': 'fa-inbox',
        'name': 'objective_filter',
        'value': 'Objective'
    }, {
        'class': 'fa-external-link-square',
        'name': 'e_commerce_filter',
        'value': 'Eshop'
    }, {
        'class': 'fa-user-secret',
        'name': 'audience_age_filter',
        'value': 'Audience Age'
    }, {
        'class': 'fa-leaf',
        'name': 'audience_gender_filter',
        'value': 'Audience Gender'
    }, {
        'class': 'fa-flag',
        'name': 'audience_interest_filter',
        'value': 'Audience Interest'
    }, {
        'class': 'fa-line-chart',
        'name': 'duration_filter',
        'value': 'Customized Ad Run duration'
    }, {
        'class': 'fa-eye',
        'name': 'see_times_filter',
        'value': 'Customized Ad Run See Times'
    }, {
        'class': 'fa-pie-chart',
        'name': 'advance_likes_filter',
        'value': 'Customized Engagements'
    }]
}

controller.$inject = ['$scope', '$uibModalInstance', 'User']

const module = angular.module(MODULE_NAME, ['bba.core'])

// 从良好的分层角度考虑，factory, service管逻辑，而不管任何关于UI显示的内容。
// 但是angular中，所有的依赖都需要通过注入完成，所以像弹出对话框这种涉及UI操作的功能，最终也必须以factory的形式导出才有办法让其他模块引用
module.factory('Reminder', ['$uibModal', 'User', '$window', function($uibModal, User, $window) {
    let reminder = {
        open: function() {
            return $uibModal.open({
                template,
                size: 'md',
                animation: true,
                controller
            })
        },
        check: function() {
            // 检查是规定时间内否在推送用户通知
            if (!User.login) return false
            if (User.info.user.role.plan != 'free') return false
            var permissionReminder
            var now = moment().format('YYYY-MM-DD')
            // localStorage内不存在通知信息或过期,重置通知信息
            if (!$window.localStorage.permissionReminder || moment(JSON.parse($window.localStorage.permissionReminder).expired).isBefore(now)) {
                $window.localStorage.setItem('permissionReminder', JSON.stringify({"isPush": false, "expired": now}))
            }
            permissionReminder = JSON.parse($window.localStorage.permissionReminder)
            // localStorage内通知信息status为未推送且在今天之内,表明今天未推送消息
            if (!permissionReminder.isPush && moment(permissionReminder.expired).isSame(now)) {
                $window.localStorage.setItem('permissionReminder', JSON.stringify({"isPush": true, "expired": permissionReminder.expired}))
                return true
            } else {
                return false
            }
        }
    }
    return reminder
}])

export { template, controller, MODULE_NAME }
export default module
