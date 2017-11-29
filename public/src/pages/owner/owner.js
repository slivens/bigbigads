import '../common/common.js'
import '../bookmark/bookmark.js'
import '../common/searcher.js'
import '../../components/sidebar.js'
import '../../components/adsearch-card.js'
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
import './owner.scss'
import template from './owner.html'

export default (angular) => {
    return angular.module('owner', ['MetronicApp', 'daterangepicker', 'akoenig.deckgrid', 'infinite-scroll']).controller('AdserController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher', '$filter', 'SweetAlert', '$state', 'Util', '$stateParams', 'User', '$location', function($rootScope, $scope, settings, $http, Searcher, $filter, SweetAlert, $state, Util, $stateParams, User, $location) {
        $scope.settings = settings
        // 搜索流程:location.search->searchOption->adSearcher.params
        // 将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
        function searchToQuery(option, searcher) {
            $location.search(searcher.searchToQuery(option, searcher.params.sort.field))
        }
        // 将query转化成搜索参数
        function queryToSearch(option, searcher) {
            searcher.queryToSearch($location.search(), option)
        }
        $scope.adser = {
            name: $stateParams.name,
            username: $stateParams.adser
        }
        var adSearcher = $scope.adSearcher = new Searcher()
        $scope.isFreeze = false
        adSearcher.checkAndGetMore = function() {
            if (!User.done) {
                adSearcher.getMore('adser')
                return
            }
            var policy = User.getPolicy('result_per_search')
            if (!policy) {
                SweetAlert.swal("no permission for get more")
                adSearcher.isend = true
                return
            }
            // console.log("max search result:", policy.value, adSearcher.params.limit[0] + adSearcher.params.limit[1]);
            if (adSearcher.params.limit[0] + adSearcher.params.limit[1] >= policy.value) {
            // SweetAlert.swal("you reached search result limit(" + policy.value + ")");
                adSearcher.isend = true
                return
            }
            adSearcher.getMore('adser').catch(function(res) {
                if (res.data instanceof Object) {
                    switch (res.data.code) {
                    case -4100:
                        $scope.isRestrict = true
                        User.openSearchResultUpgrade()
                        break
                    case -5000:
                        $scope.isFreeze = true
                        SweetAlert.swal(res.data.desc)
                        break
                    default:
                        break
                    }
                    $scope.islegal = false
                } else {
                    SweetAlert.swal(res.statusText)
                }
            })
        }
        // $scope.adSearcher.search($scope.adSearcher.defparams, true);
        $scope.reverseSort = function() {
            if (!User.can('search_sortby')) {
                SweetAlert.swal("no sort permission")
                return
            }
            $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order
            $scope.adSearcher.filter()
        }

        // text为空时就表示没有这个搜索项了
        $scope.initSearch = function() {
            var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption)
            $scope.filterOption = $scope.searchOption.filter
            // 存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
            queryToSearch(option, $scope.adSearcher)
            $scope.adSearcher.getHotWord()
            $scope.adSearcher.getAudienceInterest()
        }
        $scope.initSearch()
        $scope.currSearchOption = {}

        $scope.filter = function(option, action) {
            var category = []
            var format = []
            var buttondesc = []
            var freeMin = '2016-08-23'
            var freeMax = moment().subtract(3, 'month').format('YYYY-MM-DD')

            // 广告类型
            if (!$scope.filterOption.type) {
                $scope.adSearcher.removeFilter("ads_type")
            } else {
                $scope.adSearcher.addFilter({
                    field: 'ads_type',
                    value: $scope.filterOption.type
                })
            }
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
                    value: option.lang.join(',')
                })
                $scope.currSearchOption.filter.lang = option.lang.join(',')
            } else {
                $scope.adSearcher.removeFilter('ad_lang')
            }
            // 国家
            if (option.state) {
                $scope.adSearcher.addFilter({
                    field: 'state',
                    value: option.state.join(',')

                })
                $scope.currSearchOption.filter.state = option.state.join(',')
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

            // Audience Age
            if (option.audienceAge && option.audienceAge.length) {
                $scope.adSearcher.addFilter({
                    field: 'audience_age',
                    value: option.audienceAge.join(',')
                })
                $scope.currSearchOption.filter.audienceAge = option.audienceAge.join(',')
            } else {
                $scope.adSearcher.removeFilter("audience_age")
            }

            // Audience Gender
            if (option.audienceGender && option.audienceGender.length) {
                $scope.adSearcher.addFilter({
                    field: 'audience_gender',
                    value: option.audienceGender
                })
                $scope.currSearchOption.filter.audienceGender = option.audienceGender
            } else {
                $scope.adSearcher.removeFilter("audience_gender")
            }

            if (option.audienceInterest && option.audienceInterest.length) {
                $scope.adSearcher.addFilter({
                    field: 'audience_interest',
                    value: option.audienceInterest.join(',')
                })
                $scope.currSearchOption.filter.audienceInterest = option.audienceInterest.join(',')
            } else {
                $scope.adSearcher.removeFilter("audience_interest")
            }

            // objective
            if (option.objective && option.objective.length) {
                $scope.adSearcher.addFilter({
                    field: 'objective',
                    value: option.objective.join(',')
                })
                $scope.currSearchOption.filter.objective = option.objective.join(',')
            } else {
                $scope.adSearcher.removeFilter("objective")
            }

            // 日期范围
            if (!option.firstSee.startDate || !option.firstSee.endDate) {
                $scope.adSearcher.removeFilter('first_see')
            } else {
                startDate = option.firstSee.startDate.format('YYYY-MM-DD')
                endDate = option.firstSee.endDate.format('YYYY-MM-DD')
                $scope.adSearcher.addFilter({
                    field: "first_see",
                    min: startDate,
                    max: endDate
                })
            }

            // sort by 体现在页面currSearchOption上
            if (option.sort) $scope.currSearchOption.sort = option.sort

            $scope.isFreeLimitDate = false
            if (User.user.role.plan === 'free') {
                if (($scope.adSearcher.params.where.length > 0) || ($scope.adSearcher.params.keys.length > 0)) {
                    angular.forEach($scope.adSearcher.params.where, function(data) {
                        if (data.field === 'time') {
                            $scope.adSearcher.removeFilter('time')
                        }
                    })
                    $scope.adSearcher.addFilter({
                        field: "time",
                        min: freeMin,
                        max: freeMax,
                        role: "free"
                    })
                    // 需求变更：
                    // 暂时限定免费注册用户的所有请求都是在三个月之前的数据
                    $scope.isFreeLimitDate = true
                }
            }
            var liteFreeMin = '2016-01-01'
            var liteFreeMax = moment().subtract(14, 'days').format('YYYY-MM-DD')
            if (User.user.role.plan === 'lite') {
                if (($scope.adSearcher.params.where.length > 0) || ($scope.adSearcher.params.keys.length > 0) || $scope.adSearcher.params.sort.field != 'default') {
                    angular.forEach($scope.adSearcher.params.where, function(data) {
                        if (data.field === 'time') {
                            $scope.adSearcher.removeFilter('time')
                        }
                    })
                    // 新增需求，对于免费用户，搜索总数不到10次的给予全部的广告结果
                    $scope.adSearcher.addFilter({
                        field: "time",
                        min: liteFreeMin,
                        max: liteFreeMax,
                        role: "free"
                    })
                    // 暂时限定lite用户的所有请求都是在14天之前的数据
                    $scope.isFreeLimitDate = true
                }
            }
            $scope.currSearchOption.category = category.join(',')
            $scope.currSearchOption.format = format.join(',')
            $scope.currSearchOption.callToAction = buttondesc.join(',')
            action = 'adser'
            $scope.isRestrict = false
            $scope.adSearcher.filter(action || 'adser').catch(function(res) {
                if (res.data instanceof Object) {
                    switch (res.data.code) {
                    case -4100:
                        $scope.isRestrict = true
                        User.openSearchResultUpgrade()
                        break
                    case -5000:
                        $scope.isFreeze = true
                        SweetAlert.swal(res.data.desc)
                        break
                    default:
                        break
                    }
                } else {
                    SweetAlert.swal(res.statusText)
                }
            })
        // console.log("params", $scope.adSearcher.params)
        }
        $scope.search = function(action) {
            var option = $scope.adSearcher.searchOption
            var keys
            var range = []
            var rangeValue = []
            keys = $scope.adSearcher.params.keys = []

            // 检查权限，并且应该集中检查权限，才不会搞得逻辑混乱或者状态不一致
            if (!User.can('result_per_search')) {
                SweetAlert.swal("no search permission")
                return
            }
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
                    string: option.domain.text,
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
            $scope.filter($scope.filterOption, action)
            if (User.info.user.role.plan === 'free') {
                if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 2 || $scope.adSearcher.params.sort.field != 'default') {
                    $scope.currSearchOption.isdirty = true
                }
            } else {
                if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 1 || $scope.adSearcher.params.sort.field != 'default') {
                    $scope.currSearchOption.isdirty = true
                }
            }
            searchToQuery(option, $scope.adSearcher)
        }

        $scope.clearSearch = function() {
            $location.search({})
            $state.reload()
        }
        $scope.searchCheck = function(isSend) {
            $scope.illeageFilterParams = {}
            $scope.currIlleageOption = {}
            if (User.done) {
                var islegal = true
                var isFilterLimit
                var isLengthLimit
                var isAdvanceFilterLimit
                var illeageParams = {}
                var illeageWhere = []
                var isAdPositionLimit
                var isDateLimit
                var isSearchModeLimit
                var isSortLimit
                var value = $scope.adSearcher.searchOption.search.text
                var currIlleageOption = {}
                // isNumberLimit = Util.isNumberLimit(value);
                if (!User.login) {
                    User.openSign()
                    islegal = false
                } else {
                    // 需求变更，所有权限公开，除去tracking 和 affiliate 过滤数据过少不公开
                    // 收集无权限过滤的选项并发送到后端记录
                    // 去除原本对date 时间过滤的文案提示，统一提示为账号升级
                    // 紧急需求，本应该可以在Util把权限检查完成，只返回是否合法，放到下次在develop组件式中优化
                    // 检查搜索词长度
                    isLengthLimit = Util.isLengthLimit(value)
                    // 检查基本过滤项
                    isFilterLimit = Util.isFilterLimit($scope.filterOption, $scope.searchOption)
                    // 检查高级过滤项
                    isAdvanceFilterLimit = Util.isAdvanceFilterLimit($scope.adSearcher.searchOption.filter)
                    // 检查Ad Position过滤项,单独检查是因为与其他过滤项数据格式不一致,还需要对每个项做单独权限判断
                    isAdPositionLimit = Util.isAdPosionFilterLimit(settings.searchSetting.adsTypes, $scope.filterOption.type)
                    // 检查日期过滤项
                    isDateLimit = Util.isDateLimit($scope.filterOption)
                    // 检查search mode过滤项,单独检查是因为与其他过滤项数据格式不一致
                    isSearchModeLimit = Util.isSearchModeLimit($scope.searchOption, settings.searchSetting.rangeList, value)
                    // 检查sort by
                    isSortLimit = Util.isSortLimit($scope.adSearcher.params.sort.field, settings.searchSetting.orderBy)
                    // console.log('isSortLimit', isSortLimit)
                    // console.log('isFilterLimit', isFilterLimit)
                    // console.log('isAdvanceFilterLimit', isAdvanceFilterLimit)
                    // console.log('isAdPositionLimit', isAdPositionLimit)
                    // console.log('isDateLimit', isDateLimit)
                    // console.log('isSearchModeLimit', isSearchModeLimit)
                    if (!isFilterLimit.flag || !isAdvanceFilterLimit.flag || !isDateLimit.flag || !isAdPositionLimit.flag || !isSearchModeLimit.flag || !isSortLimit.flag) {
                        islegal = false
                    }
                    if (!isLengthLimit) {
                        SweetAlert.swal("Text Limit: 300 Character Only")
                        islegal = false
                    }
                    if (!islegal) {
                        if (!isFilterLimit.flag) {
                            angular.forEach(isFilterLimit.params, function(value) {
                                illeageWhere.push(value)
                            })
                            currIlleageOption = angular.extend(currIlleageOption, isFilterLimit.currIlleageOption)
                        }
                        if (!isAdvanceFilterLimit.flag) {
                            angular.forEach(isAdvanceFilterLimit.params, function(value) {
                                illeageWhere.push(value)
                            })
                            currIlleageOption = angular.extend(currIlleageOption, isAdvanceFilterLimit.currIlleageOption)
                        }
                        if (!isAdPositionLimit.flag) {
                            angular.forEach(isAdPositionLimit.params, function(value) {
                                illeageWhere.push(value)
                            })
                            currIlleageOption = angular.extend(currIlleageOption, isAdPositionLimit.currIlleageOption)
                        }
                        if (!isDateLimit.flag) {
                            angular.forEach(isDateLimit.params, function(value) {
                                illeageWhere.push(value)
                            })
                            currIlleageOption = angular.extend(currIlleageOption, isDateLimit.currIlleageOption)
                        }
                        if (!isSortLimit.flag) {
                            currIlleageOption = angular.extend(currIlleageOption, isSortLimit.currIlleageOption)
                            illeageParams.sort = isSortLimit.sort
                            $scope.illeageFilterParams.sort = isSortLimit.sort
                        }
                        $scope.currIlleageOption = angular.extend($scope.currIlleageOption, currIlleageOption)
                        illeageParams.where = illeageWhere
                        if (!isSearchModeLimit.flag) {
                            illeageParams.key = isSearchModeLimit.key
                        }
                        if (isSend) {
                            Util.unauthorisedFilterRequest(illeageParams)
                            User.openUpgrade(currIlleageOption)
                        }
                        $scope.illeageFilterParams.where = illeageParams.where
                    }
                    $scope.islegal = islegal
                    return islegal
                }
            } else {
                SweetAlert.swal("getting userinfo, please try again")
            }
        }
        $scope.User = User
        $scope.Searcher = Searcher

        $scope.adSearcher.params.where.push({
            field: 'adser_username',
            value: $stateParams.adser
        })
        $scope.checkBeforeSort = function() {
            // 使用sort by 前权限检查
            var illeageParams = {}
            var isLimit = false
            // 需要在此检查一次是否使用了其他过滤，但是不需要打开提示框，由点击sort by统一打印
            $scope.searchCheck(false)
            if ($scope.illeageFilterParams.where || $scope.illeageFilterParams.key || $scope.illeageFilterParams.sort) {
                illeageParams.where = $scope.illeageFilterParams.where ? $scope.illeageFilterParams.where : ''
                illeageParams.key = $scope.illeageFilterParams.key ? $scope.illeageFilterParams.key : ''
                illeageParams.sort = $scope.illeageFilterParams.sort ? $scope.illeageFilterParams.sort : ''
                isLimit = true
            }
            if (isLimit) {
                User.openUpgrade($scope.currIlleageOption)
                Util.unauthorisedFilterRequest(illeageParams)
                return false
            }
            return true
        }
        $scope.sortBy = function(action) {
            if (!User.done) return false
            if (!User.login) {
                User.openSign()
                return false
            }
            // var freeMin = '2016-01-01'
            // var freeMax = moment().subtract(3, 'month').format('YYYY-MM-DD')
            var checkBeforeSortResult
            // var searchTotalTimes
            checkBeforeSortResult = $scope.checkBeforeSort()
            if (checkBeforeSortResult) {
                $scope.filterOption.sort = $scope.adSearcher.params.sort.field
                $scope.search(action)
            }
        }
        $scope.upgrade = function() {
            if (Util.isMobile()) {
                window.open('/mobile_maintain', "_self")
            }
            $state.go("plans")
        }
        // 一切的操作应该是在获取到用户信息之后，后面应该优化直接从本地缓存读取
        User.getInfo().then(function() {
        // 根据search参数页面初始化
            if (!User.login) {
                window.open("/login", "_self")
            }
            $scope.search('adser')
        })
    // $scope.adSearcher.filter();
    }])
        .directive('owner', function() {
            return {
                restrict: 'E',
                scope: {},
                template,
                replace: false,
                controller: 'AdserController'
            }
        })
}
