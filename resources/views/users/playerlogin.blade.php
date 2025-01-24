@extends('layouts.user_master')
@section('content')
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1> <span></span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Page Hero <<===== 
    =========================== -->


    <!-- ===========================
    =====>> My Account <<===== -->
    <section id="my-account-area" class="pt-80 pb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="my-account-content">
                        <form class="my-account-form" id="user-login-form" action="{!! url('playerlogin') !!}" method="POST"  enctype="multipart/form-data" style="margin-top: -230px;background: #fff; border-radius: 35px 36px 0px 0px;">

				        {{csrf_field()}} 
                            <h2 style="padding-bottom:20px; font-style: normal; text-align: center;"><span class="text-green">LOGIN </span> AS PLAYER</h2>
                            <p   style="padding-bottom:20px; font-style: normal; text-align: center;" > JOIN OUR TRAINING CLUB AND RISE TO NEW CHALLENGE </p>
                            <!-- <label for="school">Login as School</label>
                            <input type="radio" id="school" name="logintype" value="1" checked>
                            <label for="academy">Login as Academy</label>
                            <input type="radio" id="academy" name="logintype" value="2"> -->

                            <input type="hidden" name="logintype" id="logintype" value="2">

                            <label for="username">Enter Your Email *</label>
                            <input type="email" id="email" name="email"  required minlength="3" maxlength="200"> 

                            <div class="form_block d-none" id="helpBlock">  </div>
                    		<input type="submit" name="submit" id="signupBtn" class="btn bg-green" value="Login" style="width:auto;"> 

                           <div class="row">
                                <div class="col-md-12 ">
                                    <p style="padding: 15px 0px;">Don't have an account? <span style="text-decoration:underline; cursor: pointer;" class="text-green"  onclick="window.location.href='{{URL('/')}}/registerplayer'"  >Register Now</span></p> 
                                </div>
                          
                           
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End My Account <<===== 
    =========================== -->
@endsection

@section('scripts')
<script type="text/javascript">
	$("#user-login-form").submit(function(e) { 
            e.preventDefault();  
            var errormsg = '';
            if(errormsg != '') {
                $('#helpBlock').text(errormsg);
                $('#helpBlock').css('color', '#3c763d');
                $('#helpBlock').removeClass('d-none');
                return false;
            }   else {
                $('#helpBlock').text(errormsg);
                $('#helpBlock').css('color', '#3c763d');
                $('#helpBlock').addClass('d-none');
            } 

            var form = $(this);
            var actionUrl = form.attr('action');
            var requestData = form.serialize();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: actionUrl,
                data: requestData,
                beforeSend: function( xhr ) {
                    $("#signupBtn").val('Processing..');

                    $("#signupBtn").prop('disabled', true);
                },
                complete: function( xhr ){
                    $("#signupBtn").prop('disabled', false);

                    $("#signupBtn").val('Login');
                }
            })
            .done(function(data) {
                if(data.status == 1){
                    if(data.data.is_otp_verified == 1) {
                        window.location.replace("{{URL('/')}}/home");
                    }   else {
                        window.location.href = "{{URL::to('/otp/screen')}}";
                    }
                }
                else if(data.status == 0){  
                    $('#helpBlock').text(data.message!=undefined?data.message.toUpperCase():data.message);
                    $('#helpBlock').css('color', '#3c763d');
                    $('#helpBlock').removeClass('d-none');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                var message = jqXHR.responseJSON.message;
                $('#helpBlock').text(message);
            });
        });
</script>
@endsection