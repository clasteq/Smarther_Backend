@extends('layouts.admin_master')
@section('questionbank_settings', 'active')
@section('master_questionbank', 'active')
@section('menuopenq', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/questionbank'), 'name'=>'Question Bank', 'active'=>''], ['url'=>URL('/admin/addquestionbank'), 'name'=>'Edit Question Bank', 'active'=>'active'] ];
?>
@section('content')
 <?php //echo "<pre>"; print_r($qb); exit; ?>
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid"> 
            <div class="panel"> 
                <!-- Panel Heading -->
                <div class="panel-heading"> 
                    <!-- Panel Title -->
                    <div class="panel-title">
                    </div> 
                </div>
                <div class="panel-body">  
                    <div class="row"> 
                        <div class="col-xs-12 col-md-12"> 
                        <div class="card"> 
                            <div class="card-body">
                               
                                <h4 style="font-size:20px;" class="card-title">Edit Question Bank</h4>
                                <br><br>
                                <div class="row"><div class="col-md-12">
                                    <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/admin/save/questionbank')}}"> 
                                    {{csrf_field()}}
                                    <input type="hidden" name="question_bank_id" id="question_bank_id" value="{{$id}}">
                                    <div class="row">
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line">
                                                <select class="form-control" name="class_id" id="class_id" required onchange="loadClassSubjects(this.value,'','',1); loadClassTerms(this.value);">
                                                    <option value="">Select Class</option>
                                                    @if(!empty($classes))
                                                        @foreach($classes as $class)
                                                            @php($selected = '')
                                                            @if($qb['class_id'] == $class->id)
                                                            @php($selected = 'selected')
                                                            @endif
                                                            <option value="{{$class->id}}" {{$selected}}>{{$class->class_name}}</option>
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
                                            <label class="form-label">Chapter</label>
                                            <div class="form-line">
                                                <select class="form-control" name="chapter_id" id="chapter_dropdown" required onchange="checkChapterQb(this.value)">
                                                    <option value="">Select Chapter</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Question Bank Name</label>
                                            <div class="form-line">
                                              <input type="input" value="{{$qb['qb_name']}}" class="form-control" name="qb_name" id="qb_name">
                                          </div>
                            	       </div>
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Upload Notes</label>
                                            <div class="form-line">
                                              <input type="file" class="form-control" name="notes" id="notes">
                                              @if(!@empty($qb['notes']))
                                              <?php $fileurl = config("constants.APP_IMAGE_URL").'image/notes/'.$qb['notes']; ?>
                                              <br>
                                              <input type="hidden" name="notes_file" value="{{$qb['notes']}}">
			                      <a href={{$fileurl}} class="btn btn-primary" target="_blank">Download file</a>
                                  @endif
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
                                            </label>  <button type="button" class="btn btn-success center-block plus" id="plus_{{$qtype['id']}}" data-id="{{$qtype['id']}}">+</button> 
                                            
                                            @if($qtype['questiontype_settings']['question_type_id'] == 11) 
                                            <!-- Accepted File Formats are : png,jpeg,jpg,doc,docx,mp3,mp4,pdf -->
                                            @endif
                                        </div>

                                        <div id="items_{{$qtype['id']}}" class="col-md-12 row">
                                            <!-- Start Question types -->
                                            @if(!empty($qb['questionbank_items']) && count($qb['questionbank_items'])>0)
                                                @foreach($qb['questionbank_items'] as $qid=>$qtype1)  
                                                @if(isset($qtype1))  
                                                    @if($qtype['questiontype_settings']['question_type_id'] == $qtype1['question_type_id']) 
                                                    @php($i=1)
                                                    @foreach($qtype1['qb_items'] as $item)
                                                        @include('admin.editloadquestionbank')
                                                         <?php //echo "<pre>"; print_r($item); exit;?>   
                                                        <!-- <div class="form-group form-float float-left col-md-12">
                                                            <input type="hidden" name="sno[]" id="sno_{{$i}}" value="{{$i}}">
                                                            <input type="hidden" name="qb_item_id[]" id="qb_item_id_{{$item->id}}" value="{{$item->id}}" >
                                                            <div class="form-group form-float float-left col-md-6"> 
                                                                <div class="form-line">
                                                                    <input type="text" class="form-control" name="question[{{$qtype1['question_type_id']}}][]" id="question_{{$qtype1['question_type_id']}}_{{$i}}" placeholder="Question" value="{{$item->question}}">
                                                                </div> 
                                                            </div>
                                                            <div class="form-group form-float float-left col-md-6"> 
                                                                <div class="form-line">
                                                                    <input type="text" class="form-control" name="answer[{{$qtype1['question_type_id']}}][]" id="answer_{{$qtype1['question_type_id']}}_{{$i}}" placeholder="Answer" value="{{$item->answer}}">
                                                                </div> 
                                                            </div>
                                                        </div>  -->
                                                        @php($i++)
                                                    @endforeach
                                                    @endif
                                                @endif 
                                                @endforeach
                                            @endif
                                            <!-- End Question Types --> 
                                            @include('admin.editloadquestiontype')</div>
                                        @else 
                                        <div class="row" id="items_{{$qtype['id']}}"> 
                                            <div class="form-group form-float float-left col-md-6">
                                                <label class="form-label"> Others </label>  <button type="button" class="btn btn-success center-block plus" id="plus_{{$qtype['id']}}"  data-id="{{$qtype['id']}}">+</button> 
                                            </div>
                                            <!-- Start Question types -->
                                            @if(!empty($qb['questionbank_items']) && count($qb['questionbank_items'])>0)
                                                @foreach($qb['questionbank_items'] as $qid=>$qtype1)  
                                                @if(isset($qtype1))  
                                                {{-- {{$qtype1['questiontype_settings']['question_type_id']}} --}}
                                                    @if($qtype1['questiontype_settings']['question_type_id'] == 0) 
                                                    @php($i=1)
                                                    @foreach($qtype1['qb_items'] as $item)
                                                         <?php //echo "<pre>"; print_r($item); exit;?> 
                                                            <input type="hidden" name="oqb_item_id[{{$qtype1['questiontype_settings']['id']}}][]" id="oqb_item_id_{{$item->id}}" value="{{$item->id}}" > 
                                                            {{-- <input type="hidden" name="sno[]" id="sno_{{$i}}" value="{{$i}}"> --}}
	
                                                        <div class="form-group form-float float-left col-md-12"> 
                                                            <div class="form-group form-float float-left col-md-4">
                                                            <div class="form-line">
                                                                <input type="text" class="form-control" name="oquestion_type[{{$qtype1['questiontype_settings']['id']}}][]" id="oquestion_type_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question Type" value="{{$item->question_type}}">
                                                            </div> 
                                                        </div>
                                                        {{-- <div class="form-group form-float float-left col-md-12">  --}}
                                                           
                                                            <div class="form-group form-float float-left col-md-4"> 
                                                                <div class="form-line">
                                                                    <input type="text" class="form-control" name="oquestion[{{$qtype1['questiontype_settings']['id']}}][]" id="oquestion_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Question" value="{!!$item->question!!}">
                                                                </div> 
                                                            </div>
                                                            <div class="form-group form-float float-left col-md-3"> 
                                                                <div class="form-line">
                                                                    <input type="text" class="form-control" name="oanswer[{{$qtype1['questiontype_settings']['id']}}][]" id="oanswer_{{$qtype1['questiontype_settings']['id']}}_{{$i}}" placeholder="Answer" value="{!!$item->answer!!}">
                                                                </div> 
                                                            </div>
                                                            <div class="form-group form-float float-left col-md-8"> 
                                                                <div class="form-line">
                                                                    Hint File : <input type="file" class="form-control" name="ohint_file[{{$qtype['id']}}][]" id="ohint_file_{{$qtype['id']}}_{{$i}}" placeholder="Hint File">
                                                                    @if(!empty($item->hint_file))
                                                                    <?php $fileurl = config("constants.APP_IMAGE_URL").'image/qb/'.$item->hint_file; ?>
                                                                    <img src="{{ $fileurl }}" width="90%"  style="    max-height: 300px;">

                                                                    @endif
                                                                </div> 
                                                            </div>
                                                            <div class="form-group form-float float-left col-md-1"> 
                                                                <div class="form-line">
                                                                <a style="color:red;cursor:pointer"  onclick="delete_question({{$item->id}})"><i class="fas fa-trash"></i></a>
                                                                </div> 
                                                            </div>
                                                        </div> 
                                                        @php($i++)
                                                    @endforeach
                                                    @endif
                                                @endif 
                                                @endforeach
                                            @endif
                                            <!-- End Question Types --> 
                                            <div class="form-group form-float float-left col-md-12">
                                                {{-- <input type="hidden" name="oqb_item_id[]" id="oqb_item_id_{{$i}}" value="0" > --}}
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
      <script type="text/javascript">
        $(function() { 

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

                           window.location.reload();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseJSON.message); //console.log(textStatus); console.log(errorThrown);
                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        swal('Oops',jqXHR.responseJSON.message,'error');

                    }
                };
                $("#frm_questionbank").ajaxForm(options);
            }); 

            @if($qb['class_id'] > 0) 
            loadClassSubjects('{{$qb['class_id']}}','{{$qb['subject_id']}}','',1); 
            loadClassTerms('{{$qb['class_id']}}','{{$qb['term_id']}}');
            $('#subject_id').val({{$qb['subject_id']}});
            $('#class_id').val({{$qb['class_id']}});
            $('#term_id').val({{$qb['term_id']}});  
            loadChapterOptions('{{$qb['subject_id']}}','{{$qb['chapter_id']}}','','{{$qb['term_id']}}') 
            @endif  
        });


        function delete_question(item_id){
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
                    url: " {{URL::to('admin/delete/individualquestion')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        item_id:item_id,
                    },
                    dataType:'json',
                    encode: true
                });
                request.done(function (response) { 
                   if (response.status == "SUCCESS") {

    swal({title: "Success", text: response.message, type: "success"},
        function(){
            window.location.reload();
           
        }
    );

} else if (response.status == "FAILED") {

    swal('Oops', response.message, 'warning');

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

