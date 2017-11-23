import '../../pages/common/common'
import './changepwd.scss'
import template from './changepwd.html'

angular.module('MetronicApp').controller('changepwdController', ['$scope', '$http', 'User', 'SweetAlert', 'settings', function($scope, $http, User, SweetAlert, settings) {
    var info = {
        oldpwd: null,
        newpwd: null,
        repeatpwd: null
    }
    var url = settings.remoteurl + '/changepwd'
    $scope.isbusy = false
    $scope.info = info
    var ctrl = this
    $scope.save = function(item) {
        var pwdForm = this.pwdForm
        console.log(pwdForm)
        $scope.isbusy = true
        if (info.newpwd != info.repeatpwd) {
            info.error = 'repeat password is diffrent with new password'
            return
        }
        $scope.promise = $http.post(url, info)
        $scope.promise.then(function(res) {
            $scope.isbusy = false
            var data = res.data
            if (data.code == -1) {
                info.error = 'Invalid PassWord'
                info.oldpwd = ''
            } else {
                // 修改成功后，清楚填写的记录，并重置校验，然后关闭窗口
                if (data.code == 0) {
                    info.oldpwd = ''
                    info.newpwd = ''
                    info.repeatpwd = ''
                    // 重置校验
                    pwdForm.$setPristine()
                    ctrl.toggle('openPwdEdit', false)
                }
                SweetAlert.swal({
                    title: data.code == 0 ? 'Success' : 'Error',
                    text: data.desc,
                    type: data.code == 0 ? 'success' : 'error'
                })
            }
        }, function(res) {
            SweetAlert.swal({
                title: 'Error',
                text: res.statusText,
                type: 'error'
            })
        })
    }
}])
    .component('changepwd', {
        template,
        bindings: {
            toggle: '<'
        },
        controller: 'changepwdController'
    })
