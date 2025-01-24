@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_admin', 'active')
@section('menuopen', 'menu-is-opening menu-open')
@section('content') 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel">

            <?php $acadamic_year = $display_academic_year = $helpcontact = $academic_start_date = $academic_end_date = $admin_email = $contact_address ='';
             $facebook_link = $twitter_link = $instagram_link = $skype_link = $youtube_link = '';
            if(!empty($settings)) {
                $acadamic_year = $settings->acadamic_year;
                $display_academic_year = $settings->display_academic_year;
                $helpcontact = $settings->helpcontact;
                $academic_start_date = $settings->academic_start_date;
                $academic_end_date = $settings->academic_end_date;
                $admin_email = $settings->admin_email;
                $contact_address = $settings->contact_address;
                $facebook_link = $settings->facebook_link;
                $twitter_link = $settings->twitter_link;
                $instagram_link = $settings->instagram_link;
                $skype_link = $settings->skype_link;
                $youtube_link = $settings->youtube_link;
                
            }
            ?> 
            <div class="panel-body">
 
            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <div class="card-header">General Settings
                    </div>

                    <div class="card-body">
                        <div class="row"><div class="col-md-12">
                            <form name="frm_terms" id="frm_terms" method="post" action="{{url('/admin/save/settings')}}"> 
                                {{csrf_field()}}
                            <div class="row"> 
 
                                <div class="form-group col-md-6 float-left">
                                    <label>Academic Year <span class="manstar">*</span></label>
                                    <input type="number" name="acadamic_year" id="acadamic_year" class="form-control" value="{{$acadamic_year}}" required  min="2020" max="2099" step="1" value="{{date('y')}}" minlength="4" maxlength="4">
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Display Academic Year <span class="manstar">*</span></label>
                                    <input type="text" name="display_academic_year" id="display_academic_year" class="form-control" value="{{$display_academic_year}}" required minlength="4" maxlength="15">
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Help Contact <span class="manstar">*</span></label>
                                    <input type="text" name="helpcontact" id="helpcontact" class="form-control" value="{{$helpcontact}}" required>
                                </div> 

                                <div class="form-group col-md-4 float-left">
                                    <label>Academic Start Date <span class="manstar">*</span></label>
                                    <input type="date" name="academic_start_date" id="academic_start_date" class="form-control" value="{{$academic_start_date}}" required>
                                </div>

                                <div class="form-group col-md-4 float-left">
                                    <label>Academic End Date <span class="manstar">*</span></label>
                                    <input type="date" name="academic_end_date" id="academic_end_date" class="form-control" value="{{$academic_end_date}}" required>
                                </div>

                                <div class="form-group col-md-4 float-left">
                                    <label>Update Holidays<span class="manstar">*</span></label>
                                    <input type="checkbox" name="update_holidays" id="update_holidays" class="form-control" value="1">
                                </div>

                                <div class="form-group col-md-6 float-left"> 
                                    <label>Admin Email <span class="manstar">*</span></label>
                                    <input type="text" name="admin_email" id="admin_email" class="form-control" value="{{$admin_email}}" required>
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Contact Address <span class="manstar">*</span></label>
                                    <textarea name="contact_address" id="contact_address" class="form-control" required>{{$contact_address}}</textarea>
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Facebook Link </label>
                                    <input type="text" name="facebook_link" id="facebook_link" class="form-control" value="{{$facebook_link}}"  >
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Twitter Link </label>
                                    <input type="text" name="twitter_link" id="twitter_link" class="form-control" value="{{$twitter_link}}"  >
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Instagram Link </label>
                                    <input type="text" name="instagram_link" id="instagram_link" class="form-control" value="{{$instagram_link}}"  >
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>Skype Link </label>
                                    <input type="text" name="skype_link" id="skype_link" class="form-control" value="{{$skype_link}}" >
                                </div> 

                                <div class="form-group col-md-6 float-left">
                                    <label>YouTube Link </label>
                                    <input type="text" name="youtube_link" id="youtube_link" class="form-control" value="{{$youtube_link}}"  >
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

    </script>

@endsection

