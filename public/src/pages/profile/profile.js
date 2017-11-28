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
        /*
        * 是否需要显示验证邮箱的部分
        * 【显示条件】
        * 1）2017年8月31日之前的用户（自定义，以后需要修改）
        * 2）免费用户
        *
        * 【数据解释】
        * 1）profile.user.created_at 可能值为 2017-09-25 19:28:44
        * 2）monent('xxxx-xx-xx').isBefore('xxxx-xx-xx') 可能值为 true 或则 false
        */
        profile.isShowValidate = false
        if ($scope.user.role.name == 'Free' && moment($scope.user.created_at.split(' ')[0]).isBefore('2017-07-31')) {
            profile.isShowValidate = true
            // 打开验证邮箱的编辑框
            $scope.profile.openToggle('openSubscriptionEdit', true)
        }
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
        profile.resendTime = User.info.user.retryTime
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
    profile.resendTimeMess = 'Submit'

    profile.sendActivationEmail = function() {
        if (!profile.subscriptionEmail) return
        if (User.info.user.retryTime === 0) {
            SweetAlert.swal({
                title: 'Sorry',
                text: 'Retry time has been exhausted today',
                type: 'error'
            })
            return
        }
        $http({
            method: 'POST',
            url: `/users/send-email`,
            data: {
                'user_email': User.info.user.email,
                'subscription_email': profile.subscriptionEmail
            }
        }).then(function(res) {
            /*
            * 返回发送结果
            * 1）res.data.time 发送成功
            * 2）res.data.error 邮箱已经被别人验证
            * 3）res.data.code == '-401' 发送邮件次数超过限制
            * 4) 其他未知状况，比如服务器错误
            */
            if (res.data.time) {
                SweetAlert.swal({
                    title: 'The verification email has been sent.',
                    type: 'success'
                })
                // 收起下拉框
                $scope.profile.openToggle('openSubscriptionEdit', false)
            } else if (res.data.error) {
                SweetAlert.swal({
                    title: 'Sorry',
                    text: 'The email you submited has already been verified.',
                    type: 'error'
                })
            } else if (res.data.code == '-401') {
                SweetAlert.swal({
                    title: 'Sorry',
                    text: 'Retry time has been exhausted today.',
                    type: 'error'
                })
            } else {
                SweetAlert.swal({
                    title: 'Sorry',
                    text: 'An error occurred. Please try again later!',
                    type: 'error'
                })
            }
        })
        // 计时器, 60秒间隔后才能再点击
        profile.second = 60
        var timePromise
        timePromise = $interval(function() {
            if (profile.second === -1) profile.resendTimeMess = "Submit"
            if (profile.second < 0) {
                $interval.cancel(timePromise)
                timePromise = undefined
                profile.second = 60
                profile.clickEnable = false
            } else if (profile.second >= 0) {
                profile.resendTimeMess = profile.second + " seconds"
                profile.second--
                profile.clickEnable = true
            }
        }, 1000)
        User.getInfo()
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
