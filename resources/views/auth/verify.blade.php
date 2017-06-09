@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 ">
            @if (isset($error))
            <div class="alert alert-danger">{{$error}}</div>
            @if (isset($link)) 
                <div>If you have not received the email, click <a href="{{$link}}">Here</a> to send again.</div>
            @endif
            @elseif (isset($info))
            <div class="alert alert-info">{{$info}}</div>
            @else
            <div class="alert alert-success">{{$user->name}}, You email "{{$user->email}}" has verified, click <a href="/login">Here</a> to Login!</div>
            @endif
        </div>
    </div>
</div>
<!-- google 插件代码，跟踪用户行为，需要放在注册，购买后的提示页面 -->
    <!-- Google Code for  
    &#32654;&#21152;&#20197;&#22806;&#65292;&#33521;&#35821;&#65292;&#22810;&#29256;&#26412;&#24191;&#21578;&#25991;&#26696;&#23545;&#24212;&#19981;&#21516;&#20851;&#38190;&#35789;&#32452;&#21512;&#65292;&#25628;&#32034;+&#23637;&#31034;    
    &#27880;&#20876;&#36319;&#36394; Conversion Page -->
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
    <script type="text/javascript"  
    src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
    <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt=""  
    src="//www.googleadservices.com/pagead/conversion/850659212/?value=12.00&amp;currency_code=CNY&amp;label=ZVS0COmL-XEQjI_QlQM&amp;guid=ON&amp;amp;script=0"/>
    </div>
    </noscript>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-87299802-3', 'auto');
      ga('send', 'pageview');
    </script>
@endsection
