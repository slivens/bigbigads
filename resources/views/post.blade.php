<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.1
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Bigbigads - {{$post->title}} </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="{{$post->meta_description}}" name="description" />
        <meta content="{{$post->meta_keywords}}" name="keywords" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../assets/global/css/components-md.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../assets/global/css/plugins-md.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../assets/pages/css/blog.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link rel="stylesheet" type="text/css" href="../static/custom.css"><!-- the css of head and foot -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="/static/images/favicon.ico" /> </head>
    <!-- END HEAD -->

    <body class="page-container-bg-solid page-md">

@include('tpl.header')
        <div class="page-wrapper">
            <div class="page-wrapper-row full-height">
                <div class="page-wrapper-middle">
                    <!-- BEGIN CONTAINER -->
                    <div class="page-container">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <!-- BEGIN CONTENT BODY -->
                            <!-- BEGIN PAGE CONTENT BODY -->
                            <div class="page-content">
                                <div class="container">
                                    <!-- BEGIN PAGE BREADCRUMBS -->
                                    <ul class="page-breadcrumb breadcrumb">
                                        <li>
                                            <a href="{{url('/home')}}">Home</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                        <li>
                                            <a href="{{url('/blog')}}">Blog</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                        <li>
                                            <span>{{$post->title}}</span>
                                        </li>
                                    </ul>
                                    <!-- END PAGE BREADCRUMBS -->
                                    <!-- BEGIN PAGE CONTENT INNER -->
                                    <div class="page-content-inner">
                                        <div class="blog-page blog-content-2">
                                            <div class="row">
                                                <div class="col-lg-9">
                                                    <div class="blog-single-content bordered blog-container">
                                                        <div class="blog-single-head">
                                                            <h1 class="blog-single-head-title">{{$post->title}}</h1>
                                                            <div class="blog-single-head-date">
                                                                <i class="icon-calendar font-blue"></i>
                                                                <a href="javascript:;">{{ (new Carbon\Carbon($post->created_at))->toFormattedDateString() }}</a>
                                                            </div>
                                                        </div>

                                                        @if (!empty($post->image))
                                                        <div class="blog-single-img">
                                                            <img src="{{ Voyager::image($post->image) }}" /> 
                                                        </div>
                                                        @endif
                                                            <div class="blog-single-desc">
                                                                {!! ($post->body) !!}
                                                            </div>
    <!--
                                                            <div class="blog-single-foot">
                                                                <ul class="blog-post-tags">
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Bootstrap</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Sass</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">HTML</a>
                                                                </li>
                                                            </ul>
                                                        </div>
-->
<!--
                                                        <div class="blog-comments">
                                                            <h3 class="sbold blog-comments-title">Comments(30)</h3>
                                                            <div class="c-comment-list">
                                                                <div class="media">
                                                                    <div class="media-left">
                                                                        <a href="#">
                                                                            <img class="media-object" alt="" src="../assets/pages/img/avatars/team1.jpg"> </a>
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <h4 class="media-heading">
                                                                            <a href="#">Sean</a> on
                                                                            <span class="c-date">23 May 2015, 10:40AM</span>
                                                                        </h4> Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. </div>
                                                                </div>
                                                                <div class="media">
                                                                    <div class="media-left">
                                                                        <a href="#">
                                                                            <img class="media-object" alt="" src="../assets/pages/img/avatars/team3.jpg"> </a>
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <h4 class="media-heading">
                                                                            <a href="#">Strong Strong</a> on
                                                                            <span class="c-date">21 May 2015, 11:40AM</span>
                                                                        </h4> Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.
                                                                        <div class="media">
                                                                            <div class="media-left">
                                                                                <a href="#">
                                                                                    <img class="media-object" alt="" src="../assets/pages/img/avatars/team4.jpg"> </a>
                                                                            </div>
                                                                            <div class="media-body">
                                                                                <h4 class="media-heading">
                                                                                    <a href="#">Emma Stone</a> on
                                                                                    <span class="c-date">30 May 2015, 9:40PM</span>
                                                                                </h4> Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="media">
                                                                    <div class="media-left">
                                                                        <a href="#">
                                                                            <img class="media-object" alt="" src="../assets/pages/img/avatars/team7.jpg"> </a>
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <h4 class="media-heading">
                                                                            <a href="#">Nick Nilson</a> on
                                                                            <span class="c-date">30 May 2015, 9:40PM</span>
                                                                        </h4> Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. </div>
                                                                </div>
                                                            </div>
                                                            <h3 class="sbold blog-comments-title">Leave A Comment</h3>
                                                            <form action="#">
                                                                <div class="form-group">
                                                                    <input type="text" placeholder="Your Name" class="form-control c-square"> </div>
                                                                <div class="form-group">
                                                                    <input type="text" placeholder="Your Email" class="form-control c-square"> </div>
                                                                <div class="form-group">
                                                                    <input type="text" placeholder="Your Website" class="form-control c-square"> </div>
                                                                <div class="form-group">
                                                                    <textarea rows="8" name="message" placeholder="Write comment here ..." class="form-control c-square"></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <button type="submit" class="btn blue uppercase btn-md sbold btn-block">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
-->
                                                    </div>
                                                </div>
                                                <!-- Begin right sidebar -->
                                                <div class="col-lg-3">
                                                    <div class="blog-single-sidebar bordered blog-container">
                                                        <!-- hide searchbar
                                                        <div class="blog-single-sidebar-search">
                                                            <div class="input-icon right">
                                                                <i class="icon-magnifier"></i>
                                                                <input type="text" class="form-control" placeholder="Search Blog"> </div>
                                                        </div>
                                                        -->
                                                        <div class="blog-single-sidebar-recent">
                                                            <h3 class="blog-sidebar-title uppercase">Recent Posts</h3>
                                                            <ul>

                                                            @foreach ($recents as $r)
                                                                <li>
                                                                    <a href="{{url('post', ['id' => $r->id])}}">{{$r->title}}</a>
                                                                </li>
                                                            @endforeach
                                                            </ul>
                                                        </div>
                                                        <!--
                                                        <div class="blog-single-sidebar-tags">
                                                            <h3 class="blog-sidebar-title uppercase">Tags</h3>
                                                            <ul class="blog-post-tags">
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Bootstrap</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Sass</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">HTML</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">CSS</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Gulp</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Framework</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">Admin Theme</a>
                                                                </li>
                                                                <li class="uppercase">
                                                                    <a href="javascript:;">UI Features</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        -->
                                                        <!--
                                                        <div class="blog-single-sidebar-links">
                                                            <h3 class="blog-sidebar-title uppercase">Useful Links</h3>
                                                            <ul>
                                                                <li>
                                                                    <a href="javascript:;">Lorem Ipsum </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">Dolore Amet</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">Metronic Database</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">UI Features</a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">Advanced Forms</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        -->
                                                        <!--
                                                        <div class="blog-single-sidebar-ui">
                                                            <h3 class="blog-sidebar-title uppercase">UI Examples</h3>
                                                            <div class="row ui-margin">
                                                                <div class="col-xs-4 ui-padding">
                                                                    <a href="javascript:;">
                                                                        <img src="../assets/pages/img/background/1.jpg" />
                                                                    </a>
                                                                </div>
                                                                <div class="col-xs-4 ui-padding">
                                                                    <a href="javascript:;">
                                                                        <img src="../assets/pages/img/background/37.jpg" />
                                                                    </a>
                                                                </div>
                                                                <div class="col-xs-4 ui-padding">
                                                                    <a href="javascript:;">
                                                                        <img src="../assets/pages/img/background/57.jpg" />
                                                                    </a>
                                                                </div>
                                                                <div class="col-xs-4 ui-padding">
                                                                    <a href="javascript:;">
                                                                        <img src="../assets/pages/img/background/53.jpg" />
                                                                    </a>
                                                                </div>
                                                                <div class="col-xs-4 ui-padding">
                                                                    <a href="javascript:;">
                                                                        <img src="../assets/pages/img/background/59.jpg" />
                                                                    </a>
                                                                </div>
                                                                <div class="col-xs-4 ui-padding">
                                                                    <a href="javascript:;">
                                                                        <img src="../assets/pages/img/background/42.jpg" />
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END PAGE CONTENT INNER -->
                                </div>
                            </div>
                            <!-- END PAGE CONTENT BODY -->
                            <!-- END CONTENT BODY -->
                        </div>
                        <!-- END CONTENT -->

@include('tpl.footer')
        <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<script src="../assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>
