<!-- 导航 -->
@inject('request', 'Illuminate\Http\Request')

<header>
    <div class="container ">
        <nav class="navbar " role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target="#example-navbar-collapse">
                    <span class="sr-only">切换导航</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="logo-head navbar-brand" href="/home" title=""></a>
            </div>
            <div class="collapse navbar-collapse clearfix bg_fff" id="example-navbar-collapse">
                <div class=" pull-right meun_list">
                    <ul class=" head-nav navbar-nav">
                        <li class="{{ $request->path() == 'home' ? 'active':'' }}"><a href="/home">Home</a></li>
                        <li class="{{ $request->path() == 'product' ? 'active':'' }}"> <a href="{{url('/product')}}">Product</a></li>
                        <li class="{{ $request->path() == 'pricing' ? 'active':'' }}"><a href="{{url('/pricing')}}">Pricing</a></li>
                        <li class="{{ $request->path() == 'blog' || preg_match('/^post/', $request->path()) ? 'active':'' }}"><a href="{{url('/blog')}}">Blog</a></li>
                        <li class="{{ $request->path() == 'about' ? 'active':'' }}"><a href="{{url('/about')}}" >About</a></li>
                        <li class="none"><a href="{{url('/login')}}" class="btn btn-empty w100">LOGIN</a></li>
                        <li class="none"><a href="{{url('/register')}}" class="btn w100 head-btn">SIGN UP</a></li><!--应使用register路由，否则在登陆后点注册会因找不到login#register出错-->
                    </ul>
                </div>

            </div>
        </nav>
    </div>
</header>
