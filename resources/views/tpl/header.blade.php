
@inject('request', 'Illuminate\Http\Request')

<header>

    <div class="container ads-menu-div">
        <div class="adslogo-wrapper">
            <a class="logo-head" href="/home" title=""></a>
        </div>
        <div class="menu-list">
            <a href="{{url('/login')}}" class="btn login-btn ads-font-14">{{ trans('auth.login') }}</a>
            <a href="{{url('/plan')}}" class="btn  register-btn ads-font-14 mobile-hidden">{{ trans('auth.signup') }}</a>
        </div>
    </div>
</header>
