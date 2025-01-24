@extends('layouts.admin_master')
@section('academic_settings', 'active')
@section('master_marksentry', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Marks Entry', 'active' => 'active']];
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
                  <h4 class="card-title" style="font-size: 20px;">Marks Entry    </h4> 
                    <br>
                        <div class="row"> 
                            
                            <div class="col-md-3">
                                <label style="padding-bottom: 10px;">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value);loadClassExams(this.value);">
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
                                <label class="form-label" style="padding-bottom: 10px;">Section <span class="manstar">*</span></label>
                                <div class="form-line"> <!-- loadClassSubjects(this.value); -->
                                    <select class="form-control" name="section_id" id="section_dropdown" required onchange="loadstudents(this.value,class_id.value)">

                                    </select>
                                </div>
                            </div>
                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">Students <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="student_id" id="student_id" >

                                    </select>
                                </div>
                            </div>
                            <div class=" col-md-3">
                                
                                <label class="form-label" style="padding-bottom: 10px;">Exams <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="exam_id" id="exam_id" required onchange="loadmonthyear();loadSubjects(this.value);">

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                
                                <label style="padding-bottom: 10px;">Academic Year</label>
                                <input type="month" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="monthyear" id="monthyear" value="{{ $monthyear }}" minlength="4" maxlength="4" onkeypress="return isNumber(event)" readonly>
                            </div> 

                            <div class=" col-md-3">
                               
                                <label class="form-label" style="padding-bottom: 10px;">Subjects</label>
                                <div class="form-line">
                                    <select class="form-control" name="subject_id" id="subject_id" >

                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3">
                               
                                <label class="form-label" style="padding-bottom: 10px;">Total Marks per Subject</label>
                                <div class="form-line">
                                    <input type="text" onkeypress="return isNumber(event)" class="form-control" name="total_marks" id="total_marks" required value="100"> 
                                </div>
                            </div>

                            <div class="col-md-2">
                              
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                    onclick="loadmarksentry()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                     
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <form id="frm-updatemarkentry" name="frm-updatemarkentry" method="post" action="{{url('/admin/update/all_marks_entry')}}">
                            <table class="table table-striped table-bordered tblcountries" id="attendanceentries">
                              @include('admin.loadmarksentry')
                            </table>
                            </form>
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

        function updateMarkEntry(student_id,subject) {
            var monthyear = $('#monthyear').val(); 
            monthyear = $.trim(monthyear); 

            if(monthyear == '') {
                swal("Oops!", "Please select the Academic Year", "error");
                return false;
            }
            var class_id = $('#class_id').val(); 
            var section_id = $('#section_dropdown').val(); 
            var exam_id = $('#exam_id').val(); 
            var subject_id = $('#subject_id').val(); console.log('#'+student_id+' #total_marks_'+subject)
            var total_marks = $('.'+student_id+' #total_marks_'+subject).val(); 
            var marks = $('.'+student_id+' #marks_'+subject).val(); 
            var remarks = $('.'+student_id+' #remarks_'+subject).val(); 
            //var grade = $('.'+student_id+' #grade_'+subject).val(); 


            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/update/marks_entry') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear,
                    class_id: class_id,
                    section_id: section_id,
                    exam_id: exam_id, 
                    subject_id: subject_id, 
                    total_marks: total_marks,
                    marks: marks,
                    remarks: remarks, 
                    //grade: grade, 
                    student_id:student_id,
                    subject : subject
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == 1) {
                     swal("Success!", response.message, "success");
                } else {
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadmarksentry() {
            var monthyear = $('#monthyear').val(); 
            monthyear = $.trim(monthyear); 
            var total_marks = $('#total_marks').val(); total_marks = $.trim(total_marks); 
            if(monthyear == '') {
                swal("Oops!", "Please select the Academic Year", "error");
                return false;
            }
            if(total_marks>0) {} else {
                swal("Oops!", "Please enter the Total Marks", "error");
                return false;
            }
            var class_id = $('#class_id').val(); 
            if(class_id>0) {} else {
                swal("Oops!", "Please select the Class", "error");
                return false;
            }

            var section_id = $('#section_dropdown').val(); 
            if(section_id>0) {} else {
                swal("Oops!", "Please select the Section", "error");
                return false;
            }
            var exam_id = $('#exam_id').val(); 
            if(exam_id>0) {} else {
                swal("Oops!", "Please select the Exam", "error");
                return false;
            }
            var student_id = $('#student_id').val(); 
            var subject_id = $('#subject_id').val(); 
            var total_marks = $('#total_marks').val(); 
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/marks_entry') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear, class_id:class_id, section_id:section_id, exam_id:exam_id, subject_id:subject_id,student_id:student_id,total_marks:total_marks
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == "SUCCESS") {
                    $('#attendanceentries').html(response.data);
                } else {
                    swal("Oops!", response.message, "error");
                    // $('#attendanceentries').html(response.message);
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
                "url":"{{URL('/')}}/admin/tattendencereport/excel/",   
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

        function loadstudents(section_id,class_id) { 
            $("#student_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-student') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    section_id: section_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#student_id').html(
                        '<option value="">-- Select Student --</option>');
                
                    $.each(res.student, function(key, value) {
                         $("#student_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }

        function loadSubjects(exam_id) { 
            $("#subject_id").html('');
            var class_id = $('#class_id').val();
            $.ajax({
                url: "{{ url('admin/fetch-exam-subjects') }}",
                type: "POST",
                data: {
                    exam_id: exam_id, 
                    class_id:class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#subject_id').html(
                        '<option value="">-- Select Subject --</option>');
                
                    $.each(res.subject, function(key, value) {
                         $("#subject_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }

        function updateStudentMarkEntry() {
            var monthyear = $('#monthyear').val(); 
            monthyear = $.trim(monthyear); 
            var error = 0;
            if(monthyear == '') {
                swal("Oops!", "Please select the Academic Year", "error");
                return false;  
            }
            var class_id = $('#class_id').val(); 
            var section_id = $('#section_dropdown').val(); 
            var exam_id = $('#exam_id').val(); 
            var subject_id = $('#subject_id').val(); 

            $( ".submit" ).each(function() {
                var subject = $( this ).data( "key" );
                var subjectname = $( this ).data( "name" );
                var studentname = $( this ).data( "student" );
                var student_id = $( this ).data( "student_id" );  
                var stotal_marks = $('.'+student_id+' #total_marks_'+student_id+'_'+subject).val(); 
                console.log('.'+student_id+' #total_marks_'+student_id+'_'+stotal_marks)
                var marks = $('.'+student_id+' #marks_'+student_id+'_'+subject).val(); 
                var remarks = $('.'+student_id+' #remarks_'+student_id+'_'+subject).val(); 
                //var grade = $('.'+student_id+' #grade_'+subject).val();  

                /*if($.trim(total_marks) == '') {
                    error = 1;
                    swal("Oops!", "Please enter the Total mark for "+subjectname+"--"+total_marks+"--"+studentname, "error");
                    return false; 
                }

                if($.trim(marks) == '') {
                    error = 1;
                    swal("Oops!", "Please enter the Marks for "+subjectname+" "+studentname, "error");
                    return false; 
                }*/

                if(marks > total_marks) {
                    error = 1;
                    swal("Oops!", "Marks for "+subjectname+" "+studentname+" must not be greater than Total marks", "error");
                    return false; 
                }

                if($('.'+student_id+' #is_absent_'+student_id+'_'+subject).prop('checked')) {
                    $('.'+student_id+' #marks_'+student_id+'_'+subject).val(''); 
                }
            });
            if(error == 0) {


                var entries = $('#frm-updatemarkentry').serialize();
                var request = $.ajax({
                    type: 'post',
                    url: " {{ URL::to('admin/update/all_marks_entry') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    /*data: {
                        monthyear: monthyear,
                        class_id: class_id,
                        section_id: section_id,
                        exam_id: exam_id, 
                        subject_id: subject_id, 
                        entries, 
                    },*/
                    data:entries+ "&monthyear="+monthyear+"&class_id="+class_id+"&section_id="+section_id+"&exam_id="+exam_id,
                    dataType: 'json',
                    encode: true
                });
                request.done(function(response) {
                    if (response.status == 1) {
                         swal("Success!", response.message, "success");
                    } else {
                        swal("Oops!", response.message, "error");
                    }

                });
                request.fail(function(jqXHR, textStatus) {

                    swal("Oops!", "Sorry,Could not process your request", "error");
                });
            
            }

            
        }

        function chkmark(student_id,subject) {
            var subject_id = $('#subject_id').val(); 
            var is_absent = $('.'+student_id+' #is_absent_'+student_id+'_'+subject).val(); 
            var marks = $('.'+student_id+' #marks_'+student_id+'_'+subject).val(); 

            if($('.'+student_id+' #is_absent_'+student_id+'_'+subject).prop('checked')) {
                $('.'+student_id+' #marks_'+student_id+'_'+subject).val(''); 
            }
        }
    </script>
@endsection
