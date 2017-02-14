/* common js */

if (!app)
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
            element.select2( {
                placeholder:'Select',
                allowClear:true
            });
            // scope.$watch(attrs.ngModel, function(newValue, oldValue) {
            //     if (newValue != oldValue) {
            //         $timeout(function() {
            //             scope.$apply();
            //         });
            //     }
            // });

        }
    };
})
.directive('bsSelect', ['$timeout', function($timeout) {
    return {
        restrict:'C',
        link:function(scope, element, attrs) {
            element.selectpicker({
                iconBase: 'fa',
                tickIcon: 'fa-check'
            });
            $timeout(function() {
                element.selectpicker('refresh');
            });

            scope.$on('userChanged', function() {
                $timeout(function() {
                    element.selectpicker('refresh');
                }, 800);
            });
        }
    };
}])
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
//table增加排序功能
.directive('sorttable', ["$timeout", "$compile", function($timeout, $compile) {
    return {
        scope:{
            sort:'='
        },
        link:function(scope, element, attrs) {
            // scope.$table = {$data:scope.$eval(attrs.source)};
            function init() {
                element.find(".sort").remove();
                element.find('th[data-field]').each(function() {
                    if ($(this).data('field') != scope.sort.field) {
                        $(this).append('<i class="fa fa-sort fa-fw sort"></i>');
                    } else {
                        if (!scope.sort.reverse) 
                            $(this).append('<i class="fa fa-sort-asc fa-fw sort"></i>');
                        else
                            $(this).append('<i class="fa fa-sort-desc fa-fw sort"></i>');
                    }
                });
            }
            init();
            element.find('th[data-field]').bind('click', function() {
                var flipSort = ["asc", "desc"];
                var sort = $(this).data('sort');
                $(this).data('sort',  flipSort[1 - flipSort.indexOf(sort)]);
                scope.sort.field = $(this).data('field');
                scope.sort.reverse = (sort == flipSort[1]);
                init();
                $timeout(function() {
                    scope.$apply();
                });
                console.log("sort:", scope.sort);
            });
        }
    };
}])
//当没有某个操作权限时就会加锁
.directive('policyLock', ['User', function(User) {
    return {
        link:function(scope, element, attrs) {
            function check() {
                var key = attrs.key;
                if (!User.can(key) || !User.usable(key, attrs.val)) {
                    if (element.find('.lock').length)
                        return;
                    if (attrs.trigger == "disabled")
                        element.attr("disabled", "disabled");
                    else
                        element.append('<i class="fa fa-lock  lock"></i>');
                } else {
                    if (attrs.trigger == "disabled")
                        element.removeAttr("disabled");
                    else
                        element.children('.lock').remove();
                }
            }
            check();
            scope.$on('userChanged', function() {
                check();
            });
        }
    };
}])
//资源(权限与策略以及使用情况)的显示格式化
.directive('policyFormatter', ['POLICY_TYPE', 'ADS_TYPE', function(POLICY_TYPE, ADS_TYPE) {
    return {
        link:function(scope, element, attrs) {
            var defmatch = {
                duration:"%val Days",
                platform:function(val) {
                    var text = [];
                    var num = Number(val.value);
                    if (num & ADS_TYPE.timeline)
                        text.push("Timeline");
                    if (num & ADS_TYPE.rightcolumn)
                        text.push("Rightcolumn");
                    if (num & ADS_TYPE.mobile) 
                        text.push("Mobile");
                    return text.join(' & ');
                },
                ad_date:"%val Days",
                result_per_search:"%val records",
                ranking:"Top %val",
                ranking_export:"Top %val"
            };
            var key = attrs.key;
            var usageMode = attrs.mode == "usage" ? true : false;
            var val = scope.$eval(attrs.value);
            var text;
            console.log(val);
            if ((typeof val) == 'boolean') {
                if (val)
                    element.append('<i class="icon-check font-green-jungle"></i>');
                else
                    element.append('<i class="icon-close"></i>');
            } else {
                text = val.value;
                if (val.type == POLICY_TYPE.MONTH) {
                    text += "/Month";
                    if (usageMode) {
                        text = (val.used + "(" + text + ")");
                    }
                }
                if (val.type == POLICY_TYPE.DAY) {
                    text += "/Day";
                    if (usageMode) {
                        text = (val.used + "(" + text + ")");
                    }
                }
                if (val.type == POLICY_TYPE.PERMANENT && usageMode) {
                    text = (val.used + "/" + val.value);
                }
                if (defmatch[key]) {
                    if (typeof(defmatch[key]) == 'function') {
                        text = defmatch[key](val);
                    } else {
                        text = defmatch[key].replace("%val", text);
                    }
                }
                element.append(text);
            }
        }
    };
}])
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
            $scope.Searcher = Searcher;
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
        },
        initPie:function(jsonSrc, title, labels) {
            /**
             * jsonSrc:json字符串
             * title:标题
             * labels:是否将json的属性映射到labels对应值
             */
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
    };
});

app.factory('Searcher', ['$http', '$timeout', 'settings', 'ADS_TYPE', 'ADS_CONT_TYPE', '$q',
function($http, $timeout, settings, ADS_TYPE, ADS_CONT_TYPE, $q) {
    //opt = {searchType:'adser', url:'/forward/adserSearch'}
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
        vm.search = function(params, clear, action) {
            var defer = $q.defer();
            //获取广告搜索信息
            var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/forward/adsearch');
            if (action) {
                params.action = action;
            } else {
                delete params.action;
            }
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
            }, function(res) {
                defer.reject(res);
                // console.log(res);
            }).finally(function() {
                $timeout(function() {
                    vm.busy = false;
                }, 200);
            });
            return defer.promise;
        };

        vm.getMore = function() {
            if (vm.busy)
                return;
            vm.params.limit[0] += settings.searchSetting.pageCount;
            vm.search(vm.params, false);
        };
        vm.filter = function(action) {
            var promise;
            if (vm.params == vm.oldParams)
                return;
            vm.params.limit[0] = 0;
            promise = vm.search(vm.params, true, action);
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
        vm.getStatics = function(params, action) {
            var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/forward/adsearch');
            var promise; 
            if (action) {
                params.action = action;
            } else {
                delete params.action;
            }
            params.is_stat = 1;
            promise = $http.post(searchurl, params);
            promise.then(function(res) {
                console.log(res);
            });
            return promise;
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
