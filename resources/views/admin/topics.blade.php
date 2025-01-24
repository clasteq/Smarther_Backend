@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_topics', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')

<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Topics', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">


                        <h4 style="font-size: 20px;" class="card-title"> Books
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add Books</button></a>
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

                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="overflow-x: scroll;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcategory">
                                        <thead>
                                            
                                            <tr>
                                                <th>Action</th>
                                                <th>Code</th>
                                                <th>Course</th>
                                                <th>Subject</th>
                                                <th>Term</th>
                                                <th>Chapter</th>
                                                <th>Topic</th>
                                                <th>Name</th>
                                                <th>File</th>
                                                {{-- <th>Link</th> --}}
                                                {{-- <th>Is Recommended</th> --}}
                                                <th>Position</th>
                                                <th>Status</th>
                                            </tr>
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
                                                {{-- <th></th>
                                                <th></th> --}}
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
                    <h4 class="modal-title" id="smallModalLabel">Add Books</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/topics') }}" method="post">

                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control course_id" onchange="loadClassSubject(this.value);  loadClassTerms(this.value);" name="class_id"  required>
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
                                    <select class="form-control" id="subject_id" name="subject_id"
                                        onchange="loadChapterOptions(term_id.value,class_id.value,this.value);" required>
                                        <option value="">Select Subject</option>
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
                                    <select class="form-control chapter_id" name="chapter_id" id="chapter_dropdown"
                                        onchange="loadChapterTopicsOptions(this.value);">
                                        <option value="0">Select Chapter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Topics</label>
                                <div class="form-line">
                                    <select class="form-control topic_id" name="topic_id" id="chapter_topics_dropdown">
                                        <option value="0">Select Topic</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="topic_title" required>
                                </div>
                            </div>
                            {{-- <div class="form-group form-float float-left col-md-6" hidden>
                                <label class="form-label">Type <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="topic_type" required>
                                        <option value="">Select Type</option>
                                        <option value="PDF">PDF</option>
                                        <option value="DOC">Document</option>
                                        <option value="VIDEO">Video</option>
                                    </select>
                                </div>
                            </div> --}}
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Upload File <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="topic_file" required>
                                </div>
                            </div>
                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">YouTube Video Token
                                    (ex.)https://www.youtube.com/watch?v=<b style="color: #f00;">ABCD1234-567</b></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="video_link"
                                        placeholder="ABCD1234-567">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Recommended</label>
                                <div class="form-line">
                                    <input type="checkbox" class="form-control col-md-3" name="is_recommended"
                                        value="1">
                                </div>
                            </div> --}}
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Books</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/topics') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control course_id" onchange="loadClassSubject(this.value);  loadClassTerms(this.value);" name="class_id" id="class_id" required>
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
                                    <select class="form-control " name="subject_id" id="edit_subject_id"
                                         onchange="loadChapterOptions(term_id.value,class_id.value,this.value);" required>
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
                            <input type="hidden" name="hid_chapter_id" id="hid_chapter_id">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Chapter</label>
                                <div class="form-line">
                                    <select class="form-control chapter_id" name="chapter_id" id="edit_chapter_dropdown"
                                        onchange="loadChapterTopicsOptions(this.value);">
                                        <option value="0">Select Chapter</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="hid_topic_id" id="hid_topic_id">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Topics</label>
                                <div class="form-line">
                                    <select class="form-control topic_id" name="topic_id"
                                        id="edit_chapter_topics_dropdown">
                                        <option value="0">Select Topic</option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">Name <span class="manstar">*</span></label>
                            <div class="form-line">
                                <input type="text" class="form-control" name="topic_title" id="topic_title" required>
                            </div>
                        </div>
                        {{-- <div class="form-group form-float float-left col-md-6" hidden>
                            <label class="form-label">Type <span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control" name="topic_type" id="topic_type" required>
                                    <option value="">Select Type</option>
                                    <option value="PDF">PDF</option>
                                    <option value="DOC">Document</option>
                                    <option value="VIDEO">Video</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">Upload File <span class="manstar">*</span></label>
                            <div class="form-line">
                                <input type="file" class="form-control" name="topic_file" id="topic_file">
                                <input type="hidden" name="is_topic_file" id="is_topic_file">
                            </div>
                        </div>
                        <div class="form-group form-float float-left col-md-6 d-none view_file">
                            <label class="form-label">View</label>
                            <div class="form-line">
                                <a href="" name="view_file" id="view_file" target="_blank">View</a>
                            </div>
                        </div>
                        {{-- <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">YouTube Video Token
                                (ex.)https://www.youtube.com/watch?v=<b style="color: #f00;">ABCD1234-567</b></label>
                            <div class="form-line">
                                <input type="text" class="form-control" name="video_link" id="video_link"
                                    placeholder="ABCD1234-567">
                            </div>
                        </div>
                        <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">Is Recommended</label>
                            <div class="form-line">
                                <input type="checkbox" class="form-control col-md-3" name="is_recommended"
                                    id="is_recommended" value="1">
                            </div>
                        </div> --}}
                        <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">Position <span class="manstar">*</span></label>
                            <div class="form-line">
                                <input type="number" class="form-control" name="position" id="position" required
                                    min="1">
                            </div>
                        </div>
                        <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">Status <span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control" name="status" id="status" required>
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
                
            });

        $(function() {

            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/topics/datatables/",  
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        var class_id  = $('#classid').val();
                        var subject_id  = $('#subjectid').val(); 
                        var term_id  = $('#termid').val();
                        $.extend(d, {status:status,class_id:class_id,subject_id:subject_id,term_id:term_id});

                    }
                },
                columns: [{
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            var ref_code = data.ref_code;

                            return '<a href="#" onclick="loadTopics(' + tid +
                                ')" title="Edit Topics"><i class="fas fa-edit"></i></a>';

                        },

                    },
                    { data: 'ref_code', name:  'topics.ref_code'},
                    { data: 'is_class_name', name:  'is_class_name'},
                    { data: 'is_subject_name', name:  'is_subject_name'},
                    { data: 'is_term_name', name:  'is_term_name'},
                    { data: 'chapter_name', name:  'chaptername'},
                    { data: 'chapter_topic_name', name:  'chapter_topic_name'},
                    { data: 'topic_title', name:  'topic_title'},
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var topic_file = data.topic_file;
                            var is_topic_file = data.is_topic_file;
                            if (topic_file != null && topic_file != '') {
                                return '<a href="' + is_topic_file +
                                    '" target="_blank" title="Topics" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    { data: 'position', name:  'topics.position'},
                    { data: 'status', name:  'topics.status'},
                    // {
                    //     data: null,
                    //     "render": function(data, type, row, meta) {

                    //         var video_link = data.video_link;
                    //         if (video_link != null && video_link != '') {
                    //             return '<a href="' + video_link +
                    //                 '" target="_blank" title="Topics" class="btn btn-danger">View</a>';
                    //         } else {
                    //             return '';
                    //         }
                    //     },

                    // },
                    // {
                    //     data: 'is_recommended'
                    // },
                    
                ],
                dom: 'Blfrtip',
                buttons: [



                ],
                "order": [[7, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 2 },
                    { "orderable": false, "targets": 3 },
                    { "orderable": false, "targets": 4 },
                    { "orderable": false, "targets": 8 },
                    { "orderable": false, "targets": 10 }
                ]

            });

            $('.tblcategory tfoot th').each(function(index) {

                if (index != 0 && index != 2 && index != 3 && index != 4 && index != 8 && index != 10) {
                    var title = $(this).text();
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

            $('#edit-style-form .subject_id').html('');
            $('#edit-style-form .chapter_id').html('');
            $('#edit-style-form .view_file').addClass('d-none');
            $('#edit-style-form #view_file').attr('href', '#');
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/topics') }}",
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
                $('#edit-style-form #class_id').val(response.data.class_id);
                $('#edit-style-form #edit_subject_id').val(response.data.subject_id);
                var val = response.data.subject_id;
                var selectedid = response.data.chapter_id;
                var selectedval = response.data.chapter_name;
                loadChapterOptions(val, response.data.class_id,selectedid, selectedval);

                // $('#edit-style-form #edit_chapter_dropdown').val(response.data.chapter_id);

                var val = response.data.chapter_id;
                var selectedid = response.data.topic_id;
                var selectedval = response.data.is_topic_name;
                loadChapterTopicsOptions(val, selectedid, selectedval);

                // $('#edit-style-form #edit_chapter_topics_dropdown').val(response.data.topic_id);

                $('#edit-style-form #topic_title').val(response.data.topic_title);
                $('#edit-style-form #topic_type').val(response.data.topic_type);
                $('#edit-style-form #video_link').val(response.data.is_video_token);
                $('#edit-style-form #status').val(response.data.status);
                $('#edit-style-form #position').val(response.data.position);

                loadClassSubject(response.data.class_id, response.data.subject_id, response.data.is_subject_name);
                loadClassTerms(response.data.class_id, response.data.term_id, response.data.is_term_name);

                var selectedid = response.data.chapter_id;
                var selectedval = response.data.is_chapter_name;
                loadChapterOptions(response.data.term_id,response.data.class_id,response.data.subject_id,selectedid,selectedval);

                if (response.data.topic_file != '') {
                    $('#edit-style-form .view_file').removeClass('d-none');
                    $('#edit-style-form #view_file').attr('href', response.data.is_topic_file);
                    $('#edit-style-form #is_topic_file').val(response.data.is_topic_file);
                } else {
                    $('#edit-style-form #topic_file').attr('required', 'required')
                }

                if (response.data.is_recommended == 1) {
                    $('#edit-style-form #is_recommended').prop('checked', 'checked');
                } else {
                    $('#edit-style-form #is_recommended').prop('checked', '');
                }



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
    async:true,
    success: function(res) {
        $('#subject_id,#edit_subject_id,.subject_id').html(
            '<option value="">-- Select Subject --</option>');
    
        $.each(res.subjects, function(key, value) {
            var selected = '';
                      if (selid != null || selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
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
       
     
        $('#chapter_dropdown,#edit_chapter_dropdown').html(
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


function loadChapterTopicsOptions(val,selectedid,selectedval) {

selectedid = selectedid || 0;
selectedval = selectedval || 0;

var selval = val;

var selid = selectedid;

var selectvalue = selectedval;

$("#chapter_topics_dropdown,#edit_chapter_topics_dropdown").html('');
$.ajax({
    url: $('#getChapterTopicsOptionsURL').val(),
    type: "POST",
    dataType: 'json',
    data: {
        chapter_id: selval,
    },
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    
    success: function (res) {
       
        $('#chapter_topics_dropdown,#edit_chapter_topics_dropdown').html(
            '<option value="0">-- Select Chapter --</option>');
        /*if(selid > 0 && selectvalue > 0){
            $("#edit_chapter_topics_dropdown").append('<option selected value="' + selid + '">' + selectvalue + '</option>');
           
        }*/
        $.each(res.chapter, function (key, value) {
            var selected = ''; 
            if(selid > 0  && value.id == selid ){
                selected = 'selected';
            } 
            $("#chapter_topics_dropdown,#edit_chapter_topics_dropdown").append('<option  value="' +
                value.id + '" '+selected+'>' + value.chapter_topic_name + '</option>');
        });
       
        
    }
});
}
    </script>

@endsection
