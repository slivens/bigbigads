// bba的核心内容，常量与服务为主，注意不要出现任何跟界面相关的内容
// @warning 迁移过程中保留了一些遗留代码，最终这些代码都会被移除；
// 比如对component的引用，以及$uibModal
import {template as upgradeDlgTemplate, controller as upgradeDlgController} from '../components/upgrade-dlg.js'
import {template as signTemplate, controller as signController} from '../components/sign.js'
import {template as searchResultUpgradeDlgTemplate, controller as searchResultUpgradeDlgController} from '../components/search-result-upgrade-dlg.js'
import {template as filterDataLimitDlgTemplate, controller as filterDataLimitDlgController} from '../components/filter-data-limit-dlg.js'
// import {template as pushNotificationDlgTemplate, controller as pushNotificationDlgController} from '../components/push-notification.js'

/* Setup global settings */
const module = angular.module('bba.core', ['ui.bootstrap', 'ngSanitize'])
module.constant('TIMESTAMP', Date.parse(new Date()))
module.constant('ADS_TYPE', {
    timeline: 1,
    rightcolumn: 4,
    mobile: 2,
    phone: 2
})
module.constant('ADS_CONT_TYPE', {
    SINGLE_IMAGE: "SingleImage",
    CANVAS: "Canvas",
    CAROUSEL: "Carousel",
    SINGLE_VIDEO: "SingleVideo"
})
module.constant('POLICY_TYPE', {PERMANENT: 0, MONTH: 1, DAY: 2, HOUR: 3, VALUE: 4, DURATION: 5, YEAR: 6})

// User中，关于$uibModal的相关操作，应该剥离出去
module.factory('User', ['$window', '$http', '$q', '$location', '$rootScope', 'settings', 'ADS_TYPE', '$uibModal', function($window, $http, $q, $location, $rootScope, settings, ADS_TYPE, $uibModal) {
    // 获取信息完成后应该广播消息，然后其他需要在获取用户信息才能继续的操作就放到接收到广播后处理
    var infourl = settings.remoteurl + "/userinfo"
    var user = {
        retreived: false,
        done: false,
        info: {},
        getInfo: function(refresh) {
            if (!refresh && user.retreived) return user.promise

            user.promise = $http.get(infourl)
            user.promise.then(function(res) {
                // Issue #10 User 获取用户信息时，与 localStorage 比对，发现不一致就更新
                if (JSON.stringify(res.data) != $window.localStorage.user) {
                    user.info = res.data

                    // Issue #10 更新会话存储的用户信息
                    $window.localStorage.user = JSON.stringify(user.info)

                    angular.extend(user, user.info)
                }
            }, function(res) {
                // user.info = {};
            }).finally(function() {
                user.done = true
                $rootScope.$broadcast('userChanged', user.info)
            })
            user.retreived = true
            return user.promise
        },
        // goLoginPage:function() {
        //     $location.url("/login");
        // },
        can: function(key) {
            var keyArr = key.split('|')
            var i
            // 无登陆时的策略与有登陆需要做策略区分(只在服务器端区分是更好的做法)
            if (!user.info.permissions)
                return false
            for (i = 0; i < keyArr.length; ++i)
                if (!user.info.permissions[keyArr[i]])
                    return false
            return true
        },
        usable: function(key, val) {
            // 是否满足策略要求
            var policy = user.getPolicy(key)
            var type
            if (typeof (policy) == 'boolean')
                return policy
            // 根据不同情况返回不同的权限值
            if (key == "platform") {
                if (!val)
                    return true
                type = ADS_TYPE[val]
                // console.log("policy:", policy.value, type, val);
                if ((Number(policy.value) & type) > 0)
                    return true
                return false
            }
            if (policy.used < policy.value)
                return true
            return false
        },
        getPolicy: function(key) {
            var usage
            if (!user.can(key)) // 没有权限一定没有策略
                return false
            if (!user.info.user.usage[key])// 没有策略不需要策略，组合权限不支持策略，所以也返回true
                return true
            usage = user.info.user.usage[key]
            if (usage.length > 2)
                return {type: usage[0], value: usage[1], used: usage[2]}
            return {type: usage[0], value: usage[1], used: 0}
        },
        openUpgrade: function(currIlleageOption) {
            return $uibModal.open({
                template: upgradeDlgTemplate,
                size: 'md',
                animation: true,
                controller: upgradeDlgController,
                resolve: {
                    data: function() {
                        return {
                            currIlleageOption: currIlleageOption
                        }
                    }
                }
            })
        },
        openSign: function() {
            return $uibModal.open({
                template: signTemplate,
                size: 'customer',
                backdrop: false,
                animation: true,
                controller: signController
            })
        },
        openSearchResultUpgrade: function() {
            return $uibModal.open({
                template: searchResultUpgradeDlgTemplate,
                size: 'md',
                animation: true,
                controller: searchResultUpgradeDlgController
            })
        },
        openFreeDateLimit: function() {
            return $uibModal.open({
                template: filterDataLimitDlgTemplate,
                size: 'md',
                animation: true,
                controller: filterDataLimitDlgController
            })
        }
    }

    // Issue #10 User factory 初始化时，从 localStorage 获取用户信息，获取得到就设置 done 和 retreived
    if ($window.localStorage.user) {
        user.info = JSON.parse($window.localStorage.user)

        angular.extend(user, user.info)

        user.done = true
        user.retreived = true

        var defer = $q.defer()
        user.promise = defer.promise
        defer.resolve()
    }

    return user
}])
export default module
