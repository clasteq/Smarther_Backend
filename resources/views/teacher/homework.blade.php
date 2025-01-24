@extends('layouts.teacher_master')
@section('act_settings', 'active')
@section('master_homework', 'active')
@section('menuopenac', 'active menu-is-opening menu-open') 
<?php
$breadcrumb = [['url' => URL('/teacher/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Homeworks', 'active' => 'active']];
?>
@section('content')


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Home Work
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class=" col-md-3">
                            <label class="form-label" style="padding-bottom: 10px !impotant;">Status </label>
                            <div class="form-line">
                                <select class="form-control" name="status_id" id="status_id" >
                                    <option value="">All</option>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option> 
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcategory">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Subject</th>
                                               {{--  <th>Test Name</th>
                                                <th>Period</th>
                                                <th>Title</th> --}}
                                                <th>Description</th>
                                                <th>HomeWork File</th>
                                                <th>Daily Test File</th>
                                                <th>Date</th>
                                                <th>Submission date</th>
                                                <!-- <th>Position</th>
                                                <th>Status</th> -->
                                                
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                {{-- <th></th>
                                                <th></th>
                                                <th></th> --}}
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <!-- <th></th>
                                                <th></th> -->
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
                    <h4 class="modal-title" id="smallModalLabel">Add Homework</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/homework') }}"
                    method="post">

                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control course_id" name="class_id" id="class_id"  onchange="loadClassSection(this.value);"
                                        required>
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section</label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required  onchange="loadMappedSubjects(this.value);">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <input type="checkbox" name="sms_alert" id="send_sms_checkbox" checked>
                                    <label class="form-label" for="send_sms_checkbox">Do you want to send SMS?</label>
                                </div>
                            </div>

                            <!--  onchange="testList(this.value,class_id.value,section_id.value);" -->
                            <div id="subject-homework-container" class="col-md-12">
                                <div class="subject-homework-row">
                                    <div class="form-group form-float float-left col-md-6">
                                        <label class="form-label">Subject</label>
                                        <div class="form-line">
                                            <select class="form-control subject_id" name="subject_id[]" id="subject_id" required>
                                                <option value="">Select Subject</option> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-6">
                                        <label class="form-label">Home Work Details <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <textarea name="hw_description[]" rows="3" cols="30" required></textarea>
                                        </div>
                                        <div class="">
                                            <button type="button" class="btn btn-success add-subject-homework"><i class="fas fa-plus"></i></button>
                                            <button type="button" class="btn btn-danger delete-subject-homework"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--  onchange="testList(this.value,class_id.value,section_id.value);" -->
                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-line">
                                    <select class="form-control " name="subject_id" required>
                                  
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Test </label>
                                <div class="form-line">
                                    <select class="form-control"  multiple="multiple"  name="test_id[]" id="test_dropdown"  >
                                    </select>
                                </div>
                            </div>
                            <div hidden class="form-group form-float float-left col-md-6">
                                <label class="form-label">Period</label>
                                <div class="form-line">

                                    <select class="form-control " name="period" required>
                                        <option value="">Select Period</option>
                                        @if (!empty($periods))
                                            @foreach ($periods as $key => $periodtiming)
                                                <option value="{{ $key }}">{{ $key }}
                                                </option>
                                            @endforeach

                                        @endif
                                    </select>

                                </div>
                            </div>
                            <br><br><br>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Title<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control"   name="hw_title">
                                </div>
                            </div> --}} 

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Upload Home Work File</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="hw_attachment">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Upload Daily Task File</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="dt_attachment">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label"> Home Work Date</label>
                                <div class="form-line">

 
                                    <!--<input type="datetime-local" value="<?php echo date('Y-m-d\TH:i:s'); ?>"   min="<?php echo date('Y-m-d'); ?>T00:00"  class="form-control" id="hw_date" name="hw_date"> -->
                                    <input type="datetime-local"   min="<?php echo date('Y-m-d'); ?>T00:00"  class="form-control" id="hw_date" name="hw_date" value="<?php echo date('Y-m-d H:i'); ?>">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label"> Submission Date</label>
                                <div class="form-line">
                                    <input type="datetime-local" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>T09:30:00" min="<?php echo date('Y-m-d'); ?>T00:00"  class="form-control" id="hw_submission_date" name="hw_submission_date">
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Position <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control" value="1"   name="position"  min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status"  >
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Homework</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/homework') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control course_id" id="edit_class_id" name="class_id" onchange="loadClassSection(this.value);"
                                        required>
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section</label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required  onchange="loadMappedSubjects(this.value);">

                                    </select>
                                </div>
                            </div><!--  onchange="testList(this.value,class_id.value,section_id.value);"  -->
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-line">
                                    <select class="form-control " name="subject_id"id="edit_subject_id" required>
                                    
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Home Work Details <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <textarea name="hw_description" rows="3" cols="30" id="edit_hw_description" required></textarea>
                                </div>
                            </div>
                            {{-- <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Test</label>
                                <div class="form-line">
                                    <select class="form-control" multiple="multiple"  name="test_id[]" id="edit_test_dropdown"   >

                                    </select>
                                </div>
                            </div>
                            <div hidden class="form-group form-float float-left col-md-6">
                                <label class="form-label">Period</label>
                                <div class="form-line">

                                    <select class="form-control " name="period" id="edit_period" required>
                                        <option value="">Select Period</option>
                                        @if (!empty($periods))
                                            @foreach ($periods as $key => $periodtiming)
                                                <option value="{{ $key }}">{{ $key }}
                                                </option>
                                            @endforeach

                                        @endif
                                    </select>

                                </div>
                            </div>
                            <br><br><br>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Title<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control"   name="hw_title" id="edit_hw_title">
                                </div>
                            </div> --}}

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Upload Home Work File</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="hw_attachment">
                                    <input type="hidden" name="is_hw_attachment" id="is_hw_attachment">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none hw_view_file">
                                <label class="form-label">View HomeWork File</label>
                                <div class="form-line">
                                    <a href="" name="hw_view_file" id="hw_view_file" target="_blank">View</a>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Upload Daily Task File</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="dt_attachment">
                                    <input type="hidden" name="is_dt_attachment" id="is_dt_attachment">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none dt_view_file">
                                <label class="form-label">View Daily Task File</label>
                                <div class="form-line">
                                    <a href="" name="dt_view_file" id="dt_view_file" target="_blank">View</a>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label"> Home Work Date</label>
                                <div class="form-line">
                                    <input type="datetime-local" min="<?php echo date('Y-m-d'); ?>T00:00"  class="form-control"  name="hw_date" id="edit_hw_date" value="<?php echo date('Y-m-d'); ?>T00:00">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label"> Submission Date</label>
                                <div class="form-line">

                                    <input type="datetime-local" min="<?php echo date('Y-m-d'); ?>T00:00"  class="form-control"  name="hw_submission_date" id="edit_hw_submission_date">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Position <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control"    name="position" id="edit_position" 
                                        min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status"  >
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
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
            $('#section_dropdown').val('');
            $('#subject_id').val('');
            $('#test_dropdown').html('');
            $('#hw_date').change();

            if($('.subject-homework-row').length > 1) {
                var rowindex;  var rowlen = $('.subject-homework-row').length-1;
                for(rowindex = rowlen; rowindex>=1; rowindex--) {
                    console.log(rowindex); 
                    $('.subject-homework-row')[rowindex].remove();
                } 
                $('.add-subject-homework').removeClass('disabled')
                $('.add-subject-homework').prop('disabled', false)
            }
        });

        $(function() {
            $('#hw_date').change(function() {
            date = this.value;
            date1 = date.split('T')[0];
            date2 = date.split('T')[1];
            date3 = '09:30:00';

            var someDate = new Date(date1);
            someDate.setDate(someDate.getDate() + 1); //number  of days to add, e.x. 15 days
            var dateFormated = someDate.toISOString().substr(0,10);
            console.log(dateFormated);
            fin_date = dateFormated+'T'+date3;

              $('#hw_submission_date').val(fin_date);
             
            });
            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/teacher/homework/datatables/",       
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            var approve_status = data.approve_status;
                            if(approve_status != "APPROVED") {
                                return '<a href="#" onclick="loadTopics(' + tid + ')" title="Edit Homework"><i class="fas fa-edit"></a>';
                            }  else {
                                return '';
                            }

                        },

                    },
                    {
                        data: 'is_class_name'
                    },
                    {
                        data: 'is_section_name'
                    },
                    {
                        data: 'is_subject_name'
                    },
                    /*{
                        data: 'is_new_test_name',
                            "render" : function( data, type, row, meta ) {
                                var str = '';
                               
                                $.each(data, function(key, value) {
                            // 
                               if(value == null){
                                str = '-'
                               }else{
                                var url = "{{URL('/')}}/teacher/testlist/views/"+key;
                                    str+='<a href="'+url+'" target="_blank"> '+value+'</a> ';
                                  
                               }
                               
                        });
                
                        return str;
                                      
                       },
                    },*/
                  
                    // {
                    //     data: 'period'
                    // },
                    /*{
                        data: 'hw_title'
                    },*/
                    {
                        data: 'hw_description'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var homework_file = data.hw_attachment;
                            var is_hw_attachment = data.is_hw_attachment;
                            if (homework_file != null && homework_file != '') {
                                return '<a href="' + is_hw_attachment +
                                    '" target="_blank" title="Homework" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var dailytask_file = data.dt_attachment;
                            var is_dt_attachment = data.is_dt_attachment;
                            if (dailytask_file != null && dailytask_file != '') {
                                return '<a href="' + is_dt_attachment +
                                    '" target="_blank" title="Daily Task" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'hw_date'
                    },
                    {
                        data: 'is_hw_submission_date'
                    },
                    /*{
                        data: 'position'
                    },
                    {
                        data: 'status'
                    }*/
                   
                ],
                dom: 'Blfrtip',
                buttons: [],
                "order":[[7, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 } 
                 ] 

            });

            $('.tblcategory tfoot th').each(function(index) {

                if (index != 6 && index != 7 && index != 11 && index != 0) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });
            $('#status_id').on('change', function() {
                table.draw();
            });

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

        function loadTopics(id) {

            $('#edit-style-form .hw_view_file').addClass('d-none');
            $('#edit-style-form #hw_view_file').attr('href', '#');
            $('#edit-style-form .dt_view_file').addClass('d-none');
            $('#edit-style-form #dt_view_file').attr('href', '#');

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('teacher/edit/homework') }}",
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
               $('#edit_class_id').val(response.data.class_id);
               var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.is_section_name;
                loadClassSection(val, selectedid, selectedval);

                var val = response.data.section_id;
                var selectedid = response.data.subject_id;
                var selectedval = response.data.is_subject_name;
                loadMappedSubjects(val, selectedid, selectedval);
                // $('#edit_subject_id').val(response.data.subject_id);
                // $('#edit_period').val(response.data.period);
                $('#edit_hw_title').val(response.data.hw_title);
                $('#edit_hw_description').val(response.data.hw_description);
                $('#edit_hw_date').val(response.data.hw_date);
                $('#edit_hw_submission_date').val(response.data.hw_submission_date);
                $('#edit_position').val(response.data.position);
                $('#edit_status').val(response.data.status);

                //testList(response.data.subject_id,response.data.class_id,response.data.is_test_id, response.data.is_test_id)
                if (response.data.hw_attachment != '' && response.data.hw_attachment != null) {
                    $('#edit-style-form .hw_view_file').removeClass('d-none');
                    $('#edit-style-form #hw_view_file').attr('href', response.data.is_hw_attachment);
                    $('#edit-style-form #is_hw_attachment').val(response.data.is_hw_attachment);
                }

                if (response.data.dt_attachment != '' && response.data.dt_attachment != null) {
                    $('#edit-style-form .dt_view_file').removeClass('d-none');
                    $('#edit-style-form #dt_view_file').attr('href', response.data.is_dt_attachment);
                    $('#edit-style-form #is_dt_attachment').val(response.data.is_dt_attachment);
                }

                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadClassSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $("#subject_id,#edit_subject_id").html('');
            $.ajax({
                url: "{{ url('teacher/fetch-sub-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#section_dropdown,#edit_section_dropdown').html(
                            '<option value="">-- Select Section --</option>');
                    $.each(res.section, function(key, value) {
                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadMappedSubjects(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#subject_id,#edit_subject_id").html('');
            $.ajax({
                url: "{{ url('teacher/fetch-sub-subject') }}",
                type: "POST",
                data: {
                    section_id: section_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('.subject_id').html(
                            '<option value="">-- Select Subject --</option>');
                    $.each(res.subject, function(key, value) {

                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $(".subject_id,#edit_subject_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }


        function testList(val,class_id,selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var subject_id = val;
            var selid = selectedid;
            var selval = selectedval;

            class_id = class_id;

            $("#test_dropdown,#edit_test_dropdown").html('');
            $.ajax({
                url: "{{ url('teacher/fetch-tests') }}",
                type: "POST",
                data: {
                    subject_id: subject_id,
                    class_id:class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#test_dropdown').html(
                           '<option value="">-- Select Test --</option>');
                               $.each(res.tests, function(key, value) {
                                   var selected = '';
                                   var arr = selectedid.toString().split(',');
                                   var result = arr.map(function (x) {
                              return parseInt(x, 10);
                                 });
             if(result.indexOf(value.id) !== -1)
            {

                    selected = ' selected ';
            }


                                    $("#test_dropdown,#edit_test_dropdown").append('<option value="' + value
                                        .id + '" '+selected+'>' + value.test_name + '</option>');
                                });
                }
            });
        }



        $('#hw_date').change(function() {
            date = this.value;
            date1 = date.split('T')[0];
            date2 = date.split('T')[1];
            date3 = '09:30:00';

            var someDate = new Date(date1);
            someDate.setDate(someDate.getDate() + 1); //number  of days to add, e.x. 15 days
            var dateFormated = someDate.toISOString().substr(0,10);
            console.log(dateFormated);
            fin_date = dateFormated+'T'+date3;

              $('#hw_submission_date').val(fin_date);
             
        });

        $('#hw_date').change();


        $('#edit_hw_date').change(function() {
            date = this.value;
            date1 = date.split('T')[0];
            date2 = date.split('T')[1];
            date3 = '09:30:00';

            var someDate = new Date(date1);
            someDate.setDate(someDate.getDate() + 1); //number  of days to add, e.x. 15 days
            var dateFormated = someDate.toISOString().substr(0,10);
            console.log(dateFormated);
            fin_date = dateFormated+'T'+date3;

            $('#edit_hw_submission_date').val(fin_date);
         
        });

        $('#edit_hw_date').change();

    </script>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('subject-homework-container');
            const checkbox = document.getElementById('send_sms_checkbox');
            let maxRows = 3; // Default value

            function updateMaxRows() {
                maxRows = checkbox.checked ? 3 : 6;
                toggleAddButton();
            }

            container.addEventListener('click', function(event) {
                const clickedButton = event.target.closest('button');
                if (!clickedButton) return;

                if (clickedButton.classList.contains('delete-subject-homework')) {
                    if (container.querySelectorAll('.subject-homework-row').length > 1) {
                        clickedButton.closest('.subject-homework-row').remove();
                        toggleAddButton();
                    }
                } else if (clickedButton.classList.contains('add-subject-homework')) {



                    if (checkDuplicateSubjects()) {
                        swal('Oops', 'This subject has already been selected.', 'warning');
                        return;
                    }

                    const newRow = container.querySelector('.subject-homework-row').cloneNode(true);

                    // Clear the values of the cloned row
                    newRow.querySelectorAll('input, select, textarea').forEach(function(element) {
                        element.value = '';
                    });

                    // Add event listener to the new select element
                    newRow.querySelector('select.subject_id').addEventListener('change', function() {
                        if (checkDuplicateSubjects()) {
                            swal('Oops', 'This subject has already been selected.', 'warning');
                            this.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    toggleAddButton();
                }
            });

            function toggleAddButton() {
                const rows = container.querySelectorAll('.subject-homework-row');
                const addButton = container.querySelectorAll('.add-subject-homework');
                addButton.forEach(function(button) {
                    button.disabled = rows.length >= maxRows;
                    button.classList.toggle('disabled', rows.length >= maxRows);
                });
            }

            function checkDuplicateSubjects() {
                const subjects = [];
                let hasDuplicate = false;
                container.querySelectorAll('.subject_id').forEach(function(select) {
                    if (select.value && subjects.includes(select.value)) {
                        hasDuplicate = true;
                    } else {
                        subjects.push(select.value);
                    }
                });
                return hasDuplicate;
            }

            // Attach change event to existing select elements
            container.querySelectorAll('select.subject_id').forEach(function(select) {
                select.addEventListener('change', function() {
                    if (checkDuplicateSubjects()) {
                        swal('Oops', 'This subject has already been selected.', 'warning');
                        this.value = '';
                    }
                });
            });

            // Event listener for checkbox state change
            checkbox.addEventListener('change', updateMaxRows);

            // Initial call to set the add button state
            updateMaxRows();
        });



    </script>
@endsection

