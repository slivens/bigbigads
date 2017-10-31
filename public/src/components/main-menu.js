import template from './main-menu.html'

// TODO: 进一步解耦成不依赖于User，目前依赖于$rootScope只是为了保证项目在改造过程中可以顺利运行，实际上是错误的用法
angular.module('bba.main-menu', ['ui.router']).controller('MainMenuController', ['$rootScope', '$scope', '$location', '$state', function($rootScope, $scope, $location, $state) {
    var tabmenu = {
        name: $location.path()
    }
    let user = $rootScope.User
    $scope.tabmenu = tabmenu
    $scope.tr = $rootScope.tr
    $scope.User = user
    $scope.checkAccount = function() {
        if ((user.info.user.role.name != 'Free') && (user.info.user.role.name != 'Standard')) return
        user.openUpgrade()
    }
    $scope.goBookMark = function() {
        if (!user.login) {
            user.openSign()
        } else {
            $state.go("bookmark")
        }
    }
    $scope.$on('$locationChangeSuccess', function() {
        tabmenu.name = $location.path()
    })
}])
    .directive('mainMenu', function() {
        return {
            restrict: 'EA',
            template,
            replace: true,
            scope: {},
            controller: 'MainMenuController'
        }
    })
