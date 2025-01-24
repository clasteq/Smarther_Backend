@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_subjects', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Subjects', 'active' => 'active']];
?>
@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px" class="card-title">Subjects
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class=" col-md-3">
                            <label class="form-label" >Status </label>
                            <div class="form-line">
                                <select class="form-control" name="status_id" id="status_id" >
                                    <option value="">All</option>
                                    <option value="ACTIVE" selected>ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Subject Name</th>
                                                <th>Short Name</th>
                                                <th>Subject Color Code</th>
                                                <th>Subject Image</th>
                                                <th>Position</th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Subject</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/subjects') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="subject_name" required minlength="2" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Short Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="short_name" required minlength="2" maxlength="3">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Color Code (ex., ff0000)</label>
                                <div class="form-line">
                                    {{-- <input type="text" class="form-control" name="subject_colorcode" required minlength="6" maxlength="6"> --}} 
                                    <select class="form-control subject_colorcode" name="subject_colorcode" id="subject_colorcode" required>
                                        <option value="">Select Color Code</option>
                                        <option value="#41BEFF">#41BEFF</option>
                                        <option value="#7083EA">#7083EA</option>
                                        <option value="#E57BA1">#E57BA1</option>
                                        <option value="#88A789">#88A789</option>
                                        <option value="#DEBA89">#DEBA89</option>
                                        <option value="#7B95AF">#7B95AF</option>
                                        {{-- <option value="#41BEFF">#41BEFF</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Image</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="subject_image" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Subject</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/subjects') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="subject_name" id="edit_subject_name" required minlength="2" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Short Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="short_name" id="edit_short_name" required minlength="2" maxlength="3">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Color Code (ex., ff0000)</label>
                                <div class="form-line">
                                 
                                    <select class="form-control subject_colorcode" name="subject_colorcode" id="edit_subject_colorcode" required>
                                        <option value="">Select Color Code</option>
                                        <option value="#41BEFF"><span class="color_code"></span>#41BEFF</option>
                                        <option value="#7083EA"><span class="color_code"></span>#7083EA</option>
                                        <option value="#E57BA1"><span class="color_code"></span>#E57BA1</option>
                                        <option value="#88A789"><span class="color_code"></span>#88A789</option>
                                        <option value="#DEBA89"><span class="color_code"></span>#DEBA89</option>
                                        <option value="#7B95AF"><span class="color_code"></span>#7B95AF</option>
                                        {{-- <option value="#41BEFF">#41BEFF</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Image</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="subject_image" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position" required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <img src="" id="img_subject_image" height="100" width="100">
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

        $('.subject_colorcode option').each(function() {
          
  $(this).css('background-color', $(this).val());
});

        $(function() {
            

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/subjects/datatables/",   
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadCountry(' + tid +
                                ')" title="Edit Country"><i class="fas fa-edit"></i></a>';
                        },

                    },
                    {
                        data: 'subject_name',
                        name: 'subject_name'
                    },
                    {
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            var subject_colorcode = $.trim(data.subject_colorcode);
                            if (subject_colorcode != '' || subject_colorcode != null || subject_colorcode != "null") { 
                                return '<span style="color:'+subject_colorcode+';"> '+subject_colorcode+' </span>';
                            } else {
                                return '';
                            }
                        },
                        name: 'subject_colorcode'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            if (data.subject_image != '' || data.subject_image != null) {
                                var tid = data.is_subject_image;
                                return '<img src="' + tid + '" height="50" width="50">';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    
                ],
                "order":[[4, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets":3 },
                   
                ]

            });

            $('.tblcountries tfoot th').each(function(index) {
                if (index != 5 && index != 3 && index != 0) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });
            $('#status_id').on('change', function() {
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

        function loadCountry(id) {

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/subjects') }}",
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
                $('#edit_subject_name').val(response.data.subject_name);
                $('#edit_short_name').val(response.data.short_name);
                $('#edit_subject_colorcode').val(response.data.subject_colorcode);
                $('#edit_position').val(response.data.position);
                $('#edit_status').val(response.data.status);
                $('#img_subject_image').attr('src', response.data.is_subject_image);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

   
    </script>

@endsection
