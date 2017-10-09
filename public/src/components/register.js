import template from './register.html'
import './register.scss'

angular.module('MetronicApp').controller('RegisterController', ['$scope', '$http', '$window', 'User', function($scope, $http, $window, User) {
    $scope.User = User
    $scope.formData = {email: '', name: '', password: ''}
    $scope.registerError = {}
    $scope.isShow = false
    $scope.logout = function() {
        // 根据intercom的文档，用户退出应使用shutdown方法关闭本次会话
        Intercom('shutdown')
        window.open('/logout', "_self")
    }
    $scope.checkEmail = function() {
        $scope.showHotmailMessage = false
        var emails = ['hotmail.com', 'live.com', 'outlook.com']
        angular.forEach(emails, function(item) {
            if ($scope.formData.email && $scope.formData.email.split('@')[1] === item) {
                $scope.showHotmailMessage = true
            }
        })
    }
    /*
    * 默认的用户名
    * 当用户名为空时，会自动填充为邮箱的前半段
    */
    $scope.defaultName = function() {
        if ($scope.formData.email && !$scope.formData.name) {
            $scope.formData.name = $scope.formData.email.split('@')[0]
        }
    }
    $scope.processForm = function(isValid) {
        $scope.isShow = true
        if ($scope.formData.name == ' ') { $scope.formData.name = $scope.formData.email.split('@')[0] }
        if ($window.localStorage.getItem("track")) {
            var track = JSON.parse($window.localStorage.track)
            var expired = track.expired
            if (moment().isBefore(expired)) {
                $scope.formData.track = track.code
            } else {
                $window.localStorage.removeItem("track")
            }
        }
        if (isValid) {
            $http({
                method: 'POST',
                url: '../register',
                data: $scope.formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' } // server need to know this is a ajax.
            })
                .then(
                    function successCallback(response) {
                    // location.href = response.data.url;
                        location.href = "/welcome?source=email"
                    },
                    function errorCallback(response) {
                        $scope.isShow = false
                        $scope.registerError = response.data
                    }
                )
        }
    }
    /* 弹窗中的点击事件 */
    $scope.shortcutReg = true // 初始化
    $scope.useEmailReg = false // 初始化
    $scope.turnToEmail = function() {
        $scope.shortcutReg = false
        $scope.useEmailReg = true
    }
    $scope.turnToShotcut = function() {
        $scope.shortcutReg = true
        $scope.useEmailReg = false
    }
}])
    .directive('register', function() {
        return {
            restrict: 'EA',
            template,
            replace: false,
            scope: {},
            controller: 'RegisterController'
        }
    })
