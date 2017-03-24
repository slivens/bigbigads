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
    <p class="solution-product_title "><span>BIG BIG ADS</span> 
    </p>
    <h4 class="adsintroduce">Bigbigads is the only tool that offers feature of carousel ad, canvas and call to action filter. 
Successful Marketing requires fresh and accurate information so your ads are shown to the right audience with the most
 resonate images and call to action. With million+ monthly updated data and other unique features , 
 Bigbigads guarantee you a systematic advantage over competitors.
    </h4>
<!--Larger  Faster Easier
Cross-Filtering \Niche Market analysis \ Data Exporting
Bookmark to save and manage  Find an image that resonates  Custom Setting-->
    <div class="text-left productcard">
    <div class="row">
        <div class="col-md-4">
            <img src="static/images/product/3.png" class="fl">
            <div class="mal-2">
                <h5>Larger</h5>
                <p>Quantative & Qualitive data guarantee you the most reliable intelligence and best efforts </p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/1.png" class="fl mar-2">
            <div>
                <h5>Faster</h5>
                <p>Highest Response speed ever to save your time</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/2.png" class="fl mar-2">
            <div>
                <h5>Easier</h5>
                <p>User-friendly design, very easy to use. No misunderstanding, confusing & inconvenience</p>
            </div>
        </div>
    </div>    
        <!--seconed row-->
    <div class="row">
        <div class="col-md-4">
            <img src="static/images/product/6.png" class="fl mar-2">
            <div>
                <h5>Cross-Filtering</h5>
                <p>Strongest cross-filtering ever to narrow down the results, find out your winning ads in seconds.</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/7.png" class="fl mar-2">
            <div>
                <h5>Niche Market analysis</h5>
                <p>Enter a niche keyword to learn the analysis for the market, top player, market share, ad structure</p>
            </div> 
        </div>
        <div class="col-md-4">
            <img src="static/images/product/8.png" class="fl mar-2">
            <div>
                <h5>Data Exporting</h5>
                <p>Export data in .xls format for further analysis</p>
            </div>
        </div>
    </div>
        <!--thired row-->
    <div class="row">
        <div class="col-md-4">
            <img src="static/images/product/5.png" class="fl mar-2">
            <div>
                <h5>Bookmark to save and manage</h5>
                <p>Save winning ad in your niche market, hop on wave someone else created.</p>
            </div>
        </div>
        <div class="col-md-4">
            <img src="static/images/product/6.png" class="fl mar-2">
            <div>
                <h5>Find an image that resonates</h5>
                <p>Send your brand voice to your customers through images, which they resonate with your brand.</p>
            </div>
        </div>
        
        
        <div class="col-md-4">
            <img src="static/images/product/7.png" class="fl mar-2">
            <div>
                <h5>Custom Setting</h5>
                <p>Customize your own searching filter default setting. Give different setting a name to relax your daily job.</p>
            </div>    
        </div>
    </div>

         

    <div class="clearfix text-center">
        <a class="btn btn-clg" href="">Try it For Free</a>
    </div>
        
        <p class="solution-product_tip text-center">No credit card required-Get started in seconds.</p>
    </div>
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
