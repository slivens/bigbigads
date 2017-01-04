/* Setup blank page controller */
angular.module('MetronicApp').factory('Searcher', ['$http', '$timeout', 'settings', 'ADS_TYPE', 'ADS_CONT_TYPE', 
	function($http, $timeout, settings, ADS_TYPE, ADS_CONT_TYPE) {
		var searcher = function() {
			var vm = this;
			vm.defparams = {
				"search_result": "ads",
				"sort": {
					"field": "last_view_date",
					"order": 1
				},
                "where":[ 
                ],
				"limit": [
					0,
					10
				],
				"is_why_all": 1,
				"topN": 10,
				"is_stat": 0
			};
			vm.params = angular.copy(vm.defparams);
            vm.oldParams = null;
			vm.ADS_TYPE = ADS_TYPE;
            vm.ADS_CONT_TYPE = ADS_CONT_TYPE;
			vm.pageCount = settings.searchSetting.pageCount;
			vm.ads = {
				total_count: 0
			};
            vm.isend = false;

			vm.getAdsType = function(item, type) {
				// console.log(type, item.show_way, item.show_way & type);
				if (item.show_way & type)
					return true;
				return false;
			};
			vm.search = function(params, clear) {
				//获取广告搜索信息
				var searchurl = settings.remoteurl + '/api/forward/adsearch';
				vm.busy = true;
				$http.post(
					searchurl,
					params
				).then(function(res) {
                    vm.isend = res.data.is_end;
					if (res.data.count) {
                        angular.forEach(res.data.ads_info, function(value, key) {
                            if (value.type == vm.ADS_CONT_TYPE.CAROUSEL) {
                                value.watermark = JSON.parse(value.watermark);
                                console.log('wa', value.watermark);
                            }
                        });
						if (clear) {
							vm.ads = res.data;
						} else {
							vm.ads.ads_info = vm.ads.ads_info.concat(res.data.ads_info);
						}
					}
					console.log(res.data);
					$timeout(function() {
						vm.busy = false;
					}, 500);

				}, function(res) {
					vm.busy = false;
					// console.log(res);
				});
			};

			vm.getMore = function() {
				if (vm.busy)
					return;
				vm.params.limit[0] += settings.searchSetting.pageCount;
				vm.search(vm.params, false);
				console.log("read more", vm.params.limit);
			};
            vm.filter = function() {
                if (vm.params == vm.oldParams)
                    return;
                vm.params.limit[0] = 0;
                vm.search(vm.params, true);
                vm.oldParams = angular.copy(vm.params);
            };
		};
		return searcher;
	}
]);
angular.module('MetronicApp').controller('AdsearchController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher',
	function($rootScope, $scope, settings, $http, Searcher) {
		$scope.adSearcher = new Searcher();
		$scope.adSearcher.search($scope.adSearcher.defparams, true);
        $scope.reverseSort = function() {
            $scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
            $scope.adSearcher.filter();
        };
        $scope.adSearcher.searchOption = {
            text:'',
            type:''
        };
        $scope.search = function() {
            var i, finded = false;
            var option = $scope.adSearcher.searchOption;
            if (!$.trim(option.text)) {
                $scope.adSearcher.params.keys = [];
            } else {
                $scope.adSearcher.params.keys = [
                    {
                        string:option.text,
                        search_fields:"message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink",
                        relation:"Must"
                    }
                ];
            }
            //where必须带ads_type，否则会出错
            for (i = 0;i < $scope.adSearcher.params.where.count; ++i) {
                if($scope.adSearcher.params.where[i].field == "ads_type") {
                    finded = true;
                    break;
                }
            }
            if (i == $scope.adSearcher.params.where.length) {
                $scope.adSearcher.params.where.push({field:'ads_type', value:$scope.adSearcher.searchOption.type});
            }
            $scope.adSearcher.params.where[i].value = $scope.adSearcher.searchOption.type;
            if ($.trim($scope.adSearcher.searchOption.type) === "")
                $scope.adSearcher.params.where.splice(i, 1);

            $scope.adSearcher.filter();
            console.log($scope.adSearcher.params);
        };

		$scope.$on('$viewContentLoaded', function() {
			// initialize core components
			App.initAjax();

			// set default layout mode
			$rootScope.settings.layout.pageContentWhite = true;
			$rootScope.settings.layout.pageBodySolid = false;
			$rootScope.settings.layout.pageSidebarClosed = false;

		});
	}
]);
