import '../common/common'
import './plans.scss'
import template from './plans.html'

export default angular.module('plans', ['MetronicApp']).controller('PlansController', ['$scope', 'Resource', 'User', function($scope, Resource, User) {
    var plans = new Resource('plans')
    plans.getPolicy = function(item, permissionKey, groupKey) {
        var group = item.groupPermissions[groupKey]
        var policy
        var i
        var finded = false
        if (!group)
            return false
        angular.forEach(group, function(groupItem) {
            if (groupItem.key == permissionKey) {
                finded = true
            }
        })
        if (!finded)
            return false
        for (i = 0; i < item.policies.length; ++i) {
            policy = item.policies[i]
            if (policy.key == permissionKey) {
                return {value: policy.pivot.value, type: policy.type}
            }
        }

        return true
    }
    $scope.showPlanItem = function() {
        $scope.isTurn = true
    }
    $scope.hiddenPlanItem = function() {
        $scope.isTurn = false
    }

    plans.goPlanID = function(item) {
        var id
        if (!item.plans)
            return ""

        if (plans.annually) {
            id = item.plans.annually.id
        } else {
            id = item.plans.monthly.id
        }
        // console.log(plans.annually, id);
        window.open("/pay?plan=" + id)
    }
    plans.isCurrentPlan = function(item) {
        /* if (!User.user.subscription)
            return false; */
        var planName
        var rolePlan
        var isCurrentPlan
        planName = item.plans.monthly.name
        rolePlan = User.user.role.plan
        isCurrentPlan = new RegExp(rolePlan).test(planName)
        return isCurrentPlan
    }

    plans.annually = false

    $scope.queryPromise = plans.get()
    $scope.queryPromise.then(function(items) {
        if (items.length > 0) {
            $scope.groupPermissions = items[items.length - 1].groupPermissions
        }
    })
    $scope.plans = plans
    $scope.groupPermissions = []
    User.getInfo().then(function() {
        $scope.userInfo = User.info
        if (User.info.login) {
            $scope.subscription = User.info.subscription
            $scope.userPlan = User.info.user.role.plan
        }
    })
}])
    .directive('plans', function() {
        return {
            restrict: 'E',
            scope: {},
            template,
            replace: false,
            controller: 'PlansController'
        }
    })
