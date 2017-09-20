import './analysis.scss'
import './adseranalysis.scss'
import '../common/common.js'
import '../bookmark/bookmark.js'
import downsize from 'downsize'

angular.module('MetronicApp').factory('Searcher', ['$http', '$timeout', 'settings', 'ADS_TYPE', 'ADS_CONT_TYPE', '$q', 'Util', '$filter',
    function($http, $timeout, settings, ADS_TYPE, ADS_CONT_TYPE, $q, Util, $filter) {
        // opt = {searchType:'adser', url:'/forward/adserSearch'}
        var searcher = function(opt) {
            var vm = this
            vm.opt = opt
            vm.defparams = {
                "search_result": opt && opt.searchType ? opt.searchType : "ads",
                "sort": {
                    "field": "last_view_date",
                    "order": 1
                },
                "where": [],
                "limit": [
                    0,
                    10
                ],
                "is_why_all": 1,
                "topN": 10,
                "is_stat": 0
            }
            if (opt && opt.limit) {
                vm.defparams.limit = opt.limit
            }
            searcher.defSearchFields = searcher.prototype.defSearchFields = "message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink,event_id"
            searcher.defFilterOption = searcher.prototype.defFilterOption = {
                type: "",
                date: {
                    startDate: null,
                    endDate: null
                },
                // 新增first see时间过滤
                firstSee: {
                    startDate: null,
                    endDate: null
                },
                category: settings.searchSetting.categoryList,
                format: settings.searchSetting.formatList,
                buttondesc: settings.searchSetting.buttondescList,
                duration: {
                    from: settings.searchSetting.durationRange[0],
                    to: settings.searchSetting.durationRange[1]
                },
                seeTimes: {
                    from: settings.searchSetting.seeTimesRange[0],
                    to: settings.searchSetting.seeTimesRange[1]
                },
                // engagementsFilter
                engagements: {
                    likes: {
                        min: "",
                        max: ""
                    },
                    shares: {
                        min: "",
                        max: ""
                    },
                    comments: {
                        min: "",
                        max: ""
                    },
                    views: {
                        min: "",
                        max: ""
                    },
                    engagements: {
                        min: "",
                        max: ""
                    }

                },
                isEngagementsDirty: function() {
                    var isDirty = false
                    // #issues 11 修复选择0 - 180 无效问题
                    angular.forEach(this.engagements, function(item, index) {
                        if (((item.min && (item.min != searcher.defFilterOption.engagements[index].min)) && (item.max && (item.max != searcher.defFilterOption.engagements[index].max))) || (item.min === 0 || item.max === 180))
                            isDirty = true
                    })
                    return isDirty
                },

                isDurationDirty: function() {
                    // 这里引用this,关键是要对this这个指针有透彻的理解，this是会变的。当函数作为对象的属性时，this就是对象，即当以obj.isDurationDirty()，this就是obj。如果fn=obj.isDurationDirty();fn();那么this就是window。当函数出问题时一定要检查下this。
                    if (this.duration.from == searcher.defFilterOption.duration.from &&
                        this.duration.to == searcher.defFilterOption.duration.to)
                        return false
                    return true
                },
                isSeeTimesDirty: function() {
                    if (this.seeTimes.from == searcher.defFilterOption.seeTimes.from &&
                        this.seeTimes.to == searcher.defFilterOption.seeTimes.to)
                        return false
                    return true
                }

            }
            vm.defSearchOption = {
                range: settings.searchSetting.rangeList,
                search: {
                    text: null,
                    fields: searcher.defSearchFields
                },
                domain: {
                    text: null,
                    exclude: false
                },
                audience: {
                    text: null,
                    exclude: false
                },
                filter: searcher.defFilterOption
            }
            vm.params = angular.copy(vm.defparams)
            vm.oldParams = null
            vm.ADS_CONT_TYPE = ADS_CONT_TYPE
            vm.pageCount = settings.searchSetting.pageCount
            vm.ads = {
                total_count: 0
            }
            vm.isend = false
            vm.isNoResult = false

            vm.search = function(params, clear, action) {
                var defer = $q.defer()
                // 获取广告搜索信息
                var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/forward/adsearch')
                if (action) {
                    params.action = action
                } else {
                    delete params.action
                }
                vm.busy = true
                $http.post(
                    searchurl,
                    params
                ).then(function(res) {
                    if (res.error) {
                        vm.isend = true
                        defer.reject(res)
                        return
                    }
                    vm.isend = vm.isNoResult = res.data.is_end
                    if (clear && vm.isend) { // 检测到结束就清空搜索结果
                        vm.ads = []
                        vm.ads.total_count = 0
                        if (res.data.hasOwnProperty('all_total_count')) {
                            vm.ads.all_total_count = res.data.all_total_count
                        }
                    }
                    if (res.data.count) {
                        angular.forEach(res.data.ads_info, function(value, key) {
                            /*
                            * 按字符串长度对原有数据进行裁剪，
                            * 1）裁剪后的 + see more
                            * 2）对于少于130字符的消息理应不做处理，但也可能会有连接中存在很长的href
                            * 3）规避message为空的情况
                            * 4）裁剪字符会出现不完整单词，最后一个emoji表情符号出现一场
                            * 5）按单词裁剪，中文、日本等处理不理想
                            * 6) 后续有待优化
                            */
                            if (value.message) {
                                value.excerpt = downsize(value.message, {characters: 130})
                                value.showSeeMore = value.message.length !== value.excerpt.length
                            }
                            if (value.type == vm.ADS_CONT_TYPE.CAROUSEL) {
                                // 暂时无法保证数据端会出现某个字段json解析失败，全部做异常抛出。
                                // 但是也把数据端的弊端屏蔽了，打算结合开发模式和生产模式，生产模式不做异常
                                // 抛出，本地能知道数据源问题。
                                try {
                                    value.watermark = JSON.parse(value.watermark)
                                    value.link = JSON.parse(value.link)
                                    value.buttonlink = JSON.parse(value.buttonlink)
                                    value.buttondesc = JSON.parse(value.buttondesc)
                                    value.name = JSON.parse(value.name)
                                    value.description = JSON.parse(value.description)
                                    value.local_picture = JSON.parse(value.local_picture)
                                    // 数据端出现了一个carousel的caption为json的数据
                                    if (value.caption.indexOf("[") === 0) {
                                        value.caption = JSON.parse(value.caption)
                                        // carousel页面无法引用到AdsearchController内的Util.isArray方法,不得已在AdsearchController内多加了个card.isCaptionJson的标示
                                        value.isCaptionJson = true
                                    }
                                } catch (err) {
                                    console.log(err)
                                }
                                // if (value.snapshot && value.snapshot != "")
                                //      value.snapshot = JSON.parse(value.snapshot);
                            } else if (value.type == vm.ADS_CONT_TYPE.CANVAS) {
                                try {
                                    value.link = JSON.parse(value.link)
                                    value.local_picture = JSON.parse(value.local_picture)
                                } catch (err) {
                                    console.log(err)
                                }
                                if (vm.getAdsType(value, vm.ADS_TYPE.rightcolumn)) {
                                    try {
                                        value.watermark = JSON.parse(value.watermark)
                                    } catch (err) {
                                        console.log(err)
                                    }
                                }
                            } else if (value.type == vm.ADS_CONT_TYPE.SINGLE_VIDEO) {
                                try {
                                    value.local_picture = JSON.parse(value.local_picture)
                                } catch (err) {
                                    console.log(err)
                                }
                            }
                        })

                        if (opt && opt.searchType == "adser") {
                            if (clear) {
                                vm.ads = res.data
                            } else {
                                vm.ads.adser_info = vm.ads.adser.concat(res.data.adser)
                            }
                        } else {
                            if (clear || vm.ads.total_count === 0) {
                                vm.ads = res.data
                            } else {
                                vm.ads.ads_info = vm.ads.ads_info.concat(res.data.ads_info)
                            }
                        }
                        defer.resolve(vm.ads)
                    } else {
                        defer.reject(vm.ads)
                    }
                    // console.log(res.data);
                }, function(res) {
                    vm.isend = true
                    defer.reject(res)
                    // console.log(res);
                }).finally(function() {
                    $timeout(function() {
                        vm.busy = false
                    }, 200)
                })
                return defer.promise
            }

            vm.getMore = function(action) {
                if (vm.busy)
                    return
                vm.params.limit[0] += settings.searchSetting.pageCount
                vm.search(vm.params, false, action)
            }
            vm.filter = function(action) {
                var promise
                if (vm.params == vm.oldParams)
                    return
                vm.params.limit[0] = 0
                promise = vm.search(vm.params, true, action)
                vm.oldParams = angular.copy(vm.params)
                return promise
            }
            vm.addFilter = function(obj) {
                var i
                // where必须带ads_type，否则会出错
                for (i = 0; i < vm.params.where.length; ++i) {
                    if (vm.params.where[i].field == obj.field) {
                        break
                    }
                }
                if (i == vm.params.where.length) {
                    vm.params.where.push(obj)
                } else {
                    vm.params.where[i] = obj
                }
            }
            vm.removeFilter = function(name) {
                var i
                // where必须带ads_type，否则会出错
                for (i = 0; i < vm.params.where.length; ++i) {
                    if (vm.params.where[i].field == name) {
                        vm.params.where.splice(i, 1)
                        break
                    }
                }
            }
            vm.getStatics = function(params, action) {
                var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/forward/adsearch')
                var promise
                if (action) {
                    params.action = action
                } else {
                    delete params.action
                }
                params.is_stat = 1
                promise = $http.post(searchurl, params)
                promise.then(function(res) {
                    // console.log(res);
                })
                return promise
            }
            /* 清空高级过滤的输入框 */
            vm.clearValue = function(value) {
                // $scope.filterOption.engagements.likes.min=$scope.filterOption.engagements.likes.max='';
                value.min = value.max = ""
            }
            // 请求热词数据
            vm.getHotWord = function() {
                var url = settings.remoteurl + '/' + 'hotword'
                $http.get(url, {}).then(function(data) {
                    vm.hotword = data.data
                })
            }
            // 请求兴趣受众数据
            // Todo:临时做法，后续会优化, 插件未调试成功
            vm.getAudienceInterest = function() {
                var url = settings.remoteurl + '/' + 'audience-interest'
                $http.get(url, {}).then(function(data) {
                    vm.audienceInterest = data.data
                })
            }
        }
        searcher.ADS_TYPE = searcher.prototype.ADS_TYPE = ADS_TYPE
        // 函数的静态方法以及对象的方法
        searcher.getAdsType = searcher.prototype.getAdsType = function(item, type) {
            if (item.show_way & type)
                return true
            return false
        }
        // 将搜索过滤项转换成location的参数
        searcher.searchToQuery = searcher.prototype.searchToQuery = function(option) {
            var query = {}

            if (option.search.text)
                query.searchText = option.search.text
            if (angular.isDefined(option.rangeselected) && option.rangeselected.length) {
                query.searchFields = option.rangeselected.join(',')
            }
            // if (option.search.fields != searcher.defSearchFields)
            // query.searchFields = option.search.fields;
            if (option.filter.date.startDate && option.filter.date.endDate) {
                query.startDate = option.filter.date.startDate.format('YYYY-MM-DD')
                query.endDate = option.filter.date.endDate.format('YYYY-MM-DD')
            }
            if (option.filter.type) {
                query.type = option.filter.type
            }
            if (angular.isDefined(option.filter.lang) && option.filter.lang.length) {
                query.lang = option.filter.lang.join(',')
            }
            if (option.filter.state) {
                query.state = option.filter.state
            }
            if (option.domain.text) {
                query.domain = JSON.stringify(option.domain)
            }
            if (option.audience.text) {
                query.audience = JSON.stringify(option.audience)
            }

            // category, format, buttondesc,engagement
            if (option.filter.categoryString) {
                query.category = option.filter.categoryString
            }
            if (option.filter.formatString) {
                query.format = option.filter.formatString
            }
            if (option.filter.buttondescString) {
                query.buttondesc = option.filter.buttondescString
            }
            if (option.filter.isEngagementsDirty()) {
                query.engagements = JSON.stringify(option.filter.engagements)
            }
            if (option.filter.isDurationDirty()) {
                query.duration = JSON.stringify(option.filter.duration)
            }
            if (option.filter.isSeeTimesDirty()) {
                query.seeTimes = JSON.stringify(option.filter.seeTimes)
            }
            if (option.filter.tracking) {
                query.tracking = option.filter.tracking
            }
            if (option.filter.affiliate) {
                query.affiliate = option.filter.affiliate
            }
            if (option.filter.ecommerce) {
                query.ecommerce = option.filter.ecommerce
            }
            if (option.filter.firstSee.startDate && option.filter.firstSee.endDate) {
                query.firstSeeStartDate = option.filter.firstSee.startDate.format('YYYY-MM-DD')
                query.firstSeeEndDate = option.filter.firstSee.endDate.format('YYYY-MM-DD')
            }
            if (option.filter.audienceAge) {
                query.audienceAge = option.filter.audienceAge
            }
            if (option.filter.audienceGender) {
                query.audienceGender = option.filter.audienceGender
            }
            if (option.filter.audienceInterest) {
                query.audienceInterest = option.filter.audienceInterest
            }
            if (option.filter.objective) {
                query.objective = option.filter.objective
            }
            return query
        }
        // 将location的参数转换成搜索过滤项
        searcher.queryToSearch = searcher.prototype.queryToSearch = function(locaionSearch, option) {
            var search = locaionSearch
            var duration
            var engagements
            var seeTimes
            option.rangeselected = []
            if (search.searchText) {
                option.search.text = search.searchText
            }
            if (search.searchFields && search.searchFields != searcher.defSearchFields) {
                var range = search.searchFields.split(',')
                var advertisement = {
                    "description": "description",
                    "name": "name",
                    "caption": "caption",
                    "message": "message"
                }
                var url = {
                    "link": "link",
                    "buttonlink": "buttonlink",
                    "dest_site": "dest_site"
                }
                var advertiser = {
                    "adser_name": "adser_name",
                    "adser_username": "adser_username"
                }
                var audience = {
                    "whyseeads": "whyseeads",
                    "whyseeads_all": "whyseeads_all"
                }
                var isSelectAdvertisement = false
                var isSelectUrl = false
                var isSelectAdvertiser = false
                var isSelectAudience = false
                // 使用indexOf方法判断不准确，刷新的时候会造成一个项变成两个项
                angular.forEach(range, function(item) {
                    if (advertisement.hasOwnProperty(item)) {
                        isSelectAdvertisement = true
                    }
                    if (advertiser.hasOwnProperty(item)) {
                        isSelectAdvertiser = true
                    }
                    if (url.hasOwnProperty(item)) {
                        isSelectUrl = true
                    }
                    if (audience.hasOwnProperty(item)) {
                        isSelectAudience = true
                    }
                })
                if (isSelectAdvertisement)
                    option.rangeselected.push("description,name,caption,message")
                if (isSelectAdvertiser)
                    option.rangeselected.push("adser_name,adser_username")
                if (isSelectUrl)
                    option.rangeselected.push("link,buttonlink,dest_site")
                if (isSelectAudience)
                    option.rangeselected.push("whyseeads,whyseeads_all")
            }
            if (search.startDate && search.endDate) {
                option.filter.date.startDate = moment(search.startDate, 'YYYY-MM-DD')
                option.filter.date.endDate = moment(search.endDate, 'YYYY-MM-DD')
            }
            if (search.type) {
                option.filter.type = search.type
            }
            if (search.lang) {
                option.filter.lang = search.lang.split(",")
            }
            if (search.state) {
                option.filter.state = search.state instanceof Array ? search.state : search.state.split(",")
            }
            if (search.domain) {
                option.domain = JSON.parse(search.domain)
            }
            if (search.audience) {
                option.audience = JSON.parse(search.audience)
            }
            if (search.category) {
                Util.matchkey(search.category, option.filter.category)
            }
            if (search.format) {
                option.filter.formatSelected = search.format.split(",")
                // Util.matchkey(search.format, option.filter.format);
            }
            if (search.buttondesc) {
                option.filter.callToAction = search.buttondesc instanceof Array ? search.buttondesc : search.buttondesc.split(",")
                // Util.matchkey(search.buttondesc, option.filter.buttondesc);
            }
            if (search.tracking) {
                option.filter.tracking = search.tracking instanceof Array ? search.tracking : search.tracking.split(",")
            }
            if (search.affiliate) {
                option.filter.affiliate = search.affiliate instanceof Array ? search.affiliate : search.affiliate.split(",")
            }
            if (search.ecommerce) {
                option.filter.ecommerce = search.ecommerce instanceof Array ? search.ecommerce : search.ecommerce.split(",")
            }
            if (search.firstSeeStartDate && search.firstSeeEndDate) {
                option.filter.firstSee.startDate = moment(search.firstSeeStartDate, 'YYYY-MM-DD')
                option.filter.firstSee.endDate = moment(search.firstSeeEndDate, 'YYYY-MM-DD')
            }
            if (search.audienceAge) {
                option.filter.audienceAge = search.audienceAge instanceof Array ? search.audienceAge : search.audienceAge.split(",")
            }
            if (search.audienceGender) {
                // 性别受众为单选，使用split(",")处理会出错
                option.filter.audienceGender = search.audienceGender
            }
            if (search.audienceInterest) {
                option.filter.audienceInterest = search.audienceInterest instanceof Array ? search.audienceInterest : search.audienceInterest.split(",")
            }
            if (search.objective) {
                option.filter.objective = search.objective instanceof Array ? search.objective : search.objective.split(",")
            }
            // #issues 11 连带发现的问题，刷新参数未保存
            if (search.duration) {
                duration = JSON.parse(search.duration)
                option.filter.duration = angular.extend(option.filter.duration, duration)
            }
            if (search.engagements) {
                engagements = JSON.parse(search.engagements)
                option.filter.engagements = angular.extend(option.filter.engagements, engagements)
            }
            if (search.seeTimes) {
                seeTimes = JSON.parse(search.seeTimes)
                option.filter.seeTimes = angular.extend(option.filter.seeTimes, seeTimes)
            }
        }
        return searcher
    }
])
/* adsearch js */
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User', 'ADS_TYPE', '$uibModal', '$window', 'TIMESTAMP',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User, ADS_TYPE, $uibModal, $window, TIMESTAMP) {
        // 搜索流程:location.search->searchOption->adSearcher.params
        // 将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化

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
        $scope.$on('$viewContentLoaded', function() {
            // initialize core components

            // set default layout mode
            $rootScope.settings.layout.pageContentWhite = true
            $rootScope.settings.layout.pageBodySolid = false
            $rootScope.settings.layout.pageSidebarClosed = false
        })
    }
])
    .controller('AdserController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher', '$filter', 'SweetAlert', '$state', 'Util', '$stateParams', 'User', '$location', function($rootScope, $scope, settings, $http, Searcher, $filter, SweetAlert, $state, Util, $stateParams, User, $location) {
        // 搜索流程:location.search->searchOption->adSearcher.params
        // 将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
        function searchToQuery(option, searcher) {
            $location.search(searcher.searchToQuery(option))
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
            adSearcher.getMore('adser')
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
            $scope.currSearchOption.category = category.join(',')
            $scope.currSearchOption.format = format.join(',')
            $scope.currSearchOption.callToAction = buttondesc.join(',')
            action = 'adser'
            $scope.adSearcher.filter(action || 'adser').then(function() {}, function(res) {
                if (res.data instanceof Object) {
                    // SweetAlert.swal(res.data.desc);
                } else {
                    SweetAlert.swal(res.statusText)
                }
            })
            console.log("params", $scope.adSearcher.params)
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
                if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 2) {
                    $scope.currSearchOption.isdirty = true
                }
            } else {
                if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 1) {
                    $scope.currSearchOption.isdirty = true
                }
            }
            searchToQuery(option, $scope.adSearcher)
        }

        $scope.clearSearch = function() {
            $location.search({})
            $state.reload()
        }
        $scope.searchCheck = function(value) {
            if (User.done) {
                var islegal = true
                var isFilterLimit
                isFilterLimit = Util.isFilterLimit($scope.filterOption, $scope.searchOption)
                var isAdvanceFilterLimit = Util.isAdvanceFilterLimit($scope.filterOption)
                if (!isFilterLimit) {
                    User.openUpgrade()
                    islegal = false
                }
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

                $scope.islegal = islegal
                return islegal
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
            $scope.adSearcher.filter(action)
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
    .controller('AdAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$window', '$http', 'Util', 'User', '$q',
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
            $scope.id = $stateParams.id
            $scope.adSearcher.addFilter({
                field: 'ads_id',
                value: $scope.id
            })
            $scope.adSearcher.params.ads_detail = 1
            var promise = $scope.adSearcher.filter("analysis")
            var arr
            var countryPromise = $http.get('/app/data/map-country.json').then(function(res) {
                vm.countries = res.data
                return res.data
            })
            $q.all([countryPromise, promise]).then(function(res) {
                // 只取首条消息
                if (!User.done)
                    return
                if (!User.login) {
                    window.open('/login', '_self')
                }
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
                    // $scope.card.whyseeads = $scope.card.whyseeads.split('\n');
                    $scope.card.whyseeads = JSON.parse($scope.card.whyseeads)

                    // 计算interesting的总数
                    if ($scope.card.whyseeads.interests) {
                        var interestsCount = 0
                        $scope.interestsArr = []
                        for (key in $scope.card.whyseeads.interests) {
                            $scope.interestsArr.push({
                                'name': key,
                                'value': $scope.card.whyseeads.interests[key]
                            })

                            interestsCount += $scope.card.whyseeads.interests[key]
                        }
                        /*
                         * 对数组的value进行排序,具体用法见common.js
                         * srrSort(整个数组，要根据排序的名称，正序0/逆序1)
                         */
                        $scope.interestsArr = Util.arrSort($scope.interestsArr, "value", 1)
                        $scope.interestsArr.count = interestsCount
                    }
                    // 广告详情-性别比例
                    if ($scope.card.whyseeads.gender) {
                        var asdGender = $scope.card.whyseeads.gender
                        $scope.card.gnederPieCharts = Util.pieChartsConfig([['Male', asdGender[0]], ['Female', asdGender[1]]], '60%', ['#7cb5ec', '#ee5689'])
                    } else $scope.card.gnederPieCharts = false
                    // 广告详情-年龄分布
                    if ($scope.card.whyseeads.age) {
                        var arr1 = $scope.card.whyseeads.age.map(function(v) { return v[0] })
                        var arr2 = $scope.card.whyseeads.age.map(function(v) { return v[1] })
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
                    // 国家分布
                    if ($scope.card.whyseeads.addr) {
                        // 将country里的值改为大写,并转换全称
                        for (key in $scope.card.whyseeads.addr) {
                            var countryShortName = $scope.card.whyseeads.addr[key].country.toUpperCase() // 转换成大写
                            $scope.card.whyseeads.addr[key].country = countryShortName
                            $scope.card.whyseeads.addr[key].name = vm.countries[countryShortName] ? vm.countries[countryShortName].name : countryShortName // 添加全称 
                        }
                        // 计算总数
                        var adsVisitCountryCount = 0
                        for (key in $scope.card.whyseeads.addr) {
                            adsVisitCountryCount += $scope.card.whyseeads.addr[key].value
                        }
                        $scope.card.whyseeads.count = adsVisitCountryCount
                        // 国家图表 mapChartsConfig(mapData[国家数据], mapValueCount[数据总数], mapName[标题名称], mapLegend[是否显示图例])
                        $scope.card.addrMapCharts = Util.mapChartsConfig($scope.card.whyseeads.addr, adsVisitCountryCount, 'Top countries by impression')
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
    .controller('QuickSidebarController', ['$scope', '$window', 'settings', 'User', function($scope, $window, settings, User) {
        /* Setup Layout Part - Quick Sidebar */
        // 这个控制器与广告是强绑定的，这里直接指向$parent的这个方式是非常不友好的，加大了耦合
        $scope.$on('$includeContentLoaded', function() {
            $scope.settings = settings
            $scope.filterOption = $scope.$parent.filterOption
            $scope.daterangeOption = {
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                    'All Times': ['2016-01-01', moment()]
                }
            }
            $scope.categoryOpt = {
                items: $scope.$parent.filterOption.category,
                all: false,
                collapse: true,
                defnum: 5,
                toggle: function() {
                    angular.forEach($scope.$parent.filterOption.category, function(value, key) {
                        if ($scope.categoryOpt.all)
                            value.selected = true
                        else
                            value.selected = false
                    })
                }
            }
            $scope.buttondescOpt = {
                items: $scope.$parent.filterOption.buttondesc,
                all: false,
                collapse: true,
                defnum: 5,
                toggle: function() {
                    var vm = this
                    angular.forEach(this.items, function(value, key) {
                        if (vm.all)
                            value.selected = true
                        else
                            value.selected = false
                    })
                }
            }

            $scope.reset = function() {
                $scope.$parent.initSearch()
                $scope.$parent.search()
                console.log($scope.$parent.filterOption)
            }
            $scope.User = User

            $scope.submit = function() {
                var legal = true
                if (!$scope.$parent.inAdvertiserMode) {
                    legal = $scope.$parent.searchCheck($scope.$parent.adSearcher.searchOption.search.text)
                }
                if (legal) {
                    angular.element($window).scrollTop(0)
                    $scope.$parent.search('search')
                }
            }
        })
    }])

angular.module('MetronicApp').controller('AdserSearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User) {
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
    .controller('AdserAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$http', '$uibModal', '$q', 'Util', '$timeout',
        function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $http, $uibModal, $q, Util, $timeout) {
            /*
            * 广告主分析接口未定，暂时用临时接口
            * 许多原有代码，确认无用的已经删除，不确定是干嘛的，或注释或暂留
            * 因为临时接口与原有的存在变化，广告主分析的这块代码比较混乱，待接口出来后，将再做修改
            */
            var vm = this
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
            promises[0] = getAdserAnalysis($scope.username, "overview,rank,trend,topkeyword")
            promises[0].then(function(res) {
                // console.log('date', res.data)
                // 不知道是干嘛的
                var key
                for (key in res.data) {
                    if (!$scope.card[key]) {
                        $scope.card[key] = res.data[key]
                    }
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
                promises[1] = getAdserAnalysis($scope.username, "summary,audience_all,link,topn,button")
                promises[1].then(function(res) {
                    $scope.card.info = res.data[0]
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
                            $scope.card.gnederPieCharts = Util.pieChartsConfig([['Male', audienceArr.gender[0]], ['Female', audienceArr.gender[1]]], '60%', ['#7cb5ec', '#f9c'])
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
                                ['#7cb5ec', '#f9c']
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
                        // 将原生的object 数据转换成 对应的数据
                        $scope.card.objectiveArr = []
                        for (key in objectiveArr) {
                            $scope.card.objectiveArr.push([
                                objectStr[key], objectiveArr[key]
                            ])
                        }
                        // 配置显示颜色
                        var objColors = ['#337ab7', '#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#8085e8', '#8d4653', '#91e8e1']
                        // 配置图例
                        var objPieLegend = {
                            enabled: true,
                            align: 'right',
                            verticalAlign: 'middle',
                            layout: 'vertical',
                            itemMarginTop: 5,
                            itemMarginBottom: 5,
                            labelFormatter: function() {
                                return '<label style="color:' + this.color + '">' + (this.name.length > 10 ? (this.name.substring(0, 7) + '...') : this.name) + '</label>  :  ' + this.percentage.toFixed(2) + "%"
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
                    if ($scope.card.info.phone_ad_count || $scope.card.info.rc_ad_count || $scope.card.info.tl_ad_count) {
                        var chartsDtata = [
                            ['Phone', $scope.card.info.phone_ad_count || 0],
                            ['Rc', $scope.card.info.rc_ad_count || 0],
                            ['Tl', $scope.card.info.tl_ad_count || 0]
                        ]
                        $scope.card.adsNumPieCharts = Util.pieChartsConfig(chartsDtata, '0%', ['#7cb5ec', '#337ab7', '#3c739e'], objPieLegend)
                    } else $scope.card.adsNumPieCharts = false
                    // 广告类型占比
                    if ($scope.card.info.image_ad_count || $scope.card.info.carousel_ad_count || $scope.card.info.canvas_ad_count || $scope.ads.info.video_ad_count) {
                        var typeChartsDtata = [
                            ['Images', $scope.card.info.image_ad_count || 0],
                            ['Carousels', $scope.card.info.carousel_ad_count || 0],
                            ['Canvas', $scope.card.info.canvas_ad_count || 0],
                            ['Videos', $scope.card.info.video_ad_count]
                        ]
                        $scope.card.adsTypeCharts = Util.pieChartsConfig(typeChartsDtata, '0%', ['#7cb5ec', '#337ab7', '#3c739e', '#d9edf7'], { enabled: false })
                    } else $scope.card.adsTypeCharts = false
                })
            })
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