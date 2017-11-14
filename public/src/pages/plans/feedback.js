import template from './feedback.html'

const controller = function($scope, $uibModalInstance, User) {
    // 定义价格数组
    const princeArr = {
        'lite': [{
            'id': 101,
            'price': '25,35',
            'value': '25-35($)'
        }, {
            'id': 102,
            'price': '35,45',
            'value': '35-45($)'
        }, {
            'id': 103,
            'price': '45,60',
            'value': '45-60($)'
        }, {
            'id': 104,
            'price': 'other',
            'value': 'Other'
        }],
        'plus': [{
            'id': 201,
            'price': '149,199',
            'value': '149-199($)'
        }, {
            'id': 202,
            'price': '199,249',
            'value': '199-249($)'
        }, {
            'id': 203,
            'price': '249,399',
            'value': '249-399($)'
        }, {
            'id': 204,
            'price': 'other',
            'value': 'Other'
        }]
    }
    /*
    * 相对应的价格
    * $scope.$resolve.plan = 'plus'
    */
    if (princeArr[$scope.$resolve.plan]) $scope.planPrice = princeArr[$scope.$resolve.plan]
    else $scope.planPrice = false
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }
    $scope.firstName = 'test'
}

controller.$inject = ['$scope', '$uibModalInstance', 'User']
export { template, controller }
