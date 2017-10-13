<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <title>Bigbigads Blog | Largest Facebook Ad Examples</title>
        <meta name="description" content="A Facebook ads spy tool to help you find out the competitor marketing strategy, ad creatives, ad insight.">
        <meta content="Bigbigads" name="author" />
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="./assets/pages/css/blog.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="./assets/layouts/layout3/css/layout.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="./static/images/favicon.ico" /> 
        <link href="{{bba_version('home.css')}}" rel="stylesheet">
        @include('tpl.script')
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-md">

@include('tpl.header')
        <!-- BEGIN CONTAINER -->
        <div class="wrapper">
            <div class="container-fluid">
                <div class="page-content">
                    <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                    <div class="page-content-container">
                        <div class="page-content-row">
                            <div class="page-content-col">
                                <!-- BEGIN PAGE BASE CONTENT -->
                                <div class="blog-page blog-content-1">
                                    <div class="grid hidden">
                                        @foreach ($posts as $post)
                                        <div class="grid-item col-xs-12 col-sm-6 col-md-4">
                                                    <div class="blog-post-sm bordered blog-container">
                                                        @if (!empty($post->image))
                                                        <div class="blog-img-thumb">
                                                            <a href="{{url('post', ['id' => $post->id])}}">
                                                                <img src="{{ Voyager::image($post->image) }}" class="img-responsive" />
                                                            </a>
                                                        </div>
                                                        @endif
                                                        <div class="blog-post-content">
                                                            <h2 class="blog-title blog-post-title">
                                                                <a href="{{url('post', ['id' => $post->id])}}">{{ $post->title }}</a>
                                                            </h2>
                                                            <p class="blog-post-desc">{{ $post->excerpt }} </p>
                                                            <div class="blog-post-foot">
                                                                <div class="blog-post-meta">
                                                                    <i class="icon-calendar font-blue"></i>
                                                                    <a href="{{URL('post', ['id' => $post->id])}}">{{ (new Carbon\Carbon($post->created_at))->toFormattedDateString() }}</a>
                                                                </div>
<!--
                                                                <div class="blog-post-meta">
                                                                    <i class="icon-bubble font-blue"></i>
                                                                    <a href="javascript:;">14 Comments</a>
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- END PAGE BASE CONTENT -->
                            </div>
                        </div>
                    </div>
                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>
                <!-- BEGIN FOOTER -->
@include('tpl.footer')
                <!-- END FOOTER -->
            </div>
        </div>
        <!-- END CONTAINER -->
        <!-- END QUICK NAV -->
        <!-- BEGIN CORE PLUGINS -->
        <script src="./assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script type="text/javascript">
        $(function() {

            $('.grid').removeClass("hidden");
            var $grid = $('.grid').masonry({
                itemSelector:'.grid-item',
                columnWidth:'.grid-item',
                percentPosition:true
            });
            $grid.on( 'layoutComplete', function() {
            } );
        });
        </script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>

