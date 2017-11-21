import template from './feedback.html'
import countryArr from '../../data/map-country.json'
import './feedback.scss'

const controller = function($scope, $uibModalInstance, User, $http, SweetAlert) {
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

    // 初始化
    $scope.feedback = {
        'firstName': '',
        'lastName': '',
        'email': '',
        'company': '',
        'website': '',
        'page': '',
        'phone': '',
        'skype': '',
        'location': '',
        'price': '',
        'feedback': '',
        'level': $scope.$resolve.plan || 'plus' // 可能会存在princeArr[$scope.$resolve.plan]为空
    }

    // 用于限制提交按钮是否繁忙
    $scope.isBusy = false

    /*
     * 相对应的价格
     * 1）可能数据：$scope.$resolve.plan = 'plus'
     * 2）给$scope.feedback.price默认值，因为ng-option的第一个值是空的
     */
    if (princeArr[$scope.$resolve.plan]) {
        $scope.planPrice = princeArr[$scope.$resolve.plan]
        $scope.feedback.price = $scope.planPrice[0].price
    } else $scope.planPrice = false

    $scope.locationArr = []
    if (countryArr) {
        for (let key in countryArr) {
            $scope.locationArr.push({
                'name': countryArr[key].name
            })
        }
    } else $scope.locationArr = false

    // 关闭模态框
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }

    /*
     * 点击提交按钮触发验证
     * 该方法有传值的参数是要验证的name值
     * 如果没有传参数，则对$scope.feedback这些字段全部验证
     */
    let toValidate = function(validata) {
        if (validata) {
            $scope.feedbackForm[validata].$dirty = true
        } else {
            for (let name in $scope.feedback) {
                if ($scope.feedbackForm[name] && $scope.feedbackForm[name].$invalid) {
                    $scope.feedbackForm[name].$dirty = true
                    return false
                }
            }
            return true
        }
    }

    /*
     * 点击提交
     * 1）点击的时候让submit按钮处于繁忙，避免重复点击
     * 2）对数据再次进行验证，通过了便可以提交
     * 3）提交成功后，关闭当前的模态框，并利用SweetAlert插件弹窗提示
     */
    $scope.save = function() {
        // 点击提交的时候，让按钮禁止点击，避免重复点击
        $scope.isBusy = true
        if (toValidate()) {
            $http({
                method: 'POST',
                url: '/feedback/plan',
                data: $scope.feedback
            }).then(function(res) {
                if (res.data.code == 0) {
                    // 关闭信息填写窗口
                    $scope.close()
                    // 弹出信息框，用于告知用户提交成功
                    SweetAlert.swal(
                        'Submit successfully',
                        '😃 Thank you for your participation.',
                        'success'
                    )
                } else {
                    // 恢复按钮可点击状态
                    $scope.isBusy = false
                    SweetAlert.swal(
                        'Submit failure',
                        `😐 ${res.data.desc}`,
                        'success'
                    )
                }
            }).catch(function(res) {
                // 恢复按钮可点击状态
                $scope.isBusy = false
                SweetAlert.swal(
                    'Submit failure',
                    '😐 Please enter the correct information to continue.',
                    'success'
                )
            })
        }
    }

    $scope.toValidate = toValidate
}

controller.$inject = ['$scope', '$uibModalInstance', 'User', '$http', 'SweetAlert']
export { template, controller }
