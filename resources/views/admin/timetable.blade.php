@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_timetable', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Timetable', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Timetable
                        </h4>

                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 80%; padding-left: -10px;">
                                <div class="row">
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Class</label>
                                        <div class="form-line">

                                            <select class="form-control course_id" name="class_id" id="class_id"
                                                onchange="myFunction(this.value)" required>
                                                <option value="">Select Class</option>
                                                @if (!empty($class))
                                                    @foreach ($class as $course)
                                                        <option value="{{ $course->id }}">{{ $course->class_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Section</label>
                                        <div class="form-line">
                                            <select class="form-control" name="section_id" id="section_dropdown">

                                            </select>
                                        </div>
                                    </div>
                                    <div  class="form-group form-float float-left col-md-3">
                                        <button type="submit" class="btn signupBtn"
                                            style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                            onclick="loadTimetable()">Submit </button>
                                    </div>
                                    <br>
                                    <div class="row">
                                    <div class="col-md-12" id="timetableentries">
                                        @include('admin.loadtimetable')
                                    </div>
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
        function myFunction(val) {


            var class_id = val;

            $('#timetableentries').html('');
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

        function loadTimetable() {
            $('#timetableentries').html('');
            var class_id = $('#class_id').val();
            if(class_id >0) {}
            else {
                swal("Oops!", "Please select the Class", "error");
                return false;
            }

            var section_id = $('#section_dropdown').val();
            if(section_id >0) {}
            else {
                swal("Oops!", "Please select the Section", "error");
                return false;
            }

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/timetable') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    class_id: class_id, section_id:section_id
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
        //});
    </script>

@endsection
