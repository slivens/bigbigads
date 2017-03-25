<!--footer-->
<footer>
    <div class="container">
        <ul class="col-md-3">
            <li class="footer-title"> ABOUT BIGBIGADS</li>
            <li><a href="{{url('/about')}}">About Us</a></li>
            <li><a href="">Blog</a></li>
        </ul>
        <ul class="col-md-3">
            <li class="footer-title"> USING BIGBIGADS</li>
            <li><a href="">Features</a></li>
            <li><a href="">Pricing&Buy</a></li>
        </ul>
        <ul class="col-md-3">
            <li class="footer-title"> GETTING HELP</li>
            <li><a href="">Video Demos</a></li>
            <li><a href="">Contact Us</a></li>
        </ul>
        <ul class="col-md-3">
            <li class="footer-title"> STAY CONNECTES</li>
            <li>
                <a href="" class="f-icon icon-facebook"></a>
                <a href="" class="f-icon icon-in"></a>
                <!-- <a href="" class="f-icon icon-03"></a> -->
                <!-- <a href="" class="f-icon icon-yutobe"></a> -->
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
    <script>
        var url = window.location.href;
        var urlhost = window.location.origin;
        var menu = $(".head-nav").find('a');
        var currenturl = url.replace(urlhost,'');
        var href;
        if(currenturl=='/'){
            $(menu[0]).parent().addClass("active");
            $.each(menu,function(i){
                if(i==0) return true;
                $(menu[i]).parent().removeClass("active");
            });
        }else {
            $(menu[0]).parent().removeClass("active");
            $.each(menu,function(i){
                if(i==0) return true;
                href = $(menu[i]).attr("href");
                if($.trim(currenturl).indexOf($.trim(href).replace(urlhost,''))==0) {
                    $(menu[i]).parent().addClass("active");
                }else{
                    $(menu[i]).parent().removeClass("active");
                }
            });
        }
    </script>
</footer>
