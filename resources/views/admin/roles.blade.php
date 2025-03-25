@extends('layouts.admin_master')
@section('stasettings', 'active')
@section('master_userroles', 'active')
@section('menuopensta', 'active menu-is-opening menu-open')

@section('content')

<?php 
use App\Http\Controllers\AdminRoleController;

$rights = AdminRoleController::getRights();

?>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@if($rights['rights']['view'] == 1)
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><!-- User Roles  -->
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
                            @if($rights['rights']['add'] == 1)
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>
                            @endif
                            </div>
                        </div>  
                    </h4>        
                          
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    
                    <table class="table table-striped table-bordered tblcategory">
                      <thead>
                        <tr> 
                          <th>User Role</th>
                          <th>Status</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <!-- <tfoot>
                          <tr> 
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
    </section>

    <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add User Role</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/userroles')}}"
                                  method="post">

                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id" value="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Role</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="user_role" id="user_role" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="status" required>
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
@endif
@endsection

@section('scripts')

    <script>
        $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
            });
        $(function() {
            @if($rights['rights']['list'] == 1)
            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": "{{URL('/')}}/admin/userroles/datatables/", 
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    { data: 'user_role'},
                    { data: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            var ref_code = data.ref_code;

                            @if($rights['rights']['edit'] == 1)
                                return '<a href="#" onclick="loadRole('+tid+')" title="Edit Role" ><i class="fas fa-edit"></i></a>';
                            @else 
                                return '';
                            @endif
                        },

                    },
                ], 
                "order": [],
                "columnDefs": [ {
                      "targets": 'no-sort',
                      "orderable": false,
                } ]

            });

            /*$('.tblcategory tfoot th').each( function (index) {
                var title = $(this).text();
                if(index != 2) {
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            // Apply the search
            table.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                                .search( this.value )
                                .draw();
                    }
                } );
            } );*/

            $('#status_id').on('change', function() {
                table.draw();
            });
            
            @endif
            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcategory').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                           $("#style-form")[0].reset();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });

        });

        function loadRole(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/userroles')}}",
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
                $('#user_role').val(response.data.user_role);
                $('#status').val(response.data.status);
                $('#smallModal').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
