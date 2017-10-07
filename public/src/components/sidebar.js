import './sidebar.scss'
import template from './sidebar.html'

// TODO: 移除对parent的依赖
angular.module('MetronicApp').controller('QuickSidebarController', ['$scope', '$window', 'settings', 'User', function($scope, $window, settings, User) {
    /* Setup Layout Part - Quick Sidebar */
    // 这个控制器与广告是强绑定的，这里直接指向$parent的这个方式是非常不友好的，加大了耦合
    // $scope.$on('$includeContentLoaded', function() {
    $scope.settings = settings
    $scope.filterOption = $scope.$parent.filterOption
    $scope.daterangeOption = {
        ranges: {
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'Last 90 Days': [moment().subtract(89, 'days'), moment()],
            'All Times': ['2016-01-01', moment()]
        }
    }
    $scope.categoryOpt = {
        items: $scope.$parent.filterOption.category,
        all: false,
        collapse: true,
        defnum: 5,
        toggle: function() {
            angular.forEach($scope.$parent.filterOption.category, function(value, key) {
                if ($scope.categoryOpt.all)
                    value.selected = true
                else
                    value.selected = false
            })
        }
    }
    $scope.buttondescOpt = {
        items: $scope.$parent.filterOption.buttondesc,
        all: false,
        collapse: true,
        defnum: 5,
        toggle: function() {
            var vm = this
            angular.forEach(this.items, function(value, key) {
                if (vm.all)
                    value.selected = true
                else
                    value.selected = false
            })
        }
    }

    $scope.reset = function() {
        $scope.$parent.initSearch()
        $scope.$parent.search()
        console.log($scope.$parent.filterOption)
    }
    $scope.User = User

    $scope.submit = function() {
        var legal = true
        if (!$scope.$parent.inAdvertiserMode) {
            legal = $scope.$parent.searchCheck(true)
        }
        if (legal) {
            angular.element($window).scrollTop(0)
            $scope.$parent.search('search')
        }
    }
    // })
}])
    .directive('sidebar', function() {
        return {
            restrict: 'E',
            template,
            replace: false,
            scope: {},
            controller: 'QuickSidebarController'
        }
    })
