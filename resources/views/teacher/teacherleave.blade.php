@extends('layouts.teacher_master')
@section('report_settings', 'active')
@section('master_teacherleave', 'active')
@section('menuopenr', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/teacher/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teacher Leave', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Teacher Leave
                       
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Apply Leave</button></a>
                                </h4>
                                <div class="row">
                                    <div class="row col-md-12">
                                        <div class="form-group col-md-3 " >
                                            <label class="form-label">From</label>
                                            <input class="date_range_filter date form-control" type="text" id="datepicker_from"  />
                                        </div>
                                        <div class="form-group col-md-3 " >
                                            <label class="form-label">To</label>
                                            <input class="date_range_filter date form-control" type="text" id="datepicker_to"  />
                                        </div>
                                     
                                   
                                 </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcategory">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                {{-- <th>S.no</th> --}}
                                                <th>Title</th>
                                                <th>Duration</th>
                                                <th>From Date</th>
                                                {{-- <th>To Date</th> --}}
                                                <th>Leave Reason</th>
                                                {{-- <th>Description File</th> --}}
                                                <th>Leave Apply File</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                {{-- <th></th> --}}
                                                {{-- <th></th> --}}
                                                <th></th>
                                                <th></th>
                                                {{-- <th></th> --}}
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
                    <h4 class="modal-title" id="smallModalLabel">Apply Leave</h4>
                </div>



                    <div class="panel-body">
                        <form id="style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/teacherleave') }}"
                        method="post">
                        {{ csrf_field() }}

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-6 float-left">
                                        <label class="control-label ">Leave Subject</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="title" id="title" value=""/>
                                        </div>
                                    </div>

                                    <div class="form-group  col-md-6 float-left">
                                        <label class="control-label ">Leave Duration</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" name="duration" id="duration">
                                                <option value="">Select Duration</option>
                                                <option value="Half Day - ForeNoon">Half Day - ForeNoon</option>
                                                <option value="Half Day - AfterNoon">Half Day - AfterNoon</option>
                                                <option value="One Day">One Day </option>
                                                <option value="More Than One Day">More Than One Day </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="leavefromdate" style="display:none;">
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label ">Leave From Date</label>
                                            <div class="col-lg-8">
                                                <div class='input-group date'>
                                                    <input type='date' class="form-control"  name="fromdate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="leavetodate" style="display:none;">
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label  ">Leave To Date</label>
                                            <div class="col-lg-8">
                                                <div class='input-group date'>
                                                    <input type='date' class="form-control" name="todate"/>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row" style="padding-left: 10px;">
                                        <div class="form-group  col-md-6 float-left">
                                            <label class="control-label  ">Leave Details Type</label>
                                            <div class=" ">
                                                <input type="radio" name="leaveType" class="leaveType" value="Text" checked /> Text &nbsp;&nbsp;
                                                <input type="radio" name="leaveType" class="leaveType" value="Audio"/> Audio
                                            </div>
                                        </div>

                                    </div>

                                    <div class="leavetext"  >
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label  ">Leave Reason</label>
                                            <div class="col-lg-8">
                                                <textarea name="description" id="description" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="leaveaudio" style="display:none;">
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label  ">Leave Reason</label>
                                            <div class="col-lg-8">
                                                <input type="file" class="form-control" name="descriptionfile" accept="audio/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 float-left">
                                        <label class="control-label ">Attachment</label>
                                        <div class="col-lg-8">
                                            <input type="file" class="form-control" name="LeaveApplyFile">
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
    </div>

    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Leave</h4>
                </div>



                    <div class="panel-body">
                        <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/teacherleave') }}"
                        method="post">
                        {{ csrf_field() }}
                            <input type="hidden" name="id" id="id">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-6 float-left">
                                        <label class="control-label ">Leave Subject</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="title" id="edit_title" value=""/>
                                        </div>
                                    </div>

                                    <div class="form-group  col-md-6 float-left">
                                        <label class="control-label ">Leave Duration</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" name="duration" id="edit_duration">
                                                <option value="">Select Duration</option>
                                                <option value="Half Day - ForeNoon">Half Day - ForeNoon</option>
                                                <option value="Half Day - AfterNoon">Half Day - AfterNoon</option>
                                                <option value="One Day">One Day </option>
                                                <option value="More Than One Day">More Than One Day </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="leavefromdate" style="display:none;">
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label ">Leave From Date</label>
                                            <div class="col-lg-8">
                                                <div class='input-group date'>
                                                    <input type='date' class="form-control"  name="fromdate" id="edit_fromdate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="leavetodate" style="display:none;">
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label  ">Leave To Date</label>
                                            <div class="col-lg-8">
                                                <div class='input-group date'>
                                                    <input type='date' class="form-control" name="todate" id="edit_todate" />

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row" style="padding-left: 10px;">
                                        <div class="form-group  col-md-6 float-left">
                                            <label class="control-label  ">Leave Details Type</label>
                                            <div class=" ">
                                                <input type="radio" name="leaveType" class="edit_leaveType" id="edit_leavetype_text" value="Text" checked /> Text &nbsp;&nbsp;
                                                <input type="radio" name="leaveType" class="edit_leaveType"  id="edit_leavetype_audio" value="Audio"/> Audio
                                            </div>
                                        </div>

                                    </div>

                                    <div class="leavetext"  >
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label  ">Leave Reason</label>
                                            <div class="col-lg-8">
                                                <textarea name="description" id="edit_description" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="leaveaudio" style="display:none;">
                                        <div class="form-group col-md-6 float-left">
                                            <label class="control-label  ">Leave Reason</label>
                                            <div class="col-lg-8">
                                                <input type="file" class="form-control" name="descriptionfile" accept="audio/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group form-float float-left col-md-6 d-none edit_descriptionfile">
                                        <label class="form-label">View Reason File</label>
                                        <div class="form-line">
                                            <a href="" name="edit_descriptionfile" id="edit_descriptionfile" target="_blank">View</a>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 float-left">
                                        <label class="control-label ">Attachment</label>
                                        <div class="col-lg-8">
                                            <input type="file" class="form-control" name="LeaveApplyFile">
                                        </div>
                                    </div>

                                    <div class="form-group form-float float-left col-md-6 d-none edit_leave_apply_file">
                                        <label class="form-label">View Attachment File</label>
                                        <div class="form-line">
                                            <a href="" name="leave_apply_file" id="edit_leave_apply_file" target="_blank">View</a>
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
    </div>

@endsection

@section('scripts')

    <script>


$(function() {

var table = $('.tblcategory').DataTable({
    processing: true,
    serverSide: true,
    responsive: false,
    "ajax": {
        "url":"{{URL('/')}}/teacher/tleave/datatables/",    
        data: function ( d ) {
                        var minDateFilter  = $('#datepicker_from').val();
                        var maxDateFilter  = $('#datepicker_to').val();
                        $.extend(d, {
                        minDateFilter:minDateFilter,
                        maxDateFilter:maxDateFilter
                       });

                    }
    },
    columns: [
        {
            data:null,
            "render": function ( data, type, row, meta ) {

                var tid = data.id;
                if(data.status == 'PENDING') {
                    return '<a href="#" onclick="loadLeave('+tid+')" title="Edit Leave"><i class="fas fa-edit"></i></a>';
                }   else {
                    return '';
                }
            },

        },
        // {
        //     data: 'id'
        // },
        {
            data: 'title'
        },
        {
            data: 'duration'
        },
        {
            data: 'is_from_date'
        },
        // {
        //     data: 'is_to_date'
        // },
        {
            data: 'description'
        },

        // {
        //     data: null,
        //     "render": function(data, type, row, meta) {

        //         var descriptionfile = data.descriptionfile;
        //         var is_descriptionfile = data.is_descriptionfile;
        //         if (descriptionfile != null && descriptionfile != '') {
        //             return '<a href="' + is_descriptionfile +
        //                 '" target="_blank" title="Description file" class="btn btn-info">View</a>';
        //         } else {
        //             return '';
        //         }
        //     },

        // },
        {
            data: null,
            "render": function(data, type, row, meta) {

                // var leave_apply_file = data.leave_apply_file;
                // var is_leave_apply_file = data.is_leave_apply_file;
                // if (leave_apply_file != null && leave_apply_file != '') {
                    return '<a href="' + data.is_leave_apply_file +
                        '" target="_blank" title="Leave Attachement" class="btn btn-info">View</a>';
                // } else {
                //     return '';
                // }
            },

        },
        {
            data: 'status'
        },

    ],
  
    "order": [],
    "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
        }

    ],
    dom: 'Blfrtip',
                buttons: [
                    {

                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-warning btn-md ml-3',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                "url":"{{URL('/')}}/teacher/tleavelist_excel/",  
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Teacher_Leave.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],

});

$('.tblcategory tfoot th').each(function(index) {

    if (index != 0) {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder="Search ' + title + '" />');
    }
});


$("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            $("#datepicker_to").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).change(function() {
                tabledraw();

            }).keyup(function() {
                tabledraw();
            });

            function tabledraw() {
                var minDateFilter  = $('#datepicker_from').val();
                var maxDateFilter  = $('#datepicker_to').val();
                if(new Date(maxDateFilter) < new Date(minDateFilter))
                {
                    alert('To Date must be greater than From Date');
                    return false;
                }
                table.draw();

            }



// Apply the search
table.columns().every(function() {
    var that = this;

    $('input', this.footer()).on('keyup change', function() {
        if (that.search() !== this.value) {
            that
                .search(this.value)
                .draw();
        }
    });
});
$('#addtopics').on('click', function() {
    $('#style-form .course_id').trigger('change');
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

            if (response.status == "SUCCESS") {

                swal('Success', response.message, 'success');

                $('.tblcategory').DataTable().ajax.reload();

                $('#smallModal').modal('hide');

                $("#style-form")[0].reset();

            } else if (response.status == "FAILED") {

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

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcategory').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                            $("#edit-style-form")[0].reset();

                        } else if (response.status == "FAILED") {

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
function loadLeave(id) {

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('teacher/edit/teacherleave') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                $('#id').val(response.data.id);
                $('#edit_title').val(response.data.title);
                $('#edit_duration').val(response.data.duration);
                $('#edit_duration').trigger('change');
                $('#edit_fromdate').val(response.data.is_from_date);
                $('#edit_todate').val(response.data.is_to_date);
                if(response.data.leave_type == 'audio') {
                    $('#edit_leavetype_audio').prop('checked', true);
                }   else {
                    $('#edit_leavetype_text').prop('checked', true);
                }
                $('.edit_leaveType').trigger('change');
                $('#edit_description').val(response.data.description);
                $('#edit_descriptionfile').val(response.data.descriptionfile);
                $('#edit_leave_apply_file').val(response.data.leave_apply_file);


                if (response.data.descriptionfile != '') {
                    $('#edit-style-form .edit_descriptionfile').removeClass('d-none');
                    $('#edit-style-form #edit_descriptionfile').attr('href', response.data.is_descriptionfile);
                    $('#edit-style-form #is_edit_descriptionfile').val(response.data.is_descriptionfile);
                }

                if (response.data.leave_apply_file != '') {
                    $('#edit-style-form .leave_apply_file').removeClass('d-none');
                    $('#edit-style-form #leave_apply_file').attr('href', response.data.is_leave_apply_file);
                    $('#edit-style-form #is_leave_apply_file').val(response.data.is_leave_apply_file);
                }

                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

$( function() {
        $( "#datepicker1").on( "change", function() {
            var d1 = $(this).val();
            var dt = new Date();
            var day = dt.getDate();
            var mon = dt.getMonth()+1;
            if(mon < 10){
                mon = "" + 0 + mon;
            }
            var yr = dt.getFullYear();
            var d2 = yr + '-' + mon + '-' + day;

            if(d1 == d2){
                var hr = dt.getHours();
                var min= dt.getMinutes();
                if(hr == 8 && min >30)
                {
                    alert("Sorry Time out. Not possible to apply for leave.");
                    $( "#datepicker1").val("");
                    return false;
                }
                else if(hr > 8)
                {
                    alert("Sorry Time out. Not possible to apply for leave.");
                    $( "#datepicker1").val("");
                    return false;
                }
            }
        });
        /*$( "#datepicker2").on( "change", function() {
         var d = $(this).val();
         }); */
    } );

        $(function() {
            $('#duration,#edit_duration').on('change', function() {
                var dura = $(this).val();
                if (dura == 'More Than One Day') {
                    $('.leavefromdate').show();
                    $('.leavetodate').show();
                } else {
                    $('.leavefromdate').show();
                    $('.leavetodate').hide();
                }
            });

            $('.leaveType,.edit_leaveType').change(function() {
                var $obj = $(this);
                var classname = $obj.attr('class');
                if(classname == 'leaveType') {
                    var form = "style-form";
                    var rType = $('input[class="leaveType"]:checked').val();
                }   else {
                    var form = "edit-style-form";
                    var rType = $('input[class="edit_leaveType"]:checked').val();
                }
                if (rType == 'Text') {
                    $('#'+form+ ' .leaveaudio').hide();
                    $('#'+form+ ' .leavetext').show();
                } else if (rType == 'Audio') {
                    $('#'+form+ ' .leavetext').hide();
                    $('#'+form+ ' .leaveaudio').show();
                }
            });
        });
    </script>

@endsection
