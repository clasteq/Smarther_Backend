@extends('layouts.admin_master')
@section('questionbank_settings', 'active')
@section('master_questionbank', 'active')
@section('menuopenq', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/questionbank'), 'name'=>'Question Bank', 'active'=>''], ['url'=>URL('/admin/addquestionbank'), 'name'=>'Add Question Bank', 'active'=>'active'] ];
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
                                <h4 style="font-size: 20px;" class="panel-title">Add Question Bank 
                                </h4> 
                                <div class="row"><div class="col-md-12">
                                    <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/admin/save/questionbank')}}"> 
                                    {{csrf_field()}}
                                    <div class="row">
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line">
                                                <select class="form-control" name="class_id" id="class_id" required onchange="loadClassSubjects(this.value,'','',1); loadClassTerms(this.value);">
                                                    <option value="">Select Class</option>
                                                    @if(!empty($classes))
                                                        @foreach($classes as $class)
                                                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line">
                                                <select class="form-control" name="term_id" id="term_id" required onchange="loadChapterOptions()">
                                                    <option value="">Select Term</option>
                                                    @if(!empty($terms))
                                                        @foreach($terms as $term)
                                                            <option value="{{$term->id}}">{{$term->term_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line">
                                                <select class="form-control" name="subject_id" id="subject_id" required onchange="loadChapterOptions(this.value)">
                                                    <option value="">Select Subject</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Chapter</label> &nbsp;&nbsp;&nbsp; <a href="{{URL('/admin/chapters')}}" title="Add New Chapter" style="font-size:15px;">Add Chapter</a>
                                            <div class="form-line">
                                                <select class="form-control" name="chapter_id" id="chapter_dropdown" required onchange="checkChapterQb(this.value)">
                                                    <option value="">Select Chapter</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Question Bank Name</label>
                                            <div class="form-line">
                                              <input type="text" class="form-control" name="qb_name" id="qb_name">
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Upload Notes</label>
                                            <div class="form-line">
                                              <input type="file" class="form-control" name="notes" id="notes">
                                            </div>
                                        </div>
                                    </div> 
                                    <hr>
                                    <!-- Start Question types -->
                                    @if(!empty($question_types))
                                        @foreach($question_types as $qtype)
                                        @if(isset($qtype['questiontype_settings'])) 
                                        @php($i=1)
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">@if(isset($qtype['questiontype_settings']['question_type_id']) && $qtype['questiontype_settings']['question_type_id']>0) {{$qtype['question_type']}} @else Others @endif

                                            @if($qtype['questiontype_settings']['question_type_id'] == 17) 
                                             [ Use Comma(,) for separation ]
                                            @elseif($qtype['questiontype_settings']['question_type_id'] == 7) 
                                             [ Use Space for separation ]
                                            @endif
                                            </label> 

                                            <button type="button" class="btn btn-success center-block plus" id="plus_{{$qtype['id']}}" data-id="{{$qtype['id']}}">+</button> 
                                            @if($qtype['questiontype_settings']['question_type_id'] == 11) 
                                            <!-- Accepted File Formats are : png,jpeg,jpg,doc,docx,mp3,mp4,pdf -->
                                            @endif
                                        </div>

                                        <div id="items_{{$qtype['id']}}" class="col-md-12 row">@include('admin.loadquestiontype')</div>
                                        @else 
                                        <div class="row" id="items_{{$qtype['id']}}"> 
                                            <div class="form-group form-float float-left col-md-6">
                                                <label class="form-label"> Others </label>  <button type="button" class="btn btn-success center-block plus" id="plus_{{$qtype['id']}}"  data-id="{{$qtype['id']}}">+</button> 
                                            </div>
                                            <div class="form-group form-float float-left col-md-12"> 
                                            <div class="form-group form-float float-left col-md-4"> 
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="oquestion_type[{{$qtype['id']}}][]" id="oquestion_type_{{$qtype['id']}}_{{$i}}" placeholder="Question Type">
                                                </div> 
                                            </div>
                                            <div class="form-group form-float float-left col-md-4"> 
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="oquestion[{{$qtype['id']}}][]" id="oquestion_{{$qtype['id']}}_{{$i}}" placeholder="Question">
                                                </div> 
                                            </div>
                                            <div class="form-group form-float float-left col-md-4"> 
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="oanswer[{{$qtype['id']}}][]" id="oanswer_{{$qtype['id']}}_{{$i}}" placeholder="Answer">
                                                </div> 
                                            </div>
                                            <div class="form-group form-float float-left col-md-8"> 
                                                <div class="form-line">
                                                    Hint File : <input type="file" class="form-control" name="ohint_file[{{$qtype['id']}}][]" id="ohint_file_{{$qtype['id']}}_{{$i}}" placeholder="Hint File">
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                        @endif
                                        @endforeach
                                    @endif
                                    <!-- End Question Types -->

                                    <button type="submit" class="btn btn-success center-block" id="Submit">Submit</button> 
                                    </form>
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
                    url: " {{URL::to('admin/clone/questiontype')}}",
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

                var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                           swal('Success','Question bank Saved Successfully','success');

                           //window.location.reload();
                           window.location.href = "{{URL('/')}}/admin/questionbank";

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

    </script>
 

@endsection

