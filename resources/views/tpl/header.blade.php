<!-- 导航 -->
@inject('request', 'Illuminate\Http\Request')

<header>
    <div class="container ads-menu-div clearfix">
        <div class="pull-left">
            <a class="logo-head " href="/home" title=""></a>
        </div>
        <div class=" pull-right menu_list">
            <a href="{{url('/login')}}" class="btn btn-empty w100 ads-font-14">LOGIN</a>
            <a href="{{url('/plan')}}" class="btn w100 head-btn ads-font-14 mobile-hidden">SIGN UP</a>
        </div>
    </div>
</header>
