@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_subject_mapping', 'active')
@section('menuopenu', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers Subject Mapping', 'active' => 'active']];
?>
@section('content')     
 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid"> 
            <div class="panel"> 
                <!-- Panel Heading -->
                <div class="panel-heading"> 
                    <!-- Panel Title -->
                   
                </div>
                <div class="panel-body">  
                    <div class="row"> 
                        <div class="col-xs-12 col-md-12"> 
                        <div class="card"> 
                            <div class="card-body">
                                <h4 style="font-size: 20px;" class="panel-title">Add Subject Mapping 
                                </h4> 
                                <div class="row"><div class="col-md-12">
                                  
                <form id="frm_questionbank" enctype="multipart/form-data" action="{{ url('/admin/save/subject_mapping') }}"
                method="post">

                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group form-float float-left col-md-6">
                            <label class="form-label">Teacher  <span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control course_id" onchange="loadmappedsubjects(this.value)" id="teacher_id" name="teacher_id"
                                    required>
                                    <option value="">Select Teacher</option>
                                    @if (!empty($teacher))
                                        @foreach ($teacher as $course)
                                            <option value="{{ $course->user_id }}">{{ $course->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-float float-left col-md-6">
                       
                        </div>
                        {{-- <div class="form-group form-float float-left col-md-6">
                            <label class="form-label"> Others </label>  <button type="button" class="btn btn-success center-block plus" id="plus_1" data-id="1" >+</button> 
                        </div>
                        <div class="form-group form-float float-left col-md-6">
                         
                        </div> --}}
                        @php
                            $i= 1;
                        @endphp
                            <div id="reloadSection" class="col-sm-12">
   <div class="form-group form-float float-left col-md-3">
    <label class="form-label">Class<span class="manstar">*</span></label>
    <div class="form-line">
        <select class="form-control course_id" id="class_id" name="class_id"
            onchange="loadClassSection(this.value)" required>
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
<div class="form-group form-float float-left col-md-3">
    <label class="form-label">Section <span class="manstar">*</span></label>
    <div class="form-line">
        <select class="form-control" onchange="loadsubjects(this.value)" name="section_id" id="section_dropdown" required>

        </select>
    </div>
</div>

<div class="form-group form-float float-left col-md-3">
    <label class="form-label">Subject <span class="manstar">*</span></label>
    <div class="form-line">
        <select class="form-control" name="subject_id" id="subject_dropdown" required>

        </select>
    </div>
</div>
{{-- <div class="form-group form-float float-right col-md-3">
    <label class="form-label">Status <span class="manstar">*</span></label>
    <div class="form-line">
        <select class="form-control" name="status[]" required>
            <option value="ACTIVE">ACTIVE</option>
            <option value="INACTIVE">INACTIVE</option>
        </select>
    </div>
</div> --}}
<div class="form-group form-float float-right col-md-3">
   
    <div class="form-line">
    <button class="btn btn-primary" id="Submit" style="margin-top: 35px;">Update</button>
    </div>
</div>

                            </div>
                    
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                </div> --}}

            </form>

            <div id="timetableentries"
            {{-- >@include('admin.loadmappedsubjects') --}}
        </div>
                                </div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
      <script>

        $(function() {
            CKEDITOR.replace( 'about' ); 

            $('.plus').on('click', function () {
                var qtype = $(this).data('id');
                var i = $('#items_'+qtype).find('input[name="sno[]"]').length;
                
                var request = $.ajax({
                    type: 'post',
                    url: " {{URL::to('admin/clone/subjectmapping')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        code:qtype,i:i,
                    },
                    dataType:'json',
                    encode: true
                });
                request.done(function (response) { 
                    if(response.status == 'SUCCESS') {
                        $('#items_'+qtype).append(response.data);
                    }   else {
                        swal("Oops!", "Unable to clone the type", "error");
                    }
                });
                request.fail(function (jqXHR, textStatus) {

                    swal("Oops!", "Sorry,Could not process your request", "error");
                });
            });
            $('#Submit').on('click', function () {
                var teacher_id = $('#teacher_id').val();

                var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                           swal('Success','Class Mapped Successfully..!','success');

                        //    window.location.reload();
                        loadmappedsubjects(teacher_id)
                        $('#class_id').val('');
                        $('#section_dropdown').val('');
                        $('#subject_dropdown').val('');

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#frm_questionbank").ajaxForm(options);
            });   
        });


        function loadTeacher(id) {


var request = $.ajax({
    type: 'post',
    url: " {{ URL::to('admin/edit/subject_mapping') }}",
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
    $('#edit_teacher_id').val(response.data.teacher_id);
    var val = response.data.class_id;
    var selectedid = response.data.section_id;
    loadClassSection(val, selectedid);
    var val = response.data.section_id;
    var selectedid = response.data.subject_id;
    loadsubjects(val,selectedid)
    $('#edit_status').val(response.data.status);

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
$.ajax({
    url: "{{ url('admin/fetch-section') }}",
    type: "POST",
    data: {
        class_id: class_id,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {

        $('#section_dropdown').html(
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

function loadsubjects(val, selectedid, selectedval) {

selectedid = selectedid || " ";
selectedval = selectedval || " ";
var section_id = val;
var selid = selectedid;
var selval = selectedval;
$("#subject_dropdown,#edit_subject_dropdown").html('');
$.ajax({
    url: "{{ url('admin/fetch-subject') }}",
    type: "POST",
    data: {
        section_id: section_id,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {

        $('#subject_dropdown').html(
                '<option value="">-- Select Subject --</option>');
        $.each(res.subjects, function(key, value) {
            var selected = '';
            if(selectedid != '' && selectedid == value
                .id) {
                selected = ' selected ';
            }
            $("#subject_dropdown,#edit_subject_dropdown").append('<option value="' + value
                .id + '" '+selected+'>' + value.subject_name + '</option>');
        });
    }
});
}

function deletesubject(id,teacher_id){
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
                url: " {{ URL::to('admin/delete/mappedsubjects') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id:id,
                    teacher_id: teacher_id
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == "SUCCESS") {
                    swal({title: "Success", text: response.message, type: "success"},
                            function(){
                                loadmappedsubjects(teacher_id);
                            }
                        );

                } else {
                    // $('#timetableentries').html(response.message);
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });

        }
    });
        }

function loadmappedsubjects(teacher_id) {
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/mappedsubjects') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    teacher_id: teacher_id
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == "SUCCESS") {
                    $('#timetableentries').html(response.data);
                } else {
                    // $('#timetableentries').html(response.message);
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

    
    </script>
 

@endsection

