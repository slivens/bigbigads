import './analysis.scss'
import '../common/common.js'
import '../common/searcher.js'
import '../bookmark/bookmark.js'
import '../../components/sidebar.js'
import Highcharts from 'highcharts'
import HighchartsMap from 'highcharts/modules/map.js'
import CustomWorld from '../common/world.js'
import 'highcharts-ng'
import template from './analysis.html'

window.Highcharts = Highcharts
HighchartsMap(Highcharts)
CustomWorld(Highcharts)
export default angular.module('analysis', ['MetronicApp', 'highcharts-ng']).controller('AdAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$window', '$http', 'Util', 'User', '$q',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $window, $http, Util, User, $q) {
        var vm = this
        // angualr 页面中使用Math.ceil() 无效
        $scope.ceil = Math.ceil
        var searcher = $scope.adSearcher = new Searcher()
        // $scope.adSearcher.search($scope.adSearcher.defparams, true);
        $scope.reverseSort = function() {
            $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order
            $scope.adSearcher.filter()
        }

        $scope.User = User
        $scope.Util = Util
        $scope.card = {
            end: true,
            similars: []
        }
        $scope.settings = settings
        $scope.id = $stateParams.id
        $scope.adSearcher.addFilter({
            field: 'ads_id',
            value: $scope.id
        })
        $scope.adSearcher.params.ads_detail = 1
        var promise = $scope.adSearcher.filter("analysis")
        var arr
        if (!User.done)
            return
        if (!User.login) {
            window.open('/login', '_self')
            return
        }
        var countryPromise = import('../../data/map-country.json').then(function(res) {
            vm.countries = res
            return res
        })
        $q.all([countryPromise, promise]).then(function(res) {
            // 只取首条消息
            var ads = res[1]
            // objective 转换为正常单词
            var objectStr = {
                "APP_INSTALLS": "App Installs",
                "BRAND_AWARENESS": "Brand Awareness",
                "CANVAS_APP_INSTALLS": "Canvas App Installs",
                "EVENT_RESPONSES": "Event Responses",
                "LEAD_GENERATION": "Lead Generation",
                "LINK_CLICKS": "Link Clicks",
                "LOCAL_AWARENESS": "Local Awareness",
                "PAGE_LIKES": "Page Likes",
                "POST_ENGAGEMENT": "Post Engagement",
                "PRODUCT_CATALOG_SALES": "Product Catalog Sales",
                "REACH": "Reach",
                "STORE_VISITS": "Store Visits",
                "VIDEO_VIEWS": "Video Views",
                "WEBSITE_CONVERSIONS": "Website Conversions"
            }
            $scope.card = $scope.ad = ads.ads_info[0]
            $scope.card.objectStr = objectStr
            // 表示广告在分析模式下，view根据这个字段区别不同的显示
            $scope.card.indetail = true
            $scope.card.end = false
            if ($scope.card.whyseeads_all)
                $scope.card.whyseeads_all = $scope.card.whyseeads_all.split('\n')

            // 广告impression
            if ($scope.card.impression_trend) {
                arr = JSON.parse($scope.card.impression_trend)
                var time
                var key
                for (key in arr) {
                    time = key
                }
                /*
                * 查看几天天数可修改
                * impression 可能存空值
                * 将["2017-03-21":{"12","11"...}] 转为 [{"03-21","12"},{"03-22","11"}...]
                * getTrendArr(开始的时间，长度， 每天的访问量数组)
                */
                var impressionArr = arr[time] ? Util.getTrendArr(time, 7, arr[time]) : false
                if (impressionArr) {
                    // 对器数组进行判断，如果是七天中超过5天为无效数据，则判断该数据无效
                    var impressionValid = 0
                    for (var item in impressionArr.map(function(v) { return v[1] })) {
                        impressionArr.map(function(v) { return v[1] })[item] && impressionValid++
                    }
                    if (impressionValid >= 5) {
                        // 通用折线配置 lineCharsConfig(typeData[类型], xAxisData[X轴数据*], seriesName[数据名称], seriesData[数据*], zoomTypeData[数据放大])
                        $scope.card.impressionCharts = Util.lineChartsConfig('area', impressionArr.map(function(v) { return v[0] }), 'Impression', impressionArr.map(function(v) { return v[1] }))
                    } else $scope.card.impressionCharts = false
                } else {
                    $scope.card.impressionCharts = false // 对于提供时间错误的，或则数据长度小于3的则不显示
                }
            }
            // engagements_trend
            if ($scope.card.engagements_trend) {
                arr = JSON.parse($scope.card.engagements_trend)
                // engagements_trend.trend 存在null 值
                var engagementsArr = arr.trend ? Util.getTrendArr(arr.day, 0, arr.trend) : false
                if (engagementsArr) {
                    $scope.card.engagementsCharts = Util.lineChartsConfig('area', engagementsArr.map(function(v) { return v[0] }), 'Engagements', engagementsArr.map(function(v) { return v[1] }), 'x')
                } else {
                    $scope.card.engagementsCharts = false
                }
            }
            // 如果whyseeads不为空，填充到广告趋势
            if ($scope.card.whyseeads) {
                $scope.card.whyseeads = JSON.parse($scope.card.whyseeads)

                // 获取当前用户的权限名称
                if (User.user.role.name) {
                    $scope.userPlan = User.user.role.plan
                }

                /*
                * interesting数据处理
                * 如果是免费用户，则最多只显示三条的interest的信息
                * 如果是免费的用户，并且insterest的信息大于三组，则加个标识
                */
                if ($scope.card.whyseeads.interests) {
                    $scope.card.interestsArr = []
                    let interestsCount = 0
                    for (key in $scope.card.whyseeads.interests) {
                        $scope.card.interestsArr.push({
                            'name': key,
                            'value': $scope.card.whyseeads.interests[key]
                        })
                        // 对总数继续计数
                        interestsCount += $scope.card.whyseeads.interests[key]
                    }
                    /*
                     * 对数组的value进行排序,具体用法见common.js
                     * srrSort(整个数组，要根据排序的名称，正序0/逆序1)
                     */
                    $scope.card.interestsArr = Util.arrSort($scope.card.interestsArr, "value", 1)
                    // 加个限制标识,免费用户，刚好且小于三条时，就不显示标识
                    if ($scope.userPlan == 'free' && Object.keys($scope.card.whyseeads.interests).length >= 3) {
                        $scope.card.interestsArr.limit = 3
                    }
                    $scope.card.interestsArr.count = interestsCount
                }

                /*
                * 性别占比与年龄分布处理
                * 免费用户只能见三个月前的数据，三个月内填充假数据
                */
                let lastSeeTime = moment($scope.card.last_see || moment().format("YYYY-MM-DD")) // 如果不存在last_see 默认为现在时间
                let nowTime = moment()
                let timeInterval = nowTime.diff(lastSeeTime, 'month') // 相隔时间,单位为月份

                // 如果为三个月内数据，且为免费用户，则填充假数据
                if ($scope.card.whyseeads.gender) {
                    let genderArr = []
                    let asdGender = $scope.card.whyseeads.gender
                    if ((timeInterval < 3) && $scope.userPlan == 'free') {
                        genderArr = [['Male', 1], ['Female', 1]]
                        $scope.card.whyseeads.limit = true // 添加标识
                    } else {
                        genderArr = [['Male', asdGender[0]], ['Female', asdGender[1]]]
                    }
                    $scope.card.genderPieCharts = Util.pieChartsConfig(genderArr, '60%', ['#7cb5ec', '#ee5689'])
                } else $scope.card.genderPieCharts = false

                // 广告详情-年龄分布
                if ($scope.card.whyseeads.age) {
                    let arr1, arr2
                    if ((timeInterval < 3) && $scope.userPlan == 'free') {
                        arr1 = [1, 1, 1, 1, 0, 0]
                        arr2 = [1, 1, 1, 1, 0, 0]
                    } else {
                        arr1 = $scope.card.whyseeads.age.map(function(v) { return v[0] })
                        arr2 = $scope.card.whyseeads.age.map(function(v) { return v[1] })
                    }

                    // 堆叠分布 barChartsConfig(barXAxis[X轴数据], barData[数据*], barPercent[以百分号形式显示])
                    $scope.card.ageBarCharts = Util.barChartsConfig(
                        ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'], [{
                            name: 'Male',
                            data: arr1
                        }, {
                            name: 'Female',
                            data: arr2
                        }],
                        true, // 以百分比形式显示
                        ['#7cb5ec', '#ee5689']
                    )
                } else $scope.card.ageBarCharts = false

                /*
                * 国家信息处理
                * 免费用户只能看三条记录 + Upgred
                * 如果少于三条，则全部显示
                */
                if ($scope.card.whyseeads.addr) {
                    // 将country里的值改为大写,并转换全称
                    for (key in $scope.card.whyseeads.addr) {
                        var countryShortName = $scope.card.whyseeads.addr[key].country.toUpperCase() // 转换成大写
                        $scope.card.whyseeads.addr[key].country = countryShortName
                        $scope.card.whyseeads.addr[key].name = vm.countries[countryShortName] ? vm.countries[countryShortName].name : countryShortName // 添加全称
                    }

                    // 排序处理
                    Util.arrSort($scope.card.whyseeads.addr, "value", 1)

                    // 计算总数
                    var adsVisitCountryCount = 0
                    for (key in $scope.card.whyseeads.addr) {
                        adsVisitCountryCount += $scope.card.whyseeads.addr[key].value
                    }
                    $scope.card.whyseeads.count = adsVisitCountryCount
                    // 国家图表 mapChartsConfig(mapData[国家数据], mapValueCount[数据总数], mapName[标题名称], mapLegend[是否显示图例])
                    $scope.card.countryArr = []
                    let countryItem = 0
                    if ($scope.userPlan == 'free' && $scope.card.whyseeads.addr.length > 3) {
                        $scope.card.whyseeads.addr.forEach(function(items) {
                            if (countryItem < 3) $scope.card.countryArr.push(items)
                            countryItem++
                        })
                        $scope.card.countryArr.limit = 3
                    } else {
                        $scope.card.countryArr = $scope.card.whyseeads.addr
                    }
                    $scope.card.countryArr.count = adsVisitCountryCount
                    $scope.card.addrMapLimit = $scope.userPlan == 'free'
                    $scope.card.addrMapCharts = Util.mapChartsConfig($scope.card.countryArr, adsVisitCountryCount, 'Top countries by impression')
                } else $scope.card.addrMapCharts = false
            }
            // 设备占比，当pc为0或不存在时，怎移动设备为100%
            var desktopNum = ($scope.card.pc_impression_rate ? $scope.card.pc_impression_rate : 0) * 100
            var mobileNum = 100 - desktopNum
            var pieLegend = {
                enabled: false
                /*
                    align: 'right',
                    verticalAlign: 'middle',
                    layout: 'vertical',
                    symbolPadding: 15,
                    itemMarginTop: 15,
                    labelFormatter: function() {
                        return this.name + ':' + this.y
                    }
                    */
            }
            $scope.card.devicePieCharts = Util.pieChartsConfig([['Mobile', mobileNum], ['Desktop', desktopNum]], '0%', false, pieLegend)
            searcher.findSimilar($scope.card.watermark)
        }, function(res) {
            // console.log("error res:", res);
            $scope.card.end = true
            if (res.status != 200) {
                Util.hint(res)
            }
        }).finally(function() {
            // $rootScope.$broadcast("completed");
        })

        $scope.goback = function() {
            $window.history.back()
        }

        /**
         * 查找相似图
         */
        searcher.findSimilar = function(watermark) {
            if (!watermark)
                return false
                // console.log("water", watermark);
            var similarSearcher = new Searcher()
            var similarPromise
            var md5
            if (watermark instanceof Array)
                md5 = watermark[0].source.match(/\/(\w+)\./)
            else
                md5 = watermark.match(/\/(\w+)\./)
            if (md5 === null) {
                return false
            }
            // console.log(md5);
            md5 = md5[1]

            similarSearcher.addFilter({
                field: "watermark_md5",
                value: md5
            })
            similarPromise = similarSearcher.filter('similar')
            similarPromise.then(function(ads) {
                $scope.card.similars = ads.ads_info
                console.log("similar", ads)
            })
            return similarPromise
        }
        /**
         * 加载广告趋势
         */
        searcher.getTrends = function(eventid) {
            // eventid = "118849271971984";//for test
            var params = {
                search_result: "adsid_trend",
                where: [{
                    field: "ads_id",
                    value: eventid
                }],
                event_id: eventid
            }
            return $http.post(settings.remoteurl + "/forward/trends", params)
        }
        searcher.isLoadingCharts = true
        searcher.getTrends($scope.id).then(function(res) {
            // console.log(res);
            var data = res.data
            if (!data.info) {
                searcher.noTrends = true
                return
            }
            if (data.info.comments)
                searcher.commentsTrend = Util.initTrend(data.info.comments, "comments", $scope.id)
            if (data.info.engagements)
                searcher.engagementsTrend = Util.initTrend(data.info.engagements, "engagements", $scope.id)
            if (data.info.likes)
                searcher.likesTrend = Util.initTrend(data.info.likes, "likes", $scope.id)
            if (data.info.shares)
                searcher.sharesTrend = Util.initTrend(data.info.shares, "shares", $scope.id)
            if (data.info.views)
                searcher.viewsTrend = Util.initTrend(data.info.views, "views", $scope.id)
        }).finally(function() {
            searcher.isLoadingCharts = false
        })
        $scope.$on('$viewContentLoaded', function() {
            // initialize core components

            // set default layout mode
            $rootScope.settings.layout.pageContentWhite = true
            $rootScope.settings.layout.pageBodySolid = false
            $rootScope.settings.layout.pageSidebarClosed = false
        })
        // 年龄分布-条状
        $scope.adsAgeCharts = {
            chart: {
                type: 'bar'
            },
            title: {
                text: false
            },
            credits: false,
            xAxis: {
                categories: ['18-24', '25-34', '35-44', '45-54', '55-64', '65+']
            },
            yAxis: {
                min: 0,
                title: {
                    text: false
                },
                max: 100
            },
            legend: {
                reversed: true
            },
            // colors: ['rgb(63, 169, 197)', 'rgb(116, 204, 220)'], //自定义颜色会出问题，有待解决
            plotOptions: {
                series: {
                    stacking: 'cloumn'
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>Series name: ' + this.series.name + '</b><br>' +
                            'Point name: ' + this.point.x + '<br>' +
                            'Value: ' + ((this.point.y) * 100).toFixed(1) + '%'
                }
            },
            series: [{
                name: 'Male',
                data: []
            }, {
                name: 'Female',
                data: []
            }]
        }
    }
])
    .directive('analysis', ['$templateCache', function($templateCache) {
        $templateCache.put("ad-bookmark-popover.html", '<bookmark-popover/>')
        return {
            restrict: 'E',
            scope: {},
            template,
            replace: false,
            controller: 'AdAnalysisController'
        }
    }])
