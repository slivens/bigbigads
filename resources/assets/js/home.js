import Swiper from 'swiper';
import 'bootstrap';

(function () {
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
        loop: false
    });
})();
