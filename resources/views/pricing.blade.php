<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--<meta name="viewport"-->
          <!--content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">-->
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>pricing</title>
    <link rel="stylesheet" type="text/css" href="./static/bootstrap.css">
    <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="./static/custom.css">
    <link rel="stylesheet" type="text/css" href="./static/demo.css">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
</head>
<body>
@include('tpl.header')
<section class=" ">
    <div class="pricing"></div>
    <div class="container text-center mat-500" >
        <div class="pricing-tab">
            <a href="javascript:;" class="pricing-icon icon-per active">Small Business</a>
            <a href="javascript:;" class="pricing-icon icon-ad">Advanced</a>
            <a href="javascript:;" class="pricing-icon icon-shop">Enterprice</a>
        </div>
        <p class="pricing-tab_tip mab-2">For Small Business to get inspired on products and creatives </p>
        <div class="box-shadow radius" >
            <div class="clearfix pricing-card_top" style="background-color: #fff;">
                <div class="col-md-6 font_24 pricing-tab-item">
                    <p class="pricing-item_title">STANDARD</p>
                    <p class="mab-56">A starter level which way too better than other competitor on the market.</p>
                </div>
                <div class="col-md-6 font_24  border-left pricing-tab-item">
                    <p class="pricing-item_title">ADVANCED
</p>
                    <p class="mab-56">Unblocked features on ad level,enjoy your next generation ad intelligence platform.</p>
                </div>
            </div>
            <div class="clearfix pricing-card_bottom" style="background-color: #fff;">
                <div class="col-md-6  font_24  pricing-tab-item">
                    <p >$99/Month</p>
                    <a href="/app/adsearch" class="btn font_22 pricing-item_btn">START NOW</a>
                    <p class="color_tip">Data Update Frequency:</p>
                    <p><strong>Weekly</strong></p>
                </div>
                <div class="col-md-6 font_24 border-left  pricing-tab-item">
                    <p >$169/ Month</p>
                    <a  class="btn pricing-item_btn btn_cannot">Coming Soon</a>
                    <p class="color_tip">Data Update Frequency:</p>
                    <p><strong>Daily</strong></p>
                </div>
            </div>
            
            <!--Privilege contrast-->
            <div class="bg_f6  clearfix text-left">
                <ul class="col-md-6  pricing-tab_desc">
                    <li>
                        <span class="demoicon icon-stats-dots pricing-tab_icon"></span>
                        <span class="pricing-tab_text">All Data Access</span>
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-filter pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Standard Cross Filter</span>
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-sort-by-attributes pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Standard Sorting</span>
                    </li>
                    
                    <li>
                        <span class="glyphicon glyphicon-bookmark pricing-tab_icon"></span>
                        <span class="pricing-tab_text"> Limited Bookmark</span>
                    </li>
                    <li>
                        <span class="demoicon icon-compass2 pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Limited Ad Insight</span>
                    </li>
                    
                </ul>
                <ul class="col-md-6 pricing-tab_desc border-left">
                    <li>
                        <span class="icon-book demoicon pricing-tab_icon"></span>
                        <span class="pricing-tab_text">All Features of Standard Level</span>
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-screenshot pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Advanced Cross Filter</span>
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-sort-by-alphabet pricing-tab_icon"></span>
                        <span class="pricing-tab_text"> Advanced Sorting</span>
                    </li>
                    <li>
                        <span class="demoicon icon-cog pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Custom Time Frame</span>
                    </li>
                    <li>
                        <span class="demoicon icon-binoculars pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Audience Targeting & Interests</span>
                    </li>
                    <li>
                        <span class="demoicon icon-stats-bars pricing-tab_icon"></span>
                        <span class="pricing-tab_text">Advanced Ad Insight</span>
                    </li>
                </ul>
            </div>


        </div>
    </div>

</section>

<!--begin pricing table-->
<section class="pricing-table clearfix">
    <div class="container">
        <p class="pricing-qa_title">Plans &amp; pricing</p>
        <div class="clearfix radius tabel-div">
            <table class="table">
            <tbody class="table-head text-center">
                <tr class="table-title">
                    <td class="td1"></td>
                    <td class="td2">STANDARD</td>
                    <td class="td3">PLUS</td>
                    <td class="td4">PREMIUM</td>
                </tr>
            </tbody>
            
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Daily Request Limits</td>
                    <td><i class=""></i><span class="pricing-field">1000</span></td>
                    <td><i class=""></i><span class="pricing-field">3000</span></td>
                    <td><i class=""></i><span class="pricing-field">5000</span></td>
            </tr>
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Millions of Ad Creative</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Update Frequency</td>
                    <td><i class=""></i><span class="pricing-field">Weekly</span></td>
                    <td><i class=""></i><span class="pricing-field">Weekly</span></td>
                    <td><i class=""></i><span class="pricing-field">Daily</span></td>
            </tr>     
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Ad Position Filter</td>
                    <td><i class=""></i></td>
                    <td><i class=""></i></td>
                    <td><i class=""></i></td>
                </tr>  
                <tr class="text-center">
                    <td class="pricingitme-name">Right Column Ad</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">News Feed Ad</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Mobile Ad</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody>
            
            <!--Search Mode-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Search Mode</td>
                    <td><i class=""></i></td>
                    <td><i class=""></i></td>
                    <td><i class=""></i></td>
                </tr> 
                <tr class="text-center">
                    <td class="pricingitme-name">Search Advertiser</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Search URL</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Search Advertisement</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Search Audience</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody>
            <!--Time filter-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Time filter</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Time Filter</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Custom Time Filter</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody>

            <!--ad type filter-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Ad Type Filter</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Single Image</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Video</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Canvas</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Carousel</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody>
            
            <!--call to action-->
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Call To Action Filter</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Country Filter</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Language Filter</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr> 

            <!--advanced filter-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Advanced Filter</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Customized Ad Duration</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Customized Ad See Times</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Customized Ad Engagement</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody> 

            <!--compositor-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Compositor(Sort by)</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Engagement/Duration/Last Seen</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Engagement Growth</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody> 

            <!--ad insight-->
            <tbody class="table-content">
                <tr class="text-center2 text-content2">
                    <td class="pricingitme-name">AD insight</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Overview</td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Landing Page</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Audience</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Similar Ads</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Engagement Trends</td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody> 

            <!--keyword analysis-->
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Keyword Analysis</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>

            <!--advertiser-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Advertiser </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Advertiser Search</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Advertiser Analysis</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody>

            <!--bookmark-->
            <tbody class="table-content">
                <tr class="text-center text-content2">
                    <td class="pricingitme-name">Bookmark</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="pricingitme-name">Advertisement Bookmark</td>
                    <td><i class=""></i><span class="pricing-field">Limited(1folder/30ads total)</span></td>
                    <td><i class=""></i><span class="pricing-field">Limited(5folder/150ads total)</span></td>
                    <td><i class=""></i><span class="pricing-field">10 folder/500ads total</span></td>
                </tr> 
                <tr class="text-center">
                    <td class="pricingitme-name">Advertiser Bookmark</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
                </tr>
            </tbody>

            <!--billboard-->
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Billboard</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Monitor</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>
            <tr class="text-center text-content2">
                    <td class="pricingitme-name">Export</td>
                    <td><i class="error"></i></td>
                    <td><i class="error"></i></td>
                    <td><i class="glyphicon glyphicon-ok right"></i></td>
            </tr>
            <tbody class="table-footer text-center">
                <tr class="table-title2 footertop">
                    <td class="">Monthly</td>
                    <td>$99.00/Month</td>
                    <td>$169.00/Month</td>
                    <td>start from $1??/Month</td>
                    
                </tr>
                <tr class="table-title2">
                    <td class="">Annually</td>
                    <td>$79.00/Month</td>
                    <td>$139.00/Month</td>
                    <td>start from $1??/Month</td>
                    
                </tr>
                <tr class="table-title2 footerbottom">
                    <td></td>
                    <td><a class="btn" href="app/plans">Order Now</a></td>
                    <td><a class="btn btn_cannot" >Coming Soon</a></td>
                    <td><a class="btn btn_cannot" >Coming Soon</a></td>
                    
                </tr>
           </tbody>
        </table>
    </div>
        
    </div>
</section>
<!--end pricing tabel-->
<section class="pricing-qa clearfix">
    <div class="container">
        <p class="pricing-qa_title">Frequently asked questions</p>
        <ul class="pricing-qa_item" id="qa">
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">How do I pay for Bigbigads?</a></div>
                <p class="pricing-qa_a clearfix">
                    Here are three easy steps to sign up to Bigbigads! <br>
                    Step 1: Please could you create a Bigbigads account and login. <br>
                    Step 2: Then please select the plan you want to subscribe from the plans page here.<br> 
                    Step 3: Finally please visit the billing page here, update your card details and hit pay now!
                </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q"> How can I change my password?</a></div>
                <p class="pricing-qa_a">
                    To change your password: <br>
                    1. Click on the account menu on the upper right corner of the page. 
                    2. Click Profile.<br>
                    3. Click Account Setting.<br> 
                    4. Click Change button beside the Password</p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">How many winning ads can Bigbigads help me find?</a></div>
                <p class="pricing-qa_a">We’ve found thousands of winning ad for customers within the last 6 months, and we’d make it better with your support! 67K+(with 1K+share) 10K+(with 10K+share) 1000+(with 100K+share) 11K+(with 50day+duration) Currently, Bigbigads can help you find winning ads on Facebook. Please write in and let us know if you’re interested in using Bigbigads to find winning ads in other social networks! We’re always looking to improve our product.</p>
            </li>
            <li class="">
                <span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">Where can I get my invoice?</a>
                <p class="pricing-qa_a">You may view your invoices on the billing page. Go to the account menu on top right side, Click Billing.
                </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">Why are there no results for my competitors' ads?</a></div>
                <p class="pricing-qa_a">
                    There’re several conditions that you can’t find your competitor’s ad:<br>  
                    1.You didn’t clear your search term- click “Clear All” button.<br>  
                    2.Improper keyword- check our blog “How to search smart with bigbigads”.<br>  
                    3.Check if you choose the right “Ad position”-they’re on the right side of searching engine box.<br>  
                    4.Your competitor’s ad have very few reach so there’s no need for you to study them- bookmark your competitor and set your alert for certain condition.<br>  
                    5.Your competitor didn’t do facebook ad- check this in our advanced account.<br>  
                    6. If you are very sure about there should be some results come out, try to refresh the webpage and login again to see if it is ok.
                </p>
            </li>
            <li class="">
                <span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">Do you offer yearly price plans?</a>
                <p class="pricing-qa_a">Yes, we do offer a yearly plan. Please contact our online service team for more info. If you choose to pay on a monthly basis, your subscription can be cancelled at anytime by simply downgrading to the free Basic plan.
                </p>
            </li>
            <li class="">
                <span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">What payment methods do you support?</a>
                <p class="pricing-qa_a">We accept payments via Paypal now, please talk to our online service team if you have difficulty to pay – you can start a conversation by clicking the orange chat bubble on the down-right side of the screen.
                </p>
            </li>
        </ul>
    </div>
</section>
@include('tpl.footer')
<!-- <script src="static/jquery-3.1.1.js"></script>-->
<script src="static/swiper.jquery.js"></script>
<script>
    $(function () {
        $('#qa li').click(function () {
            $('#qa li').removeClass('active');
            $(this).addClass('active');
        })
    })
</script>
</body>
</html>
