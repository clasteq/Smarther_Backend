@extends('layouts.admin_master')
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
                  <h4 class="card-title">Students Attendance  
                        <div class="row"> 
                            <div class="col-md-4">
                                <label style="padding-bottom: 10px;">Academic Year</label>
                                <input type="year" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="monthyear" id="monthyear" value="{{ $monthyear }}" minlength="4" maxlength="4" onkeypress="return isNumber(event)">
                            </div>
                            <div class="col-md-4">
                                <label style="padding-bottom: 10px;">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                @if ($class_id == $class->id) selected @endif>
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
                                    onclick="loadStudentattendance()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                  </h4>        
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries" id="attendanceentries">
                              @include('admin.loadstudentsattendance')
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
        $(function() {});

        function putattendance(studentid, mode, day, obj) {
            var monthyear = $('#monthyear').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_dropdown').val();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/update/studentattendance') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear,
                    student_id: studentid,
                    mode: mode,
                    day: day,
                    class_id: class_id,
                    section_id: section_id
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

        function loadStudentattendance() {
            var monthyear = $('#monthyear').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_dropdown').val();
            monthyear = $.trim(monthyear);
            class_id = $.trim(class_id);
            section_id = $.trim(section_id);

            if(monthyear == '' || class_id == '' || section_id == '') {
                swal("Oops!", "Please select the Academic Year and Class and Section", "error");
                return false;
            }

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/studentattendance') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear,
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
