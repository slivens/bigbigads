/* adsearch controller */
angular.module('MetronicApp')
.directive('singleImage', function() {
    return {
        restrict:'E',
        templateUrl:'views/search/single-image.html',
        replace:false,
        scope:{
            card: '='
        },
        controller:['$scope', 'settings', function($scope, settings) {
            $scope.settings = settings;
        }]
    };
})
.directive('singleVideo', function() {
    return {
        restrict:'E',
        templateUrl:'views/search/single-video.html',
        replace:false,
        scope:{
            card: '='
        },
        controller:['$scope', 'settings', function($scope, settings) {
            $scope.settings = settings;
        }]
    };
})
.directive('canvas', function() {
    return {
        restrict:'E',
        templateUrl:'views/search/canvas.html',
        replace:false,
        scope:{
            card: '='
        },
        controller:['$scope', 'settings', function($scope, settings) {
            $scope.settings = settings;
        }]
    };
})
.directive('carousel', function() {
    return {
        restrict:'E',
        templateUrl:'views/search/carousel.html',
        replace:false,
        scope:{
            card: '='
        },
        controller:['$scope', 'settings', function($scope, settings) {
            $scope.settings = settings;
        }]
    };
});

angular.module('MetronicApp').factory('Searcher', ['$http', '$timeout', 'settings', 'ADS_TYPE', 'ADS_CONT_TYPE',
    function($http, $timeout, settings, ADS_TYPE, ADS_CONT_TYPE) {
        var searcher = function() {
            var vm = this;
            vm.defparams = {
                "search_result": "ads",
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
            };
            vm.defSearchFields = "message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink";
            vm.defFilterOption = {
                type:"",
                date: {
                    startDate: null,
                    endDate: null
                },
                category:settings.searchSetting.categoryList,
                format:settings.searchSetting.formatList,
                buttondesc:settings.searchSetting.buttondescList
            };
            vm.defSearchOption = {
            range: settings.searchSetting.rangeList,
            search: {
                text: null,
                fields: vm.defSearchFields
            },
            domain: {
                text: null,
                exclude: false
            },
            audience: {
                text: null,
                exclude: false
            },
            filter: vm.defFilterOption
            };
            vm.params = angular.copy(vm.defparams);
            vm.oldParams = null;
            vm.ADS_TYPE = ADS_TYPE;
            vm.ADS_CONT_TYPE = ADS_CONT_TYPE;
            vm.pageCount = settings.searchSetting.pageCount;
            vm.ads = {
                total_count: 0
            };
            vm.isend = false;

            vm.getAdsType = function(item, type) {
                // console.log(type, item.show_way, item.show_way & type);
                if (item.show_way & type)
                    return true;
                return false;
            };
            vm.search = function(params, clear) {
                //获取广告搜索信息
                var searchurl = settings.remoteurl + '/api/forward/adsearch';
                vm.busy = true;
                $http.post(
                    searchurl,
                    params
                ).then(function(res) {
                    vm.isend = res.data.is_end;
                    if (clear && vm.isend) { //检测到结束就清空搜索结果
                        vm.ads = [];
                        vm.ads.total_count = 0;
                    }
                    if (res.data.count) {
                        angular.forEach(res.data.ads_info, function(value, key) {
                            if (value.type == vm.ADS_CONT_TYPE.CAROUSEL) {
                                value.watermark = JSON.parse(value.watermark);
                                console.log('watermark', value.watermark);
                            }
                        });
                        if (clear || vm.ads.total_count === 0) {
                            vm.ads = res.data;
                        } else {
                            vm.ads.ads_info = vm.ads.ads_info.concat(res.data.ads_info);
                        }
                    }
                    console.log(res.data);
                    $timeout(function() {
                        vm.busy = false;
                    }, 500);

                }, function(res) {
                    vm.busy = false;
                    // console.log(res);
                });
            };

            vm.getMore = function() {
                if (vm.busy)
                    return;
                vm.params.limit[0] += settings.searchSetting.pageCount;
                vm.search(vm.params, false);
                console.log("read more", vm.params.limit);
            };
            vm.filter = function() {
                if (vm.params == vm.oldParams)
                    return;
                vm.params.limit[0] = 0;
                vm.search(vm.params, true);
                vm.oldParams = angular.copy(vm.params);
            };
            vm.addFilter = function(obj) {
                var i;
                var finded = false;
                //where必须带ads_type，否则会出错
                for (i = 0; i < vm.params.where.length; ++i) {
                    if (vm.params.where[i].field == obj.field) {
                        finded = true;
                        break;
                    }
                }
                if (i == vm.params.where.length) {
                    vm.params.where.push(obj);
                } else {
                    vm.params.where[i] = obj;
                }
            };
            vm.removeFilter = function(name) {
                var i;
                //where必须带ads_type，否则会出错
                for (i = 0; i < vm.params.where.length; ++i) {
                    if (vm.params.where[i].field == name) {
                        vm.params.where.splice(i, 1);
                        break;
                    }
                }
            };
        };
        return searcher;
    }
]);
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher', '$filter', 'SweetAlert',
    function($rootScope, $scope, settings, $http, Searcher, $filter, SweetAlert) {
        $scope.swal = function(msg) {
            SweetAlert.swal(msg);
        };
        $scope.adSearcher = new Searcher();
        $scope.adSearcher.search($scope.adSearcher.defparams, true);
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
            $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption);
            $scope.filterOption = $scope.searchOption.filter;
        };
        $scope.resetSearch = function() {
            angular.forEach($scope.filterOption.category, function(value, key) {
                value.selected = false;
            });
            angular.forEach($scope.filterOption.format, function(value, key) {
                value.format = false;
            });
        };
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
            if (buttondesc.length) {
                $scope.adSearcher.addFilter({
                    field: 'buttondesc',
                    value: buttondesc.join(",")
                });
            } else {
                $scope.adSearcher.removeFilter('buttondesc');
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

            $scope.currSearchOption = angular.copy($scope.searchOption); //保存搜索
            if (option.search.text) {
                angular.forEach(option.range, function(item, key) {
                    if (item.selected) {
                        range.push(item.key);
                    }
                });

                keys.push({
                    string: option.search.text,
                    search_fields: range.length ? range.join(',') : option.search.fields,
                    relation: "Must"
                });
            }
            if (option.domain.text) {
                keys.push({
                    string: option.domain.text,
                    search_fields: 'caption,link,dest_site,buttonlink',
                    relation: option.domain.exclude ? 'Not' : 'Must'
                });
            }
            if (option.audience.text) {
                keys.push({
                    string: option.audience.text,
                    search_fields: 'whyseeads,whyseeads_all',
                    relation: option.audience.exclude ? 'Not' : 'Must'
                });
            }

            $scope.currSearchOption.range = range.join(',');
            $scope.filter($scope.filterOption);

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
]);
