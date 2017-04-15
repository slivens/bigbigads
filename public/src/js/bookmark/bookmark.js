if (!app)
    var app = angular.module('MetronicApp');

app.factory('Bookmark', ['Resource', '$uibModal', 'SweetAlert', 'BookmarkItem', 'User', function(Resource, $uibModal, SweetAlert, BookmarkItem, User) {
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
        //允许编辑，不允许添加
        if (!item && bookmark.items.length >= Number(User.getPolicy("bookmark_list").value)) {
            SweetAlert.swal("You can't create more bookmark list");
            return;
        }
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
            text: 'By clicking Yes you would delete the whole '+item.name+' folder',
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
    bookmark.decSubitemCount = function(bid) {
        for (var i = 0; i < bookmark.items.length; ++i) {
            if (bookmark.items[i].id == bid) {
                bookmark.items[i].item_count--;
                break;
            }
        }
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
            var adSearcher = new Searcher();
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
            $scope.adSearcher = adSearcher;
            $scope.adSearcher.checkAndGetMore = function() {
                adSearcher.getMore();
            };

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
        var cardText = 'Cancel the ' + type > 0 ? card.adser_username : card.event_id;
        var bookFolderText = '<h5>By clicking Yes you would remove the ad from '+ Bookmark.currItem.name +' folder</h5><br>';
        SweetAlert.swal({
            title: 'Are you sure?',
            html: true,
            text: bookFolderText+cardText,
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
            // console.log(Bookmark.subItems);
            angular.forEach(Bookmark.subItems, function(item) {
                if (type != item.type)
                    return;
                if (type === 0 && card.event_id == item.ident) {
                    BookmarkItem.del(item).then(function() {
                        for (var i = 0; i < $scope.data.ads.ads_info.length; ++i) {
                            if ($scope.data.ads.ads_info[i] == card) {
                                $scope.data.ads.ads_info.splice(i, 1);
                                Bookmark.decSubitemCount(Bookmark.bid);
                                break;
                            }
                        }
                    });
                } else if (type === 1 && card.adser_username == item.ident) {
                    BookmarkItem.del(item).then(function() {
                        for (var i = 0; i < $scope.data.adsers.adser.length; ++i) {
                            if ($scope.data.adsers.adser[i] == card) {
                                $scope.data.adsers.adser.splice(i, 1);
                                Bookmark.decSubitemCount(Bookmark.bid);
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

app.controller('BookmarkAddController', ['$scope', 'Bookmark', 'BookmarkItem', 'User', '$q', 'SweetAlert',  function($scope, Bookmark, BookmarkItem, User, $q, SweetAlert) {
    $scope.card.select = [];
    $scope.bookmark = Bookmark;
    $scope.selectedSubItems = [];
    $scope.add = function(card) {
        var promises = [];
        var i, j, noselect = true;
        for (i = 0; i < $scope.card.select.length; i++) {
            if ($scope.card.select[i]) {
                var subItem = {
                    uid: User.info.user.id,
                    bid: Bookmark.items[i].id
                };
                if (card.event_id) {
                    subItem.type = 0;
                    subItem.ident = card.event_id;
                } else if (card.adser_username) {
                    //广告收藏要检查权限
                    if (!User.can('bookmark_adser_support')) {
                        SweetAlert.swal("no permission for adser's bookmark");
                        return false;
                    }
                    subItem.type = 1;
                    subItem.ident = card.adser_username;
                }
                promises.push(BookmarkItem.save(subItem));
                noselect = false;
                // console.log(subItem);
            } else {
                for (j = 0; j < $scope.selectedSubItems.length; ++j) {
                    if ($scope.selectedSubItems[j].bid == Bookmark.items[i].id) {
                        promises.push(BookmarkItem.del($scope.selectedSubItems[j]));
                        // console.log("del", Bookmark.items[j]);
                        break;
                    }
                }
            }
        }
        $scope.addPromise = $q.all(promises);
        $scope.addPromise.finally(function() {
            card.showBookmark = false; //耦合
            if (noselect)
                card.hasBookmark = false;
            else
                card.hasBookmark = true;
        });
    };

    function match() {
        angular.forEach($scope.selectedSubItems, function(subItem, key) {
            var i;
            for (i = 0; i < $scope.bookmark.items.length; ++i) {
                if ($scope.bookmark.items[i].id == subItem.bid) {
                    $scope.card.select[i] = true;
                    break;
                }
            }
        });
    }
    $scope.User = User;
    User.getInfo().then(function() {
        if (User.info.login) {
            var type, ident, promise1, promise2, promises = [];
            if ($scope.card.event_id) {
                type = 0;
                ident = $scope.card.event_id;
            } else if ($scope.card.adser_username) {
                type = 1;
                ident = $scope.card.adser_username;
            }
            if (!Bookmark.queried) {
                promise1 = Bookmark.get({
                    uid: User.info.user.id
                });
                promise1.then(function() {
                    match();
                });
                promises.push(promise1);
            }
            promise2 = BookmarkItem.get({
                where:JSON.stringify([
                    ["ident", "=", ident],
                    ["type", "=", type]
                ])
            });
            promise2.then(function(subItems) {
                $scope.selectedSubItems = subItems;
                match();
            });
            promises.push(promise2);
            $scope.card.queryPromise = $q.all(promises);
        }
    });
}]);
