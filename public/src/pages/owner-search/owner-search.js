/*
* 广告主搜索页
* 版本：2017_10_16(1.0.1)
* 编写：余清红
* 
* 需求：
* 1）单词搜索，搜索后出现loding(屏幕居中)
* 2）下拉加载更多，出现loading动画（下边）
* 3）搜索后，url有相关的searchText信息
* 4）带url参数的，根据参数搜索
* 5）显示所有结果后，禁止下拉加载
* 6）每次加载显示十个结果
* 
* 相关权限：
* 1）游客，进入login页面
* 2）免费用户，提示无相关权限，要求升级
* 3）基本付费用户，可见，可用！
*
* 出现的自定义参数与说明：
* 1）$scope.searchText       搜索的关键词、url参数
* 2）$scope.ownerCardData    搜索后的结果集，数组
* 3）$scope.searchBusy       搜索繁忙，用于显示loading动画，禁用下拉继续加载
* 4）$scope.isEnd            是否已经加载完，用于显示结束标志，禁用继续下载加载
* 5）$scope.searchPage       搜索的当前页面，默认从第一页开始，没下拉加载一次，页数增加一次，同时也用于判断是否已经加载完全部结果
* 6）$scope.getMoreBusy      加载更多繁忙，用于显示loading动画，禁用下拉继续加载
*
* 历史修改：
* 2017.10.16  创建，初始版本，完成基本功能  负责人：余清红
*/

import './owner-search.scss'
import '../common/common.js'
import '../common/searcher.js'
import '../../components/adser-search-card.js'
import 'angular-deckgrid/angular-deckgrid'
import 'ng-infinite-scroll'
import template from './owner-search.html'

export default angular.module('owner-search', ['MetronicApp', 'akoenig.deckgrid', 'infinite-scroll']).controller('AdserSearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User', '$http',
    function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User, $http) {
        var vm = this
        $scope.settings = settings

        // 获取当前url的参数的序列化json对象
        function toUrlText(searchText) {
            searchText ? $location.search({"searchText": searchText}) : $location.search({})
        }

        // 将query转化成搜索参数
        function toSeachText(textKey) {
            $scope.searchWord = $scope.searchText = $location.search() ? $location.search()[textKey] : ''
        }

        // 弹窗提示
        $scope.swal = function(msg) {
            SweetAlert.swal(msg)
        }

        /*
        * 搜索
        * 点击搜索后，触发loading动画，为flxed居中显示
        * 同样的词不触发搜索，避免多次访问
        * 空词搜索则显示默认的所有结果
        */
        $scope.search = function() {
            $scope.searchBusy = true // 出现loading动画
            // 不一致，则触发搜索，$scope.searchWord为输入框输入的关键词，$scope.searchText为url参数或已经搜索的关键词
            if ($scope.searchText != $scope.searchWord) {
                $scope.searchText = $scope.searchWord
                $scope.ownerCardData = [] // 清空结果
                $scope.searchPage = 1
                vm.getOwner()
                toUrlText($scope.searchText)
            } else {
                $scope.searchBusy = false
            }
        }

        // 清除搜索结果
        $scope.clearSearch = function() {
            $location.search({})
            $state.reload()
        }

        /*
        * 广告主搜索
        */
        vm.getOwner = function() {
            let searchText = $scope.searchText || ''
            $http.get(`/api/owners?keywords=${searchText}&page=${$scope.searchPage}`).success(function(res) {
                $scope.getMoreBusy = false
                $scope.searchBusy = false
                if (res.data) {
                    if (res.last && res.last > $scope.searchPage) {
                        $scope.isEnd = false
                    } else {
                        $scope.isEnd = true
                    }
                    for (var key in res.data) {
                        $scope.ownerCardData.push(res.data[key])
                    }
                    $scope.total = res.total
                } else {
                    $scope.ownerCardData = false
                }
            }).error(function(err) {
                // 如果报错的话，页面减一，可以重新访问
                $scope.searchPage -= 1
                $scope.getMoreBusy = false
                $scope.searchBusy = false
                console.log(err)
            })
<<<<<<< HEAD
=======

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
>>>>>>> origin/develop
        }

        /*
        * 下来加载更多
        * 加载的页数 + 1
        * 如果已经加载完全部，禁用继续加载
        */
        $scope.getMore = function() {
            // $scope.searchBusy = true
            $scope.getMoreBusy = true
            $scope.searchPage += 1
            if (!$scope.isEnd) vm.getOwner()
        }

        // 用户登录状态
        User.getInfo().then(function() {
            // 根据search参数页面初始化
            if (!User.login) {
                window.open('/login', "_self")
            } else {
                vm.getOwner()
            }
        })

        // 初始化相关数据
        $scope.ownerCardData = []
        $scope.searchBusy = true
        $scope.canGetMore = false
        $scope.isEnd = false
        $scope.searchPage = 1
        toSeachText("searchText")
    }
])
    .directive('ownerSearch', function() {
        return {
            restrict: 'E',
            replace: false,
            template,
            controller: 'AdserSearchController'
        }
    })
