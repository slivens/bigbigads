import './../sass/plan.scss'

(function() {
    // 翻转到背面
    function turnToBack() {
        var cardName = $(this).attr("turn-back") // 获取要翻转卡片的名称
        $('[card-name=' + cardName + ']').addClass("transform-turn")
    }
    // 翻转到正面
    function turnToFront() {
        var cardName = $(this).attr("turn-front")
        $('[card-name=' + cardName + ']').removeClass("transform-turn")
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
    $("[turn-back]").on("click", turnToBack)
    $("[turn-front]").on("click", turnToFront)
    $("#app-to-top .btn").on("click", linkToUp)
    $("#click-to-down").on("click", linkToUp)
    hideTheDownIcon()
    $(window).on("scroll", hideTheDownIcon)
})()
