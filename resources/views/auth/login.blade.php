<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>Bigbigads -  Login</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Bigbigads Login Description " name="description" />
        <meta content="bigbigads" name="author" />
        <link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" /> 
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!--<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />-->
        <link href="./assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />

        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="./assets/global/css/components-md.css" rel="stylesheet" id="style_components" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="./assets/pages/css/login-3.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
        <link href="./dist/home.css?v=1" rel="stylesheet">
        @include('tpl.script')
         </head>
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGO -->


        <div class="logo text-center">
            <a href="/" style="color:white;text-decoration:none;" class="bigbigads-logo">
                <!-- <h2 style="font-family:'Times New Roman'">Bigbigads</h2> -->
                <!-- <img src="./assets/global/img/logo.png" alt="" /> -->
            </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form class="login-form" action="{{url('/login')}}" method="post" role="form">
                {{ csrf_field() }}
                <h3 class="form-title">Login</h3>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter any E-mail and password. </span>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">E-mail</label>
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="E-mail" name="email" value="{{old('email')}}" required /> </div>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" required/> </div>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                </div>
                @if (Voyager::setting('captcha') && strpos(Voyager::setting('captcha'), 'login') !== FALSE)
                @if (Voyager::setting('captcha_type') === 'recaptcha')

                <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                    {!! Recaptcha::render() !!}
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="help-block">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </span>
                        @endif
                </div>
                @else
                <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                    <label class="control-label visible-ie9">Captcha</label>
                    <div class="input-group">
                        <input class="form-control" type="text" autocomplete="off"  placeholder="captcha" name="captcha" /> 
                        <span class="input-group-addon"><a href="javascript:"><img class="captcha" src="{{ captcha_src() }}" alt="captcha" /></a></span>
                    </div>
                        @if ($errors->has('captcha'))
                            <span class="help-block">
                                <strong>{{ $errors->first('captcha') }}</strong>
                            </span>
                        @endif
                </div>
                @endif
                @endif
                <!--remember me, forget password-->
                <div class="form-actions row">
                    <div class="col-md-6 col-xs-6 rememberme-div">
                        <label class="rememberme mt-checkbox mt-checkbox-outline">
                            <input type="checkbox" name="remember" value="1" /> Remember me
                            <span></span>
                        </label>
                    </div>
                    <div class="forget-password col-md-6 col-xs-6 text-right">

                        <label>
                            <a href="javascript:;" id="forget-password" href="{{ url('/password/reset') }}">
                                Forget your password?
                            </a> 
                        </label>
                    </div> 
                </div>

                <div class="clearfix login-button">
                    <button type="submit" class="btn pull-right"> Login </button>
                </div>
                <div class="register-line">
                    <span class="underline"></span>
                    <span class="line-word">or</span>
                    <span class="underline"></span>
                </div>
                <div class="form-group text-center">
                    <!-- <a href="/socialite/github" class="github"><i class="fa fa-github fa-3x"></i></a> -->
                    <a href="/socialite/facebook" class="register-btn register-fb-btn btn  socialite disabled">
                        <i class="fa fa-facebook-square reg-btn-icon"></i>
                        <span class=" reg-btn-text">Log In With Facebook</span>
                    </a>
                    <a href="/socialite/linkedin" class="register-btn register-linkedin-btn btn  socialite disabled">
                        <i class="fa fa-linkedin-square reg-btn-icon"></i>
                        <span class=" reg-btn-text">Log In With Linknedin</span>
                    </a>
                    <a href="/socialite/google" class="register-btn btn socialite disabled">
                        <i class="fa fa-google-plus-square reg-btn-icon"></i>
                        <span class="reg-btn-text">Log In With Google+</span>
                    </a>
                </div>
                <div class="login-footer row">
                    <!--create an acount-->
                    <div class="col-xs-12 text-left">
                    <p> Don't have an account?&nbsp;
                        <a  id="register-btn" href="{{url('/register')}}" >Sign Up</a>
                    </p>
                    </div>
                </div>

            </form>
            <!-- END LOGIN FORM -->
            <!-- BEGIN FORGOT PASSWORD FORM -->
            <form class="forget-form" action="{{url('/password/email')}}" method="post">
                {{ csrf_field() }}
                <h3 class="forget-title form-title">Forget Password ?</h3>
                <p class="forget-text"> Enter your e-mail address below to reset your password. </p>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" value="{{old('email')}}" /> </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-actions margin-top-20 button-div">
                    <button type="submit" class="btn submit-btn margin-top-30"> Submit </button>
                </div>
                <class class="margin-top-30">
                    <p>Try again to <a id="back-btn" class="grey-salsa btn-outline" href="{{url('/login')}}" >Sign in</a></p>
                </class>
            </form>
            <!-- END FORGOT PASSWORD FORM -->
            <!-- BEGIN REGISTRATION FORM -->
            <form class="register-form " action="{{url('/register')}}" method="post">
                {{ csrf_field() }}
                <h3 class="form-title">Sign Up</h3>
                <a href="/socialite/facebook" class="register-btn register-fb-btn btn  facebook socialite disabled" >
                    <i class="fa fa-facebook-square reg-btn-icon"></i>
                    <span class=" reg-btn-text">Sign Up With Facebook</span>
                </a>
                <a href="/socialite/linkedin" class="register-btn register-linkedin-btn btn  socialite disabled">
                    <i class="fa fa-linkedin-square reg-btn-icon"></i>
                    <span class=" reg-btn-text">Sign Up With Linknedin</span>
                </a>
                <a href="/socialite/google" class="register-btn btn socialite disabled">
                    <i class="fa fa-google-plus-square reg-btn-icon"></i>
                    <span class="reg-btn-text">Sign Up With Google+</span>
                </a>
                <div class="register-line">
                    <span class="underline"></span>
                    <span class="line-word">or</span>
                    <span class="underline"></span>
                </div>
                <p> Enter your account details below: </p>
                <input type="hidden" name="track" value="" />
                <!--full name-->
                <!-- <div class="form-group hidden">
                    <label class="control-label visible-ie8 visible-ie9">Full Name</label>
                    <div class="input-icon">
                        <i class="fa fa-font"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="Full Name" name="fullname" /> </div>

                </div> -->

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input class="form-control placeholder-no-fix" id="register-email" type="text" placeholder="Email" name="email" value="{{old('email')}}" checkEmail/> </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                </div>

                <!-- <p> Enter your account details below: </p> -->
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="control-label visible-ie8 visible-ie9">Username</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input class="form-control placeholder-no-fix" id="register-username" type="text" autocomplete="off" placeholder="Username" name="name" value="{{old('name')}}" /> </div>

                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="control-label visible-ie9">Password</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password" placeholder="Password" name="password" /> </div>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                </div>

                <div class="form-actions button-div">
                    <button type="submit" id="register-submit-btn" class="btn signup-btn margin-top-30"> Sign Up </button>
                </div>

                <p class="text-center margin-top-15">By signing up, you agree to the
                 <a href="terms_service">Terms and Conditions</a>
                  and 
                 <a href="privacy_policy ">Privacy Policy</a>. You also agree to receive product-related emails from Bigbigads from which you can unsubscribe at any time.
                 </p>
                <div class="margin-top-30">
                <p>
                        If you have the account click to
                        <a id="register-back-btn" type="button" class=" grey-salsa btn-outline" href="{{url('/login')}}">Log in </a> 
                </p>
                </div>
            </form>
            <!-- END REGISTRATION FORM -->

        </div>


        <div class="backgrounddiv backstretch">
            <img src="/static/images/banner2.jpg">
        </div>

        <div class="submit-background hidden" id="submit-background">
            <img src="/assets/global/img/ajax-modal-loading.gif" class="img-loading">
        </div>
        <!-- END LOGIN -->
        <!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<script src="../assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="./assets/global/plugins/jquery.min.js" type="text/javascript" defer></script>
        <script src="./assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript" defer></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="./assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript" defer></script>
        <script src="./assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript" defer></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="./assets/pages/scripts/login.js?v=4" type="text/javascript" defer></script>
<script type="text/javascript">
        (function() {
            var i;
            var eles;
            var track = null;
            if (window.localStorage.getItem('track')) {
                    track = JSON.parse(window.localStorage.getItem('track'));
                    if (Date.parse(new Date()) < Date.parse(track.expired)) {
                        eles = document.querySelectorAll('[name=track]');
                        for (i = 0; i <  eles.length; ++i) {
                            eles[i].value = track.code;
                        }
                    }
            }
            eles = document.querySelectorAll('.socialite');
            for (i = 0; i < eles.length; ++i) {
                if (track) {
                    var href = eles[i].href;
                    href += "?track=" + track.code;
                    eles[i].href = href;
                }
                eles[i].classList.remove('disabled');
            }
        })();
        </script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
        <script>
            (function(h,o,t,j,a,r){
                h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
                h._hjSettings={hjid:455748,hjsv:5};
                a=o.getElementsByTagName('head')[0];
                r=o.createElement('script');r.async=1;
                r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
                a.appendChild(r);
            })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
        </script>
    </body>
</html>

