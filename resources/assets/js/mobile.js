import 'bootstrap/dist/css/bootstrap.css';
import './../sass/demo.scss';
import './../sass/mobile.scss';
import 'animate.css/animate.css';
import 'font-awesome/css/font-awesome.min.css';
/*$(document).ready(function(){
    changeWord(0)
    $('a[href="#ads-register"]').on("click",linkToUp)
})*/
(function() {
    changeWord(0)
    $('a[href="#ads-register"]').on("click", linkToUp)

    /*动态换词*/
    function changeWord(item) {
        var word = ["Ad Creatives", " Audience Targeting", "Ad Run Time", "Tracking Tool", "Eshop Platform"];
        if (word.length <= item) {
            item = 0;
        }
        $("#changeWord").html(word[item]);
        $("#changeWord").addClass("fadeIn");
        setTimeout(function() {
            $("#changeWord").removeClass("fadeIn");
            setTimeout(function() {
                changeWord(item + 1);
            }, 500);
        }, 1500);

    }


    /*页面内锚点连接上滑*/
    function linkToUp() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var $target = $(this.hash);
            $target = $target.length && $target || $('[name=' + this.hash.slice(1) + ']');
            if ($target.length) {
                var targetOffset = $target.offset().top;
                $('html,body').animate({
                        scrollTop: targetOffset
                    },
                    300);
                return false;
            }
        }
    }
})();
