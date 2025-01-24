@extends('layouts.admin_master')
@section('academic_settings', 'active')
@section('master_examination_settings', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Exam Settings', 'active' => 'active']];

$id = $examination_id = 0; $class_ids = $section_ids = $exam_startdate = $exam_enddate = '';
$rank_on_off = $grade_on_off = $result_in = $rank_settings = $grade_settings = 0;
$rank_type = $rankincludefailures = '';
if(!empty($exams_details)) {

    $id = $exams_details->id; 
    $examination_id = $exams_details->examination_id; 
    $class_ids = $exams_details->class_ids; 
    $section_ids = $exams_details->section_ids; 
    $exam_startdate = $exams_details->exam_startdate; 
    $exam_enddate = $exams_details->exam_enddate; 
    
    $rank_on_off = $exams_details->rank_on_off; 
    $grade_on_off = $exams_details->grade_on_off; 
    $result_in = $exams_details->result_in; 
    $rank_settings = $exams_details->rank_settings; 
    $grade_settings = $exams_details->grade_settings; 
    $rank_type = $exams_details->rank_type; 
    $rankincludefailures = $exams_details->rankincludefailures;  
}
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
        .textwidth {
                width: 100px;
        }
    </style>
    <section class="content">
        <!-- Exportable Table -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Exam Settings
                        </h4>

                        <input type="hidden" name="school_code" id="school_code" value="{{$school_id}}">
                        <input type="hidden" name="loadclasses" id="loadclasses" value="{!! URL('admin/fetch-term-classes') !!}">
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;"> 
                                <div class="row"> 
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Exam Name</label>
                                        <input type="hidden" name="exam_id" id="exam_id" value="{{$id}}">
                                        <div class="form-line">
                                            <select class="form-control" name="examination_id" id="examination_id" required onchange="loadClassesByTerm();"> 
                                                <option value=""> Select Exam </option>
                                                @if(!empty($exams))
                                                @foreach($exams as $exam)
                                                @php($selected = '')
                                                @if($examination_id == $exam->id) @php($selected = 'selected') @endif
                                                <option value="{{ $exam->id }}" data-id="{{ $exam->term_id }}" {{$selected}}>{{ $exam->exam_name }}</option>  
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group form-float float-left col-md-2">
                                        <label class="form-label">Start Date</label>
                                        <div class="form-line">
                                            <input class=" form-control " name="exam_startdate"  type="date" id="start_date" required value="{{$exam_startdate}}" min="{{date('Y-m-d')}}" /> 
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-2">
                                        <label class="form-label">End Date</label>
                                        <div class="form-line">
                                            <input class=" form-control" name="exam_enddate"    type="date" id="end_date"  required value="{{$exam_enddate}}" min="{{date('Y-m-d')}}" /> 
                                        </div>
                                    </div> 
                                    <div class="form-group form-float float-left col-md-2">
                                        <label class="form-label">Class</label>
                                        <div class="form-line">

                                            <select class="form-control course_id class_id" name="class_id" id="class_id" required 
                                        onchange="loadClassSectionExam(this.value);">
                                                <option value="">Select Class</option>
                                                @if (!empty($classes))
                                                    @foreach ($classes as $course)
                                                        @php($selected = '')
                                                        @if($class_ids == $course->id) @php($selected = 'selected') @endif
                                                        <option value="{{ $course->id }}" {{$selected}}>{{ $course->class_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </div>
                                    </div>

                                    <div class=" col-md-2">
                                        <label class="form-label">Section </label>
                                        <div class="form-line"> <!-- loadClassSubjects(this.value); -->
                                            <select class="form-control" name="section_id" id="section_dropdown" required>

                                            </select>
                                        </div>
                                    </div>
                               
                                    <div class="form-group form-float float-left col-md-2">
                                        <button type="submit" class="btn signupBtn"
                                            style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                            onclick="loadexaminationtable()">Submit </button>
                                    </div>
                                    <br>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="timetableentries">
                                        
                                    </div>
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

        function loadClassesByTerm() {
            var term_id = $('#examination_id option:selected').data('id'); 
            loadClasses(term_id);
        }

        $(function() { 
        });
 
        function myFunction(val) {


            var class_id = val;


            $("#section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
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
                        $("#section_dropdown").append('<option  value="' +
                            value.id + '">' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadexaminationtable() {
            var exam_id = $('#exam_id').val();
            var examination_id = $('#examination_id').val();
            if(examination_id > 0) {} else {
                swal("Oops!", "Please select Exam", "error");
                return false;
            }
            var start_date = $('#start_date').val(); 
            var end_date = $('#end_date').val();
            if(start_date != '') {}
            else {
                swal("Oops!", "Please Enter the Start Date", "error");
                return false;
            }if(end_date != '') {}
            else {
                swal("Oops!", "Please Enter the End Date", "error");
                return false;
            }

            var class_id   = $('#class_id').val(); 
            if(class_id > 0) {} else {
                swal("Oops!", "Please select Class", "error");
                return false;
            } 

            var section_id = $('#section_dropdown').val();
            

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/examination') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    exam_id:exam_id, examination_id : examination_id, start_date: start_date, end_date:end_date, class_id : class_id, section_id : section_id
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == "SUCCESS") {
                    $('#timetableentries').html(response.data);
                } else {
                    // $('#timetableentries').html(response.message);
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

      
        function saveExaminations() {
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
                                var exam_id = $('#exam_id').val();
                                if(exam_id > 0) {
                                    //window.location.reload();
                                    window.location.href = "{{URL('/')}}/admin/examination_settings";
                                } else {
                                    window.location.href = "{{URL('/')}}/admin/examination_settings";
                                }
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

        function loadClassSectionExam(val, selectedid, selectedval) { 

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                async:true,
                success: function(res) {

                    $('#section_dropdown,#edit_section_dropdown').html(
                        '<option value="0">-- All Section --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');

                    });
                        @if($id > 0)   
                        loadexaminationtable();
                        @endif
                }
            });
        }

        @if($id > 0)   
        loadClassSectionExam({{$class_ids}}, {{$section_ids}});  
        @endif
      

        function loadprac() {
            var include_practicals = $("#include_practicals").val();
            if(include_practicals == 'YES') {
                $('.is_prac').removeClass('d-none');
            }   else {
                $('.is_prac').addClass('d-none');
            }
        }
    </script>

@endsection
