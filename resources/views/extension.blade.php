<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bigbigads Extension | Largest Facebook Ad Examples</title>
    <meta name="description" content="A Facebook ads spy tool to help you find out the competitor marketing strategy, ad creatives, ad insight.">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
    <!-- TODO: home.css should be removed in later version-->
    <link href="{{bba_version('home.css')}}" rel="stylesheet">
    <link href="{{bba_version('extension.css')}}" rel="stylesheet">
    @include('tpl.script')
</head>
<body id="extension">
@include('tpl.header')
<section class="exten-banner text-center">
    <div class="container">
        <p class="ads-font-48 exten-title ads-weight-600">Get in-depth Facebook Ad analysis with one click.</p>
        <p class="ads-font-28 exten-suptitle">Simple &amp; Powerful</p>
        <div class="row bnner-img">
            <div class="video-wrapper">
                <div id="youtubeImage" class="youtube-image">
                    <i class="fa fa-youtube-play play-button"></i>
                </div>
                <iframe id="youtubeFrame" class="youtube-video hidden" width="100%" height="100%" data-url="https://www.youtube-nocookie.com/embed/9cfmC1rpEEI?rel=0&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>
<section class="exten-content">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-xs-12 instro-wrapper">
                <img src="./dist/images/extension/icon_01.png" class="instro-img" alt="">
                <p class="ads-font-24 ads-weight-600 instro-title">
                    Insight
                </p>
                <div class="instro-text">
                   One Click to see all ads on your newsfeed, check  publisher's history ads and ad insight.
                </div>
            </div>
            <div class="col-sm-4 col-xs-12 instro-wrapper">
                <img src="./dist/images/extension/icon_02.png" class="instro-img" alt="">
                <p class="ads-font-24 ads-weight-600 instro-title">
                    Inspiration
                </p>
                <div class="instro-text">
                    Find out successful ads in seconds. Get inspiration for your next ad campaign.
                </div>
            </div>
            <div class="col-sm-4 col-xs-12 instro-wrapper">
                <img src="./dist/images/extension/icon_03.png" class="instro-img" alt="">
                <p class="ads-font-24 ads-weight-600 instro-title">
                    Improve
                </p>
                <div class="instro-text">
                    Create more ads that resonate with your customer. Promote your business with lower cost.

                </div>
            </div>
        </div>
    </div>

</section>
<section class="exten-bottom">
<div class="container bottom-con">
    <p class="ads-font-32 ads-weight-600 bottom-text">What are you waiting for?</p>
    <!-- <a id="downloadExtension" href="//chrome.google.com/webstore/detail/bigbigadsfacebook-ad-exam/aeicgjbjcnodlaomefmanfbkhpcdlcbk" class="btn bottom-btn ads-font-18 ads-weight-600 text-center download-btn"><i class="fa fa-download down-icon"></i> Download Extension Now!</a>
 -->
    <button id="downloadExtension" href="#" class="btn bottom-btn ads-font-18 ads-weight-600 text-center download-btn"><i class="fa fa-download down-icon"></i> Download Extension Now!</button>
</div>
</section>
@include('tpl.footer')
</body>
<script type="text/javascript" src="{{bba_version('vendor.js')}}" defer></script>
<script type="text/javascript" src="{{bba_version('extension.js')}}" defer></script>
<script type="text/javascript" src="{{bba_version('home.js')}}" defer></script>
</html>

