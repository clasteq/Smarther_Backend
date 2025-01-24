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
                         
                        
                        <form id="registerSchoolForm" action="{!! url('signupplayer') !!}" class="my-account-form" style="margin-top: -230px;background: #fff; border-radius: 35px 36px 0px 0px;" method="POST"> 
                        	{{csrf_field()}}
                            <h2 style="padding-bottom:20px; font-style: normal; text-align: center;"><span class="text-green">REGISTER </span> AS PLAYER</h2>
                            <p   style="padding-bottom:20px; font-style: normal; text-align: center;" > JOIN OUR TRAINING CLUB AND RISE TO NEW CHALLENGE </p>

                            <div class="main">
                            <div class="row">
                                <div class="col-md-6 float-left">

                                    <label for="username"> Name *</label>
                                    <input type="text" id="school_name" name="name" required minlength="3" maxlength="200"> 
                                </div>
                                <div class="col-md-6 float-left">
                                    <label for="username"> Email *</label>
                                    <input type="email" id="email" name="email" required minlength="3" maxlength="200">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 float-left">
                                    <label for="username"> Mobile *</label>
                                    <input type="text" id="mobile" name="mobile" required minlength="10" maxlength="10">
                                </div>
                                <div class="col-md-6 float-left">
                                    <label for="username"> Academy *</label>
                                    <select id="state_id" name="academy_id" class="academy_id" required >
                                        <option value=""> Select Academy </option>
                                        @if(!empty($academies) && count($academies)>0)
                                            @foreach($academies as $aca)
                                                <option value="{{$aca->id}}">{{$aca->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row">
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
                            <div class="form_block d-none" id="helpBlock">  </div>
                    		<input type="submit" name="submit" id="signupBtn" class="btn bg-green" value="Register" style="width:auto;"> 
 
                            <div class="row">
                                <div class="col-md-12 ">
                                    <p style="padding: 15px 0px;">Already have an account? <span style="text-decoration:underline;cursor: pointer;" class="text-green"  onclick="window.location.href='{{URL('/')}}/playerlogin'"  >Login</span></p> 
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


	$("#registerSchoolForm").submit(function(e) { 
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

                    $("#signupBtn").val('SUBMIT');
                }
            })
            .done(function(data) {
                if(data.status == 1){
                    //window.location.replace("{{URL('/')}}/home");
                    window.location.href = "{{URL::to('/otp/screen')}}";
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