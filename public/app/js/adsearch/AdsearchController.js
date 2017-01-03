/* Setup blank page controller */
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', '$http', function($rootScope, $scope, settings, $http) {

	$scope.ads = [];
	var defparams = {
		"search_result": "ads",
		"where": [{
			"field": "ads_type",
			"value": "timeline"
		}],
		"limit": [
			0,
			10
		],
		"is_why_all": 1,
		"topN": 10,
		"is_stat": 0
	};
    console.log(settings.imgRemoteBase);
	var searchurl = settings.remoteurl +  '/api/forward/adsearch';
	$http.post(
		searchurl,
		defparams
    ).then(function(res) {
        if (res.data.total_count) {
            $scope.ads = res.data.tl_adsinfo;
            console.log(res.data.total_count);
        }
        console.log(res.data);
    }, function(res) {
        console.log(res);
    });
	$scope.$on('$viewContentLoaded', function() {
		// initialize core components
		App.initAjax();

		// set default layout mode
		$rootScope.settings.layout.pageContentWhite = true;
		$rootScope.settings.layout.pageBodySolid = false;
		$rootScope.settings.layout.pageSidebarClosed = false;

	});
}]);
