@extends('layouts.admin_master')
@section('stasettings', 'active')
@section('master_import_teachers', 'active')
@section('menuopensta', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Import Staff', 'active' => 'active']];
?>
@section('content')

    <style type="text/css">
        .red {
            color: red;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title">Import Staffs </h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard"> 

                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblstudents">
                                        <thead>          
                                            <tr>  
                                                <th class="red">User Role</th> 
                                                <th class="red">Name</th> 
                                                <th>Name in Tamil</th>  
                                                <th>Aadhaar Number</th> 
                                                <th class="red">Phone Number</th>  
                                                <th>Verify Status</th>
                                                <th class="red">Data of Birth</th> 
                                                <th class="red">Gender</th>  
                                                <th>Data of joining</th>
                                                <th>Email</th> 
                                                <th>Address</th>  
                                                <th>Pin code</th>
                                                <th>Blood Group</th>  
                                                <th class="red">Employee Number</th>
                                                <th class="red">Department</th> 
                                                <th class="red">Designation</th> 
                                            </tr>
                                        </thead>                                                                               
 
                                        <tbody> 
                                            <tr>
                                                <th class="red">TEACHER</th><th class="red">MALA M</th><th>மாலா எம்</th><th>578755114121</th>
                                                <th class="red">9751169878</th><th>Verified</th><th class="red">19-12-1990</th>
                                                <th class="red">Male</th><th>2020-09-08</th><th>mala@vidhyamhss.com</th>
                                                <th>3/54,MARIYAMMAN KOVIL STREET,Salem</th><th>606401</th><th>O+ve</th>
                                                <th class="red">6046</th><th class="red">Tamil</th>
                                                <th class="red">Teacher</th> 
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <a href="{{config('constants.APP_IMAGE_URL')}}uploads/Sample_StaffList.xlsx" ><button class=" btn btn-info" id="export_qb_btn">Export Sample Staff List .xlsx file</button></a>

                            <form method="post" name="qb_import" id="qb_import" action="{{URL::to('/admin/import/staffslist')}}">
                            {{csrf_field()}}   
                                <div class="row mt-5">
                                    <input type="file" name="importqb" id="importqb" required>
                                    <button class=" btn btn-info" id="import_qb_btn">Import Staff List</button>
                                    ( Export file and make changes and Upload the file )
                                </div>
                            </form>

                            <div id="postmsg" class="mt-3"></div>
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
  

            $button = $('#import_qb_btn');
            $button.on('click', function () { 
                var options = {

                    beforeSend: function (element) {

                        $("#import_qb_btn").text('Processing..');

                        $("#import_qb_btn").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#import_qb_btn").prop('disabled', false);

                        $("#import_qb_btn").text('Import Question Bank');  

                        if (response.status == "SUCCESS") {

                            swal({
                                   title: "Success", 
                                   text: "Staffs list Uploaded Successfully", 
                                   type: "success"
                                 },
                               function(){ 
                                   location.reload();
                               }
                            );  

                        }  else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }  else {
                            swal('Info',response,'info');
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#import_qb_btn").prop('disabled', false);

                        $("#import_qb_btn").text('Import Scholars list '); console.log(jqXHR)

                        swal('Oops','Something went to wrong.','error');

                        $('#postmsg').html(jqXHR.responseText);

                    }
                };
                $("#qb_import").ajaxForm(options);

                   
            }); 

        });    
    </script>

@endsection    