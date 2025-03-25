@extends('layouts.admin_master')
@section('mapsettings', 'active')
@section('masterrole_class_mapping', 'active')
@section('menuopenmap', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Role Class Mapping', 'active'=>'active']];
?>
<link rel="stylesheet" href="{{asset('public/css/select2.min.css') }}"> 
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
            .select2-container{
                width:100% !important;
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
                padding-top: 40px !important ;
            }
            body{
                    margin-left: 0px !important;
            }

           .nnsec{
            margin-left: -14px;margin-right: 10px;border-right: 1.5px solid #ecebeb85;padding-top:40px !important;
        }
        @media screen and (max-width: 700px){
                .nnsec{
                    margin-left: 0px !important;margin-right: 0px !important;border-right: 0px solid #ecebeb85!important;padding-top:20px !important;
                }
          .row.merged20 {
                    padding: 0px 0px !important;
                }
                .vanilla-calendar {
            width: 100% !important;
        }
    }
    </style>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title" style="font-size: 20px;"><!-- Role Class Mapping -->
                    <div class="row"> 
                        <div class=" col-md-3">
                            <label class="form-label" >Class </label>
                            <div class="form-line">
                                <select class="form-control" name="cls_id" id="cls_id" >
                                  <option value="">Select Class</option>
                                 @if(@isset($classes))
                                 @foreach ($classes as $class)
                                 <option value="{{$class->id}}">{{$class->class_name}}</option>   
                                 @endforeach
                                 @endif
                                </select> 
                            </div>
                        </div>
                        <div class=" col-md-3">
                            <label class="form-label" >Role </label>
                            <div class="form-line">
                                <select class="form-control" name="roleid" id="roleid" >
                                    <option value="">Select Role</option>
                                   @if(@isset($roles))
                                   @foreach ($roles as $role)
                                   <option value="{{$role->id}}">{{$role->user_role}} </option>   
                                   @endforeach
                                   @endif
                                </select> 
                            </div>
                        </div>
                        <div class=" col-md-3"> 
                            <label class="form-label" >Status </label>
                            <div class="form-line">
                                <select class="form-control" name="status_id" id="status_id" >
                                    <option value="">All</option>
                                    <option value="ACTIVE" selected>ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select> 
                            </div>
                        </div> 
                        <div class=" col-md-3"> 
                        <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn" class="btn btn-primary" style="float: right;">Add</button></a>
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
                                  <th>Role</th>
                                  <th>Classes</th>
                                  <th>Status</th>
                                  <th>Action</th> 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th><th></th>  </tr>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Role Class Mapping</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/role_class_mapping')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Role</label>
                                <div class="form-line">
                                    <select class="form-control" name="role_id" id="role_id" >
                                        <option value="">Select Role</option>
                                       @if(@isset($roles))
                                       @foreach ($roles as $role)
                                       <option value="{{$role->id}}">{{$role->user_role}} </option>   
                                       @endforeach
                                       @endif
                                    </select> 
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Classes</label>
                                <div class="form-line">
                                    <select class="form-control" id="class_id" name="class_ids[]" multiple required>
                                        <option value="">Select Classes</option>
                                        @if(!empty($classes))
                                            @foreach($classes as $class)
                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Role Class Mapping</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/role_class_mapping')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Role</label>
                                <div class="form-line">
                                    <select class="form-control" name="role_id" id="edit_role_id" >
                                        <option value="">Select Role</option>
                                       @if(@isset($roles))
                                       @foreach ($roles as $role)
                                       <option value="{{$role->id}}">{{$role->user_role}} </option>   
                                       @endforeach
                                       @endif
                                    </select> 
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Classes</label>
                                <div class="form-line">
                                    <select class="form-control" name="class_ids[]" id="edit_class_ids" multiple required>
                                        <option value="">Select Classes</option>
                                        @if(!empty($classes))
                                            @foreach($classes as $class)
                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
<script src="{{asset('public/js/select2.full.min.js') }}"></script>
    <script> 
        
        $(document).ready(function() { 
 
            $("#class_id").select2();
            $("#edit_class_ids").select2();
        });
        $('#addbtn').on('click', function () {
            $('#style-form')[0].reset();
        });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/role_class_mapping/datatables/", 
                    data: function ( d ) {
                        var roleid  = $('#roleid').val();
                        var status  = $('#status_id').val(); 
                        var cls_id  = $('#cls_id').val();
                        $.extend(d, {roleid:roleid,status:status,classid:cls_id});

                    }
                },
                columns: [
                    { data: 'user_role',  name: 'userroles.user_role'},
                    { data: 'is_classname',  name: 'class_ids'}, 
                    { data: 'status',  name: 'userroles.status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadmapping('+tid+')" title="Edit Mapping"><i class="fas fa-edit"></i></a>';
                        },

                    }, 
                   
                ],
                "order":[[0, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": "no-sort" }, 
                ]

            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if(index > 0) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );*/

            $('#status_id').on('change', function() {
                table.draw();
            });
            $('#roleid').on('change', function() {
                table.draw();
            }); 
            $('#cls_id').on('change', function() {
                table.draw();
            }); 
  
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
            } );
 

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
  

        function loadmapping(id){
            $('#edit-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/role_class_mapping')}}",
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
                $('#edit_role_id').val(response.data.role_id); 
                if($.trim(response.data.class_ids) != '') {
                    var classids = response.data.class_ids;
                    classids = classids.split(',');
                    $("#edit_class_ids").val(classids).select2();
                }
                $('#edit_status').val(response.data.status); 

                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        } 

    </script>

@endsection
