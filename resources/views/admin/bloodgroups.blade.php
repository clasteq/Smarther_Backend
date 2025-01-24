@extends('layouts.admin_master')
@section('master_settings', 'active')
@section('master_bloodgroups', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Blood Groups', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
     
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Blood Group
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class="row">
                            <div class="row col-md-12">
                                <div class="form-group col-md-3 ">
                                    <label class="form-label">Is Display</label>
                                    <select class="form-control" name="status_id" id="status_id">
                                        <option value="">All</option>
                                        <option value="YES" selected>YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries" id="example1">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th> 
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
                    <h4 class="modal-title" id="smallModalLabel">Add Blood Group</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/bloodgroups') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row"> 

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Blood Group</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required minlength="1"
                                        maxlength="5">
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Display</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" required>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Blood Group</h4>
                </div>
                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/bloodgroups') }}"
                    method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Blood Group</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" id="edit_name" required minlength="1"
                                        maxlength="5">
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Display</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" name="edit_status" required>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-link waves-effect" id="edit_style">SAVE</button>
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

            var table = $('#example1').DataTable({

                processing: false,
                serverSide: true,
                responsive: true,
                "lengthChange": false,
                "ajax": {
                    "url": "{{ URL('/') }}/admin/bloodgroups/datatables/",
                    data: function(data) {

                        // Include additional data if needed
                        data.status = $('#status_id').val();


                    }
                },
                columns: [
                    {
                        data: 'name', name:'name'
                    }, 
                    {
                        data: 'status', name:'status'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadSection(' + tid +
                                ')" title="Edit Blood Group"><i class="fas fa-edit"></i></a> <a href="#" onclick="deletedata(' + tid +
                                    ')" title="Delete Blood Group"><i class="fas fa-trash"></i></a>';
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
            $('#example1 tfoot').insertAfter('#example1 thead');
            $('#example1 tfoot th').each(function() {
                var title = $(this).text();

                if (($(this).index() != 2)) {
                    $(this).html(
                        '<input class="btn" type="text" style="width:100%;border-color:#6c757d; cursor: auto;" placeholder="Search ' +
                        title + '" />');

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

                        if (response.status == 'SUCCESS') {

                            swal('Success', response.message, 'success');

                            $('#example1').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == 'FAILED') {

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
                    beforeSend: function() {
                        $("#edit_style").text('Processing..').prop('disabled', true);
                    },
                    success: function(response) {
                        $("#edit_style").prop('disabled', false).text('SAVE');
                        if (response.status == 'SUCCESS') {
                            swal('Success', response.message, 'success');
                            $('#example1').DataTable().ajax.reload();
                            $('#smallModal-2').modal('hide');
                        } else if (response.status == 'FAILED') {
                            swal('Oops', response.message, 'warning');
                        }
                    },
                    error: function() {
                        $("#edit_style").prop('disabled', false).text('SAVE');
                        swal('Oops', 'Something went wrong.', 'error');
                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });

        });

        function loadSection(id) {
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: "{{ URL::to('/admin/edit/bloodgroups') }}",
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
                $('#edit_name').val(response.data.name); 
                $('#edit_status').val(response.data.status);
                $('#smallModal-2').modal('show');
            });
            request.fail(function() {
                swal("Oops!", "Sorry, could not process your request", "error");
            });
        }

        function deletedata(id){
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
                        url: " {{URL::to('/admin/delete/bloodgroups')}}",
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
                        if (response.status == 'SUCCESS') {

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
