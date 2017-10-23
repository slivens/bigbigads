import '../pages/common/common'
import template from './billings.html'

angular.module('MetronicApp').controller('BillingsController', ['$scope', 'User', 'Resource', 'SweetAlert', '$http', function($scope, User, Resource, SweetAlert, $http) {
    var ctrl = this
    var billings = new Resource('billings')
    ctrl.init = function() {
        User.getInfo().then(function() {
            // $scope.userInfo = User.info;
            // $scope.subscription = User.info.subscription;
            if (!User.login) return

            const user = User.info.user
            ctrl.subscriptionId = user.subscription_id
            ctrl.queryPromise = billings.get().then(() => {
                const firstCompleted = billings.items[billings.items.length - 1]

                billings.items.map((item, index) => {
                    item.openDownload = true
                    if (billings.items.length - 1 === index) {
                        item.canRefund = item.status == 'completed' && moment().diff(moment(firstCompleted.start_date), 'days') < 7 && !item.refund
                    }
                    item.openDownload = item.status == 'completed' && moment().diff(moment(firstCompleted.start_date), 'days') >= 7

                    if (item.is_effective && !ctrl.effective_id) ctrl.effective_id = item.id

                    item.invoiceMessages = 'Please download the invoice after 7 days.'
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
                SweetAlert.swal(res.data.desc)
            })
        })
    }
    ctrl.invoiceDownload = function(item) {
        item.openDownload ? window.open(`/invoices/${item.invoice_id}`) : SweetAlert.swal('Please download the invoice after 7 days.')
    }
    ctrl.$onChanges = function(obj) {
        if (obj.shouldInit.currentValue !== "true") // || ctrl.inited)
            return
        ctrl.init()
    }
}])
// TODO:templateUrl通过依赖注入加时间戳
angular.module('MetronicApp').component('billings', {
    template,
    controller: 'BillingsController',
    bindings: {
        shouldInit: '@'
    }
})
