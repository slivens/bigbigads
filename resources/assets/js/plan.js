(function() {
    $("#stand-card .adscard-btn").on("click", turnToPricing)
    $("#pricingcard-back").on("click", turnToStand)

    function turnToPricing() {
        $("#stand-card-div").addClass("transform-rotatey")
    }

    function turnToStand() {
        $("#stand-card-div").removeClass("transform-rotatey")
    }
})()
