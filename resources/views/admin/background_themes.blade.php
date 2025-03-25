@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_bthemes', 'active')
@section('menuopencomn', 'active menu-is-opening menu-open') 
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Background Themes', 'active'=>'active']];
?>
@section('content')

<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 

<style type="text/css">
    .bgtheme {
            border: 1px solid black;
    } 
</style>

@if((isset($session_module['Background Themes']) && ($session_module['Background Themes']['list'] == 1)) || ($user_type == 'SCHOOL'))
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title"><!-- Background Themes -->
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
                        @if((isset($session_module['Background Themes']) && ($session_module['Background Themes']['add'] == 1)) || ($user_type == 'SCHOOL'))
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
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th>Theme Name</th>
                                  <th>Background Image</th>
                                  <th>Position</th>
                                  <th>Status</th>
                                  <th>Action</th>

                                </tr>
                              </thead>
                              <!-- <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Background Theme</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/bthemes')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Background Theme Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required minlength="3" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Image (714 * 372) (jpg, png)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="image" required>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Background Theme</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/bthemes')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Background Theme Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="name" id="edit_name" required minlength="3" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Image (714 * 372) (jpg, png)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="image" id="edit_image">
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
                                    <select class="form-control" name="status"  id="edit_status" required>
                                      <option value="ACTIVE">ACTIVE</option>
                                      <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <img src="" id="is_image" height="100" width="100">
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

@else 
@include('admin.notavailable') 
@endif

@endsection

@section('scripts')

    <script>
        $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
            });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/bthemes/datatables/", 
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    { data: 'name',  name: 'name'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.image != '' || data.image != null){
                                var tid = data.is_image;
                                return '<img src="'+tid+'" height="50" width="50" class="bgtheme ">';
                            }   else {
                                return '';
                            }
                        },

                    }, 
                    { data: 'position',  name: 'position'},
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            var urls = ''
                            @if((isset($session_module['Background Themes']) && ($session_module['Background Themes']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                            var tid = data.id;
                            urls +=  '<a href="#" onclick="loadCategory('+tid+')" title="Edit Background Themes"><i class="fas fa-edit"></i></a>';
                            @endif
                            @if((isset($session_module['Background Themes']) && ($session_module['Background Themes']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                            var tid = data.id;
                            urls += ' <a href="#" onclick="deletedata(' + tid +')" title="Delete Background Themes"><i class="fas fa-trash"></i></a>';
                            @endif

                            return urls; 
                        },

                    },

                ],
                "order":[[0, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 1 }, { "orderable": false, "targets": 3 }, { "orderable": false, "targets": 4 }
                ],
               
            });

           /* $('.tblcountries tfoot th').each( function (index) {
                if( index != 1 && index != 3 && index != 4) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );*/

            $('#status_id').on('change', function() {
                table.draw();
            });
            // Apply the search
            /*table.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                                .search( this.value )
                                .draw();
                    }
                } );
            } );*/
            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == 'SUCCESS') {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                        }
                        else if (response.status == 'FAILED') {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });
            $('#edit_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == 'SUCCESS') {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == 'FAILED') {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });
        });

        function loadCategory(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/admin/edit/bthemes')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    code:id,
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {

                $('#id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_status').val(response.data.status);
                $('#edit_position').val(response.data.position);
                $('#is_image').attr('src', response.data.is_image);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
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
                        url: " {{URL::to('/admin/delete/bthemes')}}",
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

                            $('.tblcountries').DataTable().ajax.reload();
                        }
                        else{
                            swal('Oops',response.message,'error'); 
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
