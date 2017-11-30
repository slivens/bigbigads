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
* 4）$scope.searchErr        错误结果集，其中报错未搜索到结果，搜索次数已满等
* 5）$scope.searchPage       搜索的当前页面，默认从第一页开始，没下拉加载一次，页数增加一次，同时也用于判断是否已经加载完全部结果
* 6）$scope.getMoreBusy      加载更多繁忙，用于显示loading动画，禁用下拉继续加载
*
* 历史修改：
* 2017.10.16  创建，初始版本，完成基本功能  负责人：余清红
* 2017.11.29  修改接口
* 2017.11.30  添加当出现错误时的提示
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
        console.log('test')
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
                $scope.searchErr = false
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
        * 获取广告主数据
        *  1）如果存在错误，比如搜索超过限制，下拉超过限制 即 $scope.searchErr = true 禁用获取功能
        *  2）当返回的数据data的长度为0的时候：未搜索到结果
        *  3）返回的数据累加到 $scope.ownerCardData
        *  【错误数据】
        *  1）-4100 日搜索被限制
        *  2）-4400 下拉加载次数被限制
        *  3）其他 比如服务器问题
        */
        vm.getOwner = function() {
            // 如果出错了，就禁用搜索
            if ($scope.searchErr) return
            let searchText = $scope.searchText || ''
            $http.get(`/advertisers?keywords=${searchText}&page=${$scope.searchPage}`).success(function(res) {
                $scope.getMoreBusy = false
                $scope.searchBusy = false
                if (res.data && res.data.length) {
                    if (res.pagination && res.pagination.pages > $scope.searchPage) {
                        // 如果有正常返回数据
                        $scope.searchErr = false
                    } else {
                        $scope.searchErr = {}
                        $scope.searchErr.end = true
                    }
                    $scope.ownerCardData = $scope.ownerCardData.concat(res.data)
                    $scope.total = res.pagination.pages
                    $scope.searchErrTime = 0
                } else if (res.data && res.data.length == 0) {
                    // 未搜索到结果
                    $scope.searchErr = {}
                    $scope.searchErr.noResult = true
                }
            }).error(function(err) {
                if (err.code == '-4100') {
                    $scope.searchErr = {}
                    $scope.searchErr.searchTime = true
                } else if (err.code == '-4400') {
                    // 下拉加载 限制
                    $scope.searchErr = {}
                    $scope.searchErr.loadMore = true
                } else {
                    /*
                    * 其他错误，连续错三次，可能时服务器错误或则网络问题
                    * 尝试继续加载，如果连续出现三次错误，就停止加载
                    */
                    $scope.searchErrTime += 1
                    if ($scope.searchErrTime >= 3) {
                        $scope.searchErr = {}
                        $scope.searchErr.errTime = true
                    } else {
                        vm.getOwner()
                    }
                }
                // 如果报错的话，页面减一，可以重新访问
                $scope.getMoreBusy = false
                $scope.searchBusy = false
                console.log(err)
            })
        }

        /*
        * 下来加载更多
        *  1）加载的页数 + 1
        *  2）禁用继续下拉 $scope.getMoreBusy = true
        * 【加载条件】
        *  1）不存在错误的情况，即$scope.searchErr = false
        */
        $scope.getMore = function() {
            if (!$scope.searchErr) {
                $scope.getMoreBusy = true
                $scope.searchPage += 1
                vm.getOwner()
            }
        }
        /*
        * 点击刷新
        */
        $scope.searchRefresh = function() {
            $scope.searchBusy = true
            $scope.canGetMore = false
            $scope.searchErrTime = 0 // 搜索的错误次数
            $scope.searchErr = false
            vm.getOwner()
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
        $scope.searchPage = 1
        $scope.searchErr = false
        $scope.searchErrTime = 0 // 搜索的错误次数
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
