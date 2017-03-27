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
                element.select2({
                    maximumSelectionLength: 10
                    });
                // scope.$watch(attrs.ngModel, function(newValue, oldValue) {
                //     if (newValue != oldValue) {
                //         $timeout(function() {
                //             scope.$apply();
                //         });
                //     }
                // });

                scope.$on('userChanged', function() {
                    //当用户信息改变时重新生成，这是由于我们的权限锁加在select的option里面，但是select2这个插件只在首次下拉时，根据option的状态初始化一次。所以后面option的状态不论怎么变，都体现不到select2里面来。所以要整个插件重新生成下。
                    element.select2({
                        maximumSelectionLength: 10
                    });
                });
            }
        };
    })
    .directive('bsSelect', ['$timeout', function($timeout) {
        return {
            restrict: 'C',
            link: function(scope, element, attrs) {
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
            scope: {
                sort: '='
            },
            link: function(scope, element, attrs) {
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
                    $(this).data('sort', flipSort[1 - flipSort.indexOf(sort)]);
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
            link: function(scope, element, attrs) {
                function check() {
                    var key = attrs.key;
                    if (!key)
                        return;
                    if (!User.can(key) || !User.usable(key, attrs.val)) {
                        if (element.find('.lock').length)
                            return;
                        if ((attrs.trigger == "lockButton")&&(attrs.buttontype == "filter")) {
                            element.on('click.openUpgrade', function() {
                                User.openUpgrade();
                                return false;
                            });
                        }
                        if ((attrs.trigger == "lockButton")&&(attrs.buttontype != "filter")) {
                            element.attr("disabled", "disabled");
                            element.append('<i class="fa fa-lock  lock"></i>');
                        } else if (attrs.trigger == "disabled")
                            element.attr("disabled", "disabled");
                        else
                            element.append('<i class="fa fa-lock  lock"></i>');
                    } else {
                        if (attrs.trigger == "lockButton") {
                            element.removeAttr("disabled");
                            element.children('.lock').remove();
                            element.off('click.openUpgrade');
                        } else if (attrs.trigger == "disabled")
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
            link: function(scope, element, attrs) {
                var defmatch = {
                    duration: "%val Days",
                    platform: function(val) {
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
                    ad_date: "%val Days",
                    result_per_search: "%val records",
                    ranking: "Top %val",
                    ranking_export: "Top %val"
                };
                var key = attrs.key;
                var usageMode = attrs.mode == "usage" ? true : false;
                var val = scope.$eval(attrs.value);
                var text;
                // console.log(val);
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
    .directive('adlink', ['Util', function(Util) {
        return {
            link:function(scope, element, attrs) {
                element.on('click', function() {
                    Util.openAd(attrs.eventid);
                });
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
                poster.html('<a class="playbtn"><i class="fa xg-icon-play"></i></a>');
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
    .directive('fixsidebar', ['$timeout', '$rootScope', function($timeout, $rootScope) {
        return {
            scope: true,
            link:function(scope, element, attrs) {
                var oldtop = 0;
                
                 $timeout(function() {
                    oldtop = element[0].getBoundingClientRect().top;
                    if (oldtop < 80)
                        oldtop = 80;
                 }, 100);
                //更新浮动
                function updatefix(){
                    var top = element.parent()[0].getBoundingClientRect().top;
                    if (top < 0)
                        $(element).animate({ top: oldtop - top });
                    else if (top > 0)
                        $(element).animate({ top: oldtop});
                    // if(!$rootScope.$$phase){
                    //     scope.$digest();
                    // }
                }
                (function loop(){
                    setTimeout(loop,1000);
                    updatefix();
                })();
            }
        };
    }])
    .directive('audience', ['SweetAlert', function(SweetAlert) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    if(attrs.title!==null||attrs.title!==underfind||attrs.title!==""){
                        var why_see = attrs.title.split("\n");
                        var show_length = 0;
                        var table = "";
                        var show_text = "";
                        table_head = '<table class="table table-striped table-hover table-bordered text-left xg-audience-table"><tbody>';
                        table_body = '';
                        table_foot = '</tbody></table>';
                        if(why_see.length>10) {
                            show_length = 10;
                            show_text = '<span class="font-yellow-gold">You can see all other audience target in the ad detail page.</span>';
                        }else {
                            show_length = why_see.length;
                        }
                        for(var i=0;i<show_length;i++) {
                            table_body += '<tr><td>' + why_see[i] +'</td></tr>';
                        }
                        table = table_head + table_body + table_foot;
                        SweetAlert.swal({
                            html: true,
                            title: '',
                            text: table+show_text, 
                            confirmButtonText: "close",
                            closeOnConfirm: false,
                            closeOnCancel: false                         
                        });
                    }
                });
            }
        };
    }])
    .directive('adserlink', ['$state', function($state) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    var url = "";
                    var name = attrs.name.replace(/<[^>]+>/g,"");
                    var username = attrs.username.replace(/<[^>]+>/g,"");
                    url = $state.href('adser', {name:name, adser:username});
                    window.open(url,'_blank');
                });
            }
        };
    }])
    .directive('canvaslink', ['$state', function($state) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    var url = "https://facebook.com/"+attrs.id+"/"+attrs.eventid;
                    window.open(url);
                });
            }
        };
    }])
    //去重复：定义一个过滤器，用于去除重复的数组，确保显示的每一条都唯一
    .filter('unique', function () {  
        return function (collection) { 
            var output = []; 
            angular.forEach(collection, function (item) {  
                var itemname = item;  
                if (output.indexOf(itemname) <0) //indexOf表示首次出现的位置，===-1表示不存在
                 {  
                    output.push(itemname);  //输入到数组
                }  
            });  
            return output;  
        };  
    })
    .factory('Util', ['$uibModal', '$stateParams', 'SweetAlert' , 'User', '$state', function($uibModal, $stateParams, SweetAlert, User, $state) {
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
            initPie: function(jsonSrc, title, labels) {
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
            },
            openAd: function(id) {
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
            },
            getTrendData:function(json) {
				if (!json || !json.trend) {
					return null;
				}
				var length = json.trend.length;
				var endDate = moment(json.day, 'YYYY-MM-DD');
                var xs, ys, i;
                if (json.trend[0] < 0) {
                    xs = [];
                    ys = [];
                } else {
                    xs = [endDate.format('YYYY-MM-DD')];
                    ys = [json.trend[1]];
                }
				for (i = 1; i < length; ++i) {
                    if (json.trend[i] >= 0) {
                        xs.push(endDate.subtract(1, 'days').format('YYYY-MM-DD'));
                        ys.push(json.trend[i]);
                    }
				}
				xs = xs.reverse();
				ys = ys.reverse();
                // console.log("trend data", xs, ys);
                return [xs, ys];
            },
			/**
             * 单个广告的趋势图
             * @param json 包含day与trend数据的对象
             * @param title 标题
             * @param name 曲线名称
             *
             * @return 返回highcharts图表的配置
			 */
            initTrend:function(json, title, name) {
                var data = this.getTrendData(json);
                if (data === null)
                    return;
				return {
					title: {
						text: title
					},
					xAxis: {
						categories: data[0]
					},
					yAxis: {
						title: {
							text: title
						},
						plotLines: [{
							value: 0,
							width: 1,
						}]
					},
					series: [{
						name: name,
						data: data[1]
					}],
                    className:"response-width",
                    credits:false
				};
			},
            hint:function(res) {
                if (res.data instanceof Object) {
                    SweetAlert.swal(res.data.desc);
                } else {
                    SweetAlert.swal(res.statusText);
                }
            },
            isNumberLimit:function(value) {
                if(User.info.user.role.name !='Free') return true;
                 if(!value) return true;
                var valueArray = value.split(" ");
                if(valueArray.length>1) {
                    return false;
                }else {
                    return true;
                }
            },
            isLengthLimit:function(value) {
                if(!value) return true;
                if(value.length>300) {//20长度仅用于测试
                    return false;
                }else {
                    return true;
                }
            },
            //判断是否为数组
            isArray:function(value){
                return angular.isArray(value);
            },
            isFilterLimit:function(filter,searchOption) {
                if((User.info.user.role.name !='Free')||User.login) {
                    return true;
                }
                var isLimit = false;
                var isDateLimit;
                var isFormatLimit;
                var isCallToActionLimit;
                var isLangLimit; 
                var isRangeSelectedLimit;  
                    //console.log(filter);
                if((filter.hasOwnProperty('formatSelected'))&&(filter.formatSelected.length===0)){
                    isFormatLimit = false;
                }else if(filter.hasOwnProperty('formatSelected')){
                    isFormatLimit = true;
                }
                if((filter.hasOwnProperty('callToAction'))&&(filter.callToAction.length===0)){
                    isCallToActionLimit = false;
                }else if(filter.hasOwnProperty('callToAction')){
                    isCallToActionLimit = true;
                }
                if((filter.hasOwnProperty('lang'))&&(filter.lang.length===0)){
                    isLangLimit = false;
                }else if(filter.hasOwnProperty('lang')){
                    isLangLimit = true;
                }
                if((searchOption.hasOwnProperty('rangeselected'))&&(searchOption.rangeselected.length===0)){
                    isRangeSelectedLimit = false;
                }else if(searchOption.hasOwnProperty('rangeselected')){
                    isRangeSelectedLimit = true;
                }
                if(filter.date.endDate!==null){
                    isDateLimit = true;
                }
                if(isFormatLimit||isCallToActionLimit||isLangLimit||isDateLimit||isRangeSelectedLimit) isLimit=true;
                if(isLimit){
                    return false;
                }else {
                    return true;
                }
            }
        };
    }]);
app.service('Resource', ['$resource', 'settings', 'SweetAlert', function($resource, settings, SweetAlert) {
    function f(name) {
        var vm = this;
        var url = settings.remoteurl + '/' + name + '/:id';
        var r = $resource(url, {
            id: '@id'
        }, {
            update: {
                method: 'PUT'
            }
        });
        vm.error = true;
        vm.queried = false;
        vm.items = [];
        angular.extend(vm, {
            get: function(params) {
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
            del: function(item) {
                // console.log('ondeleting', item);
                var promise = item.$delete();
                promise.then(function(item) {
                    vm.items.splice($.inArray(item, vm.items), 1);
                }, vm.handleError);
                return promise;
            },
            save: function(item) {
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
            handleError: function(res) {
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

