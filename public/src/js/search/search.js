if (!app)
    var app = angular.module('MetronicApp');
/* adsearch js */
app.controller('AdsearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User', 'ADS_TYPE', '$uibModal', 
        function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User, ADS_TYPE, $uibModal) {
            //搜索流程:location.search->searchOption->adSearcher.params
            //将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
            function searchToQuery(option, searcher) {
                var query = {};
                if (option.search.text)
                    query.searchText = option.search.text;
                if (option.search.fields != searcher.defSearchFields)
                    query.searchFields = option.search.fields;
                if (option.filter.date.startDate && option.filter.date.endDate) {
                    query.startDate = option.filter.date.startDate.format('YYYY-MM-DD');
                    query.endDate = option.filter.date.endDate.format('YYYY-MM-DD');
                }
                if (option.filter.type) {
                    query.type = option.filter.type;
                }
                if (option.filter.lang) {
                    query.lang = option.filter.lang;
                }
                if (option.filter.state) {
                    query.state = option.filter.state;
                }
                if (option.domain.text) {
                    query.domain = JSON.stringify(option.domain);
                }
                if (option.audience.text) {
                    query.audience = JSON.stringify(option.audience);
                }

                //category, format, buttondesc
                if (option.filter.categoryString) {
                    query.category = option.filter.categoryString;
                }
                if (option.filter.formatString) {
                    query.format = option.filter.formatString;
                }
                if (option.filter.buttondescString) {
                    query.buttondesc = option.filter.buttondescString;
                }
                if (option.filter.isDurationDirty()) {
                    query.duration = JSON.stringify(option.filter.duration);
                }
                if (option.filter.isSeeTimesDirty()) {
                    query.seeTimes = JSON.stringify(option.filter.seeTimes);
                }
                $location.search(query);
            }
            //将query转化成搜索参数
            function queryToSearch(option, searcher) {
                var i;
                var search = $location.search();
                if (search.searchText) {
                    option.search.text = search.searchText;
                }
                if (search.searchFields && search.searchFields != searcher.defSearchFields) {
                    var range = search.searchFields.split(',');
                    angular.forEach(range, function(item1) {
                        for (i = 0; i < option.range.length; i++) {
                            if (item1 == option.range[i].key) {
                                option.range[i].selected = true;
                            }
                        }
                    });
                }
                if (search.startDate && search.endDate) {
                    option.filter.date.startDate = moment(search.startDate, 'YYYY-MM-DD');
                    option.filter.date.endDate = moment(search.endDate, 'YYYY-MM-DD');
                }
                if (search.type) {
                    option.filter.type = search.type;
                }
                if (search.lang) {
                    option.filter.lang = search.lang;
                }
                if (search.state) {
                    option.filter.state = search.state;
                }
                if (search.domain) {
                    option.domain = JSON.parse(search.domain);
                }
                if (search.audience) {
                    option.audience = JSON.parse(search.audience);
                }
                if (search.category) {
                    Util.matchkey(search.category, option.filter.category);
                }
                if (search.format) {
                    Util.matchkey(search.format, option.filter.format);
                }
                if (search.buttondesc) {
                    Util.matchkey(search.buttondesc, option.filter.buttondesc);
                }
                if (search.duration) {
                    option.filter.duration = JSON.parse(search.duration);
                }
                if (search.seeTimes) {
                    option.filter.seeTimes = JSON.parse(search.seeTimes);
                }
            }
            // $scope.swal = function(msg) {
            //     SweetAlert.swal(msg);
            // };
            var adSearcher = $scope.adSearcher = new Searcher();
            adSearcher.checkAndGetMore = function() {
                if (!User.done) {
                    adSearcher.getMore();
                    return;
                }
                var policy = User.getPolicy('result_per_search');
                if (!policy) {
                    SweetAlert.swal("no permission for get more");
                    adSearcher.isend = true;
                    return;
                }
                // console.log("max search result:", policy.value, adSearcher.params.limit[0] + adSearcher.params.limit[1]);
                if (adSearcher.params.limit[0] + adSearcher.params.limit[1] >= policy.value) {
                    SweetAlert.swal("you reached search result limit(" + policy.value + ")");
                    adSearcher.isend = true;
                    return;
                }
                adSearcher.getMore();
            };
            // $scope.adSearcher.search($scope.adSearcher.defparams, true);
            $scope.reverseSort = function() {
                if (!User.can('search_sortby')) {
                    SweetAlert.swal("no sort permission");
                    return;
                }
                $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
                $scope.adSearcher.filter();

            };

            //text为空时就表示没有这个搜索项了
            $scope.initSearch = function() {
                var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption);
                $scope.filterOption = $scope.searchOption.filter;

                //存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
                queryToSearch(option, $scope.adSearcher);
            };
            $scope.initSearch();

            $scope.currSearchOption = {};

            $scope.filter = function(option, action) {
                var category = [],
                    format = [],
                    buttondesc = [];
                
                //广告类型
                if (!$scope.filterOption.type) {
                    $scope.adSearcher.removeFilter("ads_type");
                } else {
                    $scope.adSearcher.addFilter({
                        field: 'ads_type',
                        value: $scope.filterOption.type
                    });
                }
                //日期范围
                if (!option.date.startDate || !option.date.endDate) {
                    $scope.adSearcher.removeFilter('time');
                } else {
                    var startDate = option.date.startDate.format('YYYY-MM-DD');
                    var endDate = option.date.endDate.format('YYYY-MM-DD');
                    $scope.adSearcher.addFilter({
                        field: "time",
                        min: startDate,
                        max: endDate
                    });
                }
                //语言
                if (option.lang) {
                    $scope.adSearcher.addFilter({
                        field: 'ad_lang',
                        value: option.lang
                    });
                } else {
                    $scope.adSearcher.removeFilter('ad_lang');
                }
                //国家
                if (option.state) {
                    $scope.adSearcher.addFilter({
                        field: 'state',
                        value: option.state
                    });
                } else {
                    $scope.adSearcher.removeFilter('state');
                }


                //支持多项搜索，以","隔开
                angular.forEach($scope.filterOption.category, function(item, key) {
                    if (item.selected) {
                        category.push(item.key);
                    }
                });
                $scope.filterOption.categoryString = category.join(',');

                if (category.length) {
                    $scope.adSearcher.addFilter({
                        field: 'category',
                        value: category.join(",")
                    });
                } else {
                    $scope.adSearcher.removeFilter('category');
                }

                //内容格式 
                angular.forEach($scope.filterOption.format, function(item, key) {
                    if (item.selected) {
                        format.push(item.key);
                    }
                });
                $scope.filterOption.formatString = format.join(',');
                if (format.length) {
                    $scope.adSearcher.addFilter({
                        field: 'media_type',
                        value: format.join(",")
                    });
                } else {
                    $scope.adSearcher.removeFilter('media_type');
                }

                //Button Description
                angular.forEach($scope.filterOption.buttondesc, function(item, key) {
                    if (item.selected) {
                        buttondesc.push(item.key);
                    }
                });
                option.buttondescString = buttondesc.join(',');
                if (buttondesc.length) {
                    $scope.adSearcher.addFilter({
                        field: 'buttondesc',
                        value: buttondesc.join(",")
                    });
                } else {
                    $scope.adSearcher.removeFilter('buttondesc');
                }

                //Duration Filter
                if (!option.isDurationDirty()) {
                    $scope.adSearcher.removeFilter('duration_days');
                } else {
                    $scope.adSearcher.addFilter({
                        field: 'duration_days',
                        min: option.duration.from,
                        max: option.duration.to
                    });
                }

                //see times Filter
                if (!option.isSeeTimesDirty()) {
                    $scope.adSearcher.removeFilter('see_times');
                } else {
                    $scope.adSearcher.addFilter({
                        field: 'see_times',
                        min: option.seeTimes.from,
                        max: option.seeTimes.to
                    });
                }
                $scope.currSearchOption.category = category.join(',');
                $scope.currSearchOption.format = format.join(',');
                $scope.currSearchOption.buttondesc = buttondesc.join(',');
                $scope.adSearcher.filter(action ? action : 'search').then(function(){}, function(res) {
                    if (res.data instanceof Object) {
                        SweetAlert.swal(res.data.desc);
                    } else {
                        SweetAlert.swal(res.statusText);
                    }
                });
                console.log("params", $scope.adSearcher.params);
            };

            $scope.search = function(action) {
                var i;
                var option = $scope.adSearcher.searchOption;
                var keys;
                var range = [];
                keys = $scope.adSearcher.params.keys = [];
                
                //检查权限，并且应该集中检查权限，才不会搞得逻辑混乱或者状态不一致
                if (!User.can('result_per_search')) {
                    SweetAlert.swal("no search permission");
                    return;
                }
                if (action == 'filter' && !User.can('search_filter')) {
                    SweetAlert.swal("no filter permission");
                    return;
                }
                if ($scope.filterOption.type) {
                    var type = ADS_TYPE[$scope.filterOption.type];
                    if (!(Number(User.getPolicy('platform').value)  & type)) {
                        SweetAlert.swal("type '" + $scope.filterOption.type + "' exceed your permission");
                        return;
                    }
                }
                //字符串和域
                $scope.currSearchOption = angular.copy($scope.searchOption); //保存搜索
                if (option.search.text) {
                    angular.forEach(option.range, function(item, key) {
                        if (item.selected) {
                            range.push(item.key);
                        }
                    });
                    option.search.fields = range.length ? range.join(',') : option.search.fields;
                    keys.push({
                        string: option.search.text,
                        search_fields: option.search.fields,
                        relation: "Must"
                    });
                }
                //域名
                if (option.domain.text) {
                    keys.push({
                        string: option.domain.text,
                        search_fields: 'caption,link,dest_site,buttonlink',
                        relation: option.domain.exclude ? 'Not' : 'Must'
                    });
                }
                //受众
                if (option.audience.text) {
                    keys.push({
                        string: option.audience.text,
                        search_fields: 'whyseeads,whyseeads_all',
                        relation: option.audience.exclude ? 'Not' : 'Must'
                    });
                }
                $scope.currSearchOption.range = range.join(',');
                $scope.filter($scope.filterOption, action);
                if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 0) {
                    $scope.currSearchOption.isdirty = true;
                }
                searchToQuery(option, $scope.adSearcher);
            };

            $scope.clearSearch = function() {
                $location.search({});
                $state.reload();
            };
            $scope.showStatics = function() {
                if (adSearcher.params.keys.length === 0) {
                    SweetAlert.swal("you must search first");
                    return;
                }
                return $uibModal.open({
                    templateUrl:'statics-dlg.html',
                    size:'lg',
                    animation:true,
                    controller:['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
                        //使用独立的搜索器，否则可能影响到原来的广告搜索结果
                        var seacher = new Searcher();
                        seacher.params = angular.copy(adSearcher.params);
                        $scope.statics = {};
                        $scope.queryPromise = seacher.getStatics(seacher.params);
                        $scope.queryPromise.then(function(res) {
                            var data = $scope.statics = res.data;
                            //饼图
                            $scope.statics.adLangConfig = Util.initPie(data.ad_lang, "AD Language");
                            $scope.statics.adserNameConfig = Util.initPie(data.adser_name, "Adser Names");
                            $scope.statics.adserUsernameConfig = Util.initPie(data.adser_username, "Adser Usernames");
                            $scope.statics.categoryConfig = Util.initPie(data.category, "Category");
                            $scope.statics.mediaTypeConfig = Util.initPie(data.media_type, "Media Type");

                            //button_link, dest_site,link,whyseeads太长，怎么处理？
                            console.log(res);
                        });
                        $scope.close = function() {
                            $uibModalInstance.dismiss('cancel');
                        };
                        
                    }]

                });
            };
            $scope.User = User;
            $scope.Searcher = Searcher;
            //一切的操作应该是在获取到用户信息之后，后面应该优化直接从本地缓存读取
            User.getInfo().then(function() {
                //根据search参数页面初始化
                $scope.search();
            });
            $scope.$on('$viewContentLoaded', function() {
                // initialize core components
                App.initAjax();

                // set default layout mode
                $rootScope.settings.layout.pageContentWhite = true;
                $rootScope.settings.layout.pageBodySolid = false;
                $rootScope.settings.layout.pageSidebarClosed = false;
            });


        }
    ])
    .controller('AdserController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher', '$filter', 'SweetAlert', '$state', 'Util', '$stateParams', function($rootScope, $scope, settings, $http, Searcher, $filter, SweetAlert, $state, Util, $stateParams) {
        $scope.swal = function(msg) {
            SweetAlert.swal(msg);
        };
        $scope.adser = {
            name: $stateParams.name,
            username: $stateParams.adser
        };
        $scope.adSearcher = new Searcher();
        // $scope.adSearcher.search($scope.adSearcher.defparams, true);
        $scope.reverseSort = function() {
            $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
            $scope.adSearcher.filter();
        };
        $scope.adSearcher.params.where.push({
            field: 'adser_username',
            value: $stateParams.adser
        });
        $scope.adSearcher.filter();
    }])
    .controller('AdAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$window',
        function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $window) {
            var searcher = $scope.adSearcher = new Searcher();
            // $scope.adSearcher.search($scope.adSearcher.defparams, true);
            $scope.reverseSort = function() {
                $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
                $scope.adSearcher.filter();
            };
            $scope.card = {
                end: true,
                similars:[]
            };
            $scope.id = $stateParams.id;
            $scope.adSearcher.addFilter({
                field: 'ads_id',
                value: $scope.id
            });
            var promise = $scope.adSearcher.filter();
            $rootScope.$broadcast("loading");
            promise.then(function(ads) {
                //只取首条消息
                $scope.card = $scope.ad = ads.ads_info[0];
                //表示广告在分析模式下，view根据这个字段区别不同的显示
                $scope.card.indetail = true;
                $scope.card.end = false;
                if ($scope.card.whyseeads_all)
                    $scope.card.whyseeads_all = $scope.card.whyseeads_all.split('\n');
                if ($scope.card.whyseeads)
                    $scope.card.whyseeads = $scope.card.whyseeads.split('\n');
                searcher.findSimilar($scope.card.watermark);
            }, function(res) {
                $scope.card.end = true;
            }).finally(function() {
                $rootScope.$broadcast("completed");
            });

            $scope.goback = function() {
                $window.history.back();
            };

            /**
             * 查找相似图
             */
            searcher.findSimilar = function(watermark) {
               var similarSearcher = new Searcher();
               var similarPromise;
               var md5 = watermark.match(/\/(\w+)\./);
               if (md5 === null) {
                    return false;
               }
               console.log(md5);
               md5 = md5[1];
               
               similarSearcher.addFilter({
                    field:"watermark_md5", 
                    value:md5
               });
               similarPromise = similarSearcher.filter();
               similarPromise.then(function(ads) {
                    $scope.card.similars = ads.ads_info;
                    console.log("similar", ads);
               });
               return similarPromise;
            };
            $scope.$on('$viewContentLoaded', function() {
                // initialize core components
                App.initAjax();

                // set default layout mode
                $rootScope.settings.layout.pageContentWhite = true;
                $rootScope.settings.layout.pageBodySolid = false;
                $rootScope.settings.layout.pageSidebarClosed = false;
            });


        }
    ])

.controller('QuickSidebarController', ['$scope', 'settings', 'User', function($scope, settings, User) {

    /* Setup Layout Part - Quick Sidebar */
    //这个控制器与广告是强绑定的，这里直接指向$parent的这个方式是非常不友好的，加大了耦合
    $scope.$on('$includeContentLoaded', function() {
        $scope.settings = settings;
        $scope.filterOption = $scope.$parent.filterOption;
        $scope.daterangeOption = {
            ranges: {
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                'Last 180 Days': [moment().subtract(179, 'days'), moment()]
            }
        };
        $scope.categoryOpt = {
            items: $scope.$parent.filterOption.category,
            all: false,
            collapse: true,
            defnum: 5,
            toggle: function() {
                angular.forEach($scope.$parent.filterOption.category, function(value, key) {
                    if ($scope.categoryOpt.all)
                        value.selected = true;
                    else
                        value.selected = false;
                });
            }
        };
        $scope.buttondescOpt = {
            items: $scope.$parent.filterOption.buttondesc,
            all: false,
            collapse: true,
            defnum: 5,
            toggle: function() {
                var vm = this;
                angular.forEach(this.items, function(value, key) {
                    if (vm.all)
                        value.selected = true;
                    else
                        value.selected = false;
                });
            }
        };

        $scope.reset = function() {
            $scope.$parent.initSearch();
            $scope.$parent.search();
            console.log($scope.$parent.filterOption);
        };
        $scope.User = User;
        setTimeout(function() {
            QuickSidebar.init(); // init quick sidebar        
        }, 100);
    });
}]);

app.controller('AdserSearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams',
        function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams) {
            //搜索流程:location.search->searchOption->adSearcher.params
            //将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
            function searchToQuery(option, searcher) {
                var query = {};
                if (option.search.text)
                    query.searchText = option.search.text;
                if (option.search.fields != searcher.defSearchFields)
                    query.searchFields = option.search.fields;
                if (option.filter.date.startDate && option.filter.date.endDate) {
                    query.startDate = option.filter.date.startDate.format('YYYY-MM-DD');
                    query.endDate = option.filter.date.endDate.format('YYYY-MM-DD');
                }
                if (option.filter.type) {
                    query.type = option.filter.type;
                }
                if (option.filter.lang) {
                    query.lang = option.filter.lang;
                }
                if (option.filter.state) {
                    query.state = option.filter.state;
                }
                if (option.domain.text) {
                    query.domain = JSON.stringify(option.domain);
                }
                if (option.audience.text) {
                    query.audience = JSON.stringify(option.audience);
                }

                //category, format, buttondesc
                if (option.filter.categoryString) {
                    query.category = option.filter.categoryString;
                }
                if (option.filter.formatString) {
                    query.format = option.filter.formatString;
                }
                if (option.filter.buttondescString) {
                    query.buttondesc = option.filter.buttondescString;
                }
                if (option.filter.isDurationDirty()) {
                    query.duration = JSON.stringify(option.filter.duration);
                }
                if (option.filter.isSeeTimesDirty()) {
                    query.seeTimes = JSON.stringify(option.filter.seeTimes);
                }
                $location.search(query);
            }
            //将query转化成搜索参数
            function queryToSearch(option, searcher) {
                var i;
                var search = $location.search();
                if (search.searchText) {
                    option.search.text = search.searchText;
                }
                if (search.searchFields && search.searchFields != searcher.defSearchFields) {
                    var range = search.searchFields.split(',');
                    angular.forEach(range, function(item1) {
                        for (i = 0; i < option.range.length; i++) {
                            if (item1 == option.range[i].key) {
                                option.range[i].selected = true;
                            }
                        }
                    });
                }
                if (search.startDate && search.endDate) {
                    option.filter.date.startDate = moment(search.startDate, 'YYYY-MM-DD');
                    option.filter.date.endDate = moment(search.endDate, 'YYYY-MM-DD');
                }
                if (search.type) {
                    option.filter.type = search.type;
                }
                if (search.lang) {
                    option.filter.lang = search.lang;
                }
                if (search.state) {
                    option.filter.state = search.state;
                }
                if (search.domain) {
                    option.domain = JSON.parse(search.domain);
                }
                if (search.audience) {
                    option.audience = JSON.parse(search.audience);
                }
                if (search.category) {
                    Util.matchkey(search.category, option.filter.category);
                }
                if (search.format) {
                    Util.matchkey(search.format, option.filter.format);
                }
                if (search.buttondesc) {
                    Util.matchkey(search.buttondesc, option.filter.buttondesc);
                }
                if (search.duration) {
                    option.filter.duration = JSON.parse(search.duration);
                }
                if (search.seeTimes) {
                    option.filter.seeTimes = JSON.parse(search.seeTimes);
                }
            }
            $scope.swal = function(msg) {
                SweetAlert.swal(msg);
            };
            $scope.adSearcher = new Searcher({
                searchType: 'adser',
                url: '/forward/adserSearch'
            });
            // $scope.adSearcher.search($scope.adSearcher.defparams, true);
            $scope.reverseSort = function() {
                $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
                $scope.adSearcher.filter();
            };


            // $scope.filterOption = {
            //     date: {
            //         startDate: null,
            //         endDate: null
            //     },
            // };
            //text为空时就表示没有这个搜索项了
            $scope.initSearch = function() {
                var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption);
                $scope.filterOption = $scope.searchOption.filter;

                //存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
                queryToSearch(option, $scope.adSearcher);
            };
            // $scope.resetSearch = function() {
            //     angular.forEach($scope.filterOption.category, function(value, key) {
            //         value.selected = false;
            //     });
            //     angular.forEach($scope.filterOption.format, function(value, key) {
            //         value.format = false;
            //     });
            // };
            $scope.initSearch();

            $scope.currSearchOption = {};
            // $scope.filterOption.category = angular.copy(settings.searchSetting.categoryList);
            // $scope.filterOption.format = angular.copy(settings.searchSetting.formatList);
            // $scope.filterOption.buttondesc = angular.copy(settings.searchSetting.buttondescList);

            $scope.filter = function(option) {
                var category = [],
                    format = [],
                    buttondesc = [];
                //广告类型
                if (!$scope.filterOption.type)
                    $scope.adSearcher.removeFilter("ads_type");
                else
                    $scope.adSearcher.addFilter({
                        field: 'ads_type',
                        value: $scope.filterOption.type
                    });
                //日期范围
                if (!option.date.startDate || !option.date.endDate) {
                    $scope.adSearcher.removeFilter('time');
                } else {
                    var startDate = option.date.startDate.format('YYYY-MM-DD');
                    var endDate = option.date.endDate.format('YYYY-MM-DD');
                    $scope.adSearcher.addFilter({
                        field: "time",
                        min: startDate,
                        max: endDate
                    });
                }
                //语言
                if (option.lang) {
                    $scope.adSearcher.addFilter({
                        field: 'ad_lang',
                        value: option.lang
                    });
                } else {
                    $scope.adSearcher.removeFilter('ad_lang');
                }
                //国家
                if (option.state) {
                    $scope.adSearcher.addFilter({
                        field: 'state',
                        value: option.state
                    });
                } else {
                    $scope.adSearcher.removeFilter('state');
                }


                //支持多项搜索，以","隔开
                angular.forEach($scope.filterOption.category, function(item, key) {
                    if (item.selected) {
                        category.push(item.key);
                    }
                });
                $scope.filterOption.categoryString = category.join(',');

                if (category.length) {
                    $scope.adSearcher.addFilter({
                        field: 'category',
                        value: category.join(",")
                    });
                } else {
                    $scope.adSearcher.removeFilter('category');
                }

                //内容格式 
                angular.forEach($scope.filterOption.format, function(item, key) {
                    if (item.selected) {
                        format.push(item.key);
                    }
                });
                $scope.filterOption.formatString = format.join(',');
                if (format.length) {
                    $scope.adSearcher.addFilter({
                        field: 'media_type',
                        value: format.join(",")
                    });
                } else {
                    $scope.adSearcher.removeFilter('media_type');
                }

                //Button Description
                angular.forEach($scope.filterOption.buttondesc, function(item, key) {
                    if (item.selected) {
                        buttondesc.push(item.key);
                    }
                });
                option.buttondescString = buttondesc.join(',');
                if (buttondesc.length) {
                    $scope.adSearcher.addFilter({
                        field: 'buttondesc',
                        value: buttondesc.join(",")
                    });
                } else {
                    $scope.adSearcher.removeFilter('buttondesc');
                }

                //Duration Filter
                if (!option.isDurationDirty()) {
                    $scope.adSearcher.removeFilter('duration_days');
                } else {
                    $scope.adSearcher.addFilter({
                        field: 'duration_days',
                        min: option.duration.from,
                        max: option.duration.to
                    });
                }

                //see times Filter
                if (!option.isSeeTimesDirty()) {
                    $scope.adSearcher.removeFilter('see_times');
                } else {
                    $scope.adSearcher.addFilter({
                        field: 'see_times',
                        min: option.seeTimes.from,
                        max: option.seeTimes.to
                    });
                }
                $scope.currSearchOption.category = category.join(',');
                $scope.currSearchOption.format = format.join(',');
                $scope.currSearchOption.buttondesc = buttondesc.join(',');
                $scope.adSearcher.filter();
                console.log("params", $scope.adSearcher.params);
            };

            $scope.search = function() {
                var i;
                var option = $scope.adSearcher.searchOption;
                var keys;
                var range = [];
                keys = $scope.adSearcher.params.keys = [];

                //字符串和域
                $scope.currSearchOption = angular.copy($scope.searchOption); //保存搜索
                if (option.search.text) {
                    angular.forEach(option.range, function(item, key) {
                        if (item.selected) {
                            range.push(item.key);
                        }
                    });
                    option.search.fields = range.length ? range.join(',') : option.search.fields;
                    keys.push({
                        string: option.search.text,
                        search_fields: option.search.fields,
                        relation: "Must"
                    });
                }
                //域名
                if (option.domain.text) {
                    keys.push({
                        string: option.domain.text,
                        search_fields: 'caption,link,dest_site,buttonlink',
                        relation: option.domain.exclude ? 'Not' : 'Must'
                    });
                }
                //受众
                if (option.audience.text) {
                    keys.push({
                        string: option.audience.text,
                        search_fields: 'whyseeads,whyseeads_all',
                        relation: option.audience.exclude ? 'Not' : 'Must'
                    });
                }
                $scope.currSearchOption.range = range.join(',');
                $scope.filter($scope.filterOption);
                if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 0) {
                    $scope.currSearchOption.isdirty = true;
                }
                searchToQuery(option, $scope.adSearcher);
            };

            $scope.clearSearch = function() {
                $location.search({});
                $state.reload();
            };

            //根据search参数页面初始化

            $scope.search();
            $scope.$on('$viewContentLoaded', function() {
                // initialize core components
                App.initAjax();

                // set default layout mode
                $rootScope.settings.layout.pageContentWhite = true;
                $rootScope.settings.layout.pageBodySolid = false;
                $rootScope.settings.layout.pageSidebarClosed = false;
            });


        }
    ])
    .controller('AdserAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$http', '$uibModal', '$q', 'Util', 
        function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $http, $uibModal, $q, Util) {

            function getAdserAnalysis(username) {
                var params = {
                    "search_result": "adser_analysis",
                    "where": [{
                        "field": "adser_username",
                        "value": username
                    }]
                };
                return $http.post(settings.remoteurl + '/forward/adserAnalysis',
                    params);
            }
            /**
             * jsonSrc:json字符串
             * title:标题
             * labels:是否将json的属性映射到labels对应值
             */
            function initPie(jsonSrc, title, labels) {
                var src = jsonSrc;
                var data = [];
                for (var key in src) {
                    if (labels)
                        data.push([labels[key], src[key]]);
                    else
                        data.push([key, src[key]]);
                }

                return {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: title
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>:{point.percentage:.1f}%'
                            }
                        }
                    },
                    series: [{
                        name: title,
                        data: data
                    }]
                };
            }
            /**
             */
            function initTrend(json, title) {
                var length = json.trend.length;
                var endDate = moment(json.day, 'YYYY-MM-DD');
                var xs = [];
                var i;
                for (i = 0; i < length; ++i) {
                    xs.push(endDate.subtract(i, 'days').format('YYYY-MM-DD'));
                }
                xs = xs.reverse();
                ys = json.trend.reverse();
                console.log("trend", title, xs, length);
                return {
                    title: {
                        text: title
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>:{point.percentage:.1f}%'
                            }
                        }
                    },
                    xAxis: {
                        categories:xs
                    },
                    yAxis: {
                        title:{
                            text:title
                        },
                        plotLines:[{
                            value:0,
                            width:1,
                        }]
                    },
                    series:[{
                        name:title,
                        data:ys
                    }]
                };
            }
            
            /**
             * 初始化单个广告主的图表
             */
            function initChart(card) {
                card.mediaTypeConfig = Util.initPie(card.media_type_groupby, "Media Type");
                card.adLangConfig = Util.initPie(card.ad_lang_groupby, 'Advertise Language');
                card.showwayConfig = Util.initPie(card.show_way_groupby, 'Show Way', {
                    "1": "timeline",
                    "2": "mobile",
                    "3": "timeline&mobile",
                    "4": "rightcolumn"
                });
                card.likesTrendConfig = initTrend(JSON.parse(card.likes_trend), "Likes Trend");
                card.sharesTrendConfig = initTrend(JSON.parse(card.shares_trend), "Shares Trend");
                card.viewsTrendConfig = initTrend(JSON.parse(card.views_trend), "Views Trend");
                card.newAdsTrendConfig = initTrend(JSON.parse(card.new_ads_trend), "New Ads Trend");
                console.log(card.likesTrendConfig);
            }

            function initCompareBar(dataArr, nameArr, title, labels) {
                var opt, i, key;
                var categories = [], map = [];
                var series = [], seriesData;
                //合并分类，去重
                for (i = 0 ;i < dataArr.length; ++i) {
                   for (key in dataArr[i]) {
                        if (!map[key]) {
                            if (labels)
                                categories.push(labels[key]);
                            else
                                categories.push(key);
                            map[key] = categories.length;
                        }
                   }
                }
                //插补数据
                for (i = 0; i < dataArr.length; ++i) {
                    seriesData = new Array(categories.length);
                    for (key = 0; key < categories.length; ++key) {
                        seriesData[key] = 0;
                    }
                    for (key in dataArr[i]) {
                        seriesData[map[key] - 1] = dataArr[i][key];
                    }
                    series.push({name:nameArr[i], data:seriesData});
                }
                opt = {
                    chart: {
                        type: 'column'
                    },
                    title:{
                        text:title
                    },
                    xAxis:{
                        categories:categories
                    },
                    yAxis:{
                        min:0
                    },
                    series:series
                };
                return opt;
            }

            function openAd(id) {
                return $uibModal.open({
                    templateUrl: 'views/ad-analysis.html',
                    size: 'lg',
                    animation: true,
                    resolve: {
                        $stateParams: function() {
                            $stateParams.id = id;
                            return $stateParams;
                        }
                    }
                });
            }

            function formatAdser(item) {
                if (!item)
                    return;
            }

            var competitorQuery = [];
            var promises = [];
            $scope.openAd = openAd;
            $scope.card = {}; //card必须先赋值，否则调用Searcher的getAdsType时会提前生成自己的card,scope出错。
            $scope.Searcher = Searcher;
            $scope.username = $stateParams.username;
            $rootScope.$broadcast("loading");
            promises[0] = getAdserAnalysis($scope.username);
            promises[0].then(function(res) {
                console.log("obj:", res.data);
                $scope.card = res.data;
                formatAdser($scope.card);
                initChart($scope.card);
                $rootScope.$broadcast("completed");
            });

            function addCompetitor(res) {
                formatAdser(res.data);
                $scope.competitors.push(res.data);
            }

            function initFromQuery() {
                if ($location.search().competitor)
                    competitorQuery = $location.search().competitor.split(',');
                for(var key in competitorQuery) {
                    promises.push(getAdserAnalysis(competitorQuery[key]));
                    promises[promises.length - 1].then(addCompetitor);
                }
                
                console.log(competitorQuery);
            }
            function initCompetitorCharts() {
                var langGroup = [$scope.card.ad_lang_groupby];
                var mediaTypeGroup = [$scope.card.media_type_groupby];
                var showwayGroup = [$scope.card.show_way_groupby];
                var nameArr = [$scope.card.adser_name];
                for (var key in $scope.competitors) {
                    langGroup.push($scope.competitors[key].ad_lang_groupby);
                    mediaTypeGroup.push($scope.competitors[key].media_type_groupby);
                    showwayGroup.push($scope.competitors[key].show_way_groupby);
                    nameArr.push($scope.competitors[key].adser_name);
                }
                console.log("nameArr:", nameArr);
                $scope.competitorsChart.langOpt = initCompareBar(langGroup, nameArr, "Language");
                $scope.competitorsChart.mediaTypeOpt = initCompareBar(mediaTypeGroup, nameArr, "Media Type");
                $scope.competitorsChart.showwayOpt = initCompareBar(showwayGroup, nameArr, "Show way", {
                    "1": "timeline",
                    "2": "mobile",
                    "3": "timeline&mobile",
                    "4": "rightcolumn"
                });
                console.log($scope.competitorsChart);
            }
            initFromQuery();
            
            $scope.competitors = [];
            $scope.competitorPopover = false;
            $scope.competitorsChart = {};
            $scope.$on('competitor', function(event, data) {
                var p;
                event.stopPropagation();
                $scope.competitorPopover = false;
                p = getAdserAnalysis(data.adser_username);
                p.then(addCompetitor).then(initCompetitorCharts);
                competitorQuery.push(data.adser_username);
                $location.search('competitor', competitorQuery.join(','));
            });

            $scope.remove = function(idx) {
                $scope.competitors.splice(idx, 1);
                competitorQuery.splice(idx, 1);
                $location.search('competitor', competitorQuery.join(','));
                initCompetitorCharts();
            };
              

            //所有广告主分析数据加载完成才处理图表
            $q.all(promises).then(initCompetitorCharts);

            $scope.$on('$viewContentLoaded', function() {
                // initialize core components
                App.initAjax();

                // set default layout mode
                $rootScope.settings.layout.pageContentWhite = true;
                $rootScope.settings.layout.pageBodySolid = false;
                $rootScope.settings.layout.pageSidebarClosed = false;

            });


        }
    ])
.controller('CompetitorSearcherController', ['$scope', 'Searcher', function($scope, Searcher) {
    var searcher = new Searcher({
                searchType: 'adser',
                url: '/forward/adserSearch',
                limit:[0, -1]
            });
    $scope.searchOpt = {text:"", inprogress:false};
    $scope.promise = null;
    $scope.search = function() {
        searcher.params.keys = [ {
            string:$scope.searchOpt.text,
            search_fields:'adser_name',
            relation:"Must"
        }
        ];
        $scope.searchOpt.inprogress = true;
        $scope.promise = searcher.filter();
        $scope.promise.then(function(data) {
            $scope.searchOpt.items = data.adser;
            // $scope.searchOpt.inprogress = false;
            $scope.searchOpt.error = null;
        }, function(data) {
            $scope.searchOpt.error = "No Data";
            $scope.searchOpt.items = null;
        }).finally(function(){
            $scope.searchOpt.inprogress = false;
        });
    };

    $scope.notify = function(item) {
        $scope.$emit('competitor', item);
    };
}]);

