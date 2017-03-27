<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bigbigads-Product</title>
    <link rel="stylesheet" type="text/css" href="./static/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./static/custom.css">
    <link rel="stylesheet" href="static/swiper.css">
    <link rel="stylesheet" type="text/css" href="./static/demo.css">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
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
                        <img src="static/images/show/product_03.jpg"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/show/product_02.jpg"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="static/images/show/product_01.jpg"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="slider-right col-md-4" >
            <ul class="slider-items">
                <li class="active">
                    <p class="slider-title">Advertiser Search</p>
                    <p class="slider-desc">Find out all advertisers for a specific product or service.</p>
                </li>
                <li>
                    <p class="slider-title">Audience Targeting</p>
                    <p class="slider-desc">Help you to know your customers and competitors better</p>
                </li>
                <li>
                    <p class="slider-title">Niche Market Analysis</p>
                    <p class="slider-desc">Enhance your ability to understand a new niche market.</p>
                </li>
            </ul>
            <a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now</a>
        </div>
    </div>
</section>




<div class="solution-product clearfix text-center container">
    <p class="solution-product_title "><span>BIG BIG ADS</span> 
    </p>
    <h4 class="adsintroduce text-left">&nbsp;&nbsp;&nbsp;
  &nbsp;Intelligence is power-if you have the right tools. Bigbigads transforms competitor intelligence data into meaningful insights that lead to competitive advantage for your company.</h4>
    <hr>
    <h4 class="adsintroduce text-left">&nbsp;&nbsp;&nbsp;
  &nbsp;
Successful Marketing requires fresh and accurate information so your ads are shown to the right audience with the most resonate images and call to action. With million+ monthly updated data and other unique features , Bigbigads guarantee you a systematic advantage over competitors.
    </h4>
<!--Larger  Faster Easier
Cross-Filtering \Niche Market analysis \ Data Exporting
Bookmark to save and manage  Find an image that resonates  Custom Setting-->
    <div class="text-left productcard">
    <div class="row">
        <div class="col-md-4">

            <div class="pull-left card-left">
                <i class="producticon demoicon icon-trophy"></i>
            </div>
            <div class=" clearfix card-right">
                <h5>Larger</h5>
                <p>Quantative & Qualitive data guarantee you the most reliable intelligence and best efforts </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left clearfix card-left">
                <i class="producticon demoicon icon-rocket"></i>
            </div>
            <div class="card-right">
                <h5>Faster</h5>
                <p>Highest Response speed ever to save your time</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-leaf"></i>
            </div>
            <div class="card-right">
                <h5>Easier</h5>
                <p>User-friendly design, very easy to use. No misunderstanding, confusing & inconvenience</p>
            </div>
        </div>
    </div>    
        <!--seconed row-->
    <div class="row">
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-filter"></i>
            </div>
            <div class="card-right ">
                <h5>Cross-Filtering</h5>
                <p>Strongest cross-filtering ever to narrow down the results, find out your winning ads in seconds.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-stats-bars"></i>
            </div>
            <div>
                <h5>Niche Market analysis</h5>
                <p>Enter a niche keyword to learn the analysis for the market, top player, market share, ad structure</p>
            </div> 
        </div>
        <div class="col-md-4">
            <div class="pull-left  card-left">
                <i class="producticon demoicon icon-stats-dots"></i>
            </div>
            <div  class="card-right">
                <h5>Data Exporting</h5>
                <p>Export data in .xls format for further analysis</p>
            </div>
        </div>
    </div>
        <!--thired row-->
    <div class="row">
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-price-tags"></i>
            </div>
            <div  class="card-right">
                <h5>Bookmark to manage</h5>
                <p>Save winning ad in your niche market, hop onto wave someone else created.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-images"></i>
            </div>
            <div  class="card-right">
                <h5>Find an image that resonates</h5>
                <p>Send your brand voice to your customers through images, which they resonate with your brand.</p>
            </div>
        </div>
        
        
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-cogs"></i>
            </div>
            <div  class="card-right">
                <h5>Custom Setting</h5>
                <p>Customize your own searching filter default setting. Give different setting a name to relax your daily job.</p>
            </div>    
        </div>
    </div>

         

    <div class="clearfix text-center">
        <a class="btn btn-clg" href="/app/adsearch">Try It Now</a>
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
