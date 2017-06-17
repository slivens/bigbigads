<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--<meta name="viewport"-->
          <!--content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">-->
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>pricing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
</head>
<body>
@include('tpl.header')
<section class="pricing-header">
    <div class="pricing"></div>
    <div class="container text-center mat-500" >
        <div class="pricing-tab">
            <a href="javascript:;" class="pricing-icon icon-per active">Small Business</a>
            <a href="javascript:;" class="pricing-icon icon-ad">Marketer</a>
            <a href="javascript:;" class="pricing-icon icon-shop">Ad Agency</a>
        </div>
        <p class="pricing-tab_tip mab-2">Inspiring small businesses, marketers &amp; ad agencies
            <br>with easily findable winning products and ad creatives
        </p>
        <div class="box-shadow radius" >
            <div class="clearfix pricing-card_top" style="background-color: #fff;">
                <div class="col-md-6 col-xs-6 font_24 pricing-tab-item">
                    <p class="pricing-item_title ads-ms-text">STANDARD</p>
                    <p class="mab-56 ads-def-text">A starter level that's way better than what others offer. </p>
                </div>
                <div class="col-md-6 col-xs-6 font_24  border-left pricing-tab-item">
                    <p class="pricing-item_title ads-ms-text">PLUS</p>
                    <p class="mab-56 ads-def-text">Unblocked advanced features. Enjoy the next generation ad intelligence platform.</p>
                </div>
            </div>
            <div class="clearfix pricing-card_bottom" style="background-color: #fff;">
                <div class="col-md-6 col-xs-6  font_24  pricing-tab-item">
                    <p class="ads-def-text">$99/Month</p>
                    <a href="/app/plans" class="btn font_22 pricing-item_btn ads-xs-text">START NOW</a>
                    <p class="color_tip ads-ms-text">Data Update Frequency:</p>
                    <p><strong class="ads-ms-text">Weekly</strong></p>
                </div>
                <div class="col-md-6 col-xs-6 font_24 border-left  pricing-tab-item">
                    <p class="ads-def-text">$169/ Month</p>
                    <a  class="btn pricing-item_btn btn_cannot ads-xs-text">Coming Soon</a>
                    <p class="color_tip ads-ms-text">Data Update Frequency:</p>
                    <p><strong  class="ads-ms-text">Daily</strong></p>
                </div>
            </div>
            
            <!--Privilege contrast-->
            <div class="bg_f6  clearfix text-left">
                <ul class="col-md-6 col-xs-6  pricing-tab_desc">
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
                <ul class="col-md-6 col-xs-6 pricing-tab_desc border-left">
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
                        <span class="pricing-tab_text">Audience Targeting &amp; Interests</span>
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
        <p class="pricing-qa_title ads-md-text">Plans &amp; pricing</p>
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
                    <td><i class=""></i><span class="pricing-field">Daily</span></td>
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
                    <td class="pricingitme-name">Last Seen/Video Views/Shares/Likes/Comments</td>
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
        <p class="pricing-qa_title ads-lg-text">F.A.Q </p>
        <ul class="pricing-qa_item" id="qa">
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">How do I pay for Bigbigads?</a></div>
                <p class="pricing-qa_a clearfix">
                    Here are three easy steps to sign up for a paid Bigbigads account: <br>
                    Step 1: Create a free Bigbigads account and log in.<br>
                    Step 2: Select the plan you want to subscribe to from the choices presented on this page.<br> 
                    Step 3: Finally, please visit the Billing page (we only accept PayPal for now).

                </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q"> How can I change my password?</a></div>
                <p class="pricing-qa_a">
                    To change your password: <br>
                    1. Click on the account menu on the upper right corner of the page. <br>
                    2. Click Profile.<br>
                    3. Click Account Setting.<br> 
                    4. Click Change button beside the Password</p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">How many winning ads can Bigbigads help me find?</a></div>
                <p class="pricing-qa_a">We've found thousands of winning ads for customers within the last 7 months! Collectively, we've helped them uncover over 81,000 ads with 1K<sup>+</sup>shares, over 12,000 ads with 10K<sup>+</sup>shares, and over 1200 ads with 100K<sup>+</sup>shares. Currently, Bigbigads is geared to help you find winning ads on Facebook, but let us know if you'd be interested in using Bigbigads to help you discover winning ads on other social networks. We're always looking to improve our product, and we're sure that we can make Bigbigads even better with your feedback! </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">Where can I get my invoice?</a></div>
                <p class="pricing-qa_a">You may view your invoices on the billing page. Go to the account menu on the top right side &amp; click "Billing".
                </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">Why are there no results for my competitors' ads?</a></div>
                <p class="pricing-qa_a">
                    There are several scenarios that might not allow you to find your competitors' ads:<br>
                    1. You didn't clear your search term; click the "Clear All" button.<br>
                    2. You input an improper keyword; check our blog post "How To Search Smartly With Bigbigads".<br>
                    3. Check if you chose the right "ad position", which you can pick from the dropdown on the right side of the
                    search box.<br>
                    4. Your competitor's ad has very low reach, so, depending on how you choose to sort the ads, they may
                    not show up immediately in the search results. You can, however, bookmark your competitor and set an alert to
                    be triggered once certain conditions are met.<br>
                    5. Your competitor doesn't have a Facebook ad; you can verify this with our advanced subscription plan.<br>
                    6. If you are positive that Bigbigads should be showing results when none are visible, refresh the webpage and
                    log in again.
                </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">Do you offer yearly price plans?</a></div>
                <p class="pricing-qa_a">Yes, we do offer a yearly plan. Please contact our online service team for more info. If you choose to pay on a monthly basis, your subscription can be cancelled at any time by simply downgrading to the free Basic plan.
                </p>
            </li>
            <li class="">
                <div><span class="caret "></span>
                <a href="javascript:;" class="pricing-qa_q">What payment methods do you support?</a></div>
                <p class="pricing-qa_a">We currently accept payments via PayPal. Please talk to our online service team if you have any difficulty paying
                – you can start a conversation by clicking the orange chat bubble on the bottom
                -right side of the screen.
                </p>
            </li>
        </ul>
    </div>
</section>
@include('tpl.footer')
 <script src="static/jquery-3.1.1.js"></script>
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
<script type="text/javascript" src="dist/vendor.js?v=5" defer></script>
<script type="text/javascript" src="dist/home.js?v=5" defer></script>
</html>
<link href="./dist/home.css?v=1" rel="stylesheet">
