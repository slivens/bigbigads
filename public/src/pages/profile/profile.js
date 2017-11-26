import './profile.scss'
import '../common/common'
import '../../components/billings'
import '../../components/subscription'
import './changepwd'
import template from './profile.html'

export default angular.module('profile', ['MetronicApp']).controller('ProfileController', ['$scope', '$location', 'User', '$uibModal', 'TIMESTAMP', '$http', 'SweetAlert', '$interval', function($scope, $location, User, $uibModal, TIMESTAMP, $http, SweetAlert, $interval) {
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
        profile.init()
    })
    $scope.userPromise = User.getInfo()
    $scope.userPromise.then(function() {
        var user = User.info.user
        // console.log(user.subscriptions);
        $scope.userInfo = User.info
        $scope.User = User
        $scope.user = user
    })
    /*
    * 用户名等编辑框的开关
    * 例: item: openNameEdit, bool: false
    * openNameEdit 用于判断ng-class
    */
    profile.openToggle = function(itme, bool) {
        $scope.profile[itme] = bool
    }

    /*
    * 修改用户名
    *
    * 提交条件
    * 1）有发生修改，利用 $dirty判断
    * 2）数据不得为空，且小于64个字符
    * 3）与改之前的数据不一致
    *
    * 修改成功后
    * 1）重置form验证
    * 2）sweetalert弹窗提示
    * 3）关闭编辑框
    * 4) 同步修改userinfo
    *
    * 失败后：
    * 1）重置校验
    * 2）弹窗提示
    */
    profile.changeName = function() {
        let username = profile.username
        let nameForm = profile.nameForm
        if (username && username.length <= 64) {
            $http({
                method: 'patch',
                url: `/users/change_profile`,
                data: {
                    'name': username
                }
            }).then(function(res) {
                SweetAlert.swal({
                    title: res.data.code == 0 ? 'Success' : 'Error',
                    text: res.data.desc,
                    type: res.data.code == 0 ? 'success' : 'error'
                })

                if (res.data && res.data.code == 0) {
                    // 关闭编辑框
                    $scope.profile.openToggle('openNameEdit', false)
                    // 同时修改userinfo中的user.name
                    $scope.user.name = username
                }
            }).catch(function(res) {
                SweetAlert.swal({
                    title: 'Sorry',
                    text: 'Sorry for the mistake, please try again later!',
                    type: 'error'
                })
            })
        }
        // 重置校验
        nameForm.$setPristine()
    }

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
                profile.openToggle('openInvoiceEdit', false)
            } else {
                SweetAlert.swal(
                    'Error',
                    res.data.desc,
                    'error'
                )
            }
        })
        // 重置校验
        profile.invoiceForm.$setPristine()
    }
    profile.subscriptionEmail = User.info.user.email
    profile.sendActivationEmail = function() {
        if (!profile.subscriptionEmail) return
        $http({
            method: 'POST',
            url: `/users/send-email`,
            data: {
                'user_email': User.info.user.email,
                'subscription_email': profile.subscriptionEmail
            }
        }).then(function(res) {
            if (res.data.time) {
                profile.resendTime = res.data.time
            } else {
                profile.resendTime = -1
            }
        })
        // 计时器, 60秒间隔后才能再点击
        profile.second = 60
        var timePromise
        timePromise = $interval(function() {
            if (profile.second <= 0) {
                $interval.cancel(timePromise)
                timePromise = undefined
                profile.second = 60
                if (profile.resendTime > 0) {
                    profile.resendTimeMess = `only ${profile.resendTime} chance to resend`
                } else {
                    profile.resendTimeMess = `no chance to resend today`
                }
                profile.clickEnable = false
            } else {
                profile.resendTimeMess = profile.second + " seconds later try again"
                profile.second--
                profile.clickEnable = true
            }
        }, 1000, 100)
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
