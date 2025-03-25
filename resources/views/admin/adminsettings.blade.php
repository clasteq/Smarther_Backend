@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_saadmin', 'active')
@section('menuopen', 'menu-is-opening menu-open')
<?php 
$user_type = Auth::User()->user_type; 
?>
@section('content') 
<meta name="csrf-token" content="{{ csrf_token() }}">
@if($user_type == "SUPER_ADMIN")
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel">

            <?php $login_image = ''; 
            if(!empty($settings)) {
                $login_image = $settings->login_image;  
                if(empty($login_image)) {
                    $login_image = asset('/public/image/adminlogin.jpeg');
                }   else {
                    $login_image = asset('/public/image/settings/'.$login_image);
                }
            }
            ?> 
            <div class="panel-body">
 
            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <!-- <div class="card-header">General Settings 
                    </div>-->

                    <div class="card-body">
                        <div class="row"><div class="col-md-12">
                            <form name="frm_terms" id="frm_terms" method="post" action="{{url('/admin/save/adminsettings')}}"> 
                                {{csrf_field()}}
                            <div class="row"> 
 
                                <div class="form-group col-md-6 float-left">
                                    <label>Login Image (870 px * 1024 px) <span class="manstar">*</span></label>
                                    <input type="file" name="login_image" id="login_image" class="form-control" required >
                                </div> 

                                <div class="form-group col-md-12 float-left">
                                    <img src="{{$login_image}}" height="300" width="300">
                                </div>   
                                
                            </div>
                            <div class="col-md-12 float-left">
                                <button type="submit" class="btn btn-success center-block" id="Submit">Save</button>
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
@else 
<section class="content">
    @include('admin.notavailable')
</section>
@endif
@endsection

@section('scripts') 
      <script>

        $(function() {

            $('#Submit').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('Save');

                        if (response.status == "SUCCESS") {

                           swal({
                                   title: "Success", 
                                   text: "Settings Info Saved Successfully", 
                                   type: "success"
                                 },
                               function(){ 
                                   location.reload();
                               }
                            );  

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('Save');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#frm_terms").ajaxForm(options);
            });       
        }); 

    </script>

@endsection

