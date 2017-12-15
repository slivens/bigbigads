import '../common/common.js'
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
                    "field": "default",
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
                        if (((item.min && (item.min != searcher.defFilterOption.engagements[index].min)) || (item.max && (item.max != searcher.defFilterOption.engagements[index].max))) || (item.min === 0 || item.max === 180))
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
                                vm.ads.adser_info = vm.ads.adser_info || []
                                vm.ads.adser_info = vm.ads.adser_info.concat(res.data.adser_info)
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
                return vm.search(vm.params, false, action)
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
        searcher.searchToQuery = searcher.prototype.searchToQuery = function(option, sort) {
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
            if (sort) {
                query.sort = sort
            }
            return query
        }
        // 将location的参数转换成搜索过滤项
        searcher.queryToSearch = searcher.prototype.queryToSearch = function(locaionSearch, option) {
            var search = locaionSearch
            var duration
            var engagements
            var seeTimes
            var vm = this
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
            // 解决刷新后sort by值丢失问题
            if (search.sort) {
                vm.params.sort.field = search.sort
                option.sort = search.sort
            }
        }
        return searcher
    }
])
