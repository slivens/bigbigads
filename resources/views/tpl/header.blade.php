
@inject('request', 'Illuminate\Http\Request')

<header>

    <div class="container ads-menu-div">
        <div class="adslogo-wrapper">
            <a class="logo-head" href="/home" title=""></a>
        </div>
        <div class="menu-list">
            <a href="{{url('/login')}}" class="btn login-btn ads-font-14">LOGIN</a>
            <a href="{{url('/plan')}}" class="btn  register-btn ads-font-14 mobile-hidden">SIGN UP</a>
        </div>
    </div>
</header>
