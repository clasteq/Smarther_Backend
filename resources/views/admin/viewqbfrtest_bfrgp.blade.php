@extends('layouts.admin_master')
@section('test_settings', 'active')
@section('master_testlist', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/testlist'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'Add Manual Test', 'active'=>'active'] ];
?>
@section('content')
 <?php // echo "<pre>".$err;  print_r($qb); ?>
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
                                <h4 style="font-size:20px;" class="panel-title">Add Test
                                </h4>
                                <div class="row"><div class="col-md-12">
                                    <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/admin/save/qbtest')}}">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <input type="hidden" name="class_id" id="class_id" value="{{$qbank[0]->class_id}}">
                                        <input type="hidden" name="subject_id" id="subject_id" value="{{$qbank[0]->subject_id }}">
                                        <input type="hidden" name="term_id" id="term_id" value="{{$qbank[0]->term_id }}">

                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">From</label>
                                            <input class="col-md-6 date_range_filter date form-control" required type="text" autocomplete="off" name="from_date" id="datepicker_from"  />
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">To</label>
                                            <input class="col-md-6 date_range_filter date form-control" required type="text" autocomplete="off" name="to_date" id="datepicker_to"  />
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line"> {{$qbank[0]->class_name }}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line"> {{$qbank[0]->subject_name }}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line"> {{$qbank[0]->term_name }}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test name</label>
                                            <?php $chaptername = '';
                                            if(!empty($qbank) && count($qbank)>0){
                                                foreach($qbank as $qid=>$qb) {
                                                    $chaptername .= $qb->chaptername .' ';
                                                }
                                            }

                                            $test_name = $chaptername; //date('Y-m-d').' '.?>
                                            <div class="form-line">
                                         <input class="col-md-8 form-control" type="text" name="test_name" id="test_name" value="{{$test_name}}" required style="width:100%;">
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test Mark</label>
                                            <div class="form-line">
                                            <input class="col-md-6 form-control" required type="text" autocomplete="off" name="test_mark" id="test_mark"  /> 
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test Time (in minutes)</label>
                                            <div class="form-line">
                                            <input class="col-md-6 form-control" required type="text"  name="test_time" id="test_time" onkeypress="return isNumber(event)"  /> 
                                            </div>
                                        </div>
                                        {{-- <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Remarks</label>
                                            <div class="form-line">
                                            </div>
                                        </div> --}}
                                    </div>
                                    <hr>
                                     <!-- Start Question types -->
                                    @if(!empty($items) && count($items)>0) 
                                        @foreach($items as $qid=>$qtype)
                                        @if(isset($qtype))
                                        <?php
                                        $question_type = 0;
                                        $qtype = (array) $qtype;
                                        $question_type = $qtype['question_type_id'];
                                        ?>
                                             <input type="hidden" name="chapter_id[]" id="chapter_id" value="{{$qb->chapter_id }}">
                                             <div class="row">
                                             <div class="form-group form-float float-left col-md-2">
                                                <input type="hidden" value="{{$qtype['question_type']}}" id="question_id">
                                           <input class="newopration_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}} operation" type="checkbox" data-qbid="{{$qtype['question_bank_id']}}"  data-qtyid="{{$question_type}}" data-qtype="{{$qtype['question_type']}}"
                                                 name="question_type[]" id="question_type" value="{{$question_type}}"> <label class="form-label"> {{$qtype['question_type']}}
                                               </label>
                                            </div>
                                            <div class="form-group form-float float-left col-md-9">
                                                <label class="form-label">
                                                </label> <input type="text" class="tot_mark total_markk_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}} col-md-2" id = "tot_mark_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}}"  placeholder="Total" data-qbid="{{$qtype['question_bank_id']}}"  data-qtyid="{{$qtype['question_type_id']}}"  data-qtype="{{$qtype['question_type']}}" name="tot_mark" >
                                            </div>

                                        </div>
                                             @php($i=1)

                                            @foreach($qtype['qb_items'] as $item) <?php echo "<pre>"; print_r($item); echo "</pre>";//exit;?>
                                                <div class="row">
                                                    <div class="form-group form-float float-left col-md-2"> 
                                                        <input type="checkbox" class="questions_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}} markquestion_{{$item->id}} markquestion_{{$item->id}}  newquestion" name="question_item_id[{{$item->id}}]"
                                                        data-qbid="{{$qtype['question_bank_id']}}"  data-qtyid="{{$qtype['question_type_id']}}"   data-qtype="{{$qtype['question_type']}}"
                                                        id="question_item_id" value="{{$item->id}}">
                                                        <input class="newmarks_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}} newmm_{{$item->id}}"  data-qbid="{{$qtype['question_bank_id']}}"  data-qtyid="{{$qtype['question_type_id']}}"  type="text" name="marks[{{$item->id}}]" id="marks_{{$item->id}}" style="width: 40%;" min="1" maxlength="3" max="200" placeholder="mark">
                                                    </div>
                                                    @if($item->question_type_id != 16)
                                                    <div class="form-group form-float float-left col-md-10">
                                                        {{$item->question}} - {{$item->answer}}
                                                    </div>
                                                    @elseif ($item->question_type_id == 16)
                                                <?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->question; ?>
                                                    <div class="form-group form-float float-left col-md-10">
                                                        <img src="{{$fileurl}}" height="150" width="150"> &nbsp;&nbsp;&nbsp; -  &nbsp;&nbsp;{{$item->answer}}
                                                    </div>
                                                    @endif
                                                </div>
                                                @php($i++)
                                            @endforeach
                                            <?php

                                            ?>
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
      <script>

        $(function() {
            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });

            $("#datepicker_to").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            });
            $("#datepicker_from").datepicker('setDate', new Date())
            $("#datepicker_to").datepicker('setDate', new Date())

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

                           swal('Success','Test Saved Successfully','success');

                           window.location.href = "{{URL('/')}}/admin/testlist";

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


        $('.operation').click(function() {

      if($(this).prop('checked')){

     var checked_val = this.value;
     var qbid =  $(this).data('qbid');
     var qtyid =  $(this).data('qtyid');
     var qtype =  $(this).data('qtype');

       /*$.ajax({
                url: "{{ url('admin/fetch-questions') }}",
                type: "POST",
                data: {
                    questions: checked_val,
                    qbid:qbid,
                    qtyid : qtyid,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
               $.each(res.questions, function(key, value) {
                $("input[value='" + value.id + "']").prop('checked', true);

                });


                }
            });*/

            $("input[type='checkbox'][data-qtype='" + qtype + "']").prop('checked', true); 

        }
        else{
        var qbid =  $(this).data('qbid');
        var qtyid =  $(this).data('qtyid');
        var qtype =  $(this).data('qtype');
        var checked_val = this.value;

        $('.newmarks_'+qbid+'_'+qtyid).val('');

         /*$.ajax({
                url: "{{ url('admin/fetch-questions') }}",
                type: "POST",
                data: {
                    questions: checked_val,
                    qbid:qbid,
                    qtyid : qtyid,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
               $.each(res.questions, function(key, value) {

                $("input[value='" + value.id + "']").prop('checked', false);

                });
                $('.total_markk_'+qbid+'_'+qtyid).val('');

                }
            });*/

            $("input[type='checkbox'][data-qtype='" + qtype + "']").prop('checked', false); 

        }

        });


        $('.newquestion').click(function() {

var qbid =  $(this).data('qbid');
var qtyid =  $(this).data('qtyid');
var qtype =  $(this).data('qtype');
 if($(this).prop('checked')){

 var questions
 var  count=0 ;
 jQuery.each(jQuery('.questions_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]:checked'), function() {
     count++;

 });

}

var actualcount = count;

jQuery.each(jQuery('.questions_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]:not(:checked)'), function() {
     count++;

 });
var newcount = count;
 if(newcount == actualcount){

var checked_val = this.value;
      $.ajax({
             url: "{{ url('admin/fetch-questions-type') }}",
             type: "POST",
             data: {
                 questions: checked_val,
                 qbid:qbid,
                 qtyid : qtyid,
                 _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
            $.each(res.questions, function(key, value) {
                $("input.newopration_"+qbid+'_'+qtyid+"[value='" + value.question_type_id + "'][data-qtype='"+qtype+"']").prop('checked', true);
             });


             }
         });

 }
 else{
     var checked_val = this.value;
     $('.newmarks_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val('');
      $.ajax({
             url: "{{ url('admin/fetch-questions-type') }}",
             type: "POST",
             data: {
                 questions: checked_val,
                 qbid:qbid,
                 qtyid : qtyid,
                 _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
           $.each(res.questions, function(key, value) {
            $("input.newopration_"+qbid+'_'+qtyid+"[value='" + value.question_type_id + "'][data-qtype='"+qtype+"']").prop('checked', false);
             });
             $('.total_markk_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val('');

             }
         });
 }




});





$(".tot_mark").keyup(function(){
   var tot_mark = this.value;
   var qbid =  $(this).data('qbid');
   var qtyid =  $(this).data('qtyid');
   var qtype =  $(this).data('qtype');
   var answerss = [];
    var questions
    var  count=0 ;
    jQuery.each(jQuery('.questions_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]:checked'), function() {
        count++;
        answerss.push(jQuery(this).val());
       
    });
  var new_length = answerss.length;
     $('.questions_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]:checked').each(function() {
      var answers = $(this).val();
       tot = tot_mark / new_length;
       $('.newmm_'+answers).val(tot.toFixed(2));
       count++;
    //   $("input.operation[value='" + answer + "']").prop('checked', true);
});


});



    </script>


@endsection

