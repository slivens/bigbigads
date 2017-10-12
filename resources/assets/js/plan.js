import './../sass/plan.scss'

(function() {
    function turnToPricing() {
        $("#stand-card-div").addClass("transform-rotatey")
    }
    function turnToStand() {
        $("#stand-card-div").removeClass("transform-rotatey")
    }
    /* 页面内锚点连接上滑 */
    function linkToUp() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var $target = $(this.hash)
            $target = ($target.length && $target) || $('[name=' + this.hash.slice(1) + ']')
            if ($target.length) {
                var targetOffset = $target.offset().top
                $('html,body').animate({
                    scrollTop: targetOffset
                },
                300)
                return false
            }
        }
    }
    /*
    * 当上滑到某种程度的时候，向下的图标隐藏
    */
    function hideTheDownIcon() {
        var windowHight = $(window).height()
        var marginTop = $("#plan-table")[0].getBoundingClientRect().top // 获取不到下边距
        var marginBottom = windowHight - marginTop
        if (marginBottom > 50) {
            $("#click-to-down").addClass("hidden")
        } else {
            $("#click-to-down").removeClass("hidden")
        }
    }
    $("#stand-card .adscard-btn").on("click", turnToPricing)
    $("#pricingcard-back").on("click", turnToStand)
    $("#app-to-top .btn").on("click", linkToUp)
    $("#click-to-down").on("click", linkToUp)
    hideTheDownIcon()
    $(window).on("scroll", hideTheDownIcon)
})()
