/***
Metronic AngularJS App Main Script
***/

/* Metronic App */
var MetronicApp = angular.module("MetronicApp", [
    "ui.router",
    "ui.bootstrap",
    "oc.lazyLoad",
    "ngSanitize",
    "oitozero.ngSweetAlert"
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
    mobile: 2
});
MetronicApp.constant('ADS_CONT_TYPE', {
    SINGLE_IMAGE: "SingleImage",
    CANVAS: "Canvas",
    CAROUSEL: "Carousel",
    SINGLE_VIDEO: "SingleVideo"
});
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
            orderBy: [{
                key: 'first_view_date',
                value: 'first_view'
            }, {
                key: 'last_view_date',
                value: 'last_view'
            }, {
                key: 'description',
                value: 'content'
            }, {
                key: 'adser_name',
                value: 'advertiser'
            }, {
                key: 'engement',
                value: 'engagement'
            }, {
                key: 'see_date',
                value: 'view_count'
            }, {
                key: 'duration_days',
                value: 'duration'
            }],
            adsTypes: [{
                key: 'timeline',
                value: 'timeline'
            }, {
                key: 'rightcolumn',
                value: 'rightcolumn'
            }, {
                key: 'phone',
                value: 'phone'
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
                value: "SingleVideo",
            }, {
                key: "Canvas",
                value: "Canvas"
            }, {
                key: "SingleImage",
                value: "SingleImage"
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
                key: "adser_name",
                value: "Advertiser"
            }, {
                key: "dest_site",
                value: "Destsite"
            }, {
                key: "description",
                value: "Content"
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
}]);
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

/* Setup Layout Part - Sidebar */
MetronicApp.controller('SidebarController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        Layout.initSidebar($state); // init sidebar
    });
}]);

/* Setup Layout Part - Quick Sidebar */
//这个控制器与广告是强绑定的
MetronicApp.controller('QuickSidebarController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        $scope.filterOption = $scope.$parent.filterOption;
        $scope.daterangeOption = {
            ranges: {
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                'Last 180 Days': [moment().subtract(179, 'days'), moment()]
            }
        };
        $scope.categoryOpt = {
            all: false,
            collapse:true,
            defnum:5
        };
        $scope.buttondescOpt = {
            items:$scope.$parent.filterOption.buttondesc,
            all: false,
            collapse:true,
            defnum:5,
            toggle:function() {
                var vm = this;
                angular.forEach(this.items, function(value, key) {
                    if (vm.all)
                        value.selected = true;
                    else
                        value.selected = false;
                });
            }
        };
        $scope.toggleCateogry = function() {
            angular.forEach($scope.$parent.filterOption.category, function(value, key) {
                if ($scope.categoryOpt.all)
                    value.selected = true;
                else
                    value.selected = false;
            });
        };
        $scope.reset = function() {
            $scope.$parent.initSearch();
            $scope.$parent.search();
            console.log($scope.$parent.filterOption);
        };
        setTimeout(function() {
            QuickSidebar.init(); // init quick sidebar        
        }, 100);
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
MetronicApp.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    // Redirect any unmatched url
    $urlRouterProvider.otherwise("/dashboard.html");

    $stateProvider

    // Dashboard
        .state('dashboard', {
        url: "/dashboard.html",
        templateUrl: "views/dashboard.html",
        data: {
            pageTitle: 'Admin Dashboard Template'
        },
        controller: "DashboardController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before a LINK element with this ID. Dynamic CSS files must be loaded between core and theme css files
                    files: [
                        '../assets/global/plugins/morris/morris.css',
                        '../assets/global/plugins/morris/morris.min.js',
                        '../assets/global/plugins/morris/raphael-min.js',
                        '../assets/global/plugins/jquery.sparkline.min.js',

                        '../assets/pages/scripts/dashboard.min.js',
                        'js/controllers/DashboardController.js',
                    ]
                });
            }]
        }
    })

    // Blank Page
    .state('blank', {
            url: "/blank",
            templateUrl: "views/blank.html",
            data: {
                pageTitle: 'Blank Page Template'
            },
            controller: "BlankController",
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before a LINK element with this ID. Dynamic CSS files must be loaded between core and theme css files
                        files: [
                            'js/controllers/BlankController.js'
                        ]
                    });
                }]
            }
        })
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
                            'js/adsearch/AdsearchController.js',
                            '/bower_components/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/pages/scripts/components-bootstrap-select.min.js',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js'
                        ]
                    });
                }]
            }
        })

    // AngularJS plugins
    .state('fileupload', {
        url: "/file_upload.html",
        templateUrl: "views/file_upload.html",
        data: {
            pageTitle: 'AngularJS File Upload'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'angularFileUpload',
                    files: [
                        '../assets/global/plugins/angularjs/plugins/angular-file-upload/angular-file-upload.min.js',
                    ]
                }, {
                    name: 'MetronicApp',
                    files: [
                        'js/controllers/GeneralPageController.js'
                    ]
                }]);
            }]
        }
    })

    // UI Select
    .state('uiselect', {
        url: "/ui_select.html",
        templateUrl: "views/ui_select.html",
        data: {
            pageTitle: 'AngularJS Ui Select'
        },
        controller: "UISelectController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'ui.select',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                        '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                    ]
                }, {
                    name: 'MetronicApp',
                    files: [
                        'js/controllers/UISelectController.js'
                    ]
                }]);
            }]
        }
    })

    // UI Bootstrap
    .state('uibootstrap', {
        url: "/ui_bootstrap.html",
        templateUrl: "views/ui_bootstrap.html",
        data: {
            pageTitle: 'AngularJS UI Bootstrap'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'MetronicApp',
                    files: [
                        'js/controllers/GeneralPageController.js'
                    ]
                }]);
            }]
        }
    })

    // Tree View
    .state('tree', {
        url: "/tree",
        templateUrl: "views/tree.html",
        data: {
            pageTitle: 'jQuery Tree View'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/jstree/dist/themes/default/style.min.css',

                        '../assets/global/plugins/jstree/dist/jstree.min.js',
                        '../assets/pages/scripts/ui-tree.min.js',
                        'js/controllers/GeneralPageController.js'
                    ]
                }]);
            }]
        }
    })

    // Form Tools
    .state('formtools', {
        url: "/form-tools",
        templateUrl: "views/form_tools.html",
        data: {
            pageTitle: 'Form Tools'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
                        '../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',
                        '../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css',
                        '../assets/global/plugins/typeahead/typeahead.css',

                        '../assets/global/plugins/fuelux/js/spinner.min.js',
                        '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
                        '../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js',
                        '../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js',
                        '../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js',
                        '../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
                        '../assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
                        '../assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
                        '../assets/global/plugins/typeahead/handlebars.min.js',
                        '../assets/global/plugins/typeahead/typeahead.bundle.min.js',
                        '../assets/pages/scripts/components-form-tools-2.min.js',

                        'js/controllers/GeneralPageController.js'
                    ]
                }]);
            }]
        }
    })

    // Date & Time Pickers
    .state('pickers', {
        url: "/pickers",
        templateUrl: "views/pickers.html",
        data: {
            pageTitle: 'Date & Time Pickers'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/clockface/css/clockface.css',
                        '../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css',
                        '../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
                        '../assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css',
                        '../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',

                        '../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                        '../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js',
                        '../assets/global/plugins/clockface/js/clockface.js',
                        '../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js',
                        '../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',

                        '../assets/pages/scripts/components-date-time-pickers.min.js',

                        'js/controllers/GeneralPageController.js'
                    ]
                }]);
            }]
        }
    })

    // Custom Dropdowns
    .state('dropdowns', {
        url: "/dropdowns",
        templateUrl: "views/dropdowns.html",
        data: {
            pageTitle: 'Custom Dropdowns'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load([{
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                        '../assets/global/plugins/select2/css/select2.min.css',
                        '../assets/global/plugins/select2/css/select2-bootstrap.min.css',

                        '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                        '../assets/global/plugins/select2/js/select2.full.min.js',

                        '../assets/pages/scripts/components-bootstrap-select.min.js',
                        '../assets/pages/scripts/components-select2.min.js',

                        'js/controllers/GeneralPageController.js'
                    ]
                }]);
            }]
        }
    })

    // Advanced Datatables
    .state('datatablesmanaged', {
        url: "/datatables/managed.html",
        templateUrl: "views/datatables/managed.html",
        data: {
            pageTitle: 'Advanced Datatables'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/datatables/datatables.min.css',
                        '../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css',

                        '../assets/global/plugins/datatables/datatables.all.min.js',

                        '../assets/pages/scripts/table-datatables-managed.min.js',

                        'js/controllers/GeneralPageController.js'
                    ]
                });
            }]
        }
    })

    // Ajax Datetables
    .state('datatablesajax', {
        url: "/datatables/ajax.html",
        templateUrl: "views/datatables/ajax.html",
        data: {
            pageTitle: 'Ajax Datatables'
        },
        controller: "GeneralPageController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/datatables/datatables.min.css',
                        '../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css',
                        '../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css',

                        '../assets/global/plugins/datatables/datatables.all.min.js',
                        '../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                        '../assets/global/scripts/datatable.min.js',

                        'js/scripts/table-ajax.js',
                        'js/controllers/GeneralPageController.js'
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
            pageTitle: 'User Profile'
        },
        controller: "UserProfileController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
                        '../assets/pages/css/profile.css',

                        '../assets/global/plugins/jquery.sparkline.min.js',
                        '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',

                        '../assets/pages/scripts/profile.min.js',

                        'js/controllers/UserProfileController.js'
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

    // Todo
    .state('todo', {
        url: "/todo",
        templateUrl: "views/todo.html",
        data: {
            pageTitle: 'Todo'
        },
        controller: "TodoController",
        resolve: {
            deps: ['$ocLazyLoad', function($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name: 'MetronicApp',
                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                    files: [
                        '../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css',
                        '../assets/apps/css/todo-2.css',
                        '../assets/global/plugins/select2/css/select2.min.css',
                        '../assets/global/plugins/select2/css/select2-bootstrap.min.css',

                        '../assets/global/plugins/select2/js/select2.full.min.js',

                        '../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',

                        '../assets/apps/scripts/todo-2.min.js',

                        'js/controllers/TodoController.js'
                    ]
                });
            }]
        }
    });

}]);

/* Init global settings and run the app */
MetronicApp.run(["$rootScope", "settings", "$state", function($rootScope, settings, $state) {
    $rootScope.$state = $state; // state to be accessed from view
    $rootScope.$settings = settings; // state to be accessed from view
    $rootScope.swal = swal;
}]);
