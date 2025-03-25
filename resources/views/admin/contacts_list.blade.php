@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_contacts_list', 'active')
@section('menuopen', 'active menu-is-opening menu-open') 
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Contacts List ', 'active'=>'active']];
?>
<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title"><!-- Contacts List  -->
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
                        @if((isset($session_module['Contacts For']) && ($session_module['Contacts For']['add'] == 1)) || ($user_type == 'SCHOOL'))
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
                                  <th>For</th>
                                  <th>Name</th>
                                  <th>Mobile</th>
                                  <th>Email</th>
                                  <th>Description</th> 
                                  <th>Status</th>
                                  <th>Action</th>

                                </tr>
                              </thead>
                              <!-- <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Contacts List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/contacts_list')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">For <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="contact_for" required>
                                        <option value="">Select Contact For</option>
                                        @if(!empty($contactsfor))
                                            @foreach($contactsfor as $for)
                                                <option value="{{$for->id}}">{{$for->name}}</option>
                                            @endforeach
                                        @endif 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="contact_name" required minlength="3" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="contact_mobile" required minlength="10" maxlength="10" onkeypress="return isNumber(event, this);">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="contact_email" maxlength="150">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Description <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="contact_info" required minlength="3" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Contacts List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/contacts_list')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">For <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="contact_for" id="contact_for" required>
                                        <option value="">Select Contact For</option>
                                        @if(!empty($contactsfor))
                                            @foreach($contactsfor as $for)
                                                <option value="{{$for->id}}">{{$for->name}}</option>
                                            @endforeach
                                        @endif 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="contact_name" id="contact_name" required minlength="3" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="contact_mobile" id="contact_mobile" required minlength="10" maxlength="10" onkeypress="return isNumber(event, this);">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="contact_email" id="contact_email" maxlength="150">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Description <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="contact_info" id="contact_info" required minlength="3" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="status" required>
                                      <option value="YES">YES</option>
                                      <option value="NO">NO</option>
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
        $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
            });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/contacts_list/datatables/", 
                },
                columns: [
                    { data: 'contact_for',  name: 'contact_for'},
                    { data: 'contact_name',  name: 'contact_name'},
                    { data: 'contact_mobile',  name: 'contact_mobile'},
                    { data: 'contact_email',  name: 'contact_email'},
                    { data: 'contact_info',  name: 'contact_info'},
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadContacts('+tid+')" title="Edit Contacts"><i class="fas fa-edit"></i></a>';
                        },

                    },

                ], 
                "columnDefs": [
                    { "orderable": false, "targets": 6 }
                ],
               
            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if( index != 0) {
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

                        $("#add_style").text('SAVE');

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

                        $("#add_style").text('SAVE');

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

                        $("#edit_style").text('SAVE');

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

                        $("#edit_style").text('SAVE');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });
        });

        function loadContacts(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/admin/edit/contacts_list')}}",
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
                $('#contact_for').val(response.data.contact_for);
                $('#contact_name').val(response.data.contact_name);
                $('#contact_mobile').val(response.data.contact_mobile);
                $('#contact_email').val(response.data.contact_email);
                $('#contact_info').val(response.data.contact_info);
                $('#status').val(response.data.status);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
