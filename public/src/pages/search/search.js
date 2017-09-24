import '../common/common.js'
import '../bookmark/bookmark.js'
import '../common/searcher.js'
import '../../components/sidebar.js'
import template from './search.html'

/* adsearch js */
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User', 'ADS_TYPE', '$uibModal', '$window', 'TIMESTAMP',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User, ADS_TYPE, $uibModal, $window, TIMESTAMP) {
        // 搜索流程:location.search->searchOption->adSearcher.params
        // 将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化

        $scope.settings = settings
        function searchToQuery(option, searcher) {
            $location.search(searcher.searchToQuery(option))
        }
        // 将query转化成搜索参数
        function queryToSearch(option, searcher) {
            searcher.queryToSearch($location.search(), option)
        }
        var adSearcher = $scope.adSearcher = new Searcher()
        adSearcher.checkAndGetMore = function() {
            if (!User.done) {
                adSearcher.getMore('search')
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
                // User.openSearchResultUpgrade();
                adSearcher.isend = true
                return
            }
            adSearcher.getMore('search')
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
        $scope.quickSearch = function(word) {
            // 热词引导搜索，且加上isHotWord标示位用于log统计 --> 废弃,这个标识在正常搜索时不会被清除，导致统计错误
            if (User.done) {
                if (!User.login) {
                    User.openSign()
                    return
                }
                $scope.adSearcher.searchOption.search.text = word
                $scope.search('search')
            }
        }

        $scope.googleQuery = function(typed) {
            // 谷歌联想搜索
            if (typed) {
                Util.googleSuggestQueries(typed).then(function(data) {
                    $scope.suggestions = data.data[1]
                })
            }
        }

        // text为空时就表示没有这个搜索项了
        $scope.initSearch = function() {
            var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption)
            $scope.filterOption = $scope.searchOption.filter
            // 存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
            queryToSearch(option, $scope.adSearcher)
            // 检查是否有track
            Util.trackState($location)
            // 获取热词
            $scope.adSearcher.getHotWord()
            $scope.adSearcher.getAudienceInterest()
        }
        $scope.initSearch()

        $scope.currSearchOption = {}

        $scope.filter = function(option, action) {
            var category = []
            var format = []
            var buttondesc = []
            var freeMin = '2016-01-01'
            var freeMax = moment().subtract(3, 'month').format('YYYY-MM-DD')
            var searchTotalTimes

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
            if (option.state && option.state.length) {
                $scope.adSearcher.addFilter({
                    field: 'state',
                    value: option.state.join(',')
                })
                $scope.currSearchOption.filter.state = option.state.join(',')
            } else {
                $scope.adSearcher.removeFilter('state')
                $scope.currSearchOption.filter.state = "" // 清空国家搜索后，搜索状态还会残留[]，暂时不知道怎么去掉，所以加这句
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
                $scope.currSearchOption.filter.duration = angular.extend($scope.currSearchOption.filter.duration, option.duration)
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
                $scope.currSearchOption.filter.seeTimes = angular.extend($scope.currSearchOption.filter.seeTimes, option.seeTimes)
            }

            // engagementsFilter
            angular.forEach(option.engagements, function(item, key) {
                // 还要排除null值
                if ((item.min === "" || item.min === null) || (item.max === "" || item.max === null)) {
                    $scope.adSearcher.removeFilter(key)
                } else {
                    $scope.adSearcher.addFilter({
                        field: key,
                        min: item.min,
                        max: item.max
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

            // Audience Interest
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

            $scope.isFreeLimitDate = false
            if (User.user.role.plan === 'free') {
                if (($scope.adSearcher.params.where.length > 0) || ($scope.adSearcher.params.keys.length > 0)) {
                    angular.forEach($scope.adSearcher.params.where, function(data) {
                        if (data.field === 'time') {
                            $scope.adSearcher.removeFilter('time')
                        }
                    })
                    // 新增需求，对于免费用户，搜索总数不到10次的给予全部的广告结果
                    searchTotalTimes = User.getPolicy('search_total_times')
                    if (searchTotalTimes[2] > 10) {
                        $scope.adSearcher.addFilter({
                            field: "time",
                            min: freeMin,
                            max: freeMax,
                            role: "free"
                        })
                    } else {
                        $scope.adSearcher.addFilter({
                            field: "time",
                            min: freeMin,
                            max: moment().format('YYYY-MM-DD'),
                            role: "free"
                        })
                    }
                    // 需求变更：
                    // 暂时限定免费注册用户的所有请求都是在三个月之前的数据
                    $scope.isFreeLimitDate = true
                }
            }
            $scope.currSearchOption.filter.category = category.join(',')
            $scope.currSearchOption.filter.format = format.join(',')
            $scope.currSearchOption.filter.callToAction = buttondesc.join(',')
            // console.log(action);
            $scope.adSearcher.filter(action || 'search').then(function() {}, function(res) {
                if (res.data instanceof Object) {
                    /* if(res.data.desc === 'no permission of search'){
                        User.openSign();
                        } */
                    // User.openUpgrade();
                    /* if(res.data.code === -4001){
                            User.openUpgrade();
                        } */
                    switch (res.data.code) {
                    case -4100:
                        User.openSearchResultUpgrade()
                        break
                    case -4199:
                        window.open('/login', "_self")
                        break
                    case -4200:
                    case -5000:
                        SweetAlert.swal(res.data.desc)
                        break
                    }
                    $scope.islegal = false
                    // SweetAlert.swal(res.data.desc);
                } else {
                    SweetAlert.swal(res.statusText)
                }
            })
            // console.log("params", $scope.adSearcher.params);
        }

        $scope.search = function(action) {
            var option = $scope.adSearcher.searchOption
            var keys
            var range = []
            var rangeValue = [] // 用于显示
            keys = $scope.adSearcher.params.keys = []
            // console.log($scope.searchOption.rangeselected);

            // 检查权限，并且应该集中检查权限，才不会搞得逻辑混乱或者状态不一致
            if (!User.can('result_per_search')) {
                SweetAlert.swal("no search permission")
                return
            }
            // 2017-03-09 Liuwc:设计变更，过滤权限分开，同时不检查搜索类型
            // if (action == 'filter' && !User.can('search_filter')) {
            // SweetAlert.swal("no filter permission");
            // return;
            // }
            // if ($scope.filterOption.type) {
            // var type = ADS_TYPE[$scope.filterOption.type];
            // if (!(Number(User.getPolicy('platform').value) & type)) {
            // SweetAlert.swal("type '" + $scope.filterOption.type + "' exceed your permission");
            // return;
            // }
            // }
            // 字符串和域
            $scope.currSearchOption = angular.copy($scope.searchOption) // 保存搜索
            if (option.rangeselected && option.rangeselected.length) {
                angular.forEach(option.rangeselected, function(item) {
                    range.push(item)
                })
            }
            if (option.search.text || range.length) {
                option.search.fields = range.length ? range.join(',') : $scope.Searcher.defSearchFields // 默认值
                // 存在isHotWord便新增isHotWord参数作为后端log统计标记
                if (option.search.isHotWord) {
                    keys.push({
                        string: option.search.text ? option.search.text : "",
                        search_fields: option.search.fields,
                        relation: "Must",
                        isHotWord: option.search.isHotWord
                    })
                } else {
                    keys.push({
                        string: option.search.text ? option.search.text : "",
                        search_fields: option.search.fields,
                        relation: "Must"
                    })
                }
                // alert-warning range显示文本
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
            $scope.currSearchOption.range = rangeValue.join(',')
            $scope.filter($scope.filterOption, action)
            if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 0) {
                $scope.currSearchOption.isdirty = true
            }
            searchToQuery(option, $scope.adSearcher)
        }

        $scope.clearSearch = function() {
            $location.search({})
            $state.reload()
        }
        $scope.showStatics = function() {
            if (!User.can('statics_all')) {
                SweetAlert.swal("you have no permission")
                return
            }

            if (adSearcher.params.keys.length === 0) {
                SweetAlert.swal("you must search first")
                return
            }
            return $uibModal.open({
                templateUrl: 'statics-dlg.html?t=' + TIMESTAMP,
                size: 'lg',
                animation: true,
                controller: ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
                    // 使用独立的搜索器，否则可能影响到原来的广告搜索结果
                    var seacher = new Searcher()
                    seacher.params = angular.copy(adSearcher.params)
                    $scope.statics = {}
                    $scope.queryPromise = seacher.getStatics(seacher.params, "statics")
                    $scope.queryPromise.then(function(res) {
                        var data = $scope.statics = res.data
                        // 饼图
                        $scope.statics.adLangConfig = Util.initPie(data.ad_lang, "AD Language")
                        $scope.statics.adserNameConfig = Util.initPie(data.adser_name, "Adser Names")
                        $scope.statics.adserUsernameConfig = Util.initPie(data.adser_username, "Adser Usernames")
                        $scope.statics.categoryConfig = Util.initPie(data.category, "Category")
                        $scope.statics.mediaTypeConfig = Util.initPie(data.media_type, "Media Type")

                        // button_link, dest_site,link,whyseeads太长，怎么处理？
                        // console.log(res);
                    }, function(res) {
                        $uibModalInstance.dismiss("cancel")
                        Util.hint(res)
                    })
                    $scope.close = function() {
                        $uibModalInstance.dismiss('cancel')
                    }
                }]

            })
        }
        $scope.searchCheck = function(value) {
            if (User.done) {
                var islegal = true
                var isFilterLimit
                var isLengthLimit
                // isNumberLimit = Util.isNumberLimit(value);
                if (!User.login) {
                    User.openSign()
                    islegal = false
                } else {
                    isLengthLimit = Util.isLengthLimit(value)
                    isFilterLimit = Util.isFilterLimit($scope.filterOption, $scope.searchOption)
                    var isAdvanceFilterLimit = Util.isAdvanceFilterLimit($scope.adSearcher.searchOption.filter)
                    // 临时去除对高级过滤的权限检查
                    // isAdvanceFilterLimit = true;
                    /* if(!isNumberLimit) {
                            User.openUpgrade();
                           islegal = false;
                        } */
                    if (!isAdvanceFilterLimit) {
                        $scope.adSearcher.removeFilter('duration_days')
                        $scope.adSearcher.removeFilter('see_times')
                    }
                    if ((User.info.user.role.name === 'Free') && ($scope.filterOption.date.endDate !== null) && !isAdvanceFilterLimit) {
                        // 临时去除free注册用户时间筛选框功能
                        User.openFreeDateLimit()
                        islegal = false
                    } else if (!isAdvanceFilterLimit) {
                        User.openUpgrade()
                        islegal = false
                    } else if ((User.info.user.role.name === 'Free') && ($scope.filterOption.date.endDate !== null)) {
                        User.openFreeDateLimit()
                        islegal = false
                    }

                    if (!isFilterLimit) {
                        User.openUpgrade()
                        islegal = false
                    }
                    if (!isLengthLimit) {
                        SweetAlert.swal("Text Limit: 300 Character Only")
                        islegal = false
                    }
                }
                $scope.islegal = islegal
                return islegal
            } else {
                SweetAlert.swal("getting userinfo, please try again")
            }
        }
        // sort by 过滤free用户也需要加上time限制
        $scope.sortBy = function(action) {
            var freeMin = '2016-01-01'
            var freeMax = moment().subtract(3, 'month').format('YYYY-MM-DD')
            if (User.info.user.role.name === 'Free') {
                $scope.adSearcher.addFilter({
                    field: "time",
                    min: freeMin,
                    max: freeMax,
                    role: "free"
                })
            }
            // 独立的filter，返回的异常上面的与$scope.filter无关
            // 由于select2插件添加点击事件无效，未登录用户点击sort by弹出注册框采用后台返回错误的形式打开
            $scope.adSearcher.filter(action).then(function() {}, function(res) {
                if (res.data instanceof Object) {
                    switch (res.data.code) {
                    case -4199:
                        User.openSign()
                        break
                    }
                }
            })
        }
        $scope.upgrade = function() {
            if (Util.isMobile()) {
                window.open('/mobile_maintain', "_self")
            }
            $state.go("plans")
        }
        $scope.Util = Util
        $scope.User = User
        $scope.Searcher = Searcher
        adSearcher.busy = true
        // 一切的操作应该是在获取到用户信息之后，后面应该优化直接从本地缓存读取
        User.getInfo().then(function() {
            // 根据search参数页面初始化
            $scope.search('search')
        })
    }
])
    .directive('search', function() {
        return {
            restrict: 'E',
            scope: {},
            template,
            replace: false,
            controller: 'AdsearchController'
        }
    })
