import template from './sign.html'

const signController = function($scope, $uibModalInstance, $window) {
    var slides = $scope.slides = []
    var i
    $scope.addSlide = function() {
        var imgItem = slides.length + 1
        slides.push({
            image: 'adscard_0' + imgItem + '.jpg'
        })
    }
    for (i = 0; i < 4; i++) {
        $scope.addSlide()
    }
    $scope.close = function() {
        $uibModalInstance.dismiss('cancle')
    }

    // 获取track码
    if ($window.localStorage.getItem('track')) {
        var track = JSON.parse($window.localStorage.getItem('track'))
        if (Date.parse(new Date()) < Date.parse(track.expired)) {
            $scope.trackCode = track.code
        }
    } else {
        $scope.trackCode = null
    }
}

signController.$inject = ['$scope', '$uibModalInstance', '$window']

export {template, signController}
