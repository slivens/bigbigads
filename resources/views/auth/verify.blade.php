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
<!-- 需求改变，两段谷歌统计代码已经移到RegisterListenter内，手动点击激活链接发现会报错:
    Undefined variable: email (View: E:\wamp64\www\bigbigads_new\resources\views\auth\verify.blade.php
-->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-87299802-3', 'auto');
      ga('send', 'pageview');
    </script>
@endsection
