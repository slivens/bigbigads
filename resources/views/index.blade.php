<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bigbigads</title>
    <link rel="stylesheet" type="text/css" href="./static/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./static/custom.css">
    <link rel="stylesheet" type="text/css" href="./static/demo.css">
    <link rel="stylesheet" href="static/swiper.css">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
    <script src="static/jquery-3.1.1.js"></script>
    <script src="static/swiper.jquery.js"></script>
    <script src="static/bootstrap.js"></script>
</head>
<body>
@include('tpl.header')
<section class="search">
    <div class="container">
    <div class="search-title">Facebook Ads has never been so easy</div>
    <div class="search-title_sub">To get inspired</div>
    <form class="margin-bottom30 " style="margin: 0 16% 30px" action="/app/adsearch" method="GET">
        <div class="input-group" style="width: 100%">
            <input type="search" placeholder="Please enter a keyword " class="search-input pull-left" style="width:100%;" name="searchText"/>
            <input type="submit" class="btn search-btn pull-left" value="Search">
        </div>
    </form>
        <div style="margin-bottom: 80px">
            <!-- <a class="btn  btn-clg" href="{{url('/register')}}">Try it For Free</a>
            <a class="search-more" href="">or Learn more</a> -->
        </div>

    </div>
</section>
<!-- 轮播图 -->
<section class="slider">
    <div class="container">
        <div class="col-md-7 col-xs-7 slider-left">
            <div class="swiper-container" id="slider" >
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="static/images/show/01.jpg"/>
                        <div class="swiper-adsbar row absolute">
                            <div class="col-md-2 adsbigword">YES!</div>
                            <div class="col-md-6 adstext">I wanna learn my competitor's marketing strategy Now!</div>
                            <div class="col-md-4 adsbutton text-center">
                                <a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now </a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/show/02.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">yes!</div>
                            <div class="col-md-6 adstext">I'd like to save hundreds of hours doing tedious research!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now </a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/show/03.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">yes!</div>
                            <div class="col-md-6 adstext">Help me to know more!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now</a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/show/04.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">yes!</div>
                            <div class="col-md-6 adstext">I'd like to use your benefit to comfort my job!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now</a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/show/05.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">YES!</div>
                            <div class="col-md-6 adstext">I want to hop onto the wave someone else created!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1 col-xs-1"></div>
        <div class="slider-right col-md-4 col-xs-4" >
            <ul class="slider-items">
                <li class="active">
                    <p class="slider-title">The Most Fresh Data</p>
                    <p class="slider-desc">With 700K+ monthly updated database, bigbigads offer the most authentic and fresh data for your business. No more guessing which ad your competitor are making money from.
</p>
                </li>
                <li>
                    <p class="slider-title">Strongest filter</p>
                    <p class="slider-desc">Strongest filter ever to narrow down huge data in a lightning fast way. Say goodbye to terrible experience of the endless page down work. </p>
                </li>
                <li>
                    <p class="slider-title">Uncover More About An Ad</p>
                    <p class="slider-desc">With our unique feature (Canvas/Carousel/Landing Page/Audience Targeting etc), you can reveal more comprehensive intelligence others never know. No more questioning why a logo only ad attract so many reactions. </p>
                </li>
                <li>
                    <p class="slider-title">Easy To Start</p>
                    <p class="slider-desc">X-generation database structure for highest search efficiency ever. User-friendly design, video tutorial, and article for usage. 
</p>
                </li>
                <li>
                    <p class="slider-title">Spy, Save &amp; Success</p>
                    <p class="slider-desc">Use the bookmark to save your favorite ad or advertiser, set an alert for tracking. Set up different file for each niche market, find out the winner and learn.</p>
                </li>
            </ul>
            
        </div>
    </div>
</section>

<section class="video">
    <div class="container">
        <div class="col-md-5">
            <p class="video-title ">Keep you in pace with Facebook Ads!</p>
            <a href="/app/adsearch" class="btn btn-lgm font_22">Try It Now</a>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 ">
            <div class="video-content">
                
            
            <iframe class="video-iframe" src="https://www.youtube.com/embed/zi8wZ0YPRWw" frameborder="0" allowfullscreen></iframe>
        <!-- <span class="video-content_play" data-toggle="modal" data-target="#md_video"></span> --></div>
    </div>
    </div>
</section>

<section class="solution">
    <div class="container">
        <div class="solution-title">The Best Solution</div>
        <div class="solution-title_sub">For Ad Buyer, Manufacturer, Ad Agency &amp; Researchers</div>
        <ul class="solution-itmes">
            <li class="solution-item">
                <img src="static/images/solution1.png" class="img-circle bg"/>
                <div class="solution-item_number clearfix">
                    <span class="pull-left">3,000,000</span>
                    <span class="solution-item_symbol pull-left">+</span></div>
                <div class="solution-item_desc">Ad data</div>
            </li>
            <li class="solution-item">
                <img src="static/images/solution2.png"  class="img-circle bg"/>
                <div class="solution-item_number clearfix">       
                    <span class="pull-left">1,000,000</span>
                    <span class="solution-item_symbol pull-left">+</span>
                </div>
              
                <div class="solution-item_desc">Advertiser</div>
            </li>
            <li class="solution-item">
                <img src="static/images/solution3.png"  class="img-circle bg"/>
                <div class="solution-item_number clearfix" style="width: 180px;margin:0 auto">
                    <span class="pull-left">700,000</span>
                    <span class="solution-item_symbol pull-left">+</span>
                </div>
                <div class="solution-item_desc">Monthly Updated</div>
            </li>
            <li class="solution-item">
                <img src="static/images/solution4.png"  class="img-circle bg"/>
                <div class="solution-item_number clearfix" style="width: 66px;margin:0 auto"><span class="pull-left">90</span>
                    <span class="solution-item_symbol pull-left">+</span>
                </div>
                <div class="solution-item_desc">Languages</div>
            </li>
        </ul>
    </div>
</section>
<!--blog slider-->
<section class="blog">
    <div class="container ">
        <p class="blog-head-title text-center">From Our Blog </p>
        <div class="blog-slider clearfix ">
            <div class="swiper-container" id="blog_slider">
                <div class="swiper-wrapper">
                    <div class="blog-slider_item swiper-slide">
                        <img src="static/images/blog/01.jpg" alt="">
                        <a href="/">
                            <p class="blog-slider_title">Hot jar is a new and easy to way to truly understand 1.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog/02.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand 2.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog/03.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand 3.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>


                    <div class="blog-slider_item swiper-slide">
                        <img src="static/images/blog/04.jpg" alt="">
                        <a href="/">
                            <p class="blog-slider_title">Hot jar is a new and easy to way to truly understand 4.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog/05.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand .</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog/06.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand .</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                </div>
                <!-- Add Arrows -->
                <div class="blog-slider-next blog-slider_btn"></div>
                <div class="blog-slider-prev blog-slider_btn"></div>
            </div>
        </div>
    </div>
</section>
@include('tpl.footer')
<!-- Modal -->

<!--弹窗播放-->
<div class="modal fade" id="md_video" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">video</h4>
            </div>


            <div class="modal-body">
                <div class="text-center">Video is coming...</div>
                <!-- <iframe class=" video-content" style="margin: 0" src="https://www.youtube.com/embed/3-NTv0CdFCk" frameborder="0"allowfullscreen ></iframe> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>

    var slider = new Swiper('#slider', {slidesPerView: 1});

    $('.slider-items li').click(function(){
        $('.slider-items li').removeClass('active');
        $(this).addClass('active');
        slider.slideTo($(this).index('.slider-items li'), 1000, false);//切换到第一个slide，速度为1秒
    });

    var blog_slider = new Swiper('#blog_slider', {
        slidesPerView: 3,
        nextButton: '.blog-slider-next',
        prevButton: '.blog-slider-prev',
        spaceBetween: 30,
        loop: true
    });
</script>
</body>
</html>
