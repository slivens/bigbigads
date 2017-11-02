import '../../components/audience.js'
import '../../components/single-image.js'
import '../../components/single-video.js'
import '../../components/carousel.js'
import '../../components/adcanvas.js'

/* common js */
angular.module('MetronicApp').directive('sweetalert', ['SweetAlert', function(SweetAlert) {
    return {
        link: function(scope, element, attrs) {
            element.bind('click', function() {
                SweetAlert.swal(attrs.title)
            })
        }
    }
}])
angular.module('MetronicApp').directive('lazyImg', ['$timeout', 'Util', function($timeout, Util) {
    return {
        restrict: 'A',
        scope: {
            lazyImg: '@'
        },
        link: function($scope, element, attrs) {
            $timeout(function() {
                var imageSrc
                var width
                if (attrs.refObject) {
                    width = $(element).parents(attrs.refObject).width()
                } else {
                    width = $(element).width()
                }
                if (attrs.type === 'bba') {
                    // 处理默认图片不能显示问题
                    if (!$scope.lazyImg) {
                        $scope.lazyImg = '/watermark/default.jpg'
                        imageSrc = Util.getImageRandomSrc('') + $scope.lazyImg
                    } else {
                        // imageSrc = Util.getImageRandomSrc('') + '/thumb.php?src=' + $scope.lazyImg.replace(/#.+\*.+$/, '') + '&size=' + width + 'x'
                        imageSrc = Util.getImageRandomSrc('') + $scope.lazyImg.replace(/#.+\*.+$/, '') + '#size=' + width + 'x'
                    }
                } else {
                    imageSrc = $scope.lazyImg
                }
                element.attr('src', imageSrc)
            })
        }
    }
}])
angular.module('MetronicApp').directive('fancybox', ['$compile', '$timeout', function($compile, $timeout) {
    return {
        link: function($scope, element, attrs) {
            element.fancybox({
                hideOnOverlayClick: false,
                hideOnContentClick: false,
                enableEscapeButton: false,
                showNavArrows: true,
                onComplete: function() {
                    $timeout(function() {
                        $compile($("#fancybox-content"))($scope)
                        $scope.$apply()
                        $.fancybox.resize()
                    })
                }
            })
        }
    }
}])
    .directive('select2', function() {
        return {
            link: function(scope, element, attrs) {
                element.select2({
                    maximumSelectionLength: 10
                })
                // scope.$watch(attrs.ngModel, function(newValue, oldValue) {
                //     if (newValue != oldValue) {
                //         $timeout(function() {
                //             scope.$apply();
                //         });
                //     }
                // });

                scope.$on('userChanged', function() {
                    // 当用户信息改变时重新生成，这是由于我们的权限锁加在select的option里面，但是select2这个插件只在首次下拉时，根据option的状态初始化一次。所以后面option的状态不论怎么变，都体现不到select2里面来。所以要整个插件重新生成下。
                    element.select2({
                        maximumSelectionLength: 10
                    })
                })
            }
        }
    })
    .directive('bsSelect', ['$timeout', function($timeout) {
        return {
            restrict: 'C',
            link: function(scope, element, attrs) {
                element.selectpicker({
                    iconBase: 'fa',
                    tickIcon: 'fa-check'
                })
                $timeout(function() {
                    element.selectpicker('refresh')
                })

                scope.$on('userChanged', function() {
                    $timeout(function() {
                        element.selectpicker('refresh')
                    }, 800)
                })
            }
        }
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
                var slider
                element.ionRangeSlider({
                    type: "double",
                    from: scope.ngForm,
                    to: scope.ngTo,
                    max: scope.ngMax,
                    min: scope.ngMin,
                    onChange: function(data) {
                        // 必须在$apply触发$digist，才能将该scope的修改反映到与之绑定的外部变量
                        // 由于scope的修改，会导致scope.$watch监测到变化，所以在scope.$watch必须不应该重复赋值
                        scope.$apply(function() {
                            if (data.from != scope.ngForm)
                                scope.ngFrom = data.from
                            if (data.to != scope.ngTo)
                                scope.ngTo = data.to
                        })
                    }
                })
                slider = element.data('ionRangeSlider')
                scope.$watch('ngFrom', function(newValue, oldValue) {
                    // 如果触发是由内部改变引起的就应该忽略，否则可能出现另外一个问题
                    // 就是在执行这一步时，用户再次拖动界面上的滑块，两者同时发生时将出现滑块不按用户拖动的方向走
                    if (scope.ngFrom === newValue)
                        return
                    // console.log("slider", scope.ngFrom, newValue);
                    slider.update({
                        from: newValue
                    })
                })
                scope.$watch('ngTo', function(newValue, oldValue) {
                    if (scope.ngTo === newValue)
                        return
                    slider.update({
                        to: newValue
                    })
                })
            }
        }
    })
    // table增加排序功能
    .directive('sorttable', ["$timeout", "$compile", function($timeout, $compile) {
        return {
            scope: {
                sort: '='
            },
            link: function(scope, element, attrs) {
                // scope.$table = {$data:scope.$eval(attrs.source)};
                function init() {
                    element.find(".sort").remove()
                    element.find('th[data-field]').each(function() {
                        if ($(this).data('field') != scope.sort.field) {
                            $(this).append('<i class="fa fa-sort fa-fw sort"></i>')
                        } else {
                            if (!scope.sort.reverse)
                                $(this).append('<i class="fa fa-sort-asc fa-fw sort"></i>')
                            else
                                $(this).append('<i class="fa fa-sort-desc fa-fw sort"></i>')
                        }
                    })
                }
                init()
                element.find('th[data-field]').bind('click', function() {
                    var flipSort = ["asc", "desc"]
                    var sort = $(this).data('sort')
                    $(this).data('sort', flipSort[1 - flipSort.indexOf(sort)])
                    scope.sort.field = $(this).data('field')
                    scope.sort.reverse = (sort == flipSort[1])
                    init()
                    $timeout(function() {
                        scope.$apply()
                    })
                    console.log("sort:", scope.sort)
                })
            }
        }
    }])
    // 当没有某个操作权限时就会加锁
    .directive('policyLock', ['User', function(User) {
        return {
            link: function(scope, element, attrs) {
                function check() {
                    var key = attrs.key
                    if (!key)
                        return
                    if (!User.can(key) || !User.usable(key, attrs.val)) {
                        if (element.find('.lock').length)
                            return
                        if ((attrs.trigger == "lockButton") && (attrs.buttontype == "filter")) {
                            element.on('click.openUpgrade', function() {
                                User.openUpgrade()
                                return false
                            })
                        }
                        if ((attrs.trigger == "lockButton") && (attrs.buttontype != "filter")) {
                            element.attr("disabled", "disabled")
                            element.append('<i class="fa fa-lock  lock"></i>')
                        } else if (attrs.trigger == "disabled")
                            element.attr("disabled", "disabled")
                        else
                            element.append('<i class="fa fa-lock  lock"></i>')
                    } else {
                        if (attrs.trigger == "lockButton") {
                            element.removeAttr("disabled")
                            element.children('.lock').remove()
                            element.off('click.openUpgrade')
                        } else if (attrs.trigger == "disabled")
                            element.removeAttr("disabled")
                        else
                            element.children('.lock').remove()
                    }
                }
                check()
                scope.$on('userChanged', function() {
                    check()
                })
            }
        }
    }])
    // 资源(权限与策略以及使用情况)的显示格式化
    .directive('policyFormatter', ['POLICY_TYPE', 'ADS_TYPE', function(POLICY_TYPE, ADS_TYPE) {
        return {
            link: function(scope, element, attrs) {
                var defmatch = {
                    duration: "%val Days",
                    platform: function(val) {
                        var text = []
                        var num = Number(val.value)
                        if (num & ADS_TYPE.timeline)
                            text.push("Timeline")
                        if (num & ADS_TYPE.rightcolumn)
                            text.push("Rightcolumn")
                        if (num & ADS_TYPE.mobile)
                            text.push("Mobile")
                        return text.join(' & ')
                    },
                    ad_date: "%val Days",
                    result_per_search: "%val records",
                    ranking: "Top %val",
                    ranking_export: "Top %val"
                }
                var key = attrs.key
                var usageMode = attrs.mode == "usage"
                var val = scope.$eval(attrs.value)
                var text
                // console.log(val);
                if ((typeof val) == 'boolean') {
                    if (val)
                        element.append('<i class="icon-check font-green-jungle"></i>')
                    else
                        element.append('<i class="icon-close"></i>')
                } else {
                    text = val.value
                    if (val.type == POLICY_TYPE.MONTH) {
                        text += "/Month"
                        if (usageMode) {
                            text = (val.used + "(" + text + ")")
                        }
                    }
                    if (val.type == POLICY_TYPE.DAY) {
                        text += "/Day"
                        if (usageMode) {
                            text = (val.used + "(" + text + ")")
                        }
                    }
                    if (val.type == POLICY_TYPE.PERMANENT && usageMode) {
                        text = (val.used + "/" + val.value)
                    }
                    if (defmatch[key]) {
                        if (typeof (defmatch[key]) == 'function') {
                            text = defmatch[key](val)
                        } else {
                            text = defmatch[key].replace("%val", text)
                        }
                    }
                    element.append(text)
                }
            }
        }
    }])
    .directive('adlink', ['Util', function(Util) {
        return {
            link: function(scope, element, attrs) {
                element.on('click', function() {
                    Util.openAd(attrs.eventid)
                })
            }
        }
    }])
    .directive('rankBoard', ['$compile', '$timeout', 'TIMESTAMP', function($compile, $timeout, TIMESTAMP) {
        return {
            scope: {
                title: '@'
            },
            templateUrl: 'tpl/dashboard.html?t=' + TIMESTAMP,
            transclude: true,
            link: function(scope, element, attrs) {
                scope.title = attrs.title
            }
        }
    }])
    .directive('advideo', ['$compile', '$timeout', 'Util', function($compile, $timeout, Util) {
        return {
            restrict: 'EA',
            link: function(scope, element, attrs) {
                var poster = $('<div class="advideo"></div>')
                var img = $('<img/>')
                var imageSrc
                imageSrc = Util.getImageRandomSrc(attrs.preview)
                img.attr('src', imageSrc)
                poster.addClass('video')
                poster.html('<a class="playbtn"><i class="fa xg-icon-play"></i></a>')
                poster.append(img)
                element.before(poster)
                element.hide()

                poster.find('.playbtn').click(function() {
                    element.trigger('play')
                    poster.hide()
                    element.show()
                })
                // $timeout(function() {
                //     $compile(element)(scope);
                //     scope.$apply();
                // });
            }
        }
    }])
    .directive('fixsidebar', ['$timeout', '$rootScope', function($timeout, $rootScope) {
        return {
            scope: true,
            link: function(scope, element, attrs) {
                var oldtop = 0

                $timeout(function() {
                    oldtop = element[0].getBoundingClientRect().top
                    if (oldtop < 80)
                        oldtop = 80
                }, 100)
                // 更新浮动
                function updatefix() {
                    var top = element.parent()[0].getBoundingClientRect().top
                    if (top < 0)
                        $(element).animate({top: oldtop - top})
                    else if (top > 0)
                        $(element).animate({top: oldtop})
                    // if(!$rootScope.$$phase){
                    //     scope.$digest();
                    // }
                }
                (function loop() {
                    setTimeout(loop, 1000)
                    updatefix()
                })()
            }
        }
    }])
    .directive('adserlink', ['$state', 'User', function($state, User) {
        return {
            link: function(scope, element, attrs) {
                element.bind('click', function() {
                    if (!User.login) {
                        User.openSign()
                    } else {
                        var url = ""
                        var name = attrs.name.replace(/<[^>]+>/g, "")
                        var username = attrs.username.replace(/<[^>]+>/g, "")
                        url = $state.href('adser', {name: name, adser: username})
                        window.open(url, '_blank')
                    }
                })
            }
        }
    }])
    .directive('originlink', ['$state', function($state) {
        return {
            link: function(scope, element, attrs) {
                if (attrs.type == 4) {
                    element.attr("disabled", "disabled")
                    element.css("cursor", "not-allowed")
                } else {
                    element.bind('click', function() {
                        var url = "https://facebook.com/" + attrs.id + "_" + attrs.eventid
                        window.open(url)
                    })
                }
            }
        }
    }])
    .directive('avatar', ['Util', function(Util) {
        return {
            link: function(scope, element, attrs) {
                var imageSrc
                imageSrc = Util.getImageRandomSrc(attrs.image)
                element.attr("src", imageSrc)
            }
        }
    }])
    /*
    * 再广告详情页中滑动的指令
    */
    .directive('overviewScroll', ['$window', function($window) {
        return {
            link: function(scope, element, attrs) {
                var elm = element
                angular.element($window).bind("scroll", function() {
                    var elementHight = elm.height()
                    var marginTop = elm[0].getBoundingClientRect().top
                    if (elementHight + marginTop > 514) {
                        // 因为有些修改不能赋值到view，需要应该$apply()方法
                        if (scope.infoPosition) scope.$apply(function() { scope.infoPosition = false })
                    } else {
                        if (!scope.infoPosition) scope.$apply(function() { scope.infoPosition = true })
                    }
                })
            }
        }
    }])
    .directive('payCheck', ['User', 'SweetAlert', function(User, SweetAlert) {
        return {
            link: function(scope, element, attrs) {
                if (!attrs.name) {
                    return
                }
                // 现在不支持用户再次购买同类的plan计划，即已经是standard月付不能再购买standard 季付和年付
                // var userPlanType = attrs.name.split("_")
                // var userPlan = userPlanType[0]
                var sub = User.info.effective_sub
                element.bind("click", function() {
                    // 判断计划是错误做法，应该是判断用户是否有订阅，并且计划没被取消
                    if (sub) {
                        SweetAlert.swal({
                            title: "You have a subscription already. Contact help@bigbigads.com",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: false,
                            closeOnCancel: true },
                        function(isConfirm) {
                            if (isConfirm) {
                                window.open("mailto:help@bigbigads.com", '_self')
                            }
                        })
                        return false
                    } else {
                        window.open('/pay?name=' + attrs.name, '_self')
                    }
                })
            }
        }
    }])
    /* .directive('iframe', function() {
        return {
            link: function(scope, element, attrs) {
                console.log(attrs.value);
            }
        }
    }) */
    .directive('analysisOpener', ['User', function(User) {
        return {
            link: function(scope, element, attrs) {
                element.bind("click", function() {
                    if (!User.done)
                        return
                    if (User.login) {
                        window.open('./adAnalysis/' + attrs.userid)
                    } else {
                        User.openSign()
                    }
                })
            }
        }
    }])

    /*
    * 复制到剪贴板
    * copy-dom 上有三个属性，分别是value, button-text, success-text
    * value: 要复制的文本
    * button-text：按钮上的文字
    * success-text: 复制成功后的文字
    */
    .directive('copyDom', function() {
        return {
            restrict: 'EA',
            link: function(scope, element, attrs) {
                let poster = $('<div class="copy-dom-wrapper"></div>')
                let copyText = attrs.value // 要复制的文字
                let buttonText = attrs.buttonText // 按钮上的文字
                let markedWords = '' // 复制后提示语言
                let markedClass = '' // 复制后样式
                let html = `<input type="text" readOnly = "true" value="${copyText}"/>
                    <button class="btn btn-primary">
                        <i class="fa fa-clipboard"></i>
                        ${buttonText}
                    </button>
                    <span></span>`
                poster.html(html)
                element.before(poster)
                element.hide()
                poster.find('button').click(function() {
                    let inputDom = poster.find("input")
                    inputDom.select() // 选择对象
                    // 可能存在复制失败的情况
                    try {
                        document.execCommand("Copy") // 执行浏览器复制命令
                        markedWords = attrs.successText // 复制成功后的提示
                        markedClass = 'text-success'
                    } catch (err) {
                        markedWords = 'Replication failed and attempted manual replication' // 复制失败，尝试手动复制
                        markedClass = 'text-danger'
                    }
                    poster.find("span").html("(" + markedWords + ")").removeClass("hiden-text").addClass("show-text").addClass(markedClass)
                    // 暂时屏蔽点击按钮
                    $(this).attr("disabled", "disabled")
                    // 三秒后隐藏提示语，并恢复按钮可以点击
                    setTimeout(function() {
                        poster.find("span").addClass("hiden-text").removeClass("show-text")
                        poster.find("button").removeAttr("disabled")
                    }, 3000)
                })
                // 点击输入框的时候，会让其选中
                poster.find("input").click(function() {
                    $(this).select()
                })
            }
        }
    })
    // 去重复：定义一个过滤器，用于去除重复的数组，确保显示的每一条都唯一
    .filter('unique', function() {
        return function(collection) {
            var output = []
            angular.forEach(collection, function(item) {
                var itemname = item
                if (output.indexOf(itemname) < 0) { // indexOf表示首次出现的位置，===-1表示不存在
                    output.push(itemname) // 输入到数组
                }
            })
            return output
        }
    })
    .factory('Util', ['$uibModal', '$stateParams', 'SweetAlert', 'User', '$state', 'settings', 'TIMESTAMP', '$http', function($uibModal, $stateParams, SweetAlert, User, $state, settings, TIMESTAMP, $http) {
        return {
            matchkey: function(origstr, destArr) {
                var orig = origstr.split(',')
                var i
                angular.forEach(orig, function(item1) {
                    for (i = 0; i < destArr.length; i++) {
                        if (item1 == destArr[i].key) {
                            destArr[i].selected = true
                        }
                    }
                })
            },
            initPie: function(jsonSrc, title, labels) {
                /**
                 * jsonSrc:json字符串
                 * title:标题
                 * labels:是否将json的属性映射到labels对应值
                 */
                var src = jsonSrc
                var data = []
                for (var key in src) {
                    if (labels)
                        data.push([labels[key], src[key]])
                    else
                        data.push([key, src[key]])
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
                }
            },
            openAd: function(id) {
                return $uibModal.open({
                    templateUrl: 'views/ad-analysis.html?t=' + TIMESTAMP,
                    size: 'lg',
                    animation: true,
                    resolve: {
                        $stateParams: function() {
                            $stateParams.id = id
                            return $stateParams
                        }
                    }
                })
            },
            getTrendData: function(json) {
                if (!json || !json.trend) {
                    return null
                }
                var length = json.trend.length
                var endDate = moment(json.day, 'YYYY-MM-DD')
                var xs, ys, i
                if (json.trend[0] < 0) {
                    xs = []
                    ys = []
                } else {
                    xs = [endDate.format('YYYY-MM-DD')]
                    ys = [json.trend[1]]
                }
                for (i = 1; i < length; ++i) {
                    if (json.trend[i] >= 0) {
                        xs.push(endDate.subtract(1, 'days').format('YYYY-MM-DD'))
                        ys.push(json.trend[i])
                    }
                }
                xs = xs.reverse()
                ys = ys.reverse()
                // console.log("trend data", xs, ys);
                return [xs, ys]
            },
            /**
             * 单个广告的趋势图
             * @param json 包含day与trend数据的对象
             * @param title 标题
             * @param name 曲线名称
             *
             * @return 返回highcharts图表的配置
             */
            initTrend: function(json, title, name) {
                var data = this.getTrendData(json)
                if (data === null)
                    return
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
                            width: 1
                        }]
                    },
                    series: [{
                        name: name,
                        data: data[1]
                    }],
                    className: "response-width",
                    credits: false
                }
            },
            hint: function(res) {
                if (res.data instanceof Object) {
                    SweetAlert.swal(res.data.desc)
                } else {
                    SweetAlert.swal(res.statusText)
                }
            },
            isNumberLimit: function(value) {
                if (User.info.user.role.name != 'Free') return true
                if (!value) return true
                var valueArray = value.split(" ")
                if (valueArray.length > 1) {
                    return false
                } else {
                    return true
                }
            },
            isLengthLimit: function(value) {
                if (!value) return true
                if (value.length > 300) { // 20长度仅用于测试
                    return false
                } else {
                    return true
                }
            },
            // 判断是否为数组
            isArray: function(value) {
                return angular.isArray(value)
            },
            isFilterLimit: function(filter, searchOption) {
                // 检查是否使用了某个过滤项和是否具有权限, 同时将无权限的过滤选项记录
                // if ((User.info.user.role.name != 'Free')) {
                //     return true
                // }
                var isLimit = false
                var illeageWhere = []
                var filterLimitState
                // 由于变量命明没统一,造成代码无法优雅处理, 采用对象+数组的方式做权限判断
                var filterObject = {
                    adType: {
                        property: 'formatSelected',
                        permission: 'format_filter',
                        field: 'media_type',
                        mode: 'Must',
                        flag: ''
                    },
                    callToAction: {
                        property: 'callToAction',
                        permission: 'call_action_filter',
                        field: 'buttondesc',
                        mode: 'Must',
                        flag: ''
                    },
                    language: {
                        property: 'lang',
                        permission: 'lang_filter',
                        field: 'ad_lang',
                        mode: 'Must',
                        flag: ''
                    },
                    ecommerce: {
                        property: 'ecommerce',
                        permission: 'e_commerce_filter',
                        field: 'ecommerce',
                        mode: 'Must',
                        flag: ''
                    },
                    objective: {
                        property: 'objective',
                        permission: 'objective_filter',
                        field: 'objective',
                        mode: 'Must',
                        flag: ''
                    },
                    state: {
                        property: 'state',
                        permission: 'country_filter',
                        field: 'state',
                        mode: 'Must',
                        flag: ''
                    },
                    audienceAge: {
                        property: 'audienceAge',
                        permission: 'audience_age_filter',
                        field: 'audience_age',
                        mode: 'Must',
                        flag: ''
                    },
                    audienceInterest: {
                        property: 'audienceInterest',
                        permission: 'audience_interest_filter',
                        field: 'audience_interest',
                        mode: 'Must',
                        flag: ''
                    },
                    audienceGender: {
                        property: 'audienceGender',
                        permission: 'audience_gender_filter',
                        field: 'audience_gender',
                        mode: 'simple',
                        flag: ''
                    }
                }
                var filterValue = ['adType', 'callToAction', 'language', 'ecommerce', 'objective', 'state', 'audienceAge', 'audienceInterest', 'audienceGender']
                var valueProperty // 对应filterObject 过滤项的property
                var valuePermission // 对应filterObject 过滤项的permission
                var valueField // 对应filterObject 过滤项的field
                var valueFlag // 对应filterObject 过滤项的flag
                var valueMode
                var currIlleageOption = {}
                angular.forEach(filterValue, function(value) {
                    valueProperty = filterObject[value].property
                    valuePermission = filterObject[value].permission
                    valueField = filterObject[value].field
                    valueMode = filterObject[value].mode
                    // valueFlag = filterObject[value].flag
                    // filterValue filterObject的过滤项权限数组，每一个对应filterObject的属性对象
                    // filterObject[value] 具体的过滤权限对象，例如 filterObject.state 的属性名、权限名、请求参数、具体参数名和是否合法的标示
                    // filter 即 $scope.filterOption , filter[value.property] 为用户选择的具体过滤选项
                    if ((filter.hasOwnProperty(valueProperty)) && (filter[valueProperty].length === 0)) {
                        filterObject[value].flag = false
                    } else if (filter.hasOwnProperty(valueProperty) && !User.can(valuePermission)) {
                        filterObject[value].flag = true
                        if (valueMode === 'simple') {
                            // 单选过滤框
                            illeageWhere.push({"field": valueField, "value": filter[valueProperty]})
                            currIlleageOption[valueProperty] = filter[valueProperty]
                        } else {
                            // 多选过滤框
                            illeageWhere.push({"field": valueField, "value": filter[valueProperty].join(',')})
                            currIlleageOption[valueProperty] = filter[valueProperty].join(',')
                        }
                    }
                })
                angular.forEach(filterValue, function(value) {
                    valueFlag = filterObject[value].flag
                    if (valueFlag) isLimit = true
                })
                // if ((filter.hasOwnProperty('formatSelected')) && (filter.formatSelected.length === 0)) {
                //     isFormatLimit = false
                // } else if (filter.hasOwnProperty('formatSelected') && !User.can('format_filter')) {
                //     isFormatLimit = true
                //     illeageWhere.push({"field": "media_type", "value": filter.formatSelected[0]})
                // }
                if (isLimit) {
                    filterLimitState = {
                        flag: false,
                        params: illeageWhere,
                        currIlleageOption: currIlleageOption
                    }
                } else {
                    filterLimitState = {
                        flag: true,
                        params: ''
                    }
                }
                return filterLimitState
            },
            isAdvanceFilterLimit: function(filter) {
                // 需求变更，free用户无高级过滤权限
                // if (User.info.user.role.name != 'Free') {
                //     return true
                // }
                var isLimit = false
                var isDurationLimit = false
                var isSeeTimeLimit = false
                var isEngagementsLimit = false
                var illeageFilerRecord = []
                var engagementsArray = ['likes', 'engagements', 'shares', 'views', 'comments']
                var AdvanceFilterLimitState
                var currIlleageOption = {}
                if (((filter.duration.from !== 0) || (filter.duration.to !== 180)) && !User.can('duration_filter')) {
                    isDurationLimit = true
                    illeageFilerRecord.push({"field": "duration", "from": filter.duration.from, "to": filter.duration.to})
                    currIlleageOption.duration = {
                        from: filter.duration.from,
                        to: filter.duration.to
                    }
                }
                if (((filter.seeTimes.from !== 0) || (filter.seeTimes.to !== 180)) && !User.can('see_times_filter')) {
                    isSeeTimeLimit = true
                    illeageFilerRecord.push({"field": "see_times", "from": filter.seeTimes.from, "to": filter.seeTimes.to})
                    currIlleageOption.seeTimes = {
                        from: filter.seeTimes.from,
                        to: filter.seeTimes.to
                    }
                }
                angular.forEach(engagementsArray, function(data) {
                    if (filter.engagements.hasOwnProperty(data) && (filter.engagements[data].max || filter.engagements[data].min) && !User.can('see_times_filter')) {
                        isEngagementsLimit = true
                        illeageFilerRecord.push({'field': data, 'max': filter.engagements[data].max, 'min': filter.engagements[data].min})
                        currIlleageOption[data] = {
                            max: filter.engagements[data].max,
                            min: filter.engagements[data].min
                        }
                    }
                })
                if (isDurationLimit || isEngagementsLimit || isSeeTimeLimit) isLimit = true
                if (isLimit) {
                    AdvanceFilterLimitState = {
                        flag: false,
                        params: illeageFilerRecord,
                        currIlleageOption: currIlleageOption
                    }
                } else {
                    AdvanceFilterLimitState = {
                        flag: true,
                        params: ''
                    }
                }
                return AdvanceFilterLimitState
            },
            isAdPosionFilterLimit: function(adTypeSetting, params) {
                var isLimit = false
                var AdPosionFilterLimitState
                var illeageFilerRecord = []
                var currIlleageOption = {}
                angular.forEach(adTypeSetting, function(adType) {
                    if (adType.key === params && !User.can(adType.permission)) {
                        isLimit = true
                        illeageFilerRecord.push({'field': 'ads_type', 'value': params})
                        currIlleageOption.ads_type = params
                    }
                })
                if (isLimit) {
                    AdPosionFilterLimitState = {
                        flag: false,
                        params: illeageFilerRecord,
                        currIlleageOption: currIlleageOption
                    }
                } else {
                    AdPosionFilterLimitState = {
                        flag: true,
                        params: ''
                    }
                }
                return AdPosionFilterLimitState
            },
            isSortLimit: function(sort, orderBy) {
                var isLimit = false
                var illeageFilerRecord = []
                var sortLimitState
                var currIlleageOption = {}
                angular.forEach(orderBy, function(value) {
                    if (value.key === sort && !User.can(value.permission)) {
                        isLimit = true
                        illeageFilerRecord.push({'field': sort, order: 1})
                        console.log('value', value)
                        currIlleageOption.sort = value.value
                    }
                })
                if (isLimit) {
                    sortLimitState = {
                        flag: false,
                        sort: illeageFilerRecord,
                        currIlleageOption: currIlleageOption
                    }
                } else {
                    sortLimitState = {
                        flag: true,
                        sort: ''
                    }
                }
                return sortLimitState
            },
            isDateLimit: function(filter) {
                var dateObject = {
                    date: {
                        property: 'date',
                        permission: 'last_time_filter',
                        field: 'time'
                    },
                    firstSee: {
                        property: 'firstSee',
                        permission: 'first_time_filter',
                        field: 'first_see'
                    }
                }
                var dateFilter = ['date', 'firstSee']
                var isLimit = false
                var illeageFilerRecord = []
                var valueProperty
                var valuePermission
                var valueField
                var dateLimitState
                var currIlleageOption = {}
                angular.forEach(dateFilter, function(value) {
                    valueProperty = dateObject[value].property
                    valuePermission = dateObject[value].permission
                    valueField = dateObject[value].field
                    if (filter.hasOwnProperty(valueProperty) && !User.can(valuePermission) && filter[valueProperty].startDate) {
                        isLimit = true
                        illeageFilerRecord.push({"field": valueField, "min": filter[valueProperty].startDate.format('YYYY-MM-DD'), "max": filter[valueProperty].endDate.format('YYYY-MM-DD')})
                        currIlleageOption[valueProperty] = {
                            startDate: filter[valueProperty].startDate.format('YYYY-MM-DD'),
                            endDate: filter[valueProperty].endDate.format('YYYY-MM-DD')
                        }
                    }
                })
                if (isLimit) {
                    dateLimitState = {
                        flag: false,
                        params: illeageFilerRecord,
                        currIlleageOption: currIlleageOption
                    }
                } else {
                    dateLimitState = {
                        flag: true,
                        params: ''
                    }
                }
                return dateLimitState
            },
            isSearchModeLimit: function(searchOption, rangeList, string) {
                var isLimit
                var illeageFilerRecord = []
                var searchFields = ''
                var SearchModeLimitState = {}
                var currIlleageOption = {}
                // console.log('isSearchModeLimit')
                if ((searchOption.hasOwnProperty('rangeselected')) && (searchOption.rangeselected.length === 0)) {
                    isLimit = false
                } else if (searchOption.hasOwnProperty('rangeselected')) {
                    angular.forEach(searchOption.rangeselected, function(range) {
                        angular.forEach(rangeList, function(value) {
                            if (range == value.key && !User.can(value.permission)) {
                                searchFields += range
                                isLimit = true
                            }
                        })
                    })
                    if (searchFields) {
                        // string = string ? string : ''
                        illeageFilerRecord.push({string: string, search_fields: searchFields, relation: "Must"})
                        currIlleageOption.range = searchFields
                    }
                }
                if (isLimit) {
                    SearchModeLimitState = {
                        key: illeageFilerRecord,
                        currIlleageOption: currIlleageOption,
                        flag: false
                    }
                } else {
                    SearchModeLimitState = {
                        key: [],
                        flag: true
                    }
                }
                return SearchModeLimitState
            },
            unauthorisedFilterRequest: function(params) {
                // 向后端发送无权限的请求
                var recordParams
                var url = settings.remoteurl + '/filter-record'
                recordParams = {
                    params: params,
                    userId: User.info.user.id
                }
                $http.post(
                    url,
                    recordParams
                ).success(function(data) {
                    // defer.resolve(data);
                })
            },
            getImageRandomSrc: function(src) {
                // 图片采用随机向4个域名发起请求的方式，
                // 视频由于设置了不预加载，便不采取这样的方式
                var imageSrcIndex
                var imageSrc
                imageSrcIndex = parseInt(10 * Math.random()) % 4
                imageSrc = settings.imgRemoteBase[imageSrcIndex] + src
                return imageSrc
            },
            trackState: function(location) {
                // console.log(location.search());
                if (location.search().track) {
                    var url = settings.remoteurl + '/trackState'
                    $http({
                        method: "post",
                        params: {"track": location.search().track},
                        url: url
                    }).success(function(data) {
                        // defer.resolve(data);
                    })
                }
            },
            isMobile: function() {
                if (/iphone|nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|wap|mobile/i.test(navigator.userAgent.toLowerCase())) {
                    return true
                } else {
                    return false
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
                // 当为1个长度的数组或这非数组时，不做排序，原原本本返回
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
                            } else {
                                return n < 0 ? -1 : 1
                            }
                        })
                    }
                }
            },
            /*
            * 对象排序
            * 先将对象转为数组，再对数据转进行排序，最后再转为对象
            * 支持正序(0)和逆序(1)
            * itme为排序方式value，和 name
            */
            objectSort: function(object, item, n) {
                // 判断object不能为空，且长度至少为2
                if (object && Object.keys(object).length >= 2) {
                    // 转为格式为[{name:**,value:**},{..},..]
                    var arr = []
                    for (var key in object) {
                        arr.push({
                            'name': key,
                            'value': object[key]
                        })
                    }
                    arr = this.arrSort(arr, item, n)
                    // 将排序好的数组在转为对象
                    var newObject = {}
                    for (var i = 0; i < arr.length; i++) {
                        newObject[arr[i].name] = arr[i].value
                    }
                    return newObject
                } else return object
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
                n = n === 0 ? data.length : n
                if (data.length < 3) return false // 对于长度小于3的不作处理
                else {
                    var newTime
                    try {
                        // 获取的时间格式可能为2017-01-01，需将其转换
                        newTime = new Date(time.split("-")[0], time.split("-")[1] - 1, time.split("-")[2])
                    } catch (e) {
                        return false
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
                            arr.shift() // 加入新的，并删除第一个元素
                        }
                        newTime.setDate(newTime.getDate() + 1)
                    }
                    return arr
                }
            },
            googleSuggestQueries: function(value) {
                var query = encodeURI(value)
                var myUrl = "https://suggestqueries.google.com/complete/search?client=firefox&hl=en&q=" + query + "&callback=JSON_CALLBACK"
                // var result
                // 调用谷歌搜索接口,需要使用jsonp方式请求
                return $http.jsonp(myUrl).success(
                    function(data) {
                        return data
                    }
                ).error(function(date) {
                    console.log(date)
                })
            },
            /*
            * 定义的charts配置
            * 因为图表的配置差异比较大，所以对每个类型写个配置,不能使用通用
            * 支持pie、line、map、 bar这几种配置
            * 后续如有其他的配置可再加
            * typeData, 可选，不填为默认；
            * seriesName,可选，不填为默认；
            * seriesData,必填！因为数据格式不固定，再controller里处理好了，再传到这
            * type 图标类型，值为：“pie、bar、line、map”
            * xaxis 对于line类型的图表，需要提供xaxis
            */
            // line 折线图表，支持相似类型的或则衍生图表,比如 area,line等
            lineChartsConfig: function(typeData, xAxisData, seriesName, seriesData, zoomTypeData) {
                return {
                    chart: {
                        type: typeData || 'line',
                        zoomType: zoomTypeData || false,
                        spacingBottom: 0,
                        backgroundColor: null
                    },
                    title: false,
                    subtitle: false,
                    legend: false,
                    xAxis: {
                        categories: xAxisData || false
                    },
                    yAxis: {
                        title: false,
                        min: 0
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>' + this.series.name + '</b><br/>' +
                                this.x + ': ' + this.y
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: seriesName || '',
                        data: seriesData || ''
                    }]
                }
            },
            /*
            * 地图图标
            * mapData 国家数据 {['country': 'CN','value': 99],[...]}
            * mapValueCount 全部总数，用于计算百分比，默认为100,且会去掉百分号
            * mapName 说明标识文字
            * mapLegend 图例，被默认不显示
            */
            mapChartsConfig: function(mapData, mapValueCount, mapName, mapLegend) {
                // 地图图表
                return {
                    chart: {
                        borderWidth: 0, // 边框
                        type: 'map',
                        backgroundColor: null
                    },
                    colors: ['rgba(19,64,117,0.05)', 'rgba(19,64,117,0.2)', 'rgba(19,64,117,0.4)',
                        'rgba(19,64,117,0.5)', 'rgba(19,64,117,0.6)', 'rgba(19,64,117,0.8)', 'rgba(19,64,117,1)'
                    ],
                    title: {
                        text: false
                    },
                    credits: false,
                    mapNavigation: {
                        enabled: true, // 缩放
                        enableDoubleClickZoomTo: true,
                        enableMouseWheelZoom: false
                    },
                    xAxis: {
                        lineWidth: 0, // 轴线宽度
                        tickLength: 0, // 刻度线长度
                        labels: false
                    },
                    yAxis: {
                        gridLineWidth: 0,
                        // lineWidth:0
                        labels: false,
                        title: false
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>' + this.series.name + '</b><br>' +
                                'Country: <span style="color:#eb6130">' + this.point.name + '</span><br>' +
                                'Value: <span style="color:#eb6130">' + ((this.point.value / (mapValueCount || 100)) * 100).toFixed(1) + (mapValueCount ? '%' : '') + '</span><br>'
                        }
                    },
                    legend: !mapLegend ? false : {},
                    colorAxis: {
                        min: 0,
                        stops: [
                            [0, '#EFEFFF'],
                            [0.5, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).brighten(-0.5).get()]
                        ]
                    },
                    series: [{
                        data: mapData || [],
                        mapData: Highcharts.maps['custom/world'],
                        joinBy: ['iso-a2', 'country'],
                        animation: false, // 取消掉动画，不然地图会重绘，第二次加载很别扭
                        name: mapName || false,
                        states: {
                            hover: {
                                color: '#BADA55'
                            }
                        }
                    }]
                }
            },
            /*
            * 饼状图
            * pieData 格式：['name':value],[...],...
            * pieInnerSize 内径，默认60%
            * pieColor ['#fff', '#ccc']
            * pielegend 图例格式
            * pieToolTip 鼠标经过饼图显示的格式
            */
            pieChartsConfig: function(pieData, pieInnerSize, pieColor, pieLegend, pieToolTip) {
                return {
                    chart: {
                        plotBackgroundColor: null,
                        type: 'pie'
                    },
                    colors: pieColor || ['#7cb5ec', '#337ab7'],
                    title: false,
                    credits: false, // 角标
                    tooltip: pieToolTip || {
                        headerFormat: null,
                        pointFormat: '<b>{point.name}:</b>{point.percentage:.1f}%'
                    },
                    legend: pieLegend || {},
                    plotOptions: {
                        pie: {
                            allowPointSelect: false, // 点击可选
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false // 显示注释
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        innerSize: pieInnerSize || '60%',
                        dataLabels: false,
                        data: pieData
                    }]
                }
            },
            /*
            * bar 堆叠分布
            * barXAxis X轴数据 格式类似 ['18-24', '25-34', '35-44', '45-54', '55-64', '65+']
            * barData 数据 [{name:'name',data:[1,2,55,4...]}, {name:'name2', data:[...]},...]
            * barPercent 是否已百分号显示
            * barColor 显示颜色
            */
            barChartsConfig: function(barXAxis, barData, barPercent, barColor) {
                var barDataArr
                /*
                * 转换为百分数
                * 如过是要以百分数进行显示，则在此转化为百分值
                */
                var toPercent = function(arr) {
                    try {
                        var valueCount = 0
                        for (var arrItem in arr) {
                            for (var arrKey in arr[arrItem].data) {
                                valueCount += arr[arrItem].data[arrKey]
                            }
                        }
                        // 对所有数组进行修改，改为已百分比值
                        // 继续命名为arrItem 检测插件会报错
                        for (var arrItem1 in arr) {
                            for (var arrKey1 in arr[arrItem1].data) {
                                arr[arrItem1].data[arrKey1] = (arr[arrItem1].data[arrKey1] / valueCount) * 100
                            }
                        }
                        return arr
                    } catch (err) {
                        return arr
                    }
                }
                if (barPercent) barDataArr = toPercent(barData)
                else barDataArr = barData
                return {
                    chart: {
                        type: 'bar',
                        backgroundColor: null
                    },
                    title: {
                        text: false
                    },
                    colors: barColor || ['#7cb5ec', '#337ab7'],
                    credits: false,
                    xAxis: {
                        categories: barXAxis || ['18-24', '25-34', '35-44', '45-54', '55-64', '65+']
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: false
                        },
                        max: barPercent ? 100 : null
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
                        pointFormat: barPercent ? '<b>{point.name}</b><br>{series.name}:{point.y:.1f}%' : '<b>{point.name}</b><br>{series.name}:{point.y}'
                    },
                    series: barDataArr || [{ name: 'Male', data: [1, 2, 0, 0, 5] }, { name: 'Female', data: [2, 1, 0, 0, 5] }]
                }
            }
        }
    }])
angular.module('MetronicApp').service('Resource', ['$resource', 'settings', 'SweetAlert', function($resource, settings, SweetAlert) {
    function f(name) {
        var vm = this
        var url = settings.remoteurl + '/' + name + '/:id'
        var r = $resource(url, {
            id: '@id'
        }, {
            update: {
                method: 'PUT'
            }
        })
        vm.error = true
        vm.queried = false
        vm.items = []
        angular.extend(vm, {
            get: function(params) {
                var promise = r.query(params).$promise
                promise.then(function(items) {
                    vm.items = items
                    vm.error = false
                }, function(res) {
                    vm.error = true
                }).finally(function() {
                    vm.queried = true
                })
                return promise
            },
            del: function(item) {
                // console.log('ondeleting', item);
                var promise = item.$delete()
                promise.then(function(item) {
                    vm.items.splice($.inArray(item, vm.items), 1)
                }, vm.handleError)
                return promise
            },
            save: function(item) {
                var promise
                var update = false
                if (item.$save) {
                    promise = item.$update()
                    update = true
                } else
                    promise = r.save(item).$promise
                promise.then(function(newItem) {
                    if (update) {
                        for (var i = 0; i < vm.items.length; ++i) {
                            if (vm.items[i].id == item.id) {
                                vm.items.splice(i, 1, newItem)
                                break
                            }
                        }
                    } else
                        vm.items.push(newItem)
                }, vm.handleError)
                return promise
            },
            handleError: function(res) {
                if (res.data instanceof Object) {
                    SweetAlert.swal(res.data.desc)
                } else {
                    SweetAlert.swal(res.statusText)
                }
            }
        })
    }
    return f
}])
