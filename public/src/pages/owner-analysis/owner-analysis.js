import './owner-analysis.scss'
import '../common/common.js'
import '../bookmark/bookmark.js'
import '../common/searcher.js'
import Highcharts from 'highcharts'
import HighchartsMap from 'highcharts/modules/map.js'
import CustomWorld from '../common/world.js'
import 'highcharts-ng'
import template from './owner-analysis.html'

window.Highcharts = Highcharts
HighchartsMap(Highcharts)
CustomWorld(Highcharts)

export default angular.module('owner-analysis', ['MetronicApp', 'highcharts-ng']).controller('AdserAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$http', '$uibModal', '$q', 'Util', '$timeout', 'User',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $http, $uibModal, $q, Util, $timeout, User) {
        /*
        * 广告主分析接口未定，暂时用临时接口
        * 许多原有代码，确认无用的已经删除，不确定是干嘛的，或注释或暂留
        * 因为临时接口与原有的存在变化，广告主分析的这块代码比较混乱，待接口出来后，将再做修改
        */
        var vm = this
        var key
        /*
        * 判断用户状态
        * 1、未登录，跳转到登录页面
        * 2、免费用户，回到/app
        * 3、其他权限：需求未详细给出！
        */
        $scope.user = User
        if (!User.login) {
            window.open('/login', '_self')
            return
        } else if ($scope.user.user.role.plan == 'free') {
            window.open('/app', '_self')
            return
        }

        function getAdserAnalysis(username, select) {
            // if (select === undefined)
            //     select = "all"
            // var params = {
            //     "search_result": "adser_analysis",
            //     "where": [{
            //         "field": "adser_username",
            //         "value": username
            //     }],
            //     "select": select
            // }
            // console.log("params", params)
            return $http.get(settings.remoteurl + `/api/adserAnalysis/${username}`)
        }
        $scope.ceil = Math.ceil
        var competitorQuery = []
        var promises = []
        // $scope.openAd = Util.openAd
        $scope.card = {
            words: []
        } // card必须先赋值，否则调用Searcher的getAdsType时会提前生成自己的card,scope出错。
        // $scope.Searcher = Searcher
        $scope.username = $stateParams.username
        $rootScope.$broadcast("loading")
        // promises[0] = getAdserAnalysis($scope.username, "overview,rank,trend,topkeyword")
        promises[0] = getAdserAnalysis($scope.username)
        promises[0].then(function(res) {
            if (res.data) {
                $scope.card.info = res.data[0]
            }

            // 获取不到数据，可认为存在问题，直接跳往404页面
            if (!res.data) {
                window.open('/404', '_self')
                return
            }
            // console.log("first phase", $scope.card)
            // 不知道是干嘛的
            for (key in $scope.card.top_keyword) {
                $scope.card.words.push({
                    text: key,
                    weight: $scope.card.top_keyword[key]
                })
            }

            $rootScope.$broadcast("jqchange")
            $rootScope.$broadcast("completed")
            // 异步获取广告主详情数据
            // promises[1] = getAdserAnalysis($scope.username, "summary,audience_all,link,topn,button")
            // promises[1].then(function(res) {
            // impression_trend
            if ($scope.card.info.impression_trend) {
                try {
                    var impresArr = JSON.parse($scope.card.info.impression_trend)
                    var impresTime
                    for (var impresKey in impresArr) {
                        impresTime = impresKey
                    }
                    var impressionArr = Util.getTrendArr(impresTime, 7, impresArr[impresTime])
                    $scope.card.impressionCharts = Util.lineChartsConfig('area', impressionArr.map(v => v[0]), 'Impression Trend', impressionArr.map(v => v[1]), 'x')
                } catch (err) { $scope.card.impressionCharts = false }
            } else $scope.card.impressionCharts = false

            // emgagements_trend
            if ($scope.card.info.engagements_trend) {
                try {
                    var engaArr = JSON.parse($scope.card.info.engagements_trend)
                    var engagementsArr = Util.getTrendArr(engaArr.day, 0, engaArr.trend)
                    $scope.card.engagementsCharts = Util.lineChartsConfig('area', engagementsArr.map(v => v[0]), 'Impression Trend', engagementsArr.map(v => v[1]), 'x')
                } catch (err) { $scope.card.engagementsCharts = false }
            } else $scope.card.engagementsCharts = false

            // 性别比例、年龄分布、国家分布、兴趣爱好统计
            if ($scope.card.info.audience) {
                $scope.card.info.audience = JSON.parse($scope.card.info.audience)
                var audienceArr = $scope.card.info.audience
                // 性别占比 因为其空值至少会带个[]所以直接判断其长度
                if (audienceArr.gender.length) {
                    $scope.card.gnederPieCharts = Util.pieChartsConfig([['Male', audienceArr.gender[0]], ['Female', audienceArr.gender[1]]], '60%', ['#337ab7', '#f9c'])
                } else $scope.card.gnederPieCharts = false
                // 年龄分布
                if (audienceArr.age.length) {
                    var arr1 = audienceArr.age.map(function(v) { return v[0] })
                    var arr2 = audienceArr.age.map(function(v) { return v[1] })
                    $scope.card.ageBarCharts = Util.barChartsConfig(
                        ['18-24', '25-34', '35-44', '45-54', '55-64', '65+'], [{
                            name: 'Male',
                            data: arr1
                        }, {
                            name: 'Female',
                            data: arr2
                        }],
                        true, // 以百分比形式显示
                        ['#337ab7', '#f9c']
                    )
                } else $scope.card.ageBarCharts = false
                // 兴趣爱好; 有些数据的空值是{},所以要判断知否存在key值，但其值是null时会报错！
                if (audienceArr.interests && Object.keys(audienceArr.interests).length) {
                    var interestsCount = 0
                    $scope.card.interestsArr = []
                    for (var interestKey in audienceArr.interests) {
                        $scope.card.interestsArr.push({
                            'name': interestKey,
                            'value': audienceArr.interests[interestKey]
                        })

                        interestsCount += audienceArr.interests[interestKey]
                    }
                    // 对兴趣按照value值
                    $scope.card.interestsArr = Util.arrSort($scope.card.interestsArr, "value", 1)
                    $scope.card.interestsArr.count = interestsCount
                } else $scope.card.interestsArr = false
                // 国家分布
                if (audienceArr.addr.length) {
                    // 将country里的值改为大写,并转换全称
                    var addrCount = 0
                    for (key in audienceArr.addr) {
                        addrCount += audienceArr.addr[key].value
                    }
                    // 去获取地图数据
                    $http.get('/app/data/map-country.json').then(function(res) {
                        vm.countries = res.data
                        for (key in audienceArr.addr) {
                            var countryShortName = audienceArr.addr[key].country.toUpperCase() // 转换成大写
                            audienceArr.addr[key].country = countryShortName
                            audienceArr.addr[key].name = vm.countries[countryShortName] ? vm.countries[countryShortName].name : countryShortName // 添加全称
                        }
                        $scope.card.addrMapCharts = Util.mapChartsConfig(audienceArr.addr, addrCount, 'Top countries by impression')
                        // 对国家分布数据进行排序
                        $scope.card.addrTable = Util.arrSort(audienceArr.addr, 'value', 1)
                        $scope.card.addrTable.count = addrCount
                    })
                } else {
                    $scope.card.addrMapCharts = false
                    $scope.card.addrTable = false
                }
            }

            // 设备占比 PC为0或不存在时，mobile为100%
            var desktopNum = ($scope.card.info.pc_impression_rate ? $scope.card.info.pc_impression_rate : 0) * 100
            var mobileNum = 100 - desktopNum
            var pieLegend = {
                enabled: false
            }
            $scope.card.devicePieCharts = Util.pieChartsConfig([['Desktop', desktopNum], ['Mobile', mobileNum]], '0%', false, pieLegend)

            // Object
            if ($scope.card.info.objective) {
                // 先对其进行排序
                var objectiveArr = Util.objectSort(JSON.parse($scope.card.info.objective), 'value', 1)
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

                /*
                * 将原生的object 数据转换成 对应的数据
                * 将object显示前3个 + Others
                */
                $scope.card.objectiveArr = []
                let objectiveArrItem = 0 // 定义一个object的计数
                let objectiveOtherSum = 0 // 定义其他的总数
                for (key in objectiveArr) {
                    if (objectiveArrItem < 3) {
                        $scope.card.objectiveArr.push([
                            objectStr[key], objectiveArr[key]
                        ])
                    } else {
                        objectiveOtherSum += objectiveArr[key]
                    }
                    objectiveArrItem++
                }
                $scope.card.objectiveArr.push([
                    'Others', objectiveOtherSum
                ])

                // 配置显示颜色
                var objColors = ['#2196F3', '#64B5F6', '#90CAF9', '#eb6130', '#eb6130', '#8085e9', '#f15c80', '#e4d354', '#8085e8', '#8d4653', '#91e8e1']

                // 配置图例
                var objPieLegend = {
                    enabled: true,
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'vertical',
                    itemMarginTop: 5,
                    itemMarginBottom: 5,
                    x: 0,
                    y: 120,
                    floating: true,
                    labelFormatter: function() {
                        return '<label style="color:#333">' + this.name + '</label>  :  ' + this.percentage.toFixed(2) + "%"
                    }
                }
                // 配置鼠标经过显示
                var objPieToolTip = {
                    headerFormat: '<b>Objective</b><br>',
                    pointFormat: '<b>{point.name}:</b>{point.y}--{point.percentage:.1f}%'
                }
                $scope.card.objectPieCharts = Util.pieChartsConfig($scope.card.objectiveArr, '0%', objColors, objPieLegend, objPieToolTip)
            } else {
                $scope.card.objectPieCharts = false
                $scope.card.objectiveArr = false
            }

            // 广告个数分布
            var adsNumPieArr = []
            adsNumPieArr.push({'name': 'Mobile', 'value': $scope.card.info.phone_ad_count || 0})
            adsNumPieArr.push({'name': 'Right Column', 'value': $scope.card.info.rc_ad_count || 0})
            adsNumPieArr.push({'name': 'News Feed', 'value': $scope.card.info.tl_ad_count || 0})
            // adsNumPieArr.push({'name': 'Audience Network', 'value': 0})

            adsNumPieArr = Util.arrSort(adsNumPieArr, 'value', 1)
            var newAdsPieArr = []
            adsNumPieArr.forEach(function(items) {
                newAdsPieArr.push([items.name, items.value])
            })

            if ($scope.card.info.phone_ad_count || $scope.card.info.rc_ad_count || $scope.card.info.tl_ad_count) {
                $scope.card.adsNumPieCharts = Util.pieChartsConfig(newAdsPieArr, '0%', ['#2196F3', '#64B5F6', '#90CAF9', '#BBDEFB'], objPieLegend)
            } else $scope.card.adsNumPieCharts = false

            /*
            * 广告类型占比
            * 将数据组装成数组,然后进行排序
            */
            var adsTypeArr = []
            adsTypeArr.push({'name': 'Images', 'value': $scope.card.info.image_ad_count || 0})
            adsTypeArr.push({'name': 'Carousels', 'value': $scope.card.info.carousel_ad_count || 0})
            adsTypeArr.push({'name': 'Canvas', 'value': $scope.card.info.carousel_ad_count || 0})
            adsTypeArr.push({'name': 'Videos', 'value': $scope.card.info.video_ad_count || 0})
            adsTypeArr = Util.arrSort(adsTypeArr, 'value', 1)
            if ($scope.card.info.image_ad_count || $scope.card.info.carousel_ad_count || $scope.card.info.canvas_ad_count || $scope.ads.info.video_ad_count) {
                var sortAdsTypeArr = []
                adsTypeArr.forEach(function(items) {
                    sortAdsTypeArr.push([items.name, items.value])
                })
                $scope.card.adsTypeCharts = Util.pieChartsConfig(sortAdsTypeArr, '0%', ['#2196F3', '#64B5F6', '#90CAF9', '#BBDEFB'], objPieLegend)
            } else $scope.card.adsTypeCharts = false
        }).catch(function(err) {
            console.log(err)
        })
        // })
        $scope.competitors = []
        $scope.competitorPopover = false
        $scope.competitorsChart = {}

        // 不知道该功能是干嘛的，暂时留着
        $scope.$on('competitor', function(event, data) {
            event.stopPropagation()
            $scope.competitorPopover = false
            // p = getAdserAnalysis(data.adser_username)
            // p.then(addCompetitor).then(initCompetitorCharts)
            competitorQuery.push(data.adser_username)
            $location.search('competitor', competitorQuery.join(','))
        })

        $scope.remove = function(idx) {
            $scope.competitors.splice(idx, 1)
            competitorQuery.splice(idx, 1)
            $location.search('competitor', competitorQuery.join(','))
            // initCompetitorCharts()
        }

        // 所有广告主分析数据加载完成才处理图表
        // $q.all(promises).then(initCompetitorCharts)

        // $scope.$on('$viewContentLoaded', function() {
        //     // initialize core components
        //     // set default layout mode
        //     $rootScope.settings.layout.pageContentWhite = true
        //     $rootScope.settings.layout.pageBodySolid = false
        //     $rootScope.settings.layout.pageSidebarClosed = false
        // })
        /*

        * 加载like_rate等数据
        * 1）现有接口用的是移动端的接口，暂时使用
        * 2）移动端返回的数据格式不适合该页面使用，所有要重新整理
        * 3）返回的数据格式类似，故写成方法
        */
        var getRateData = function(userId, paramater) {
            let resArr = []
            $http.get(`/api/topAds/${userId}/${paramater}`, {}).success(
                function(data) {
                    if (data.ads) {
                        data.ads.forEach(function(items) {
                            if (items && items.forEach) {
                                items.forEach(function(item) {
                                    resArr.push(item)
                                })
                            } else {
                                Object.keys(items).forEach(function(key) {
                                    resArr.push(items[key])
                                })
                            }
                        })
                    } else resArr = false
                    console.log(paramater, resArr)
                    if (paramater == 'like_rate') $scope.card.like_rate = resArr
                    if (paramater == 'share_rate') $scope.card.share_rate = resArr
                    if (paramater == 'comment_rate') $scope.card.comment_rate = resArr
                    if (paramater == 'total_impression') $scope.card.total_impression = resArr
                }).error(function(data) {
                console.log(data)
            })
        }

        // 详情页导航菜单初始化
        $scope.analyMenu1 = true
        $scope.analyMenu2 = false
        $scope.analyMenu3 = false
        // 点击菜单跳转
        $scope.turnAnaly = function(menu) {
            $scope.analyMenu1 = false
            $scope.analyMenu2 = false
            $scope.analyMenu3 = false
            $scope[menu] = true
            // 菜单3选项卡的时候再加载数据
            if (menu === "analyMenu3") {
                // 判断是否已经加载过了
                $scope.card.like_rate || getRateData($scope.username, 'like_rate')
                $scope.card.share_rate || getRateData($scope.username, 'share_rate')
                $scope.card.comment_rate || getRateData($scope.username, 'comment_rate')
                $scope.card.total_impression || getRateData($scope.username, 'total_impression')
            }
        }
    }
])
    .controller('CompetitorSearcherController', ['$scope', 'Searcher', function($scope, Searcher) {
        var searcher = new Searcher({
            searchType: 'adser',
            url: '/forward/adserSearch',
            limit: [0, -1]
        })
        $scope.searchOpt = {
            text: "",
            inprogress: false
        }
        $scope.promise = null
        $scope.search = function() {
            searcher.params.keys = [{
                string: $scope.searchOpt.text,
                search_fields: 'adser_name',
                relation: "Must"
            }]
            $scope.searchOpt.inprogress = true
            $scope.promise = searcher.filter()
            $scope.promise.then(function(data) {
                $scope.searchOpt.items = data.adser
                // $scope.searchOpt.inprogress = false;
                $scope.searchOpt.error = null
            }, function(data) {
                $scope.searchOpt.error = "No Data"
                $scope.searchOpt.items = null
            }).finally(function() {
                $scope.searchOpt.inprogress = false
            })
        }

        $scope.notify = function(item) {
            $scope.$emit('competitor', item)
        }
    }])
    .directive('ownerAnalysis', function() {
        return {
            restrict: 'E',
            scope: {},
            template,
            replace: false,
            controller: 'AdserAnalysisController'
        }
    })
