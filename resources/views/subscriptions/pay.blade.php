<?php
use \Illuminate\Support\Facades\Input;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bigbigads') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="/static/images/favicon.ico" media="screen">
    <link href="./dist/pay.css?v=2.0.1" rel="stylesheet">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <div class="container pay">
            <div class="pay-div">
            @if ($errors->has('message'))
            <div class="alert alert-danger">
                 {{ $errors->first('message') }}
            </div>
            @endif
                <div class="pay-head hidden">
                    
                </div>
                <div class="pay-content">
                    <div class="alert pay-alert-gray pay-overview">
                        <div class="row overview-describe">
                            <div class="col-sm-7  descr-left-text ads-font-14">
                            <span class="paypage-title overview-title">Order Overview:</span>
                                <p class="ads-font-14" for="price" v-bind:extra='initAmount({{$plan->amount}})'>BIGBIGADS(<span class="">{{$plan->display_name}}</span>) - Get Inspired by most successful Facebook ad campaign of your competitors.Create your high-performance ads.</p>
                                 
                            </div>
                            <div class="col-sm-5 descr-right-text">
                                <p class="overview-cost ads-font-18">Today's Payment: 
                                    <span class="" v-cloak >$@{{ amount }}.00</span>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-7 ads-font-14">
                                BIGBIGADS - Would you like to know what's your competitor's profitable marketing strategy now?Join thousands of paid users to reveal it.
                            </div>
                            <div class="col-sm-5">
                                <div class="ads-font-14 text-right overview-notice">
                                    <p>Includes <span class="notice-pay-cycle">{{$plan->frequency_interval}}&nbsp;{{$plan->frequency}}</span> of service</p>
                                    <p>Future Payments: <span class="" v-cloak >$@{{ amount }}.00</span> will be billed every</p>
                                    <p> <span class="notice-pay-cycle">{{$plan->frequency_interval}}&nbsp;{{$plan->frequency}}</span> until cancelled</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form ref="checkout" action="{{url('/pay')}}" method="post">
                        {{ csrf_field() }}
                        <input  type="hidden" name="planid" value="{{$plan->id}}"/> 
                        <!--if is guest-->
                        
                        
                        <div class="alert alert-warning pay-coupon">
                        @if (Auth::guest())
                        <div class=" pay-email">
                            <div class="paypage-title coupon-title">Email</div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div v-bind:class="{'form-group':true, 'pay-email-div':true, 'has-error':emailErr}">
                                         <input type="email" class="form-control" placeholder="Email" name="Email" v-model="email" v-on:keyup="onEmailEnter" v-on:blur="onEmailEnter">
                                         <label v-bind:class="{'control-label':true, 'email-remain':true, 'ads-font-14':true, 'show':emailErr}">@{{emailMessage}}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 ads-font-14 email-text">
                                    Your default password is your email address, please change it to a secure password.
                                </div>
                            </div>
                        </div>
                        @endif
                            <div class="paypage-title coupon-title">Coupon</div>
                            <div class="row">
                                <div class="col-sm-6 coupon-form ">
                                     <div class="navbar-form ads-font-16">
                                         <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Coupon" v-model="coupon" name="coupon">
                                         </div>
                                         <button v-bind:class="{'btn':true, 'btn-primary':true,  'disabled':loading || !coupon}" v-bind:disabled="loading || !coupon" v-on:click="applyCoupon">Apply</button>
                                     </div>
                                </div>
                                <div class="col-sm-6 coupon-text ads-font-14">
                                    <p class="">Please Enter Coupon Code（Price will be updated in billing page)</p>
                                    <p class="conpon-text hidden">You pay the preferential: <span class="" v-cloak>$@{{discount}}.00</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="alert pay-payment">
                            <div class="paypage-title payment-title">Payment Options</div>
                            <div class="row payment-div">
                                <div class="col-sm-6 ads-font-14">
                                    <div class="payment-text">Pay Using PayPal: Send money securely in a few clicks, with no need for bank details.</div>
                                    <img src="static/images/pay/paypal01.png" alt="">
                                </div>
                                <div class="col-sm-6 payment-right-div">
                                    <!--hidden it for a moment-->
                                    <!-- <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                          <input type="checkbox"> <span class="ads-font-14">Check the box to use Credit Card through PayPal</span> 
                                        </label>
                                    </div> -->
                                    @if (Auth::guest())
                                    <div class="payment-btn text-center">
                                        <button type="button" border="0" alt="PayPal - The safer, easier way to pay online!" class="paypal-btn" v-on:click="toRegister" v-bind:disabled="emailErr"></button>
                                    </div>
                                    @else
                                    <div class="payment-btn text-center">
                                        <input type="button" border="0" alt="PayPal - The safer, easier way to pay online!" class="btn paypal-btn" value="" v-on:click="toCheckout" >
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="or-line">
                                <span class="under-line"></span>
                                <span class="under-line-word">OR</span>
                                <span class="under-line"></span>
                            </div>
                            <div class="row pay-creditcard">
                                <div class="col-sm-6 ads-font-14">
                                   
                                </div>
                                <div class="col-sm-6 creditcard-btn clearfix">
                                    <div class="text-center">
                                        <button class="btn ads-font-22" disabled="disabled"><i class="glyphicon glyphicon-credit-card credit-icon ads-font-24"></i>Credit Card</button>
                                    </div>
                                    
                                    <div class="text-center safety-signs">
                                         <img src="static/images/pay/verified_secured_pic01.gif" alt="">
                                    </div>
                                </div>
                                <div class="creditcard-disable text-center ads-font-22" disabled="disabled" > Coming Soon:Check out with Credit Card</div>
                            </div>
                            <hr>
                            <h4>Terms of Sales:</h4>
                            <ul class="ads-font-14">
                                <li>Your IP is <span>@{{ipAddr}}</span> - This has been recorded. All attempts at fraud will be prosecuted.</li>
                                <li>This product is created and sold by BIGBIGADS.INC,the product's vendor.</li>
                                <li>If you're not fully satisfied with BIGBIGADS‘ service within 7 days, we'll refund 100% of your purchase price! First-time buyers only.</li>
                                <li>Please send Email to help@bigbigads.com for any advice or support request.</li>
                                <li>The vendor of this product reserves the right to do business or not do business with whom they choose.</li>
                            </ul>
                        </div>
                    </form>
                    <sweet-modal icon="warning" ref="modal">
                     @{{errorMessage}}
                    </sweet-modal>
                    <div class="row pay-footer ads-font-14">
                        <div class="col-sm-5 text-center">
                            <span> &copy;2017 BIGBIGADS.COM</span>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="col-sm-7 text-center">
                           <span class=""><a href="/terms_service" target="_blank">Terms of Service</a></span>
                           <span class="border-left"><a href="/privacy_policy" target="_blank">Privacy Policy</a></span>
                           <span class="border-left"><a href="mailto:sale@bigbigads.com">Contact Us</a></span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div v-bind:class="{'pay-loading':true, 'hidden':showLoading}">
            <img src="./static/images/pay/pay-loading.gif" alt="" class="load-gif">
            <p class="loading-text ads-font-18">loading...</p>
        </div>
    </div>
</body>
</html>
<script src="http://pv.sohu.com/cityjson?ie=utf-8"></script> 
<script type="text/javascript" src="dist/vendor.js?v=1"></script>
<script type="text/javascript" src="dist/pay.js?v=2.0.1"></script>
 

