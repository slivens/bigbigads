import '../common/common'
import '../../components/billings'
import '../../components/subscription'

angular.module('MetronicApp').controller('PlansController', ['$scope', 'Resource', 'User', function($scope, Resource, User) {
    var plans = new Resource('plans')
    plans.getPolicy = function(item, permissionKey, groupKey) {
        var group = item.groupPermissions[groupKey]
        var policy
        var i
        var finded = false
        if (!group)
            return false
        angular.forEach(group, function(groupItem) {
            if (groupItem.key == permissionKey) {
                finded = true
            }
        })
        if (!finded)
            return false
        for (i = 0; i < item.policies.length; ++i) {
            policy = item.policies[i]
            if (policy.key == permissionKey) {
                return {value: policy.pivot.value, type: policy.type}
            }
        }

        return true
    }
    $scope.showPlanItem = function() {
        $scope.isTurn = true
    }
    $scope.hiddenPlanItem = function() {
        $scope.isTurn = false
    }

    plans.goPlanID = function(item) {
        var id
        if (!item.plans)
            return ""

        if (plans.annually) {
            id = item.plans.annually.id
        } else {
            id = item.plans.monthly.id
        }
        // console.log(plans.annually, id);
        window.open("/pay?plan=" + id)
    }
    plans.isCurrentPlan = function(item) {
        /* if (!User.user.subscription)
            return false; */
        var planName
        var rolePlan
        var isCurrentPlan
        planName = item.plans.monthly.name
        rolePlan = User.user.role.plan
        isCurrentPlan = new RegExp(rolePlan).test(planName)
        return isCurrentPlan
    }

    plans.annually = false

    $scope.queryPromise = plans.get()
    $scope.queryPromise.then(function(items) {
        if (items.length > 0) {
            $scope.groupPermissions = items[items.length - 1].groupPermissions
        }
    })
    $scope.plans = plans
    $scope.groupPermissions = []
    User.getInfo().then(function() {
        $scope.userInfo = User.info
        if (User.info.login) {
            $scope.subscription = User.info.subscription
            $scope.userPlan = User.info.user.role.plan
        }
    })
}])
angular.module('MetronicApp').controller('ProfileController', ['$scope', '$location', 'User', '$uibModal', 'TIMESTAMP', function($scope, $location, User, $uibModal, TIMESTAMP) {
    // var vm = this
    var profile = {
        init: function() {
            var search = $location.search()
            if (search.active && search.active != this.active) {
                this.active = Number(search.active)
            }
            if (search.pay && search.pay == 'success') {
                /* eslint-disable */
                // 七天内 支付成功的谷歌统计,暂时忽略刷新造成的统计干扰
                window.google_conversion_id = 850659212; // 该处不加;号会导致谷歌统计代码出错无效(Tag Assistant检测)
                window.google_conversion_language = "en"
                window.google_conversion_format = "3"
                window.google_conversion_color = "ffffff"
                window.google_conversion_label = "z7gMCKGZznQQjI_QlQM"
                window.google_remarketing_only = false
    
                var script = document.createElement("script")
                script.type = "text/javascript"
                script.src = "//www.googleadservices.com/pagead/conversion.js"
                document.getElementsByTagName("head")[0].appendChild(script)
                // 七天内 支付成功的必应统计
                ;(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"5713181"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq")
                window.uetq = window.uetq || []
                window.uetq.push({'ec': 'conversion', 'ea': 'pay_scccessed', 'el': 'pay', 'ev': 60})
                // 添加谷歌七日内付款成功统计事件,临时方案，后续会按要求以guzzle同步请求实现
                ga('send', 'event', 'conversion', 'payed', 'pay_standard');
                /* eslint-enable */
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
            templateUrl: 'views/profile/changepwd.html?t=' + TIMESTAMP,
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
            // console.log($scope.info)
            // $scope.promise = bookmark.save(item);
            // $scope.promise.then(function() {
            //     $uibModalInstance.dismiss('success');
            // });
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
