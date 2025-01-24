@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_chapertopics', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')

<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Country', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" style="font-size:20px;">Chapter Topics
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add Topics</button></a>
                        </h4>
                        <div class="row">
                            <div class=" col-md-3 float-left">
                                <label class="form-label" style="padding-bottom: 10px;">Status </label>
                                <div class="form-line">
                                    <select class="form-control" name="status_id" id="status_id" >
                                        <option value="">All</option>
                                        <option value="ACTIVE" selected>ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select> 
                                </div>
                            </div>

                            <div class=" col-md-3 float-left">
                                <label class="form-label" >Class </label>
                                <div class="form-line">
                                    <select onchange="loadClassSubject(this.value); loadClassTerms(this.value);" class="form-control course_id" name="class_id" id="classid" required >
                                        <option value="">Select Class</option>
                                        @if(!empty($classes)) 
                                            @foreach($classes as $course) 
                                                <option value="{{$course->id}}">{{$course->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3 float-left">
                                <label class="form-label" >Subject </label>
                                <div class="form-line">
                                    <select  class="form-control subject_id" name="subject_id" id="subjectid" required >
                                        <option value="">Select Subject</option> 
                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3 float-left">
                                <label class="form-label" >Terms </label>
                                <div class="form-line">
                                    <select class="form-control term_id" name="term_id" id="termid" required >
                                        <option value="">Select Term</option> 
                                    </select>
                                </div>
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
                                                <th>Code</th>
                                                <th>Class</th>
                                                <th>Subject</th>
                                                <th>Term</th>
                                                <th>Chapter</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Chapter Topics</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/chaptertopics') }}"
                    method="post">

                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">

                                    <select class="form-control" onchange="loadClassSubject(this.value); loadClassTerms(this.value);" name="class_id" required>
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-line"> 
                                    <select class="form-control "  name="subject_id" id="subject_id" onchange="loadChapterOptions(term_id.value,class_id.value,this.value);" required>
                                      
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Terms</label>
                                <div class="form-line"> 
                                    <select class="form-control "  name="term_id" id="term_id"  onchange="loadChapterOptions(this.value,class_id.value,subject_id.value);" required>
                                      
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Chapter</label>
                                <div class="form-line">
                                    <select class="form-control" name="chapter_id" id="chapter_dropdown">
                                        <option value="">Select Chapter</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="chapter_topic_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="position" required
                                        min="1">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Chapter Topics</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/chaptertopics') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">

                                    <select class="form-control" onchange="loadClassSubject(this.value)" name="class_id" id="edit_class_id" required>
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-line"> 
                                    <select class="form-control " id="edit_subject_id" name="subject_id"  onchange="loadChapterOptions(term_id.value,class_id.value,this.value);" required>
                                     
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Terms</label>
                                <div class="form-line"> 
                                    <select class="form-control "  name="term_id" id="edit_term_id"  onchange="loadChapterOptions(this.value,class_id.value,subject_id.value);" required>
                                      
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Chapter</label>
                                <div class="form-line">
                                    <select class="form-control chapter_id" name="chapter_id" id="edit_chapter_dropdown">
                                        <option value="">Select Chapter</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="chapter_topic_name"
                                        id="chapter_topic_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position" required
                                        min="1">
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
        $(function() {

            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/chaptertopics/datatables/",     
                    data: function ( d ) {
                        var status  = $('#status_id').val();

                        var class_id  = $('#classid').val();
                        var subject_id  = $('#subjectid').val(); 
                        var term_id  = $('#termid').val();
                        $.extend(d, {status:status,class_id:class_id,subject_id:subject_id,term_id:term_id});

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            var ref_code = data.ref_code;

                            return '<a href="#" onclick="loadTopics(' + tid +
                                ')" title="Edit Topics"><i class="fas fa-edit"></i></a>';

                        },

                    }, 
                    { data: 'ref_code', name: 'chapter_topics.ref_code' },
                    { data: 'is_class_name', name: 'is_class_name' },
                    { data: 'is_subject_name', name: 'is_subject_name' },
                    { data: 'is_term_name', name: 'is_term_name' },
                    { data: 'chaptername', name: 'chaptername' },
                    { data: 'chapter_topic_name', name: 'chapter_topic_name' },
                    { data: 'position', name: 'chapter_topics.position' },
                    { data: 'status', name: 'chapter_topics.status' },
                  
                ],
                "order": [[6, 'asc']],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],


            });

            $('.tblcategory tfoot th').each(function(index) {
                var title = $(this).text();
                if (index != 0 && index != 2 && index != 3 && index != 4 && index != 8) {
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });

            $('#status_id,#classid,#subjectid,#termid').on('change', function() {
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
            $('.subject_id').html('');
            $('.chapter_id').html('');
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/chaptertopics') }}",
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
                $('#edit_class_id').val(response.data.class_id);
                $('#edit_subject_id').val(response.data.subject_id);
                loadClassSubject(response.data.class_id, response.data.subject_id, response.data.is_subject_name) 

                var val = response.data.subject_id;
                var selectedid = response.data.chapter_id;
                var selectedval = response.data.is_chapter_name;
                loadClassTerms(response.data.class_id, response.data.term_id, response.data.is_term_name);
                loadChapterOptions(response.data.term_id,response.data.class_id,response.data.subject_id,selectedid,selectedval);
                $('#edit_chapter_dropdown').val(response.data.chapter_id);
                $('#chapter_topic_name').val(response.data.chapter_topic_name);
                $('#edit_status').val(response.data.status);
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadClassSubject(val, selectedid, selectedval) {

selectedid = selectedid || " ";
selectedval = selectedval || " ";
var class_id = val;
var selid = selectedid;
var selval = selectedval;

$("#subject_id,#edit_subject_id,.subject_id").html('');
$.ajax({
    url: "{{ url('admin/fetch-class-subject') }}",
    type: "POST",
    data: {
        class_id: class_id,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {
        $('#subject_id,#edit_subject_id,.subject_id').html(
            '<option value="">-- Select Subject --</option>');
    
        $.each(res.subjects, function(key, value) {
            var selected = '';
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                     
            $("#subject_id,#edit_subject_id,.subject_id").append('<option value="' + value
                .id + '" '+selected+'>' + value.subject_name + '</option>');
        });
    }
});
}



function loadChapterOptions(val,class_id,subject_id,selectedid,selectedval) {

selectedid = selectedid || '';
selectedval = selectedval || '';

var selval = val;
var selid = selectedid;
var selectvalue = selectedval;

if(subject_id > 0) {} else {
    swal("Oops!", "Please select the Subject", "error"); return false;
}

$("#chapter_dropdown,#edit_chapter_dropdown").html('');
$.ajax({
    url:  "{{ url('admin/fetch-chapters') }}",
    type: "POST",
    dataType: 'json',
    data: {
        term_id: val, 
        subject_id: subject_id, 
        class_id:class_id,
        _token: '{{ csrf_token() }}'
    },
   
    
    success: function (res) {
        console.log('res', res);
       
     
        $('#chapter_dropdown').html(
            '<option value="">-- Select Chapter --</option>');
            $.each(res.chapter, function (key, value) {
                var selected = '';
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                    
                       $("#chapter_dropdown,#edit_chapter_dropdown").append('<option  value="' +
                    value.id + '" '+selected+'>' + value.chaptername + '</option>');
            });
        
       
        
    }
});
}

    </script>

@endsection
