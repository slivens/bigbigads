/* Setup blank page controller */
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', '$http', 'ADS_TYPE',
    function($rootScope, $scope, settings, $http, ADS_TYPE) {

	$scope.ads = [];
    $scope.searchOptions = {};
    $scope.ADS_TYPE = ADS_TYPE;
	var defparams = {
		"search_result": "ads",
		"where": [{
			"field": "ads_type",
			"value": "timeline"
		}],
		"limit": [
			0,
			20
		],
		"is_why_all": 1,
		"topN": 10,
		"is_stat": 0
	};
    //获取广告搜索信息
	var searchurl = settings.remoteurl +  '/api/forward/adsearch';
	$http.post(
		searchurl,
		defparams
    ).then(function(res) {
        if (res.data.total_count) {
            $scope.ads = res.data;
            console.log(res.data.total_count);
        }
        console.log(res.data);
    }, function(res) {
        console.log(res);
    });
    $scope.getAdsType = function(item, type) {
        // console.log(type, item.show_way, item.show_way & type);
        if (item.show_way & type)
            return true;
        return false;
    };

	$scope.$on('$viewContentLoaded', function() {
		// initialize core components
		App.initAjax();

		// set default layout mode
		$rootScope.settings.layout.pageContentWhite = true;
		$rootScope.settings.layout.pageBodySolid = false;
		$rootScope.settings.layout.pageSidebarClosed = false;

	});
}]);
