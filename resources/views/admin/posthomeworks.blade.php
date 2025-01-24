@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_posthomeworks', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
@section('content')
<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@if((isset($session_module['Homeworks']) && ($session_module['Homeworks']['view'] == 1)) || ($user_type == 'SCHOOL'))
<meta name="csrf-token" content="{{ csrf_token() }}">

<style type="text/css">
        .actinput {
            background-color: white; 
            border-radius: 50px;
        }
        .photos {
            background-color: unset;
            width: 50%;
            border-radius: 50px;
        }
        .submitact {
            border-color: #fff;
            border-radius: 20%;
            border-style: hidden;
            background: #f8f6f6;
        }
        input[type=file] {
          display: block;
          color: red;
          font-style: oblique;
        }
        input[type=file]::file-selector-button {
          /*display: none;
           visibility:hidden;*/ 
        }
        .activityimage img {
            width: 70px;
            height: auto; /*200px;*/
            border-radius: 3%;
        }
        .editact {
            width: 20px;
            height: 20px !important;
        }
        .deleteact {
            width: 20px;
            height: 20px !important;
        }
        .likeact {
            cursor: pointer;
        }
        .w-15 {
            width: 15px !important;
        }

        .offerolympiaimg {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #fff !important;
            min-height: 322px !important;
            max-height: 322px !important;
            max-width: 712px !important;
            overflow-y: auto;
        }

        .offerolympia {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #000;
            overflow-y: auto;
        } 
        .ml-15 {
            margin-left: 9rem !important;
        }

        blockquote {
            background-color: transparent;
            border-left: .2rem solid #007bff;
            margin: 1.5em .7rem;
            padding: .5em .7rem; 
        }
</style>

<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel"> 
                <div class="panel-body">


            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <div class="card-header">Homeworks
                        @if((isset($session_module['Homeworks']['add']) && ($session_module['Homeworks']['add'] == 1)) || ($user_type == 'SCHOOL'))
                        <a href="#" data-toggle="modal" data-target="#smallModal" id="addbanner"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        @endif
                        <div class="row">
                            <div class="form-group  col-md-2">
                                <label class="form-label">Approval Status </label>
                                <div class="form-line">
                                    <select class="form-control" name="approval_status_id" id="approval_status_id" >
                                        <option value="">All</option>
                                        <option value="APPROVED">APPROVED</option>
                                        <option value="UNAPPROVED">UNAPPROVED</option>
                                    </select>
                                </div>
                            </div>
                            <div class=" col-md-2">
                                <label class="form-label">Class </label>
                                <div class="form-line">
                                    <select class="form-control" name="classid" id="classid" onchange="loadClassSectionHw(this.value);" >
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

                            <div class=" col-md-2">
                                <label class="form-label">Section </label>
                                <div class="form-line">
                                    <select class="form-control section_id" name="sectionid" id="sectionid" onchange="loadClassSubjectsHw(this.value);">
                                        <option value="">Select Section</option>
                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3 d-none">
                                <label class="form-label">Subject </label>
                                <div class="form-line">
                                    <select class="form-control" name="subjectid" id="subjectid" >
                                        <option value="">Select Subject</option> 
                                    </select> 
                                </div>
                            </div>
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

                    <div >  
                        <input type="hidden" name="pagename" id="pagename" value="communcation_post_homeworks">
                        <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
                    </div>
                    <div class="col-md-9 ml-15" >
                        @include('admin.posthomeworks_list')   
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
                    <h4 class="modal-title" id="smallModalLabel">Add Home Work</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/homework') }}"
                    method="post">

                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control course_id" name="class_id" id="class_id" onchange="loadClassSectionHw(this.value);loadClassSubjectsHw(this.value,'','',1,1);"
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
                                    <select class="form-control section_id" name="section_id" id="section_dropdown" required  onchange="loadClassSubjectsHw(this.value,'','','',1);">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <div class="form-line">
                                    <input type="checkbox" name="sms_alert" id="send_sms_checkbox" value="1">
                                    <label class="form-label" for="send_sms_checkbox">Smart SMS</label>
                                </div>
                            </div>

                            <!--  onchange="testList(this.value,class_id.value,section_id.value);" -->
                            <div id="subject-homework-container" class="col-md-12">
                                <div class="subject-homework-row">
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Subject</label>
                                        <div class="form-line">
                                            <select class="form-control subject_id" name="subject_id[]" id="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @if (!empty($subjects))
                                                    @foreach ($subjects as $course)
                                                        <option value="{{ $course->id }}">{{ $course->subject_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-8">
                                        <label class="form-label">Home Work Details <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <textarea class="form-control" name="hw_description[]" rows="3" cols="30" required></textarea>
                                        </div>
                                        <div class="">
                                            <button type="button" class="btn btn-success add-subject-homework"><i class="fas fa-plus"></i></button>
                                            <button type="button" class="btn btn-danger delete-subject-homework"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Test </label>
                                <div class="form-line">
                                    <select class="form-control"  multiple="multiple"  name="test_id[]" id="test_dropdown" >
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
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

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Title<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="hw_title">
                                </div>
                            </div>--}}


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


                                    <!-- <input type="datetime-local" value="<?php echo date('Y-m-d\TH:i:s'); ?>" min="<?php echo date('Y-m-d'); ?>T00:00" required class="form-control" id="hw_date" name="hw_date"> -->

                                    <input type="datetime-local"  min="<?php echo date('Y-m-d'); ?>T00:00" required class="form-control" id="hw_date" name="hw_date" value="<?php echo date('Y-m-d H:i'); ?>">

                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label"> Submission Date</label>
                                <div class="form-line">
                                    <input type="datetime-local" value="<?php echo date('Y-m-d'); ?>T09:30:00" required class="form-control" min="<?php echo date('Y-m-d'); ?>T00:00" id="hw_submission_date" name="hw_submission_date">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Approval Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="approve_status">
                                        <option value="APPROVED">APPROVED</option>
                                        <option value="UNAPPROVED">UNAPPROVED</option>
                                    </select>
                                </div>
                            </div>

                            <!-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control"  name="position" min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status">
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div> -->
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Home Work</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/homeworkgrp') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">

                                    <select class="form-control " name="class_id" id="edit_class_id"
                                        onchange="loadClassSectionHw(this.value);loadClassSubjectsHw(this.value,'','',1,2);" required>
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
                                    <select class="form-control section_id" name="section_id" id="edit_section_dropdown" required  onchange="loadClassSubjectsHw(this.value,'','','',2);">

                                    </select>
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-6 d-none">
                                <div class="form-line">
                                    <input type="checkbox" name="sms_alert" id="edit_send_sms_checkbox">
                                    <label class="form-label" for="edit_send_sms_checkbox">Smart SMS</label>
                                </div>
                            </div>

                            <!--  onchange="testList(this.value,class_id.value,section_id.value);" -->
                            <div id="edit_subject-homework-container" class="col-md-12">
                                <div class="edit_subject-homework-row"> 
                                    <input type="hidden" name="subject_hw_id[]" id="edit_subject_hw_id_0" value="0">
                                    <div class="form-group form-float float-left col-md-6">
                                        <label class="form-label">Subject</label>
                                        <div class="form-line">
                                            <select class="form-control subject_id" name="edit_subject_id[]" id="edit_subject_id_0" required>
                                                <option value="">Select Subject</option>
                                                @if (!empty($subjects))
                                                    @foreach ($subjects as $course)
                                                        <option value="{{ $course->id }}">{{ $course->subject_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-6">
                                        <label class="form-label">Home Work Details <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <textarea class="form-control hw_description" name="hw_description[]" id="edit_hw_description_0"  rows="3" cols="30" required></textarea>
                                        </div>
                                        <div class="">
                                            <button type="button" class="btn btn-success edit_add-subject-homework"><i class="fas fa-plus"></i></button>
                                            <button type="button" class="btn btn-danger edit_delete-subject-homework"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Test</label>
                                <div class="form-line">
                                    <select class="form-control" multiple="multiple"  name="test_id[]" id="edit_test_dropdown"   >

                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
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

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Title<span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="hw_title" id="edit_hw_title">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Home Work Details <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <textarea name="hw_description[]" rows="3" cols="30" id="edit_hw_description" required></textarea>
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
                            <div class="form-group form-float float-left col-md-6 ">
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
                                    <input type="datetime-local" min="<?php echo date('Y-m-d'); ?>T00:00" class="form-control" name="hw_date" id="edit_hw_date" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label"> Submission Date</label>
                                <div class="form-line">

                                    <input type="datetime-local" min="<?php echo date('Y-m-d'); ?>T00:00" class="form-control" name="hw_submission_date" id="edit_hw_submission_date">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Approval Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="approve_status" id="edit_approve_status">
                                        <option value="APPROVED">APPROVED</option>
                                        <option value="UNAPPROVED">UNAPPROVED</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position"
                                        min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status">
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div> -->

                        </div>

                        <div class="modal-footer">
                            <button type="sumbit" class="btn btn-link waves-effect" id="edit_style">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>

                </form>
            </div>
        </div>
    </div>
    <input type="hidden" name="getFetchSectionURL" id="getFetchSectionURL"  value="{{ url('admin/fetch-section') }}">
    <input type="hidden" name="getFetchSubjectURL" id="getFetchSubjectURL" value="{{ url('admin/fetch-subject') }}">


@else 
@include('admin.notavailable') 
@endif

@endsection

@section('scripts') 
    <!-- <script src="{{asset('/public/js/homeworks.js')}}" type="text/javascript"></script> -->
    <script> 
        function deleteactivity(id){
            $('#filter_pagename').val($('#pagename').val());
            swal({

                title : "",
                text : "Are you sure to delete this from your Homework List?",
                type : "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",  
                    
                   
            },function(isConfirm){ 
                if(isConfirm) {
                        $('#filter_pagename').val($('#pagename').val());
                        var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('admin/delete/posthomeworks')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            post_id:id,
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if(response.status == 1)   {
                             swal('Success',response.message,'success');
                             filterProducts();
                         } else {
                             swal('warning',response.message,'warning');
                         }
                    
                    });
        
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });  
                }
            }); 
        } 


        $('#classid').on('change', function() {
            filterposts();
        }); 

        $('#sectionid').on('change', function() {
            filterposts();
        }); 

        $('#subjectid').on('change', function() {
            filterposts();
        });  

        $('#approval_status_id').on('change', function() {
            filterposts();
        });

        $("#datepicker_from").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        }).change(function() {
            filterposts();
        }).keyup(function() {
            filterposts();
        });

        $("#datepicker_to").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        }).change(function() {
            filterposts();
        }).keyup(function() {
            filterposts();
        });

        function filterposts() {
            $('#filter_pagename').val($('#pagename').val());
            var minDateFilter  = $('#datepicker_from').val();
            var maxDateFilter  = $('#datepicker_to').val();
            if(new Date(maxDateFilter) < new Date(minDateFilter))
            {
                alert('To Date must be greater than From Date');
                return false;
            }
             
            $('#filter_from_date').val(minDateFilter);
            $('#filter_to_date').val(maxDateFilter);
            $('#filter_category_id').val($('#category_id').val());
            $('#filter_search').val($('#search').val());

            $('#filter_status_id').val($('#approval_status_id').val());
            $('#filter_approval_status_id').val($('#approval_status_id').val());
            $('#filter_class_id').val($('#classid').val());
            $('#filter_section_id').val($('#sectionid').val());
            $('#filter_subject_id').val($('#subjectid').val()); 
                      

            filterProducts();
        }


        function updatestatus(obj, id) {
            var status = $(obj).val();
            if(status != '') {
                swal({
                        title: "Are you sure you want to change?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-info",
                        cancelButtonColor: "btn-danger",
                        confirmButtonText: "Yes!",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false
                        
                        
                       
                },function(inputValue){
                    if(inputValue===false) {
                          swal('Info',"Nothing done",'info');
                          
                          $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
                    }else{
                            $('#filter_pagename').val($('#pagename').val());
                            var request = $.ajax({
                            type: 'post',
                            url: " {{URL::to('admin/update/posthomeworks')}}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                post_id:id,status:status
                            },
                            dataType:'json',
                            encode: true
                        });
                        request.done(function (response) {
                            if(response.status == 1)   {
                                 swal('Success',response.message,'success');
                                 filterProducts();
                             } else {
                                 swal('warning',response.message,'warning');
                             }
                        
                        });
            
                        request.fail(function (jqXHR, textStatus) {

                            swal("Oops!", "Sorry,Could not process your request", "error");
                        });  
                    }
                });  
            }
        }
    </script>
 
    <script type="text/javascript">
        

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

                            filterposts();

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

                            filterposts();

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
                url: " {{ URL::to('admin/edit/homeworkgrp') }}",
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
                $('#edit_class_id').val(response.data.homeworks_list[0].class_id);

                var val = response.data.homeworks_list[0].class_id;
                var selectedid = response.data.homeworks_list[0].section_id;
                var selectedval = response.data.homeworks_list[0].is_section_name;
                loadClassSection(val, selectedid, selectedval);

                $('#edit_section_id').val(response.data.homeworks_list[0].section_id);
                $('#edit_subject_id').val(response.data.subject_id);
                //loadClassSubjectsHw(response.data.homeworks_list[0].section_id);

                //testList(response.data.subject_id,response.data.class_id,response.data.is_test_id, response.data.is_test_id)

                //$('#edit_test_dropdown').val(response.data.is_test_id);
                // response.data.teachers.is_subject_id
                // $('#edit_period').val(response.data.period);
                $('#edit_hw_title').val(response.data.hw_title);
                $('#edit_hw_description').val(response.data.hw_description);
                $('#edit_hw_date').val(response.data.homeworks_list[0].hw_date);
                $('#edit_hw_submission_date').val(response.data.homeworks_list[0].hw_submission_date);
                //$('#edit_position').val(response.data.position);
                //$('#edit_status').val(response.data.status);
                $('#edit_approve_status').val(response.data.homeworks_list[0].approve_status);

                if (response.data.homeworks_list[0].hw_attachment != '' && response.data.homeworks_list[0].hw_attachment != null) {
                    $('#edit-style-form .hw_view_file').removeClass('d-none');
                    $('#edit-style-form #hw_view_file').attr('href', response.data.homeworks_list[0].is_hw_attachment);
                    $('#edit-style-form #is_hw_attachment').val(response.data.homeworks_list[0].is_hw_attachment);
                }

                if (response.data.homeworks_list[0].dt_attachment != '' && response.data.homeworks_list[0].dt_attachment != null) {
                    $('#edit-style-form .dt_view_file').removeClass('d-none');
                    $('#edit-style-form #dt_view_file').attr('href', response.data.homeworks_list[0].is_dt_attachment);
                    $('#edit-style-form #is_dt_attachment').val(response.data.homeworks_list[0].is_dt_attachment);
                } 

                if(response.data.homeworks_list[0].is_sms_alert == 0) {
                    $('#edit_send_sms_checkbox').prop('checked', false);
                }   else if(response.data.homeworks_list[0].is_sms_alert == 1) {
                    $('#edit_send_sms_checkbox').prop('checked', false);
                }  

                /*var len2 = $('.edit_subject-homework-row').length;
                var len1 = 0;
                $.each(response.data.homeworks_list, function(key, value) { 
                    len1 = len1+1; 

                    if(key != 0) {
                        $('.edit_add-subject-homework').trigger('click');
                    }
                }); */

                $('#edit_subject-homework-container').html(response.content);

                $('#smallModal-2').modal('show'); 

                /*$.each(response.data.homeworks_list, function(key, value) {
                    console.log('.edit_subject-homework-row .subject_id' + key + '=='+ value.subject_id)
                    key = parseInt(key);
                    $('.edit_subject-homework-row .subject_id').eq(key).val(value.subject_id); 


                    console.log($('.edit_subject-homework-row .subject_id').eq(key).html())
                    
                    $('.edit_subject-homework-row .hw_description').eq(key).val(value.hw_description);
                }); */

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }



        function myFunction(val, selectedid, selectedval) {

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
                    if (selid != null && selval != null) {

                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');

                    } else {
                        $('#section_dropdown').html(
                            '<option value="">-- Select Section --</option>');
                    }
                    $.each(res.section, function(key, value) {
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '">' + value.section_name + '</option>');
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
                url: "{{ url('admin/fetch-tests') }}",
                type: "POST",
                data: {
                    subject_id: subject_id,
                    class_id:class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#test_dropdown').html('<option value="">-- Select Test --</option>');
                    $.each(res.tests, function(key, value) {
                        var selected = '';
                        var arr = selectedid.toString().split(',');
                        var result = arr.map(function (x) {
                            return parseInt(x, 10);
                        });
                        if(result.indexOf(value.id) !== -1) {
                            selected = ' selected ';
                        }
                        $("#test_dropdown,#edit_test_dropdown").append('<option value="' + value.id + '" '+selected+'>' + value.test_name + ' '+ value.from_date + ' to '+ value.to_date + '</option>');
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


        function loadClassSectionHw(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $(".section_id").html('');
            $.ajax({
                url: $('#getFetchSectionURL').val(),
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('.section_id').html(
                        '<option value="-1">-- All Section --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $(".section_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadClassSubjectsHw(val, selectedid, selectedval, isclass, from) {
 
            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            isclass = isclass || 0;
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;
            if(section_id < 0) {
                isclass = 1;
                if(from == 1) {
                    section_id = $('#class_id').val();
                } else if(from == 2) {
                    section_id = $('#edit_class_id').val();
                }
            }   
            console.log(section_id);  console.log(from)
            
            $(".subject_id").html('');
            $.ajax({
                url: $('#getFetchSubjectURL').val(),
                type: "POST",
                data: {
                    section_id: section_id,isclass:isclass,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('.subject_id').html(
                        '<option value="">-- Select Subject --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.subjects, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $(".subject_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }

        //$('#edit_hw_date').change(); 

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


        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('edit_subject-homework-container');
            const checkbox = document.getElementById('edit_send_sms_checkbox');
            let maxRows = 3; // Default value

            function edit_updateMaxRows() {
                maxRows = checkbox.checked ? 3 : 6;
                edit_toggleAddButton();
            }

            container.addEventListener('click', function(event) {
                const clickedButton = event.target.closest('button');
                if (!clickedButton) return;

                if (clickedButton.classList.contains('edit_delete-subject-homework')) {
                    if (container.querySelectorAll('.edit_subject-homework-row').length > 1) {
                        clickedButton.closest('.edit_subject-homework-row').remove();
                        edit_toggleAddButton();
                    }
                } else if (clickedButton.classList.contains('edit_add-subject-homework')) {



                    if (edit_checkDuplicateSubjects()) {
                        swal('Oops', 'This subject has already been selected.', 'warning');
                        return;
                    }

                    const newRow = container.querySelector('.edit_subject-homework-row').cloneNode(true);

                    // Clear the values of the cloned row
                    newRow.querySelectorAll('input, select, textarea').forEach(function(element) {
                        //element.value = '';
                    });

                    // Add event listener to the new select element
                    newRow.querySelector('select.subject_id').addEventListener('change', function() {
                        if (edit_checkDuplicateSubjects()) {
                            swal('Oops', 'This subject has already been selected.', 'warning');
                            this.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    edit_toggleAddButton();
                }
            });

            function edit_toggleAddButton() {
                const rows = container.querySelectorAll('.edit_subject-homework-row');
                const addButton = container.querySelectorAll('.edit_add-subject-homework');
                addButton.forEach(function(button) {
                    button.disabled = rows.length >= maxRows;
                    button.classList.toggle('disabled', rows.length >= maxRows);
                });
            }

            function edit_checkDuplicateSubjects() {
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
                    if (edit_checkDuplicateSubjects()) {
                        swal('Oops', 'This subject has already been selected.', 'warning');
                        this.value = '';
                    }
                });
            });

            // Event listener for checkbox state change
            checkbox.addEventListener('change', edit_updateMaxRows);

            // Initial call to set the add button state
            edit_updateMaxRows();
        });  

    </script>
@endsection

