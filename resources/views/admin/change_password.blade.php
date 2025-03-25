@extends('layouts.admin_master') 
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
                    <div class="card-header">Change Password
                    </div>

                    <div class="card-body">
                        <div class="row"><div class="col-md-12">
                            <form id="PasswordForm" name="PasswordForm" action="{{url('/admin/change_password')}}" 
                              method="post">
                                {{csrf_field()}}
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>New Password <span class="manstar">*</span></label>
                                    <input type="password" class="form-control" name="new_password" id="new_password"  placeholder="Enter New Password" autocomplete="off" required minlength="4" maxlength="50" />
                                </div>
                                
                            </div> 
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Confirm Password <span class="manstar">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password"  placeholder="Enter Confirm Password" autocomplete="off" required minlength="4" maxlength="50"/>
                                </div>
                                
                            </div> 
                            <button type="submit" class="btn btn-success center-block" id="Submit">SUBMIT</button> 
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
                            $("#PasswordForm")[0].reset();
                           swal({
                                title: "SUCCESS",
                                text: response.message,
                                type: "success"
                            }, function() {
                                window.location.reload();
                            }); 

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
                $("#PasswordForm").ajaxForm(options);
            });   
        });

    </script>
 

@endsection

