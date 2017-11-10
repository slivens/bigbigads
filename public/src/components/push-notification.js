import template from './push-notification.html'
import './push-notification.scss'

const controller = function($scope, $uibModalInstance, User) {
    $scope.close = function() {
        $uibModalInstance.dismiss('success')
    }
    $scope.User = User
    $scope.filterArr = [{
        'class': 'fa-calendar', // 图标样式
        'name': 'first_time_filter', // filter
        'value': 'First See Time Frame' // filter的name
    }, {
        'class': 'fa-calendar',
        'name': 'last_time_filter',
        'value': 'Last See Time Frame'
    }, {
        'class': 'fa-picture-o',
        'name': 'format_filter',
        'value': 'Ad Type'
    }, {
        'class': 'fa-flag',
        'name': 'country_filter',
        'value': 'Country or Region'
    }, {
        'class': 'fa-inbox',
        'name': 'objective_filter',
        'value': 'Objective'
    }, {
        'class': 'fa-external-link-square',
        'name': 'e_commerce_filter',
        'value': 'Eshop'
    }, {
        'class': 'fa-user-secret',
        'name': 'audience_age_filter',
        'value': 'Audience Age'
    }, {
        'class': 'fa-leaf',
        'name': 'audience_gender_filter',
        'value': 'Audience Gender'
    }, {
        'class': 'fa-flag',
        'name': 'audience_interest_filter',
        'value': 'Audience Interest'
    }, {
        'class': 'fa-line-chart',
        'name': 'duration_filter',
        'value': 'Customized Ad Run duration'
    }, {
        'class': 'fa-eye',
        'name': 'see_times_filter',
        'value': 'Customized Ad Run See Times'
    }, {
        'class': 'fa-pie-chart',
        'name': 'advance_likes_filter',
        'value': 'Customized Engagements'
    }]
}

controller.$inject = ['$scope', '$uibModalInstance', 'User']

export { template, controller }
