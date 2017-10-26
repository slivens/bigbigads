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
    <link href="{{bba_version('pay.css')}}" rel="stylesheet">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <!-- Hotjar Tracking Code for http://www.bigbigads.com -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:455748,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    @include('tpl.script')
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
                                    <span class="">
                                        <span v-if="!discount">${{$plan->amount}}</span>
                                        <span v-cloak v-if="discount">$@{{ (amount - discount).toFixed(2) }}</span> 
                                        <span class="ads-font-12" v-cloak v-if="discount">
                                            (- $@{{discount.toFixed(2)}})
                                        </span>
                                    </span>
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
                                    <p>Future Payments: <span class="">${{ $plan->amount }}.00</span> will be billed every</p>
                                    <p> <span class="notice-pay-cycle">{{$plan->frequency_interval}}&nbsp;{{$plan->frequency}}</span> until canceled</p>
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
                                    <div class="form-group">
                                        <div v-bind:class="{'pay-email-div':true, 'has-error':emailErr}">
                                             <input type="email" class="form-control" placeholder="Email" name="Email" v-model="email" v-on:keyup="onEmailEnter" v-on:blur="onEmailEnter">
                                             <label v-cloak v-bind:class="{'control-label':true, 'email-remain':true, 'ads-font-14':true, 'show':emailErr}">@{{emailMessage}}</label>
                                        </div>
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
                                         <button type="button" v-bind:class="{'btn':true, 'btn-primary':true,  'disabled':loading || !coupon}" v-bind:disabled="loading || !coupon" v-on:click="applyCoupon">Apply</button>
                                     </div>
                                </div>
                                <div class="col-sm-6 coupon-text ads-font-14">
                                    <p class="">Please Enter Coupon Code（Price will be updated in billing page)</p>
                                    <p class="conpon-text hidden">You pay the preferential: <span class="" v-cloak>$@{{discount}}.00</span></p>
                                </div>
                            </div>
                        </div>
                    </form>
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
                                    <!-- Begin DigiCert site seal HTML and JavaScript -->
                                    <div id="DigiCertClickID_-MoxKleO" data-language="en">
                                        <a href="https://www.digicert.com/ev-ssl-certification.htm"></a>
                                    </div>
                                    <!-- End DigiCert site seal HTML and JavaScript -->
                                </div>
                            </div>
                            <div class="or-line">
                                <span class="under-line"></span>
                                <span class="under-line-word">OR</span>
                                <span class="under-line"></span>
                            </div>

                            <form v-on:submit.prevent ref="payform" action="/pay" method="post" id="payment-form">
                                <div class="row pay-creditcard">
                                    <div class="col-sm-6 ads-font-14">
                                            {{ csrf_field() }}

                                            <input type="hidden"  v-model="token" name="stripeToken">
                                            <input type="hidden"  value="{{$plan->id}}" name="planid">
                                            <input type="hidden"  value="stripe" name="payType">
                                            <div class="form-row">
                                                <label for="card-element">
                                                    Credit or debit card
                                                </label>
                                                <card class='stripe-card'
                                                       :class='{ complete }'
                                                       stripe='{{$key}}'
                                                       :options='stripeOptions'
                                                       @change='complete = $event.complete'
                                                       />
                                            </div>
                                    </div>
                                    <div class="col-sm-6 creditcard-btn clearfix">
                                        <div class="text-center">
                                            <button class="btn ads-font-22" @click='pay' :disabled="!complete"><i class="glyphicon glyphicon-credit-card credit-icon ads-font-24"></i>Credit Card</button>
                                        </div>
                                        <div class="text-center safety-signs">
                                             <img src="static/images/pay/verified_secured_pic01.gif" alt="">
                                        </div>
                                    </div>

                                    <div class="creditcard-disable text-center ads-font-22" disabled="disabled" > Coming Soon:Check out with Credit Card</div>
                                </div>
                            </form>
                            <hr>
                            <h4>Terms of Sales:</h4>
                            <ul class="ads-font-14">
                                <li>This product is created and sold by BIGBIGADS.INC,the product's vendor.</li>
                                <li>If you're not fully satisfied with BIGBIGADS‘ service within 7 days, we'll refund 100% of your purchase price! First-time buyers only.</li>
                                <li>Please send Email to help@bigbigads.com for any advice or support request.</li>
                                <li>The vendor of this product reserves the right to do business or not do business with whom they choose.</li>
                            </ul>
                        </div>
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

    <script type="text/javascript">
    var
     __dcid = __dcid || [];__dcid.push(["DigiCertClickID_-MoxKleO", "10",
    "m", "black", "-MoxKleO"]);(function(){var
    cid=document.createElement("script");cid.async=true;cid.src="//seal.digicert.com/seals/cascade/seal.min.js";var s = document.getElementsByTagName("script");var ls = s[(s.length - 1)];ls.parentNode.insertBefore(cid, ls.nextSibling);}());
    </script>
    <!-- biying analysis -->
    <!-- 
        发现resources下有的页面有引用foot.blade.php,有的没有,造成添加统计代码混乱,先单独
        添加该页面的谷歌统计和必应统计,下个礼拜重新整理和优化统计代码
     -->
    <script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"5713181"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script><noscript><img src="//bat.bing.com/action/0?ti=5713181&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>
</body>
</html>

<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script type="text/javascript" src="{{bba_version('vendor.js')}}" defer></script>
<script type="text/javascript" src="{{bba_version('pay.js')}}" defer></script>
 

