<!-- 导航 -->
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
                <a class="logo-head navbar-brand" href="./" title=""></a>
            </div>
            <div class="collapse navbar-collapse clearfix bg_fff" id="example-navbar-collapse">
                <div class=" pull-right ">
                    <ul class=" head-nav navbar-nav">
                        <li class="active"><a href="/">Home</a></li>
                        <li> <a href="{{url('/product')}}">Product</a></li>
                        <li><a href="{{url('/pricing')}}">Pricing</a></li>
                        <li><a href="{{url('/blog')}}">Blog</a></li>
                        <li ><a href="{{url('/about')}}" >About</a></li>
                        <li class="none"><a href="{{url('/login')}}" class="btn btn-empty w100">LOGIN</a></li>
                        <li class="none"><a href="{{url('/register')}}" class="btn w100 head-btn">SIGN UP</a></li>
                    </ul>
                </div>

            </div>
        </nav>
    </div>
</header>
