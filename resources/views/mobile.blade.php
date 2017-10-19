<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <!-- <link rel="stylesheet" type="text/css" href="./static/bootstrap.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="./static/mobile.css"> -->
    <!-- <link rel="stylesheet" href="./static/animate.min.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="./static/demo.css"> -->
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" />
    <meta name="description" content="A Facebook ads spy tool to help you find out the competitor marketing strategy, ad creatives, ad insight.">
    <title>Bigbigads Home | The Largest  Facebook Ad Examples to Smart Your Facebook Marketing</title>
    <link href="{{bba_version('mobile.css')}}" rel="stylesheet">
    @include('tpl.script')
</head>
<body id="new_index">
    <!--begin header-->
    <div class="header clearfix">
        <div class="pull-left">
            <a class="ads-logo-link" href="/home" title="">
                <img class="ads-logo-png" src="/../assets/global/img/logo2.png" alt="">
            </a>
        </div>
        <!-- <a class="ads-menu text-center">
            <i class="ads-menu-icon fa fa-reorder"></i>
        </a> -->

        <!--toggle button-->
        <a href="/mobile" class="header-login-button pull-right">Login</a>
    </div>

    <!--content-->
    <div class="content">
        
        <!-- Content introduction -->
        <div class="introduce-div clearfix">
            <p class="ads-font-32 introduce-title text-center">Largest Facebook Ad Examples To See:</p>
            <p id="changeWord" class="instroduce-bigword text-center animated">Eshop Platform</p>
            <p class="text-center instroduce-text">which offer you systematic advantage compare to your competitors</p>
            <div class="instroduce-data">
                
                <div class="data-content data-top">
                    <div class="data-roomdiv data-roomdiv-left">
                        <p class="data-number">
                        @if (isset($totalAdsNumber))
                        <span>{{ $totalAdsNumber }}<sup>+</sup></span></p>
                        @endif
                        <p class="data-text">Ads</p>
                    </div>
                    <div class="data-roomdiv">
                        <p class="data-number">
                        <span class="" id="adsernumber">2,054,000<sup>+</sup></span></p>
                        <p class="data-text">Advertisers</p>
                    </div>
                </div>
                <div class="data-content data-bottom">
                    <div class="data-roomdiv data-roomdiv-left">
                        <p class="data-number">1,000,000<sup>+</sup></p>
                        <p class="data-text">Monthly Updates</p>
                    </div>
                    <div class="data-roomdiv">
                        <p class="data-number">90<sup>+</sup></p>
                        <p class="data-text">Languages</p>
                    </div>
                </div>
                <p class="ads-font-32 text-center instroduce-data-title">The Best Solution</p>
                <p class="text-center">For Advertisers，Agencies，Ad network &amp; Publishers
                </p>
            </div>
            <a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
        </div>

        <!--begin register-->
        <div id="ads-register" class="register-div clearfix">
            <p class="ads-font-28 text-center ads-reg-title">Your online advertising,<br/> at its best. </p>
            <p class="ads-reg-text ads-font-16 text-center">Bigbigads'  facebook ad examples make sure you to create your Low-Cost, High-Performance ad campaign. </p>
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
            <p class="ads-reg-line text-center">
                <span class="ads-underline"></span>
                <span class="ads-font-16 reg-line-text">or</span> 
                <span class="ads-underline"></span>
            </p>
            <form class="form-inline reg-input-form" method="POST" action="{{ url('/register') }}">
                 {{ csrf_field() }}
                <div class="reg-inp-email">
                    <div class="form-group from-group-fname{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="sr-only" for="reg-email" class = "control-label">Email Address</label>
                            <input type="email" class="form-control" id="email" placeholder="Email" maxlength="72" name="email" value="{{ old('email') }}" required>
                            
                    </div>
                    @if ($errors->has('email'))
                        <span class="help-block err-remin">
                            {{ $errors->first('email') }}
                        </span>
                    @endif
                </div>
                <div class="reg-inp-name">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="sr-only" for="reg-Name" class = "control-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{ old('name') }}" required>    
                            
                    </div>
                    @if ($errors->has('name'))
                        <span class="help-block err-remin">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
                
                <div class="reg-inp-password">
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="sr-only" for="reg-password" class = "control-label">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                    </div>
                    @if ($errors->has('password'))
                            <span class="help-block" err-remin>
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                    @endif

                </div>
                <input type="hidden" name="track" value="{{ request()->get('track')}}" />
                <button type="submit" class="btn reg-sub-btn ads-reg-button ">Try it Now <small>It's free</small></button>
                <p class="reg-policy-text ads-font-14 text-center">By signing up, you agree to the
                 <a href="terms_service">Terms and Conditions</a>
                  and 
                 <a href="privacy_policy ">Privacy Policy</a>. You also agree to receive product-related emails from Bigbigads from which you can unsubscribe at any time.
                 </p>
            </form>
        </div>
        <!--end register-->
        <!--show ads-->
        <div class="ads-show">
            <div class="show-content">
                <p class="show-title ads-font-24 text-center">Leverage on your competitor's profitable marketing  now
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
            <a href="#ads-register" class="btn ads-font-22 introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
        </div>

        <!--pricing-->
        <div class="pricing-div alert">
        
            <div class="pricing-table">
                <div class="pricing-title">Compare Plans</div>
                <table cellspacing="0" cellpadding="0" class="table table-bordered">
                    <tr class="pricing-td-title">
                        <td class="pricing-name"></td>
                        <td class="pricing-item">Free</td>
                        <td class="pricing-item">Standard</td>
                    </tr>
                    <tr>
                        <td class="pricing-td-title">Advanced Filter
                    </td>
                        <td>Limited</td>
                        <td><i class="glyphicon glyphicon-ok"></i></td>
                    </tr>
                    <tr>
                        <td class="pricing-td-title">Audience &amp; Interest</td>
                        <td>Limited</td>
                        <td><i class="glyphicon glyphicon-ok"></i></td>
                    </tr>
                    <tr>
                        <td class="pricing-td-title">Data Amount</td>
                        <td>Limited</td>
                        <td>5M<sup>+</sup></td>
                    </tr>
                    <tr>
                        <td class="pricing-td-title">Mobile Ads &amp; App Ads
                        </td>
                        <td></td>
                        <td><i class="glyphicon glyphicon-ok"></i></td>
                    </tr>
                    <tr>
                        <td class="pricing-td-title">Bookmark</td>
                        <td>Single</td>
                        <td>Multiple</td>
                    </tr>
                    <tr>
                        <td class="pricing-td-title">Landing Page</td>
                        <td>Limited</td>
                        <td><i class="glyphicon glyphicon-ok"></i></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><a href="/plan" class="btn btn-orange">Sign Up</a></td>
                        <td><a href="/plan" class="btn btn-orange">Buy Now</a></td>
                    </tr>
                </table>
            </div>
            <div class="pricing-question">
                <div class="question-title pricing-title">Frequently asked questions</div>
                <p class="question-q">What forms of payment do you accept? </p>
                <p class="question-a">
                    We currently accept PayPal. We'll try our best to offer more payment options soon.
                </p>
                <p class="question-q">Are prices shown in USD?</p> 
                <p class="question-a">Yes. All plan prices are in USD.</p>

                <p class="question-q">How long are these contracts? </p>
                <p class="question-a">We support month-to-month subscriptions which can be canceled anytime. One-month purchases subscription will be launched when it's ready. </p>

                <p class="question-q">Is there a free trial or demo? </p>
                <p class="question-a">Yes, you are able to test out what Bigbigads has by signing up a free level account. That way you can be sure before you buy.</p>

                <p class="question-q">Is there a discount for a yearly subscription? </p>
                <p class="question-a">Yes, you get a savings of about 20% off on yearly plans. </p>
            </div>
        
            <div class="pricing-discounts">
                <div class="discounts-title pricing-title">Educational discounts</div>
                
                <div class="discounts-text-div">
                    <div class="discounts-text-title text-center">
                        Educational discounts
                    </div>
                        <p class="text-center discounts-text">If you are currently a student, 
                    <a href="mailto:sale@bigbigads.com">
                    email us a copy of your transcript
                    </a> 
                    to get 80% off our standard plan for use on non-commercial projects. If you are a teacher, 
                    <a href="mailto:sale@bigbigads.com">
                    send us a copy of your syllabus
                    </a> 
                    to set up a discounted plan that can be used by your students for their coursework.
                    </p>
                </div>
                
                <div class="discounts-text-div text-center">
                    <p class="discounts-text-title">Buying for a team of 5 or more?</p>
                    <p class="discounts-text">We'll work with your specific needs to develop a plan that's right for your team. 
                    <a href="mailto:sale@bigbigads.com">Get in touch with us
                    </a> today to receive a custom quote.</p>
                </div>
            </div>
        </div>

    </div>
    @include('tpl.mobile_footer')
    <div class="footer"></div>
    <!--end header-->
<script type="text/javascript" src="{{bba_version('vendor.js')}}" defer></script>
<script type="text/javascript" src="{{bba_version('mobile.js')}}" defer></script>
<!-- <script src="./static/jquery-3.1.1.js"></script> -->
<!-- <script src="./static/js/mobile.js"></script> -->
</body>
</html>
