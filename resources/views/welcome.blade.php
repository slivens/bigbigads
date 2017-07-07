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
    <link href="https://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
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

    /*移动端显示*/
    @media screen and (max-width: 768px) {
        /*移动端维护页面*/
        /*采用淘宝移动端适配方案
        *设计稿采用iphone6，即宽度为375pt
        *界面内容的大小设置应为（设计大小px/37.5）rem，也可采用css3的calc计算
        */
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
                        <a href="/app" class="btn continue-btn text-center">Continue</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="update_page-foot clearfix">
            <p class="text-center">&copy;2017 BIGBIGADS.COM</p>
        </div>
    </div>
</body>
</html>
