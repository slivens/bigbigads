<!--footer-->
<footer>
    <div class="container">
        <ul class="col-md-3 col-sm-6">
            <li class="footer-title"> ABOUT BIGBIGADS</li>
            <li><a href="{{url('/about')}}">About Us</a></li>
            <li><a href="{{url('/methodology')}}">Methodology</a></li>
            <li class="hidden"><a href="{{url('/blog')}}">Blog</a></li>
            <li><a href="{{url('/terms_service')}}">Terms Service</a></li>
            <li><a href="{{url('/privacy_policy')}}">Privacy Policy</a></li>
        </ul>
        <ul class="col-md-3 col-sm-6">
            <li class="footer-title"> USING BIGBIGADS</li>
            <li><a href="{{url('./extension')}}">Extension</a></li>
            <li><a href="{{url('./product')}}">Features</a></li>
            <li><a href="{{url('/pricing')}}" >Pricing&amp;Buy</a></li>
        </ul>
        <ul class="col-md-3 col-sm-6">
            <li class="footer-title"> GETTING HELP</li>
            <li><a href="https://www.youtube.com/channel/UCtXk7wpkmVO7SdStR4JwX0A" target="_blank">Video Tutorial</a></li>
            <li><a href="mailto:sale@bigbigads.com">Contact Us</a></li>
            <li><a href="http://support.bigbigads.com" target="_blank">Support</a></li>
        </ul>
        <ul class="col-md-3 col-sm-6">
            <li class="footer-title"> STAY CONNECTED</li>
            <li>
                <a href="https://www.facebook.com/1869675289915326" class="footer-icon demoicon icon-facebook"></a>
                <a href="" class="footer-icon demoicon icon-linkedin2 hidden"></a>
                <a href="" class="footer-icon demoicon icon-twitter hidden"></a>
                <a href="https://youtu.be/xo9cSuR50Js" class="footer-icon demoicon icon-youtube"></a>
            </li>
        </ul>
    </div>
    
@if (!is_psi_agent()) 
    <script>
      window.intercomSettings = {
        app_id: "pv0r2p1a"
      };

    </script>
    <script>
        (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/pv0r2p1a';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()
    </script>
    <!-- Hotjar Tracking Code for bigbigads.com -->
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
    <script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"5713181"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script><noscript><img src="//bat.bing.com/action/0?ti=5713181&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>
@endif
</footer>
