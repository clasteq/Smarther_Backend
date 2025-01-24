@extends('layouts.admin_master') 
@section('master_settings', 'active')
@section('master_modules', 'active')
@section('menuopenm', 'active menu-is-opening menu-open') 
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
					<h4 class="card-title">
						Modules
						@if($rights['rights']['add'] == 1)
						<a href="#" data-toggle="modal" data-target="#smallModal" id="addbtn"><button class="btn btn-primary" style="float: right;">Add</button></a>
						@endif
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
											<th>Parent Module</th>	
											<th>Rank</th>		
										    <th>Url</th>
										    <th>Is Heading</th>
											<th>Status</th>
											<th class="not-export-column">Action</th>
										</tr>
									</thead>
									<tfoot>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										
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
				<h4 class="modal-title" id="smallModalLabel">Add Module</h4>
			</div>

			<form id="style-form" enctype="multipart/form-data"
				action="{{url('/admin/save/module')}}" method="post">

				{{csrf_field()}}

				<div class="modal-body">
					<div class="row">

						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Module Name</label>
							<div class="form-line">
								<input type="text" class="form-control" name="name" required>
							</div>
						</div>
						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Parent Module</label>
							<div class="form-line">
								<select style="width: 100%" class="form-control select2"
									name="module_id">
									<option value="0">Select Module</option>
									@if(count($module)>0) @foreach($module as $k=>$v)
									<option value="{{$v->id}}">{{$v->module_name}}</option>
									@endforeach @endif
								</select>
							</div>
						</div>
						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Rank</label>
							<div class="form-line">
								<input type="text" class="form-control" name="rank"
									required>
							</div>
						</div>
						
						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Url</label>
							<div class="form-line">
								<input type="text" class="form-control" name="url" required>
							</div>
						</div>

						<div class="form-group form-float float-left col-md-6 ">
							<label class="form-label">Is Heading</label>
							<div class="form-line">
								<select class="form-control" name="menu_item" required>
									<option value="">Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>

						<div class="form-group form-float float-left col-md-12">
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Add</label>
								<div class="form-line">
									<input type="checkbox" name="module_add" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Edit</label>
								<div class="form-line">
									<input type="checkbox" name="module_edit" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Delete</label>
								<div class="form-line">
									<input type="checkbox" name="module_delete" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">View</label>
								<div class="form-line">
									<input type="checkbox" name="module_view" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">List</label>
								<div class="form-line">
									<input type="checkbox" name="module_list" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Status Update</label>
								<div class="form-line">
									<input type="checkbox" name="module_status_update" value="1">
								</div>
							</div>
						</div>
						

						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Status</label>
							<div class="form-line">
								<select class="form-control" name="status" required>
									<option value="1">ACTIVE</option>
									<option value="2">INACTIVE</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="sumbit" class="btn btn-link waves-effect"
						id="add_style">SAVE</button>
					<button type="button" class="btn btn-link waves-effect"
						data-dismiss="modal">CLOSE</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="smallModalLabel">Edit Module</h4>
			</div>

			<form id="edit-style-form" enctype="multipart/form-data"
				action="{{url('/admin/save/module')}}" method="post">

				{{csrf_field()}} <input type="hidden" name="id" id="id">
				<div class="modal-body">
					<div class="row">
						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Name</label>
							<div class="form-line">
								<input type="text" class="form-control" name="name"
									id="edit_name" required>
							</div>
						</div>
						
						
						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Parent Module</label>
							<div class="form-line">
								<select style="width: 100%" class="form-control select2"
									name="module_id" id="edit_module_id">
									<option value="0">Select Module</option>
									@if(count($module)>0) @foreach($module as $k=>$v)
									<option value="{{$v->id}}">{{$v->module_name}}</option>
									@endforeach @endif
								</select>
							</div>
						</div>

						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Rank</label>
							<div class="form-line">
								<input type="text" class="form-control" name="rank"
									id="edit_rank" required>
							</div>
						</div>
						
						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">URL</label>
							<div class="form-line">
								<input type="text" class="form-control" name="url"
									id="edit_url" required>
							</div>
						</div>
						
						<div class="form-group form-float float-left col-md-6 ">
							<label class="form-label">Is Heading</label>
							<div class="form-line">
								<select class="form-control" name="menu_item" id="edit_menu_item" required>
									<option value="">Select</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>  

						<div class="form-group form-float float-left col-md-12">
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Add</label>
								<div class="form-line">
									<input type="checkbox" name="module_add" id="edit_module_add" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Edit</label>
								<div class="form-line">
									<input type="checkbox" name="module_edit" id="edit_module_edit" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Delete</label>
								<div class="form-line">
									<input type="checkbox" name="module_delete" id="edit_module_delete" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">View</label>
								<div class="form-line">
									<input type="checkbox" name="module_view" id="edit_module_view" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">List</label>
								<div class="form-line">
									<input type="checkbox" name="module_list" id="edit_module_list" value="1">
								</div>
							</div>
							<div class="form-group form-float float-left col-md-2">
								<label class="form-label">Status Update</label>
								<div class="form-line">
									<input type="checkbox" name="module_status_update" id="edit_module_status_update" value="1">
								</div>
							</div>
						</div>

						<div class="form-group form-float float-left col-md-6">
							<label class="form-label">Status</label>
							<div class="form-line">
								<select class="form-control" name="status" id="edit_status"
									required>
									<option value="1">ACTIVE</option>
									<option value="2">INACTIVE</option>
								</select>
							</div>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="sumbit" class="btn btn-link waves-effect"
						id="edit_style">SAVE</button>
					<button type="button" class="btn btn-link waves-effect"
						data-dismiss="modal">CLOSE</button>
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
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                stateSave: true,
                "ajax": {
                    "url": "{{URL('/')}}/admin/modules/datatables/",
                },
                columns: [
                	{ data: 'module_name',name:'modules.module_name'},        
                    { data: 'is_parent_module',name:'pf.module_name'},         
                    { data: 'menu_rank',name:'modules.menu_rank'},                   
                    { data: 'url',name:'modules.url'},      
                    { data: 'menu_item',name:'modules.menu_item'},                 
                    { data: 'is_status',name:'modules.is_status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            @if($rights['rights']['edit'] == 1)
                            	return '<a href="#" onclick="loadModule('+tid+')" title="Edit "><i class="fas fa-edit"></i></a>';
                        	@else 
                                return '';
                            @endif
                        },

                    },
                ],
                /*dom: 'Blfrtip',
                buttons: 
                    [                  
                	{ extend: 'csv',  
                    	text: 'Export All',
                		className: 'btn btn-warning btn-md ml-3',
                    	action: newExportAction,exportOptions: 
                    	{
                        columns: ":not(.not-export-column)"
                        } 
                    },
                    
                	],*/
                "columnDefs": [
                    { "orderable": false, "targets": 6 }, 
                    { "orderable": false, "targets": 5 } 
                ]

            });

            $('.tblcountries tfoot th').each( function () {
                var title = $(this).text();
                var index=$(this).index();
                if(index<5){
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
            } );
            @endif
            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

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

                        $("#edit_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });



        });

        function loadModule(id){
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/module')}}",
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
                $('#edit_name').val(response.data.module_name);  
                $('#edit_url').val(response.data.url);  
                $('#edit_icon').val(response.data.icon);  
                $('#edit_rank').val(response.data.menu_rank);   
                $('#edit_menu_item').val(response.data.menu_item);   
                $('#edit_module_id').val(response.data.parent_module_fk).trigger('change');           
                $('#edit_status').val(response.data.status);   

                if(response.data.module_add  == 1) {
                	$('#edit_module_add').prop('checked', true);
                } 	else {
                	$('#edit_module_add').prop('checked', false);
                }

                if(response.data.module_edit    == 1) {
                	$('#edit_module_edit').prop('checked', true);
                } 	else {
                	$('#edit_module_edit').prop('checked', false);
                }

                if(response.data.module_delete    == 1) {
                	$('#edit_module_delete').prop('checked', true);
                } 	else {
                	$('#edit_module_delete').prop('checked', false);
                }

                if(response.data.module_view     == 1) {
                	$('#edit_module_view').prop('checked', true);
                } 	else {
                	$('#edit_module_view').prop('checked', false);
                }

                if(response.data.module_list     == 1) {
                	$('#edit_module_list').prop('checked', true);
                } 	else {
                	$('#edit_module_list').prop('checked', false);
                }

                if(response.data.module_status_update  == 1) {
                	$('#edit_module_status_update').prop('checked', true);
                } 	else {
                	$('#edit_module_status_update').prop('checked', false);
                }

                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
