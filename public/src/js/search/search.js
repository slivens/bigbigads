if (!app)
	var app = angular.module('MetronicApp');

app.factory('Searcher', ['$http', '$timeout', 'settings', 'ADS_TYPE', 'ADS_CONT_TYPE', '$q', 'Util', 
	function($http, $timeout, settings, ADS_TYPE, ADS_CONT_TYPE, $q, Util) {
		//opt = {searchType:'adser', url:'/forward/adserSearch'}
		var searcher = function(opt) {
			var vm = this;
			vm.opt = opt;
			vm.defparams = {
				"search_result": opt && opt.searchType ? opt.searchType : "ads",
				"sort": {
					"field": "last_view_date",
					"order": 1
				},
				"where": [],
				"limit": [
					0,
					10
				],
				"is_why_all": 1,
				"topN": 10,
				"is_stat": 0
			};
			if (opt && opt.limit) {
				vm.defparams.limit = opt.limit;
			}
			searcher.defSearchFields = searcher.prototype.defSearchFields = "message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink";
			searcher.defFilterOption = searcher.prototype.defFilterOption = {
				type: "",
				date: {
					startDate: null,
					endDate: null
				},
				category: settings.searchSetting.categoryList,
				format: settings.searchSetting.formatList,
				buttondesc: settings.searchSetting.buttondescList,
				duration: {
					from: settings.searchSetting.durationRange[0],
					to: settings.searchSetting.durationRange[1]
				},
				seeTimes: {
					from: settings.searchSetting.seeTimesRange[0],
					to: settings.searchSetting.seeTimesRange[1]
				},
				//engagementsFilter
				engagements:{
					likes:{
						min:"",	
						max:""
					},
					shares:{
						min:"",	
						max:""
					},
					comments:{
						min:"",	
						max:""
					},
					views:{
						min:"",	
						max:""
					},
					engagements:{
						min:"",	
						max:""
					}

				},
				isEngagementsDirty: function() {
					if (this.engagements.likes.min == searcher.defFilterOption.engagements.likes.min && 
						this.engagements.likes.max == searcher.defFilterOption.engagements.likes.max &&
						this.engagements.shares.min == searcher.defFilterOption.engagements.shares.min &&
						this.engagements.shares.max == searcher.defFilterOption.engagements.shares.max &&
						this.engagements.comments.min == searcher.defFilterOption.engagements.comments.min &&
						this.engagements.comments.max == searcher.defFilterOption.engagements.comments.max &&
						this.engagements.views.min == searcher.defFilterOption.engagements.views.min &&
						this.engagements.views.max == searcher.defFilterOption.engagements.views.max &&
						this.engagements.engagements.min == searcher.defFilterOption.engagements.engagements.min &&
						this.engagements.engagements.max == searcher.defFilterOption.engagements.engagements.max){
						return false;
					}
					return true;
				},
				isDurationDirty: function() {
					//这里引用this,关键是要对this这个指针有透彻的理解，this是会变的。当函数作为对象的属性时，this就是对象，即当以obj.isDurationDirty()，this就是obj。如果fn=obj.isDurationDirty();fn();那么this就是window。当函数出问题时一定要检查下this。
					if (this.duration.from == searcher.defFilterOption.duration.from &&
						this.duration.to == searcher.defFilterOption.duration.to)
						return false;
					return true;
				},
				isSeeTimesDirty: function() {
					if (this.seeTimes.from == searcher.defFilterOption.seeTimes.from &&
						this.seeTimes.to == searcher.defFilterOption.seeTimes.to)
						return false;
					return true;
				}

			};
			vm.defSearchOption = {
				range: settings.searchSetting.rangeList,
				search: {
					text: null,
					fields: searcher.defSearchFields
				},
				domain: {
					text: null,
					exclude: false
				},
				audience: {
					text: null,
					exclude: false
				},
				filter: searcher.defFilterOption
			};
			vm.params = angular.copy(vm.defparams);
			vm.oldParams = null;
			vm.ADS_CONT_TYPE = ADS_CONT_TYPE;
			vm.pageCount = settings.searchSetting.pageCount;
			vm.ads = {
				total_count: 0
			};
			vm.isend = false;
			vm.isNoResult = false;
			vm.search = function(params, clear, action) {
				var defer = $q.defer();
				//获取广告搜索信息
				var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/forward/adsearch');
				if (action) {
					params.action = action;
				} else {
					delete params.action;
				}
				vm.busy = true;
				$http.post(
					searchurl,
					params
				).then(function(res) {
                    if (res.error) {
						defer.reject(res);
                        return;
                    }
					vm.isend = vm.isNoResult = res.data.is_end;
					if (clear && vm.isend) { //检测到结束就清空搜索结果
						vm.ads = [];
						vm.ads.total_count = 0;
					}
					if (res.data.count) {
						angular.forEach(res.data.ads_info, function(value, key) {
							if (value.type == vm.ADS_CONT_TYPE.CAROUSEL) {
								value.watermark = JSON.parse(value.watermark);
								value.link = JSON.parse(value.link);
								value.buttonlink = JSON.parse(value.buttonlink);
								value.buttondesc = JSON.parse(value.buttondesc);
								value.name = JSON.parse(value.name);
								value.description = JSON.parse(value.description);
								value.local_picture = JSON.parse(value.local_picture);
								// if (value.snapshot && value.snapshot != "")
								//      value.snapshot = JSON.parse(value.snapshot);
							} else if (value.type == vm.ADS_CONT_TYPE.CANVAS) {
								value.link = JSON.parse(value.link);
								value.local_picture = JSON.parse(value.local_picture);
								if (vm.getAdsType(value, vm.ADS_TYPE.rightcolumn)) {
									value.watermark = JSON.parse(value.watermark);
								}
							} else if (value.type == vm.ADS_CONT_TYPE.SINGLE_VIDEO) {
								value.local_picture = JSON.parse(value.local_picture);
							}
						});

						if (opt && opt.searchType == "adser") {
							if (clear) {
								vm.ads = res.data;
							} else {
								vm.ads.adser = vm.ads.adser.concat(res.data.adser);
							}
						} else {
							if (clear || vm.ads.total_count === 0) {
								vm.ads = res.data;
							} else {
								vm.ads.ads_info = vm.ads.ads_info.concat(res.data.ads_info);
							}
						}
						defer.resolve(vm.ads);
					} else {
						defer.reject(vm.ads);
					}
					// console.log(res.data);
				}, function(res) {
					defer.reject(res);
					// console.log(res);
				}).finally(function() {
					$timeout(function() {
						vm.busy = false;
					}, 200);
				});
				return defer.promise;
			};

			vm.getMore = function() {
				if (vm.busy)
					return;
				vm.params.limit[0] += settings.searchSetting.pageCount;
				vm.search(vm.params, false);
			};
			vm.filter = function(action) {
				var promise;
				if (vm.params == vm.oldParams)
					return;
				vm.params.limit[0] = 0;
				promise = vm.search(vm.params, true, action);
				vm.oldParams = angular.copy(vm.params);
				return promise;
			};
			vm.addFilter = function(obj) {
				var i;
				var finded = false;
				//where必须带ads_type，否则会出错
				for (i = 0; i < vm.params.where.length; ++i) {
					if (vm.params.where[i].field == obj.field) {
						finded = true;
						break;
					}
				}
				if (i == vm.params.where.length) {
					vm.params.where.push(obj);
				} else {
					vm.params.where[i] = obj;
				}
			};
			vm.removeFilter = function(name) {
				var i;
				//where必须带ads_type，否则会出错
				for (i = 0; i < vm.params.where.length; ++i) {
					if (vm.params.where[i].field == name) {
						vm.params.where.splice(i, 1);
						break;
					}
				}
			};
			vm.getStatics = function(params, action) {
				var searchurl = settings.remoteurl + (opt && opt.url ? opt.url : '/forward/adsearch');
				var promise;
				if (action) {
					params.action = action;
				} else {
					delete params.action;
				}
				params.is_stat = 1;
				promise = $http.post(searchurl, params);
				promise.then(function(res) {
					console.log(res);
				});
				return promise;
			};


		};
		searcher.ADS_TYPE = searcher.prototype.ADS_TYPE = ADS_TYPE;
		//函数的静态方法以及对象的方法
		searcher.getAdsType = searcher.prototype.getAdsType = function(item, type) {
			if (item.show_way & type)
				return true;
			return false;
		};
        //将搜索过滤项转换成location的参数
        searcher.searchToQuery = searcher.prototype.searchToQuery = function(option) {
				var query = {};
				if (option.search.text)
					query.searchText = option.search.text;
				if (angular.isDefined(option.rangeselected) && option.rangeselected.length) {
					query.searchFields = option.rangeselected.join(',');
				}
				//if (option.search.fields != searcher.defSearchFields)
				//	query.searchFields = option.search.fields;
				if (option.filter.date.startDate && option.filter.date.endDate) {
					query.startDate = option.filter.date.startDate.format('YYYY-MM-DD');
					query.endDate = option.filter.date.endDate.format('YYYY-MM-DD');
				}
				if (option.filter.type) {
					query.type = option.filter.type;
				}
				if (angular.isDefined(option.filter.lang) && option.filter.lang.length) {
					query.lang = option.filter.lang.join(',');
				}
				if (option.filter.state) {
					query.state = option.filter.state;
				}
				if (option.domain.text) {
					query.domain = JSON.stringify(option.domain);
				}
				if (option.audience.text) {
					query.audience = JSON.stringify(option.audience);
				}

				//category, format, buttondesc,engagement
				if (option.filter.categoryString) {
					query.category = option.filter.categoryString;
				}
				if (option.filter.formatString) {
					query.format = option.filter.formatString;
				}
				if (option.filter.buttondescString) {
					query.buttondesc = option.filter.buttondescString;
				}
				if (option.filter.isEngagementsDirty()) {
					query.engagements = JSON.stringify(option.filter.engagements);
				}
				if (option.filter.isDurationDirty()) {
					query.duration = JSON.stringify(option.filter.duration);
				}
				if (option.filter.isSeeTimesDirty()) {
					query.seeTimes = JSON.stringify(option.filter.seeTimes);
				}
                return query;
        };
        //将location的参数转换成搜索过滤项
        searcher.queryToSearch = searcher.prototype.queryToSearch = function(locaionSearch, option) {
				var i;
                var search = locaionSearch;
                option.rangeselected =[];
				if (search.searchText) {
					option.search.text = search.searchText;
				}
				if (search.searchFields && search.searchFields != searcher.defSearchFields) {
					var range = search.searchFields.split(',');
					angular.forEach(range, function(item1) {
						for (i = 0; i < option.range.length; i++) {
							if (option.range[i].key.indexOf(item1)>-1 && option.rangeselected.indexOf(option.range[i].key)==-1)
									option.rangeselected.push(option.range[i].key);//原始range.key是多个单词组合而成
								
						}
					});
				}
				if (search.startDate && search.endDate) {
					option.filter.date.startDate = moment(search.startDate, 'YYYY-MM-DD');
					option.filter.date.endDate = moment(search.endDate, 'YYYY-MM-DD');
				}
				if (search.type) {
					option.filter.type = search.type;
				}
				if (search.lang) {
					option.filter.lang = search.lang.split(",");
				}
				if (search.state) {
					option.filter.state = search.state;
				}
				if (search.domain) {
					option.domain = JSON.parse(search.domain);
				}
				if (search.audience) {
					option.audience = JSON.parse(search.audience);
				}
				if (search.category) {
					Util.matchkey(search.category, option.filter.category);
				}
				if (search.format) {
					option.filter.formatSelected = search.format.split(",");
					//Util.matchkey(search.format, option.filter.format);
				}
				if (search.buttondesc) {
					option.filter.callToAction=search.buttondesc.split(",");
					//Util.matchkey(search.buttondesc, option.filter.buttondesc);
				}
				if (search.engagements) {
					option.filter.engagements = JSON.parse(search.engagements);
				}
				if (search.duration) {
					option.filter.duration = JSON.parse(search.duration);
				}
				if (search.seeTimes) {
					option.filter.seeTimes = JSON.parse(search.seeTimes);
				}
        };
		return searcher;
	}
]);
/* adsearch js */
app.controller('AdsearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams', 'User', 'ADS_TYPE', '$uibModal', 'FreeLimit',
		function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams, User, ADS_TYPE, $uibModal, FreeLimit) {
			//搜索流程:location.search->searchOption->adSearcher.params
			//将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化

            function searchToQuery(option, searcher) {
                $location.search(searcher.searchToQuery(option));
            }
			//将query转化成搜索参数
            function queryToSearch(option, searcher) {
                searcher.queryToSearch($location.search(), option);
            }
			// $scope.swal = function(msg) {
			//     SweetAlert.swal(msg);
			// };
			var adSearcher = $scope.adSearcher = new Searcher();
			adSearcher.checkAndGetMore = function() {
				if (!User.done) {
					adSearcher.getMore();
					return;
				}
				var policy = User.getPolicy('result_per_search');
				if (!policy) {
					SweetAlert.swal("no permission for get more");
					adSearcher.isend = true;
					return;
				}
				// console.log("max search result:", policy.value, adSearcher.params.limit[0] + adSearcher.params.limit[1]);
				if (adSearcher.params.limit[0] + adSearcher.params.limit[1] >= policy.value) {
					//SweetAlert.swal("you reached search result limit(" + policy.value + ")");
					adSearcher.isend = true;
					return;
				}
				adSearcher.getMore();
			};
			// $scope.adSearcher.search($scope.adSearcher.defparams, true);
			$scope.reverseSort = function() {
				if (!User.can('search_sortby')) {
					SweetAlert.swal("no sort permission");
					return;
				}
				$scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
				$scope.adSearcher.filter();

			};

			//text为空时就表示没有这个搜索项了
			$scope.initSearch = function() {
				var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption);
				$scope.filterOption = $scope.searchOption.filter;

				//存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
				queryToSearch(option, $scope.adSearcher);
			};
			$scope.initSearch();

			$scope.currSearchOption = {};

			$scope.filter = function(option, action) {
				var category = [],
					format = [],
					formatList='',
					buttondesc = [];

				//广告类型
				if (!$scope.filterOption.type) {
					$scope.adSearcher.removeFilter("ads_type");
				} else {
					$scope.adSearcher.addFilter({
						field: 'ads_type',
						value: $scope.filterOption.type
					});
				}
				//日期范围
				if (!option.date.startDate || !option.date.endDate) {
					$scope.adSearcher.removeFilter('time');
				} else {
					var startDate = option.date.startDate.format('YYYY-MM-DD');
					var endDate = option.date.endDate.format('YYYY-MM-DD');
					$scope.adSearcher.addFilter({
						field: "time",
						min: startDate,
						max: endDate
					});
				}
				//语言
				if (option.lang && option.lang.length) {
					$scope.adSearcher.addFilter({
						field: 'ad_lang',
						value: option.lang.join(',')
					});
					$scope.currSearchOption.filter.lang = option.lang.join(',');
				} else {
					$scope.adSearcher.removeFilter('ad_lang');
				}
				//国家
				if (option.state) {
					$scope.adSearcher.addFilter({
						field: 'state',
						value: option.state
					});
				} else {
					$scope.adSearcher.removeFilter('state');
				}

				//支持多项搜索，以","隔开
				angular.forEach($scope.filterOption.category, function(item, key) {
					if (item.selected) {
						category.push(item.key);
					}
				});
				$scope.filterOption.categoryString = category.join(',');

				if (category.length) {
					$scope.adSearcher.addFilter({
						field: 'category',
						value: category.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('category');
				}

				//format by select2 multiple
				angular.forEach($scope.filterOption.formatSelected, function(item) {
						format.push(item);
				});
				$scope.filterOption.formatString = format.join(',');
				if (format.length) {
					$scope.adSearcher.addFilter({
						field: 'media_type',
						value: format.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('media_type');
				}

				//Call To Action
				angular.forEach($scope.filterOption.callToAction, function(item) {
						buttondesc.push(item);
				});
				option.buttondescString = buttondesc.join(',');
				if (buttondesc.length) {
					$scope.adSearcher.addFilter({
						field: 'buttondesc',
						value: buttondesc.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('buttondesc');
				}

				//Duration Filter
				if (!option.isDurationDirty()) {
					$scope.adSearcher.removeFilter('duration_days');
				} else {
					$scope.adSearcher.addFilter({
						field: 'duration_days',
						min: option.duration.from,
						max: option.duration.to
					});
				}

				//see times Filter
				if (!option.isSeeTimesDirty()) {
					$scope.adSearcher.removeFilter('see_times');
				} else {
					$scope.adSearcher.addFilter({
						field: 'see_times',
						min: option.seeTimes.from,
						max: option.seeTimes.to
					});
				}

				//engagementsFilter
				angular.forEach(option.engagements,function(item,key){
					if (item.min==="" && item.max==="") {
						$scope.adSearcher.removeFilter(key);
					}else{
						$scope.adSearcher.addFilter({
						field: key,
						min: item.min,
						max: item.max
					});
					}
				}); 
				$scope.currSearchOption.filter.category = category.join(',');
				$scope.currSearchOption.filter.format = format.join(',');
				$scope.currSearchOption.filter.callToAction = buttondesc.join(',');
				$scope.adSearcher.filter(action ? action : 'search').then(function() {}, function(res) {
					if (res.data instanceof Object) {
						SweetAlert.swal(res.data.desc);
					} else {
						SweetAlert.swal(res.statusText);
					}
				});
				console.log("params", $scope.adSearcher.params);
			};

			$scope.search = function(action) {
				var i;
				var option = $scope.adSearcher.searchOption;
				var keys;
				var range = [];
				var range_value=[];//用于显示
				keys = $scope.adSearcher.params.keys = [];
				//console.log($scope.searchOption.rangeselected);

				//检查权限，并且应该集中检查权限，才不会搞得逻辑混乱或者状态不一致
				if (!User.can('result_per_search')) {
					SweetAlert.swal("no search permission");
					return;
				}
                //2017-03-09 Liuwc:设计变更，过滤权限分开，同时不检查搜索类型
				// if (action == 'filter' && !User.can('search_filter')) {
				// 	SweetAlert.swal("no filter permission");
				// 	return;
				// }
				// if ($scope.filterOption.type) {
				// 	var type = ADS_TYPE[$scope.filterOption.type];
				// 	if (!(Number(User.getPolicy('platform').value) & type)) {
				// 		SweetAlert.swal("type '" + $scope.filterOption.type + "' exceed your permission");
				// 		return;
				// 	}
				// }
				//字符串和域
				$scope.currSearchOption = angular.copy($scope.searchOption); //保存搜索
				if (option.rangeselected && option.rangeselected.length) {
					angular.forEach(option.rangeselected, function(item) {
							range.push(item);
					});
				}
				if (option.search.text || range.length) {
					option.search.fields = range.length ? range.join(',') : $scope.Searcher.defSearchFields;//默认值
					keys.push({
						string: option.search.text,
						search_fields: option.search.fields,
						relation: "Must"
					});
					//alert-warning range显示文本
					angular.forEach(option.range,function(item){
						if (range.indexOf(item.key)>-1)range_value.push(item.value);
					});

				}
				/*if (option.rangeselected && option.search.text) {
					angular.forEach(option.rangeselected, function(item) {
							range.push(item);
					});
					option.search.fields = range.length ? range.join(',') : option.search.fields;
					keys.push({
						string: option.search.text,
						search_fields: option.search.fields,
						relation: "Must"
					});
				}*/
				//域名
				if (option.domain.text) {
					keys.push({
						string: option.domain.text,
						search_fields: 'caption,link,dest_site,buttonlink',
						relation: option.domain.exclude ? 'Not' : 'Must'
					});
				}
				//受众
				if (option.audience.text) {
					keys.push({
						string: option.audience.text,
						search_fields: 'whyseeads,whyseeads_all',
						relation: option.audience.exclude ? 'Not' : 'Must'
					});
				}
				$scope.currSearchOption.range = range_value.join(',');
				$scope.filter($scope.filterOption, action);
				if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 0) {
					$scope.currSearchOption.isdirty = true;
				}
				searchToQuery(option, $scope.adSearcher);
			};

			$scope.clearSearch = function() {
				$location.search({});
				$state.reload();
			};
			$scope.showStatics = function() {
                if (!User.can('statics_all')) {
					SweetAlert.swal("you have no permission");
                    return;
                }

				if (adSearcher.params.keys.length === 0) {
					SweetAlert.swal("you must search first");
					return;
				}
				return $uibModal.open({
					templateUrl: 'statics-dlg.html',
					size: 'lg',
					animation: true,
					controller: ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {
						//使用独立的搜索器，否则可能影响到原来的广告搜索结果
						var seacher = new Searcher();
						seacher.params = angular.copy(adSearcher.params);
						$scope.statics = {};
						$scope.queryPromise = seacher.getStatics(seacher.params, "statics");
						$scope.queryPromise.then(function(res) {
							var data = $scope.statics = res.data;
							//饼图
							$scope.statics.adLangConfig = Util.initPie(data.ad_lang, "AD Language");
							$scope.statics.adserNameConfig = Util.initPie(data.adser_name, "Adser Names");
							$scope.statics.adserUsernameConfig = Util.initPie(data.adser_username, "Adser Usernames");
							$scope.statics.categoryConfig = Util.initPie(data.category, "Category");
							$scope.statics.mediaTypeConfig = Util.initPie(data.media_type, "Media Type");

							//button_link, dest_site,link,whyseeads太长，怎么处理？
							// console.log(res);
                        }, function(res) {
                            $uibModalInstance.dismiss("cancel");
                            Util.hint(res);
                        });
						$scope.close = function() {
							$uibModalInstance.dismiss('cancel');
						};

					}]

				});
			};
			/*$scope.islegel = true;*/
			$scope.searchCheck = function(value) {
				var islegel = true;
				var numberLimit;
				var lengthLimit;
				numberLimit = FreeLimit.numberLimit(value);
				lengthLimit = FreeLimit.lengthLimit(value);
				if(numberLimit instanceof Object) numberLimit = false;
				if(numberLimit && lengthLimit){
					islegel=true;
				}else{
					islegel=false;
				}
				$scope.islegel = islegel;
			};

            $scope.Util = Util;
			$scope.User = User;
			$scope.Searcher = Searcher;
			//一切的操作应该是在获取到用户信息之后，后面应该优化直接从本地缓存读取
			User.getInfo().then(function() {
				//根据search参数页面初始化
				$scope.search();
			});
			$scope.$on('$viewContentLoaded', function() {
				// initialize core components
				App.initAjax();

				// set default layout mode
				$rootScope.settings.layout.pageContentWhite = true;
				$rootScope.settings.layout.pageBodySolid = false;
				$rootScope.settings.layout.pageSidebarClosed = false;
			});


		}
	])
	.controller('AdserController', ['$rootScope', '$scope', 'settings', '$http', 'Searcher', '$filter', 'SweetAlert', '$state', 'Util', '$stateParams', 'User', '$location', function($rootScope, $scope, settings, $http, Searcher, $filter, SweetAlert, $state, Util, $stateParams, User, $location) {
        //搜索流程:location.search->searchOption->adSearcher.params
        //将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
        function searchToQuery(option, searcher) {
            $location.search(searcher.searchToQuery(option));
        }
        //将query转化成搜索参数
        function queryToSearch(option, searcher) {
            searcher.queryToSearch($location.search(), option);
        }
		$scope.adser = {
			name: $stateParams.name,
			username: $stateParams.adser
		};
		$scope.adSearcher = new Searcher();
		$scope.adSearcher.checkAndGetMore = function() {
				if (!User.done) {
					adSearcher.getMore();
					return;
				}
				var policy = User.getPolicy('result_per_search');
				if (!policy) {
					SweetAlert.swal("no permission for get more");
					adSearcher.isend = true;
					return;
				}
				// console.log("max search result:", policy.value, adSearcher.params.limit[0] + adSearcher.params.limit[1]);
				if (adSearcher.params.limit[0] + adSearcher.params.limit[1] >= policy.value) {
					//SweetAlert.swal("you reached search result limit(" + policy.value + ")");
					adSearcher.isend = true;
					return;
				}
				adSearcher.getMore();
			};
		// $scope.adSearcher.search($scope.adSearcher.defparams, true);
		$scope.reverseSort = function() {
            if (!User.can('search_sortby')) {
                SweetAlert.swal("no sort permission");
                return;
            }
			$scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
			$scope.adSearcher.filter();
		};

        //text为空时就表示没有这个搜索项了
        $scope.initSearch = function() {
            var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption);
            $scope.filterOption = $scope.searchOption.filter;

            //存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
            queryToSearch(option, $scope.adSearcher);
        };
        $scope.initSearch();
        $scope.currSearchOption = {};

			$scope.filter = function(option, action) {
				var category = [],
					format = [],
					buttondesc = [];

				//广告类型
				if (!$scope.filterOption.type) {
					$scope.adSearcher.removeFilter("ads_type");
				} else {
					$scope.adSearcher.addFilter({
						field: 'ads_type',
						value: $scope.filterOption.type
					});
				}
				//日期范围
				if (!option.date.startDate || !option.date.endDate) {
					$scope.adSearcher.removeFilter('time');
				} else {
					var startDate = option.date.startDate.format('YYYY-MM-DD');
					var endDate = option.date.endDate.format('YYYY-MM-DD');
					$scope.adSearcher.addFilter({
						field: "time",
						min: startDate,
						max: endDate
					});
				}
				//语言
				if (option.lang && option.lang.length) {
					$scope.adSearcher.addFilter({
						field: 'ad_lang',
						value: option.lang
					});
				} else {
					$scope.adSearcher.removeFilter('ad_lang');
				}
				//国家
				if (option.state) {
					$scope.adSearcher.addFilter({
						field: 'state',
						value: option.state
					});
				} else {
					$scope.adSearcher.removeFilter('state');
				}


				//支持多项搜索，以","隔开
				angular.forEach($scope.filterOption.category, function(item, key) {
					if (item.selected) {
						category.push(item.key);
					}
				});
				$scope.filterOption.categoryString = category.join(',');

				if (category.length) {
					$scope.adSearcher.addFilter({
						field: 'category',
						value: category.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('category');
				}

				//format by select2 multiple
				angular.forEach($scope.filterOption.formatSelected, function(item) {
						format.push(item);
				});
				$scope.filterOption.formatString = format.join(',');
				if (format.length) {
					$scope.adSearcher.addFilter({
						field: 'media_type',
						value: format.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('media_type');
				}

				//Call To Action
				angular.forEach($scope.filterOption.callToAction, function(item) {
						buttondesc.push(item);
				});
				option.buttondescString = buttondesc.join(',');
				if (buttondesc.length) {
					$scope.adSearcher.addFilter({
						field: 'buttondesc',
						value: buttondesc.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('buttondesc');
				}

				//Duration Filter
				if (!option.isDurationDirty()) {
					$scope.adSearcher.removeFilter('duration_days');
				} else {
					$scope.adSearcher.addFilter({
						field: 'duration_days',
						min: option.duration.from,
						max: option.duration.to
					});
				}

				//see times Filter
				if (!option.isSeeTimesDirty()) {
					$scope.adSearcher.removeFilter('see_times');
				} else {
					$scope.adSearcher.addFilter({
						field: 'see_times',
						min: option.seeTimes.from,
						max: option.seeTimes.to
					});
				}
				//engagementsFilter
				angular.forEach(option.engagements,function(key,item){
					if (key.min==="" && key.max==="") {
						$scope.adSearcher.removeFilter(item);
					}else{
						$scope.adSearcher.addFilter({
						field: item,
						min: key.min,
						max: key.max
					});
					}
				});
				$scope.currSearchOption.category = category.join(',');
				$scope.currSearchOption.format = format.join(',');
				$scope.currSearchOption.callToAction = buttondesc.join(',');
				$scope.adSearcher.filter(action ? action : 'search').then(function() {}, function(res) {
					if (res.data instanceof Object) {
						SweetAlert.swal(res.data.desc);
					} else {
						SweetAlert.swal(res.statusText);
					}
				});
				console.log("params", $scope.adSearcher.params);
			};

			$scope.search = function(action) {
				var i;
				var option = $scope.adSearcher.searchOption;
				var keys;
				var range = [];
				var range_value=[];
				keys = $scope.adSearcher.params.keys = [];

				//检查权限，并且应该集中检查权限，才不会搞得逻辑混乱或者状态不一致
				if (!User.can('result_per_search')) {
					SweetAlert.swal("no search permission");
					return;
				}
				/*同步adsearchController代码----20170316
				if (action == 'filter' && !User.can('search_filter')) {
					SweetAlert.swal("no filter permission");
					return;
				}
				if ($scope.filterOption.type) {
					var type = ADS_TYPE[$scope.filterOption.type];
					if (!(Number(User.getPolicy('platform').value) & type)) {
						SweetAlert.swal("type '" + $scope.filterOption.type + "' exceed your permission");
						return;
					}
				}*/
				//字符串和域
				$scope.currSearchOption = angular.copy($scope.searchOption); //保存搜索
				if (option.rangeselected && option.rangeselected.length) {
					angular.forEach(option.rangeselected, function(item) {
							range.push(item);
					});
				}
				if (option.search.text || range.length) {
					option.search.fields = range.length ? range.join(',') : option.search.fields;
					keys.push({
						string: option.search.text,
						search_fields: option.search.fields,
						relation: "Must"
					});
					angular.forEach(option.range,function(item){
						if (range.indexOf(item.key)>-1)range_value.push(item.value);
					});
				}
				//域名
				if (option.domain.text) {
					keys.push({
						string: option.domain.text,
						search_fields: 'caption,link,dest_site,buttonlink',
						relation: option.domain.exclude ? 'Not' : 'Must'
					});
				}
				//受众
				if (option.audience.text) {
					keys.push({
						string: option.audience.text,
						search_fields: 'whyseeads,whyseeads_all',
						relation: option.audience.exclude ? 'Not' : 'Must'
					});
				}
				$scope.currSearchOption.range = range.join(',');
				$scope.filter($scope.filterOption, action);
				if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 1) {
					$scope.currSearchOption.isdirty = true;
				}
				searchToQuery(option, $scope.adSearcher);
			};

			$scope.clearSearch = function() {
				$location.search({});
				$state.reload();
			};
			$scope.User = User;
			$scope.Searcher = Searcher;

        $scope.adSearcher.params.where.push({
            field: 'adser_username',
            value: $stateParams.adser
        });

        //一切的操作应该是在获取到用户信息之后，后面应该优化直接从本地缓存读取
        User.getInfo().then(function() {
            //根据search参数页面初始化
            $scope.search();
        });
        // $scope.adSearcher.filter();
	}])
	.controller('AdAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$window', '$http', 'Util','User', 
		function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $window, $http, Util, User) {
			var searcher = $scope.adSearcher = new Searcher();
			// $scope.adSearcher.search($scope.adSearcher.defparams, true);
			$scope.reverseSort = function() {
				$scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
				$scope.adSearcher.filter();
			};
            $scope.User = User;
			$scope.card = {
				end: true,
				similars: []
			};
			$scope.id = $stateParams.id;
			$scope.adSearcher.addFilter({
				field: 'ads_id',
				value: $scope.id
			});
			var promise = $scope.adSearcher.filter("analysis");
			//$rootScope.$broadcast("loading");
			promise.then(function(ads) {
				//只取首条消息
				$scope.card = $scope.ad = ads.ads_info[0];
				//表示广告在分析模式下，view根据这个字段区别不同的显示
				$scope.card.indetail = true;
				$scope.card.end = false;
				if ($scope.card.whyseeads_all)
					$scope.card.whyseeads_all = $scope.card.whyseeads_all.split('\n');
				if ($scope.card.whyseeads)
					$scope.card.whyseeads = $scope.card.whyseeads.split('\n');
				searcher.findSimilar($scope.card.watermark);
			}, function(res) {
                // console.log("error res:", res);
				$scope.card.end = true;
                if (res.status != 200) {
                    Util.hint(res);
                }
			}).finally(function() {
		//		$rootScope.$broadcast("completed");
			});

			$scope.goback = function() {
				$window.history.back();
			};

			/**
			 * 查找相似图
			 */
			searcher.findSimilar = function(watermark) {
                if (!watermark)
                    return false;
                console.log("water", watermark);
				var similarSearcher = new Searcher();
				var similarPromise;
                var md5;
                if (watermark instanceof Array)
                    md5 = watermark[0].source.match(/\/(\w+)\./);
                else 
                    md5 = watermark.match(/\/(\w+)\./);
				if (md5 === null) {
					return false;
				}
				// console.log(md5);
				md5 = md5[1];

				similarSearcher.addFilter({
					field: "watermark_md5",
					value: md5
				});
				similarPromise = similarSearcher.filter();
				similarPromise.then(function(ads) {
					$scope.card.similars = ads.ads_info;
					console.log("similar", ads);
				});
				return similarPromise;
			};
            /**
             * 加载广告趋势
             */
            searcher.getTrends = function(eventid) {
                // eventid = "118849271971984";//for test
                var params = {
                    search_result:"adsid_trend",
                    where:[
                    {
                        field:"ads_id",
                        value:eventid
                    }
                    ],
                    event_id:eventid
                };
                return $http.post(settings.remoteurl + "/forward/trends", params);
            };
            searcher.isLoadingCharts = true;
            searcher.getTrends($scope.id).then(function(res) {
                // console.log(res);
                var data = res.data;
                if (!data.info) {
                    searcher.noTrends = true;
                    return;
                }
                if (data.info.comments)
                    searcher.commentsTrend = Util.initTrend(data.info.comments, "comments", $scope.id);
                if (data.info.engagements)
                    searcher.engagementsTrend = Util.initTrend(data.info.engagements, "engagements", $scope.id);
                if (data.info.likes) 
                    searcher.likesTrend = Util.initTrend(data.info.likes, "likes", $scope.id);
                if (data.info.shares)
                    searcher.sharesTrend = Util.initTrend(data.info.shares, "shares", $scope.id);
                if (data.info.views)
                    searcher.viewsTrend = Util.initTrend(data.info.views, "views", $scope.id);
            }).finally(function() {
                searcher.isLoadingCharts = false;
            });
			$scope.$on('$viewContentLoaded', function() {
				// initialize core components
				App.initAjax();

				// set default layout mode
				$rootScope.settings.layout.pageContentWhite = true;
				$rootScope.settings.layout.pageBodySolid = false;
				$rootScope.settings.layout.pageSidebarClosed = false;
			});


		}
	])

.controller('QuickSidebarController', ['$scope', 'settings', 'User', function($scope, settings, User) {

	/* Setup Layout Part - Quick Sidebar */
	//这个控制器与广告是强绑定的，这里直接指向$parent的这个方式是非常不友好的，加大了耦合
	$scope.$on('$includeContentLoaded', function() {
		$scope.settings = settings;
		$scope.filterOption = $scope.$parent.filterOption;
		$scope.daterangeOption = {
			ranges: {
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'Last 90 Days': [moment().subtract(89, 'days'), moment()],
				'All Times': ['2016-08-23', moment()]
			}
		};
		$scope.categoryOpt = {
			items: $scope.$parent.filterOption.category,
			all: false,
			collapse: true,
			defnum: 5,
			toggle: function() {
				angular.forEach($scope.$parent.filterOption.category, function(value, key) {
					if ($scope.categoryOpt.all)
						value.selected = true;
					else
						value.selected = false;
				});
			}
		};
		$scope.buttondescOpt = {
			items: $scope.$parent.filterOption.buttondesc,
			all: false,
			collapse: true,
			defnum: 5,
			toggle: function() {
				var vm = this;
				angular.forEach(this.items, function(value, key) {
					if (vm.all)
						value.selected = true;
					else
						value.selected = false;
				});
			}
		};

		$scope.reset = function() {
			$scope.$parent.initSearch();
			$scope.$parent.search();
			console.log($scope.$parent.filterOption);
		};
		$scope.User = User;
		setTimeout(function() {
			QuickSidebar.init(); // init quick sidebar        
		}, 100);
	});
}]);

app.controller('AdserSearchController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', 'Util', '$stateParams',
		function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, Util, $stateParams) {
			//搜索流程:location.search->searchOption->adSearcher.params
			//将搜索参数转换成url的query，受限于url的长度，不允许直接将参数json化
            function searchToQuery(option, searcher) {
                $location.search(searcher.searchToQuery(option));
            }
            //将query转化成搜索参数
            function queryToSearch(option, searcher) {
                searcher.queryToSearch($location.search(), option);
            }
            $scope.swal = function(msg) {
				SweetAlert.swal(msg);
			};
			$scope.adSearcher = new Searcher({
				searchType: 'adser',
				url: '/forward/adserSearch'
			});
            $scope.inAdvertiserMode = true;
			// $scope.adSearcher.search($scope.adSearcher.defparams, true);
			$scope.reverseSort = function() {
				$scope.adSearcher.params.sort.order = 1 - $scope.adSearcher.params.sort.order;
				$scope.adSearcher.filter();
			};


			// $scope.filterOption = {
			//     date: {
			//         startDate: null,
			//         endDate: null
			//     },
			// };
			//text为空时就表示没有这个搜索项了
			$scope.initSearch = function() {
				var option = $scope.searchOption = $scope.adSearcher.searchOption = angular.copy($scope.adSearcher.defSearchOption);
				$scope.filterOption = $scope.searchOption.filter;

				//存在广告主的情况下，直接搜广告主，去掉所有搜索条件，否则就按标准的搜索流程
				queryToSearch(option, $scope.adSearcher);
			};
			// $scope.resetSearch = function() {
			//     angular.forEach($scope.filterOption.category, function(value, key) {
			//         value.selected = false;
			//     });
			//     angular.forEach($scope.filterOption.format, function(value, key) {
			//         value.format = false;
			//     });
			// };
			$scope.initSearch();

			$scope.currSearchOption = {};
			// $scope.filterOption.category = angular.copy(settings.searchSetting.categoryList);
			// $scope.filterOption.format = angular.copy(settings.searchSetting.formatList);
			// $scope.filterOption.buttondesc = angular.copy(settings.searchSetting.buttondescList);

			$scope.filter = function(option) {
				var category = [],
					format = [],
					buttondesc = [];
				//广告类型
				if (!$scope.filterOption.type)
					$scope.adSearcher.removeFilter("ads_type");
				else
					$scope.adSearcher.addFilter({
						field: 'ads_type',
						value: $scope.filterOption.type
					});
				//日期范围
				if (!option.date.startDate || !option.date.endDate) {
					$scope.adSearcher.removeFilter('time');
				} else {
					var startDate = option.date.startDate.format('YYYY-MM-DD');
					var endDate = option.date.endDate.format('YYYY-MM-DD');
					$scope.adSearcher.addFilter({
						field: "time",
						min: startDate,
						max: endDate
					});
				}
				//语言
				if (option.lang && option.lang.length) {
					$scope.adSearcher.addFilter({
						field: 'ad_lang',
						value: option.lang
					});
				} else {
					$scope.adSearcher.removeFilter('ad_lang');
				}
				//国家
				if (option.state) {
					$scope.adSearcher.addFilter({
						field: 'state',
						value: option.state
					});
				} else {
					$scope.adSearcher.removeFilter('state');
				}


				//支持多项搜索，以","隔开
				angular.forEach($scope.filterOption.category, function(item, key) {
					if (item.selected) {
						category.push(item.key);
					}
				});
				$scope.filterOption.categoryString = category.join(',');

				if (category.length) {
					$scope.adSearcher.addFilter({
						field: 'category',
						value: category.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('category');
				}

				//format by select2 multiple
				angular.forEach($scope.filterOption.formatSelected, function(item) {
						format.push(item);
				});
				$scope.filterOption.formatString = format.join(',');
				if (format.length) {
					$scope.adSearcher.addFilter({
						field: 'media_type',
						value: format.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('media_type');
				}

				//Call To Action
				angular.forEach($scope.filterOption.callToAction, function(item) {
						buttondesc.push(item);
				});
				option.buttondescString = buttondesc.join(',');
				if (buttondesc.length) {
					$scope.adSearcher.addFilter({
						field: 'buttondesc',
						value: buttondesc.join(",")
					});
				} else {
					$scope.adSearcher.removeFilter('buttondesc');
				}

				//Duration Filter
				if (!option.isDurationDirty()) {
					$scope.adSearcher.removeFilter('duration_days');
				} else {
					$scope.adSearcher.addFilter({
						field: 'duration_days',
						min: option.duration.from,
						max: option.duration.to
					});
				}

				//see times Filter
				if (!option.isSeeTimesDirty()) {
					$scope.adSearcher.removeFilter('see_times');
				} else {
					$scope.adSearcher.addFilter({
						field: 'see_times',
						min: option.seeTimes.from,
						max: option.seeTimes.to
					});
				}
				//engagementsFilter
				angular.forEach(option.engagements,function(key,item){
					if (key.min==="" && key.max==="") {
						$scope.adSearcher.removeFilter(item);
					}else{
						$scope.adSearcher.addFilter({
						field: item,
						min: key.min,
						max: key.max
					});
					}
				});
				$scope.currSearchOption.category = category.join(',');
				$scope.currSearchOption.format = format.join(',');
				$scope.currSearchOption.callToAction = buttondesc.join(',');
                $scope.adSearcher.filter().then(function() {}, function(res) {
                    if (res.status != 200)
                        Util.hint(res);
                });

				console.log("params", $scope.adSearcher.params);
			};

			$scope.search = function() {
				var i;
				var option = $scope.adSearcher.searchOption;
				var keys;
				var range = [];
				var range_value=[];
				keys = $scope.adSearcher.params.keys = [];

				//字符串和域
				$scope.currSearchOption = angular.copy($scope.searchOption); //保存搜索
				if (option.rangeselected && option.rangeselected.length) {
					angular.forEach(option.rangeselected, function(item) {
							range.push(item);
					});
				}
				if (option.search.text || range.length) {
					option.search.fields = range.length ? range.join(',') : option.search.fields;
					keys.push({
						string: option.search.text,
						search_fields: option.search.fields,
						relation: "Must"
					});
					angular.forEach(option.range,function(item){
						if (range.indexOf(item.key)>-1)range_value.push(item.value);
					});
				}
				//域名
				if (option.domain.text) {
					keys.push({
						string: option.domain.text,
						search_fields: 'caption,link,dest_site,buttonlink',
						relation: option.domain.exclude ? 'Not' : 'Must'
					});
				}
				//受众
				if (option.audience.text) {
					keys.push({
						string: option.audience.text,
						search_fields: 'whyseeads,whyseeads_all',
						relation: option.audience.exclude ? 'Not' : 'Must'
					});
				}
				$scope.currSearchOption.range = range.join(',');
				$scope.filter($scope.filterOption);
				if ($scope.adSearcher.params.keys.length > 0 || $scope.adSearcher.params.where.length > 0) {
					$scope.currSearchOption.isdirty = true;
				}
				searchToQuery(option, $scope.adSearcher);
			};

			$scope.clearSearch = function() {
				$location.search({});
				$state.reload();
			};

			//根据search参数页面初始化

			$scope.search();
			$scope.$on('$viewContentLoaded', function() {
				// initialize core components
				App.initAjax();

				// set default layout mode
				$rootScope.settings.layout.pageContentWhite = true;
				$rootScope.settings.layout.pageBodySolid = false;
				$rootScope.settings.layout.pageSidebarClosed = false;
			});


		}
	])
	.controller('AdserAnalysisController', ['$rootScope', '$scope', 'settings', 'Searcher', '$filter', 'SweetAlert', '$state', '$location', '$stateParams', '$http', '$uibModal', '$q', 'Util', '$timeout',  
		function($rootScope, $scope, settings, Searcher, $filter, SweetAlert, $state, $location, $stateParams, $http, $uibModal, $q, Util, $timeout) {

			function getAdserAnalysis(username, select) {
				if (select === undefined)
					select = "all";
				var params = {
					"search_result": "adser_analysis",
					"where": [{
						"field": "adser_username",
						"value": username
					}],
					"select": select
				};
				console.log("params", params);
				return $http.post(settings.remoteurl + '/forward/adserAnalysis',
					params);
			}
			/**
			 * jsonSrc:json字符串
			 * title:标题
			 * labels:是否将json的属性映射到labels对应值
			 */
			function initPie(jsonSrc, title, labels) {
				var src = jsonSrc;
				var data = [];
				for (var key in src) {
					if (labels)
						data.push([labels[key], src[key]]);
					else
						data.push([key, src[key]]);
				}

				return {
					chart: {
						type: 'pie'
					},
					title: {
						text: title
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>:{point.percentage:.1f}%'
							}
						}
					},
					series: [{
						name: title,
						data: data
					}],
                    credits:false
				};
			}
            function getTrendData(json) {
				if (!json || !json.trend) {
					return null;
				}
				var length = json.trend.length;
				var endDate = moment(json.day, 'YYYY-MM-DD');
				var xs = [endDate.format('YYYY-MM-DD')];
				var i;
				for (i = 1; i < length; ++i) {
					xs.push(endDate.subtract(1, 'days').format('YYYY-MM-DD'));
				}
				xs = xs.reverse();
				ys = json.trend.reverse();
                return [xs, ys];
            }
			/**
             * 单个广告的趋势图
			 */
			function initTrend(json, title, name) {

                var data = getTrendData(json);
                if (data === null)
                    return;
				return {
					title: {
						text: title
					},
					xAxis: {
						categories: data[0]
					},
					yAxis: {
						title: {
							text: title
						},
						plotLines: [{
							value: 0,
							width: 1,
						}]
					},
					series: [{
						name: name,
						data: data[1]
					}],
                    className:"response-width",
                    credits:false
				};
			}

			/**
			 * 初始化单个广告主的图表
			 */
			function initChart(card) {
				card.mediaTypeConfig = Util.initPie(card.media_type_groupby, "Media Type");
				card.adLangConfig = Util.initPie(card.ad_lang_groupby, 'Advertise Language');
				card.showwayConfig = Util.initPie(card.show_way_groupby, 'Show Way', {
					"1": "timeline",
					"2": "mobile",
					"3": "timeline&mobile",
					"4": "rightcolumn"
				});
				if (card.likes_trend)
					card.likesTrendConfig = initTrend((card.likes_trend), "Likes Trend", card.name);
				if (card.shares_trend)
					card.sharesTrendConfig = initTrend((card.shares_trend), "Shares Trend", card.name);
				if (card.views_trend)
					card.viewsTrendConfig = initTrend((card.views_trend), "Views Trend", card.name);
				if (card.new_ads_trend)
					card.newAdsTrendConfig = initTrend((card.new_ads_trend), "New Ads Trend", card.name);
			}

			function initCompareBar(dataArr, nameArr, title, labels) {
				var opt, i, key;
				var categories = [],
					map = [];
				var series = [],
					seriesData;
				//合并分类，去重
				for (i = 0; i < dataArr.length; ++i) {
					for (key in dataArr[i]) {
						if (!map[key]) {
							if (labels)
								categories.push(labels[key]);
							else
								categories.push(key);
							map[key] = categories.length;
						}
					}
				}
				//插补数据
				for (i = 0; i < dataArr.length; ++i) {
					seriesData = new Array(categories.length);
					for (key = 0; key < categories.length; ++key) {
						seriesData[key] = 0;
					}
					for (key in dataArr[i]) {
						seriesData[map[key] - 1] = dataArr[i][key];
					}
					series.push({
						name: nameArr[i],
						data: seriesData
					});
				}
				opt = {
					chart: {
						type: 'column'
					},
					title: {
						text: title
					},
					xAxis: {
						categories: categories
					},
					yAxis: {
						min: 0
					},
					series: series,
                    credits:false
				};
				return opt;
			}

            function initCompareTrend(jsonArr, nameArr, title)
            {
                var i, series = [], xs;
                angular.forEach(jsonArr, function(json, key) {
                    var data = getTrendData(json);
                    if (!data)
                        return;
                    xs = data[0];
                    series.push({name:nameArr[key], data:data[1]});    
                });
                if (series.length === 0)
                    return null;
				return {
					title: {
						text: title
					},
					xAxis: {
						categories: xs
					},
					yAxis: {
						title: {
							text: title
						},
						plotLines: [{
							value: 0,
							width: 1,
						}]
					},
					series: series,
                    credits:false
				};
            }


			var competitorQuery = [];
			var promises = [];
			$scope.openAd = Util.openAd;
			$scope.card = {words:[]}; //card必须先赋值，否则调用Searcher的getAdsType时会提前生成自己的card,scope出错。
			$scope.Searcher = Searcher;
			$scope.username = $stateParams.username;
			$rootScope.$broadcast("loading");
			promises[0] = getAdserAnalysis($scope.username, "overview,rank,trend,topkeyword");
			promises[0].then(function(res) {
                var key;
				for (key in res.data) {
					if (!$scope.card[key]) {
						$scope.card[key] = res.data[key];
					}
				}
				console.log("first phase", $scope.card);
				initChart($scope.card);
                for (key in $scope.card.top_keyword) {
                    $scope.card.words.push({text:key, weight:$scope.card.top_keyword[key]});
                }

				$rootScope.$broadcast("jqchange");
                // console.log("words", $scope.card.words);
				$rootScope.$broadcast("completed");

                promises[1] = getAdserAnalysis($scope.username, "summary,audience_all,link,topn,button");
                promises[1].then(function(res) {
                    // $scope.card = res.data;
                    for (var key in res.data) {
                        if (!$scope.card[key]) {
                            $scope.card[key] = res.data[key];
                        }
                    }

                    console.log("second phase", $scope.card);
                    initChart($scope.card);
                    // $timeout(function() {
                    //     $scope.$apply();
                    // }, 0);
                });
			});
            // $timeout(function() {
            //     promises[1] = getAdserAnalysis($scope.username, "summary,audience_all,link,topn,button");
            //     promises[1].then(function(res) {
            //         // $scope.card = res.data;
            //         for (var key in res.data) {
            //             if (!$scope.card[key]) {
            //                 $scope.card[key] = res.data[key];
            //             }
            //         }

            //         console.log("second phase", $scope.card);
            //         initChart($scope.card);
            //         // $timeout(function() {
            //         //     $scope.$apply();
            //         // }, 0);
            //     });

            // },0);

			function addCompetitor(res) {
				$scope.competitors.push(res.data);
			}

			function initFromQuery() {
				if ($location.search().competitor)
					competitorQuery = $location.search().competitor.split(',');
				for (var key in competitorQuery) {
					promises.push(getAdserAnalysis(competitorQuery[key]));
					promises[promises.length - 1].then(addCompetitor);
				}

				// console.log(competitorQuery);
			}

			function initCompetitorCharts() {
				var langGroup = [$scope.card.ad_lang_groupby];
				var mediaTypeGroup = [$scope.card.media_type_groupby];
				var showwayGroup = [$scope.card.show_way_groupby];
				var nameArr = [$scope.card.name];
                var likesArr = [$scope.card.likes_trend];
                var sharesArr = [$scope.card.shares_trend];
                var viewsArr = [$scope.card.views_trend];
                var newAdsArr = [$scope.card.new_ads_trend];
				for (var key in $scope.competitors) {
					langGroup.push($scope.competitors[key].ad_lang_groupby);
					mediaTypeGroup.push($scope.competitors[key].media_type_groupby);
					showwayGroup.push($scope.competitors[key].show_way_groupby);
					nameArr.push($scope.competitors[key].name);
                    likesArr.push($scope.competitors[key].likes_trend);
                    sharesArr.push($scope.competitors[key].shares_trend);
                    viewsArr.push($scope.competitors[key].views_trend);
                    newAdsArr.push($scope.competitors[key].new_ads_trend);
				}
				$scope.competitorsChart.langOpt = initCompareBar(langGroup, nameArr, "Language");
				$scope.competitorsChart.mediaTypeOpt = initCompareBar(mediaTypeGroup, nameArr, "Media Type");
				$scope.competitorsChart.showwayOpt = initCompareBar(showwayGroup, nameArr, "Show way", {
					"1": "timeline",
					"2": "mobile",
					"3": "timeline&mobile",
					"4": "rightcolumn"
				});
                $scope.competitorsChart.likesTrendOpt = initCompareTrend(likesArr, nameArr, "Likes Trend");
                $scope.competitorsChart.sharesTrendOpt = initCompareTrend(sharesArr, nameArr, "Shares Trend");
                $scope.competitorsChart.viewsTrendOpt = initCompareTrend(viewsArr, nameArr, "Views Trend");
                $scope.competitorsChart.newAdsTrendOpt = initCompareTrend(newAdsArr, nameArr, "New Ads Trend");
				// console.log($scope.competitorsChart);
			}
			initFromQuery();

			$scope.competitors = [];
			$scope.competitorPopover = false;
			$scope.competitorsChart = {};
			$scope.$on('competitor', function(event, data) {
				var p;
				event.stopPropagation();
				$scope.competitorPopover = false;
				p = getAdserAnalysis(data.adser_username);
				p.then(addCompetitor).then(initCompetitorCharts);
				competitorQuery.push(data.adser_username);
				$location.search('competitor', competitorQuery.join(','));
			});

			$scope.remove = function(idx) {
				$scope.competitors.splice(idx, 1);
				competitorQuery.splice(idx, 1);
				$location.search('competitor', competitorQuery.join(','));
				initCompetitorCharts();
			};


			//所有广告主分析数据加载完成才处理图表
			$q.all(promises).then(initCompetitorCharts);

			$scope.$on('$viewContentLoaded', function() {
				// initialize core components
				App.initAjax();

				// set default layout mode
				$rootScope.settings.layout.pageContentWhite = true;
				$rootScope.settings.layout.pageBodySolid = false;
				$rootScope.settings.layout.pageSidebarClosed = false;

			});


		}
	])
	.controller('CompetitorSearcherController', ['$scope', 'Searcher', function($scope, Searcher) {
		var searcher = new Searcher({
			searchType: 'adser',
			url: '/forward/adserSearch',
			limit: [0, -1]
		});
		$scope.searchOpt = {
			text: "",
			inprogress: false
		};
		$scope.promise = null;
		$scope.search = function() {
			searcher.params.keys = [{
				string: $scope.searchOpt.text,
				search_fields: 'adser_name',
				relation: "Must"
			}];
			$scope.searchOpt.inprogress = true;
			$scope.promise = searcher.filter();
			$scope.promise.then(function(data) {
				$scope.searchOpt.items = data.adser;
				// $scope.searchOpt.inprogress = false;
				$scope.searchOpt.error = null;
			}, function(data) {
				$scope.searchOpt.error = "No Data";
				$scope.searchOpt.items = null;
			}).finally(function() {
				$scope.searchOpt.inprogress = false;
			});
		};

		$scope.notify = function(item) {
			$scope.$emit('competitor', item);
		};
	}]);
