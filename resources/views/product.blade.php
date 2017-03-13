<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bigbigads-Product</title>
    <link rel="stylesheet" type="text/css" href="./static/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./static/custom.css">
    <link rel="stylesheet" href="static/swiper.css">
</head>
<body>
@include('tpl.header')
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
                    <p class="slider-desc">Find your most interested products or activitisers with Bigbiga</p>
                </li>
                <li>
                    <p class="slider-title">Ranking</p>
                    <p class="slider-desc">Ranking Download Advertiser Search Analysis</p>
                </li>
                <li>
                    <p class="slider-title">Ranking</p>
                    <p class="slider-desc">Ranking Download Advertiser Search Analysis</p>
                </li>
            </ul>
            <a href="" class="btn btn-clg slider-btn">Try it For Free</a>
        </div>
    </div>
</section>


<div class="solution-product clearfix text-center container">
    <p class="solution-product_title ">An <span>All_in_one</span> solution-packed with powerful features</p>
    <div class="text-left">
        <div class="col-md-4">
            <img src="static/images/product/1.png" class="fl mar-2"/>
            <div>
                <h5>Unlimited Users</h5>
                <p>We have no user limits on any plan. Invite all your team and c</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/2.png" class="fl mar-2"/>
            <div>
                <h5>Multi-device Support</h5>
                <p>All our features track and work on Desktop,Mobile and Tablet v</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/3.png" class="fl"/>
            <div class="mal-2">
                <h5>Rectangle 2</h5>
                <p>Installation is quick and simple using one script with support</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/4.png" class="fl mar-2"/>
            <div>
                <h5>Powerful Targeting</h5>
                <p>Target visitors using URLs,device or custom javascript trigger</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/5.png" class="fl mar-2"/>
            <div>
                <h5>Unlimited Responses</h5>
                <p>There are no limits to how many responses you can collect using</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/6.png" class="fl mar-2"/>
            <div>
                <h5>Behabiour logic</h5>
                <p>Decide when feedback widgets should show such as on-exit inten</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/7.png" class="fl mar-2"/>
            <div>
                <h5>Ready Localized</h5>
                <p>Feedback tools come ready localized in over 40 languages.</p>
            </div>
            </li>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/8.png" class="fl mar-2"/>
            <div>
                <h5>Export & Share</h5>
                <p>Export responses you collect in CSV or XLSX fotmat. Share Heatm</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/9.png" class="fl mar-2"/>
            <div>
                <h5>Block IPs</h5>
                <p>Exculde tracking yourself, your team or you clients in Hotjar b</p>
            </div>
        </div>
    </div>


    <a class="btn btn-clg" href="">Try it For Free</a>
    <p class="solution-product_tip">No credit card required-Get started in seconds.</p>
</div>
@include('tpl.footer')
<script src="static/jquery-3.1.1.js"></script>
<script src="static/swiper.jquery.js"></script>
<script>
    var slider = new Swiper('#slider', {});

    $('.slider-items li').click(function(){
        $('.slider-items li').removeClass('active');
        $(this).addClass('active');
        slider.slideTo($(this).index('.slider-items li'), 1000, false);//切换到第一个slide，速度为1秒
    });
</script>
</body>
</html>
