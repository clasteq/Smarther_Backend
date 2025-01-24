@extends('layouts.admin_master')
@section('fees_settings', 'active')
@section('master_receipthead', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Receipt Head', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('public/css/select2.min.css') }}"> 
<style>
    .dropdown-menu.show {
        display: block;
        width: 100%;
        top: 30px !important;
        left: auto !important;
        padding: 20px;
    }
    .checkbox input[type="checkbox"] {
            width: 20px !important;
            
    }
     .select2-container--default .select2-selection--single {
        height: 45px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-top: 8px; 
    }
    .select2-selection__choice {
        color: #000 !important;
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
        .vanilla-calendar {
            width: 100% !important;
        }
    }
</style>
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Receipt Head
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class="row">
                            <div class="row col-md-12">
                             <div class="form-group col-md-3 " >
                                 <label class="form-label">Status</label>
                                 <select class="form-control" name="status_id" id="status_id">
                                     <option value="" >All</option>
                                     <option value="ACTIVE" selected>ACTIVE</option>
                                     <option value="INACTIVE">INACTIVE</option>
                                 </select>
                             </div>
                             <div class=" col-md-3 d-none">
                                <label class="form-label" >Subjects </label>
                                <div class="form-line">
                                    <select class="form-control" name="subject_id" id="subject_id" >
                                        <option value="">Select Subject</option>
                                        @if (!empty($subject))
                                            @foreach ($subject as $subjects)
                                                <option value="{{ $subjects->id }}">
                                                    {{ $subjects->subject_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    </select>
                                </div>
                            </div>
                         </div>
        
                     </div>

                    
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries" id="example1">
                                        <thead>
                                            <tr>
                                                <th>Receipt Name</th>
                                                <th>Starting Number</th>
                                                <!-- <th>Subjects</th> -->
                                                <th>No Prefix</th>
                                                <th>No Suffix</th>
                                                <th>Padding</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                                
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <!-- <th></th> -->
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Receipt Head</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/receipt_head') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Receipt Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="receipt_name" required minlength="1"
                                        maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Starting Number <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="starting_number" required minlength="1"
                                        maxlength="5">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Number Prefix</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="no_prefix" minlength="1"
                                        maxlength="5">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Number Suffix</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="no_suffix" minlength="1"
                                        maxlength="5">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Padding Digit <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="padding_digit" required minlength="1"
                                        maxlength="8">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Sections</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/receipt_head') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                           
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Receipt Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="receipt_name"
                                        id="edit_receipt_name" required minlength="1" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Starting Number <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="edit_starting_number" name="starting_number" required minlength="1"
                                        maxlength="5">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Number Prefix</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="edit_no_prefix" name="no_prefix" minlength="1"
                                        maxlength="5">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Number Suffix</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="edit_no_suffix" name="no_suffix" minlength="1"
                                        maxlength="5">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Padding Digit <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control" id="edit_padding_digit" name="padding_digit" required minlength="1"
                                        maxlength="8">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position"
                                        required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
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
@endsection

@section('scripts')
    <script src="{{asset('public/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() { 
            $('.select2').select2(); 
        });
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });

        
        // $(function() {
        //     var table = $('.tblcountries').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         responsive: false,
        //         "ajax": {
        //             "url":"{{URL('/')}}/admin/sections/datatables/", 
        //             data: function ( d ) {
        //                 var subject  = $('#subject_id').val();
        //                 var status = $('#status_id').val();
        //                 $.extend(d, {subject:subject,status:status});

        //             }
        //         },
        //         columns: [
        //             {
        //                 data: null,
        //                 "render": function(data, type, row, meta) {

        //                     var tid = data.id;
        //                     return '<a href="#" onclick="loadSection(' + tid +
        //                         ')" title="Edit Section"><i class="fas fa-edit"></i></a>';
        //                 },

        //             },
        //             {
        //                 data: 'class_name',
        //                 name: 'classes.class_name'
        //             },
        //             {
        //                 data: 'section_name',
        //                 name: 'sections.section_name'
        //             },
        //             /*{
        //                 data: 'is_subject_name',
        //                 name: 'sections.is_subject_name'
        //             },*/
        //             {
        //                 data: 'position',
        //                 name: 'sections.position'
        //             },
        //             {
        //                 data: 'status',
        //                 name: 'sections.status'
        //             },
                  
        //         ],
        //         "order":[[1, 'asc']],
        //         "columnDefs": [
        //             { "orderable": false, "targets": 0 }
        //         ],
              
        //     });

        //     $('.tblcountries tfoot th').each(function(index) {
        //         if (index != 0 && index != 4) {
        //             var title = $(this).text();
        //             $(this).html('<input type="text" placeholder="Search' + title + '" />');
        //         }
        //     });

        //     $('#subject_id').on('change', function() {
        //         table.draw();
        //     });

        //     $('#status_id').on('change', function() {
        //         table.draw();
        //     });
        //     // Apply the search
        //     table.columns().every(function() {
        //         var that = this;

        //         $('input', this.footer()).on('keyup change', function() {
        //             if (that.search() !== this.value) {
        //                 that.search(this.value).draw();
        //             }
        //         });
        //     });
      
        // });

        $(function() {

                var table = $('#example1').DataTable({

                    processing: false,
                    serverSide: true,
                    responsive: true,
                    "lengthChange": false,
                    "ajax": {
                        "url": "{{URL('/')}}/admin/receipt_head_data/datatables/", 
                        data: function(data) {

                        // Include additional data if needed
                        data.status = $('#status_id').val();
                       

                        }
                    },
                    columns: [
                        { data: 'name'},
                        { data: 'starting_number'},
                        { data: 'no_prefix'},
                        { data: 'no_suffix'},
                        { data: 'padding_digit'},
                        { data: 'position'},
                        { data: 'status'},
                        {
                            data: null,
                            "render": function(data, type, row, meta) {

                                var tid = data.id;
                                return '<a href="#" onclick="loadSection(' + tid +
                                    ')" title="Edit Receipt Head"><i class="fas fa-edit"></i></a> <a href="#" onclick="deletedata(' + tid +
                                    ')" title="Delete Receipt Head"><i class="fas fa-trash"></i></a>';
                            },

                        },
                       
                    ],
                    "order": [],
                    "columnDefs": [

                    {
                        "targets": 'no-sort',
                        "orderable": false,
                    }
                    ],
                    //  dom: 'Bfrtip',
                    //  "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

                });
                $('#example1 tfoot').insertAfter('#example1 thead');
                $('#example1 tfoot th').each( function () {
                        var title = $(this).text();

                        if(($(this).index() != 6)&& ($(this).index() != 7) ){
                            $(this).html( '<input class="btn" type="text" style="width:100%;border-color:#6c757d; cursor: auto;" placeholder="Search '+title+'" />' );

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

                    $('#status_id').on('change', function () {
                            table.draw();
                    });


            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('#example1').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });

            $('#edit_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('#example1').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });

            });

        function loadSection(id) {
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('/admin/edit/receipt_head') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    code: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {

                $('#id').val(response.data.id);
               
                $('#edit_receipt_name').val(response.data.name);
                $('#edit_starting_number').val(response.data.starting_number);
                $('#edit_no_prefix').val(response.data.no_prefix);
                $('#edit_no_suffix').val(response.data.no_suffix);
                $('#edit_padding_digit').val(response.data.padding_digit);
               
 
                $('#edit_status').val(response.data.status);
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

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
                        url: " {{URL::to('/admin/delete/receipt_head')}}",
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

                            $('#example1').DataTable().ajax.reload();
                        }
                        else{
                            swal('Oops',response.message,'error');

                        //   $('.tblcountries').DataTable().ajax.reload();
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
