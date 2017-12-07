<!DOCTYPE html>
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<title>welcome</title>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="The bigbigads.com's phone interface is being maintained" name="description" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{bba_version('home.css')}}" rel="stylesheet">
    <!-- END GLOBAL MANDATORY STYLES -->
    <link rel="shortcut icon" type="image/x-icon" href="static/images/favicon.ico" media="screen">
<style type="text/css">

    html{
        height: 100%;
        width: 100%;
    }
    body{
        background-color: #f5f8fa;
        width: 100%;
        height: 100%;
    }
    .content{
        height: 100%;
        width: 100%;
    }
    .update-page-head{
        background: #fff;
    }
    .update-page-head .container{
        padding: 15px;
    }
    .update-page-head img{
        height: 46px;
    }
    .update_page-content {
        margin: 0 auto;
        background: url(static/images/update_page02.jpg) no-repeat center;
        background-size:100% auto;
        height: calc(100% - (30px + 46px) - 50px);
        min-height: 400px;
        width: 100%;
    }
    
    .update_page-content .content-text {
        color: #fff;
        width: 55%;
        display: flex;
        justify-content: center; 
        align-items: center;
        height: 100%; 
    }
    .update_page-content .container{
        height: 100%;
    }
    .update_page-content .content-text p {
        color: #fff;
        font-family: "open sans";
        font-size: 30px;
        font-weight: 600;
    }
    .continue-btn{
        display:block;
        width: 200px;
        padding: 15px 20px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        background: #eb6130;
        color: #fff;
        margin: 50px auto;
        transition: box-shadow .28s cubic-bezier(.4,0,.2,1);
        box-shadow: 0 1px 3px rgba(0,0,0,.1), 0 1px 2px rgba(0,0,0,.18);
    }
    .continue-btn:hover{
        color: #fff;
        box-shadow: 0 3px 6px rgba(0,0,0,.2), 0 3px 6px rgba(0,0,0,.26);
    }
    .update_page-foot {
            background: #333;
            color: #fff;
            padding: 15px;
        }
    .update_page-foot p {
        font-size: 16px;
        margin: 0;
        text-align: center;
        height:20px;
        line-height: 20px; 
    }

    /*show on mobile*/
    @media screen and (max-width: 768px) {
        /* with iphone desiner*/
        html {
            font-size: calc(100vw / 10);
        }
        body {
            font-size: 16px;
        }

        .update-page-head .container{
            padding: 0.4rem 0.4rem;
        }
        .update-page-head img{
            height: 1.226rem;
        }
        .update_page-content {
            background-size: auto 114%;
            height: calc(100% - (0.4rem) * 4 - 1.266rem - 0.64rem);  
        }
        .update_page-content .content-text {
            width: 70%;
            padding-left:0.4rem;
        }
        .update_page-content .content-text p {
            font-size: 0.5333333rem;
            font-weight: 200;
        }
        .update_page-foot {
            padding: 0.4rem;
        }
        .update_page-foot p {
            font-weight: 200;
            font-size: 0.42666rem;
            height:0.64rem;
            line-height: 0.64rem; 
        }
        .continue-btn{
            padding: 0.25rem 0.533rem;
            font-size: 0.5333rem;
            margin: 0 auto;
            margin-top:0.5333rem;
            width: auto;
        }
    }
</style>
<!-- Google statistics code -->
@include('tpl.script')
<script>
  /*
    for example
    ga('send', {
    hitType: 'event',
    eventCategory: 'conversion',
    eventAction: 'register',
    eventLabel: 'social_facebook'
  });
  */
  /*Send Google events*/
  var href = window.location.href;
  var params = href.split("?");
  var socialite = params[1].split("=");
  if (socialite[0] === 'socialite') {
      ga('send', 'event', 'conversion', 'register', socialite[1]);
  }
</script>
</head>
<!-- END HEAD -->
<body class=" page-500-full-page">
    <div class="content">
        <div class="update-page-head clearfix">
            <div class="container">
                <!-- Branding Image -->
                <a class="" href="/app">
                    <!-- Bigbigads -->
                    <!-- set ng filter images request is no work, so request image from remote org  -->
                    <img src="http://image1.bigbigads.com:88/image/upgrade/logo2.png" alt="">
                </a> 
            </div>
        </div>
        <div class="update_page-content clearfix">
            <div class="container">
                <div class="content-text">
                    <div class="clearfix">
                        <p class="text-center">Welcome to use BIGBIGADS' service. Please notice that we are redesigning the interface for our mobile app so you can only use basic function now. You can login to the desktop for full function.
                        </p>
                        <a id="continue" class="btn continue-btn text-center">Continue</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="update_page-foot clearfix">
            <p class="text-center">&copy;2017 BIGBIGADS.COM</p>
        </div>
    </div>
</body>
    <!-- Google Code for  
    &#32654;&#21152;&#20197;&#22806;&#65292;&#33521;&#35821;&#65292;&#22810;&#29256;&#26412;&#24191;&#21578;&#25991;&#26696;&#23545;&#24212;&#19981;&#21516;&#20851;&#38190;&#35789;&#32452;&#21512;&#65292;&#25628;&#32034;+&#23637;&#31034;    
    &#27880;&#20876;&#36319;&#36394; Conversion Page -->
<!-- google 1 translate -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 850659212;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "ZVS0COmL-XEQjI_QlQM";
    var google_conversion_value = 12.00;
    var google_conversion_currency = "CNY";
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>

<!-- Google Code for bigbigads&#27880;&#20876;(info) Conversion Page -->
<!-- google 2 translate -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 851092927;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "ohi0CK6n93EQv8vqlQM";
var google_remarketing_only = false;
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>

<script>
(function(h,o,t,j,a,r){
    h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
    h._hjSettings={hjid:455748,hjsv:5};
    a=o.getElementsByTagName('head')[0];
    r=o.createElement('script');r.async=1;
    r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
    a.appendChild(r);
})(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
</script>
<!-- 必应统计 -->
<script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"5713181"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script><noscript><img src="//bat.bing.com/action/0?ti=5713181&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>

<!-- Event snippet for register_welcome conversion page -->
<script>
  gtag('event', 'conversion', {'send_to': 'AW-828108332/bGxTCP726nkQrNzvigM'});
</script>
<!-- fb Event for register_welcome -->
<script type="text/javascript">
  fbq('track', 'CompleteRegistration');
</script>
<script type="text/javascript" src="{{bba_version('vendor.js')}}" defer></script>
<script type="text/javascript" src="{{bba_version('welcome.js')}}" defer></script>
</html>
