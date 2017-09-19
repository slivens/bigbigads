import '../common/common'

angular.module('MetronicApp').controller('SubscriptionController', ['$scope', '$http', 'User', 'SweetAlert', function($scope, $http, User, SweetAlert) {
    var ctrl = this
    function init(force) {
        User.getInfo(force).then(function() {
            // console.log("inited", User.info.user)
            var user = User.info.user
            var i
            ctrl.userInfo = User.info
            ctrl.subscription = User.user.subscription
            ctrl.canCancel = false
            if (ctrl.subscription &&
                    (ctrl.subscription.status != 'canceled'))
                ctrl.canCancel = true
            for (i = user.subscriptions.length - 1; i >= 0; --i) {
                if (user.subscriptions[i].status != 'created') {
                    ctrl.lastSubscription = user.subscriptions[i]
                    break
                }
            }
            if (!ctrl.lastSubscription)
                return
            if (ctrl.lastSubscription.status == 'canceled') {
                ctrl.lastSubscription.payments.map(function(item) {
                    if (item.is_effective)
                        ctrl.effectPayment = item
                })
            }
            if (ctrl.lastSubscription.status == 'subscribed' || ctrl.lastSubscription.status == 'pending') {
                setTimeout(function() {
                    // console.log("update")
                    $scope.$apply(function() {
                        $http.post('/subscriptions/' + ctrl.lastSubscription.agreement_id + '/sync').then(function(res) {
                            init(true)
                        })
                    })
                }, 10000)
            }
        })
    }
    ctrl.cancel = function() {
        SweetAlert.swal({
            title: "Are you sure to cancel?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        },
        function(isConfirm) {
            if (isConfirm) {
                var url = '/subscription/' + ctrl.subscription.id + '/cancel'
                ctrl.cancelPromise = $http.post(url).then(function(res) {
                    if (res.data.code !== 0)
                        throw res
                    init(true)
                }).catch(function(res) {
                    SweetAlert.swal(res.data.message)
                })
            }
        })
    }
    init(false)
}])
    .component('subscription', {
        templateUrl: 'components/subscription.html',
        controller: 'SubscriptionController'
    })
