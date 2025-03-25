@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_categories', 'active')
@section('menuopencomn', 'active menu-is-opening menu-open') 
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Categories', 'active'=>'active']];
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
        table.dataTable tbody td {
          vertical-align: middle;
        }
</style>
@if((isset($session_module['Category']) && ($session_module['Category']['list'] == 1)) || ($user_type == 'SCHOOL'))
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title"> <!-- Categories -->
                        <div class="row col-md-12">
                            <div class="form-inline col-md-3 " >
                                <label class="form-label mr-1">Status: </label>
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="" >All</option>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                            </div>
                            <div class="form-inline col-md-8 float-right " ></div>
                            <div class="form-inline col-md-1 float-right " >
                            @if((isset($session_module['Category']) && ($session_module['Category']['add'] == 1)) || ($user_type == 'SCHOOL'))
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary float-right" id="addbtn" style="float: right;">Add</button></a>
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
                                  <th>Category Name</th>
                                  <th>Background Theme</th>
                                  <th>Text Color</th>
                                  <th>Position</th>
                                  <th>Status</th>
                                  <th>Action</th>

                                </tr>
                              </thead>
                              <!-- <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/categories')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Category Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required minlength="3" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-group form-float float-left col-md-12">
                                    <label class="form-label">Background Theme</label>
                                    <div class="form-line">
                                        <select class="form-control  col-md-8 float-left mr-2" name="background_theme_id" required onchange="previeewtheme(this);">
                                            <option value="" data-image="">Select BG Theme</option>
                                            @if(!empty($bgthemes))
                                                @foreach($bgthemes as $themes)
                                                    <option value="{{$themes->id}}" data-image="{{$themes->is_image}}">{{$themes->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <img class="is_image" src="" height="50" width="50" style="display: none;">
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Text Color</label>
                                <div class="form-line">
                                    <input type="color" class="form-control" name="text_color">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/categories')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Category Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="name" id="edit_name" required minlength="3" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-group form-float float-left col-md-12">
                                    <label class="form-label">Background Theme</label>
                                    <div class="form-line">
                                        <select class="form-control  col-md-8 float-left mr-2" name="background_theme_id" id="edit_background_theme_id" required onchange="previeewtheme(this);">
                                            <option value="" data-image="">Select BG Theme</option>
                                            @if(!empty($bgthemes))
                                                @foreach($bgthemes as $themes)
                                                    <option value="{{$themes->id}}" data-image="{{$themes->is_image}}">{{$themes->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-2">
                                    <img class="is_image" src="" height="50" width="50" style="display: none;">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Text Color</label>
                                <div class="form-line">
                                    <input type="color" class="form-control" name="text_color"id="edit_text_color" >
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

        function previeewtheme($obj) {
            var bg_color = $($obj).find(':selected').data('image');
            if(bg_color != '' && bg_color != null) {
                $('.is_image').attr('src', bg_color);
                $('.is_image').css('display', 'block');
            } else {
                $('.is_image').css('display', 'none');
            }
        }

        $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
            });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/categories/datatables/", 
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
                            
                            var tid = data.is_background_theme;
                            if(tid != '' && tid != null) {
                                return ' <img src="'+tid.is_image+'" class="bgtheme mr-2" title="Backgrond Theme" height="50" width="50">' + tid.name;
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            var text_color = $.trim(data.text_color);
                            if (text_color != '' || text_color != null || text_color != "null") { 
                                var bg = data.is_background_theme;
                                var bgt = '';
                                if(bg != '' && bg != null) {
                                    bgt = "background-image:url('"+bg.is_image+"'); background-size: cover;  background-repeat: no-repeat;";
                                } 
                                return '<span style="padding:2%; color:'+text_color+';'+bgt+'"> Preview </span>';
                            } else {
                                return '';
                            }
                        },
                        name: 'text_color'
                    },
                    { data: 'position',  name: 'position'},
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            var urls = ''
                            @if((isset($session_module['Category']) && ($session_module['Category']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                            var tid = data.id;
                            urls += '<a href="#" onclick="loadCategory('+tid+')" title="Edit Category"><i class="fas fa-edit"></i></a>';
                            @endif
                            @if((isset($session_module['Category']) && ($session_module['Category']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                            var tid = data.id;
                            urls += ' <a href="#" onclick="deletedata(' + tid +')" title="Delete Category"><i class="fas fa-trash"></i></a>';
                            @endif

                            return urls; 
                        },

                    },

                ],
                "order":[[3, 'asc']],
                "columnDefs": [ { "orderable": false, "targets": 4 } ,  { "orderable": false, "targets": 5 }],
               
            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if( index != 2 && index != 3) {
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

        function loadCategory(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/admin/edit/categories')}}",
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
                $('#edit_background_theme_id').val(response.data.background_theme_id);
                $('#edit_status').val(response.data.status);
                $('#edit_position').val(response.data.position);
                $('#edit_text_color').val(response.data.text_color);
                previeewtheme($('#edit_background_theme_id'));
                
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
                        url: " {{URL::to('/admin/delete/categories')}}",
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
