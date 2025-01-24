@extends('layouts.teacher_master')
@section('attendance_settings', 'active')
@section('master_attendance', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Student Attendance', 'active'=>'active']];
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
                  <h4 style="font-size:20px;" class="card-title">Students Daily Attendance</h4>  
                  <br><br> 
                        <div class="row"> 
                            <div class="col-md-4" hidden>
                                <label style="padding-bottom: 10px;">Academic Year</label>
                                <input type="month" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="monthyear" id="monthyear" value="{{ date('Y-m') }}" minlength="4" maxlength="7">
                            </div>
                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">Date </label>
                                <div class="form-line">
                                    <input class="date_range_filter date form-control" name="date" type="text" id="datepicker_from"  />
                                </div>
                            </div>
                             <div class="col-md-3">
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

                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">Section </label>
                                <div class="form-line">
                                    <input type="hidden" name="sectionid" id="sectionid" value="{{$section_id}}">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>

                         
                            <div class="col-md-2">
                                {{-- <br> --}}
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:40px"
                                    onclick="loadStudentattendance()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                     
                          
                </div> 
                        
                       <div class="col-md-12" id="attendanceentries">
                            @include('teacher.loadstudentsdailyattendance')
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
            var val = $('#class_id').val();
           loadClassSection(val)
            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                // maxDate: '0',   
              });
            //   $("#datepicker_from").datepicker({maxDate: new Date()});
            $("#datepicker_from").datepicker('setDate', new Date())
            // checkSession();
            // checkanSession();
           
        });


        function putattendance(studentid, mode, day, obj) {
            var monthyear = $('#monthyear').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_dropdown').val();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('teacher/update/studentdailyattendance') }}",
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
            var date = $('#datepicker_from').val();
            var monthyear = $('#monthyear').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_dropdown').val();
            monthyear = $.trim(monthyear);
            class_id = $.trim(class_id);
            section_id = $.trim(section_id);
            date = $.trim(date);
           
            if(monthyear == '' || class_id == '' || section_id == '') {
                swal("Oops!", "Please select the Academic Year and Class and Section", "error");
                return false;
            }

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('teacher/load/studentdailyattendance') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    date : date,
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
                "url":"{{URL('/')}}/teacher/attendencereport/datatables/",   
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



function checkSession(){
  
    var class_id =  $('#tclass_id').val();
    var section_id =  $('#tsection_id').val();

     if($('#fn_chk').prop('checked')){
      var checked_val = this.value;
       $.ajax({
             url: "{{ url('teacher/fetch_attendance') }}",
             type: "POST",
             data: {
                 class_id:class_id,
                 section_id : section_id,
                 _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
            $.each(res.student, function(key, value) {
                $("input.fn_section[value='" + value.id + "']").prop('checked', true);
             });


             }
         });

 }else{
    var checked_val = this.value;
       $.ajax({
             url: "{{ url('teacher/fetch_attendance') }}",
             type: "POST",
             data: {
                 class_id:class_id,
                 section_id : section_id,
                 _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
            $.each(res.student, function(key, value) {
                $("input.fn_section[value='" + value.id + "']").prop('checked', false);
             });


             }
         });
 }
}

function checkanSession(){
  
  var class_id =  $('#tclass_id').val();
  var section_id =  $('#tsection_id').val();

   if($('#an_chk').prop('checked')){
    var checked_val = this.value;
     $.ajax({
           url: "{{ url('teacher/fetch_attendance') }}",
           type: "POST",
           data: {
               class_id:class_id,
               section_id : section_id,
               _token: '{{ csrf_token() }}'
           },
           dataType: 'json',
           success: function(res) {
          $.each(res.student, function(key, value) {
              $("input.an_section[value='" + value.id + "']").prop('checked', true);
           });


           }
       });

}else{
  var checked_val = this.value;
     $.ajax({
           url: "{{ url('teacher/fetch_attendance') }}",
           type: "POST",
           data: {
               class_id:class_id,
               section_id : section_id,
               _token: '{{ csrf_token() }}'
           },
           dataType: 'json',
           success: function(res) {
          $.each(res.student, function(key, value) {
              $("input.an_section[value='" + value.id + "']").prop('checked', false);
           });


           }
       });
}
}

function saveTimetable() {
        //$('#edit_style').on('click', function() {
             var options = {

                beforeSend: function(element) {

                    $("#edit_style").text('Processing..');

                    $("#edit_style").prop('disabled', true);

                },
                success: function(response) {

                    $("#edit_style").prop('disabled', false);

                    $("#edit_style").text('SUBMIT');

                    if (response.status == "SUCCESS") {

                        swal({title: "Success", text: response.message, type: "success"},
                            function(){
                                window.location.reload();
                            }
                        );

                    } else if (response.status == "FAILED") {

                        swal('Oops', response.message, 'warning');

                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {

                    $("#edit_style").prop('disabled', false);

                    $("#edit_style").text('SUBMIT');

                    swal('Oops', 'Something went to wrong.', 'error');

                }
            };
            $("#edit-style-form").ajaxForm(options);
        }

// $('.session_chk').click(function() {
//     alert("true")



// });


function loadClassSection(val) {
 var class_id = val;
$("#section_dropdown").html('');
var section_id = $("#sectionid").val();
$.ajax({
    url: "{{ url('teacher/fetch-section') }}",
    type: "POST",
    data: {
        class_id: class_id,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {

        $('#section_dropdown').html(
            '<option value="">-- Select Section --</option>');

        
        $.each(res.section, function(key, value) {
            var selected = '';
            if(section_id == value.id) {
                selected = ' selected ';
            }
            $("#section_dropdown").append('<option value="' + value.id + ' "'+ selected +'>' + value.section_name + '</option>');
        });
    }
});
}

    </script>
@endsection
