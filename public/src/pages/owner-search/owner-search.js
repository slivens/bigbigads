import './owner-search.scss'
import '../common/common.js'
import '../bookmark/bookmark.js'
import '../common/searcher.js'
import '../../components/sidebar.js'
import '../../components/adser-search-card.js'
import 'angular-deckgrid/angular-deckgrid'
import 'ng-infinite-scroll'
import 'angular-daterangepicker'
import 'bootstrap-daterangepicker/daterangepicker.css'
import 'bootstrap-daterangepicker'
import 'select2/dist/css/select2.min.css'
import 'select2'
import 'bootstrap-select/dist/css/bootstrap-select.min.css'
import 'bootstrap-select'
import 'ion-rangeslider/css/ion.rangeSlider.css'
import 'ion-rangeslider/css/ion.rangeSlider.skinModern.css'
import 'ion-rangeslider'
import template from './owner-search.html'

export default angular.module('owner-search', ['MetronicApp', 'daterangepicker', 'akoenig.deckgrid', 'infinite-scroll']).controller('AdserSearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User) {
        $scope.settings = settings
        // 搜索流程:location.search->searchOption->adSearcher.params
        // 将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
        function searchToQuery(option, searcher) {
            $location.search(searcher.searchToQuery(option))
        }
        // 将query转化成搜索参数
        function queryToSearch(option, searcher) {
            searcher.queryToSearch($location.search(), option)
        }
        $scope.swal = function(msg) {
            SweetAlert.swal(msg)
        }
        $scope.adSearcher = new Searcher({
            searchType: 'adser',
            url: '/forward/adserSearch'
        })
        $scope.inAdvertiserMode = true
        // $scope.adSearcher.search($scope.adSearcher.defparams, true);
        $scope.reverseSort = function() {
            $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order
            $scope.adSearcher.filter()
        }

        // $scope.filterOption = {
        //     date: {
        //         startDate: null,
        //         endDate: null
        //     },
        // };
        // text为空时就表示没有这个搜索项了
        $scope.initSearch = function() {
            var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption)
            $scope.filterOption = $scope.searchOption.filter

            // 存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
            queryToSearch(option, $scope.adSearcher)
        }
        // $scope.resetSearch = function() {
        //     angular.forEach($scope.filterOption.category, function(value, key) {
        //         value.selected = false;
        //     });
        //     angular.forEach($scope.filterOption.format, function(value, key) {
        //         value.format = false;
        //     });
        // };
        $scope.initSearch()

        $scope.currSearchOption = {}
        // $scope.filterOption.category = angular.copy(settings.searchSetting.categoryList);
        // $scope.filterOption.format = angular.copy(settings.searchSetting.formatList);
        // $scope.filterOption.buttondesc = angular.copy(settings.searchSetting.buttondescList);

        $scope.filter = function(option) {
            var category = []
            var format = []
            var buttondesc = []
            // 广告类型
            if (!$scope.filterOption.type)
                $scope.adSearcher.removeFilter("ads_type")
            else
                $scope.adSearcher.addFilter({
                    field: 'ads_type',
                    value: $scope.filterOption.type
                })
                // 日期范围
            if (!option.date.startDate || !option.date.endDate) {
                $scope.adSearcher.removeFilter('time')
            } else {
                var startDate = option.date.startDate.format('YYYY-MM-DD')
                var endDate = option.date.endDate.format('YYYY-MM-DD')
                $scope.adSearcher.addFilter({
                    field: "time",
                    min: startDate,
                    max: endDate
                })
            }
            // 语言
            if (option.lang && option.lang.length) {
                $scope.adSearcher.addFilter({
                    field: 'ad_lang',
                    value: option.lang
                })
            } else {
                $scope.adSearcher.removeFilter('ad_lang')
            }
            // 国家
            if (option.state) {
                $scope.adSearcher.addFilter({
                    field: 'state',
                    value: option.state
                })
            } else {
                $scope.adSearcher.removeFilter('state')
            }

            // 支持多项搜索，以","隔开
            angular.forEach($scope.filterOption.category, function(item, key) {
                if (item.selected) {
                    category.push(item.key)
                }
            })
            $scope.filterOption.categoryString = category.join(',')

            if (category.length) {
                $scope.adSearcher.addFilter({
                    field: 'category',
                    value: category.join(",")
                })
            } else {
                $scope.adSearcher.removeFilter('category')
            }

            // format by select2 multiple
            angular.forEach($scope.filterOption.formatSelected, function(item) {
                format.push(item)
            })
            $scope.filterOption.formatString = format.join(',')
            if (format.length) {
                $scope.adSearcher.addFilter({
                    field: 'media_type',
                    value: format.join(",")
                })
            } else {
                $scope.adSearcher.removeFilter('media_type')
            }

            // Call To Action
            angular.forEach($scope.filterOption.callToAction, function(item) {
                buttondesc.push(item)
            })
            option.buttondescString = buttondesc.join(',')
            if (buttondesc.length) {
                $scope.adSearcher.addFilter({
                    field: 'buttondesc',
                    value: buttondesc.join(",")
                })
            } else {
                $scope.adSearcher.removeFilter('buttondesc')
            }

            // Duration Filter
            if (!option.isDurationDirty()) {
                $scope.adSearcher.removeFilter('duration_days')
            } else {
                $scope.adSearcher.addFilter({
                    field: 'duration_days',
                    min: option.duration.from,
                    max: option.duration.to
                })
            }

            // see times Filter
            if (!option.isSeeTimesDirty()) {
                $scope.adSearcher.removeFilter('see_times')
            } else {
                $scope.adSearcher.addFilter({
                    field: 'see_times',
                    min: option.seeTimes.from,
                    max: option.seeTimes.to
                })
            }
            // engagementsFilter
            angular.forEach(option.engagements, function(item, key) {
                if ((item.min === "" || item.min === null) || (item.max === "" || item.max === null)) {
                    $scope.adSearcher.removeFilter(item)
                } else {
                    $scope.adSearcher.addFilter({
                        field: item,
                        min: key.min,
                        max: key.max
                    })
                }
            })

            // tracking tools
            if (option.tracking && option.tracking.length) {
                $scope.adSearcher.addFilter({
                    field: 'tracking',
                    value: option.tracking.join(',')
                })
                $scope.currSearchOption.filter.tracking = option.tracking.join(',')
            } else {
                $scope.adSearcher.removeFilter("tracking")
            }

            // Affiliate
            if (option.affiliate && option.affiliate.length) {
                $scope.adSearcher.addFilter({
                    field: 'affiliate',
                    value: option.affiliate.join(',')
                })
                $scope.currSearchOption.filter.affiliate = option.affiliate.join(',')
            } else {
                $scope.adSearcher.removeFilter("affiliate")
            }

            // E_Commerce
            if (option.ecommerce && option.ecommerce.length) {
                $scope.adSearcher.addFilter({
                    field: 'e_commerce',
                    value: option.ecommerce.join(',')
                })
                $scope.currSearchOption.filter.ecommerce = option.ecommerce.join(',')
            } else {
                $scope.adSearcher.removeFilter("e_commerce")
            }

            $scope.currSearchOption.category = category.join(',')
            $scope.currSearchOption.format = format.join(',')
            $scope.currSearchOption.callToAction = buttondesc.join(',')
            $scope.adSearcher.filter().then(function() {}, function(res) {
                if (res.status != 200)
                    Util.hint(res)
            })

            // console.log("params", $scope.adSearcher.params);
        }

        $scope.search = function() {
            var option = $scope.adSearcher.searchOption
            var keys
            var range = []
            var rangeValue = []
            keys = $scope.adSearcher.params.keys = []

            // 字符串和域
            $scope.currSearchOption = angular.copy($scope.searchOption) // 保存搜索
            if (option.rangeselected && option.rangeselected.length) {
                angular.forEach(option.rangeselected, function(item) {
                    range.push(item)
                })
            }
            if (option.search.text || range.length) {
                option.search.fields = range.length ? range.join(',') : option.search.fields
                keys.push({
                    string: option.search.text ? option.search.text : "",
                    search_fields: option.search.fields,
                    relation: "Must"
                })
                angular.forEach(option.range, function(item) {
                    if (range.indexOf(item.key) > -1) rangeValue.push(item.value)
                })
            }
            // 域名
            if (option.domain.text) {
                keys.push({
                    string: option.domain.text ? option.domain.text : "",
                    search_fields: 'caption,link,dest_site,buttonlink',
                    relation: option.domain.exclude ? 'Not' : 'Must'
                })
            }
            // 受众
            if (option.audience.text) {
                keys.push({
                    string: option.audience.text,
                    search_fields: 'whyseeads,whyseeads_all',
                    relation: option.audience.exclude ? 'Not' : 'Must'
                })
            }
            $scope.currSearchOption.range = range.join(',')
            $scope.filter($scope.filterOption)
            if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 0) {
                $scope.currSearchOption.isdirty = true
            }
            searchToQuery(option, $scope.adSearcher)
        }

        $scope.clearSearch = function() {
            $location.search({})
            $state.reload()
        }

        User.getInfo().then(function() {
            // 根据search参数页面初始化
            if (!User.login) {
                window.open('/login', "_self")
            }
            $scope.search()
        })
        $scope.$on('$viewContentLoaded', function() {
            // initialize core components

            // set default layout mode
            $rootScope.settings.layout.pageContentWhite = true
            $rootScope.settings.layout.pageBodySolid = false
            $rootScope.settings.layout.pageSidebarClosed = false
        })
    }
])
    .directive('ownerSearch', function() {
        return {
            restrict: 'E',
            replace: false,
            template,
            controller: 'AdserSearchController'
        }
    })
