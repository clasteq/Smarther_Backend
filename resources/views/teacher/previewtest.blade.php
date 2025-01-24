@extends('layouts.teacher_master')
@section('test_settings', 'active')
@section('master_test', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/teacher/testlist'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'View Test', 'active'=>'active'] ];
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
                                <h4 style="font-size:20px;" class="panel-title">View Test
                                </h4> 
                                <div class="row"><div class="col-md-12"> 
                                    <div class="row">
                                      
                                    <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">From Date</label>
                                            <div class="form-line"> {{$qb['class_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">To Date</label>
                                            <div class="form-line">{{$qb['subject_name']}} </div>
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
                                    </div> 
                                    <hr>
                                   
                                    <!-- Start Question types -->
                                    @if(!empty($qb['test_items']) && count($qb['test_items'])>0)
                                    @foreach($qb['test_items'] as $qid=>$qtype)  
                                    @if(isset($qtype))  
                                        <div class="form-group form-float float-left col-md-12"><label class="form-label">{{$qtype['question_type']}}</label></div>@php($i=1)
                                        <?php //echo "<pre>"; print_r($item); exit;?>  

                                        @foreach($qtype['tt_items'] as $item) 
                                        @if($item->question_type_id != 16)
                                            <div class="form-group form-float float-left col-md-12">
                                                {{$i}})&nbsp;&nbsp; {!! $item->question !!} -  {!! $item->answer !!} 
                                            </div> 
                                            @elseif ($item->question_type_id == 16)
                                            <?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->question; ?>
                                            <div class="form-group form-float float-left col-md-12">
                                                {{$i}})&nbsp;&nbsp; <img src="{{$item->question}}" height="150" width="150"> &nbsp;&nbsp;&nbsp; -  &nbsp;&nbsp;{{$item->answer}}
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

