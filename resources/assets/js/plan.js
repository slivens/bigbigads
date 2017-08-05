(function() {
    $("#stand-card .adscard-btn").on("click", turnToPricing)
    $("#pricingcard-back").on("click", turnToStand)
    $("#app-to-top .btn").on("click", linkToUp)

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
})()
