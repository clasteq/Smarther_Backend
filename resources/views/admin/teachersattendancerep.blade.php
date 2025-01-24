@extends('layouts.admin_master')
@section('staattendance_settings', 'active')
@section('master_teachersatten', 'active')
@section('menuopenstaatt', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers Attendance', 'active' => 'active']];
?>

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>
        .form-control:focus {
            color: #495057;
            background-color: #fff !important;
            border: none;
            outline: 0;
            box-shadow: 0 0 0 0.2rem #dee2e6 !important;
        }

        .greentick {
            color: #A3D10C;
        }

        .redcross {
            color: #dc3545;
        }

        .greentickbox {
            color: #fff;
            background: #007bff;
            font-size: 10px;
            padding: 4px;
            cursor: pointer;
        }

        .redcrossbox {
            color: #fff;
            background: #dc3545;
            font-size: 13px;
            padding: 4px;
            margin-top: 5px;
            cursor: pointer;
        }

        .greentickboxharizondal {
            color: #fff;
            background: #007bff;
            font-size: 10px;
            padding: 5px 4px 4px 4px;
        }

        .redcrossboxharizondal {
            color: #fff;
            background: #dc3545;
            font-size: 12px;
            padding: 4px;
            margin-top: 0px;
        }

        .rowcen {
            padding-left: 6px;
            margin-top: 7px;
        }

        @media only screen and (max-width: 600px) {
            .my-account-form {
                overflow-x: scroll !important;
            }

        }
    </style>
@endsection
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title" style="font-size: 20px;">Teachers Attendance  </h4> 
                  
                        <div class="row"> 
                           
                            <div class="col-md-4">
                                <br>
                                <label style="padding-bottom: 10px;">Academic Year</label>
                                <input type="month" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="monthyear" id="monthyear" value="{{ $monthyear }}" minlength="4" maxlength="4" onkeypress="return isNumber(event)">
                            </div> 
                            <div class="col-md-2" style="padding-top: 10px;">
                               <br>
                                <button type="submit"  class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px;"
                                    onclick="loadTeacherattendance()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                        
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries" id="attendanceentries">
                              {{-- @include('admin.loadteachersattendance') --}}
                            </table>
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
    <script type="text/javascript">
        $(function() {
            loadTeacherattendance();
        });

        function putattendance(teacherid, mode, day,session, obj) {
            var monthyear = $('#monthyear').val(); 
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/update/teacherattendancerep') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear,
                    teacherid: teacherid,
                    mode: mode,
                    day: day, 
                    session:session
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == 1) {
                    if (mode == 1) {
                        $(obj).parent('td').html('<i class="fa fa-check greentick" aria-hidden="true"></i>');
                    } else {
                        $(obj).parent('td').html('<i class="fa fa-times redcross" aria-hidden="true"></i>');
                    }
                } else {
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadTeacherattendance() {
            var monthyear = $('#monthyear').val(); 
            monthyear = $.trim(monthyear); 

            if(monthyear == '') {
                swal("Oops!", "Please select the Academic Year", "error");
                return false;
            }

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/teacherattendancerep') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear, 
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == "SUCCESS") {
                    $('#attendanceentries').html(response.data);
                } else {
                    $('#attendanceentries').html(response.message);
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function excelFunction(slot) {

            var monthyear = $('#monthyear').val();
            var slot = $('#slot').val(); 

            $.ajax({
                "url":"{{URL('/')}}/admin/teacherattendence/excel/",    
                type: 'get',
                data: {
                    monthyear: monthyear,
                    slot: slot
                },
                "success": function(res, status, xhr) {
                    var csvData = new Blob([res], {
                        type: 'application/xls;charset=utf-8;'
                    });
                    var csvURL = window.URL.createObjectURL(csvData);
                    var tempLink = document.createElement('a');
                    tempLink.href = csvURL;
                    tempLink.setAttribute('download', 'Students Attendence.xls');
                    tempLink.click();
                }
            });
        }
    </script>
@endsection
