@extends('layouts.admin_master')
@section('academic_settings', 'active')
@section('master_terms', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Terms', 'active'=>'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('public/css/select2.min.css') }}"> 
    <style type="text/css">
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

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #006fe6;
            color: #fff;
            padding: 0 10px;
            margin-top: .31rem;
        }
    </style>
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Terms
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
                            <option value="ACTIVE" selected>ACTIVE</option>
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
                                  <th>Term</th>
                                  <th>Classes</th>
                                  <th>Status</th>
                                  
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Terms</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/terms')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Term</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="term_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Classes</label>
                                <div class="form-line">
                                    <select class="form-control select2" name="class_ids[]" id="class_ids" multiple required >
                                        <!-- <option value="">Select Classes</option> -->
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Term</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/terms')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Term</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="term_name" id="term_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Classes</label>
                                <div class="form-line">
                                    <select class="form-control select2" name="class_ids[]" multiple id="edit_class_ids" required>
                                        <!-- <option value="">Select Classes</option> -->
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
                                    <select class="form-control" name="status" id="status" required>
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
        $('#addbtn').on('click', function () {
            $('#style-form')[0].reset();
            $("#class_ids").val('').select2();
        });
        $(function() {
            $("#class_ids").select2();
            $("#edit_class_ids").select2();

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/terms/datatables/",   
                    data: function ( d ) {
                        var cls_id  = $('#cls_id').val();
                        var status  = $('#status_id').val();
                        $.extend(d, {cls_id:cls_id,status:status});

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadTerm('+tid+')" title="Edit Term"><i class="fas fa-edit"></i></a>';
                        },

                    },
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'is_classname',  name: 'is_classname'},
                    { data: 'status',  name: 'status'},
                   
                ],
                "order": [],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 3 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if( index != 3 && index != 2 && index != 0 ) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );
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

        function loadTerm(id){
            $('#edit-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/terms')}}",
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
                $('#term_name').val(response.data.term_name);
                if($.trim(response.data.class_ids) != '') {
                    var classids = response.data.class_ids;
                    classids = classids.split(',');
                    $("#edit_class_ids").val(classids).select2();
                }
                $('#status').val(response.data.status);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
