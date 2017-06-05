<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>Bigbigads</title>

    <link rel="stylesheet" type="text/css" href="./dist/home.css">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
</head>
<body>
@include('tpl.header')
<section class="search">
    <div class="container">
    <div class="search-title">Facebook Ads have never been so easy </div>
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
<!-- bootstraps swiper-slide -->
<section class="slider">
    <div class="container">
        <div class="col-md-7 col-xs-7 slider-left">
            <div class="swiper-container" id="slider" >
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img data-normal="static/images/show/01.jpg"/>
                        <div class="swiper-adsbar row absolute">
                            <div class="col-md-2 adsbigword">YES!</div>
                            <div class="col-md-6 adstext">I wanna learn my competitor's marketing strategy Now!</div>
                            <div class="col-md-4 adsbutton text-center">
                                <a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now </a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img data-normal="static/images/show/02.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">yes!</div>
                            <div class="col-md-6 adstext">I'd like to save hundreds of hours doing tedious research!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now </a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img data-normal="static/images/show/03.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">yes!</div>
                            <div class="col-md-6 adstext">Help me to know more!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now</a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img data-normal="static/images/show/04.jpg"/>
                        <div class="swiper-adsbar row">
                            <div class="col-md-2 adsbigword">yes!</div>
                            <div class="col-md-6 adstext">I'd like to use your benefit to comfort my job!</div>
                            <div class="col-md-4 adsbutton text-center"><a href="/app/adsearch" class="btn btn-clg slider-btn">Try It Now</a></div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img data-normal="static/images/show/05.jpg"/>
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
                    <p class="slider-title">The Freshest Data</p>
                    <p class="slider-desc">With a database that has 700K<sup>+</sup> monthly updates, Bigbigads offers the most authentic and freshest data for your business.  No more guessing which ads your competitors are making money with. 
</p>
                </li>
                <li>
                    <p class="slider-title">Superior filtering</p>
                    <p class="slider-desc">Bigbigads’ filtering allows you to narrow down huge amounts of data in record time.  Say goodbye to endless scrolling.  </p>
                </li>
                <li>
                    <p class="slider-title">Uncover More About An Ad </p>
                    <p class="slider-desc">With our unique features (Canvas/Carousel/Landing Page/Audience Targeting etc.), you can see the comprehensive info that others can’t.  There will be no more questioning as to why certain ads attract so many reactions.  </p>
                </li>
                <li>
                    <p class="slider-title">Easy To Start</p>
                    <p class="slider-desc">Our user-friendly design, video tutorials & blog articles make it easy to get started, and we use next-generation data search for the highest search efficiency ever.   
</p>
                </li>
                <li>
                    <p class="slider-title">Search, Save &amp; Success</p>
                    <p class="slider-desc">You can use the bookmark feature to save your favorite ad or advertiser.  You can also implement an alert for tracking. In addition, as you find winners, you can set up a different file for each niche market. </p>
                </li>
            </ul>
            
        </div>
    </div>
</section>

<section class="video">
    <div class="container">
        <div class="col-md-5">
            <p class="video-title ">Keep pace with Facebook Ads! </p>
            <a href="/app/adsearch" class="btn btn-lgm font_22">Try It Now</a>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-6 ">
            <div class="video-content">
            <img  width="100%" src="/images/youtube.jpeg" id="youtubeImage"  />     
           <iframe id="youtubeFrame" class="hidden" width="100%" height="360" data-url="https://www.youtube.com/embed/rEDusFMbVvk?autoplay=1" frameborder="0" allowfullscreen></iframe> 
        <!-- <span class="video-content_play" data-toggle="modal" data-target="#md_video"></span> --></div>
    </div>
    </div>
</section>

<section class="solution">
    <div class="container" style="width: 100%">
        <div class="solution-title">The Best Solution</div>
        <div class="solution-title_sub">For Ad Buyers, Manufacturers, Ad Agencies and Researchers</div>
        <ul class="solution-itmes">
            <li class="solution-item">
                <img data-normal="static/images/solution1.png" class="img-circle bg"/>
                <div class="solution-item_number clearfix">
                    <span class="pull-left">4,000,000</span>
                    <span class="solution-item_symbol pull-left">+</span></div>
                <div class="solution-item_desc">Ads</div>
            </li>
            <li class="solution-item">
                <img data-normal="static/images/solution2.png"  class="img-circle bg"/>
                <div class="solution-item_number clearfix">       
                    <span class="pull-left">1,000,000</span>
                    <span class="solution-item_symbol pull-left">+</span>
                </div>
              
                <div class="solution-item_desc"> Advertisers</div>
            </li>
            <li class="solution-item">
                <img data-normal="static/images/solution3.png"  class="img-circle bg"/>
                <div class="solution-item_number clearfix" style="width: 180px;margin:0 auto">
                    <span class="pull-left">700,000</span>
                    <span class="solution-item_symbol pull-left">+</span>
                </div>
                <div class="solution-item_desc">Monthly Updates </div>
            </li>
            <li class="solution-item">
                <img data-normal="static/images/solution4.png"  class="img-circle bg"/>
                <div class="solution-item_number clearfix" style="width: 66px;margin:0 auto"><span class="pull-left">90</span>
                    <span class="solution-item_symbol pull-left">+</span>
                </div>
                <div class="solution-item_desc"> Languages </div>
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

                    @foreach ($recents as $post)
                    <div class="blog-slider_item swiper-slide">
                        @if (!empty($post->image))
                        <img data-normal="{{ Voyager::image($post->image) }}" alt="{{$post->title}}" data-type="post">
                        @endif
                        <a href="{{url('post', ['id' => $post->id])}}">
                            <p class="blog-slider_title">{{ $post->title }}</p>
                            <p class="blog-slider_time">{{ (new Carbon\Carbon($post->created_at))->toFormattedDateString() }}</p>
                        </a>
                    </div>
                    @endforeach
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

<!--set modal to play video-->
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
<script type="text/javascript" src="dist/vendor.js?v=1"></script>
<script type="text/javascript" src="dist/home.js?v="></script>
</body>
</html>
