import './profile.scss'
import '../common/common'
import '../../components/billings'
import '../../components/subscription'
import template from './profile.html'
import changePwdTemplate from './changepwd.html'

export default angular.module('profile', ['MetronicApp']).controller('ProfileController', ['$scope', '$location', 'User', '$uibModal', 'TIMESTAMP', function($scope, $location, User, $uibModal, TIMESTAMP) {
    // var vm = this
    var profile = {
        init: function() {
            var search = $location.search()
            if (search.active && search.active != this.active) {
                this.active = Number(search.active)
            }
        }
    }
    profile.init()
    $scope.profile = profile
    $scope.$watch('profile.active', function(newValue, oldValue) {
        if (newValue == oldValue)
            return
        $location.search('active', newValue)
    })
    $scope.$on('$locationChangeSuccess', function(ev) {
        // console.log($location.search());
        profile.init()
    })
    $scope.changePwd = function() {
        return $uibModal.open({
            template: changePwdTemplate,
            size: 'md',
            animation: true,
            controller: 'ChangepwdController'
        })
    }
    $scope.userPromise = User.getInfo()
    $scope.userPromise.then(function() {
        var user = User.info.user
        // console.log(user.subscriptions);
        $scope.userInfo = User.info
        $scope.User = User
        $scope.user = user
    })
}])
    .controller('ChangepwdController', ['$scope', '$uibModalInstance', '$http', 'settings', function($scope, $uibModalInstance, $http, settings) {
        var info = {
            oldpwd: null,
            newpwd: null,
            repeatpwd: null
        }
        var url = settings.remoteurl + "/changepwd"
        $scope.info = info
        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel')
        }
        $scope.save = function(item) {
            if (info.newpwd != info.repeatpwd) {
                info.error = "repeat password is diffrent with new password"
                return
            }
            $scope.promise = $http.post(url, info)
            $scope.promise.then(function(res) {
                var data = res.data
                if (Number(data.code) !== 0) {
                    info.error = data.desc
                    return
                }
                $uibModalInstance.dismiss('save')
            }, function(res) {
                info.error = res.statusText
            })
        }
    }])
    .directive('profile', function() {
        return {
            restrict: 'E',
            scope: {},
            template,
            replace: false,
            controller: 'ProfileController'
        }
    })
