import 'jquery'
import 'font-awesome/css/font-awesome.css'
import 'simple-line-icons/css/simple-line-icons.css'
import 'bootstrap/dist/css/bootstrap.css'
import "bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css"
import 'sweetalert/dist/sweetalert.css'
import 'angular-busy/dist/angular-busy.min.css'
import swal from 'sweetalert'
import './styles/index.scss'
import './pages/common/settings.js'
import './components/app.js'
import './lib/core.js'
// import tr from './lib/intl.js'

window.moment = require('moment')
// 见 Issue #31 为了让babel的promise垫片可以工作，否则在IE下会提示Promise not defined错误
window.Promise = Promise

import('./lib/fuckadblock.js' /* webpackChunkName:"fuckadblock" */).catch(() => {
    swal({title: "Warning", text: "If you're not seeing any ads, it's possible you're running an Ad Blocking plugin on your browser. To view our ads, you'll need to disable it while you're here... ;-)", type: "warning"})
})
// checkAdblock()
/* Metronic App */
var MetronicApp = angular.module("MetronicApp", [
    "ui.router",
    "ui.bootstrap",
    "oc.lazyLoad",
    "ngSanitize",
    "oitozero.ngSweetAlert",
    'ngResource',
    'cgBusy',
    'bba.settings',
    'bba.core',
    'bba.app'
])

// MetronicApp.filter('nocache', ['TIMESTAMP', function(TIMESTAMP) {
//     return function(url) {
//         return url + '?t=' + TIMESTAMP
//     }
// }])
MetronicApp.filter('toHtml', ['$sce', function($sce) {
    return function(text) {
        return $sce.trustAsHtml(text)
    }
}])
    .filter('trusted', ['$sce', function($sce) {
        return function(url) {
            return $sce.trustAsResourceUrl(url)
        }
    }])
    .filter('clearHttps', function() {
        var link = ""
        return function(httpLink) {
            if (httpLink === null || httpLink === undefined || httpLink === '') return
            if (httpLink.indexOf("http") >= 0) {
                link = httpLink.replace(/http:\/\//, "")
            }
            if (httpLink.indexOf("https") >= 0) {
                link = httpLink.replace(/https:\/\//, "")
            }
            return link
        }
    })
    .filter('addUnit', function() {
        var unitNum = ''
        return function(num) {
            if (num === null || num === undefined || num === '') return
            num = Number(num)
            if (num < 1000) {
                return num
            }
            if (num >= 1000 && num < 1000000) {
                num = num / 1000
                unitNum = num.toFixed(1) + 'K'
                return unitNum
            }
            if (num >= 1000000) {
                num = num / 1000000
                unitNum = num.toFixed(1) + 'M'
                return unitNum
            }
        }
    })
    .filter('getImageSize', function() {
        // @param type 0表示宽度,1表示高度
        return function(url, type) {
            var size
            var pos = url.lastIndexOf('#')
            if (pos < 0)
                return 0
            size = url.slice(pos + 1).split('*')
            if (size.length != 2)
                return 0
            return size[type]
        }
    })
    .filter('mediaType', function() {
        return function(type) {
            var showType = ''
            switch (type) {
            case 'timeline': { showType = 'Newsfeed'; break }
            case 'rightcolumn': { showType = 'Right Column'; break }
            case 'phone': { showType = 'Mobile'; break }
            case 'suggested app': { showType = 'App'; break }
            default: break
            }
            return showType
        }
    })
    .filter('adsCount', function() {
        return function(adsNumber) {
            if (adsNumber === 0) return adsNumber
            if (!adsNumber) return
            var countString = ''
            var re = /(?=(?!\b)(\d{3})+$)/g
            adsNumber = String(adsNumber)
            countString = adsNumber.replace(re, ',')
            return countString
        }
    })
    .filter('noPrice', function() {
        return function(price) {
            if (price === 0) return price
            if (!price) return
            if (price >= 299) return '???'
            // 不使用 === 判断是因为年月份计费时有小数
            return price
        }
    })
    .filter('formatType', function() {
        return function(type) {
            var showType = ''
            var adsTypes = type.split(',')
            angular.forEach(adsTypes, function(item) {
                switch (item) {
                case 'Canvas': { showType += ' Others'; break }
                case 'SingleVideo': { showType += ' Video'; break }
                case 'SingleImage': { showType += ' Image'; break }
                case 'Carousel': { showType += ' Carousel'; break }
                default: break
                }
            })
            return showType
        }
    })
    .filter('adsTypes', function() {
        // 广告的类型show_way字段调整为从右到左 比特位分别表示 时间线 手机端 右边栏 安卓
        // 后续还会继续添加类型
        return function(showWay) {
            if (!showWay) return
            var binary = showWay.toString(2)
            var adsTypesNumber = binary.split("").reverse()
            var showString = ""
            var adsTypesString = ["News Feed", "Mobile", "Right Column"] /*, "Andorid" */
            var index
            // 广告可能同时出现在多个位置
            for (index = 0; index < adsTypesNumber.length; index++) {
                // Andorid 标示暂不显示
                if (adsTypesNumber[index] === "1" && index != 3) {
                    if (!showString) {
                        showString = adsTypesString[index]
                    } else {
                        showString = showString + ' & ' + adsTypesString[index]
                    }
                }
            }
            return showString
        }
    })
    .filter('SortTypes', ['settings', function(settings) {
        // Sort by 搜索项名称转换
        return function(sortItem) {
            if (!sortItem) return
            angular.forEach(settings.searchSetting.orderBy, function(item) {
                if (item.key === sortItem) {
                    sortItem = item.value
                }
            })
            return sortItem
        }
    }])
MetronicApp.factory('loader', ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
    return (name) => {
        let d = $q.defer()
            import(
                `./pages/${name}/${name}.js` /* webpackChunkName:`${name}` */
            ).then(m => {
                let module
                if (typeof (m.default) === 'function')
                    module = m.default(angular)
                else
                    module = m.default
                $ocLazyLoad.load({
                    name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                })
                d.resolve(module)
            })

            return d.promise
    }
}])

/* Setup Rounting For All Pages */
MetronicApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$urlMatcherFactoryProvider', function($stateProvider, $urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider) {
    // Redirect any unmatched url
    $urlMatcherFactoryProvider.strictMode(false)
    // $urlRouterProvider.when("/", "/adsearch");
    $urlRouterProvider.otherwise("/404")

    // TODO:resolve中的重复代码需要清理下
    $stateProvider
        .state('/', {
            url: '/',
            template: '<search />',
            data: {
                pageTitle: 'Advertise Search'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    import(
                        './pages/search/search.js' /* webpackChunkName:"search" */
                    ).then(m => {
                        let module = m.default(angular)
                        $ocLazyLoad.load({
                            name: 'search' // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }
        })
        .state('adsearch', {
            url: '/adsearch',
            template: '<search />',
            data: {
                pageTitle: 'Advertise Search'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    import(
                        './pages/search/search.js' /* webpackChunkName:"search" */
                    ).then(m => {
                        let module = m.default(angular)
                        $ocLazyLoad.load({
                            name: 'search'
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }
        })
        .state('adser', {
            url: '/adsearch/{adser}/{name}',
            template: '<owner />',
            data: {
                pageTitle: 'Advertiser\'s ads'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    import(
                        './pages/owner/owner.js' /* webpackChunkName:"owner" */
                    ).then(m => {
                        let module = m.default(angular)
                        $ocLazyLoad.load({
                            name: 'owner'
                        })
                        d.resolve(module)
                    })
                    return d.promise
                }]
            }

        })
        .state('ownerSearch', {
            url: '/ownerSearch',
            template: '<owner-search />',
            data: {
                pageTitle: 'Advertiser Search'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'owner-search'
                    import(
                        "./pages/owner-search/owner-search.js" /* webpackChunkName:"owner-search" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }
        })
        .state('adserAnalysis', {
            url: '/adserAnalysis/{username}',
            template: "<owner-analysis />",
            data: {
                pageTitle: 'Advertiser Analysis'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'owner-analysis'
                    import(
                        "./pages/owner-analysis/owner-analysis.js" /* webpackChunkName:"owner-analysis" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }
        })
        .state('adAnalysis', {
            url: '/adAnalysis/{id}',
            template: "<analysis />",
            data: {
                pageTitle: 'Specific Advertise Analysis'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'analysis'
                    import(
                        "./pages/analysis/analysis.js" /* webpackChunkName:"analysis" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }

        })
        .state('ranking', {
            url: '/ranking',
            template: "<ranking />",
            data: {
                pageTitle: 'Ranking'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'ranking'
                    import(
                        "./pages/ranking/ranking.js" /* webpackChunkName:"ranking" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }
        })
        .state('bookmark', {
            url: '/bookmark',
            template: '<bookmark />',
            data: {
                pageTitle: 'Bookmark'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'search'
                    import(
                        "./pages/search/search.js" /* webpackChunkName:"search" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }

        })
        .state('plans', {
            url: '/plans',
            template: "<plans />",
            data: {
                pageTitle: 'Plans'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'plans'
                    import(
                        "./pages/plans/plans.js" /* webpackChunkName:"plans" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }

        })
    // User Profile
        .state("profile", {
            url: "/profile",
            template: '<profile />',
            data: {
                pageTitle: 'Profile'
            },
            resolve: {
                deps: ['$q', '$ocLazyLoad', function($q, $ocLazyLoad) {
                    let d = $q.defer()
                    let name = 'profile'
                    import(
                        "./pages/profile/profile.js" /* webpackChunkName:"profile" */
                    ).then(m => {
                        let module
                        if (typeof (m.default) === 'function')
                            module = m.default(angular)
                        else
                            module = m.default
                        $ocLazyLoad.load({
                            name // 页面创建一个属于自己的模块，此处传递模块名，$ocLazyLoad会将其加入到angular的核心中，如果没有这句，即便模块加载成功了也无法引用
                        })
                        d.resolve(module)
                    })

                    return d.promise
                }]
            }
        })
        .state('notfound', {
            url: "/404",
            template: "The Page Not Found"
        })
    $locationProvider.html5Mode(true)
}])

/* Init global settings and run the app */
MetronicApp.run(["$rootScope", "settings", "$state", 'User', 'SweetAlert', '$location', '$window', function($rootScope, settings, $state, User, SweetAlert, $location, $window) {
    var days
    if ($location.search().track) {
        days = $location.search().track.match(/\d\d$/)
        days = days ? Number(days[0]) : 90
        $window.localStorage.setItem('track', JSON.stringify({"code": $location.search().track, "expired": moment().add(days, 'days').format('YYYY-MM-DD')}))
    }
    $rootScope.$state = $state // state to be accessed from view
    $rootScope.$settings = settings // state to be accessed from view
    $rootScope.User = User
    // $rootScope.tr = tr
    // 使用boot方法启动是另一套js
    var APP_ID = "pv0r2p1a"
    var w = window; var ic = w.Intercom; if (typeof ic === "function") { ic('reattach_activator'); ic('update', intercomSettings) } else {
        /* eslint-disable no-inner-declarations */
        var d = document; var i = function() { i.c(arguments) }; i.q = []; i.c = function(args) { i.q.push(args) }; w.Intercom = i; function l() {
            var s = d.createElement('script'); s.type = 'text/javascript'; s.async = true
            s.src = 'https://widget.intercom.io/widget/' + APP_ID
            var x = d.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x)
        } if (w.attachEvent) { w.attachEvent('onload', l) } else { w.addEventListener('load', l, false) }
    }
    User.getInfo().then(function() {
        if (User.login) {
            // intercom文档建议使用boot方式来启动，配合shutdown方法关闭会话，提高安全性
            window.Intercom('boot', {
                app_id: "pv0r2p1a",
                custom_launcher_selector: '#serviceLauncher',
                email: User.user.email,
                name: User.user.name,
                created_at: User.user.created_at,
                user_hash: User.emailHmac // intercom开启验证用户email的hash值,提高安全性
            })
        } else {
            window.Intercom('boot', {
                app_id: "pv0r2p1a",
                custom_launcher_selector: '#serviceLauncher'
            })
        }
        // intercom生成的代码
        // var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function (){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/pv0r2p1a';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}
    })
    setInterval(function() {
        // 每隔一段时间再次更新user信息，一方面是获取新权限，另一方面是防止session过期客户端不知道;
        if (!User.login)
            return
        var oldInfo = null
        User.getInfo(true)
        $rootScope.$on('userChanged', function(ev, newInfo) {
            if (!oldInfo) {
                oldInfo = newInfo
                return
            }
            if (!newInfo.login && oldInfo.login) {
                // 暂时先注释，还在尝试解决
                // SweetAlert.swal("you have logout because of no operation for a long time");

            }
            // console.log("get info again:", oldInfo);
        })
    }, 60000 * 5)

    // Issue #10 run初始化时，调用User.getInfo(true)强制获取一次用户信息
    User.getInfo(true)
}])

MetronicApp.controller('UserController', ['$scope', '$http', '$window', 'User', function($scope, $http, $window, User) {
    $scope.User = User
    $scope.isShow = false
    $scope.logout = function() {
        // 根据intercom的文档，用户退出应使用shutdown方法关闭本次会话
        Intercom('shutdown')
        window.open('/logout', "_self")
    }
}])
