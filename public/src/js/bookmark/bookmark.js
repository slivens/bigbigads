if (!app)
    var app = angular.module('MetronicApp');

app.factory('Bookmark', ['Resource', '$uibModal', 'SweetAlert', 'BookmarkItem', function(Resource, $uibModal, SweetAlert, BookmarkItem) {
    var bookmark = new Resource('bookmark');
    bookmark.subItems = [];
    bookmark.editBookmarkByBid = function(bid) {
        var item = null, i;
        for (i = 0; i < bookmark.items.length; ++i) {
            if (bookmark.items[i].id == bid) {
                item = bookmark.items[i];
                break;
            }
        }
        if (item)
            bookmark.addBookmark(item);
    };
    bookmark.addBookmark = function(item) {
        return $uibModal.open({
            templateUrl: 'views/bookmark-add-dialog.html',
            size: 'sm',
            animation: true,
            controller: ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
                if (item)
                    $scope.item = angular.copy(item);
                else
                    $scope.item = {
                        name: ""
                    };
                $scope.bookmark = bookmark;
                $scope.cancel = function() {
                    $uibModalInstance.dismiss('cancel');
                };
                $scope.save = function(item) {
                    $scope.promise = bookmark.save(item);
                    $scope.promise.then(function() {
                        $scope.$emit('bookmarkAdded', item);
                        $uibModalInstance.dismiss('success');
                    });

                };
            }]
        });
    };
    bookmark.delBookmarkByBid = function(bid) {
        var item = null, i;
        for (i = 0; i < bookmark.items.length; ++i) {
            if (bookmark.items[i].id == bid) {
                item = bookmark.items[i];
                break;
            }
        }
        if (item)
            bookmark.delBookmark(item);
    };
    bookmark.delBookmark = function(item) {
        SweetAlert.swal({
            title: 'Are you sure?',
            text: 'All the bookmarks will be removed,too?',
            type: 'warning',
            showCancelButton: true,
            // confirmButtonColor: '#DD6B55',   
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                bookmark.del(item);
            }
        });
    };

    bookmark.showDetail = function(bid) {
        var promise = BookmarkItem.get({
            where: JSON.stringify([
                ["bid", "=", bid]
            ])
        });
        promise.then(function(items) {
            bookmark.subItems = items;
            bookmark.bid = bid;
            for (var i = 0; i < bookmark.items.length; ++i) {
                if (bookmark.items[i].id == bid) {
                    bookmark.currItem = bookmark.items[i];
                    break;
                }
            }
        });
        return promise;
    };
    return bookmark;
}]);
app.factory('BookmarkItem', ['Resource', '$uibModal', 'SweetAlert', function(Resource, $uibModal, SweetAlert) {
    var bookmarkItem = new Resource('BookmarkItem');

    return bookmarkItem;
}]);

app.controller('BookmarkController', ['$scope', 'settings', '$http', 'Resource', '$uibModal', 'User', 'Bookmark', 'BookmarkItem', '$location', 'Searcher', 'SweetAlert', function($scope, settings, $http, Resource, $uibModal, User, Bookmark, BookmarkItem, $location, Searcher, SweetAlert) {
    function loadAds(type, bid) {
        Bookmark.showDetail(bid).then(function(items) {
            var wanted = [];
            var wantedAdsers = [];
            var adSearcher = new Searcher({
                limit: [0, -1]
            });
            var adserSearcher = new Searcher({
                searchType: 'adser',
                url: '/forward/adserSearch',
                limit: [0, -1]
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
            $scope.data.ads = {};
            if (wanted.length > 0) {
                adSearcher.addFilter({
                    field: 'ads_id',
                    value: wanted.join(',')
                });
                adSearcher.filter().then(function(data) {
                    $scope.data.ads = data;
                    $scope.data.ads.bookmark = true;
                    $scope.data.ads.wantedLength = wanted.length;
                    // console.log("ads", 'wanted:', wanted.length, $scope.data.ads);
                }, function() {
                    $scope.data.ads = {};
                });
            }
            $scope.data.adsers = {};
            //获取广告主
            if (wantedAdsers.length > 0) {
                adserSearcher.addFilter({
                    field: 'adser_username',
                    value: wantedAdsers.join(',')
                });
                adserSearcher.filter().then(function(data) {
                    $scope.data.adsers = data;
                    $scope.data.adsers.bookmark = true;
                    $scope.data.adsers.wantedLength = wantedAdsers.length;
                    // console.log("adsers", $scope.data.adsers);
                }, function() {
                    $scope.data.adsers = {};
                });
            }
        });
    }

    function load(type, bid) {
        Bookmark.bid = bid;
        loadAds(type, bid);
        $location.search('bid', bid);
        $location.search('type', type);
    }

    function init() {
        var search = $location.search();
        if (search.bid) {
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
    $scope.User = User;
    //$scope.bookmark.type = 0;//当前显示类型
    $scope.load = load;
    $scope.data = {ads:{}, adsers:{}}; //广告列表
    $scope.cancelBookmark = function(type, card) {
        SweetAlert.swal({
            title: 'Are you sure?',
            text: 'Cancel the ' + type > 0 ? card.adser_username : card.event_id,
            type: 'warning',
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
                        for (var i = 0; i < $scope.data.ads.ads_info.length; ++i) {
                            if ($scope.data.ads.ads_info[i] == card) {
                                $scope.data.ads.ads_info.splice(i, 1);
                                break;
                            }
                        }
                    });
                } else if (type === 1 && card.adser_username == item.ident) {
                    BookmarkItem.del(item).then(function() {
                        for (var i = 0; i < $scope.data.adsers.adser.length; ++i) {
                            if ($scope.data.adsers.adser[i] == card) {
                                $scope.data.adsers.adser.splice(i, 1);
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
            Bookmark.get({
                uid: User.info.user.id
            }).then(function() {
                init();
            });
    });


    setTimeout(function() {
        QuickSidebar.init(); // init quick sidebar        
    }, 200);
}]);

app.controller('BookmarkAddController', ['$scope', 'Bookmark', 'BookmarkItem', 'User', '$q', function($scope, Bookmark, BookmarkItem, User, $q) {
    $scope.select = [];
    $scope.bookmark = Bookmark;
    $scope.add = function(card) {
        var promises = [];
        for (var i = 0; i < $scope.select.length; i++) {
            if ($scope.select[i]) {
                var subItem = {
                    uid: User.info.user.id,
                    bid: Bookmark.items[i].id
                };
                if (card.event_id) {
                    subItem.type = 0;
                    subItem.ident = card.event_id;
                } else if (card.adser_username) {
                    subItem.type = 1;
                    subItem.ident = card.adser_username;
                }
                promises.push(BookmarkItem.save(subItem));
                console.log(subItem);
            }
        }
        $scope.addPromise = $q.all(promises);
        $scope.addPromise.finally(function() {
            card.showBookmark = false; //耦合
        });
    };
    if (!Bookmark.queried) {
        User.getInfo().then(function() {
            if (User.info.login) {
                $scope.queryPromise = Bookmark.get({
                    uid: User.info.user.id
                });
            }
        });
    }
}]);
