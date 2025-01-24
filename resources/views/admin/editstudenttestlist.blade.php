@extends('layouts.admin_master')
@section('report_settings', 'active')
@section('master_studentstest', 'active')
@section('menuopenr', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/tests'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'Edit Students Test', 'active'=>'active'] ];
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
                @if(count($qb) > 0)
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="panel-title" style="font-size: 20px;">Edit Students Test
                                </h4>
                                <div class="row"><div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Student</label>
                                            <div class="form-line"> {{$qb['student_name']}} {{$qb['admission_no']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line"> {{$qb['class_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line">{{$qb['subject_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test</label>
                                            <div class="form-line">{{$qb['test_name']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line">{{$qb['term_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test Mark</label>
                                            <div class="form-line">{{$qb['test_mark']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Student Mark</label>
                                            <div class="form-line">{{$qb['student_mark']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Grade</label>
                                            <div class="form-line">{{$qb['student_grade']}} </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <form name="students_mark" id="students_mark" method="post" action="{{URL('/')}}/admin/save/studentstestmarks">
                                        {{csrf_field()}}
                                        <input type="hidden" name="students_test_id" id="students_test_id" value="{{$id}}">
                                    <!-- Start Question types -->
                                    @if(!empty($qb['test_items']) && count($qb['test_items'])>0)
                                        @foreach($qb['test_items'] as $qid=>$qtype)
                                        @if(isset($qtype))
                                            <div class="form-group form-float float-left col-sm-7"><label class="form-label">{{$qtype['question_type']}}</label></div>
                                            <div class="form-group form-float float-left col-sm-3"><label class="form-label">Student Answer</label></div>
                                            @php($i=1)
                                            @foreach($qtype['tt_items'] as $item) <?php //echo "<pre>"; print_r($item); exit;?>
                                                {{-- <div class="form-group form-float float-left col-md-5">
                                                    {{$item->question}} - {{$item->answer}}
                                                </div> --}}
                                                <div class="form-group form-float float-left col-sm-7">
                                                    @if($qtype['question_type_id'] == 16)
                                                    <img src="{{$item->question}}"  >
                                                    @else 
                                                    {{$item->question}}
                                                    @endif
                                                    <br>
                                                   Ans :{{$item->answer}}
                                                </div>

                                                <div class="form-group form-float float-left col-sm-3">
                                                    {{$item->student_answer}}
                                                </div>

                                                <div class="form-group form-float float-left col-sm-2">
                                                    <input type="text" name="mark[{{$item->question_bank_item_id}}]" data-qbid="{{$item->question_bank_item_id}}"  id="mark_{{$item->question_bank_item_id}}" value="{{$item->student_mark}}" class="col-md-5 marks noofquest noofquest_[{{$item->question_bank_item_id}}"> / {{$item->mark}}
                                                    <input type="hidden" data-qbid="{{$item->question_bank_item_id}}" value="{{$item->mark}}" class="tot_mark_{{$item->question_bank_item_id}}"> 
                                                </div>
                                                @php($i++)
                                            @endforeach
                                        @endif
                                        @endforeach
                                    @endif
                                    <hr>
                                    <div class="form-group form-float float-left col-md-7">

                                    </div>
                                    <div class="form-group form-float float-left col-md-3">
                                        <label class="form-label">Total Marks</label>
                                    </div>
                                    <div class="form-group form-float float-left col-md-2">
                                        <input type="text" required name="grade" id="grade" value="{{$qb['student_grade']}}" class="col-md-6  float-left">
                                    </div>

                                    <div class="form-group form-float float-right col-md-4">
                                        <br><br>
                                        <button type="submit" class="btn btn-success center-block float-right" id="Submit">Submit</button>
                                    </div>
                                    </form>


                                    <!-- End Question Types -->
                                </div></div>

                                <a href="{{url('/admin/studentstestlist')}}" class="btn btn-info waves-effect">BACK</a>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                No Test Details
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
      <script>

        $(function() {


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

                           swal('Success','Marks Saved Successfully','success');

                           window.location.reload();

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
                $("#students_mark").ajaxForm(options);
            });
        });

        $(".noofquest").keyup(function(){
  
  var qbid =  $(this).data('qbid');
  var total_ques = parseInt($('.tot_mark_'+qbid).val(), 10) || 0;
  var tot_question = parseInt(this.value, 10) || 0;
  if(total_ques < tot_question){
    $('.noofquest_'+qbid).val('0');
    swal('Oops','Maximum Mark for this Question is '+total_ques,'error');
  }
  
});
        

    </script>


@endsection

