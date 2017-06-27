import 'bootstrap/dist/css/bootstrap.css';
import 'swiper/dist/css/swiper.css';
import './../sass/custom.scss';
import './../sass/demo.scss';
import Swiper from 'swiper';
import Layzr from 'layzr.js';
import moment from 'moment';
import 'js-url';
import 'bootstrap';
import 'animate.css/animate.min.css';


(function() {
    var slider = new Swiper('#slider', {
        slidesPerView: 1
    });

    $('.slider-items li').click(function() {
        $('.slider-items li').removeClass('active');
        $(this).addClass('active');
        slider.slideTo($(this).index('.slider-items li'), 1000, false); //switch to the first slide, the rate of 1 second.
    });

    var blog_slider = new Swiper('#blog_slider', {
        slidesPerView: 3,
        nextButton: '.blog-slider-next',
        prevButton: '.blog-slider-prev',
        spaceBetween: 30,
        loop: false,
        // Responsive breakpoints
        breakpoints: {
            // when window width is <= 320px
            320: {
                slidesPerView: 1,
                spaceBetween: 10
            },
            // when window width is <= 640px
            640: {
                slidesPerView: 2,
                spaceBetween: 30
            }
        }
    });

    var instance = Layzr({
        normal: 'data-normal',
        retina: 'data-retina',
        srcset: 'data-srcset',
        threshold: 100
    });

    instance.on('src:before', function(element) {
        if ($(element).data('type') == 'post') {
            var ele = $(element);
            var normal = ele.data('normal');
            ele.attr('data-normal', '/image?src=' + normal + '&width=' + ele.width());
            // console.log("post:", ele.attr('data-normal'));
        }
    });
    document.addEventListener('DOMContentLoaded', function(event) {
        $('#youtubeImage').click(function() {
            $('#youtubeFrame').attr('src', $('#youtubeFrame').data('url'));
            $('#youtubeFrame').removeClass('hidden');
            $(this).addClass('hidden');
        });
        instance
            .update() // track initial elements
            .check() // check initial elements
            .handlers(true) // bind scroll and resize handlers
        var track = url("?track");
        if (track) {
            var days = track.match(/\d\d$/);
            days = days ? Number(days[0]) : 90;
            window.localStorage.setItem('track', JSON.stringify({ "code": track, "expired": moment().add(days, 'days').format('YYYY-MM-DD') }));
        }
    });

    /*广告词动画切换*/
    toChangeWord(0);
})();

/*home界面的广告词切换*/
function toChangeWord(item) {
    var word = ["To see resonate image <br/>for ad designer",
        " To see resonate text  <br/>for copywriter",
        " To see resonate markeing strategy  <br/>for marketing planner",
        " To see competitive intelligence  <br/>for ad buyer "
    ];
    if (word.length <= item) {
        item = 0;
    }
    $("#changeWord").html(word[item]);
    $("#changeWord").addClass("fadeIn");
    setTimeout(function() {
        $("#changeWord").removeClass("fadeIn");
        setTimeout(function() {
            toChangeWord(item + 1);
        }, 500);
    }, 2500);
}
