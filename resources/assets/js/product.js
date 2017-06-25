

import Swiper from 'swiper';
(function() {
var slider = new Swiper('#slider', {});
    $('.slider-items li').click(function(){
        $('.slider-items li').removeClass('active');
        $(this).addClass('active');
        slider.slideTo($(this).index('.slider-items li'), 1000, false);//switch to the first slide, the rate of 1 second.
    });
})();