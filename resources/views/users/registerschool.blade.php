@extends('layouts.user_master')
@section('content')
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1> <span> </span></h1>
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
                         
                        
                        <form id="registerSchoolForm" action="{!! url('signup') !!}" class="my-account-form" style="margin-top: -230px;background: #fff; border-radius: 35px 36px 0px 0px;" method="POST"> 
                        	{{csrf_field()}}
                            <h2 style="padding-bottom:20px; font-style: normal; text-align: center;"><span class="text-green">REGISTER </span> AS SCHOOL</h2>
                            <p   style="padding-bottom:20px; font-style: normal; text-align: center;" > JOIN OUR TRAINING CLUB AND RISE TO NEW CHALLENGE </p>

                            <div class="main">

                                <div class="col-md-6 float-left">
                                <label for="username"> School Name *</label>
                                <input type="text" id="school_name" name="school_name" required minlength="3" maxlength="200">
                                </div>
                                <div class="col-md-6 float-left">
                                <label for="username"> Address *</label>
                                <input type="text" id="school_address" name="school_address" required minlength="3" maxlength="200">
                                </div>
                                <div class="col-md-6 float-left">
                                <label for="username"> Email *</label>
                                <input type="email" id="email" name="email" required minlength="3" maxlength="200">
                                </div>
                                <div class="col-md-6 float-left">
                                <label for="username"> Mobile *</label>
                                <input type="text" id="mobile" name="mobile" required minlength="10" maxlength="10" onkeypress="return isNumber(event)" >
                                </div>
                                <div class="col-md-6 float-left">
                                <input type="hidden" name="country_id" id="country_id" value="1">

                                <label for="username"> State *</label>
                                <select id="state_id" name="state_id" class="state_id" required onchange="loadDistricts1(this);">
                                	<option value=""> Select State </option>
                                </select>
                                </div>
                                <div class="col-md-6 float-left">
                                <label for="username"> City *</label>
                                <select id="city_id" name="city_id" class="district_id" required >
                                	<option value=""> Select City </option>
                                </select>
                                </div> 

                            </div>
                            <div class="sub d-none">

                                <div class="col-md-6 float-left">
                                    <label for="username"> Pincode </label>
                                    <input type="text" id="pincode" name="pincode"  minlength="5" maxlength="6" onkeypress="return isNumber(event)" >
                                </div>
                                <div class="col-md-6 float-left">
                                    <label for="username"> Youtube id </label>
                                    <input type="text" id="youtube_id" name="youtube_id"  minlength="6" maxlength="15"> 
                                </div>

                                <div class="col-md-12 float-left"> 
                                    <input type="button" name="skip" id="skip" class="btn bg-green" value="Skip" style="width:auto;"> 
                                </div>
                            </div>
                            <div class="col-md-12 float-left"> 
                                <input type="submit" name="submit" id="signupBtn" class="btn bg-green" value="Register" style="width:auto;"> 
                            </div>
                            <div class="form_block d-none" id="helpBlock">  </div>
                            
                            <div class="row">
                                <div class="col-md-12 ">
                                    <p style="padding: 15px 0px;">Already have an account? <span style="text-decoration:underline;cursor: pointer;" class="text-green"  onclick="window.location.href='{{URL('/')}}/login'"  >Login</span></p> 
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
	$(function () { 
		loadStates();
	})

    function showfrms() {
        $('.main').addClass('d-none');
        $('.sub').removeClass('d-none');
        return false;
    }

    $('#skip').on('click', function () {
        $("#skip").val('Processing..'); 
        $("#skip").prop('disabled', true);
        submitform();
    })

	$("#registerSchoolForm").submit(function(e) { 
            e.preventDefault();  
            var errormsg = '';
            if(errormsg != '') {
                $('#helpBlock').text(errormsg);
                $('#helpBlock').css('color', '#f00');
                $('#helpBlock').removeClass('d-none');
                return false;
            }   else {
                $('#helpBlock').text(errormsg);
                $('#helpBlock').css('color', '#f00');
                $('#helpBlock').addClass('d-none');
            } 

            var main = $('.main').hasClass('d-none');
            var sub = $('.sub').hasClass('d-none');

            if(main === false) {
                console.log('1')
                $('.main').addClass('d-none');
                $('.sub').removeClass('d-none');
            }   else {
                console.log('2')
                var pincode = $('#pincode').val();
                var youtube_id = $('#youtube_id').val();

                pincode = $.trim(pincode);
                youtube_id = $.trim(youtube_id);

                if(pincode == '') {
                    $('#helpBlock').text('Please enter the Pincode');
                    $('#helpBlock').css('color', '#f00');
                    $('#helpBlock').removeClass('d-none');
                    return false;
                }

                if(youtube_id == '') {
                    $('#helpBlock').text('Please enter the Youtube id');
                    $('#helpBlock').css('color', '#f00');
                    $('#helpBlock').removeClass('d-none');
                    return false;
                }

                if(pincode != '' && youtube_id != '') {
                    submitform();
                }
                
            }
            
        });

    function submitform() {
        var form = $("#registerSchoolForm");
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

                $("#signupBtn").val('SUBMIT');

                $("#skip").val('Skip'); 
                $("#skip").prop('disabled', false);

            }
        })
        .done(function(data) {
            if(data.status == 1){
                //window.location.replace("{{URL('/')}}/home");
                window.location.href = "{{URL::to('/otp/screen')}}";
            }
            else if(data.status == 0){  
                $('#helpBlock').text(data.message!=undefined?data.message.toUpperCase():data.message);
                $('#helpBlock').css('color', '#f00');
                $('#helpBlock').removeClass('d-none');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            var message = jqXHR.responseJSON.message;
            $('#helpBlock').text(message);
        });
    }
</script>
@endsection