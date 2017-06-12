<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bigbigads-Product</title>
    <link rel="stylesheet" type="text/css" href="./static/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./static/custom.css">
       <link rel="stylesheet" href="./static/test.css">
    <link rel="stylesheet" href="static/swiper.css">
    <link rel="stylesheet" type="text/css" href="./static/demo.css">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
</head>
<body>
@include('tpl.header')
<!-- swiper-wrapper -->
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
                    <p class="slider-desc">Find all advertisers for a specific product or service. </p>
                </li>
                <li>
                    <p class="slider-title">Audience Targeting</p>
                    <p class="slider-desc">Know your customers and competition better. </p>
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
  &nbsp;Intelligence is power – if you have the right tools. Bigbigads transforms competitor intelligence data into meaningful insights that lead to a competitive advantage for your company. </h4>
    <hr>
    <h4 class="adsintroduce text-left">&nbsp;&nbsp;&nbsp;
  &nbsp;Successful marketing requires fresh and accurate information so your ads can be shown to the right audience with the most impactful images and calls to action. With 1 million<sup>+</sup> monthly updates ads and other unique features, Bigbigads guarantees you a systematic advantage over competitors. </h4>
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
                <h5>Large</h5>
                <p>Quantitative &amp; qualitative data guarantees you the most reliable intelligence and best results. </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left clearfix card-left">
                <i class="producticon demoicon icon-rocket"></i>
            </div>
            <div class="card-right">
                <h5>Fast</h5>
                <p>We have the highest response speed of any comparable software to save you time.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-leaf"></i>
            </div>
            <div class="card-right">
                <h5>Easy</h5>
                <p>Our user-friendly design is specially created to be as intuitive &amp; easy to use as possible. </p>
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
                <h5>Cross Filtering</h5>
                <p>Super-strong cross filtering allows you to narrow down results and find your winning ads in seconds.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-stats-bars"></i>
            </div>
            <div>
                <h5>Niche Market Analysi</h5>
                <p>Enter a niche keyword to analyze the market, top player, market share &amp; ad structure.  </p>
            </div> 
        </div>
        <div class="col-md-4">
            <div class="pull-left  card-left">
                <i class="producticon demoicon icon-stats-dots"></i>
            </div>
            <div  class="card-right">
                <h5>Data Exporting</h5>
                <p>You can export data into an .xls format spreadsheet for further analysis offline.</p>
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
                <h5>Bookmark to Manage</h5>
                <p>Save winning ads in your niche market and hop onto a wave that someone else created. </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-images"></i>
            </div>
            <div  class="card-right">
                <h5>Find An Image That Resonates </h5>
                <p>Easily see which images are getting the best engagement and use them to build engagement with your own target audience(s).</p>
            </div>
        </div>
        
        
        <div class="col-md-4">
            <div class="pull-left card-left">
                <i class="producticon demoicon icon-cogs"></i>
            </div>
            <div  class="card-right">
                <h5>Custom Settings</h5>
                <p>Customize your own search filter default settings and name the different settings to make your job easier. </p>
            </div>    
        </div>
    </div>

         

    <div class="clearfix text-center">
        <a class="btn btn-clg" href="/app/adsearch">Try It Now</a>
    </div>
        
        <p class="solution-product_tip text-center">No credit card required. Get started in seconds. </p>
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
        slider.slideTo($(this).index('.slider-items li'), 1000, false);//switch to the first slide, the rate of 1 second.
    });
</script>
</body>
</html>
