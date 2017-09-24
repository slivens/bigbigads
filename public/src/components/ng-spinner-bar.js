// import './ng-spinner-bar.scss'
// import template from './ng-spinner-bar.html'

angular.module('bba.ng-spinner-bar', ['ui.router']).directive('ngSpinnerBar', ['$rootScope', '$state',
    function($rootScope, $state) {
        return {
            link: function(scope, element, attrs) {
                // by defult hide the spinner bar
                element.addClass('hide') // hide spinner bar by default

                // display the spinner bar whenever the route changes(the content part started loading)
                $rootScope.$on('$stateChangeStart', function() {
                    element.removeClass('hide') // show spinner bar
                })

                // hide the spinner bar on rounte change success(after the content loaded)
                $rootScope.$on('$stateChangeSuccess', function(event) {
                    element.addClass('hide') // hide spinner bar
                    $('body').removeClass('page-on-load') // remove page loading indicator

                    // auto scorll to page top
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'normal')
                        // App.scrollTop(); // scroll to the top on content load
                    }, $rootScope.settings.layout.pageAutoScrollOnLoad)
                })

                // handle errors
                $rootScope.$on('$stateNotFound', function() {
                    element.addClass('hide') // hide spinner bar
                })

                // handle errors
                $rootScope.$on('$stateChangeError', function() {
                    element.addClass('hide') // hide spinner bar
                })

                $rootScope.$on('loading', function() {
                    element.removeClass('hide') // show spinner bar
                    $('body').addClass('page-on-load') // remove page loading indicator
                })
                $rootScope.$on('completed', function() {
                    element.addClass('hide') // hide spinner bar
                    $('body').removeClass('page-on-load') // remove page loading indicator
                })
            }
        }
    }
])
