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
    mobile: 2
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
            durationRange:[0, 365],
            seeTimesRange:[0, 365],
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
                key: "adser_name,adser_username",
                value: "Advertiser"
            }, {
                key: "dest_site",
                value: "Destsite"
            }, {
                key: "description",
                value: "Content"
            }, {
                key:"whyseeads,whyseeads_all",
                value:"Audience"
            }],
            langList: [{
                key: "af",
                value: "af"
            }, {
                key: "ar",
                value: "ar"
            }, {
                key: "bg",
                value: "bg"
            }, {
                key: "bn",
                value: "bn"
            }, {
                key: "ca",
                value: "ca"
            }, {
                key: "cs",
                value: "cs"
            }, {
                key: "cy",
                value: "cy"
            }, {
                key: "da",
                value: "da"
            }, {
                key: "de",
                value: "de"
            }, {
                key: "el",
                value: "el"
            }, {
                key: "en",
                value: "en"
            }, {
                key: "es",
                value: "es"
            }, {
                key: "et",
                value: "et"
            }, {
                key: "fa",
                value: "fa"
            }, {
                key: "fi",
                value: "fi"
            }, {
                key: "fr",
                value: "fr"
            }, {
                key: "gu",
                value: "gu"
            }, {
                key: "he",
                value: "he"
            }, {
                key: "hi",
                value: "hi"
            }, {
                key: "hr",
                value: "hr"
            }, {
                key: "hu",
                value: "hu"
            }, {
                key: "id",
                value: "id"
            }, {
                key: "it",
                value: "it"
            }, {
                key: "ja",
                value: "ja"
            }, {
                key: "kn",
                value: "kn"
            }, {
                key: "ko",
                value: "ko"
            }, {
                key: "lt",
                value: "lt"
            }, {
                key: "lv",
                value: "lv"
            }, {
                key: "mk",
                value: "mk"
            }, {
                key: "ml",
                value: "ml"
            }, {
                key: "mr",
                value: "mr"
            }, {
                key: "ne",
                value: "ne"
            }, {
                key: "nl",
                value: "nl"
            }, {
                key: "no",
                value: "no"
            }, {
                key: "pa",
                value: "pa"
            }, {
                key: "pl",
                value: "pl"
            }, {
                key: "pt",
                value: "pt"
            }, {
                key: "ro",
                value: "ro"
            }, {
                key: "ru",
                value: "ru"
            }, {
                key: "sk",
                value: "sk"
            }, {
                key: "sl",
                value: "sl"
            }, {
                key: "so",
                value: "so"
            }, {
                key: "sq",
                value: "sq"
            }, {
                key: "sv",
                value: "sv"
            }, {
                key: "sw",
                value: "sw"
            }, {
                key: "ta",
                value: "ta"
            }, {
                key: "te",
                value: "te"
            }, {
                key: "th",
                value: "th"
            }, {
                key: "tl",
                value: "tl"
            }, {
                key: "tr",
                value: "tr"
            }, {
                key: "uk",
                value: "uk"
            }, {
                key: "ur",
                value: "ur"
            }, {
                key: "vi",
                value: "vi"
            }, {
                key: "zh-cn",
                value: "zh-cn"
            }, {
                key: "zh-tw",
                value: "zh-tw"
            }, {
                key: "af",
                value: "Afrikaans"
            }, {
                key: "am",
                value: "Amharic"
            }, {
                key: "an",
                value: "Aragonese"
            }, {
                key: "ar",
                value: "Arabic"
            }, {
                key: "as",
                value: "Assamese"
            }, {
                key: "az",
                value: "Azerbaijani"
            }, {
                key: "be",
                value: "Belarusian"
            }, {
                key: "bg",
                value: "Bulgarian"
            }, {
                key: "bn",
                value: "Bengali"
            }, {
                key: "br",
                value: "Breton"
            }, {
                key: "bs",
                value: "Bosnian"
            }, {
                key: "ca",
                value: "Catalan"
            }, {
                key: "cs",
                value: "Czech"
            }, {
                key: "cy",
                value: "Welsh"
            }, {
                key: "da",
                value: "Danish"
            }, {
                key: "de",
                value: "German"
            }, {
                key: "dz",
                value: "Dzongkha"
            }, {
                key: "el",
                value: "Modern Greek (1453-)"
            }, {
                key: "en",
                value: "English"
            }, {
                key: "eo",
                value: "Esperanto"
            }, {
                key: "es",
                value: "Spanish"
            }, {
                key: "et",
                value: "Estonian"
            }, {
                key: "eu",
                value: "Basque"
            }, {
                key: "fa",
                value: "Persian"
            }, {
                key: "fi",
                value: "Finnish"
            }, {
                key: "fo",
                value: "Faroese"
            }, {
                key: "fr",
                value: "French"
            }, {
                key: "ga",
                value: "Irish"
            }, {
                key: "gl",
                value: "Galician"
            }, {
                key: "gu",
                value: "Gujarati"
            }, {
                key: "he",
                value: "Hebrew"
            }, {
                key: "hi",
                value: "Hindi"
            }, {
                key: "hr",
                value: "Croatian"
            }, {
                key: "ht",
                value: "Haitian"
            }, {
                key: "hu",
                value: "Hungarian"
            }, {
                key: "hy",
                value: "Armenian"
            }, {
                key: "id",
                value: "Indonesian"
            }, {
                key: "is",
                value: "Icelandic"
            }, {
                key: "it",
                value: "Italian"
            }, {
                key: "ja",
                value: "Japanese"
            }, {
                key: "jv",
                value: "Javanese"
            }, {
                key: "ka",
                value: "Georgian"
            }, {
                key: "kk",
                value: "Kazakh"
            }, {
                key: "km",
                value: "Central Khmer"
            }, {
                key: "kn",
                value: "Kannada"
            }, {
                key: "ko",
                value: "Korean"
            }, {
                key: "ku",
                value: "Kurdish"
            }, {
                key: "ky",
                value: "Kirghiz"
            }, {
                key: "la",
                value: "Latin"
            }, {
                key: "lb",
                value: "Luxembourgish"
            }, {
                key: "lo",
                value: "Lao"
            }, {
                key: "lt",
                value: "Lithuanian"
            }, {
                key: "lv",
                value: "Latvian"
            }, {
                key: "mg",
                value: "Malagasy"
            }, {
                key: "mk",
                value: "Macedonian"
            }, {
                key: "ml",
                value: "Malayalam"
            }, {
                key: "mn",
                value: "Mongolian"
            }, {
                key: "mr",
                value: "Marathi"
            }, {
                key: "ms",
                value: "Malay (macrolanguage)"
            }, {
                key: "mt",
                value: "Maltese"
            }, {
                key: "nb",
                value: "Norwegian Bokmål"
            }, {
                key: "ne",
                value: "Nepali (macrolanguage)"
            }, {
                key: "nl",
                value: "Dutch"
            }, {
                key: "nn",
                value: "Norwegian Nynorsk"
            }, {
                key: "no",
                value: "Norwegian"
            }, {
                key: "oc",
                value: "Occitan (post 1500)"
            }, {
                key: "or",
                value: "Oriya (macrolanguage)"
            }, {
                key: "pa",
                value: "Panjabi"
            }, {
                key: "pl",
                value: "Polish"
            }, {
                key: "ps",
                value: "Pushto"
            }, {
                key: "pt",
                value: "Portuguese"
            }, {
                key: "qu",
                value: "Quechua"
            }, {
                key: "ro",
                value: "Romanian"
            }, {
                key: "ru",
                value: "Russian"
            }, {
                key: "rw",
                value: "Kinyarwanda"
            }, {
                key: "se",
                value: "Northern Sami"
            }, {
                key: "si",
                value: "Sinhala"
            }, {
                key: "sk",
                value: "Slovak"
            }, {
                key: "sl",
                value: "Slovenian"
            }, {
                key: "sq",
                value: "Albanian"
            }, {
                key: "sr",
                value: "Serbian"
            }, {
                key: "sv",
                value: "Swedish"
            }, {
                key: "sw",
                value: "Swahili (macrolanguage)"
            }, {
                key: "ta",
                value: "Tamil"
            }, {
                key: "te",
                value: "Telugu"
            }, {
                key: "th",
                value: "Thai"
            }, {
                key: "tl",
                value: "Tagalog"
            }, {
                key: "tr",
                value: "Turkish"
            }, {
                key: "ug",
                value: "Uighur"
            }, {
                key: "uk",
                value: "Ukrainian"
            }, {
                key: "ur",
                value: "Urdu"
            }, {
                key: "vi",
                value: "Vietnamese"
            }, {
                key: "vo",
                value: "Volapük"
            }, {
                key: "wa",
                value: "Walloon"
            }, {
                key: "xh",
                value: "Xhosa"
            }, {
                key: "unknown_none",
                value: "Yaghan"
            }, {
                key: "unknown",
                value: "Yutes"
            }, {
                key: "zh",
                value: "Chinese"
            }, {
                key: "zu",
                value: "Zulu"
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
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'js/adsearch/AdsearchController.js'
                        ]
                    });
                }]
            }
        })
        .state('adser', {
            url: '/adsearch/{adser}?n={name}',
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
                            'js/adsearch/AdsearchController.js'
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
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'js/adsearch/AdsearchController.js'
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
                            'js/adsearch/AdsearchController.js'
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
                            'js/adsearch/AdsearchController.js'
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
                            'js/adsearch/AdsearchController.js'
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
                            'js/adsearch/AdsearchController.js'
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
                        '/node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                        '/node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                        '/node_modules/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js',

                            'js/adsearch/AdsearchController.js'
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
                            'js/adsearch/AdsearchController.js'
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
    $locationProvider.html5Mode(true);

}]);
MetronicApp.config(['lazyImgConfigProvider', function(lazyImgConfigProvider) {
    // var scrollable = document.querySelector('#scrollable');
    lazyImgConfigProvider.setOptions({
      offset: 100, // how early you want to load image (default = 100)
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


MetronicApp.factory('User', ['$http', '$q', '$location', '$rootScope', 'settings', 'ADS_TYPE', function($http, $q, $location, $rootScope, settings, ADS_TYPE) {
    //获取信息完成后应该广播消息，然后其他需要在获取用户信息才能继续的操作就放到接收到广播后处理
    var infourl = settings.remoteurl  + "/userinfo";
    var user = {
        retreived:false,
        info:{},
        getInfo:function(refresh) {
            if (!refresh && user.retreived)
                return user.promise;
           user.promise = $http.get(infourl);
           user.promise.then(function(res) {
                user.info = res.data;
                console.log(user.info);
           }, function(res) {
                user.info = {};
           }).finally(function() {
                $rootScope.$broadcast('userChanged', user.info);
           });
           user.retreived = true;
           return user.promise;
        },
        login:function() {
            $location.url("/login");
        },
        can:function(key) {
            //无登陆时的策略与有登陆需要做策略区分(只在服务器端区分是更好的做法)
            if (!user.info.permissions)
                return false;
            if (!user.info.permissions[key])
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
            if (!user.info.user.usage[key])//没有策略不需要策略
                return true;
            usage = user.info.user.usage[key];
            if (usage.length > 2)
                return {type:usage[0], value:usage[1], used: usage[2]};
            return {type:usage[0], value:usage[1], used:0};
        }
    };
    return user;
}]);

MetronicApp.controller('UserController', ['$scope', 'User', function($scope, User) {
    User.getInfo().then(function(res) {
        console.log(User.info);
    });
    $scope.User = User;
}]);
