@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fees_structure', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Fee Structure', 'active' => 'active']];
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
                        <h4 style="font-size:20px;" class="card-title">Fee Structure
                            <a href="{{ URL('/admin/fee_structure/add') }}"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries" id="example1">
                                        <thead>
                                            <tr>
                                                <th>Batch</th>
                                                <th>Term</th>
                                                <th>Category Name</th>
                                                <th>Item Name</th>
                                                <th>For</th>
                                                <th></th>
                                                <th>Gender</th>
                                                <th>Amount</th>
                                                <th>Fee Type</th>
                                                <th class="nowrap">Due Date</th> 
                                                <th class="no-sort nowrap"></th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th>
                                                <th></th><th></th><th></th>
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
    </section>
    
    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Fee Struture Item</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/edit/fee_structure_item') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">

                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Concession Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="concession_name"
                                        id="edit_concession_name" required minlength="1" maxlength="200">
                                </div>
                            </div> --}}
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Amount</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="amount" id="edit_amount"
                                        required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Due Date</label>
                                <div class="form-line">
                                    <input type="text" name="due_date" id="edit_due_date" class="form-control datetime-picker" placeholder="Select Date and Time" required style="background-color: white;">
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
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.datetime-picker', {
                enableTime: false   ,
                dateFormat: "Y-m-d",
                // Add more options as needed
            });
        });
    </script>

    <script src="{{asset('public/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() { 
            $('.select2').select2(); 
        });
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });

        

        $(function() {

                var table = $('#example1').DataTable({

                    processing: false,
                    serverSide: true,
                    responsive: true,
                    "lengthChange": false,
                    "ajax": {
                        "url": "{{URL('/')}}/admin/fee_structure_list/datatables/", 
                    },
                    columns: [  
                       
                        { data: 'batch'},
                        { data: 'term_name'},
                        { data: 'name'},
                        { data: 'item_name'},
                        {
                            data: null,
                            "render": function(data, type, row, meta) {

                                var fee_post_type = data.fee_post_type;
                                var feetype = '';
                                if(fee_post_type == 1) {
                                    feetype = 'Class';
                                } else if(fee_post_type == 2) {
                                    feetype = 'Sections';
                                } else if(fee_post_type == 3) {
                                    feetype = 'All';
                                } else if(fee_post_type == 4) {
                                    feetype = 'Group';
                                } else if(fee_post_type == 5) {
                                    feetype = 'Scholar';
                                } else {
                                    feetype = '';
                                }

                                return feetype;
                            },

                        }, 
                        {
                            data: null,
                            "render": function(data, type, row, meta) {
                                var ids = '';
                                var is_receivers = data.is_receivers; 
                                if(is_receivers != '' && is_receivers != null)  {
                                    $.each(is_receivers, function(key, value) { 
                                        ids += value.name1 +' '+value.name+', ';
                                    });
                                }

                                return ids;
                            },

                        }, 
                        {
                            data: null,
                            "render": function(data, type, row, meta) {

                                var gender = data.gender; 
                                if(gender == 1) {
                                    gender = 'All';
                                } else if(gender == 2) {
                                    gender = 'Boys';
                                } else if(gender == 3) {
                                    gender = 'Girls';
                                } else {
                                    gender = '';
                                }

                                return gender;
                            },

                        }, 
                        { data: null,
                            "render": function(data, type, row, meta) {
                                if(data.cancel_status == 0) { 
                                    return data.amount;
                                } else {
                                    return '<span style="color:red;">'+data.amount+'</span>';
                                }
                            },
                        },
                        {
                            data: null,
                            "render": function(data, type, row, meta) {

                                var fee_type = data.fee_type; 
                                if(fee_type == 1) {
                                    fee_type = 'Mandatory';
                                } else if(fee_type == 2) {
                                    fee_type = 'Variable';
                                } else if(fee_type == 3) {
                                    fee_type = 'Optional';
                                } else {
                                    fee_type = '';
                                }

                                return fee_type;
                            },

                        }, 
                        { data: 'due_date'},      
                        {
                            data: null,
                            "render": function(data, type, row, meta) {
                                if(data.cancel_status == 0) {
                                    var tid = data.id;
                                    return '<a href="#" onclick="loadSection(' + tid +
                                        ')" title="Edit Section"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="#" onclick="deleteFeeStructure(' + tid + ')" title="Delete Fee Structure"><i class="fas fa-trash"></i></a>';
                                } else {
                                    return '';
                                }
                            },

                        },
                    ],
                    "order": [],
                    "columnDefs": [ { "targets": 'no-sort', "orderable": false, },  
                        { "targets": 'nowrap', "className": 'nowrap', },
                    ], 

                });
               

                  

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
            //   $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('/admin/edit/get_fee_structure_data') }}",
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
                $('#edit_amount').val(response.data.amount);
                $('#edit_due_date').val(response.data.due_date);
                $('#smallModal-2').modal('show');
            });
            request.fail(function(jqXHR, textStatus) {
                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        
        function deleteFeeStructure(id){
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
                        url: " {{URL::to('/admin/delete/fee_structure_data')}}",
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
                        if (response.status == "SUCCESS") {
                            
                            swal('Success',response.message,'success');

                            $('#example1').DataTable().ajax.reload();
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
