if (!app)
    var app = angular.module('MetronicApp');
app.controller('PlansController', ['$scope', 'Resource', 'User', function($scope, Resource, User) {
    var plans = new Resource('plans');
    plans.getPolicy = function(item, permissionKey, groupKey) {
        var group = item.groupPermissions[groupKey], policy;
        var i, finded = false;
        if (!group)
            return false;
        angular.forEach(group, function(groupItem) {
            if (groupItem.key == permissionKey) {
                finded = true;   
            }
        });
        if (!finded)
            return false;
        for(i = 0;i < item.policies.length; ++i) {
            policy = item.policies[i];
            if (policy.key == permissionKey) {
                return {value:policy.pivot.value, type:policy.type};
            }
        }

        return true;
    };

    plans.goPlanID = function(item) {
        var id;
        if (!item.plan)
            return "";
        if (plans.annually) {
            id = item.plan.annually.id;
        }  else  {
            id = item.plan.monthly.id;
        }
        // console.log(plans.annually, id);
        window.open("/pay?plan=" + id);
    };
    plans.isCurrentPlan = function(item) {
        if (!User.info.subscription)
            return false;
        return User.info.subscription.braintree_plan.replace('_Monthly', '') == item.name;
    };

    plans.annually = false;

    $scope.queryPromise = plans.get();
    $scope.queryPromise.then(function(items) {
        // console.log(items);
        if(items.length > 0) {
            $scope.groupPermissions = items[items.length - 1].groupPermissions;
        }
    });
    $scope.plans = plans;
    $scope.groupPermissions = [];
    User.getInfo().then(function() {
        $scope.userInfo = User.info;
        if (User.info.login) {
            $scope.subscription = User.info.subscription;
        }
    });
}]);
app.controller('ProfileController', ['$scope', '$location', 'User', '$uibModal', function($scope, $location, User, $uibModal) {
    var profile = {
        init:function() {
            var search = $location.search();
            if (search.active && search.active != this.active) {
                this.active = Number(search.active);
            }
        }
    };
    profile.init();
    $scope.profile = profile;
    $scope.$watch('profile.active', function(newValue, oldValue) {
        if (newValue == oldValue)
            return;
         $location.search('active', newValue);
    });
    $scope.$on('$locationChangeSuccess', function(ev) {
        // console.log($location.search());
        profile.init();
    });
    $scope.changePwd = function() {
        return $uibModal.open({
            templateUrl:'views/profile/changepwd.html',
            size:'md',
            animation:true,
            controller:'ChangepwdController'
        });
    };
    $scope.userPromise = User.getInfo();
    $scope.userPromise.then(function() {
        $scope.userInfo = User.info;
        $scope.user = User.info.user;
        $scope.User = User;
    });
}]);
app.controller('SubscriptionController', ['$scope', 'User', function($scope, User) {
    User.getInfo().then(function() {
        $scope.userInfo = User.info;
        $scope.subscription = User.info.subscription;
    });
}]);
app.controller('BillingsController', ['$scope', 'User', 'Resource', function($scope, User, Resource) {
    var billings = new Resource('billings');
    $scope.billings = billings;

    $scope.beatifyDate = function(dateStr) {
        return dateStr.split(' ')[0];
    };
    User.getInfo().then(function() {
        $scope.userInfo = User.info;
        $scope.subscription = User.info.subscription;
        if (!User.info.login) 
            return;
        $scope.queryPromise = billings.get();
    });
}]);
app.controller('ChangepwdController', ['$scope', '$uibModalInstance', '$http', 'settings', function($scope, $uibModalInstance, $http, settings) {
    var info = {
        oldpwd:null,
        newpwd:null,
        repeatpwd:null
        };
    var url = settings.remoteurl + "/changepwd";
    $scope.info = info;
    $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
    };
    $scope.save = function(item) {
        console.log($scope.info);
        // $scope.promise = bookmark.save(item);
        // $scope.promise.then(function() {
        //     $uibModalInstance.dismiss('success');
        // });
       if (info.newpwd != info.repeatpwd) {
            info.error = "repeat password is diffrent with new password";
            return;
       }
       $scope.promise = $http.post(url, info);
       $scope.promise.then(function(res) {
            var data = res.data;
            if (Number(data.code) !== 0) {
                info.error = data.desc;
                return;
            }
            $uibModalInstance.dismiss('save');
       }, function(res) {
            info.error = res.statusText;
       });
    };
}]);