<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bigbigads</title>
    <link rel="stylesheet" type="text/css" href="./static/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./static/custom.css">
    <link rel="stylesheet" href="static/swiper.css">
    <script src="static/jquery-3.1.1.js"></script>
    <script src="static/swiper.jquery.js"></script>
    <script src="static/bootstrap.js"></script>
</head>
<body>
@include('tpl.header')
<section class="search">
    <div class="container">
    <div class="search-title">Facebook Ads has never been so easy</div>
    <div class="search-title_sub">Inspire your Facebook Ads with Bigbigads’s 1 million+ Ads datab</div>
    <form class="margin-bottom30 " style="margin: 0 16% 30px" action="/app/adsearch" method="GET">
        <div class="input-group" style="width: 100%">
            <input type="search" placeholder="Please Enter a brand name or site name" class="search-input" style="    width: 100%;" name="searchText"/>
            <input type="submit" class="btn search-btn" value="Search">
        </div>
    </form>
        <div style="margin-bottom: 80px">
            <a class="btn  btn-clg" href="{{url('/register')}}">Try it For Free</a>
            <a class="search-more" href="">or Learn more</a>
        </div>

    </div>
</section>
<!-- 轮播图 -->
<section class="slider">
    <div class="container">
        <div class="col-md-7 slider-left">
            <div class="swiper-container" id="slider" >
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="static/images/slider1.png"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/slider1.png"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/slider1.png"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="slider-right col-md-4" >
            <ul class="slider-items">
                <li class="active">
                    <p class="slider-title">Filter</p>
                    <p class="slider-desc">Find your most interested products or activitisers
                        with Bigbigads’ professional filter,you can filter the
                        resulit by position category,formatetc.</p>
                </li>
                <li>
                    <p class="slider-title">Ranking</p>
                    <p class="slider-desc">Ranking Download Advertiser Search Analysis</p>
                </li>
                <li>
                    <p class="slider-title">Download</p>
                    <p class="slider-desc">Ranking Download Advertiser Search Analysis</p>
                </li>
                <li>
                    <p class="slider-title">Advertiser Search</p>
                    <p class="slider-desc">Ranking Download Advertiser Search Analysis</p>
                </li>
                <li>
                    <p class="slider-title">Analysis</p>
                    <p class="slider-desc">Ranking Download Advertiser Search Analysis</p>
                </li>
            </ul>
            <a href="" class="btn btn-clg slider-btn">Try it For Free</a>
        </div>
    </div>
</section>

<section class="video">
    <div class="container">
        <div class="col-md-5">
            <p class="video-title ">Keep you in pace with Facebook Ads!</p>
            <a href="" class="btn btn-lgm font_22">Try it For Free</a>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 ">
            <div class="video-content">
                <span class="video-content_play" data-toggle="modal" data-target="#md_video"></span>
            </div>
            <!--<iframe class=" " src="https://www.youtube.com/embed/3-NTv0CdFCk" frameborder="0"-->
                    <!--allowfullscreen></iframe>-->
        </div>
    </div>
</section>

<section class="solution">
    <div class="container">
        <div class="solution-title">The Best Solution</div>
        <div class="solution-title_sub">For Ad Buyer, Manufacturer, Ad Agency&Researchers</div>
        <ul class="solution-itmes">
            <li class="solution-item">
                <img src="static/images/solution1.png" class="img-circle bg"/>
                <div class="solution-item_number">1,000,000+</div>
                <div class="solution-item_desc">Advertise</div>
            </li>
            <li class="solution-item">
                <img src="static/images/solution2.png"  class="img-circle bg"/>
                <div class="solution-item_number">300,000+</div>
                <div class="solution-item_desc">advertiser</div>
            </li>
            <li class="solution-item">
                <img src="static/images/solution3.png"  class="img-circle bg"/>
                <div class="solution-item_number">300,000+</div>
                <div class="solution-item_desc">Monthy Ads Update</div>
            </li>
            <li class="solution-item">
                <img src="static/images/solution4.png"  class="img-circle bg"/>
                <div class="solution-item_number">297</div>
                <div class="solution-item_desc">All Countries</div>
            </li>
        </ul>
    </div>
</section>
<!--blog slider-->
<section class="blog">
    <div class="container ">
        <p class="blog-title text-center">From Our Blog </p>
        <div class="blog-slider clearfix ">
            <div class="swiper-container" id="blog_slider">
                <div class="swiper-wrapper">
                    <div class="blog-slider_item swiper-slide">
                        <img src="static/images/blog1.jpg" alt="">
                        <a href="/">
                            <p class="blog-slider_title">Hot jar is a new and easy to way to truly understand 1.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog1.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand 2.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog1.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand 3.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>


                    <div class="blog-slider_item swiper-slide">
                        <img src="static/images/blog1.jpg" alt="">
                        <a href="/">
                            <p class="blog-slider_title">Hot jar is a new and easy to way to truly understand 4.</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog1.jpg" alt="">
                            <p class="blog-slider_title">Hotjar is a new and easy to way to truly understand .</p>
                            <p class="blog-slider_time">2017.02.14</p>
                        </a>
                    </div>
                    <div class="blog-slider_item swiper-slide">
                        <a href="/">
                            <img src="static/images/blog1.jpg" alt="">
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
