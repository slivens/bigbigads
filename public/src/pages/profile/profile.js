import './profile.scss'
import '../common/common'
import '../../components/billings'
import '../../components/subscription'
import template from './profile.html'
import changePwdTemplate from './changepwd.html'

export default angular.module('profile', ['MetronicApp']).controller('ProfileController', ['$scope', '$location', 'User', '$uibModal', 'TIMESTAMP', '$http', 'SweetAlert', function($scope, $location, User, $uibModal, TIMESTAMP, $http, SweetAlert) {
    let profile = {
        isFirstInit: false,
        init() {
            var search = $location.search()
            if (search.active && search.active != this.active) {
                this.active = Number(search.active)
            }
            if (!this.isFirstInit) {
                this.isFirstInit = true
                $http({
                    method: 'GET',
                    url: `/users/customize_invoice`
                }).then((res) => {
                    profile.companyName = res.data.company_name
                    profile.address = res.data.address
                    profile.contactInfo = res.data.contact_info
                    profile.website = res.data.website
                    profile.taxNo = res.data.tax_no
                })
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
    $scope.change = function(type) {
        if (type == 'name') {
            $http({
                method: 'patch',
                url: `/users/change_profile`,
                data: {
                    'name': User.info.user.name
                }
            }).then(function(res) {
                SweetAlert.swal({
                    title: res.data.code == 0 ? 'Success' : 'Error',
                    text: res.data.desc,
                    type: res.data.code == 0 ? 'success' : 'error'
                })
            })
        } else {
            $uibModal.open({
                template: changePwdTemplate,
                size: 'md',
                animation: true,
                controller: 'ChangepwdController'
            })
        }
    }
    /* $scope.changePwd = function() {
        return $uibModal.open({
            template: changePwdTemplate,
            size: 'md',
            animation: true,
            controller: 'ChangepwdController'
        })
    } */
    $scope.userPromise = User.getInfo()
    $scope.userPromise.then(function() {
        var user = User.info.user
        // console.log(user.subscriptions);
        $scope.userInfo = User.info
        $scope.User = User
        $scope.user = user
    })
    profile.customizeSubmit = function() {
        if ((profile.companyName == null || profile.companyName == '') && (profile.address == null || profile.address == '') && (profile.contactInfo == null || profile.contactInfo == '') && (profile.website == null || profile.website == '') && (profile.taxNo == null || profile.taxNo == '')) {
            SweetAlert.swal({
                title: 'Are you sure?',
                text: 'This will be not saved anything.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "Confirm！",
                confirmButtonColor: "#DD6B55"
            }, function(isConfirm) {
                if (!isConfirm)
                    return
                profile.customizeRequest()
            })
        } else {
            profile.customizeRequest()
        }
    }
    profile.customizeRequest = function() {
        $http({
            method: 'POST',
            url: `/users/customize_invoice`,
            data: {
                'company_name': profile.companyName,
                'address': profile.address,
                'contact_info': profile.contactInfo,
                'website': profile.website,
                'tax_no': profile.taxNo
            }
        }).then(function(res) {
            if (res.data.code == 0) {
                SweetAlert.swal(
                    'Save Done!',
                    res.data.desc,
                    'success'
                )
            } else {
                SweetAlert.swal(
                    'Error',
                    res.data.desc,
                    'error'
                )
            }
        })
    }
}])
    .controller('ChangepwdController', ['$scope', '$uibModalInstance', '$http', 'settings', function($scope, $uibModalInstance, $http, settings) {
        var info = {
            oldpwd: null,
            newpwd: null,
            repeatpwd: null
        }
        var url = settings.remoteurl + '/changepwd'
        $scope.info = info
        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel')
        }
        $scope.save = function(item) {
            if (info.newpwd != info.repeatpwd) {
                info.error = 'repeat password is diffrent with new password'
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
