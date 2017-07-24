<!-- 导航 -->
@inject('request', 'Illuminate\Http\Request')

<header>
    <div class="container ads-menu-div clearfix">
        <div class="pull-left">
            <a class="logo-head navbar-brand" href="/home" title=""></a>
        </div>
        <div class=" pull-right menu-list">
            <a href="{{url('/login')}}" class="btn btn-empty w100">LOGIN</a>
            <a href="{{url('/register')}}" class="btn w100 head-btn">SIGN UP</a>
            <!--应使用register路由，否则在登陆后点注册会因找不到login#register出错-->
        </div>
    </div>
</header>
