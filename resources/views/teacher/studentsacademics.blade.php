@extends('layouts.teacher_master')
@section('master_settings', 'active')
@section('master_student_academics', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Student', 'active' => 'active']];
?>
@section('content')


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Students Class Mappings
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
                                                <th>Admission No</th>

                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Academic Year</th>
                                                <th>From Month</th>
                                                <th>To Month</th>
                                                <th>Class</th>
                                                <th>Section</th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Student Academics</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/studentacademics') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Student <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="user_id" required>
                                        <option value="">Select Student</option>
                                        @if (!empty($students))
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->name }} {{ $student->last_name }} {{ $student->admission_no }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control course_id" name="class_id"
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
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Academic Year <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="year" class="form-control"
                                        style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                        name="academic_year" id="academic_year" minlength="4" maxlength="4"  minlength="4" maxlength="4" onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">From Month<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="month" class="form-control"
                                        style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                        name="from_month" id="from_month">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">To Month<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="month" class="form-control"
                                        style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                        name="to_month" id="to_month">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Student</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/studentacademics') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                    <div class="modal-body">
                        <div class="row">
                        	<div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Student <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" disabled name="user_id" id="edit_user_id" required>
                                        <option value="">Select Student</option>
                                        @if (!empty($students))
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->name }} {{ $student->last_name }} {{ $student->admission_no }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control course_id" name="class_id" id="edit_class_id"
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
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Academic Year <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="year" class="form-control"
                                        style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                        name="academic_year" id="edit_academic_year" minlength="4" maxlength="4" onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">From Month<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="month" class="form-control"
                                        style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                        name="from_month" id="edit_from_month">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">To Month<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="month" class="form-control"
                                        style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                        name="to_month" id="edit_to_month">
                                </div>
                            </div>
                            <div class="form-group form-float float-right col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
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
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{ route('tstudent_academics.data') }}',
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadStudentAcademics(' + tid +
                                ')" title="Edit Student Academics"><i class="fas fa-edit"></i></a>';
                        },

                    },
                	{ data: 'admission_no', name: 'admission_no' },

                	{ data: 'name',  name: 'name' },
                    { data: 'last_name',  name: 'last_name' },
                    { data: 'academic_year',  name: 'academic_year' },
                    { data: 'from_month',  name: 'from_month' },
                    { data: 'to_month',  name: 'to_month' },
                    { data: 'class_name',  name: 'class_name' },
                    { data: 'section_name',  name: 'section_name' },
                ],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]


            });


            $('.tblcountries tfoot th').each(function(index) {
                if (index != 0) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
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

        function loadStudentAcademics(id) {

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/studentacademics') }}",
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
                $('#edit_user_id').val(response.data.user_id);
                $('#edit_class_id').val(response.data.class_id);
                $('#section_dropdown').val(response.data.section_id);
                $('#edit_academic_year').val(response.data.academic_year);
                $('#edit_from_month').val(response.data.from_month);
                $('#edit_to_month').val(response.data.to_month);
                $('#edit_status').val(response.data.status);
                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.section_name;
                loadClassSection(val, selectedid, selectedval);
// alert(response.data.section_id)
                $('#section_dropdown').val(response.data.section_id);
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

                    } else {
                        $('#state-dropdown').html(
                            '<option value="">-- Select State --</option>');
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

                        if(selid != null && selval != null){
                            $("#edit_districts-dropdown").append('<option selected value="' + selid + '">' + selval + '</option>');
                        }else{
                            $('#districts-dropdown,#edit_districts-dropdown').html(
                        '<option value="">-- Select City --</option>');
                        }
                        $.each(res.districts, function(key, value) {
                            $("#districts-dropdown,#edit_districts-dropdown").append('<option value="' + value
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
        $('#section_dropdown,#edit_section_dropdown').html(
            '<option value="">-- Select Section --</option>');
        if (selid != null && selval != null) {
            $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                '  </option>');
        }
        $.each(res.section, function(key, value) {
            $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                .id + '">' + value.section_name + '</option>');
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

    </script>
@endsection
