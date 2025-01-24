@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_posts', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
<?php $user_type = Auth::User()->user_type;
$session_module = session()->get('module');
?>
@section('content')
@if((isset($session_module['Posts']) && ($session_module['Posts']['view'] == 1)) || ($user_type == 'SCHOOL'))
<meta name="csrf-token" content="{{ csrf_token() }}">

<style type="text/css">
	.activityimage img {
        width: 70px;
        height: auto; /*200px;*/
        border-radius: 3%;
    }
</style>
<section class="content">
    <!-- Exportable Table -->
    <div class="content container-fluid">

        <div class="panel"> 
            <div class="panel-body"> 
		        <div class="row">

		            <div class="col-xs-12 col-md-12">
		        
			            <div class="card">
			                <div class="card-header">Post Status</div>  
			                @php($id = $post['id'])
			                <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
				                <div class="d-flex activity activityimage">
				                    <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle">
				                    <p class="mt-2 ms-3"><b>{{$post['post_category']}} </b><br> {{$post['posted_user']['name_code']}} <br> 
				                    Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p>  
				                </div>
				                <div class="activitycontent mt-3">
				                    <p>{{$post['title']}}</p>
				                </div>  
				                <?php $img = $post['post_theme']['is_image']; ?>
				                <div class="activitycontent offerolympia" >
				                    <p>{!! $post['message'] !!}</p>
				                </div>  
				                <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
				                    <div class=" ">
				                       <div class="likeact float-left" id="likeact_{{$id}}" >  
				                       	@if(!empty($post_receivers))
				                       		@foreach($post_receivers as $receivers)
				                         <p class="btn btn-info">{{$receivers->name}}  {{$receivers->name1}}</p> 
				                        	@endforeach
				                        @endif 
				                        </div>  
				                        <p class=" float-right">{{$post['is_created_ago']}}</p>

				                    </div> 
				                </div>
				            </div>


			                <div class="card-content collapse show">
			                  	<div class="card-body card-dashboard">
			                    	<div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
				                        <div class="table-responsicve">
				                            <table class="table table-striped table-bordered tblcountries">
				                              <thead>
				                                <tr> 
				                                  <th>Name</th> 
				                                  <th>Read Date</th>  
				                                  <th>Sent Date</th>
				                                  <th>Acknowledged Status</th>
				                                  <th>App Installed</th>
				                                  <th>Mobile</th> 
				                                  <th>Class</th> 
				                                  <th>Section</th>  
				                                </tr>
				                              </thead>
				                              <tfoot>
				                                  <tr><th></th><th></th><th></th>
				                                      <th></th><th></th><th></th>
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
			</div>
		</div>
	</div>
</section>

@else 
@include('admin.notavailable') 
@endif

@endsection

@section('scripts')

    <script>

    	$(function() {
       
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/poststatus/datatables?id={{$post['id']}}",   
                },
                columns: [ 
                    { data: 'name',  name: 'users.name'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.notify != '' && data.notify != null && data.notify.read_status != null){
                                var tid = data.notify.read_status;
                                var tdate = data.notify.read_date;
                                if(tid == 1)
                                	return tdate;
                                else 
                            		return '-';
                            }   else {
                                return '-';
                            }
                        },

                    }, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.notify != '' && data.notify != null && data.notify.created_at != null){
                                var tid = data.notify.created_at; 
                        		return tid;
                            }   else {
                                return '-';
                            }
                        },
                        name: 'notifications.created_at'
                    }, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.notify != '' && data.notify != null && data.notify.is_acknowledged != null){
                                var tid = data.notify.is_acknowledged; 
                        		if(tid == 1)
                                	return 'Acknowledged';
                                else 
                            		return 'Not Acknowledged';
                            }   else {
                                return '-';
                            }
                        },

                    }, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.is_app_installed == 1){ 
                            	return 'Installed'; 
                            }   else {
                                return 'Not Installed';
                            }
                        },

                    }, 
                    { data: 'mobile',  name: 'users.mobile'}, 
                    { data: 'class_name',  name: 'classes.class_name'}, 
                    { data: 'section_name',  name: 'sections.section_name'}, 
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
                if(index == 0)
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

        } );

    </script>

@endsection
