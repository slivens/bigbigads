<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<!-- <link rel="stylesheet" type="text/css" href="./static/bootstrap.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="./static/mobile.css"> -->
	<!-- <link rel="stylesheet" href="./static/animate.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="./static/demo.css"> -->
	<link rel="shortcut icon" type="image/x-icon" href="./static/images/favicon.ico" media="screen" />
	<meta name="description" content="A Facebook ads spy tool to help you find out the competitor marketing strategy, ad creatives, ad insight.">
	<title>Bigbigads Home | The Largest  Facebook Ad Examples to Smart Your Facebook Marketing</title>
	<link href="./dist/mobile.css?v=1" rel="stylesheet">
</head>
<body id="new_index">
	<!--begin header-->
	<div class="header clearfix">
		<div class="pull-left">
			<a class="ads-logo-link" href="/home" title="">
				<img class="ads-logo-png" src="/../assets/global/img/logo2.png" alt="">
			</a>
		</div>
		<!-- <a class="ads-menu text-center">
			<i class="ads-menu-icon fa fa-reorder"></i>
		</a> -->

		<!--toggle button-->
		<!-- <a href="/login" class="header-login-button pull-right">Login</a href="/login"> -->
	</div>

	<!--content-->
	<div class="content">
		
		<!--内容介绍框-->
		<div class="introduce-div clearfix">
			<p class="ads-font-32 introduce-title text-center">Largest Facebook Ad Examples To See:</p>
			<p id="changeWord" class="instroduce-bigword text-center animated">test</p>
			<p class="text-center instroduce-text">which offer you systematic advantage compare to your competitors</p>
			<div class="instroduce-data">
				
				<div class="data-content data-top">
					<div class="data-roomdiv data-roomdiv-left">
						<p class="data-number">5,000,000<sup>+</sup></p>
						<p class="data-text">Ads</p>
					</div>
					<div class="data-roomdiv">
						<p class="data-number">1,300,000<sup>+</sup></p>
						<p class="data-text">Advertisers</p>
					</div>
				</div>
				<div class="data-content data-bottom">
					<div class="data-roomdiv data-roomdiv-left">
						<p class="data-number">1,000,000<sup>+</sup></p>
						<p class="data-text">Monthly Updates</p>
					</div>
					<div class="data-roomdiv">
						<p class="data-number">90<sup>+</sup></p>
						<p class="data-text">Languages</p>
					</div>
				</div>
				<p class="ads-font-32 text-center instroduce-data-title">The Best Solution</p>
				<p class="text-center">For Advertisers，Agencies，Ad network & Publishers
				</p>
			</div>
			<a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
		</div>

		<!--begin register-->
		<div id="ads-register" class="register-div clearfix">
			<p class="ads-font-28 text-center ads-reg-title">Your online advertising,<br/> at its best. </p>
			<p class="ads-reg-text ads-font-16 text-center">Bigbigads'  facebook ad examples make sure you to create your Low-Cost, High-Performance ad campaign. </p>
			<a class="ads-reg-btn btn" href="/socialite/facebook">
				<i class="fa fa-facebook-square reg-btn-icon"></i>
				<span class="ads-font-18 reg-btn-text">Sign Up With Facebook</span>
			</a>
			<p class="ads-reg-line text-center">
				<span class="ads-underline"></span>
				<span class="ads-font-16 reg-line-text">or</span> 
				<span class="ads-underline"></span>
			</p>
			<form class="form-inline reg-input-form" method="POST" action="{{ url('/register') }}">
				 {{ csrf_field() }}
				<div class="reg-inp-email">
					<div class="form-group from-group-fname{{ $errors->has('email') ? ' has-error' : '' }}">
				   	 	<label class="sr-only" for="reg-email" class = "control-label">Email Address</label>
				    		<input type="email" class="form-control" id="email" placeholder="Email" maxlength="72" name="email" value="{{ old('email') }}" required>
				    		
				  	</div>
				  	@if ($errors->has('email'))
						<span class="help-block err-remin">
							{{ $errors->first('email') }}
						</span>
					@endif
				</div>
				<div class="reg-inp-name">
  					<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
	    					<label class="sr-only" for="reg-Name" class = "control-label">Name</label>
	    					<input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{ old('name') }}" required autofocus>	
	    					
  					</div>
  					@if ($errors->has('name'))
                    	<span class="help-block err-remin">
                    		<strong>{{ $errors->first('name') }}</strong>
                    	</span>
					@endif
				</div>
				
				<div class="reg-inp-password">
					<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
						<label class="sr-only" for="reg-password" class = "control-label">Password</label>
						<input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
					</div>
					@if ($errors->has('password'))
							<span class="help-block" err-remin>
								<strong>{{ $errors->first('password') }}</strong>
							</span>
					@endif
				</div>
				<button type="submit" class="btn reg-sub-btn ads-reg-button ">Try it Now <small>It's free</small></button>
				<p class="reg-policy-text ads-font-14 text-center">By signing up, you agree to the
				 <a href="terms_service">Terms and Conditions</a>
				  and 
				 <a href="privacy_policy ">Privacy Policy</a>. You also agree to receive product-related emails from Bigbigads from which you can unsubscribe at any time.
				 </p>
			</form>
		</div>
		<!--end register-->
		<!--展示-->
		<div class="ads-show">
			<div class="show-content">
				<p class="show-title ads-font-24 text-center">Leverage on your competitor's profitable marketing  now
			</p>
				<img src="static/images/home/show_01.jpg" alt="" class="show-img ads-center">
				<p class="ads-font-22 show-ins-title text-center">Facebook Marketing Strategy</p>
				<p class="ads-font-16 show-ins-text text-center">In a digital world, your competitor's marketing strategy is evolving quickly. Do you know what they are up to for an upcoming festival? Bigbigads does.</p>
				<a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
			</div>
			<div class="show-content">
				<p class="show-title ads-font-24 text-center">Build a Better Ad Campaign Through Competitive Intelligence</p>
				<img src="static/images/home/show_02.jpg" alt="" class="show-img ads-center">
				<p class="ads-font-22 show-ins-title text-center">Competitive Intelligence</p>
				<p class="ads-font-16 show-ins-text text-center">Find new competitors you don't know, track the ad campaign of existing competitors, find their winning ads, use the advantage to create your ads more efficiently.</p>
				<a href="#ads-register" class="btn introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
			</div>
			<div class="show-content">
				<p class="show-title ads-font-24 text-center">Accelerate success for your facebook marketing</p>
				<img src="static/images/home/show_03.jpg" alt="" class="show-img ads-center">
				<p class="ads-font-22 show-ins-title text-center">Creative inspiration</p>
				<p class="ads-font-16 show-ins-text text-center">Find out how to sell product with social proof, break through culture difference for the audience in a new geo, know what kind of ad resonates with the audience in a specific niche market, know your potential customer better.</p>
			</div>
			<a href="#ads-register" class="btn ads-font-22 introduce-reg-btn reg-sub-btn">Get Started <small>It's Free</small></a>
		</div>

	</div>
	@include('tpl.mobile_footer')
	<div class="footer"></div>
	<!--end header-->
<script type="text/javascript" src="dist/vendor.js?v=5" defer></script>
<script type="text/javascript" src="dist/mobile.js?v=5" defer></script>
<!-- <script src="./static/jquery-3.1.1.js"></script> -->
<!-- <script src="./static/js/mobile.js"></script> -->
</body>
</html>