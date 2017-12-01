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

        /*
        * 用户限制表
        * 0：不限制
        * 1、2、3....限制的数量
        * true 限制
        * false 不限制
        * 暂时只针对free用户限制
        */
        const permission = {
            'Free': {
                'audiencesList': 3, // 受众数据条数，仅显示三条
                'demographyShow': false, // 人口统计显示：不限制
                'demographyTime': { // 限制时间3个月
                    'type': 'month', // 按月份算
                    'value': 3 // 数量 3
                },
                'countryMapShow': true, // 国家地图限制：限制
                'countryTableList': 3, // 国家国家数据条数3条
                'countryTableShow': false // 表格显示，不限制
            },
            'Lite': {
                'audiencesList': 3,
                'demographyShow': false,
                'demographyTime': {
                    'type': 'weeks',
                    'value': 2
                },
                'countryMapShow': true,
                'countryTableList': 3,
                'countryTableShow': false
            },
            'Standard': {
                'audiencesList': 0,
                'demographyShow': false,
                'demographyTime': 0,
                'countryMapShow': false,
                'countryTableList': 0,
                'countryTableShow': false
            },
            'Advanced': {
                'audiencesList': 0,
                'demographyShow': false,
                'demographyTime': 0,
                'countryMapShow': false,
                'countryTableList': 0,
                'countryTableShow': false
            },
            'Pro': {
                'audiencesList': 0,
                'demographyShow': false,
                'demographyTime': 0,
                'countryMapShow': false,
                'countryTableList': 0,
                'countryTableShow': false
            }
        }
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

            $scope.userLimit = permission[User.user.role.name]
            console.log($scope.userLimit)
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
                * 受众
                * 将所有受众的信息都集合到一个数组中
                * 根据用户的权限，对数据数据显示进行限制
                * 限制根据：userLimit
                */
                let audienceName = {
                    'speak_languages': 'speak languages', // 语言
                    'audience_called': 'audience called', // 受众称呼
                    'education_level': 'education level', // 学历
                    'custom_audiences_visited_their_website_or_used_one_of_their_apps': 'custom audiences', // 受众定制
                    'demographics_near_their_business': 'demographics', // 人口统计
                    'relationship': 'relationship', // 关系
                    'interests': 'interests', // 兴趣
                    'job_title': 'job title', // 工作职位
                    'education_school': 'education school', // 教育学校
                    'work_company': 'work company', // 工作公司
                    'connections_like_their_Page_list': 'connections' // 链接
                }
                let audienceArr = []
                for (let key1 in $scope.card.whyseeads) {
                    if (audienceName[key1] && $scope.card.whyseeads[key1]) {
                        for (let key2 in $scope.card.whyseeads[key1]) {
                            audienceArr.push({
                                'type': audienceName[key1],
                                'name': key2.replace(/_/g, ' '),
                                'value': $scope.card.whyseeads[key1][key2]
                            })
                        }
                    }
                }
                // 根据type进行排序
                $scope.card.audieces = audienceArr.length && Util.arrSort(audienceArr, 'type', 0)

                /*
                * $scope.userLimit.audiencesList 为限制受众的长度，free的为3 ，意思是仅显示3条数据
                * standard的及以上为0 ，则不做限制，全部展示
                * 如果受众的长度小于限制的长度， 则不做限制， 全部显示
                */
                if ($scope.userLimit.audiencesList && audienceArr.length <= $scope.userLimit.audiencesList) $scope.userLimit.audiencesList = 0

                /*
                * 性别占比与年龄分布处理
                *
                * 【需求】
                *  1）免费用户只能见三个月前的数据
                *  2）liet级别用户限制为两周以前的数据
                *  3）基本付费不限制
                *  4）超过该限制，则填充假数据
                *
                * 【数据说明】
                * $scope.userLimit.demographyTime.type: weeks/month 按月份计算，按周计算
                * $scope.userLimit.demographyTime.value：[数字]
                */
                if ($scope.card.whyseeads.gender && $scope.card.whyseeads.age) {
                    let adsGender = $scope.card.whyseeads.gender
                    let adsAge = $scope.card.whyseeads.age
                    let genderArr = [] // 性别占比 数组
                    let ageArr1, ageArr2 // 龄分布 数组

                    // 是否对demography(人口统计)有限制
                    if ($scope.userLimit.demographyTime) {
                        let timeType = $scope.userLimit.demographyTime.type

                        let lastSeeTime = moment($scope.card.last_see || moment().format("YYYY-MM-DD")) // 如果不存在last_see 默认为现在时间
                        let nowTime = moment() // 现在时间
                        let timeInterval = nowTime.diff(lastSeeTime, timeType) // 相隔时间,单位为月份/周

                        // 是否在限制之内
                        if (timeInterval < $scope.userLimit.demographyTime.value) {
                            genderArr = [['Male', 1], ['Female', 1]]
                            $scope.userLimit.demographyShow = true // 限制显示

                            ageArr1 = [1, 1, 1, 1, 0, 0]
                            ageArr2 = [1, 1, 1, 1, 0, 0]
                            $scope.userLimit.demographyShow = true // 限制显示
                        } else {
                            genderArr = [['Male', adsGender[0]], ['Female', adsGender[1]]]

                            ageArr1 = adsAge.map(function(v) { return v[0] })
                            ageArr2 = adsAge.map(function(v) { return v[1] })
                        }
                    } else {
                        genderArr = [['Male', adsGender[0]], ['Female', adsGender[1]]]

                        ageArr1 = adsAge.map(function(v) { return v[0] })
                        ageArr2 = adsAge.map(function(v) { return v[1] })
                    }
                    // 相别占比数据
                    $scope.card.genderPieCharts = Util.pieChartsConfig(genderArr, '60%', ['#7cb5ec', '#ee5689'])

                    // 堆叠分布 barChartsConfig(barXAxis[X轴数据], barData[数据*], barPercent[以百分号形式显示])
                    $scope.card.ageBarCharts = Util.barChartsConfig(
                        ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'], [{
                            name: 'Male',
                            data: ageArr1
                        }, {
                            name: 'Female',
                            data: ageArr2
                        }],
                        true, // 以百分比形式显示
                        ['#7cb5ec', '#ee5689']
                    )
                } else {
                    $scope.card.genderPieCharts = false
                    $scope.card.ageBarCharts = false
                }

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
                    // $scope.card.whyseeads.count = adsVisitCountryCount
                    // 国家图表 mapChartsConfig(mapData[国家数据], mapValueCount[数据总数], mapName[标题名称], mapLegend[是否显示图例])
                    $scope.card.countryArr = [] // 国家数组
                    let countryItem = 0
                    if ($scope.userLimit.countryTableList && $scope.card.whyseeads.addr.length > $scope.userLimit.countryTableList) {
                        $scope.card.whyseeads.addr.forEach(function(items) {
                            if (countryItem < $scope.userLimit.countryTableList) $scope.card.countryArr.push(items)
                            countryItem++
                        })
                    } else {
                        $scope.card.countryArr = $scope.card.whyseeads.addr
                    }
                    // 如果国家表格的数据小于三条， 则让表格高斯模糊显示
                    if ($scope.userLimit.countryTableList && $scope.card.whyseeads.addr.length <= $scope.userLimit.countryTableList) {
                        $scope.userLimit.countryTableShow = true
                    } else $scope.userLimit.countryTableShow = false

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
            // searcher.findSimilar($scope.card.watermark)
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
        $templateCache.put("ad-bookmark-popover.html", '<bookmark-popover card="$parent.card"/>')
        return {
            restrict: 'E',
            scope: {},
            template,
            replace: false,
            controller: 'AdAnalysisController'
        }
    }])
