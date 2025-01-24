@extends('layouts.teacher_master')
@section('test_settings', 'active')
@section('master_autoaddtestlist', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/teacher/testlist'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'Auto Test Creation', 'active'=>'active'] ];
?>
@section('content')
 <?php
//   echo "<pre>".$err;  print_r($qb_id); ?>
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
                                <h4 style="font-size:20px;" class="panel-title">Auto Test Creation
                                </h4>
                                <div class="row"><div class="col-md-12">
                                    <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/teacher/save/qbautotest')}}">
                                    {{csrf_field()}}
                                    <div class="row">
                                    <input type="hidden" name="class_id" id="class_id" value="{{$qbank[0]->class_id}}">
                                        <input type="hidden" name="subject_id" id="subject_id" value="{{$qbank[0]->subject_id }}">
                                        <input type="hidden" name="term_id" id="term_id" value="{{$qbank[0]->term_id }}">
                                        @if(isset($qb_id))
                                        @foreach($qb_id as $value)
                                        <input type="hidden" name="qb_id[]" value="{{$value}}" >
                                        @endforeach
                                        @endif
                                        

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
                                        
                                        <!-- <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Remarks</label>
                                            <div class="form-line">
                                            </div>
                                        </div> -->
                                    </div>
                                    <hr>
                                     <!-- Start Question types -->
                                   
                                        <!-- <input type="hidden" name="chapter_id[]" id="chapter_id" value="{{$qb->chapter_id}}"> -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3 float-left">Type</div>
                                                <div class="col-md-2 float-left">Avail Quest</div>
                                                <div class="col-md-2 float-left">No of Quest</div>
                                                <div class="col-md-2 float-left">Mark per Quest</div>
                                                <div class="col-md-3 float-left">Total Mark</div>
                                            </div>
                                        </div>
                                        <br>
                                        @php
                                        $questions = 0;
                                        @endphp
                                        @if(!empty($items) && count($items)>0) 
                                        @foreach($items as $qid=>$qtype)
                                     
                                        @if(isset($qtype))
                                        <input type="hidden" name="chapter_id[]" id="chapter_id" value="{{$qb->chapter_id}}">

                                     
                                        
<div class="row">
    <div class="col-md-12">
        <div class="col-md-3 float-left"><label class="form-label">{{$qtype->question_type}}</label></div>
        <div class="col-md-2 float-left">{{count($qtype->qb_items)}}</div>

        
        

        <input type="hidden" name="tot_ques[{{$qtype->question_type_id}}_{{$qtype->question_bank_id}}_{{$qtype->question_type}}]"
        value="{{count($qtype->qb_items)}}" class="tot_ques_{{$qtype->question_bank_id}}_{{$qtype->question_type_id}}"  data-qbid="{{$qtype->question_bank_id}}"  data-qtyid="{{$qtype->question_type_id}}" data-qtype="{{$qtype->question_type}}">
        <div class="col-md-2 float-left" style="padding: 5px;">

           <input type="text" name="noofquest[{{$qtype->question_type_id}}_{{$qtype->question_bank_id}}_{{$qtype->question_type}}]" class="form-control noofquest newnoofquest_{{$qtype->question_bank_id}}_{{$qtype->question_type_id}}"  id="noofquest" data-qbid="{{$qtype->question_bank_id}}"  data-qtyid="{{$qtype->question_type_id}}" value="{{count($qtype->qb_items)}}" data-qtype="{{$qtype->question_type}}"></div>
        <div class="col-md-2 float-left" style="padding: 5px;">

        <input type="text" data-qbid="{{$qtype->question_bank_id}}"  data-qtyid="{{$qtype->question_type_id}}" name="marksperquest[{{$qtype->question_type_id}}_{{$qtype->question_bank_id}}_{{$qtype->question_type}}]" id="marksperquest" class="marksperquest newmarksperquest_{{$qtype->question_bank_id}}_{{$qtype->question_type_id}} form-control" data-qbid="{{$qtype->question_bank_id}}"  data-qtyid="{{$qtype->question_type_id}}"  value="1" data-qtype="{{$qtype->question_type}}"></div>

        <div class="col-md-2 float-left" id="">
            <input type="text" readonly name="total_mark[{{$qtype->question_type_id}}_{{$qtype->question_bank_id}}_{{$qtype->question_type}}]" id="total_mark" class="total_mark newtotal_mark_{{$qtype->question_bank_id}}_{{$qtype->question_type_id}} form-control" data-qbid="{{$qtype->question_bank_id}}"  data-qtyid="{{$qtype->question_type_id}}" value="{{count($qtype->qb_items) *1}}" data-qtype="{{$qtype->question_type}}">
            </div>
    </div>
</div>
                                        @endif
                                        @php
                                          $questions += count($qtype->qb_items);

                                        @endphp
                                        @endforeach
                                        @endif
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3 float-left"><b>Total No of Questions</b></div>
                                                
                                                <div class="col-md-2 float-left">{{$questions}}</div>
                                                {{-- <div class="col-md-2 float-left"><b>Total Mark</b></div>
                                                <div class="col-md-3 float-left"></div> --}}
                                            </div>
                                        </div>
                                   
                                    <br>
                                 
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

                           window.location.href = "{{URL('/')}}/teacher/testlist";

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

$(".noofquest").keyup(function(){
    var qbid =  $(this).data('qbid');
    var qtyid =  $(this).data('qtyid');
    var qtype =  $(this).data('qtype');
    var total_ques = parseInt($('.tot_ques_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val(), 10) || 0;
    var tot_question = parseInt(this.value, 10) || 0;
   if(total_ques >= tot_question){
        
    var mark_per_que = $('.newmarksperquest_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val();
    tot_mark = tot_question * mark_per_que;
   $('.newtotal_mark_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val(tot_mark)
}
    else{
      
        swal('Oops','Available Questions only '+total_ques,'error');
    }
});

$(".marksperquest").keyup(function(){

    var qbid =  $(this).data('qbid');
    var qtyid =  $(this).data('qtyid');
    var qtype =  $(this).data('qtype');
    var tot_question = this.value;
    var mark_per_que = $('.newnoofquest_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val();
    tot_mark = tot_question * mark_per_que;
   $('.newtotal_mark_'+qbid+'_'+qtyid+'[data-qtype="'+qtype+'"]').val(tot_mark)

});

    </script>


@endsection

