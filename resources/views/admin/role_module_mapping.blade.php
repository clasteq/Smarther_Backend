@extends('layouts.admin_master')
@section('mapsettings', 'active')
@section('masterrole_module_mapping', 'active')
@section('menuopenmap', 'active menu-is-opening menu-open')
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
				<!-- <div class="card-header">
					<h4 class="card-title">
						Role Module Mapping
                        @if($rights['rights']['add'] == 1)
                        
                        @endif
					</h4>

				</div> -->
				<div class="card-content collapse show">
					<div class="card-body card-dashboard">
						<div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
							<div class="table-responsicve">
								<table class="table table-striped table-bordered tblcountries">
									<thead>
										<tr>
											<th>Name</th>																				
											
											<th class="not-export-column">Action</th>
										</tr>
									</thead>
									<tfoot>
										
										
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
@endif

@endsection

@section('scripts')

<script>

        $(function() {
            @if($rights['rights']['list'] == 1)
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": "{{URL('/')}}/admin/user_roles/datatables/", 
                },
                columns: [
                	{ data: 'user_role'},        

                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            @if($rights['rights']['edit'] == 1)
                                return '<a href="role_module_mapping/update_role_access?id='+tid+'" title="Edit"><i class="fas fa-edit"></i></a>';
                            @else 
                                return '';
                            @endif
                        },

                    },
                ], 

            });

            $('.tblcountries tfoot th').each( function () {
                var title = $(this).text();
                var index=$(this).index();
                if(index!=1){
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

        function loadRole(id){
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/role')}}",
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
                $('#edit_name').val(response.data.role_name);                           
                $('#edit_status').val(response.data.role_status);               
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
