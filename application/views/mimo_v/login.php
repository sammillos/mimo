<body id="body">

<!-- count particles -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand navbar-link" href="#">
                <!--Logo-->
                <?php $this->load->view('include/mimologo')?>
                <!---->
                <strong>MimO</strong> </a>
                <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            </div>
			 <div class="collapse navbar-collapse" id="navcol-1">
				 <ul class="nav navbar-nav navbar-right">
					<li class="presentation" role="presentation"><a class="disabled">
					<p class=""> Have an account already? </p></a></li>
                    <li class="" role="presentation"><a href="<?php echo base_url('accounts/signin');?>" class=" btn bar">
					<p class="hidden-sm"><b>Login!</b></p></a></li>
				</ul>
				</div>
             
        </div>
    </nav>
    <!-- Sign up Form -->
   <div class="container">
        <div id="login-box">
            <div class="row">
                <div class="col-md-12">
                    <form class="form" role="form" method="post" action="" accept-charset="UTF-8" id="login-nav">
                        <legend>
                            <i class="material-icons">public</i></a> Sign up!</legend>
                        <div id="log"></div>
                        <div class="col-md-6">
                            <input id="first" class="form-control signupForm" name="firstname" placeholder="First Name" type="text" required autofocus />
                        </div>
                        <div class="col-md-6">
                            <input id="last" class="form-control signupForm" name="lastname" placeholder="Last Name" type="text"  />
                        </div>
                        <div class="col-md-12">
                        <input id="user" class="form-control signupForm" name="username" placeholder="Stage Name" type="text" required />
                        <input id="email" class="form-control signupForm" name="email" placeholder="you@email.com" type="email" required />
                        <input id="pass" class="form-control signupForm" name="password" placeholder="New Password" type="password" required />
                        <input id="rpass" class="form-control signupForm" name="password" placeholder="Repeat Password" type="password" required />
                        <br />
                        <br />
                        <button class="btn btn-lg btn-custom btn-block" type="button" name="signup" id="signup">Sign up</button>
                        <br />
                        
                        </div>
                    </form>
						<p class="text-center">or</p>
					<a href="<?php echo $authUrl;?>" role="button" style="font-size: 14px; width: 100%" class="btn btn-primary btn-md">
						<i class="fa fa-facebook-square"></i> &nbsp;Continue with Facebook</a>
                </div>
            </div>
        </div>
    </div>





<!--background-->
<div id="particles-js"></div>
<!-- scripts -->
<script src="http://localhost/mimo/assets/js/particles.js"></script>
<script src="http://localhost/mimo/assets/js/app.js"></script>
<!-- footer -->
<footer id="sticky"><a href="" style="color: white">Mimo</a> &copy&nbsp 2017</footer>



<script type="text/javascript">
    $('#signup').click(function(e) {
        e.preventDefault();
        $('#log').html("")
        var firstname = $("#first").val();
        var lastname = $("#last").val();
        var username = $("#user").val();
        var email = $("#email").val();
        var password = $("#pass").val();
        var rpassword = $("#rpass").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>accounts/createaccount',
            data:{
                firstname:firstname,
                lastname:lastname,
                username:username,
                email:email,
                password:password,
                rpassword:rpassword,
            },
            success: function(response){
                var res = JSON.parse(response)
                console.log(res);
                if(res.status != "success"){
                    $("#log").attr('class', 'alert alert-danger');
                    $( "#log" ).text(res.eventid);
                }
                else{
                    window.location = "http://localhost/mimo/";
                }
            },
            error: function(e){
                console.log(e);            }

        });
    });
</script>
</body>
