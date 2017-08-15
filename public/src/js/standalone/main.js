import "bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css"
import 'sweetalert/dist/sweetalert.css'
import 'angular-busy/dist/angular-busy.min.css'
import './../../sass/global/font.scss'
import './../../sass/global/components-md.scss'
import './../../sass/layouts/layout3/layout.scss'
import './../../sass/layouts/layout3/themes/yellow-orange.scss'

/***
Metronic AngularJS App Main Script
***/
window.moment = require('moment')

/* Metronic App */
var MetronicApp = angular.module("MetronicApp", [
    "ui.router",
    "ui.bootstrap",
    "oc.lazyLoad",
    "ngSanitize",
    "oitozero.ngSweetAlert",
    'ngResource',
    'cgBusy'
])

/* Configure ocLazyLoader(refer: https://github.com/ocombe/ocLazyLoad) */
MetronicApp.config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
    $ocLazyLoadProvider.config({
        // global configs go here
    })
}])

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

// AngularJS v1.3.x workaround for old style controller declarition in HTML
MetronicApp.config(['$controllerProvider', function($controllerProvider) {
    // this option might be handy for migrating old apps, but please don't use it
    // in new ones!
    $controllerProvider.allowGlobals()
}])

/********************************************
 END: BREAKING CHANGE in AngularJS v1.3.x:
*********************************************/
/* Setup global settings */
MetronicApp.constant('TIMESTAMP', Date.parse(new Date()))
MetronicApp.constant('ADS_TYPE', {
    timeline: 1,
    rightcolumn: 4,
    mobile: 2,
    phone: 2
})
MetronicApp.constant('ADS_CONT_TYPE', {
    SINGLE_IMAGE: "SingleImage",
    CANVAS: "Canvas",
    CAROUSEL: "Carousel",
    SINGLE_VIDEO: "SingleVideo"
})
MetronicApp.constant('POLICY_TYPE', {PERMANENT: 0, MONTH: 1, DAY: 2, HOUR: 3, VALUE: 4, DURATION: 5, YEAR: 6})
MetronicApp.factory('settings', ['$rootScope', function($rootScope) {
    // supported languages
    var settings = {
        timestamp: Date.parse(new Date()),
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
        remoteurl: '', // 根目录
        imgRemoteBase: [
            'http://image1.bigbigads.com:88',
            'http://image2.bigbigads.com:88',
            'http://image3.bigbigads.com:88',
            'http://image4.bigbigads.com:88'
        ],
        videoRemoteBase: 'http://image1.bigbigads.com:88',
        searchSetting: {
            pageCount: 10, // 每一页的数据量
            durationRange: [0, 180],
            seeTimesRange: [0, 180],
            orderBy: [{
                key: 'last_view_date',
                value: 'Last_Seen',
                last: false,
                group: 'time',
                permission: 'date_sort'
            }, {
                key: 'duration_days',
                value: 'Duration',
                last: true,
                group: 'time',
                permission: 'duration_sort'
            }, {
                key: 'engagements',
                value: 'Engagements',
                last: false,
                group: 'seen',
                permission: 'engagements_sort'
            }, {
                key: 'views',
                value: 'Video Views',
                last: false,
                group: 'seen',
                permission: 'views_sort'
            }, {
                key: 'engagements_per_7d',
                value: 'Engagements Growth',
                last: false,
                group: 'seen',
                permission: 'engagement_inc_sort'
            }, {
                key: 'views_per_7d',
                value: 'Video Views Growth',
                last: true,
                group: 'seen',
                permission: 'views_inc_sort'
            }, {
                key: 'likes',
                value: 'Likes',
                last: false,
                group: 'interactive',
                permission: 'likes_sort'
            }, {
                key: 'shares',
                value: 'Shares',
                last: false,
                group: 'interactive',
                permission: 'shares_sort'
            }, {
                key: 'comments',
                value: 'Comments',
                last: false,
                group: 'interactive',
                permission: 'comment_sort'
            }, {
                key: 'likes_per_7d',
                value: 'Likes Growth',
                last: false,
                group: 'interactive',
                permission: 'likes_inc_sort'
            }, {
                key: 'shares_per_7d',
                value: 'Shares Growth',
                last: false,
                group: 'interactive',
                permission: 'shares_inc_sort'
            }, {
                key: 'comments_per_7d',
                value: 'Comments Growth',
                last: true,
                group: 'interactive',
                permission: 'comments_inc_sort'
            }],
            adsTypes: [{
                key: 'timeline',
                value: 'Newsfeed',
                permission: 'timeline_filter'
            }, {
                key: 'rightcolumn',
                value: 'Right Column',
                permission: 'rightcolumn_filter'
            }, {
                key: 'phone',
                value: 'Mobile',
                permission: 'phone_filter'
            }, {
                key: 'suggested app',
                value: 'App',
                permission: 'app_filter'
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
                value: "Video"
            }, {
                key: "Canvas",
                value: "Others"
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
                permission: "advertiser_search"
            }, {
                key: "link,buttonlink,dest_site",
                value: "URL",
                permission: "dest_site_search"
            }, {
                key: "description,name,caption,message",
                value: "Advertisement",
                permission: "content_search"
            } /* { 摒弃，已细化出为更具体的受众过滤
                key:"whyseeads,whyseeads_all",
                value:"Audience",
                permission:"audience_search"
            } */],
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
            }],
            /* add country list */
            country: [
                {
                    key: "Brazil",
                    value: "Brazil"
                }, {
                    key: "Canada",
                    value: "Canada"
                }, {
                    key: "Denmark",
                    value: "Denmark"
                }, {
                    key: "Finland",
                    value: "Finland"
                }, {
                    key: "France",
                    value: "France"
                }, {
                    key: "Germany",
                    value: "Germany"
                }, {
                    key: "Hongkong",
                    value: "Hongkong"
                }, {
                    key: "Indonesia",
                    value: "Indonesia"
                }, {
                    key: "India",
                    value: "India"
                }, {
                    key: "Italy",
                    value: "Italy"
                }, {
                    key: "Japan",
                    value: "Japan"
                }, {
                    key: "Korea",
                    value: "Korea"
                }, {
                    key: "Mexico",
                    value: "Mexico"
                }, {
                    key: "Norway",
                    value: "Norway"
                }, {
                    key: "Philippines",
                    value: "Philippines"
                }, {
                    key: "Russia",
                    value: "Russia"
                }, {
                    key: "Sweden",
                    value: "Sweden"
                }, {
                    key: "Thailand",
                    value: "Thailand"
                }, {
                    key: "United Kingdom",
                    value: "United Kingdom"
                }, {
                    key: "United States",
                    value: "United States"
                }, {
                    key: "Vietnam",
                    value: "Vietnam"
                }, {
                    key: "Taiwan",
                    value: "Taiwan"
                }],
            trackingList: [{
                key: "CPV Lab",
                value: "CPV Lab"
            }, {
                key: "iMobiTrax",
                value: "iMobiTrax"
            }, {
                key: "prosper202",
                value: "prosper202"
            }, {
                key: "voluum",
                value: "voluum"
            }, {
                key: "thrive",
                value: "thrive"
            }, {
                key: "google analytics",
                value: "google analytics"
            }],
            affiliateList: [{
                key: "oasis",
                value: "oasis"
            }, {
                key: "ad4game",
                value: "ad4game"
            }],
            eCommerceList: [{
                key: "teespring",
                value: "teespring"
            }, {
                key: "teechip",
                value: "teechip"
            }, {
                key: "teezily",
                value: "teezily"
            }, {
                key: "shopify",
                value: "shopify"
            }, {
                key: "magento",
                value: "magento"
            }, {
                key: "wooCommerce",
                value: "wooCommerce"
            }],
            audienceAge: [{
                key: "18-24",
                value: "18-24"
            }, {
                key: "25-34",
                value: "25-34"
            }, {
                key: "35-44",
                value: "35-44"
            }, {
                key: "45-54",
                value: "45-54"
            }, {
                key: "55-64",
                value: "55-64"
            }, {
                key: "65",
                value: "65"
            }],
            audienceGender: [{
                key: "only female",
                value: "only female"
            }, {
                key: "only male",
                value: "only male"
            }, {
                key: "both",
                value: "both"
            }, {
                key: "include female",
                value: "include female"
            }, {
                key: "include male",
                value: "include male"
            }],
            objective: [{
                key: "APP_INSTALLS",
                value: "APP_INSTALLS"
            }, {
                key: "BRAND_AWARENESS",
                value: "BRAND_AWARENESS"
            }, {
                key: "CANVAS_APP_INSTALLS",
                value: "CANVAS_APP_INSTALLS"
            }, {
                key: "EVENT_RESPONSES",
                value: "EVENT_RESPONSES"
            }, {
                key: "LEAD_GENERATION",
                value: "LEAD_GENERATION"
            }, {
                key: "LINK_CLICKS",
                value: "LINK_CLICKS"
            }, {
                key: "LOCAL_AWARENESS",
                value: "LOCAL_AWARENESS"
            }, {
                key: "PAGE_LIKES",
                value: "PAGE_LIKES"
            }, {
                key: "POST_ENGAGEMENT",
                value: "POST_ENGAGEMENT"
            }, {
                key: "PRODUCT_CATALOG_SALES",
                value: "PRODUCT_CATALOG_SALES"
            }, {
                key: "REACH",
                value: "REACH"
            }, {
                key: "STORE_VISITS",
                value: "STORE_VISITS"
            }, {
                key: "VIDEO_VIEWS",
                value: "VIDEO_VIEWS"
            }, {
                key: "WEBSITE_CONVERSIONS",
                value: "WEBSITE_CONVERSIONS"
            }]
        }
    }

    $rootScope.settings = settings

    return settings
}])
MetronicApp.filter('nocache', ['TIMESTAMP', function(TIMESTAMP) {
    return function(url) {
        return url + '?t=' + TIMESTAMP
    }
}])
MetronicApp.filter('toHtml', ['$sce', function($sce) {
    return function(text) {
        return $sce.trustAsHtml(text)
    }
}])
    .filter('trusted', ['$sce', function($sce) {
        return function(url) {
            return $sce.trustAsResourceUrl(url)
        }
    }])
    .filter('clearHttps', function() {
        var link = ""
        return function(httpLink) {
            if (httpLink === null || httpLink === undefined || httpLink === '') return
            if (httpLink.indexOf("http") >= 0) {
                link = httpLink.replace(/http:\/\//, "")
            }
            if (httpLink.indexOf("https") >= 0) {
                link = httpLink.replace(/https:\/\//, "")
            }
            return link
        }
    })
    .filter('addUnit', function() {
        var unitNum = ''
        return function(num) {
            if (num === null || num === undefined || num === '') return
            num = Number(num)
            if (num < 1000) {
                return num
            }
            if (num >= 1000 && num < 1000000) {
                num = num / 1000
                unitNum = num.toFixed(1) + 'K'
                return unitNum
            }
            if (num >= 1000000) {
                num = num / 1000000
                unitNum = num.toFixed(1) + 'M'
                return unitNum
            }
        }
    })
    .filter('getImageSize', function() {
        // @param type 0表示宽度,1表示高度
        return function(url, type) {
            var size
            var pos = url.lastIndexOf('#')
            if (pos < 0)
                return 0
            size = url.slice(pos + 1).split('*')
            if (size.length != 2)
                return 0
            return size[type]
        }
    })
    .filter('mediaType', function() {
        return function(type) {
            var showType = ''
            switch (type) {
            case 'timeline': { showType = 'Newsfeed'; break }
            case 'rightcolumn': { showType = 'Right Column'; break }
            case 'phone': { showType = 'Mobile'; break }
            case 'suggested app': { showType = 'App'; break }
            default: break
            }
            return showType
        }
    })
    .filter('adsCount', function() {
        return function(adsNumber) {
            if (adsNumber === 0) return adsNumber
            if (!adsNumber) return
            var countString = ''
            var re = /(?=(?!\b)(\d{3})+$)/g
            adsNumber = String(adsNumber)
            countString = adsNumber.replace(re, ',')
            return countString
        }
    })
    .filter('noPrice', function() {
        return function(price) {
            if (price === 0) return price
            if (!price) return
            if (price >= 299) return '???'
            // 不使用 === 判断是因为年月份计费时有小数
            return price
        }
    })
    .filter('formatType', function() {
        return function(type) {
            var showType = ''
            switch (type) {
            case 'Canvas': { showType = 'Others'; break }
            case 'SingleVideo': { showType = 'Video'; break }
            case 'SingleImage': { showType = 'Image'; break }
            case 'Carousel': { showType = 'Carousel'; break }
            default: break
            }
            return showType
        }
    })
    .filter('adsTypes', function() {
        // 广告的类型show_way字段调整为从右到左 比特位分别表示 时间线 手机端 右边栏 安卓
        // 后续还会继续添加类型
        return function(showWay) {
            if (!showWay) return
            var binary = showWay.toString(2)
            var adsTypesNumber = binary.split("").reverse()
            var showString = ""
            var adsTypesString = ["News Feed", "Mobile", "Right Column"] /*, "Andorid" */
            var index
            // 广告可能同时出现在多个位置
            for (index = 0; index < adsTypesNumber.length; index++) {
                // Andorid 标示暂不显示
                if (adsTypesNumber[index] === "1" && index != 3) {
                    if (!showString) {
                        showString = adsTypesString[index]
                    } else {
                        showString = showString + ' & ' + adsTypesString[index]
                    }
                }
            }
            return showString
        }
    })
/* Setup App Main Controller */
MetronicApp.controller('AppController', ['$scope', '$rootScope', 'User', function($scope, $rootScope, User) {
    $scope.$on('$viewContentLoaded', function() {
        // Layout.init(); //  Init entire layout(header, footer, sidebar, etc) on page load if the partials included in server side instead of loading with ng-include directive 
    })
}])

/***
Layout Partials.
By default the partials are loaded through AngularJS ng-include directive. In case they loaded in server side(e.g: PHP include function) then below partial 
initialization can be disabled and Layout.init() should be called on page load complete as explained above.
***/

/* Setup Layout Part - Header */
MetronicApp.controller('HeaderController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        // Layout.initHeader(); // init header

    })
}])
MetronicApp.controller('TabMenuController', ['$scope', '$location', 'User', '$state', function($scope, $location, User, $state) {
    var tabmenu = {
        name: $location.path()
    }
    $scope.tabmenu = tabmenu
    $scope.User = User
    $scope.checkAccount = function() {
        if ((User.info.user.role.name != 'Free') && (User.info.user.role.name != 'Standard')) return
        User.openUpgrade()
    }
    $scope.goBookMark = function() {
        if (!User.login) {
            User.openSign()
        } else {
            $state.go("bookmark")
        }
    }
    $scope.$on('$locationChangeSuccess', function() {
        tabmenu.name = $location.path()
    })
}])
/* Setup Layout Part - Sidebar */
MetronicApp.controller('SidebarController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        // Layout.initSidebar($state); // init sidebar
    })
}])

/* Setup Layout Part - Footer */
MetronicApp.controller('FooterController', ['$scope', function($scope) {
    $scope.$on('$includeContentLoaded', function() {
        // Layout.initFooter(); // init footer
    })
}])

/* Setup Rounting For All Pages */
MetronicApp.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$urlMatcherFactoryProvider', 'TIMESTAMP', function($stateProvider, $urlRouterProvider, $locationProvider, $urlMatcherFactoryProvider, TIMESTAMP) {
    var ts = TIMESTAMP
    // Redirect any unmatched url
    $urlMatcherFactoryProvider.strictMode(false)
    // $urlRouterProvider.when("/", "/adsearch");
    $urlRouterProvider.otherwise("/404.html?t=" + ts)

    $stateProvider
        .state('/', {
            url: '/',
            templateUrl: "views/search.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinModern.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            '/node_modules/allmighty-autocomplete/script/autocomplete.js',
                            '/node_modules/allmighty-autocomplete/style/autocomplete.css',
                            'js/bigbigads.js?r=' + ts
                        ]
                    })
                }]
            }
        })
        .state('adsearch', {
            url: '/adsearch',
            templateUrl: "views/search.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinModern.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            '/node_modules/allmighty-autocomplete/script/autocomplete.js',
                            '/node_modules/allmighty-autocomplete/style/autocomplete.css',
                            'js/bigbigads.js?r=' + ts
                        ]
                    })
                }]
            }
        })
        .state('adser', {
            url: '/adsearch/{adser}/{name}',
            templateUrl: "views/adser.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }

        })
        .state('ownerSearch', {
            url: '/ownerSearch',
            templateUrl: "views/owner-search.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css',
                            '../assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css',
                            '../assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js',
                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }
        })
        .state('adserAnalysis', {
            url: '/adserAnalysis/{username}',
            templateUrl: "views/adser-analysis.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            '/node_modules/jqcloud2/dist/jqcloud.min.css',
                            '/node_modules/jqcloud2/dist/jqcloud.min.js',
                            '../assets/global/plugins/angular-jqcloud.js',
                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }
        })
        .state('adAnalysis', {
            url: '/adAnalysis/{id}',
            templateUrl: "views/ad-analysis.html?t=" + ts,
            data: {
                pageTitle: 'Advertise Analysis'
            },
            resolve: {
                deps: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'MetronicApp',
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/angular-deckgrid/angular-deckgrid.js',
                            '/node_modules/ng-infinite-scroll/build/ng-infinite-scroll.min.js',
                            '../assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css',
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/highcharts-ng/dist/highcharts-ng.min.js',
                            '../assets/layouts/layout3/css/analysis.css',
                            'js/bigbigads.js?r=' + Math.random()
                        ]
                    }, {
                        serie: true,
                        insertBefore: ' #ng_load_plugins_before',
                        files: [
                            '/node_modules/highcharts/highcharts.js',
                            '/node_modules/highcharts/modules/map.js',
                            '../assets/global/scripts/world.min.js'
                        ]
                    }]).then(function() {

                    })
                }]
            }

        })
        .state('ranking', {
            url: '/ranking',
            templateUrl: "views/ranking.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }
        })
        .state('bookmark', {
            url: '/bookmark',
            templateUrl: "views/bookmark.html?t=" + ts,
            data: {
                pageTitle: 'Bookmark'
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }

        })
        .state('plans', {
            url: '/plans',
            templateUrl: "views/plans.html?t=" + ts,
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
                            '../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js',
                            '../assets/global/plugins/select2/css/select2.min.css',
                            '../assets/global/plugins/select2/js/select2.full.min.js',

                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css',
                            '../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js',
                            '/node_modules/angular-daterangepicker/js/angular-daterangepicker.min.js',
                            '/node_modules/fancybox/dist/css/jquery.fancybox.css',
                            '/node_modules/fancybox/dist/js/jquery.fancybox.pack.js',
                            '/node_modules/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
                            '/node_modules/bootstrap-switch/dist/js/bootstrap-switch.min.js',
                            '/node_modules/angular-bootstrap-switch/dist/angular-bootstrap-switch.min.js',

                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }

        })
    // User Profile
        .state("profile", {
            url: "/profile",
            templateUrl: "views/profile/main.html?t=" + ts,
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
                            'js/bigbigads.js?t=' + ts
                        ]
                    })
                }]
            }
        })

    // User Profile Dashboard
        .state("profile.dashboard", {
            url: "/dashboard",
            templateUrl: "views/profile/dashboard.html?t=" + ts,
            data: {
                pageTitle: 'User Profile'
            }
        })

    // User Profile Account
        .state("profile.account", {
            url: "/account",
            templateUrl: "views/profile/account.html?t=" + ts,
            data: {
                pageTitle: 'User Account'
            }
        })
    // User Profile Help
        .state("profile.help", {
            url: "/help",
            templateUrl: "views/profile/help.html?t=" + ts,
            data: {
                pageTitle: 'User Help'
            }
        })
    $locationProvider.html5Mode(true)
}])

/* Init global settings and run the app */
MetronicApp.run(["$rootScope", "settings", "$state", 'User', 'SweetAlert', '$location', '$window', function($rootScope, settings, $state, User, SweetAlert, $location, $window) {
    var days
    if ($location.search().track) {
        days = $location.search().track.match(/\d\d$/)
        days = days ? Number(days[0]) : 90
        $window.localStorage.setItem('track', JSON.stringify({"code": $location.search().track, "expired": moment().add(days, 'days').format('YYYY-MM-DD')}))
    }
    $rootScope.$state = $state // state to be accessed from view
    $rootScope.$settings = settings // state to be accessed from view
    // 使用boot方法启动是另一套js
    var APP_ID = "pv0r2p1a"
    var w = window; var ic = w.Intercom; if (typeof ic === "function") { ic('reattach_activator'); ic('update', intercomSettings) } else {
        /* eslint-disable no-inner-declarations */ 
        var d = document; var i = function() { i.c(arguments) }; i.q = []; i.c = function(args) { i.q.push(args) }; w.Intercom = i; function l() {
            var s = d.createElement('script'); s.type = 'text/javascript'; s.async = true
            s.src = 'https://widget.intercom.io/widget/' + APP_ID
            var x = d.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x)
        } if (w.attachEvent) { w.attachEvent('onload', l) } else { w.addEventListener('load', l, false) }
    }
    User.getInfo().then(function() {
        if (User.login) {
            // intercom文档建议使用boot方式来启动，配合shutdown方法关闭会话，提高安全性
            window.Intercom('boot', {
                app_id: "pv0r2p1a",
                email: User.user.email,
                name: User.user.name,
                created_at: User.user.created_at,
                user_hash: User.emailHmac // intercom开启验证用户email的hash值,提高安全性
            })
        } else {
            window.Intercom('boot', {
                app_id: "pv0r2p1a"
            })
        }
        // intercom生成的代码 
        // var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function (){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/pv0r2p1a';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}
    })
    setInterval(function() {
        // 每隔一段时间再次更新user信息，一方面是获取新权限，另一方面是防止session过期客户端不知道;
        if (!User.login)
            return
        var oldInfo = null
        User.getInfo(true)
        $rootScope.$on('userChanged', function(ev, newInfo) {
            if (!oldInfo) {
                oldInfo = newInfo
                return
            }
            if (!newInfo.login && oldInfo.login) {
                // 暂时先注释，还在尝试解决
                // SweetAlert.swal("you have logout because of no operation for a long time");

            }
            // console.log("get info again:", oldInfo);
        })
    }, 60000 * 5)

    // Issue #10 run初始化时，调用User.getInfo(true)强制获取一次用户信息
    User.getInfo(true)
}])

MetronicApp.factory('User', ['$window', '$http', '$q', '$location', '$rootScope', 'settings', 'ADS_TYPE', '$uibModal', 'TIMESTAMP', function($window, $http, $q, $location, $rootScope, settings, ADS_TYPE, $uibModal, TIMESTAMP) {
    // 获取信息完成后应该广播消息，然后其他需要在获取用户信息才能继续的操作就放到接收到广播后处理
    var infourl = settings.remoteurl + "/userinfo"
    var user = {
        retreived: false,
        done: false,
        info: {},
        getInfo: function(refresh) {
            if (!refresh && user.retreived) return user.promise

            user.promise = $http.get(infourl)
            user.promise.then(function(res) {
                // Issue #10 User 获取用户信息时，与 localStorage 比对，发现不一致就更新
                if (JSON.stringify(res.data) != $window.localStorage.user) {
                    user.info = res.data

                    // Issue #10 更新会话存储的用户信息
                    $window.localStorage.user = JSON.stringify(user.info)

                    angular.extend(user, user.info)
                }
            }, function(res) {
                // user.info = {};
            }).finally(function() {
                user.done = true
                $rootScope.$broadcast('userChanged', user.info)
            })
            user.retreived = true
            return user.promise
        },
        // goLoginPage:function() {
        //     $location.url("/login");
        // },
        can: function(key) {
            var keyArr = key.split('|')
            var i
            // 无登陆时的策略与有登陆需要做策略区分(只在服务器端区分是更好的做法)
            if (!user.info.permissions)
                return false
            for (i = 0; i < keyArr.length; ++i)
                if (!user.info.permissions[keyArr[i]])
                    return false
            return true
        },
        usable: function(key, val) {
            // 是否满足策略要求
            var policy = user.getPolicy(key)
            var type
            if (typeof (policy) == 'boolean')
                return policy
            // 根据不同情况返回不同的权限值
            if (key == "platform") {
                if (!val)
                    return true
                type = ADS_TYPE[val]
                // console.log("policy:", policy.value, type, val);
                if ((Number(policy.value) & type) > 0)
                    return true
                return false
            }
            if (policy.used < policy.value)
                return true
            return false
        },
        getPolicy: function(key) {
            var usage
            if (!user.can(key)) // 没有权限一定没有策略
                return false
            if (!user.info.user.usage[key])// 没有策略不需要策略，组合权限不支持策略，所以也返回true
                return true
            usage = user.info.user.usage[key]
            if (usage.length > 2)
                return {type: usage[0], value: usage[1], used: usage[2]}
            return {type: usage[0], value: usage[1], used: 0}
        },
        openUpgrade: function() {
            return $uibModal.open({
                templateUrl: 'views/upgrade.html?t=' + TIMESTAMP,
                size: 'md',
                animation: true,
                controller: ['$scope', '$uibModalInstance', '$state', function($scope, $uibModalInstance, $state) {
                    $scope.goIndex = function() {
                        window.open('/home', "_self")
                        $uibModalInstance.dismiss('success')
                    }
                    $scope.goPlans = function() {
                        $state.go("plans")
                        $uibModalInstance.dismiss('success')
                    }
                }]
            })
        },
        openSign: function() {
            return $uibModal.open({
                templateUrl: 'views/sign.html?t=' + TIMESTAMP,
                size: 'customer',
                backdrop: false,
                animation: true,
                controller: ['$scope', '$uibModalInstance', '$window', function($scope, $uibModalInstance, $window) {
                    var slides = $scope.slides = []
                    var i
                    $scope.addSlide = function() {
                        var imgItem = slides.length + 1
                        slides.push({
                            image: 'adscard_0' + imgItem + '.jpg'
                        })
                    }
                    for (i = 0; i < 4; i++) {
                        $scope.addSlide()
                    }
                    $scope.close = function() {
                        $uibModalInstance.dismiss('cancle')
                    }

                    // 获取track码
                    if ($window.localStorage.getItem('track')) {
                        var track = JSON.parse($window.localStorage.getItem('track'))
                        if (Date.parse(new Date()) < Date.parse(track.expired)) {
                            $scope.trackCode = track.code
                        }
                    } else {
                        $scope.trackCode = null
                    }
                }]
            })
        },
        openSearchResultUpgrade: function() {
            return $uibModal.open({
                templateUrl: 'views/search-result-upgrade.html?t=' + TIMESTAMP,
                size: 'md',
                animation: true,
                controller: ['$scope', '$uibModalInstance', '$state', function($scope, $uibModalInstance, $state) {
                    $scope.goPlans = function() {
                        $state.go("plans")
                        $uibModalInstance.dismiss('success')
                    }
                }]
            })
        },
        openFreeDateLimit: function() {
            return $uibModal.open({
                templateUrl: 'views/filter-data-limit.html?t=' + TIMESTAMP,
                size: 'md',
                animation: true,
                controller: ['$scope', '$uibModalInstance', '$state', function($scope, $uibModalInstance, $state) {
                    $scope.goPlans = function() {
                        $state.go("plans")
                        $uibModalInstance.dismiss('success')
                    }
                    $scope.goIndex = function() {
                        window.open('/home', "_self")
                        $uibModalInstance.dismiss('success')
                    }
                }]
            })
        }
    }

    // Issue #10 User factory 初始化时，从 localStorage 获取用户信息，获取得到就设置 done 和 retreived
    if ($window.localStorage.user) {
        user.info = JSON.parse($window.localStorage.user)

        angular.extend(user, user.info)

        user.done = true
        user.retreived = true

        var defer = $q.defer()
        user.promise = defer.promise
        defer.resolve()
    }

    return user
}])
MetronicApp.controller('UserController', ['$scope', '$http', '$window', 'User', function($scope, $http, $window, User) {
    $scope.User = User
    $scope.formData = {name: ' ', email: '', password: ''}
    $scope.registerError = {}
    $scope.isShow = false
    $scope.logout = function() {
        // 根据intercom的文档，用户退出应使用shutdown方法关闭本次会话
        Intercom('shutdown')
        window.open('/logout', "_self")
    }
    $scope.checkEmail = function() {
        $scope.showHotmailMessage = false
        var emails = ['hotmail.com', 'live.com', 'outlook.com']
        angular.forEach(emails, function(item) {
            if ($scope.formData.email && $scope.formData.email.split('@')[1] === item) {
                $scope.showHotmailMessage = true
            }
        })
    }
    $scope.processForm = function(isValid) {
        $scope.isShow = true
        if ($scope.formData.name == ' ') { $scope.formData.name = $scope.formData.email.split('@')[0] }
        if ($window.localStorage.getItem("track")) {
            var track = JSON.parse($window.localStorage.track)
            var expired = track.expired
            if (moment().isBefore(expired)) {
                $scope.formData.track = track.code
            } else {
                $window.localStorage.removeItem("track")
            }
        }
        if (isValid) {
            $http({
                method: 'POST',
                url: '../register',
                data: $scope.formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' } // server need to know this is a ajax.
            })
                .then(
                    function successCallback(response) {
                    // location.href = response.data.url;
                        location.href = "/welcome?source=email"
                    },
                    function errorCallback(response) {
                        $scope.isShow = false
                        $scope.registerError = response.data
                    }
                )
        }
    }
    /* 弹窗中的点击事件 */
    $scope.shortcutReg = true // 初始化
    $scope.useEmailReg = false // 初始化
    $scope.turnToEmail = function() {
        $scope.shortcutReg = false
        $scope.useEmailReg = true
    }
    $scope.turnToShotcut = function() {
        $scope.shortcutReg = true
        $scope.useEmailReg = false
    }
}])
