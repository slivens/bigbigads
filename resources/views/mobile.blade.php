<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" />
    <meta name="description" content="A Facebook ads spy tool to help you find out the competitor marketing strategy, ad creatives, ad insight.">
    <title>Bigbigads Home | The Largest Facebook Ad Examples to Smart Your Facebook Marketing</title>
    <link href="{{bba_version('mobile.css')}}" rel="stylesheet">
    <!-- Facebook Pixel Code -->
    <script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window,
        document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '1555915891116409'); // Insert your pixel ID here. 
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1555915891116409&ev=PageView&noscript=1" /></noscript>
    <!-- DO NOT MODIFY -->
    <!-- End Facebook Pixel Code -->
    @include('tpl.script')
</head>

<body id="mobile">
    <!--begin header-->
    <div class="header clearfix">
        <div class="pull-left">
            <a class="ads-logo-link" href="/home" title="">
                <img class="ads-logo-png" src="/../assets/global/img/logo2.png" alt="">
            </a>
        </div>
        <a href="/mobile" class="header-login-button pull-right">Login</a>
    </div>
    <!--content-->
    <div class="content">
        <!-- Content introduction -->
        <div class="introduce-div clearfix">
            <p class="ads-font-32 introduce-title text-center">Facebook Ad ExtensionTo See</p>
            <div class="change-word-wrapper">
                <p id="change-word" class="instroduce-bigword text-center animated">Eshop Platform</p>
            </div>
            <p class="text-center instroduce-text">One-click to do your social media marketing research!</p>
            
            <div class="video-wrapper">
                <div id="youtubeImage" class="youtube-image">
                    <i class="fa fa-youtube-play play-button"></i>
                </div>    
                <iframe id="youtubeFrame" class="youtube-video" width="100%" height="100%" data-url="https://www.youtube-nocookie.com/embed/9cfmC1rpEEI?rel=0&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>
            </div>
            
            <a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
            
            <div class="instroduce-data">
                <p class="ads-font-32 text-center instroduce-data-title">The Best Solution</p>
                <p class="text-center">For Business owner, Publisher and Ad agency
                </p>
                <div class="data-content">
                    <p class="data-number">
                        @if (isset($totalAdsNumber))
                        <span>{{ $totalAdsNumber }}<sup>+</sup></span></p>
                        @else
                        <span>15,000,000<sup>+</sup></span></p>
                        @endif
                    </p>
                    <p class="data-text">Ads</p>
                </div>
                <div class="data-content">
                    <p class="data-number">
                        <span>3,000,000<sup>+</sup></span></p>
                    <p class="data-text">Publishers</p>
                </div>
                <div class="data-content">
                    <p class="data-number">1,000,000<sup>+</sup></p>
                    <p class="data-text">Updates</p>
                </div>
                <a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
            </div>
            
        </div>
        <!--begin register-->
        <div id="ads-register" class="register-div clearfix">
            <p class="ads-font-28 text-center ads-reg-title">Your online advertising,
                <br/> at its best. </p>
            <p class="ads-reg-text ads-font-16 text-center">Bigbigads' facebook ad examples make sure you to create your Low-Cost, High-Performance ad campaign. </p>
            <a class="ads-reg-btn btn socialite reg-with-fb" href="/socialite/facebook">
                <i class="fa fa-facebook-square reg-btn-icon"></i>
                <span class="ads-font-18 reg-btn-text">Sign Up With Facebook</span>
            </a>
            <a class="ads-reg-btn btn socialite reg-with-linkedin" href="/socialite/linkedin">
                <i class="fa fa-linkedin-square reg-btn-icon"></i>
                <span class="ads-font-18 reg-btn-text">Sign Up With Linkedin</span>
            </a>
            <a class="ads-reg-btn btn socialite reg-sub-btn" href="/socialite/google">
                <i class="fa fa-google-plus-square reg-btn-icon"></i>
                <span class="ads-font-18 reg-btn-text">Sign Up With Google+</span>
            </a>
            <p class="reg-policy-text ads-font-14 text-center">By signing up, you agree to the
                 <a href="terms_service">Terms and Conditions</a>
                  and 
                 <a href="privacy_policy ">Privacy Policy</a>. You also agree to receive product-related emails from Bigbigads from which you can unsubscribe at any time.
                 </p>
            <p class="ads-reg-line text-center hidden">
                <span class="ads-underline"></span>
                <span class="ads-font-16 reg-line-text">or</span>
                <span class="ads-underline"></span>
            </p>
        </div>
        <!--end register-->
        <!--show ads-->
        <div class="ads-show">
            <div class="show-content">
                <p class="show-title ads-font-24 text-center">Leverage on your competitor's profitable marketing now
                </p>
                <img src="static/images/home/show_01.jpg" alt="" class="show-img ads-center">
                <p class="ads-font-22 show-ins-title text-center">Facebook Marketing Strategy</p>
                <p class="ads-font-16 show-ins-text text-center">In a digital world, your competitor's marketing strategy is evolving quickly. Do you know what they are up to for an upcoming festival? Bigbigads does.</p>
                <a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
            </div>
            <div class="show-content">
                <p class="show-title ads-font-24 text-center">Build a Better Ad Campaign Through Competitive Intelligence</p>
                <img src="static/images/home/show_02.jpg" alt="" class="show-img ads-center">
                <p class="ads-font-22 show-ins-title text-center">Competitive Intelligence</p>
                <p class="ads-font-16 show-ins-text text-center">Find new competitors you don't know, track the ad campaign of existing competitors, find their winning ads, use the advantage to create your ads more efficiently.</p>
                <a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
            </div>
            <div class="show-content">
                <p class="show-title ads-font-24 text-center">Accelerate success for your facebook marketing</p>
                <img src="static/images/home/show_03.jpg" alt="" class="show-img ads-center">
                <p class="ads-font-22 show-ins-title text-center">Creative inspiration</p>
                <p class="ads-font-16 show-ins-text text-center">Find out how to sell product with social proof, break through culture difference for the audience in a new geo, know what kind of ad resonates with the audience in a specific niche market, know your potential customer better.</p>
            </div>
            <a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
        </div>
    </div>
    @include('tpl.mobile_footer')
    <div class="footer"></div>
    <!--end header-->
    <script type="text/javascript" src="{{bba_version('vendor.js')}}" defer></script>
    <script type="text/javascript" src="{{bba_version('mobile.js')}}" defer></script>
</body>

</html>