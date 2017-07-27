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
    $scope.showPlanItem = function(){
        $scope.isTurn = true;
    };
    $scope.hiddenPlanItem = function(){
        $scope.isTurn = false;
    }

    plans.goPlanID = function(item) {
        var id;
        if (!item.plans)
            return "";
        
        if (plans.annually) {
            id = item.plans.annually.id;
        }  else  {
            id = item.plans.monthly.id;
        }
        // console.log(plans.annually, id);
        window.open("/pay?plan=" + id);
    };
    plans.isCurrentPlan = function(item) {
        /*if (!User.user.subscription)
            return false;*/
        var planName;
        var rolePlan;
        var isCurrentPlan;
        planName = item.plans.monthly.name;
        rolePlan = User.user.role.plan;
        isCurrentPlan = new RegExp(rolePlan).test(planName);
        return isCurrentPlan;
    };

    plans.annually = false;

    $scope.queryPromise = plans.get();
    $scope.queryPromise.then(function(items) {
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
app.controller('ProfileController', ['$scope', '$location', 'User', '$uibModal', 'TIMESTAMP', function($scope, $location, User, $uibModal, TIMESTAMP) {
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
            templateUrl:'views/profile/changepwd.html?t=' + TIMESTAMP,
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
        $scope.subscription = User.user.subscription;
    });
}]);
//TODO:templateUrl通过依赖注入加时间戳
app.component('billings', {
    templateUrl:'components/billings.html',
    controller:'BillingsController',
    bindings:{
        shouldInit:'@'
    }
});
app.controller('BillingsController', ['$scope', 'User', 'Resource', function($scope, User, Resource) {
    var ctrl = this;
    var billings = new Resource('billings');
    ctrl.billings = billings;

    ctrl.beatifyDate = function(dateStr) {
        return dateStr.split(' ')[0];
    };
    ctrl.$onChanges = function(obj) {
        if (obj.shouldInit.currentValue !== "true" || ctrl.inited)
            return;

        User.getInfo().then(function() {
            // $scope.userInfo = User.info;
            // $scope.subscription = User.info.subscription;
            if (!User.login) 
                return;
            ctrl.queryPromise = billings.get();
            ctrl.inited = true;
        });
    };
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
