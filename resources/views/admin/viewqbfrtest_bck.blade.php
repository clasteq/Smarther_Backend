@extends('layouts.admin_master')
@section('test_settings', 'active')
@section('master_testlist', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/testlist'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'View Test Question Bank', 'active'=>'active'] ];
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
                    <div class="panel-title"> Test View
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row"><div class="col-md-12">
                                    <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/admin/save/qbtest')}}">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <input type="hidden" name="class_id" id="class_id" value="{{$qbank[0]['class_id']}}">
                                        <input type="hidden" name="subject_id" id="subject_id" value="{{$qbank[0]['subject_id']}}">
                                        <input type="hidden" name="term_id" id="term_id" value="{{$qbank[0]['term_id']}}">

                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line"> {{$qbank[0]['class_name']}}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line"> {{$qbank[0]['subject_name']}}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line"> {{$qbank[0]['term_name']}}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test name</label>
                                            <?php $chaptername = '';
                                            if(!empty($qbank) && count($qbank)>0){
                                                foreach($qbank as $qid=>$qb) {
                                                    $chaptername .= $qb['chaptername'].' ';
                                                }
                                            }

                                            $test_name = $chaptername; //date('Y-m-d').' '.?>
                                            <div class="form-line"> <input type="text" name="test_name" id="test_name" value="{{$test_name}}" required style="width:100%;">
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Remarks</label>
                                            <div class="form-line">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                     <!-- Start Question types -->
                                    @if(!empty($qbank) && count($qbank)>0)
                                    @foreach($qbank as $qid=>$qb)
                                    @if(!empty($qb['questionbank_items']) && count($qb['questionbank_items'])>0)
                                        @foreach($qb['questionbank_items'] as $qid=>$qtype)
                                        @if(isset($qtype))
                                        <?php
$question_type = 0;
$question_type = $qtype['question_type_id'];
?>
                                             <input type="hidden" name="chapter_id[]" id="chapter_id" value="{{$qb['chapter_id']}}">
                                             <div class="form-group form-float float-left col-md-12"><label class="form-label">
                                                <input class="operation" type="checkbox"
                                                 name="question_type[]" id="question_type" value="{{$question_type}}"> {{$qtype['question_type']}}

                                               </label></div>
                                             @php($i=1)
                                            @foreach($qtype['qb_items'] as $item) <?php //echo "<pre>"; print_r($item); exit;?>
                                                <div class="row">
                                                    <div class="form-group form-float float-left col-md-2">
                                                        <input type="checkbox" class="questions" name="question_item_id[{{$item->id}}]" id="question_item_id_{{$item->id}}" value="{{$item->id}}">
                                                        <input type="number" name="marks[{{$item->id}}]" id="marks_{{$item->id}}" style="width: 40%;" min="1" maxlength="3" max="200" placeholder="mark">
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-10">
                                                        {{$i}}) {{$item->question}} - {{$item->answer}}
                                                    </div>
                                                </div>
                                                @php($i++)
                                            @endforeach
                                        @endif

                                        @endforeach
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
     alert(checked_val)
    //             // var questions = $("input.operation:checked").map(function(){
    //     //       return $(this).val();
    // //     //  }).get(); // you have use get() here

    //         var questions = [];
    // // var
    // var  count=0 ;


    //          jQuery.each(jQuery('.operation:checked'), function() {
    //     count++;
    //     questions.push(jQuery(this).val());
    // });
    // // alert(questions)
    // var animals = $('.operation');

    // var questions = $("input.operation:checked").map(function(){
    //           return $(this).val();
    //      }).get(); // you have use get() here


    // // $.each(animals, function() {
	// //   			var $this = $(this);
    // //         if($this.is(":checked")) {
	// //   				// put the checked animal value to the html list
	// //   				// html += "<li>"+$this.val()+"</li>";
    // //                   questions =  $this.val();
	// //   			}
	// //   		});

    //         alert(questions)

     $.ajax({
                url: "{{ url('admin/fetch-questions') }}",
                type: "POST",
                data: {
                    questions: checked_val,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
               $.each(res.questions, function(key, value) {
                alert(value.question_type_id)

                $("input[value='" + value.id + "']").prop('checked', true);

                });


                }
            });

        }
        else{
            var questions = $("input.operation:not(:checked)").map(function(){
              return $(this).val();
         }).get(); // you have use get() here

         $.ajax({
                url: "{{ url('admin/fetch-questions') }}",
                type: "POST",
                data: {
                    questions: questions,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
               $.each(res.questions, function(key, value) {
                         alert(value.id);
                $("input[value='" + value.id + "']").prop('checked', false);

                });


                }
            });

        }

        });

//         $('.questions').click(function() {


//     var answers = [];
//     var questions
//     var  count=0 ;
//     jQuery.each(jQuery('.questions:checked'), function() {
//         count++;
//         answers.push(jQuery(this).val());
//     });

//     var questions = $("input.operation:not(:checked)").map(function(){
//               return $(this).val();
//          }).get(); // you have use get() here

//         //  if(jQuery.inArray(questions, answers)){
//         //      $("input.operation[value='" + answers + "']").prop('checked', true);

//         //  }

//         $('input.questions[type=checkbox]:checked').each(function() {
//       var answer = $(this).val();
//       $("input.operation[value='" + answer + "']").prop('checked', true);
// });




// });


    </script>


@endsection

