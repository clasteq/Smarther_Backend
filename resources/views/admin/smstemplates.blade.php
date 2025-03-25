@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_smstemplates', 'active')
@section('menuopen', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$user_type = Auth::User()->user_type;
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Country', 'active'=>'active']];
?>
@section('content')
@if($user_type == "SUPER_ADMIN")
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title"><!-- SMS Templates -->
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
                        @if($user_type == 'SUPER_ADMIN')
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
                                  <th>Name</th>
                                  <th>Content</th>
                                  <th>Template ID</th>
                                  <th>No of Variables</th>
                                  <th>Is Name replace</th>
                                  <th>Replace Variables</th> 
                                  <th>Status</th>
                                  <th class="no-sort">Action</th>
                                </tr>
                              </thead><!-- 
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
                    <h4 class="modal-title" id="smallModalLabel">Add MS Template</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/smstemplates')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required minlength="1" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Content</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="content" required minlength="1" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Template ID</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="template_id" required>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">No of variables</label>
                                <div class="form-line">
                                    <input type="number" class="form-control no_of_variables" name="no_of_variables" required min="0" max="10" onkeypress="loadreplacevars(this);">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Name Replace Needed</label>
                                <div class="form-line">
                                    <select class="form-control is_name_replace" name="is_name_replace" required  onchange="loadreplacevars(this);">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Replace Variables</label>
                                <div class="form-line">
                                    <select type="number" class="form-control is_name_replace_var" name="is_name_replace_var">
                                        <option value="">Select</option>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit MS Template</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/smstemplates')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" id="edit_name" required minlength="1" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Content</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="content" id="edit_content" required minlength="1" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Template ID</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="template_id" id="edit_template_id" required>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">No of variables</label>
                                <div class="form-line">
                                    <input type="number" class="form-control no_of_variables" name="no_of_variables" id="edit_no_of_variables" required min="0" max="10" onkeypress="loadreplacevars(this);">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Name Replace Needed</label>
                                <div class="form-line">
                                    <select class="form-control is_name_replace " name="is_name_replace" id="edit_is_name_replace" required onchange="loadreplacevars(this);">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Replace Variables</label>
                                <div class="form-line">
                                    <select class="form-control is_name_replace_var" name="is_name_replace_var" id="edit_is_name_replace_var">
                                        <option value="">Select</option>
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
@else 
<section class="content">
    @include('admin.notavailable')
</section>
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
                    "url":"{{URL('/')}}/admin/smstemplates/datatables/",
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    { data: 'name',  name: 'name'},
                    { data: 'content',  name: 'content'}, 
                    { data: 'template_id',  name: 'template_id'},
                    { data: 'no_of_variables',  name: 'no_of_variables'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.is_name_replace > 0){ 
                                return 'Yes'; 
                            }   else {
                                return 'No';
                            }
                        },

                    },
                    { data: 'is_name_replace_var',  name: 'is_name_replace_var'}, 
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadTemplate('+tid+')" title="Edit Template"><i class="fas fa-edit"></i></a> <a href="#" onclick="deletedata(' + tid +')" title="Delete Template"><i class="fas fa-trash"></i></a>';
                        },

                    },

                ],
                "order":[],
                "columnDefs": [
                    { "orderable": false, "targets": 3 }, 
                    { "orderable": false, "targets": 'no-sort', },
                ]

            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if(index != 4 && index != 0 && index != 8) {
                    var title = $(this).text();
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

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

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
            $('#edit_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == "FAILED") {

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

        function loadTemplate(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/smstemplates')}}",
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
                $('#edit_content').val(response.data.content);
                $('#edit_template_id').val(response.data.template_id);
                $('#edit_no_of_variables').val(response.data.no_of_variables);
                $('#edit_is_name_replace').val(response.data.is_name_replace);
                $('#edit_status').val(response.data.status); 
                $('#smallModal-2').modal('show');

                loadreplacevars($('#edit_is_name_replace_var'), response.data.is_name_replace_var);
                $('#edit_is_name_replace_var').val(response.data.is_name_replace_var); 

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadreplacevars($obj, selval) {
            var frm = $($obj).parents('form').attr('id');
            var html = '<option value="">Select</option>';
            var is_name_replace = $('#'+frm+' .is_name_replace').val();
            if(is_name_replace == 1) {
                var no_of_variables = $('#'+frm+' .no_of_variables').val();
                if(no_of_variables > 10) {
                     $('#'+frm+' .no_of_variables').val(10);
                     no_of_variables = 10;
                } 
                if(no_of_variables <0) {
                     $('#'+frm+' .no_of_variables').val(0);
                     no_of_variables = 0;
                } 
                no_of_variables = parseInt(no_of_variables);

                var html = '<option value="">Select</option>';
                for(var i=1; i<=no_of_variables; i++) {
                    var selectedval = ''; console.log(selval)
                    if(selval != '' && selval != null && selval == '##var'+i+'##') { console.log(selval)
                        selectedval = 'selected';
                    }
                    html += '<option value="##var'+i+'##" '+selectedval+'>##var'+i+'##</option>';
                }
            }
            $('#'+frm+' .is_name_replace_var').html(html);
            $('#'+frm+' .is_name_replace_var').val(selval);
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
                        url: " {{URL::to('/admin/delete/smstemplates')}}",
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

                            $('.tblcountries').DataTable().ajax.reload();
                        } else{
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
