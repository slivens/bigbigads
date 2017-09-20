import '../pages/common/common'

angular.module('MetronicApp').controller('BillingsController', ['$scope', 'User', 'Resource', 'SweetAlert', '$http', function($scope, User, Resource, SweetAlert, $http) {
    var ctrl = this
    var billings = new Resource('billings')
    ctrl.init = function() {
        User.getInfo().then(function() {
            // $scope.userInfo = User.info;
            // $scope.subscription = User.info.subscription;
            if (!User.login)
                return
            var user = User.info.user

            ctrl.subscriptionId = user.subscription_id
            ctrl.queryPromise = billings.get().then(() => {
                billings.items.map(function(item) {
                    // 7天以内且成功支付的订单才允许申请退款
                    if (moment().diff(moment(item.start_date), 'days') <= 7 && item.status == 'completed' && !item.refund)
                        item.canRefund = true
                    if (item.is_effective && !ctrl.effective_id)
                        ctrl.effective_id = item.id
                })
            })
            ctrl.inited = true
        })
    }

    ctrl.billings = billings

    ctrl.beatifyDate = function(dateStr) {
        return dateStr.split(' ')[0]
    }
    ctrl.refund = function(item) {
        SweetAlert.swal({
            title: "check refunding?",
            type: "warning",
            showCancelButton: true
        }, function(isConfirm) {
            if (!isConfirm)
                return
            // 退款成功重新获取订单
            $http.put('/payments/' + item.number + '/refund_request').then(function(res) {
                if (res.data.code != 0)
                    throw res
                ctrl.init()
            }).catch(function(res) {
                SweetAlert.swal(res.data.message)
            })
        })
    }
    ctrl.$onChanges = function(obj) {
        if (obj.shouldInit.currentValue !== "true") // || ctrl.inited)
            return
        ctrl.init()
    }
}])
// TODO:templateUrl通过依赖注入加时间戳
angular.module('MetronicApp').component('billings', {
    templateUrl: 'components/billings.html',
    controller: 'BillingsController',
    bindings: {
        shouldInit: '@'
    }
})
