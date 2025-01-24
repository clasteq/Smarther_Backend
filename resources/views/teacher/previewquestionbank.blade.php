@extends('layouts.teacher_master')
@section('questionbank_settings', 'active')
@section('master_questionbank', 'active')
@section('menuopenq', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/teacher/questionbank'), 'name'=>'Question Bank', 'active'=>''], ['url'=>'#', 'name'=>'View Question Bank', 'active'=>'active'] ];
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
                                <h4 style="font-size:20px" class="panel-title">View Question Bank 
                                </h4> 
                                <div class="row"><div class="col-md-12"> 
                                    <div class="row">
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line"> {{$qb['class_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line">{{$qb['subject_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Chapter</label>
                                            <div class="form-line">{{$qb['chaptername']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line">{{$qb['term_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Question Bank Name</label>
                                            <div class="form-line">{{$qb['qb_name']}}</div>
                                        </div>
                                    </div> 
                                    <hr>
                                    <!-- Start Question types -->
                                    @if(!empty($qb['questionbank_items']) && count($qb['questionbank_items'])>0)
                                        @foreach($qb['questionbank_items'] as $qid=>$qtype)  
                                        @if(isset($qtype))  
                                            <div class="form-group form-float float-left col-md-12"><label class="form-label">{{$qtype['question_type']}}</label></div>@php($i=1)
                                            @foreach($qtype['qb_items'] as $item) <?php //echo "<pre>"; print_r($item); exit;?>  
                                                @if($item->question_type_id != 16)
                                                <div class="form-group form-float float-left col-md-12">
                                                    {{$i}}) {!! $item->question !!} -  {!! $item->answer !!} <!--  <br> {{$item->display_answer}} {{$item->option_1}} {{$item->option_2}} {{$item->option_3}} {{$item->option_4}}  -->
                                                </div> 
                                                @elseif ($item->question_type_id == 16)
                                                <div class="form-group form-float float-left col-md-12">
                                                    @if(!empty($item->question))
                                                    <?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->question; ?>
                                                  @endif
                                                    {{$i}})&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img src="{{ $fileurl }}" width="100" height="100">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   -  {{$item->answer}} {{$item->display_answer}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @if(!empty($item->option_1))
                                                    <?php $option_1 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_1; ?>
                                                    <img src="{{ $option_1 }}" width="100" height="100">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    @if(!empty($item->option_2))
                                                    <?php $option_2 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_2; ?>
                                                    <img src="{{ $option_2 }}" width="100" height="100">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    @if(!empty($item->option_3))
                                                    <?php $option_3 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_3; ?>
                                                    <img src="{{ $option_3 }}"  width="100" height="100">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    @if(!empty($item->option_4))
                                                    <?php $option_4 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_4; ?>
                                                    
                                                    <img src="{{ $option_4 }}"  width="100" height="100">
                                                    @endif
                                                   
                                                 
                                                   
                                                        {{-- {{$item->option_1}} {{$item->option_2}} {{$item->option_3}} {{$item->option_4}}  --}}
                                                </div> 
                                                @endif
                                                @php($i++)
                                            @endforeach
                                        @endif 
                                        @endforeach
                                    @endif
                                    <!-- End Question Types --> 
                                </div></div>
                            </div>
                        </div>
                    </div>
                </div>
                @else 
                No Question Bank Details
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts') 
      <script>

        $(function() { 

            $('.plus').on('click', function () {
                var qtype = $(this).data('id');
                var i = $('#items_'+qtype).find('input[name="sno[]"]').length;
                var request = $.ajax({
                    type: 'post',
                    url: " {{URL::to('teacher/clone/questiontype')}}",
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

