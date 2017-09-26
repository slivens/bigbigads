import "bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css"
import 'sweetalert/dist/sweetalert.css'
import 'angular-busy/dist/angular-busy.min.css'
// import './sass/global/font.scss'
// import './sass/global/components-md.scss'
// import './sass/layouts/layout3/themes/yellow-orange.scss'
import './styles/index.scss'
import './pages/common/settings.js'
import './components/app.js'
import {template as upgradeDlgTemplate, controller as upgradeDlgController} from './components/upgrade-dlg.js'
import {template as signTemplate, controller as signController} from './components/sign.js'
import {template as searchResultUpgradeDlgTemplate, controller as searchResultUpgradeDlgController} from './components/search-result-upgrade-dlg.js'
import {template as filterDataLimitDlgTemplate, controller as filterDataLimitDlgController} from './components/filter-data-limit-dlg.js'

window.moment = require('moment')

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
    'bba.app'
])

/* Configure ocLazyLoader(refer: https://github.com/ocombe/ocLazyLoad) */
MetronicApp.config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
    $ocLazyLoadProvider.config({
        // global configs go here
    })
}])

/* Setup global settings */
MetronicApp.constant('TIMESTAMP', Date.parse(new Date()))
MetronicApp.constant('ADS_TYPE', {
    timeline: 1,
    rightcolumn: 4,
    mobile: 2,
    phone: 2
})
MetronicApp.constant('ADS_CONT_TYPE', {
    SINGLE_IMAGE: "SingleImage",
    CANVAS: "Canvas",
    CAROUSEL: "Carousel",
    SINGLE_VIDEO: "SingleVideo"
})
MetronicApp.constant('POLICY_TYPE', {PERMANENT: 0, MONTH: 1, DAY: 2, HOUR: 3, VALUE: 4, DURATION: 5, YEAR: 6})
MetronicApp.filter('nocache', ['TIMESTAMP', function(TIMESTAMP) {
    return function(url) {
        return url + '?t=' + TIMESTAMP
    }
}])
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

/***
Layout Partials.
By default the partials are loaded through AngularJS ng-include directive. In case they loaded in server side(e.g: PHP include function) then below partial 
initialization can be disabled and Layout.init() should be called on page load complete as explained above.
***/

MetronicApp.controller('TabMenuController', ['$scope', '$location', 'User', '$state', function($scope, $location, User, $state) {
    var tabmenu = {
        name: $location.path()
    }
    $scope.tabmenu = tabmenu
    $scope.User = User
    $scope.checkAccount = function() {
        if ((User.info.user.role.name != 'Free') && (User.info.user.role.name != 'Standard')) return
        User.openUpgrade()
    }
    $scope.goBookMark = function() {
        if (!User.login) {
            User.openSign()
        } else {
            $state.go("bookmark")
        }
    }
    $scope.$on('$locationChangeSuccess', function() {
        tabmenu.name = $location.path()
    })
}])

/* Setup Rounting For All Pages */
MetronicApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$urlMatcherFactoryProvider', 'TIMESTAMP', function($stateProvider, $urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider, TIMESTAMP) {
    // var ts = TIMESTAMP
    // Redirect any unmatched url
    $urlMatcherFactoryProvider.strictMode(false)
    // $urlRouterProvider.when("/", "/adsearch");
    $urlRouterProvider.otherwise("/404")

    $stateProvider
        .state('/', {
            url: '/',
            template: '<search />',
            data: {
                pageTitle: 'Advertise Search'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinModern.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'search.css',
                            'search.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinModern.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'search.css',
                            'search.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'owner.css',
                            'owner.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'owner-search.css',
                            'owner-search.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            '/node_modules/jqcloud2/dist/jqcloud.min.css',
                            '/node_modules/jqcloud2/dist/jqcloud.min.js',
                            '../assets/global/plugins/angular-jqcloud.js',
                            'owner-analysis.css',
                            'owner-analysis.js'
                        ]
                    }, {
                        serie: true,
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts/modules/map.js',
                            '../assets/global/scripts/world.min.js'
                        ]
                    }]).then(function() {

                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'analysis.css',
                            'analysis.js'
                        ]
                    }, {
                        serie: true,
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts/modules/map.js',
                            '../assets/global/scripts/world.min.js'
                        ]
                    }]).then(function() {

                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'ranking.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'search.css',
                            'search.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                            '/node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                            '/node_modules/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js',
                            'plans.css',
                            'plans.js'
                        ]
                    })
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
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                            '/node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                            '/node_modules/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js',
                            'profile.js'
                        ]
                    })
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
                email: User.user.email,
                name: User.user.name,
                created_at: User.user.created_at,
                user_hash: User.emailHmac // intercom开启验证用户email的hash值,提高安全性
            })
        } else {
            window.Intercom('boot', {
                app_id: "pv0r2p1a"
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

MetronicApp.factory('User', ['$window', '$http', '$q', '$location', '$rootScope', 'settings', 'ADS_TYPE', '$uibModal', 'TIMESTAMP', function($window, $http, $q, $location, $rootScope, settings, ADS_TYPE, $uibModal, TIMESTAMP) {
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
        openUpgrade: function() {
            return $uibModal.open({
                template: upgradeDlgTemplate,
                size: 'md',
                animation: true,
                controller: upgradeDlgController
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
MetronicApp.controller('UserController', ['$scope', '$http', '$window', 'User', function($scope, $http, $window, User) {
    $scope.User = User
    $scope.isShow = false
    $scope.logout = function() {
        // 根据intercom的文档，用户退出应使用shutdown方法关闭本次会话
        Intercom('shutdown')
        window.open('/logout', "_self")
    }
}])
