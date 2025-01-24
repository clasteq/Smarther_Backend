@extends('layouts.teacher_master')
@section('mastersettings', 'active')
@section('master_circulars', 'active')
{{-- @section('menuopenm', 'active menu-is-opening menu-open') --}}
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Circulars', 'active'=>'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Circulars
                    <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn" class="btn btn-primary" style="float: right;">Add</button></a>
                  </h4>
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
                        </select>
                    </div>
                </div>
                <div class=" col-md-3">
                    <label class="form-label" >Status </label>
                    <div class="form-line">
                        <select class="form-control" name="status_id" id="status_id" >
                            <option value="">All</option>
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
                        </select>
                        </select>
                    </div>
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
                                    <th>Action</th>
                                  <th>Title</th>
                                  <th>Classes</th>
                                  <th>Date</th>
                                  <th>Image</th>
                                  <th>Status</th>
                                  <th>Approve Status</th>
                                 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
                                      <th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Circulars</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/teacher/save/circulars')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="circular_title" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Message</label>
                                <div class="form-line">
                                    <textarea type="text" class="form-control" name="circular_message" required></textarea>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Classes</label>
                                <div class="form-line">
                                    <select class="form-control" name="class_ids[]" multiple required>
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
                                <label class="form-label">Date</label>
                                <div class="form-line">
                                    <input class="date_range_filter date" name="circular_date" type="text" id="datepicker_from" autocomplete="off" value="" required/>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Image (jpg, jpeg, png)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="circular_image" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Attachments (mp3, mp4)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="circular_attachments">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Circular</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/teacher/save/circulars')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Title</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="circular_title" id="edit_circular_title" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Message</label>
                                <div class="form-line">
                                    <textarea type="text" class="form-control" name="circular_message" id="edit_circular_message" required></textarea>
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
                                <label class="form-label">Date</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="circular_date" id="edit_circular_date" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Image (jpg, jpeg, png)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="circular_image">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Attachments (mp3, mp4)</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="circular_attachments">
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
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <img src="" id="img_circular_img" height="100" width="100">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 imgca">
                                <div class="form-line">
                                    <a href="" target="_blank" id="img_circular_attach">view attachment</a>
                                    <audio controls style="display:none;">
                                        <source src="horse.ogg" type="audio/ogg"  class="img_circular_attach">
                                        <source src="horse.mp3" type="audio/mpeg"  class="img_circular_attach">
                                        Your browser does not support the audio element.
                                    </audio>
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
                    "url":"{{URL('/')}}/teacher/circulars/datatables/",   
                    data: function ( d ) {
                        var cls_id  = $('#cls_id').val();
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status,cls_id:cls_id});

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            var approve_status = data.approve_status;
                            if(approve_status != "APPROVED") {
                                return '<a href="#" onclick="loadCircular('+tid+')" title="Edit Circular"><i class="fas fa-edit"></i></a>';
                            }  else {
                                return '';
                            }
                        },

                    },
                    { data: 'circular_title',  name: 'circular_title'},
                    { data: 'is_classname',  name: 'is_classname'},
                    { data: 'circular_date',  name: 'circular_date'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.circular_image != '' || data.circular_image != null){
                                var tid = data.is_circular_image;
                                return '<img src="'+tid+'" height="50" width="50">';
                            }   else {
                                return '';
                            }
                        },

                    },
                    { data: 'status',  name: 'status'},
                    { data: 'approve_status',  name: 'approve_status'},
                  
                ],
                "order":[],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 2 },
                    { "orderable": false, "targets": 4 },
                        ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 5 && index != 4 && index != 2 && index != 0) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            });
            $('#status_id').on('change', function() {
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

            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left",
                startDate: new Date()

            });

            $("#edit_circular_date").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left",
                startDate: new Date()
            });

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

        function loadCircular(id){
            $('#edit-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/circulars')}}",
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
                $('#edit_circular_title').val(response.data.circular_title);
                $('#edit_circular_message').val(response.data.circular_message);
                $('#edit_circular_date').val(response.data.is_circular_date);
                if($.trim(response.data.class_ids) != '') {
                    var classids = response.data.class_ids;
                    classids = classids.split(',');
                    $("#edit_class_ids").val(classids);
                }
                $('#edit_status').val(response.data.status);
                $('#img_circular_img').attr('src', response.data.is_circular_image);
                $('.img_circular_attach').attr('src', response.data.is_circular_attachments);
                if(response.data.is_circular_attachments != '')  {
                    $('#img_circular_attach').attr('href', response.data.is_circular_attachments);
                    $('.imgca').css('display', 'block');
                } else {
                    $('#img_circular_attach').attr('href', '#');
                    $('.imgca').css('display', 'none');
                }
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
