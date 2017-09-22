angular.module('MetronicApp').controller('RankingController', ['$scope', 'settings', '$http', 'SweetAlert', '$location', 'User', function($scope, settings, $http, SweetAlert, $location, User) {
    var ranking = {
        active: 0,
        sort: {field: null, reverse: true},
        adsers: [],
        ads: [],
        keywords: [],
        categoryList: angular.copy(settings.searchSetting.categoryList),
        activedTabs: [false, false, false],
        getTopAdsers: function(category) {
            var rankurl = settings.remoteurl + '/ranking'
            var params = null
            if (category) {
                params = {category: category}
            }
            console.log(params)

            $http.get(rankurl, {params: params}).then(function(res) {
                ranking.adsers = res.data
                angular.forEach(ranking.adsers, function(item) {
                    if (item.page_website && !item.page_website.match("^http")) {
                        item.page_website = "http://" + item.page_website
                    }
                })
            }, function(res) {
                if (res.data instanceof Object) {
                    SweetAlert.swal(res.data.desc)
                } else {
                    SweetAlert.swal(res.statusText)
                }
            })
        },
        initActiveData: function() {
            var active = ranking.active
            if (ranking.activedTabs[active])
                return
            switch (active) {
            case 0:
                ranking.getTopAdsers(ranking.category)
                break
            case 1:
                break
            case 2:
                break
            }
            ranking.activedTabs[active] = true
        }
    }

    function init() {
        // 防止未登录进入广告主排名界面
        User.getInfo().then(function() {
            if (!User.login) {
                window.open('/login', "_self")
            }
        })
        var search = $location.search()
        if (search.active)
            ranking.active = Number(search.active)
        if (search.category)
            ranking.category = search.category
        ranking.initActiveData()
    }

    init()
    $scope.settings = settings
    $scope.ranking = ranking
    $scope.changeRanking = function() {
        ranking.getTopAdsers(ranking.category)
        $location.search('category', ranking.category)
    }
    $scope.clear = function() {
        ranking.category = ""
        $scope.changeRanking()
    }
    $scope.$watch('ranking.active', function(newValue, oldValue) {
        if (newValue != oldValue) {
            $location.search("active", newValue)
            ranking.initActiveData()
        }
    })
}])
