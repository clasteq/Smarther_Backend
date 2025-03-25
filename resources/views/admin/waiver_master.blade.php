@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('master_waiver_category', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Waiver Category', 'active' => 'active']];
?><?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">
    <style>
        .dropdown-menu.show {
            display: block;
            width: 100%;
            top: 30px !important;
            left: auto !important;
            padding: 20px;
        }

        .checkbox input[type="checkbox"] {
            width: 20px !important;

        }

        .select2-container--default .select2-selection--single {
            height: 45px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-top: 8px;
        }

        .select2-selection__choice {
            color: #000 !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #f8fafa;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }

        .row.merged20 {
            margin: 0px 0px !important;
        }

        .sidecoderight {
            padding-top: 40px !important;
        }

        body {
            margin-left: 0px !important;
        }

        .nnsec {
            margin-left: -14px;
            margin-right: 10px;
            border-right: 1.5px solid #ecebeb85;
            padding-top: 40px !important;
        }

        @media screen and (max-width: 700px) {
            .nnsec {
                margin-left: 0px !important;
                margin-right: 0px !important;
                border-right: 0px solid #ecebeb85 !important;
                padding-top: 20px !important;
            }

            .row.merged20 {
                padding: 0px 0px !important;
            }

            .vanilla-calendar {
                width: 100% !important;
            }
        }
    </style>
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title"><!-- Waiver Category -->
                            <div class="row col-md-12">
                            <div class="form-inline col-md-3 " >
                                    <label class="form-label mr-1">Status</label>
                                    <select class="form-control" name="status_id" id="status_id">
                                        <option value="" >All</option>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                            </div>
                            <div class="form-inline col-md-8 float-right " ></div>
                            <div class="form-inline col-md-1 float-right " >
                            @if( ($user_type == 'SCHOOL'))
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>
                            @endif
                            </div>
                            </div>  
                        </h4>  

                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries" id="example1">
                                        <thead>
                                            <tr>
                                                <th>Waiver Category</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <!-- <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot> -->
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
                    <h4 class="modal-title" id="smallModalLabel">Add Waiver Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/wavier_category') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Waiver Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="wavier_name" required minlength="1"
                                        maxlength="200">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Waiver Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/wavier_category') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Waiver Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="wavier_name" id="edit_wavier_name"
                                        required minlength="1" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position"
                                        required min="1">
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
    <script src="{{ asset('public/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });



        $(function() {

            var table = $('#example1').DataTable({

                processing: true,
                serverSide: true,
                responsive: false, 
                "ajax": {
                    "url": "{{ URL('/') }}/admin/wavier_category_data/datatables/",
                    data: function(data) {

                        // Include additional data if needed
                        data.status = $('#status_id').val();


                    }
                },
                columns: [
                    {
                        data: 'name'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadSection(' + tid +
                                ')" title="Edit wavier category"><i class="fas fa-edit"></i></a>  <a href="#" onclick="deletecategory(' + tid +
                                    ')" title="Delete wavier category"><i class="fas fa-trash"></i></a>';
                        },

                    },

                ],
                "order": [],
                "columnDefs": [

                    {
                        "targets": 'no-sort',
                        "orderable": false,
                    }
                ],
                //  dom: 'Bfrtip',
                //  "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

            });
            /*$('#example1 tfoot').insertAfter('#example1 thead');
            $('#example1 tfoot th').each(function() {
                var title = $(this).text();

                if (($(this).index() != 2) && ($(this).index() != 3)) {
                    $(this).html(
                        '<input class="btn" type="text" style="width:100%;border-color:#6c757d; cursor: auto;" placeholder="Search ' +
                        title + '" />');

                }
            });*/

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

            $('#status_id').on('change', function() {
                table.draw();
            });


            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('#example1').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

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

                        $("#edit_style").text('SAVE');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('#example1').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });

        });

        function loadSection(id) {
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('/admin/edit/wavier_category') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    code: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {

                $('#id').val(response.data.id);

                $('#edit_wavier_name').val(response.data.name);

                $('#edit_status').val(response.data.status);
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function deletecategory(id){
            swal({
                title : "",
                text : "Are you sure to delete?",
                type : "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
            },
            function(isConfirm){
                if (isConfirm) {
                    var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('/admin/delete/wavier_category')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            id:id,
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if (response.status == 1) {

                            swal('Success',response.message,'success');

                            $('#example1').DataTable().ajax.reload();
                        }
                        else{
                            swal('Oops',response.message,'error');

                        //   $('.tblcountries').DataTable().ajax.reload();
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });
                }
            })


        }
    </script>

@endsection
