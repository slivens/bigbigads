import template from './bookmark-popover.html'

angular.module('MetronicApp').controller('BookmarkAddController', ['$scope', 'Bookmark', 'BookmarkItem', 'User', '$q', 'SweetAlert', function($scope, Bookmark, BookmarkItem, User, $q, SweetAlert) {
    $scope.card.select = []
    $scope.bookmark = Bookmark
    $scope.selectedSubItems = []
    $scope.add = function(card) {
        var promises = []
        var i
        var j
        var noselect = true
        for (i = 0; i < $scope.card.select.length; i++) {
            if ($scope.card.select[i]) {
                var subItem = {
                    uid: User.info.user.id,
                    bid: Bookmark.items[i].id
                }
                if (card.event_id) {
                    subItem.type = 0
                    subItem.ident = card.event_id
                } else if (card.adser_username) {
                    // 广告收藏要检查权限
                    if (!User.can('bookmark_adser_support')) {
                        SweetAlert.swal("no permission for adser's bookmark")
                        return false
                    }
                    subItem.type = 1
                    subItem.ident = card.adser_username
                }
                promises.push(BookmarkItem.save(subItem))
                noselect = false
                // console.log(subItem);
            } else {
                for (j = 0; j < $scope.selectedSubItems.length; ++j) {
                    if ($scope.selectedSubItems[j].bid == Bookmark.items[i].id) {
                        promises.push(BookmarkItem.del($scope.selectedSubItems[j]))
                        // console.log("del", Bookmark.items[j]);
                        break
                    }
                }
            }
        }
        $scope.addPromise = $q.all(promises)
        $scope.addPromise.then(function() {}, function() {
            // 返回错误或者已经达到收藏最大数量,收藏按钮不变黑。
            noselect = true
        }).finally(function() {
            card.showBookmark = false // 耦合
            if (noselect)
                card.hasBookmark = false
            else
                card.hasBookmark = true
        })
    }

    function match() {
        angular.forEach($scope.selectedSubItems, function(subItem, key) {
            var i
            for (i = 0; i < $scope.bookmark.items.length; ++i) {
                if ($scope.bookmark.items[i].id == subItem.bid) {
                    $scope.card.select[i] = true
                    break
                }
            }
        })
    }
    $scope.User = User
    User.getInfo().then(function() {
        if (User.info.login) {
            var type
            var ident
            var promise1
            var promise2
            var promises = []
            if ($scope.card.event_id) {
                type = 0
                ident = $scope.card.event_id
            } else if ($scope.card.adser_username) {
                type = 1
                ident = $scope.card.adser_username
            }
            if (!Bookmark.queried) {
                promise1 = Bookmark.get({
                    uid: User.info.user.id
                })
                promise1.then(function() {
                    match()
                })
                promises.push(promise1)
            }
            promise2 = BookmarkItem.get({
                where: JSON.stringify([
                    ["ident", "=", ident],
                    ["type", "=", type]
                ])
            })
            promise2.then(function(subItems) {
                $scope.selectedSubItems = subItems
                match()
            })
            promises.push(promise2)
            $scope.card.queryPromise = $q.all(promises)
        }
    })
}])
    .directive('bookmarkPopover', ['$templateCache', function($templateCache) {
        return {
            restrict: 'E',
            template,
            replace: false,
            controller: 'BookmarkAddController'
        }
    }])
