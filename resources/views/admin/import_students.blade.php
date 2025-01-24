@extends('layouts.admin_master')
@section('sch_settings', 'active')
@section('master_import_students', 'active')
@section('menuopensch', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Student', 'active' => 'active']];
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
                        <h4 style="font-size: 20px;" class="card-title">Import Students </h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px; display: none;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblclasses">
                                        <thead>
                                            <tr> 
                                                <th>Id</th>
                                                <th>Class Name</th> 
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th> 
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @if(!empty($classes))
                                                @foreach($classes as $class)
                                                <tr>
                                                    <td>{{$class->id}}</td>
                                                    <td>{{$class->class_name}}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px; display: none;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblsections">
                                        <thead>
                                            <tr> 
                                                <th>Id</th>
                                                <th>Class Name</th> 
                                                <th>Section Name</th> 
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th> 
                                                <th></th> 
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @if(!empty($sections))
                                                @foreach($sections as $section)
                                                <tr>
                                                    <td>{{$section->id}}</td>
                                                    <td>{{$section->class_name}}</td>
                                                    <td>{{$section->section_name}}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblstudents">
                                        <thead>          
                                            <tr> 
                                                <th>EMIS Id</th>
                                                <th class="red">Name</th> 
                                                <th>Name in Tamil</th> 
                                                <th class="red">Class</th>
                                                <th class="red">Section</th> 
                                                <th class="red">Father Name</th>  
                                                <th>Father Occupation</th>
                                                <th>Father Education</th> 
                                                <th>Mother Name</th>  
                                                <th>Mother Occupation</th>
                                                <th>Mother Education</th> 
                                                <th>Guardian Name</th>  
                                                <th>Guardian Occupation</th>
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
                                                <th>Religion</th>  
                                                <th>Medium of Instruction</th>  
                                                <th class="red">Admission Number</th>
                                                <th>Community</th> 
                                                <th>Disability Group Name</th>
                                                <th>GroupCode</th> 
                                                <th>Mother Tounge</th>  
                                                <th>Medium_1</th>
                                                <th>Medium_2</th> 
                                                <th>Medium_3</th>  
                                                <th>Medium_4</th>
                                                <th>Medium_5</th> 
                                                <th>Medium_6</th>  
                                                <th>Medium_7</th>
                                                <th>Medium_8</th> 
                                                <th>Medium_9</th> 
                                                <th>Medium_10</th>
                                                <th>Medium_11</th> 
                                                <th>Medium_12</th> 
                                            </tr>
                                        </thead>                                                                               
 
                                        <tbody> 
                                            <tr>
                                                <th>2023176086</th><th class="red">DHIVIN V </th><th>திவின் வீ</th>
                                                <th class="red">4th grade</th><th class="red">Diamond</th><th>VEERAN</th>
                                                <th>Daily wages</th><th>12th</th><th>VITHYA</th><th>Un-employed</th> 
                                                <th> </th><th></th><th></th><th>578755114121</th>
                                                <th class="red">9751169878</th>
                                                <th>Verified</th><th class="red">19-12-2014</th>
                                                <th class="red">Male</th><th>2020-09-08</th><th></th><th>3/54,MARIYAMMAN KOVIL STREET,SELLAMPATTU,KALLAKURICHI</th><th>606401</th><th>O+ve</th><th>Hindu</th>
                                                <th>Tamil</th><th class="red">6046</th><th>SC-Others</th>
                                                <th>Not Applicable</th><th></th>
                                                <th>Tamil</th><th></th><th></th><th></th><th></th><th></th><th></th>
                                                <th></th><th></th><th></th><th></th><th></th><th></th> 
                                                 
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <a href="{{config('constants.APP_IMAGE_URL')}}uploads/Sample_StudentsList.xlsx" ><button class=" btn btn-info" id="export_qb_btn">Export Sample Scholars List .xlsx file</button></a>

                            <form method="post" name="qb_import" id="qb_import" action="{{URL::to('/admin/import/scholarslist')}}">
                            {{csrf_field()}}   
                                <div class="row mt-5">
                                    <input type="file" name="importqb" id="importqb" required>
                                    <button class=" btn btn-info" id="import_qb_btn">Import Scholars List</button>
                                    ( Export file and make changes and Upload the file )
                                </div>
                            </form>

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
            var table1 = $('.tblclasses').DataTable();
            var table2 = $('.tblsections').DataTable();

            $('.tblclasses tfoot th').each(function(index) { 
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />'); 
            });
            $('.tblsections tfoot th').each(function(index) { 
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />'); 
            });


            // Apply the search
            table1.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });

            // Apply the search
            table2.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });


            $button = $('#import_qb_btn');
            $button.on('click', function () { 
                var options = {

                    beforeSend: function (element) {

                        $("#import_qb_btn").text('Processing..');

                        $("#import_qb_btn").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#import_qb_btn").prop('disabled', false);

                        $("#import_qb_btn").text('Import Scholars List');

                        if (response.status == "SUCCESS") {

                            swal({
                                   title: "Success", 
                                   text: "Scholars list Uploaded Successfully", 
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

                        $("#import_qb_btn").prop('disabled', false);

                        $("#import_qb_btn").text('Import Scholars list ');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#qb_import").ajaxForm(options);

                   
            }); 

        });    
    </script>

@endsection    