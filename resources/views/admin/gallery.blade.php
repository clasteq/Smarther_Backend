@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_gallery', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$user_type = Auth::User()->user_type;
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Events', 'active'=>'active']];
?> 
<style> 

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
                  <h4 class="card-title" style="font-size: 20px;"><!-- Events -->
                    <div class="row col-md-12">
                        <div class="form-group col-md-3 " >
                            <label class="form-label">From</label>
                            <input class="date_range_filter date form-control" type="text" id="from_date" />
                        </div>
                        <div class="form-group col-md-3 " >
                            <label class="form-label">To</label>
                            <input class="date_range_filter date form-control" type="text" id="to_date"  />
                        </div> 
                        <div class="form-group col-md-3 " >
                            <label class="form-label mr-1">Status</label>
                            <select class="form-control" name="status_id" id="status_id">
                                <option value="" >All</option>
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div> 
                        <div class="form-inline col-md-3 float-right " >
                        @if((isset($session_module['Gallery']) && ($session_module['Gallery']['add'] == 1)) || ($user_type == 'SCHOOL'))
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
                                  <th>Title</th> 
                                  <th>Status</th>  
                                  <th>Action</th>
                                  
                                </tr>
                              </thead>
                              <!-- <tfoot>
                                  <tr><th></th><th></th><th></th> 
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
                    <h4 class="modal-title" id="smallModalLabel">Add Gallery</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/gallery')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="gallery_title" required>
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Images (Hold Ctrl and select multiple images)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" multiple="multiple" name="gallery_image[]" required>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Gallery</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/gallery')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="gallery_title" id="edit_gallery_title" required>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Images (Hold Ctrl and select multiple images)</label>
                                <div class="form-line">
                                    <input type="file" multiple class="form-control" name="gallery_image[]">
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
                            <div class="form-group form-float float-left col-md-12">
                                <div class="form-line gallery_images" id="gallery_images">

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
                    "url":"{{URL('/')}}/admin/gallery/datatables/", 
                    data: function ( d ) { 
                        var status  = $('#status_id').val(); 
                        var minDateFilter  = $('#from_date').val();
                        var maxDateFilter  = $('#to_date').val();  
                        $.extend(d, {status:status, minDateFilter:minDateFilter,maxDateFilter:maxDateFilter});

                    }
                },
                columns: [
                    { data: 'gallery_title',  name: 'gallery_title'},
                    { data: 'status',  name: 'status'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadGallery('+tid+')" title="Edit Gallery"><i class="fas fa-edit"></i></a>';
                        },

                    },  
                   
                ],
                "order":[[0, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": 2 }
                ]

            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if(index != 0 && index != 2 && index != 4  && index != 6) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );*/

            $('#status_id').on('change', function() {
                table.draw();
            }); 

            $("#from_date").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left", 
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            $("#to_date").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left", 
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            function tabledraw() {
                var minDateFilter  = $('#from_date').val();
                var maxDateFilter  = $('#to_date').val();
                if(new Date(maxDateFilter) < new Date(minDateFilter))
                {
                    alert('To Date must be greater than From Date');
                    return false;
                }
                table.draw();

            }

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
            } ); 
            */
            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left",
                startDate : new Date()
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

                        if (response.status == 1) {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                        }
                        else if (response.status == 0) {

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

                        if (response.status == 1) {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == 0) {

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

        function loadGallery(id){
            $('#edit-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/gallery')}}",
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
                $('#edit_gallery_title').val(response.data.gallery_title); 
                $('#edit_status').val(response.data.status);  

                var attach = '';
                $( response.data.is_gallery_images ).each(function( index, value ) {
                    attach += '<div class="form-group form-float float-left col-md-4 id="img_'+index+'"><span class="image img_'+index+'" onclick="Removecircularimage(\''+value.images+'\', '+response.data.id+', '+index+');"><i class="btn-delete fas fa-trash float-right"></i><img src="'+value.images+'" class="" height="200" width="200"></span></div>';
                  
                });
                $('.gallery_images').html(attach);

                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        } 
         
        function Removecircularimage(imgid, galleryid, imgname){

            swal({
                title: "Do you want to delete the selected image?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                cancelButtonColor: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: true

            },function(inputValue){
                if(inputValue===false) {
                      swal("closed");
                      $( ".confirm" ).trigger( "click" );

                     }else{

                        var request = $.ajax({
                            type: 'post',
                            url: "{!! url('admin/delgalleryimage') !!}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                imgid:imgid,galleryid:galleryid,
                            },
                            dataType:'json',
                            encode: true
                        });
                     request.done(function (response) {

                        if(response.status == 1){
                            swal('Success',"Successfully Removed",'success');
                            $('.img_'+imgname).text('');
                        }
                        else if(response.status == 0){
                            swal("Oops!", response.message, "error");
                        }

                     });

                        request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });

                }
            });

        } 
    </script>

@endsection
