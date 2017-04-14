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
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="./assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="./assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="./assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="./dist/global/css/components-md.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../dist/global/css/plugins-md.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="./dist/pages/css/login-3.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="stylesheet" type="text/css" href="./static/custom.css">
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
                
                <div class="login-footer row">
                    <!--create an acount-->
                    <div class="col-xs-12 text-left">
                    <p> Don't have an account?&nbsp;
                        <a href="javascript:;" id="register-btn" href="{{url('/register')}}" >Sign Up</a>
                    </p>
                    </div>
                </div>
                
                <!-- <div class="login-options">
                    <h4>Or login with</h4>
                    <ul class="social-icons">
                        <li>
                            <a class="facebook" data-original-title="facebook" href="javascript:;"> </a>
                        </li>
                        <li>
                            <a class="twitter" data-original-title="Twitter" href="javascript:;"> </a>
                        </li>
                        <li>
                            <a class="googleplus" data-original-title="Goole Plus" href="javascript:;"> </a>
                        </li>
                        <li>
                            <a class="linkedin" data-original-title="Linkedin" href="javascript:;"> </a>
                        </li>
                    </ul>
                </div> -->
                <!--forget password-->  
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
                    <p>Try again to <a id="back-btn" class="grey-salsa btn-outline">Sign in</a></p>
                </class>
            </form>
            <!-- END FORGOT PASSWORD FORM -->
            <!-- BEGIN REGISTRATION FORM -->
            <form class="register-form" action="{{url('/register')}}" method="post">
                {{ csrf_field() }}
                <h3 class="form-title">Sign Up</h3>
                <p> Enter your account details below: </p>

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
                        <input class="form-control placeholder-no-fix" id="register-email" type="text" placeholder="Email" name="email" value="{{old('email')}}" /> </div>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                </div>

                <!--address-->
                <!-- <div class="form-group hidden">
                    <label class="control-label visible-ie8 visible-ie9">Address</label>
                    <div class="input-icon">
                        <i class="fa fa-check"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="Address" name="address" /> </div>
                </div> -->

                <!--city town-->
                <!-- <div class="form-group hidden">
                    <label class="control-label visible-ie8 visible-ie9">City/Town</label>
                    <div class="input-icon">
                        <i class="fa fa-location-arrow"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="City/Town" name="city" /> </div>
                </div> -->

                <!--contry select-->
                <!-- <div class="form-group hidden">
                    <label class="control-label visible-ie8 visible-ie9">Country</label>
                    <select name="country" id="country_list" class="select2 form-control">
                        <option value=""></option>
                        <option value="AF">Afghanistan</option>
                    </select>
                </div> -->
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
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password" placeholder="Password" name="password" /> </div>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                </div>

                <!--comfirm password-->
                <!-- <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
                    <div class="controls">
                        <div class="input-icon">
                            <i class="fa fa-check"></i>
                            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-type Your Password" name="password_confirmation" /> </div>
                    </div>
                </div> -->

                <!--agree checkbox-->
                <div class="form-group">
                    <label class="mt-checkbox mt-checkbox-outline">
                        <input type="checkbox" name="tnc" checked/> I agree to the
                        <a href="{{url('/terms_service')}}">Terms of Service </a> &
                        <a href="{{url('/privacy_policy')}}">Privacy Policy </a>
                        <span></span>
                    </label>
                    <div id="register_tnc_error"> </div>
                </div>

                <div class="form-actions button-div">
                    <button type="submit" id="register-submit-btn" class="btn signup-btn margin-top-30"> Sign Up </button>
                </div>
                <div class="margin-top-30">
                <p>
                        If you have the account click to
                        <a id="register-back-btn" type="button" class=" grey-salsa btn-outline">Sign in </a> 
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
        <script src="./assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="./assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="./assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="./dist/app.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="./assets/pages/scripts/login.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>
