@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_exams', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Edit Exams', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Edit Exam 
                        </h4>

                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <input type="hidden" name="id" id="id" value="{{$exam['id']}}">
                                <div class="row"> 
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Exam Name</label>
                                        <div class="form-line">
                                            <input type="text" value="{{$exam['exam_name']}}" class="form-control" id="exam_name" name="exam_name" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Month Year</label>
                                        <div class="form-line">
                                            <input type="month"  value="{{$exam['monthyear']}}" class="form-control" id="monthyear" name="monthyear" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Start Date</label>
                                        <div class="form-line">
                                            <input class="date_range_filter date form-control exam_startdate" name="exam_startdate"  value="{{$exam['exam_startdate']}}"  type="text" id="start_date"   value="" required/> 
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">End Date</label>
                                        <div class="form-line">
                                            <input class="date_range_filter date exam_enddate form-control" name="exam_enddate"  value="{{$exam['exam_enddate']}}"  type="text" id="end_date"   value="" required/> 
                                        </div>
                                    </div> 
                                    {{-- <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Class</label>
                                        <div class="form-line">

                                            <select class="form-control course_id" name="class_id" id="class_id"
                                               required>
                                                <option value="">Select Class</option>
                                                @if (!empty($class))
                                                    @foreach ($class as $course)
                                                        <option value="{{ $course->id }}">{{ $course->class_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </div>
                                    </div> --}}
                               
                                    <div class="form-group form-float float-left col-md-2">
                                        <button type="submit" class="btn signupBtn"
                                            style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                            onclick="loadexamtable()">Submit </button>
                                    </div>
                                    <br>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="timetableentries">
                                        @include('admin.loadexamlist')
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
        $(function() {
            $("#start_date").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                // maxDate: '0',   
              });
              $("#end_date").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                // maxDate: '0',   
              });
            //   $("#datepicker_from").datepicker({maxDate: new Date()});
            // $("#start_date").datepicker('setDate', new Date())
            // checkSession();
            // checkanSession();
        });

        $(document).ready(function(){
            loadexamtable();
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

        function loadexamtable() {
            var start_date = $('#start_date').val();
            var monthyear = $('#monthyear').val();
            var end_date = $('#end_date').val();
            var exam_name = $('#exam_name').val();
            var id = $('#id').val();
            // if(start_date == '') {}
            // else {
            //     swal("Oops!", "Please Enter the Start Date", "error");
            //     return false;
            // }

            // var section_id = $('#section_dropdown').val();
            // if(end_date == '') {}
            // else {
            //     swal("Oops!", "Please Enter the End Date", "error");
            //     return false;
            // }

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/exams') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    start_date: start_date, end_date:end_date,monthyear : monthyear,exam_name : exam_name, id:id
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

      
        function saveExams() {
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
        //});


      
    </script>

@endsection
