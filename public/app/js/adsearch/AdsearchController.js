/* adsearch controller */
angular.module('MetronicApp')
.directive('fancybox',['$compile', '$timeout', function($compile, $timeout){
    return {
        link: function($scope, element, attrs) {
            element.fancybox({
                hideOnOverlayClick:false,
                hideOnContentClick:false,
                enableEscapeButton:false,
                showNavArrows:true,
                onComplete: function(){
            $timeout(function(){
                        $compile($("#fancybox-content"))($scope);
                        $scope.$apply();
                        $.fancybox.resize();
                    });
                }
            });
        }
    };
}])
.directive('select2', function() {
    return {
        link:function(scope, element, attrs) {
            element.select2();
        }
    };
})
.directive('advideo', ['$compile', '$timeout',function($compile, $timeout) {
    return {
        link: function(scope, element, attrs) {
            var poster = $('<div></div>');
            var img = $('<img/>');
            img.attr('src', attrs.preview);
            poster.addClass('video');
            poster.html('<a class="playbtn"><i class="fa fa-play-circle-o fa-4x font-yellow-gold"></i></a>');
            poster.append(img);
            element.before(poster);
            element.hide();

            poster.find('.playbtn').click(function() {
                element.trigger('play');
                poster.hide();
                element.show();
            });
            // $timeout(function() {
            //     $compile(element)(scope);
            //     scope.$apply();
            // });
        }
    };
}])
.directive('singleImage', function() {
    return {
        restrict:'E',
        templateUrl:'views/search/single-image.html',
        replace:false,
        scope:{
            card: '='
        },
        controller:['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
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
        controller:['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
            $scope.settings = settings;
            $scope.Searcher = Searcher;
        }]
    };
})
.directive('adcanvas', function() {
    return {
        restrict:'E',
        templateUrl:'views/search/canvas.html',
        replace:false,
        scope:{
            card: '='
        },
        controller:['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
            $scope.settings = settings;
            $scope.Searcher = Searcher;
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
})
.factory('Util', function() {
    return {
        matchkey:function(origstr, destArr) {
            var orig = origstr.split(',');
            angular.forEach(orig, function(item1) {
                for (i = 0; i < destArr.length;i++){
                      if (item1 == destArr[i].key) {
                          destArr[i].selected = true;
                      }
                }
            });
        }
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
            searcher.defSearchFields = searcher.prototype.defSearchFields = "message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink";
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
            filter: vm.defFilterOption
            };
            vm.params = angular.copy(vm.defparams);
            vm.oldParams = null;
            searcher.ADS_TYPE = searcher.prototype.ADS_TYPE = ADS_TYPE;
            vm.ADS_CONT_TYPE = ADS_CONT_TYPE;
            vm.pageCount = settings.searchSetting.pageCount;
            vm.ads = {
                total_count: 0
            };
            vm.isend = false;
            //函数的静态方法以及对象的方法
            searcher.getAdsType = searcher.prototype.getAdsType = function(item, type) {
                // console.log(type, item.show_way, item.show_way & type);
                if (item.show_way & type)
                    return true;
                return false;
            };
            searcher.prototype.search = function(params, clear) {
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
                                value.link = JSON.parse(value.link);
                                value.buttonlink = JSON.parse(value.buttonlink);
                                value.buttondesc = JSON.parse(value.buttondesc);
                                value.name = JSON.parse(value.name);
                                value.description = JSON.parse(value.description);
                                value.local_picture = JSON.parse(value.local_picture);
                            } else if (value.type == vm.ADS_CONT_TYPE.CANVAS) {
                                value.link = JSON.parse(value.link);
                                value.local_picture = JSON.parse(value.local_picture);
                                if (vm.getAdsType(value, vm.ADS_TYPE.rightcolumn)) {
                                    value.watermark = JSON.parse(value.watermark);
                                }
                            } else if (value.type == vm.ADS_CONT_TYPE.SINGLE_VIDEO) {
                                value.local_picture = JSON.parse(value.local_picture);
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
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util',
    function($rootScope, $scope, settings, $http, Searcher, $filter, SweetAlert, $state, $location, Util) {
        //搜索流程:location.search->searchOption->adSearcher.params
        
        //将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
        function searchToQuery(option, searcher) {
            var query = {};
            if (option.search.text)
                query.searchText = option.search.text;
            if (option.search.fields != searcher.defSearchFields)
                query.searchFields =  option.search.fields;
            if (option.filter.date.startDate && option.filter.date.endDate) {
                query.startDate =  option.filter.date.startDate.format('YYYY-MM-DD');
                query.endDate =  option.filter.date.endDate.format('YYYY-MM-DD');
            }
            if (option.filter.type)  {
                query.type =  option.filter.type;
            }
            if (option.filter.lang) {
                query.lang =  option.filter.lang;
            }
            if (option.filter.state) {
                query.state =  option.filter.state;
            }
            if (option.domain.text) {
                query.domain =  JSON.stringify(option.domain);
            }
            if (option.audience.text) {
                query.audience = JSON.stringify(option.audience);
            }

            //category, format, buttondesc
            if (option.filter.categoryString) {
                query.category =  option.filter.categoryString;
            }
            if (option.filter.formatString) {
                query.format = option.filter.formatString;
            }
            if (option.filter.buttondescString) {
                query.buttondesc = option.filter.buttondescString;
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
                    for (i = 0; i < option.range.length;i++){
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
        }
        $scope.swal = function(msg) {
            SweetAlert.swal(msg);
        };
        $scope.adSearcher = new Searcher();
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
            $scope.filterOption.buttondescString = buttondesc.join(',');
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
.controller('QuickSidebarController', ['$scope', function($scope) {

/* Setup Layout Part - Quick Sidebar */
//这个控制器与广告是强绑定的，这里直接指向$parent的这个方式是非常不友好的，加大了耦合
    $scope.$on('$includeContentLoaded', function() {
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
            items:$scope.$parent.filterOption.category,
            all: false,
            collapse:true,
            defnum:5,
            toggle:function() {
            angular.forEach($scope.$parent.filterOption.category, function(value, key) {
                if ($scope.categoryOpt.all)
                    value.selected = true;
                else
                    value.selected = false;
            });
            }
        };
        $scope.buttondescOpt = {
            items:$scope.$parent.filterOption.buttondesc,
            all: false,
            collapse:true,
            defnum:5,
            toggle:function() {
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
        setTimeout(function() {
            QuickSidebar.init(); // init quick sidebar        
        }, 100);
    });
}]);
