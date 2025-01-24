@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_class_teachers', 'active')
@section('menuopenu', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers', 'active' => 'active']];
?>
@section('content')



    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px !important;" class="card-title">Class Teachers
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Action</th>
                                                <th>Teacher Name</th>
                                                <th>Class Teacher</th>
                                                <th>Section</th>
                                                <th>Subject</th>
                                                <th>Status</th>

                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>

                                            </tr>
                                        </tfoot>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Teachers</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/class_teachers') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Teacher  <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control course_id" id="class_tutor" name="class_tutor"
                                        onchange="loadClassSection(this.value)" required>
                                        <option value="">Select Teacher</option>
                                        @if (!empty($teacher))
                                            @foreach ($teacher as $course)
                                                <option value="{{ $course->id }}">{{ $course->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control course_id" id="class_tutor" name="class_tutor"
                                        onchange="loadClassSection(this.value)" required>
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" onchange="loadsubjects(this.value)" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="subject_dropdown" required>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-right col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Teachers</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/teachers') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control " multiple="multiple" name="class_id[]" required
                                        id="edit_class">
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))

                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="edit_section_dropdown">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="subject_id" id="subject_id" id="edit_subject_dropdown" required>

                                    </select>
                                </div>
                            </div>

                            <br>
                            <div class="form-group form-float float-right col-md-4">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <img src="" id="img_profile_image" height="100" width="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="sumbit" class="btn btn-link waves-effect" id="edit_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{ route('teachers.data') }}',
                    data: function ( d ) {
                        var subject  = $('#sub_id').val();
                        $.extend(d, {subject:subject});

                    }
                },
                columns: [{
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadTeacher(' + tid +
                                ')" title="Edit Country"><i class="fas fa-edit"></i></a>';
                        },

                    },
                    {
                        data: 'name',
                        name: 'users.name'
                    },
                    {
                        data: 'last_name',
                        name: 'users.last_name'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            if (data.profile_image != '' || data.profile_image !=
                                null) {
                                var tid = data.is_profile_image;
                                return '<img src="' + tid + '" height="50" width="50">';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'gender',
                        name: 'users.gender'
                    },
                    {
                        data: 'email',
                        name: 'users.email'
                    },

                ],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]


            });


            $('.tblcountries tfoot th').each(function(index) {
                // if (index != 19 && index != 0) {
                //     var title = $(this).text();
                //     $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                // }
                 if (index != 0 && index != 3 && index != 13) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });

            $('#sub_id').on('change', function() {
                table.draw();
            });

            // Apply the search
            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });
            $('#edit_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

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
            });
        });

        function loadTeacher(id) {

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/class_teachers') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {


                $('#id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_last_name').val(response.data.last_name);
                $('#edit_gender').val(response.data.gender);
                $('#edit_email').val(response.data.email);
                $('#edit_dob').val(response.data.dob);
                $('#edit_mobile').val(response.data.mobile);
                $('#edit_emp_no').val(response.data.emp_no);
                $('#edit_date_of_joining').val(response.data.date_of_joining);
                $('#edit_qualification').val(response.data.qualification);
                $('#edit_exp').val(response.data.exp);
                $('#edit_post_details').val(response.data.post_details);
                $('#edit_subject').val(response.data.teachers.is_subject_id);

                $('#edit_class').val(response.data.teachers.is_class_id);
                $('#edit_class_tutor').val(response.data.class_tutor);
                $('#edit_father_name').val(response.data.father_name);
                $('#edit_address').val(response.data.address);
                var val = response.data.class_tutor;
                var selectedid = response.data.section_id;
                loadClassSection(val, selectedid);

                $('#edit_country-dropdown').val(response.data.country);

                var val = response.data.country;
                var selectedid = response.data.state_id;
                var selectedval = response.data.is_state_name;
                myFunction(val, selectedid, selectedval);

                $('#edit_state_dropdown').val(response.data.state_id);
                var val = response.data.state_id;
                var selectedid = response.data.city_id;
                var selectedval = response.data.is_district_name;
                stateFunction(val, selectedid, selectedval);

                $('#edit_districts-dropdown').val(response.data.city_id);
                $('#edit_status').val(response.data.status);
                $('#img_profile_image').attr('src', response.data.is_profile_image);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function myFunction(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var idCountry = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#state-dropdown,#edit_state_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-states') }}",
                type: "POST",
                data: {
                    country_id: idCountry,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#state-dropdown').html(
                        '<option value="">-- Select State --</option>');
                    if (selid != null && selval != null) {
                        $("#edit_state_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }
                    $.each(res.states, function(key, value) {
                        $("#state-dropdown,#edit_state_dropdown").append('<option value="' + value
                            .id + '">' + value.state_name + '</option>');
                    });
                }
            });
        }

        function stateFunction(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";

            var idState = val;

            var selid = selectedid;
            var selval = selectedval;


            $("#districts-dropdown,#edit_districts-dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-districts') }}",
                type: "POST",
                data: {
                    state_id: idState,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    if (selid != null && selval != null) {
                        $("#edit_districts-dropdown").append('<option selected value="' + selid + '">' +
                            selval + '</option>');
                    } else {
                        $('#districts-dropdown,#edit_districts-dropdown').html(
                            '<option value="">-- Select City --</option>');
                    }
                    $.each(res.districts, function(key, value) {
                        $("#districts-dropdown,#edit_districts-dropdown").append('<option value="' +
                            value
                            .id + '">' + value.district_name + '</option>');
                    });
                }
            });
        }

        function loadClassSection(val, selectedid, selectedval) {

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
                success: function(res) {

                    $('#section_dropdown').html(
                            '<option value="">-- Select Section --</option>');
                    $.each(res.section, function(key, value) {
                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

           function loadsubjects(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#subject_dropdown,#edit_subject_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-subject') }}",
                type: "POST",
                data: {
                    section_id: section_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#subject_dropdown').html(
                            '<option value="">-- Select Subject --</option>');
                    $.each(res.subjects, function(key, value) {
                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $("#subject_dropdown,#edit_subject_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }
    </script>
@endsection
