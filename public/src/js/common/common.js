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
app.directive('lazyImg', ['$timeout', 'Util', function($timeout, Util) {
	return {
        restrict:'A',
        scope:{
            lazyImg:'@'
        },
		link:function($scope, element, attrs) {
            $timeout(function() {   
                var imageSrc;
                var width = $(element).width();
                if (attrs.type === 'bba') {
                    //处理默认图片不能显示问题
                    if (!$scope.lazyImg) {
                        $scope.lazyImg = '/watermark/default.jpg';    
                        imageSrc = Util.getImageRandomSrc('') + $scope.lazyImg;              
                    } else {
                        imageSrc = Util.getImageRandomSrc('') + '/thumb.php?src='+ $scope.lazyImg.replace(/#.+\*.+$/, '') + '&size=' + width + 'x' ;
                    }        
                } else {
                    imageSrc = $scope.lazyImg;
                }
                element.attr('src', imageSrc);
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
                        //必须在$apply触发$digist，才能将该scope的修改反映到与之绑定的外部变量
                        //由于scope的修改，会导致scope.$watch监测到变化，所以在scope.$watch必须不应该重复赋值
                        scope.$apply(function() {
                            if (data.from != scope.ngForm)
                                scope.ngFrom = data.from;
                            if (data.to != scope.ngTo)
                                scope.ngTo = data.to;
                            });
                    }
                });
                slider = element.data('ionRangeSlider');
                scope.$watch('ngFrom', function(newValue, oldValue) {
                    //如果触发是由内部改变引起的就应该忽略，否则可能出现另外一个问题
                    //就是在执行这一步时，用户再次拖动界面上的滑块，两者同时发生时将出现滑块不按用户拖动的方向走
                    if (scope.ngFrom === newValue)
                        return;
                    // console.log("slider", scope.ngFrom, newValue);
                    slider.update({
                        from: newValue
                    });
                });
                scope.$watch('ngTo', function(newValue, oldValue) {
                    if (scope.ngTo === newValue)
                        return;
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
    .directive('rankBoard', ['$compile', '$timeout', 'TIMESTAMP', function($compile, $timeout, TIMESTAMP) {
        return {
            scope: {
                title: '@'
            },
            templateUrl: 'tpl/dashboard.html?t=' + TIMESTAMP,
            transclude: true,
            link: function(scope, element, attrs) {
                scope.title = attrs.title;
            }
        };
    }])
    .directive('advideo', ['$compile', '$timeout', 'Util', function($compile, $timeout, Util) {
        return {
            restrict: 'EA',
            link: function(scope, element, attrs) {
                var poster = $('<div class="advideo"></div>');
                var img = $('<img/>');
                var imageSrc;
                imageSrc = Util.getImageRandomSrc(attrs.preview);
                img.attr('src', imageSrc);
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
    .directive('singleImage', ['TIMESTAMP', function(TIMESTAMP) {
        return {
            restrict: 'E',
            templateUrl: 'views/search/single-image.html?t=' + TIMESTAMP,
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
                $scope.settings = settings;
                $scope.Searcher = Searcher;
            }]
        };
    }])
    .directive('singleVideo', ['TIMESTAMP', function(TIMESTAMP) {
        return {
            restrict: 'E',
            templateUrl: 'views/search/single-video.html?t=' + TIMESTAMP,
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
                $scope.settings = settings;
                $scope.Searcher = Searcher;
            }]
        };
    }])
    .directive('adcanvas', ['TIMESTAMP', function(TIMESTAMP) {
        return {
            restrict: 'E',
            templateUrl: 'views/search/canvas.html?t=' + TIMESTAMP,
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', 'Searcher', function($scope, settings, Searcher) {
                $scope.settings = settings;
                $scope.Searcher = Searcher;
            }]
        };
    }])
    .directive('carousel', ['TIMESTAMP', function(TIMESTAMP) {
        return {
            restrict: 'E',
            templateUrl: 'views/search/carousel.html?t=' + TIMESTAMP,
            replace: false,
            scope: {
                card: '='
            },
            controller: ['$scope', 'settings', function($scope, settings) {
                $scope.settings = settings;
            }]
        };
    }])
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
                function updatefix() {
                    var top = element.parent()[0].getBoundingClientRect().top;
                    if (top < 0)
                        $(element).animate({ top: oldtop - top });
                    else if (top > 0)
                        $(element).animate({ top: oldtop});
                    // if(!$rootScope.$$phase){
                    //     scope.$digest();
                    // }
                }
                (function loop() {
                    setTimeout(loop,1000);
                    updatefix();
                })();
            }
        };
    }])
    .directive('audience', ['$uibModal', 'TIMESTAMP', function($uibModal, TIMESTAMP) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    if(attrs.title) {
                        var why_see = attrs.title.split("\n");
                        return $uibModal.open({
                            templateUrl: 'views/audience.html?t=' + TIMESTAMP,
                            size: 'customer',     
                            animation: true,
                            controller:['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
                                $scope.why_see = why_see;
                                $scope.audienceLength = why_see.length;
                                $scope.close = function() {
                                    $uibModalInstance.dismiss('cancle');
                                };
                            }]
                        });
                    }       
                });
            }
        };
    }])
    .directive('adserlink', ['$state','User', function($state, User) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    if(!User.login) {
                        User.openSign();
                    }else {
                        var url = "";
                        var name = attrs.name.replace(/<[^>]+>/g,"");
                        var username = attrs.username.replace(/<[^>]+>/g,"");
                        url = $state.href('adser', {name:name, adser:username});
                        window.open(url,'_blank');
                    }
                });
            }
        };
    }])
    .directive('originlink', ['$state', function($state) {
        return {
            link: function(scope, element, attrs) {
                if(attrs.type == 4) {
                    element.attr("disabled", "disabled");
                    element.css("cursor", "not-allowed");
                }else{
                    element.bind('click', function() {
                        var url = "https://facebook.com/"+attrs.id+"_"+attrs.eventid;
                        window.open(url);
                    });
                }
            }
        };
    }])
    .directive('avatar', ['Util', function(Util){
        return {
            link: function(scope, element, attrs) {
                var imageSrc;
                imageSrc = Util.getImageRandomSrc(attrs.image);
                element.attr("src", imageSrc);
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
    .factory('Util', ['$uibModal', '$stateParams', 'SweetAlert' , 'User', '$state', 'settings', 'TIMESTAMP', '$http',function($uibModal, $stateParams, SweetAlert, User, $state, settings, TIMESTAMP, $http) {
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
                    templateUrl: 'views/ad-analysis.html?t=' + TIMESTAMP,
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
                if((User.info.user.role.name != 'Free') || User.login) {
                    return true;
                }
                var isLimit = false;
                var isDateLimit;
                var isFormatLimit;
                var isCallToActionLimit;
                var isLangLimit; 
                var isRangeSelectedLimit;  
                //console.log(filter);
                if((filter.hasOwnProperty('formatSelected')) && (filter.formatSelected.length === 0)){
                    isFormatLimit = false;
                }else if(filter.hasOwnProperty('formatSelected')){
                    isFormatLimit = true;
                }
                if((filter.hasOwnProperty('callToAction')) && (filter.callToAction.length === 0)){
                    isCallToActionLimit = false;
                }else if(filter.hasOwnProperty('callToAction')){
                    isCallToActionLimit = true;
                }
                if((filter.hasOwnProperty('lang')) && (filter.lang.length === 0)){
                    isLangLimit = false;
                }else if(filter.hasOwnProperty('lang')){
                    isLangLimit = true;
                }
                if((searchOption.hasOwnProperty('rangeselected')) && (searchOption.rangeselected.length === 0)){
                    isRangeSelectedLimit = false;
                }else if(searchOption.hasOwnProperty('rangeselected')) {
                    isRangeSelectedLimit = true;
                }
                if(isFormatLimit || isCallToActionLimit || isLangLimit || isRangeSelectedLimit) isLimit = true;
                if(isLimit) {
                    return false;
                }else {
                    return true;
                }
            },
            isAdvanceFilterLimit:function(filter) {
                //需求变更，free用户无高级过滤权限
                if (User.info.user.role.name != 'Free') {
                    return true;
                }
                var isLimit = false;
                var isDurationLimit = false;
                var isEngagementsLimit = false;
                if ((filter.duration.from !== 0) || (filter.duration.to !== 180)) isLimit = true;
                if ((filter.seeTimes.from !== 0) || (filter.seeTimes.to !== 180)) isLimit = true;
                angular.forEach(filter.engagements, function(data) {
                    if (data.max || data.min) { 
                        isEngagementsLimit = true;
                    }
                });
                if (isDurationLimit || isEngagementsLimit) isLimit = true;
                if (isLimit) {
                    return false;
                }else {
                    return true;
                }       
            },
            getImageRandomSrc:function(src) {
                //图片采用随机向4个域名发起请求的方式，
                //视频由于设置了不预加载，便不采取这样的方式
                var imageSrcIndex;
                var imageSrc;
                imageSrcIndex = parseInt(10 * Math.random()) % 4;
                imageSrc = settings.imgRemoteBase[imageSrcIndex] + src;
                return imageSrc;
            },
            trackState:function(location) {
                //console.log(location.search());
                if (location.search().track) {
                    var url = settings.remoteurl + '/trackState';
                    $http({
                        method: "post",
                        params: {"track" : location.search().track},
                        url: url
                    }).success(function(data) {
                        //defer.resolve(data);
                    });
                }
            },
            isMobile:function() {
                if (/iphone|nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|wap|mobile/i.test(navigator.userAgent.toLowerCase())) {
                    return true;
                } else {
                    return false;
                }
            },
            /*
             * 数组排序
             * arr 排序的数组，返回一个新的数组
             * item 要进行排序根据的项，可以为字符串或数字
             * n 排序方式，0 为正序，1 为逆序
             * 对字符串排序未详测试，项目对字符串排序功能暂时未用到
             */
            arrSort: function(arr, item, n) {
                //当为1个长度的数组或这非数组时，不做排序，原原本本返回
                if (arr.length < 2 || !arr) {
                    // console.warn("arr to short or not have value")
                    return arr
                } else {
                    // 如果是数字类型
                    if (typeof arr[0][item] === "number") {
                        return arr.sort(function(a, b) { return n > 0 ? b[item] - a[item] : a[item] - b[item] })
                    } else {
                        // 对字符串进行排序
                        return arr.sort(function(a, b) {
                            if (a[item] > b[item]) {
                                return n > 0 ? -1 : 1
                            } else  {
                                return n < 0 ? -1 : 1
                            }
                        })
                    }
                }
            },

            /*
             * 返回类似广告impression的完整数组
             * 将["2017-03-21":{"12","11"...}] 转为 [{"03-21","12"},{"03-22","11"}...]
             * 提供开始时间，与开始时间之后每天的访问量
             * 返回的数组为最后一次访问的前七天
             * time为开始时间，n为截取的数组长度,需要是7天，也可能是十天，data原始数据
             * 当n = 0 时，则显示所有
             */
            getTrendArr: function(time, n, data) {
                n = n === 0 ? data.length : n;
                if (data.length < 3) return false; // 对于长度小于3的不作处理
                else {
                    var newTime
                    try {
                        //获取的时间格式可能为2017-01-01，需将其转换
                        newTime = new Date(time.split("-")[0], time.split("-")[1] - 1, time.split("-")[2])
                    } catch (e) {
                        return false;
                    }
                    var arr = []
                    /*
                     * 给的数组长度可能大于7，也可能小于7
                     * 数组长度大于n则循环数组
                     */
                    for (var i = 0; i < (n > data.length ? n : data.length); i++) {
                        var arrTime = ((newTime.getMonth() + 1) < 10 ? '0' + (newTime.getMonth() + 1).toString() : (newTime.getMonth() + 1).toString()) + "-" + (newTime.getDate().toString() < 10 ? '0' + newTime.getDate().toString() : newTime.getDate())
                        if (arr.length < n) {
                            arr.push([arrTime, data[i] != undefined ? data[i] : ''])
                        } else {
                            arr.push([arrTime, data[i] != undefined ? data[i] : ''])
                            arr.shift() //加入新的，并删除第一个元素
                        }
                        newTime.setDate(newTime.getDate() + 1);
                    }
                    return arr;
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

