@extends('layouts.user_master')

@section('css')

    <style>
        #wrapper {
          font-family: Lato;
          font-size: 1.5rem;
          text-align: center;
          box-sizing: border-box;
          color: #333;
        }
    </style>


@endsection

@section('content')
<!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1> <span>OTP Verification</span></h1>
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
                         
                        
                        <form id="otp-form" name="otp-form" action="{!! url('verify/otp') !!}" class="my-account-form" style="margin-top: -230px;background: #fff; border-radius: 35px 36px 0px 0px;" method="POST" enctype="multipart/form-data"> 
                          {{csrf_field()}}
                            <h2 style="padding-bottom:20px; font-style: normal; text-align: center;"><span class="text-green">OTP </span> VERIFICATION</h2>
                            <p   style="padding-bottom:20px; font-style: normal; text-align: center;" > Please enter the 4-digit verification code we sent via Email</p>

                            <input type="hidden" value="{{$user->id}}" name="user_id" required/>
                             
                            <div style="margin-left:10%">
                                      <input type="text" name="otp_1" required maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" style=" margin: 0 5px !important;text-align: center !important;line-height: 80px !important;font-size: 50px !important;border: solid 1px #ccc !important;box-shadow: 0 0 5px #ccc inset !important;outline: none !important;width: 20% !important;transition: all .2s ease-in-out !important;border-radius: 3px !important;"/>
                                      <input type="text" name="otp_2" required maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" style=" margin: 0 5px !important;text-align: center !important;line-height: 80px !important;font-size: 50px !important;border: solid 1px #ccc !important;box-shadow: 0 0 5px #ccc inset !important;outline: none !important;width: 20% !important;transition: all .2s ease-in-out !important;border-radius: 3px !important;"/>
                                      <input type="text" name="otp_3" required maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" style=" margin: 0 5px !important;text-align: center !important;line-height: 80px !important;font-size: 50px !important;border: solid 1px #ccc !important;box-shadow: 0 0 5px #ccc inset !important;outline: none !important;width: 20% !important;transition: all .2s ease-in-out !important;border-radius: 3px !important;"/>
                                      <input type="text" name="otp_4"  required maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" style=" margin: 0 5px !important;text-align: center !important;line-height: 80px !important;font-size: 50px !important;border: solid 1px #ccc !important;box-shadow: 0 0 5px #ccc inset !important;outline: none !important;width: 20% !important;transition: all .2s ease-in-out !important;border-radius: 3px !important;"/>
                                      <div class="col-md-12">

                                      <button id="user-login" type="submit" class="btn btn-primary btn-embossed verify-btn bg-green" style="margin-left: 38% !important; margin-top: 4%;width: auto !important;padding: 6px !important;border: none !important;text-transform: uppercase !important;">Verify</button>
                                      </div>
                                </div>
                                
                                <small id="emailHelp" class="form-text text-m" style="color: #9a080d;font-size: 13px;font-weight: 500;"></small>
        
                                <small id="emailHelpS" class="form-text text-t" style="color: #9a080d;font-size: 13px;font-weight: 500;"></small>
                  
  
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

	<script>
        $('#user-login').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#user-login").text('Processing....');

                        $("#user-login").prop('disabled', true);

                    },
                    success: function (response) {

                        $('#emailHelp').text('');

                        $("#user-login").prop('disabled', false);

                        $("#user-login").text('Verify');

                        if(response.status == 1) {
                            
                          /* $('#emailHelpS').text('Your account has been verified');  
                           window.location.href = "{{URL::to('/')}}"; */

                           setTimeout(function() {
                                swal({
                                    title: "Success!",
                                    text: "Registered Successfully!",
                                    type: "success"
                                }, function() {
                                    window.location = "{{URL::to('/')}}";;
                                });
                            }, 1000);

                        }
                        else if (response.status == 0) {

                            $('#emailHelp').text(response.message);

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#user-login").prop('disabled', false);

                        $("#user-login").text('Verify');

                        $('#emailHelp').text('Oops! Something went to wrong.');
                    }
                };
                $("#otp-form").ajaxForm(options);
            });
            
        $(function() {
          
          'use strict';
        
          var body = $('body');
        
          function goToNextInput(e) {
            var key = e.which,
              t = $(e.target),
              sib = t.next('input');
        
            if (key != 9 && (key < 48 || key > 57)) {
              e.preventDefault();
              return false;
            }
        
            if (key === 9) {
              return true;
            }
        
            if (!sib || !sib.length) {
              sib = body.find('input').eq(0);
            }
            sib.select().focus();
          }
        
          function onKeyDown(e) {
            var key = e.which;
        
            if (key === 9 || (key >= 48 && key <= 57)) {
              return true;
            }
        
            e.preventDefault();
            return false;
          }
          
          function onFocus(e) {
            $(e.target).select();
          }
        
          body.on('keyup', 'input', goToNextInput);
          body.on('keydown', 'input', onKeyDown);
          body.on('click', 'input', onFocus);
        
        })
	</script>


@endsection



