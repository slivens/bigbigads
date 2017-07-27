<!-- 导航 -->
@inject('request', 'Illuminate\Http\Request')

<header>

    <div class="container ads-menu-div">
        <div class="adslogo-wrapper">
            <a class="logo-head" href="/home" title=""></a>
        </div>
        <div class="menu-list">
            <a href="{{url('/login')}}" class="btn login-btn ads-font-14">LOGIN</a>
            <a href="{{url('/plan')}}" class="btn  register-btn ads-font-14 mobile-hidden">SIGN UP</a>
            <!--应使用register路由，否则在登陆后点注册会因找不到login#register出错-->
        </div>
    </div>
</header>
