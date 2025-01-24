@extends('layouts.admin_master')
@section('mastersettings', 'active') 
@section('master_banners', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Banner', 'active'=>'active']];
?>
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Banners 
              
                    <a href="#" data-toggle="modal" data-target="#smallModal" id="addbanner"><button class="btn btn-primary" style="float: right;">Add</button></a> 
               
                </h4>        
                          
                </div>
               
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr> 
                                  <th>Banner Title</th> 
                                  <th>Image</th>  
                                  <th>Position</th>
                                  <th>Status</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Banner</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/banners')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
						
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Banner Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
													
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Image</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="image" required>
                                </div>
                            </div> 
                            <div class="d-none form-group form-float float-left col-md-6">
                                <label class="form-label">Is Link</label>
                                <div class="form-line">
                                    <select class="form-control" name="is_link" >
                                        <option value="NO">Select</option>
                                      <option value="YES">YES</option>
                                      <option value="NO" selected>NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none ">
                                <label class="form-label">Link To</label>
                                <div class="form-line"><!--  onchange="loadLevels(this);" -->
                                    <select class="form-control" name="link_level"  id="link_level" onchange="loadcatprod(this)">
                                        <option value="0">Select</option>
                                      <option value="1">Categories</option>
                                      <option value="2">Products</option>
                                    </select>
                                </div>
                            </div> <?php // echo "<pre>"; print_r($categories); exit; ?>
                            <div class="form-group form-float float-left col-md-6 category_id d-none">
                                <label class="form-label">Categories</label>
                                <div class="form-line">
                                    <select class="form-control link_id" name="category_id" id="category_id">
                                        <option value="0">Select</option> 
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6 product_id d-none">
                                <label class="form-label">Products</label>
                                <div class="form-line">
                                    <select class="form-control link_id" name="product_id" id="product_id">
                                        <option value="0">Select</option> 
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6 d-none ">
                                <label class="form-label">Type</label>
                                <div class="form-line"> 
                                    <select class="form-control" name="type">
                                        <option value="MENU_BANNER">Home page</option>
                                        <option value="TOP_BANNER">Categories page</option>
										<!--    <option value="OFFER_BANNER">OFFER_BANNER</option>
										
										<option value="FEATURE_BANNER">FEATURE_BANNER</option>
										<option value="SUBSCRIPTION_BANNER">SUBSCRIPTION_BANNER</option>
										<option value="BRAND_BANNER">BRAND_BANNER</option>-->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Display Position</label>
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
								<div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Banner Short Description</label>
                                <div class="form-line">
                                    <textarea type="text" class="form-control" name="short_desc" ></textarea>
                                </div>
                            </div>	
						<div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Banner Long Description</label>
                                <div class="form-line">
                                    <textarea type="text" class="form-control" name="long_desc" ></textarea>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Banner</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/banners')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
						
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Banner Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control " name="name" id="edit_name" required>
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Image</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="image" id="edit_image">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Banner Short Description</label>
                                <div class="form-line">
                                    <textarea type="text" class="form-control" name="short_desc" id="short_desc" ></textarea>
                                </div>
                            </div>  
                         
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Banner Long Description</label>
                                <div class="form-line">
                                    <textarea type="text" class="form-control" name="long_desc" id="long_desc" ></textarea>
                                </div>
                            </div>  
                         
                            <div class="d-none form-group form-float float-left col-md-6">
                                <label class="form-label">Is Link</label>
                                <div class="form-line">
                                    <select class="form-control" name="is_link" id="edit_is_link"> 
                                      <option value="YES">YES</option>
                                      <option value="NO" selected>NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none ">
                                <label class="form-label">Link To</label>
                                <div class="form-line">
                                    <select class="form-control" name="link_level" id="edit_link_level"  onchange="loadcatprod(this)">
                                      <option value="0">Select</option>
                                      <option value="1">Categories</option>
                                      <option value="2">Products</option>
                                    </select>
                                </div>
                            </div>
                            <div class=" form-group form-float float-left col-md-6 category_id d-none">
                                <label class="form-label">Categories</label>
                                <div class="form-line">
                                    <input type="hidden" name="link_id_value" id="link_id_value">
                                    <select class="form-control link_id" name="category_id" id="edit_category_id">
                                        <option value="">Select</option>
                                        @if(!empty($categories))
                                            @foreach($categories as $cat)
                                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6 product_id d-none">
                                <label class="form-label">Products</label>
                                <div class="form-line">
                                    <select class="form-control link_id" name="product_id" id="edit_product_id">
                                        <option value="0">Select</option>
                                        @if(!empty($products))
                                            @foreach($products as $pro)
                                                <option value="{{$pro->id}}">{{$pro->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6 d-none ">
                                <label class="form-label">Type</label>
                                <div class="form-line">
                                    <select class="form-control" name="type"  id="edit_type">
                                        <option value="MENU_BANNER">Home page</option>
                                        <option value="TOP_BANNER">Categories page</option>
										<!--     <option value="TOP_BANNER">TOP_BANNER</option>
										<option value="OFFER_BANNER">OFFER_BANNER</option>
										<option value="FEATURE_BANNER">FEATURE_BANNER</option>
										<option value="SUBSCRIPTION_BANNER">SUBSCRIPTION_BANNER</option>
										<option value="BRAND_BANNER">BRAND_BANNER</option>
										<option value="FIRST">FIRST</option>
										<option value="MIDDLE">MIDDLE</option>
										<option value="LAST">LAST</option>-->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Display Position</label>
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
                                    <img src="" id="is_banner_image" height="100" width="100">
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

        $('#addbanner').on('click', function() {
            $('#style-form')[0].reset();
            $('#link_id_value').val('');
            $('.category_id').addClass('d-none');
            $('.product_id').addClass('d-none');
            //$('.link_id').html('');
            //$('#edit_link_level').trigger('onchange');

        });

        $(function() {
       
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("banners.data")}}',
                },
                columns: [ 
                    { data: 'name',  name: 'name'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.image != '' || data.image != null){
                                var tid = data.is_image;
                                return '<img src="'+tid+'" height="50" width="50">';
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

                            var tid = data.id;
                           
                            return '<a href="#" onclick="loadBanner('+tid+')" title="Edit Banner"><i class="fas fa-edit"></i></a><a  class="ml-2" style="cursor:pointer" onclick="deletedata('+tid+')" title="Delete banner"><i class="fa fa-trash"></i></a>';
                            
                        },

                    },
                ],
                 "order": [],
                "columnDefs": [
				
                {
                      "targets": 'no-sort',
                      "orderable": false,
                }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                var title = $(this).text();
                if(index != 1 && index != 4)
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
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


        function loadBanner(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/banners')}}",
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
                $('#link_id_value').val('');
                $('#id').val(response.data.id); 
                $('#short_desc').val(response.data.short_desc); 
				 $('#long_desc').val(response.data.long_desc); 
				  $('#edit_name').val(response.data.name); 
                $('#edit_status').val(response.data.status);
                $('#is_banner_image').attr('src', response.data.is_image);
                $('#edit_is_link').val(response.data.is_link);
                $('#edit_link_level').val(response.data.link_level);
                $('#link_id_value').val(response.data.link_id);
                $('#edit_link_id').val(response.data.link_id);
                $('#edit_position').val(response.data.position);
                $('#edit_type').val(response.data.type);
				$('#edit_vendor_id').val(response.data.vendor_id); 
                if(response.data.link_level > 0) {
                    //$('#edit_link_level').trigger('onchange');
                } 
                $('#edit_link_level').trigger('onchange'); 
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadLevels($obj) {
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/loadLevels')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    link_level:$($obj).val(),
                    link_id:$('#link_id_value').val(),
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                if(response.status == 'SUCCESS')
                    $('.link_id').html(response.data);
                else 
                    $('.link_id').html('');
            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }
		function deletedata(id){
           
			swal({
					title: "Do you want to delete this from your Banner list?",
					text: "",
					type: "warning",
					showCancelButton: true,
					confirmButtonClass: "btn-info",
					cancelButtonColor: "btn-danger",
					confirmButtonText: "Yes!",
					cancelButtonText: "No",
					closeOnConfirm: false,
					closeOnCancel: false
					
					
				   
            },function(inputValue){
                 if(inputValue===false) {
					  swal('Success',"Data has been saved",'success');
					  
					  $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
                  }else{
						var request = $.ajax({
						type: 'post',
						url: " {{URL::to('admin/delete/deletelist')}}",
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						data:{
							code:id,type:"banner",
						},
						dataType:'json',
						encode: true
					});
				 request.done(function (response) {
					 $('.tblcountries').DataTable().ajax.reload();
					 swal('Success',response.message,'success');
				 });
        
					request.fail(function (jqXHR, textStatus) {

					swal("Oops!", "Sorry,Could not process your request", "error");
				});
					

					
			    	}
               });

            
        }

        function loadcatprod(obj) {
            $('.category_id').addClass('d-none');
            $('.product_id').addClass('d-none');
            var link_level = $(obj).val(); 
            if(link_level == "1") {  // cat
                $('.category_id').removeClass('d-none');
                $('.product_id').addClass('d-none');
            }   else if(link_level == "2") {  // prod
                $('.category_id').addClass('d-none');
                $('.product_id').removeClass('d-none');
            }
        }
    </script>

@endsection
