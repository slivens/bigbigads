<!--footer-->
<footer>
    <div class="container">
        <ul class="col-md-3">
            <li class="footer-title"> ABOUT BIGBIGADS</li>
            <li><a href="{{url('/about')}}">About Us</a></li>
            <li><a href="{{url('/blog')}}">Blog</a></li>
        </ul>
        <ul class="col-md-3">
            <li class="footer-title"> USING BIGBIGADS</li>
            <li><a href="{{url('./product')}}">Features</a></li>
            <li><a href="{{url('/pricing')}}" >Pricing&Buy</a></li>
        </ul>
        <ul class="col-md-3">
            <li class="footer-title"> GETTING HELP</li>
            <li><a href="https://www.youtube.com/channel/UCtXk7wpkmVO7SdStR4JwX0A" target="_blank">Video Tutorial</a></li>
            <li><a href="javascript:Intercom('show');">Contact Us</a></li>
        </ul>
        <ul class="col-md-3">
            <li class="footer-title"> STAY CONNECTES</li>
            <li>
                <a href="" class="footer-icon demoicon icon-facebook"></a>
                <a href="" class="footer-icon demoicon icon-linkedin2 hidden"></a>
                <a href="" class="footer-icon demoicon icon-twitter hidden"></a>
                <a href="" class="footer-icon demoicon icon-youtube"></a>
            </li>
        </ul>
    </div>
    <script src="static/jquery-3.1.1.js"></script>
    <script>
      window.intercomSettings = {
        app_id: "pv0r2p1a"
      };
    </script>
    <script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/pv0r2p1a';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
</footer>
