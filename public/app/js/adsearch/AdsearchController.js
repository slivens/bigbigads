/* adsearch controller */
var app = angular.module('MetronicApp');
    app.directive('sweetalert', ['SweetAlert', function(SweetAlert) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    SweetAlert.swal(attrs.title);
                });
            }
        };
    }]);
    app.directive('fancybox', ['$compile', '$timeout', function($compile, $timeout) {
        return {
            link: function($scope, element, attrs) {
                element.fancybox({
                    hideOnOverlayClick: false,
                    hideOnContentClick: false,
                    enableEscapeButton: false,
                    showNavArrows: true,
                    onComplete: function() {
                        $timeout(function() {
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
            link: function(scope, element, attrs) {
                element.select2();
            }
        };
    })
    .directive('ionRangeSlider', function() {
        return {
            scope: {
                ngFrom: '=',
                ngTo: '=',
                ngMax: '=',
                ngMin: '='
            },
            link: function(scope, element, attrs) {
                var slider;
                element.ionRangeSlider({
                    type: "double",
                    from: scope.ngForm,
                    to: scope.ngTo,
                    max: scope.ngMax,
                    min: scope.ngMin,
                    onChange: function(data) {
                        if (data.from != scope.ngForm)
                            scope.ngFrom = data.from;
                        if (data.to != scope.ngTo)
                            scope.ngTo = data.to;
                    }
                });
                slider = element.data('ionRangeSlider');
                scope.$watch('ngFrom', function(newValue, oldValue) {
                    slider.update({
                        from: newValue
                    });
                });
                scope.$watch('ngTo', function(newValue, oldValue) {
                    slider.update({
                        to: newValue
                    });
                });
            }
        };
    })
    .directive('rankBoard', ['$compile', '$timeout', function($compile, $timeout) {
        return {
            scope: {
                title: '@'
            },
            templateUrl: 'tpl/dashboard.html',
            transclude: true,
            link: function(scope, element, attrs) {
                scope.title = attrs.title;
            }
        };
    }])
    .directive('advideo', ['$compile', '$timeout', function($compile, $timeout) {
        return {
            restrict: 'EA',
            link: function(scope, element, attrs) {
                var poster = $('<div class="advideo"></div>');
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
            restrict: 'E',
            templateUrl: 'views/search/single-image.html',
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
                $scope.settings = settings;
            }]
        };
    })
    .directive('singleVideo', function() {
        return {
            restrict: 'E',
            templateUrl: 'views/search/single-video.html',
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
                $scope.settings = settings;
                $scope.Searcher = Searcher;
            }]
        };
    })
    .directive('adcanvas', function() {
        return {
            restrict: 'E',
            templateUrl: 'views/search/canvas.html',
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
                $scope.settings = settings;
                $scope.Searcher = Searcher;
            }]
        };
    })
    .directive('carousel', function() {
        return {
            restrict: 'E',
            templateUrl: 'views/search/carousel.html',
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', function($scope, settings) {
                $scope.settings = settings;
            }]
        };
    })
    .factory('Util', function() {
        return {
            matchkey: function(origstr, destArr) {
                var orig = origstr.split(',');
                angular.forEach(orig, function(item1) {
                    for (i = 0; i < destArr.length; i++) {
                        if (item1 == destArr[i].key) {
                            destArr[i].selected = true;
                        }
                    }
                });
            }
        };
    });

app.factory('Searcher', ['$http', '$timeout', 'settings', 'ADS_TYPE', 'ADS_CONT_TYPE', '$q',
    function($http, $timeout, settings, ADS_TYPE, ADS_CONT_TYPE, $q) {
        //opt = {searchType:'adser', url:'/api/forward/adserSearch'}
        var searcher = function(opt) {
            var vm = this;
            vm.opt = opt;
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
            };
            if (opt && opt.limit) {
                vm.defparams.limit = opt.limit;
            }
            searcher.defSearchFields = searcher.prototype.defSearchFields = "message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink";
            searcher.defFilterOption = searcher.prototype.defFilterOption = {
                type: "",
                date: {
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
                isDurationDirty: function() {
                    //这里引用this,关键是要对this这个指针有透彻的理解，this是会变的。当函数作为对象的属性时，this就是对象，即当以obj.isDurationDirty()，this就是obj。如果fn=obj.isDurationDirty();fn();那么this就是window。当函数出问题时一定要检查下this。
                    if (this.duration.from == searcher.defFilterOption.duration.from &&
                        this.duration.to == searcher.defFilterOption.duration.to)
                        return false;
                    return true;
                },
                isSeeTimesDirty: function() {
                    if (this.seeTimes.from == searcher.defFilterOption.seeTimes.from &&
                        this.seeTimes.to == searcher.defFilterOption.seeTimes.to)
                        return false;
                    return true;
                }

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
                filter: searcher.defFilterOption
            };
            vm.params = angular.copy(vm.defparams);
            vm.oldParams = null;
            vm.ADS_CONT_TYPE = ADS_CONT_TYPE;
            vm.pageCount = settings.searchSetting.pageCount;
            vm.ads = {
                total_count: 0
            };
            vm.isend = false;
            vm.search = function(params, clear) {
                var defer = $q.defer();
                //获取广告搜索信息
                var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/api/forward/adsearch');
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
                                // if (value.snapshot && value.snapshot != "")
                                //      value.snapshot = JSON.parse(value.snapshot);
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

                        if (opt && opt.searchType == "adser") {
                            if (clear) {
                                vm.ads = res.data;
                            } else {
                                vm.ads.adser = vm.ads.adser.concat(res.data.adser);
                            }
                        } else {
                            if (clear || vm.ads.total_count === 0) {
                                vm.ads = res.data;
                            } else {
                                vm.ads.ads_info = vm.ads.ads_info.concat(res.data.ads_info);
                            }
                        }
                        defer.resolve(vm.ads);
                    } else {
                        defer.reject(vm.ads);
                    }
                    console.log(res.data);
                    $timeout(function() {
                        vm.busy = false;
                    }, 500);

                }, function(res) {
                    vm.busy = false;
                    // console.log(res);
                });
                return defer.promise;
            };

            vm.getMore = function() {
                if (vm.busy)
                    return;
                vm.params.limit[0] += settings.searchSetting.pageCount;
                vm.search(vm.params, false);
            };
            vm.filter = function() {
                var promise;
                if (vm.params == vm.oldParams)
                    return;
                vm.params.limit[0] = 0;
                promise = vm.search(vm.params, true);
                vm.oldParams = angular.copy(vm.params);
                return promise;
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
        searcher.ADS_TYPE = searcher.prototype.ADS_TYPE = ADS_TYPE;
        //函数的静态方法以及对象的方法
        searcher.getAdsType = searcher.prototype.getAdsType = function(item, type) {
            if (item.show_way & type)
                return true;
            return false;
        };
        return searcher;
    }
])
.service('Resource', ['$resource', 'settings', 'SweetAlert', function($resource, settings, SweetAlert) {
    function f(name) {
        var vm = this;
        var url = settings.remoteurl + '/' + name + '/:id';
        var r = $resource(url, {id:'@id'}, {
                update: {method:'PUT'}
            });
        vm.error = true;
        vm.queried = false;
        vm.items = [];
        angular.extend(vm, {
        get:function(params) {
            var promise = r.query(params).$promise;
            promise.then(function(items) {
                vm.items = items;
                vm.error = false;
            }, function(res) {
                vm.error = true;
                console.log(res);
            }).finally(function() {
                vm.queried = true;
            });
            return promise;
        },
        del:function(item) {
            console.log('ondeleting', item);
            var promise = item.$delete();
            promise.then(function(item) {
                vm.items.splice($.inArray(item, vm.items), 1);
            }, vm.handleError);
            return promise;
        },
        save:function(item) {
            var promise;
            var update = false;
            if (item.$save) {
               promise = item.$update();
               update = true;
            } else
               promise = r.save(item).$promise;
            promise.then(function(newItem) {
                if (update) {
                    for (var i = 0; i < vm.items.length; ++i) {
                        if (vm.items[i].id == item.id) {
                            vm.items.splice(i, 1, newItem);
                            break;
                        }
                    }
                } else
                    vm.items.push(newItem);
            }, vm.handleError);
            return promise;
        },
        handleError:function(res) {
            console.log(res);
            if (res.data instanceof Object) {
                SweetAlert.swal(res.data.desc);
            } else {
                SweetAlert.swal(res.statusText);
            }
        }
    });
    }
    return f;
}]);
app.factory('Bookmark', ['Resource', '$uibModal', 'SweetAlert', 'BookmarkItem', function(Resource, $uibModal, SweetAlert, BookmarkItem) {
    var bookmark = new Resource('bookmark');
    bookmark.subItems = [];
    bookmark.addBookmark = function(item) {
        return $uibModal.open({
            templateUrl: 'views/bookmark-add-dialog.html',
            size: 'sm',
            animation: true,
            controller:['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
                if (item)
                    $scope.item = angular.copy(item);
                else
                    $scope.item = {name:""};
                console.log(item);
                $scope.bookmark = bookmark;
                $scope.cancel = function() {
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.save = function(item) {
                    $scope.promise = bookmark.save(item);
                    $scope.promise.then(function() {
                        $uibModalInstance.dismiss('success');
                    });

                };
            }]
        });
    };
    bookmark.delBookmark = function(item) {
			SweetAlert.swal({   
              title: 'Are you sure?',   
              text: 'All the bookmarks will be removed,too?',   
              type: 'warning',   
              showCancelButton: true,   
              // confirmButtonColor: '#DD6B55',   
              confirmButtonText: 'Yes',   
              cancelButtonText: 'Cancel',   
              closeOnConfirm: true,   
              closeOnCancel: true 
            }, function(isConfirm){  
              if (isConfirm) {     
				bookmark.del(item);
              } 
            });
    };
    //添加收藏
    bookmark.collect = function(type, item) {
        
    };
    bookmark.showDetail = function(bid) {
        var promise = BookmarkItem.get({
            where:JSON.stringify([["bid", "=", bid]])
        });
        promise.then(function(items) {
            bookmark.subItems = items;
            bookmark.bid = bid;
        });
        return promise;
    };
    return bookmark;
}]);
app.controller('AdsearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams',
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
            $scope.swal = function(msg) {
                SweetAlert.swal(msg);
            };
            $scope.adSearcher = new Searcher();
            // $scope.adSearcher.search($scope.adSearcher.defparams, true);
            $scope.reverseSort = function() {
                $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
                $scope.adSearcher.filter();
            };
            $scope.card = {
                end: true
            };
            $scope.id = $stateParams.id;
            $scope.adSearcher.addFilter({
                field: 'ads_id',
                value: $scope.id
            });
            $scope.adSearcher.filter().then(function(ads) {
                //只取首条消息
                $scope.card = $scope.ad = ads.ads_info[0];
                //表示广告在分析模式下，view根据这个字段区别不同的显示
                $scope.card.indetail = true;
                $scope.card.end = false;
                if ($scope.card.whyseeads_all)
                    $scope.card.whyseeads_all = $scope.card.whyseeads_all.split('\n');
                if ($scope.card.whyseeads)
                    $scope.card.whyseeads = $scope.card.whyseeads.split('\n');
            }, function(res) {
                $scope.card.end = true;
            });

            $scope.goback = function() {
                $window.history.back();
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

.controller('QuickSidebarController', ['$scope', 'settings', function($scope, settings) {

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
                url: '/api/forward/adserSearch'
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
    .controller('AdserAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$http', '$uibModal', '$q',
        function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $http, $uibModal, $q) {

            function getAdserAnalysis(username) {
                var params = {
                    "search_result": "adser_analysis",
                    "where": [{
                        "field": "adser_username",
                        "value": username
                    }]
                };
                return $http.post(settings.remoteurl + '/api/forward/adserAnalysis',
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
             * 初始化单个广告主的图表
             */
            function initChart(card) {
                card.mediaTypeConfig = initPie(card.media_type_groupby, "Media Type");
                card.adLangConfig = initPie(card.ad_lang_groupby, 'Advertise Language');
                card.showwayConfig = initPie(card.showway_groupby, 'Show Way', {
                    "1": "timeline",
                    "2": "mobile",
                    "3": "timeline&mobile",
                    "4": "rightcolumn"
                });
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
            promises[0] = getAdserAnalysis($scope.username);
            promises[0].then(function(res) {
                console.log("obj:", res.data);
                $scope.card = res.data;
                formatAdser($scope.card);
                initChart($scope.card);
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
                var showwayGroup = [$scope.card.showway_groupby];
                var nameArr = [$scope.card.adser_name];
                for (var key in $scope.competitors) {
                    langGroup.push($scope.competitors[key].ad_lang_groupby);
                    mediaTypeGroup.push($scope.competitors[key].media_type_groupby);
                    showwayGroup.push($scope.competitors[key].showway_groupby);
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
                url: '/api/forward/adserSearch',
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

app.controller('RankingController', ['$scope', 'settings', '$http', function($scope, settings, $http) {
    function get(rankList) {
        $http.get(rankurl).then(function(res) {
            rankList.items = res.data;
        }, function(res) {
            console.log(res);
        });
    }

    var rankurl = settings.remoteurl + '/ranking';
    $scope.rankList = {items:null};
    get($scope.rankList);
}]);
app.factory('BookmarkItem', ['Resource', '$uibModal', 'SweetAlert', function(Resource, $uibModal, SweetAlert) {
    var bookmarkItem = new Resource('BookmarkItem');

    return bookmarkItem;
}]);

app.controller('BookmarkController', ['$scope', 'settings', '$http', 'Resource', '$uibModal', 'User', 'Bookmark', 'BookmarkItem', '$location', 'Searcher', 'SweetAlert', function($scope, settings, $http,  Resource, $uibModal, User, Bookmark, BookmarkItem, $location, Searcher, SweetAlert) {
    

    function loadAds(type, bid) {
        Bookmark.showDetail(bid).then(function(items) {
            var wanted = [];
            var wantedAdsers = [];
            var adSearcher = new Searcher();
            var adserSearcher = new Searcher({
                    searchType: 'adser',
                    url: '/api/forward/adserSearch'
                });
            angular.forEach(items, function(item) {
                if (Number(item.type) === 0 && item.bid == bid) {
                    wanted.push(item.ident);
                } else if (Number(item.type) === 0 && item.bid == bid) {
                    wantedAdsers.push(item.ident);
                }
            });
            console.log(type, bid, wanted, wantedAdsers);
        
            //获取广告
            adSearcher.addFilter({
                field: 'ads_id',
                value: wanted.join(',')
            });
            adSearcher.filter().then(function(data) {
                $scope.ads = data;
                $scope.ads.bookmark = true;
            }, function() {
                $scope.ads = [];
            });
            //获取广告主
            adserSearcher.addFilter({
                field:'adser_username',
                value:wantedAdsers.join(',')
            });
            adserSearcher.filter().then(function(data) {
                $scope.adsers = data;
                $scope.adsers.bookmark = true;
            }, function() {
                $scope.adsers = [];
            });
        });
    }
    function load(type, bid) {
        loadAds(type, bid);
        $location.search('bid', bid);
        $location.search('type', type);
    }
    function init() {
        var search = $location.search();
        if(search.bid) {
           loadAds(search.type, search.bid);
        } else {
            if (Bookmark.items.length) {
                loadAds(0, Bookmark.items[0].id);
            }
        }
    }
    $scope.Searcher = Searcher;
    $scope.bookmark = Bookmark;
    $scope.bookmark.type = 0;//当前显示类型
    $scope.load = load;
    $scope.ads = {};//广告列表
    $scope.adsers = {};//广告主列表
    $scope.cancelBookmark = function(type, card) {
        SweetAlert.swal({
            title:'Are you sure?',
            text:'Cancel the ' + type > 0 ? card.adser_username : card.event_id,
            type:'warning',
              showCancelButton: true,   
              // confirmButtonColor: '#DD6B55',   
              confirmButtonText: 'Yes',   
              cancelButtonText: 'Cancel',   
              closeOnConfirm: true,   
              closeOnCancel: true 
        }, function(isConfirm) {
            if (!isConfirm)
                return;
            console.log("ident",card.event_id);
            console.log(Bookmark.subItems);
            angular.forEach(Bookmark.subItems, function(item) {
                if (type != item.type)
                    return;
                if (type === 0 && card.event_id == item.ident) {
                    BookmarkItem.del(item).then(function() {
                        for (var i = 0; i < $scope.ads.ads_info.length; ++i) {
                            if ($scope.ads.ads_info[i] == card) {
                                console.log("idx", i);
                                $scope.ads.ads_info.splice(i, 1);
                                break;
                            }
                        }
                    });
                }
            });
        });
    };
    User.getInfo().then(function() {
        if (User.info.login)
            Bookmark.get({uid:User.info.user.id}).then(function() {
                init();    
            });
    });

    
    setTimeout(function() {
        QuickSidebar.init(); // init quick sidebar        
    }, 200);
}]);
app.controller('BookmarkAddController', ['$scope', 'Bookmark', 'BookmarkItem', 'User', '$q', function($scope, Bookmark, BookmarkItem, User, $q) {
    $scope.select = [];
    $scope.bookmark = Bookmark;
    $scope.add = function(card) {
        var promises = [];
        for(var i = 0; i < $scope.select.length; i++) {
            if ($scope.select[i]) {
                var subItem = {uid:User.info.user.id, bid:Bookmark.items[i].id};
                if (card.event_id) {
                    subItem.type = 0;
                    subItem.ident = card.event_id;
                } else if(card.adser_username) {
                    subItem.type = 1;
                    subItem.ident = card.adser_username;
                }
                promises.push(BookmarkItem.save(subItem));
                console.log(subItem);
            }
        }
        $scope.addPromise = $q.all(promises);
        $scope.addPromise.finally(function() {
            card.showBookmark = false;//耦合
        });
    };
    if (!Bookmark.queried) {
        User.getInfo().then(function() {
            if (User.info.login) {
                $scope.queryPromise = Bookmark.get({uid:User.info.user.id});
            }
        });
    }
}]);

