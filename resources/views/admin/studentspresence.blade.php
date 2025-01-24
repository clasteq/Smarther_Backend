@extends('layouts.admin_master')
@section('report_settings', 'active')
@section('master_studentspresence', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Student Attendance', 'active'=>'active']];
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
    <link rel="stylesheet" href="{{asset('/public/css/bootstrap-datepicker.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Students Attendance</h4>  
                  <br><br> 
                        <div class="row"> 
                            <div class="col-md-4">
                                <label style="padding-bottom: 10px;">Date</label>
                                <input type="text" name="daterange" id="dates"  class="form-control"/>
                                 
                            </div>
                            <div class="col-md-4">
                                <label style="padding-bottom: 10px;">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" >
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class=" col-md-4">
                                <label class="form-label" style="padding-bottom: 10px;">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                    onclick="loadStudentspresence()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                     
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries" id="attendanceentries">
                              @include('admin.loadstudentspresence')
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

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript">
        $(function() {});

        $('#dates').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY'
            },
            ranges: {
                'Today': [moment().toDate(), moment().toDate()],
                'Yesterday': [
                    moment().subtract(1, 'days').toDate(),
                    moment().subtract(1, 'days').toDate()
                ],
                'Last 7 Days': [
                    moment().subtract(6, 'days').toDate(),
                    moment().toDate()
                ],
                'Last 30 Days': [
                    moment().subtract(29, 'days').toDate(),
                    moment().toDate()
                ],
                'This Month': [
                    moment().startOf('month').toDate(),
                    moment().endOf('month').toDate()
                ],
                'Last Month': [
                    moment().subtract(1, 'month').startOf('month').toDate(),
                    moment().subtract(1, 'month').endOf('month').toDate()
                ]
            }
        }); 
 
        function loadStudentspresence() {
            var dates = $('#dates').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_dropdown').val();
            dates = $.trim(dates);
            class_id = $.trim(class_id);
            section_id = $.trim(section_id);

            if(dates == '' || class_id == '' || section_id == '') {
                swal("Oops!", "Please select the Date range and Class and Section", "error");
                return false;
            }

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/studentspresence') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    dates: dates,
                    class_id: class_id,
                    section_id: section_id
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
                "url": "{{ route('attendence.excel') }}",
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
