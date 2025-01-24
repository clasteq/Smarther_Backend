@extends('layouts.teacher_master')
@section('profile', 'active')
@section('content') 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel">

             
            <div class="panel-body">
 
            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <h4 class="card-header">Profile
                    </h4>

                    <div class="card-body">
                        <div class="row"><div class="col-md-12">
                            <form name="frm_terms" id="frm_terms" method="post" action="{{url('/teacher/save/profile')}}"> 
                                {{csrf_field()}}
                            <div class="row">

                              <input type="hidden" value="{{$teachers->id}}" name="id" id="id">
 
                                <div class="form-group col-md-6 float-left">
                                    <label>First Name <span class="manstar">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{$teachers->name}}" required>
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Last Name <span class="manstar">*</span></label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{$teachers->last_name}}" required>
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Mobile Number<span class="manstar">*</span></label>
                                    <input type="text"minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)" name="mobile" id="mobile" class="form-control" value="{{$teachers->mobile}}" required>
                                </div> 

                                <div class="form-group float-left col-md-6">
                                    <label >Gender <span class="manstar">*</span></label>
                                    
                                        <select class="form-control" name="gender" id="gender" >
                                            <option value="MALE"  <?php if($teachers->gender == "MALE") { echo "selected"; } ?>>Male</option>
                                            <option value="FEMALE" <?php if($teachers->gender == "FEMALE") { echo "selected"; } ?> >Female</option>
                                        </select>
                                    
                                </div>

                                
                                <div class="form-group col-md-6 float-left">
                                    <label>Email<span class="manstar">*</span></label>
                                    <input type="text" name="email" id="email" class="form-control" value="{{$teachers->email}}" required>
                                </div> 
                                

                                <div class="form-group col-md-6 float-left">
                                    <label>Password<span class="manstar">*</span></label>
                                    <input type="text" name="password" minlength="6" maxlength="20" id="password" value="{{$teachers->passcode}}" class="form-control"  >
                                </div> 

                                <div class="form-group float-left col-md-6">
                                    <label>Date of Birth <span class="manstar">*</span></label>
                                    <div class="form-line">
                                        
                                        <input type="date" max="<?php echo date('Y-m-d'); ?>" class="form-control" value="{{$teachers->dob}}" name="dob" id="dob" >
                                    </div>
                                </div>
                                <div class="form-group col-md-6 float-left">
                                    <label>Father Name </label>
                                    <input type="text" name="father_name" id="father_name" class="form-control" value="{{$teachers->father_name}}" >
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Address</label>
                                    <input type="text" name="address" id="address" class="form-control" value="{{$teachers->address}}" >
                                </div> 
                                <div class="form-group float-left col-md-6">
                                    <label>Photo </label>
                                    <div class="form-line">
                                        <input type="file" class="form-control" id="profile_image" name="profile_image" >
                                    </div>
                                </div>
                                <div class="form-group col-md-6 float-left">
                                    <label>Post Details</label>
                                    <input type="text" name="post_details" id="post_details" class="form-control" value="{{$teachers->post_details}}" >
                                </div> 
                                <div class="form-group col-md-6 float-left">
                                    <label>Qualification </label>
                                    <input type="text" name="qualification" id="qualification" class="form-control" value="{{$teachers->qualification}}" >
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Experience</label>
                                    <input type="text" name="exp" id="exp" class="form-control" value="{{$teachers->exp}}" >
                                </div> 

                                <div class="form-group float-left col-md-6">
                                  
                                    <label>Country </label>
                                   <select class="form-control" name="country" id="country" onchange="myFunction(this.value)"
                                            >
                                            <option value="" disabled selected>--Select Country--</option>
                                            @foreach ($countries as $item)
                                                <option value="{{ $item->id }}" <?php if($teachers->country == $item->id) { echo "selected"; } ?>>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                   
                                </div>
                                <div class="form-group form-float float-left col-md-6">
                                    <input type="hidden" name="sta_id" id="sta_id" value="{{$teachers->state_id}}">
                                    <input type="hidden" name="city" id="city" value="{{$teachers->city_id}}">
                                    <label class="form-label">State </label>
                                    <div class="form-line">
                                        <div class="form-group form-float">
    
                                            <div class="form-line">
                                                <select id="state-dropdown" class="form-control" name="state_id"
                                                    onchange="stateFunction(this.value)" >
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6 ">
                                    <label class="form-label">City </label>
                                    <div class="form-line">
                                        <select id="districts-dropdown" class="form-control" name="city_id" >
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group float-left col-md-6">
                                    <div class="form-line">
                                        <img src="{{$teachers->is_profile_image}}" id="img_profile_image" height="100" width="100">
                                    </div>
                                </div>
                                 </div>
                            <div class="col-md-12 float-left">
                                <button type="submit" class="btn btn-success center-block" id="Submit">Submit</button>
                            </div>
                            </form>
                        </div></div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
</div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
      <script>

        $(function() {

                var val = $('#country').val();
                var selectedid =$('#sta_id').val();
               myFunction(val, selectedid);
               var val =$('#sta_id').val();
               var selectedid = $('#city').val();
               stateFunction(val,selectedid)

            $('#Submit').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                           swal('Success','Profile Saved Successfully','success');

                           window.location.reload();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#frm_terms").ajaxForm(options);
            });       
        });

        function isDecimal(evt, obj) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)  && (charCode != 46 || $(obj).val().indexOf('.') != -1)) {
                return false;
            }
            return true;
        }


        function myFunction(val, selectedid) {

selectedid = selectedid || " ";

var idCountry = val;
var selid = selectedid;

$("#state-dropdown").html('');
$.ajax({
    url: "{{ url('admin/fetch-states') }}",
    type: "POST",
    data: {
        country_id: idCountry,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {

        $('#state-dropdown').html(
                '<option value="">-- Select State --</option>');
       
        $.each(res.states, function(key, value) {
            var selected = '';
                        if(selid != '' && selid == value
                            .id) {
                            selected = ' selected ';
                        }
            $("#state-dropdown").append('<option value="' + value
                .id + '"'+selected+'>' + value.state_name + '</option>');
        });
    }
});
}

function stateFunction(val, selectedid) {

selectedid = selectedid || " ";

var idState = val;

var selid = selectedid;


$("#districts-dropdown").html('');
$.ajax({
    url: "{{ url('admin/fetch-districts') }}",
    type: "POST",
    data: {
        state_id: idState,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {

                $("#districts-dropdown").html('<option value="">-- Select City --</option>');
           
            $.each(res.districts, function(key, value) {
                var selected = '';
                        if(selid != '' && selid == value
                            .id) {
                            selected = ' selected ';
                        }
                $("#districts-dropdown").append('<option value="' + value
                    .id + '"'+selected+'>' + value.district_name + '</option>');
            });
    }
});
}

    </script>

@endsection

