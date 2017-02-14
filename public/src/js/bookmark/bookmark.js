
if (!app)
    var app = angular.module('MetronicApp');
app.factory('BookmarkItem', ['Resource', '$uibModal', 'SweetAlert', function(Resource, $uibModal, SweetAlert) {
    var bookmarkItem = new Resource('BookmarkItem');

    return bookmarkItem;
}]);

app.controller('BookmarkController', ['$scope', 'settings', '$http', 'Resource', '$uibModal', 'User', 'Bookmark', 'BookmarkItem', '$location', 'Searcher', 'SweetAlert', function($scope, settings, $http,  Resource, $uibModal, User, Bookmark, BookmarkItem, $location, Searcher, SweetAlert) {
    

    function loadAds(type, bid) {
        Bookmark.showDetail(bid).then(function(items) {
            var wanted = [];
            var wantedAdsers = [];
            var adSearcher = new Searcher({
                limit:[0, -1]
            });
            var adserSearcher = new Searcher({
                    searchType: 'adser',
                    url: '/forward/adserSearch',
                    limit:[0, -1]
                });
            angular.forEach(items, function(item) {
                if (Number(item.type) === 0 && item.bid == bid) {
                    wanted.push(item.ident);
                } 
                if (Number(item.type) === 1 && item.bid == bid) {
                    wantedAdsers.push(item.ident);
                }
            });
        
            //获取广告
            adSearcher.addFilter({
                field: 'ads_id',
                value: wanted.join(',')
            });
            adSearcher.filter().then(function(data) {
                $scope.ads = data;
                $scope.ads.bookmark = true;
            }, function() {
                $scope.ads = {};
            });
            //获取广告主
            adserSearcher.addFilter({
                field:'adser_username',
                value:wantedAdsers.join(',')
            });
            adserSearcher.filter().then(function(data) {
                $scope.adsers = data;
                $scope.adsers.bookmark = true;
            }, function() {
                $scope.adsers = {};
            });
        });
    }
    function load(type, bid) {
        loadAds(type, bid);
        $location.search('bid', bid);
        $location.search('type', type);
    }
    function init() {
        var search = $location.search();
        if(search.bid) {
           $scope.bookmark.type = Number(search.type);
           loadAds(search.type, search.bid);
        } else {
            if (search.type) 
                $scope.bookmark.type = Number(search.type);
            else
                $scope.bookmark.type = 0;
            if (Bookmark.items.length) {
                loadAds($scope.bookmark.type, Bookmark.items[0].id);
            }
        }

    }
    $scope.Searcher = Searcher;
    $scope.bookmark = Bookmark;
    //$scope.bookmark.type = 0;//当前显示类型
    $scope.load = load;
    $scope.ads = {};//广告列表
    $scope.adsers = {};//广告主列表
    $scope.cancelBookmark = function(type, card) {
        SweetAlert.swal({
            title:'Are you sure?',
            text:'Cancel the ' + type > 0 ? card.adser_username : card.event_id,
            type:'warning',
              showCancelButton: true,   
              // confirmButtonColor: '#DD6B55',   
              confirmButtonText: 'Yes',   
              cancelButtonText: 'Cancel',   
              closeOnConfirm: true,   
              closeOnCancel: true 
        }, function(isConfirm) {
            if (!isConfirm)
                return;
            console.log(Bookmark.subItems);
            angular.forEach(Bookmark.subItems, function(item) {
                if (type != item.type)
                    return;
                if (type === 0 && card.event_id == item.ident) {
                    BookmarkItem.del(item).then(function() {
                        for (var i = 0; i < $scope.ads.ads_info.length; ++i) {
                            if ($scope.ads.ads_info[i] == card) {
                                $scope.ads.ads_info.splice(i, 1);
                                break;
                            }
                        }
                    });
                }
            });
        });
    };
    $scope.$watch('bookmark.type', function(newValue, oldValue) {
        if (newValue != oldValue) {
            $location.search("type", newValue);
        }
    });

    User.getInfo().then(function() {
        if (User.info.login)
            Bookmark.get({uid:User.info.user.id}).then(function() {
                init();    
            });
    });

    
    setTimeout(function() {
        QuickSidebar.init(); // init quick sidebar        
    }, 200);
}]);
