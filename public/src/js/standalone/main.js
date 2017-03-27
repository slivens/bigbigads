/***
Metronic AngularJS App Main Script
***/

/* Metronic App */
var MetronicApp = angular.module("MetronicApp", [
    "ui.router",
    "ui.bootstrap",
    "oc.lazyLoad",
    "ngSanitize",
    "oitozero.ngSweetAlert",
    "angularLazyImg",
    'ngResource',
    'cgBusy'
]);

/* Configure ocLazyLoader(refer: https://github.com/ocombe/ocLazyLoad) */
MetronicApp.config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
    $ocLazyLoadProvider.config({
        // global configs go here
    });
}]);

/********************************************
 BEGIN: BREAKING CHANGE in AngularJS v1.3.x:
*********************************************/
/**
`$controller` will no longer look for controllers on `window`.
The old behavior of looking on `window` for controllers was originally intended
for use in examples, demos, and toy apps. We found that allowing global controller
functions encouraged poor practices, so we resolved to disable this behavior by
default.

To migrate, register your controllers with modules rather than exposing them
as globals:

Before:

```javascript
function MyController() {
  // ...
}
```

After:

```javascript
angular.module('myApp', []).controller('MyController', [function() {
  // ...
}]);

Although it's not recommended, you can re-enable the old behavior like this:

```javascript
angular.module('myModule').config(['$controllerProvider', function($controllerProvider) {
  // this option might be handy for migrating old apps, but please don't use it
  // in new ones!
  $controllerProvider.allowGlobals();
}]);
**/

//AngularJS v1.3.x workaround for old style controller declarition in HTML
MetronicApp.config(['$controllerProvider', function($controllerProvider) {
    // this option might be handy for migrating old apps, but please don't use it
    // in new ones!
    $controllerProvider.allowGlobals();
}]);

/********************************************
 END: BREAKING CHANGE in AngularJS v1.3.x:
*********************************************/
/* Setup global settings */
MetronicApp.constant('ADS_TYPE', {
    timeline: 1,
    rightcolumn: 4,
    mobile: 2,
    phone: 2
});
MetronicApp.constant('ADS_CONT_TYPE', {
    SINGLE_IMAGE: "SingleImage",
    CANVAS: "Canvas",
    CAROUSEL: "Carousel",
    SINGLE_VIDEO: "SingleVideo"
});
MetronicApp.constant('POLICY_TYPE', {PERMANENT:0, MONTH:1, DAY:2, HOUR:3, VALUE:4, DURATION:5, YEAR:6});
MetronicApp.factory('settings', ['$rootScope', function($rootScope) {
    // supported languages
    var settings = {
        layout: {
            pageSidebarClosed: false, // sidebar menu state
            pageContentWhite: true, // set page content layout
            pageBodySolid: false, // solid body color state
            pageAutoScrollOnLoad: 1000 // auto scroll to top on page load
        },
        assetsPath: '../assets',
        globalPath: '../assets/global',
        layoutPath: '../assets/layouts/layout3',
        baseurl: '/app/',
        remoteurl: '', //根目录
        imgRemoteBase: 'http://image1.bigbigads.com:88',
        searchSetting: {
            pageCount: 10, //每一页的数据量
            durationRange:[0, 180],
            seeTimesRange:[0, 180],
            orderBy: [{
                key: 'last_view_date',
                value: 'Last_Seen',
                last: false,
                group: 'time',
                permission:'date_sort',
            }, {
                key: 'duration_days',
                value: 'Duration',
                last: true,
                group: 'time',
                permission:'duration_sort'
            }, {
                key: 'engagements',
                value: 'Engagements',
                last: false,
                group: 'seen',
                permission:'engagements_sort'
            }, {
                key: 'views',
                value:'Video Views',
                last: false,
                group: 'seen',
                permission:'views_sort'
            }, {
                key: 'engagements_per_7d',
                value: 'Engagements Growth',
                last: false,
                group: 'seen',
                permission:'engagement_inc_sort'
            }, {
                key: 'views_per_7d',
                value: 'Video Views Growth',
                last: true,
                group: 'seen',
                permission:'views_inc_sort'
            }, {
                key:'likes',
                value:'Likes',
                last: false,
                group: 'interactive',
                permission:'likes_sort',
            }, {
                key:'shares',
                value:'Shares',
                last: false,
                group: 'interactive',
                permission:'shares_sort'
            }, {
                key:'comments',
                value:'Comments',
                last: false,
                group: 'interactive',
                permission:'comment_sort'
            }, {
                key:'likes_per_7d',
                value:'Likes Growth',
                last: false,
                group: 'interactive',
                permission:'likes_inc_sort'
            }, {
                key:'shares_per_7d',
                value:'Shares Growth',
                last: false,
                group: 'interactive',
                permission:'shares_inc_sort'
            }, {
                key:'comments_per_7d',
                value:'Comments Growth',
                last: true,
                group: 'interactive',
                permission:'comments_inc_sort'
            }],
            adsTypes: [{
                key: 'timeline',
                value: 'Newsfeed'
            }, {
                key: 'rightcolumn',
                value: 'Right Column'
            }, {
                key: 'phone',
                value: 'Mobile'
            }],
            categoryList: [{
                key: "Advertising Agency",
                value: "Advertising Agency"
            }, {
                key: "Agriculture Company",
                value: "Agriculture Company"
            }, {
                key: "App Page",
                value: "App Page"
            }, {
                key: "Arts & Entertainment",
                value: "Arts & Entertainment"
            }, {
                key: "Author/Writer",
                value: "Author/Writer"
            }, {
                key: "Baby Goods/Kids Goods",
                value: "Baby Goods/Kids Goods"
            }, {
                key: "Bags/Luggage",
                value: "Bags/Luggage"
            }, {
                key: "Bar",
                value: "Bar"
            }, {
                key: "Book",
                value: "Book"
            }, {
                key: "Brand",
                value: "Brand"
            }, {
                key: "Business Service",
                value: "Business Service"
            }, {
                key: "Business/Economy",
                value: "Business/Economy"
            }, {
                key: "Car",
                value: "Car"
            }, {
                key: "Cargo & Freight Company",
                value: "Cargo & Freight Company"
            }, {
                key: "Cause",
                value: "Cause"
            }, {
                key: "Cleaning Service",
                value: "Cleaning Service"
            }, {
                key: "Clothing",
                value: "Clothing"
            }, {
                key: "Comedian",
                value: "Comedian"
            }, {
                key: "Community",
                value: "Community"
            }, {
                key: "Company/Internet Company",
                value: "Company/Internet Company"
            }, {
                key: "Consulting Agency",
                value: "Consulting Agency"
            }, {
                key: "Contractor",
                value: "Contractor"
            }, {
                key: "Dentist",
                value: "Dentist"
            }, {
                key: "Doctor",
                value: "Doctor"
            }, {
                key: "Education",
                value: "Education"
            }, {
                key: "Electronics",
                value: "Electronics"
            }, {
                key: "Energy",
                value: "Energy"
            }, {
                key: "Entrepreneur",
                value: "Entrepreneur"
            }, {
                key: "Event",
                value: "Event"
            }, {
                key: "Event Planning Service",
                value: "Event Planning Service"
            }, {
                key: "Fictional Character",
                value: "Fictional Character"
            }, {
                key: "Financial Service",
                value: "Financial Service"
            }, {
                key: "Food",
                value: "Food"
            }, {
                key: "Games/Toys",
                value: "Games/Toys"
            }, {
                key: "Gift",
                value: "Gift"
            }, {
                key: "Hair Salon",
                value: "Hair Salon"
            }, {
                key: "Health/Beauty",
                value: "Health/Beauty"
            }, {
                key: "Heating, Ventilating & Air Conditioning Service",
                value: "Heating, Ventilating & Air Conditioning Service"
            }, {
                key: "Home Decor",
                value: "Home Decor"
            }, {
                key: "Industrial Company",
                value: "Industrial Company"
            }, {
                key: "Insurance Agent",
                value: "Insurance Agent"
            }, {
                key: "Jewelry/Watches",
                value: "Jewelry/Watches"
            }, {
                key: "Just For Fun",
                value: "Just For Fun"
            }, {
                key: "Kitchen/Cooking",
                value: "Kitchen/Cooking"
            }, {
                key: "Landscape Company",
                value: "Landscape Company"
            }, {
                key: "Law Firm",
                value: "Law Firm"
            }, {
                key: "Magazine",
                value: "Magazine"
            }, {
                key: "Media/News",
                value: "Media/News"
            }, {
                key: "Medical & Health",
                value: "Medical & Health"
            }, {
                key: "Mobile Phone Shop",
                value: "Mobile Phone Shop"
            }, {
                key: "Movie",
                value: "Movie"
            }, {
                key: "Musician/Band",
                value: "Musician/Band"
            }, {
                key: "Non-Governmental Organization (NGO)",
                value: "Non-Governmental Organization (NGO)"
            }, {
                key: "Non-Profit Organization",
                value: "Non-Profit Organization"
            }, {
                key: "Organization",
                value: "Organization"
            }, {
                key: "Others",
                value: "Others"
            }, {
                key: "Personal Blog",
                value: "Personal Blog"
            }, {
                key: "Pet Service",
                value: "Pet Service"
            }, {
                key: "Photographer",
                value: "Photographer"
            }, {
                key: "Politician",
                value: "Politician"
            }, {
                key: "Producer",
                value: "Producer"
            }, {
                key: "Product/Service",
                value: "Product/Service"
            }, {
                key: "Public Figure",
                value: "Public Figure"
            }, {
                key: "Publisher",
                value: "Publisher"
            }, {
                key: "Real Estate",
                value: "Real Estate"
            }, {
                key: "Record Label",
                value: "Record Label"
            }, {
                key: "Recreation & Fitness",
                value: "Recreation & Fitness"
            }, {
                key: "Religious Organization",
                value: "Religious Organization"
            }, {
                key: "Restaurant/Cafe/Hotel",
                value: "Restaurant/Cafe/Hotel"
            }, {
                key: "Shopping/Retail",
                value: "Shopping/Retail"
            }, {
                key: "Society/Culture Website",
                value: "Society/Culture Website"
            }, {
                key: "Software",
                value: "Software"
            }, {
                key: "Sports",
                value: "Sports"
            }, {
                key: "Tattoo & Piercing Shop",
                value: "Tattoo & Piercing Shop"
            }, {
                key: "Tools/Equipment",
                value: "Tools/Equipment"
            }, {
                key: "Travel",
                value: "Travel"
            }, {
                key: "TV Show/TV Network",
                value: "TV Show/TV Network"
            }, {
                key: "Website/Entertainment Website",
                value: "Website/Entertainment Website"
            }, {
                key: "Wine/Spirits",
                value: "Wine/Spirits"
            }],
            formatList: [{
                key: "SingleVideo",
                value: "Video",
            }, {
                key: "Canvas",
                value: "Canvas"
            }, {
                key: "SingleImage",
                value: "Image"
            }, {
                key: "Carousel",
                value: "Carousel"
            }],
            buttondescList: [{
                key: "Apply Now",
                value: "Apply Now"
            }, {
                key: "Book Now",
                value: "Book Now"
            }, {
                key: "Buy",
                value: "Buy"
            }, {
                key: "Buy Now",
                value: "Buy Now"
            }, {
                key: "Buy Tickets",
                value: "Buy Tickets"
            }, {
                key: "Call Now",
                value: "Call Now"
            }, {
                key: "Contact Us",
                value: "Contact Us"
            }, {
                key: "Donate",
                value: "Donate"
            }, {
                key: "Donate Now",
                value: "Donate Now"
            }, {
                key: "Download",
                value: "Download"
            }, {
                key: "Get Deal",
                value: "Get Deal"
            }, {
                key: "Get Directions",
                value: "Get Directions"
            }, {
                key: "Get Offer",
                value: "Get Offer"
            }, {
                key: "Get Quote",
                value: "Get Quote"
            }, {
                key: "Get Tickets",
                value: "Get Tickets"
            }, {
                key: "Get Your Code",
                value: "Get Your Code"
            }, {
                key: "Install App",
                value: "Install App"
            }, {
                key: "Install Now",
                value: "Install Now"
            }, {
                key: "Learn More",
                value: "Learn More"
            }, {
                key: "Like Page",
                value: "Like Page"
            }, {
                key: "Liked",
                value: "Liked"
            }, {
                key: "Listen Now",
                value: "Listen Now"
            }, {
                key: "Listen on Apple Music",
                value: "Listen on Apple Music"
            }, {
                key: "Listen on Deezer",
                value: "Listen on Deezer"
            }, {
                key: "Listen on Whooshkaa",
                value: "Listen on Whooshkaa"
            }, {
                key: "Open Link",
                value: "Open Link"
            }, {
                key: "Order Now",
                value: "Order Now"
            }, {
                key: "Play",
                value: "Play"
            }, {
                key: "Play Game",
                value: "Play Game"
            }, {
                key: "Play Now",
                value: "Play Now"
            }, {
                key: "Request Time",
                value: "Request Time"
            }, {
                key: "Save",
                value: "Save"
            }, {
                key: "Save Offer",
                value: "Save Offer"
            }, {
                key: "SaveSaved",
                value: "SaveSaved"
            }, {
                key: "See Details",
                value: "See Details"
            }, {
                key: "See Menu",
                value: "See Menu"
            }, {
                key: "Sell Now",
                value: "Sell Now"
            }, {
                key: "Send Message",
                value: "Send Message"
            }, {
                key: "Shop Now",
                value: "Shop Now"
            }, {
                key: "Sign Up",
                value: "Sign Up"
            }, {
                key: "Spotify Icon",
                value: "Spotify Icon"
            }, {
                key: "Spotify IconAdd to Spotify",
                value: "Spotify IconAdd to Spotify"
            }, {
                key: "Use App",
                value: "Use App"
            }, {
                key: "Use Now",
                value: "Use Now"
            }, {
                key: "View Event",
                value: "View Event"
            }, {
                key: "Visit Website",
                value: "Visit Website"
            }, {
                key: "Vote Now",
                value: "Vote Now"
            }, {
                key: "Watch More",
                value: "Watch More"
            }, {
                key: "Watch Video",
                value: "Watch Video"
            }],
            rangeList: [{
                key: "adser_name,adser_username",
                value: "Advertiser",
                permission:"advertiser_search"
            }, {
                key: "dest_site",
                value: "URL",
                permission:"dest_site_search"
            }, {
                key: "description",
                value: "Advertisement",
                permission:"content_search"
            }/*, {
                key:"whyseeads,whyseeads_all",
                value:"Audience",
                permission:"audience_search"
            }*/],
            langList: [{
                key: "English",
                value: "English"
            }, {
                key: "Chinese",
                value: "Chinese"
            }, {
                key: "Japanese",
                value: "Japanese"
            }, {
                key: "Korean",
                value: "Korean"
            }, {
                key: "French",
                value: "French"
            }, {
                key: "German",
                value: "German"
            }, {
                key: "Portuguese",
                value: "Portuguese"
            }, {
                key: "Spanish",
                value: "Spanish"
            }, {
                key: "Russian",
                value: "Russian"
            }, {
                key: "Arabic",
                value: "Arabic"
            }, {
                key: "Others",
                value: "Others"
            }]
        }
    };

    $rootScope.settings = settings;

    return settings;
}]);
MetronicApp.filter('toHtml', ['$sce', function($sce) {　　
        return function(text) {　　
            return $sce.trustAsHtml(text);　　
        };
    }])
    .filter('trusted', ['$sce', function($sce) {
        return function(url) {
            return $sce.trustAsResourceUrl(url);
        };
    }])
    .filter('clearHttps',function() {
        var link = "";
        return function(httpLink) {
            if(httpLink===null||httpLink===undefined||httpLink==='') return;
            if(httpLink.indexOf("http") >= 0){ 
                link = httpLink.replace(/http:\/\//,"");
            }
            if(httpLink.indexOf("https") >= 0){
                link = httpLink.replace(/https:\/\//,"");
            }
            return link;
        };
    })
    .filter('addUnit',function() {
        var unit_num = '';
        return function(num) {
            if(num===null||num===undefined||num==='') return;
            num = Number(num); 
            if(num<1000) {
                return num;
            }
            if(num>=1000 && num<1000000) {
                num = num/1000;
                unit_num = num.toFixed(1) + 'K';
                return unit_num;
            }
            if(num>=1000000) {
                num = num/1000000;
                unit_num = num.toFixed(1) + 'M';
                return unit_num;
            }
        };
    })
    .filter('getImageSize', function() {
        //@param type 0表示宽度,1表示高度
        return function(url, type) {
            var size;
            var pos = url.lastIndexOf('#');
            if (pos < 0)
                return 0;
            size = url.slice(pos + 1).split('*');
            if (size.length != 2)
                return 0;
            return size[type];    
        };
    })
    .filter('mediaType', function() {
        return function(type) {
            var showType='';
            switch(type){
                case 'timeline': {showType = 'Newsfeed';break;}
                case 'rightcolumn': {showType = 'Right Column';break;}
                case 'phone': {showType = 'Mobile';break;}
                default: break;
            }
            return showType;
        };
    })
    .filter('adsCount', function() {
        return function(adsNumber) {
            if(adsNumber===0) return adsNumber;
            if(!adsNumber) return ;
            var countString = '';
            var re = /(?=(?!\b)(\d{3})+$)/g;
            adsNumber = String(adsNumber);
            countString = adsNumber.replace(re, ',');
            return countString;
        };
    });
/* Setup App Main Controller */
MetronicApp.controller('AppController', ['$scope', '$rootScope', function($scope, $rootScope) {
    $scope.$on('$viewContentLoaded', function() {
        App.initComponents(); // init core components
        //Layout.init(); //  Init entire layout(header, footer, sidebar, etc) on page load if the partials included in server side instead of loading with ng-include directive 
    });
}]);

/***
Layout Partials.
By default the partials are loaded through AngularJS ng-include directive. In case they loaded in server side(e.g: PHP include function) then below partial 
initialization can be disabled and Layout.init() should be called on page load complete as explained above.
***/

/* Setup Layout Part - Header */
MetronicApp.controller('HeaderController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        Layout.initHeader(); // init header
    });
}]);
MetronicApp.controller('TabMenuController', ['$scope', '$location', 'User', function($scope, $location, User) {
    var tabmenu = {
        name: $location.path()
    };
    $scope.tabmenu = tabmenu;
    $scope.User = User;
    $scope.checkAccount = function() {
        if((User.info.user.role.name !='Free')&&(User.info.user.role.name !='Standard')) return;
        User.openUpgrade();
    };
    $scope.$on('$locationChangeSuccess', function() {
         tabmenu.name = $location.path();
    });
}]);
/* Setup Layout Part - Sidebar */
MetronicApp.controller('SidebarController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        Layout.initSidebar($state); // init sidebar
    });
}]);

/* Setup Layout Part - Sidebar */
MetronicApp.controller('PageHeadController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        Demo.init(); // init theme panel
    });
}]);

/* Setup Layout Part - Theme Panel */
MetronicApp.controller('ThemePanelController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        Demo.init(); // init theme panel
    });
}]);

/* Setup Layout Part - Footer */
MetronicApp.controller('FooterController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        Layout.initFooter(); // init footer
    });
}]);

/* Setup Rounting For All Pages */
MetronicApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$urlMatcherFactoryProvider', function($stateProvider, $urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider) {
    // Redirect any unmatched url
    $urlMatcherFactoryProvider.strictMode(false);
    $urlRouterProvider.when("/", "/adsearch");
    $urlRouterProvider.otherwise("/404.html");

    $stateProvider
    .state('adsearch', {
            url: '/adsearch',
            templateUrl: "views/adsearch.html",
            data: {
                pageTitle: 'Advertise Search'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinModern.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'http://code.highcharts.com/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'js/bigbigads.js?r=' + Math.random() 
                        ]
                    });
                }]
            }
        })
        .state('adser', {
            url: '/adsearch/{adser}/{name}',
            templateUrl:"views/adser.html",
            data: {
                pageTitle: 'Specific Advertise'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }

        })
        .state('adserSearch', {
            url: '/adserSearch',
            templateUrl: "views/adser-search.html",
            data: {
                pageTitle: 'Advertiser Search'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }
        })
        .state('adserAnalysis', {
            url: '/adserAnalysis/{username}',
            templateUrl:"views/adser-analysis.html",
            data: {
                pageTitle: 'Advertiser Analysis'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'http://code.highcharts.com/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            '/node_modules/jqcloud2/dist/jqcloud.min.css',
                            '/node_modules/jqcloud2/dist/jqcloud.min.js',
                            '../assets/global/plugins/angular-jqcloud.js',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }
        })
        .state('adAnalysis', {
            url:'/adAnalysis/{id}',
            templateUrl:"views/ad-analysis.html",
            data: {
                pageTitle: 'Advertise Analysis'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'http://code.highcharts.com/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }

        })
        .state('ranking', {
            url:'/ranking',
            templateUrl:"views/ranking.html",
            data: {
                pageTitle: 'Ranking'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }
        })
        .state('bookmark', {
            url:'/bookmark',
            templateUrl:"views/bookmark.html",
            data: {
                pageTitle: 'Bookmark'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/bower_components/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }

        })
        .state('plans', {
            url:'/plans',
            templateUrl:"views/plans.html",
            data: {
                pageTitle: 'Plans'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/css/select2-bootstrap.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/pages/scripts/components-select2.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                        '/node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                        '/node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                        '/node_modules/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js',

                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    });
                }]
            }

        })
    // User Profile
    .state("profile", {
        url: "/profile",
        templateUrl: "views/profile/main.html",
        data: {
            pageTitle: 'Profile'
        },
        controller: "ProfileController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                            '/node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                            '/node_modules/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js',
                            'js/bigbigads.js?r=' + Math.random()
                    ]
                });
            }]
        }
    })

    // User Profile Dashboard
    .state("profile.dashboard", {
        url: "/dashboard",
        templateUrl: "views/profile/dashboard.html",
        data: {
            pageTitle: 'User Profile'
        }
    })

    // User Profile Account
    .state("profile.account", {
        url: "/account",
        templateUrl: "views/profile/account.html",
        data: {
            pageTitle: 'User Account'
        }
    })

    // User Profile Help
    .state("profile.help", {
        url: "/help",
        templateUrl: "views/profile/help.html",
        data: {
            pageTitle: 'User Help'
        }
    })
    //权限升级提示模板
    .state("upgrade", {
            url: "views/upgrade",
            templateUrl: "upgrade.html"
        });
    $locationProvider.html5Mode(true);

}]);
MetronicApp.config(['lazyImgConfigProvider', function(lazyImgConfigProvider) {
    // var scrollable = document.querySelector('#scrollable');
    lazyImgConfigProvider.setOptions({
      offset: 200, // how early you want to load image (default = 100)
      errorClass: 'error', // in case of loading image failure what class should be added (default = null)
      successClass: 'success', // in case of loading image success what class should be added (default = null)
      onError: function(image){}, // function fired on loading error
      onSuccess: function(image){} //function fired on loading success
    });
  }]);

/* Init global settings and run the app */
MetronicApp.run(["$rootScope", "settings", "$state", function($rootScope, settings, $state) {
    $rootScope.$state = $state; // state to be accessed from view
    $rootScope.$settings = settings; // state to be accessed from view

}]);


MetronicApp.factory('User', ['$http', '$q', '$location', '$rootScope', 'settings', 'ADS_TYPE', '$uibModal', function($http, $q, $location, $rootScope, settings, ADS_TYPE ,$uibModal) {
    //获取信息完成后应该广播消息，然后其他需要在获取用户信息才能继续的操作就放到接收到广播后处理
    var infourl = settings.remoteurl  + "/userinfo";
    var user = {
        retreived:false,
        done:false,
        info:{},
        getInfo:function(refresh) {
            if (!refresh && user.retreived)
                return user.promise;
           user.promise = $http.get(infourl);
           user.promise.then(function(res) {
                user.info = res.data;
                angular.extend(user, user.info);
                console.log(user);
           }, function(res) {
                user.info = {};
           }).finally(function() {
                user.done = true;
                $rootScope.$broadcast('userChanged', user.info);
           });
           user.retreived = true;
           return user.promise;
        },
        // goLoginPage:function() {
        //     $location.url("/login");
        // },
        can:function(key) {
            var keyArr = key.split('|');
            var i;
            //无登陆时的策略与有登陆需要做策略区分(只在服务器端区分是更好的做法)
            if (!user.info.permissions)
                return false;
            for (i = 0; i < keyArr.length; ++i) 
                if (!user.info.permissions[keyArr[i]])
                    return false;
            return true;
        },
        usable:function(key, val) {
            //是否满足策略要求
            var policy = user.getPolicy(key);
            var type;
            if (typeof(policy) == 'boolean')
                return policy;
            //根据不同情况返回不同的权限值
            if (key == "platform") {
                if (!val)
                    return true;
                type = ADS_TYPE[val];
                // console.log("policy:", policy.value, type, val);
                if ((Number(policy.value) & type) > 0)
                    return true;
                return false;
            }
            if (policy.used < policy.value)
                return true;
            return false;
        },
        getPolicy:function(key) {
            var usage;

            if (!user.can(key)) //没有权限一定没有策略
                return false;
            if (!user.info.user.usage[key])//没有策略不需要策略，组合权限不支持策略，所以也返回true
                return true;
            usage = user.info.user.usage[key];
            if (usage.length > 2)
                return {type:usage[0], value:usage[1], used: usage[2]};
            return {type:usage[0], value:usage[1], used:0};
        },
        openUpgrade:function() {
            return $uibModal.open({
                templateUrl: 'views/upgrade.html',
                size: 'md',
                animation: true,
                controller: ['$scope', '$uibModalInstance', '$state', function($scope, $uibModalInstance, $state) {
                    $scope.goPlans = function() {
                        $state.go("plans");
                        $uibModalInstance.dismiss('success');
                    };
                    $scope.goLogin = function() {
                        window.open('/login',"_self");
                        $uibModalInstance.dismiss('success');
                    };
                }]
            });
        }
    };
    return user;
}]);

MetronicApp.controller('UserController', ['$scope', 'User', function($scope, User) {
    User.getInfo();
    $scope.User = User;
}]);
