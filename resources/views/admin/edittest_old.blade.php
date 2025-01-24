@extends('layouts.admin_master')
@section('test_settings', 'active')
@section('master_test', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/testlist'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'Edit Test', 'active'=>'active'] ];
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
                                <h4 style="font-size: 20px;" class="panel-title">Edit Test
                                </h4> 
                                <div class="row"><div class="col-md-12"> 
                                   
                                        <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/admin/update/updatetest')}}">
                                            <div class="row">
                                            {{csrf_field()}}
                                            <input type="hidden" name="test_id" id="test_id" value={{$qb['id']}}>
                                            <input type="hidden" name="class_id" id="class_id" value="{{$qb['class_id']}}">
                                            <input type="hidden" name="subject_id" id="subject_id" value="{{$qb['subject_id']}}">
                                            <input type="hidden" name="term_id" id="term_id" value="{{$qb['term_id']}}">
                                            <div class="form-group form-float float-left col-md-6">
                                                <label class="form-label">From</label>
                                                <input class="col-md-6 date_range_filter date form-control" type="text" autocomplete="off" value="{{$qb['from_date']}}" name="from_date" id="datepicker_from"  />
                                            </div>
                                            <div class="form-group form-float float-left col-md-6">
                                                <label class="form-label">To</label>
                                                <input class="col-md-6 date_range_filter date form-control" type="text" autocomplete="off" value="{{$qb['to_date']}}" name="to_date" id="datepicker_to"  />
                                            </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line"> {{$qb['class_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-right col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line">{{$qb['subject_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-right col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line">{{$qb['term_name']}} </div>
                                        </div>
                                        <div style="float: right !important;" class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test</label>
                                            <div class="form-line">
                                                <input class="col-md-8 form-control col-md-6" type="text" name="test_name" id="test_name" value="{{$qb['test_name']}}" required style="width:100%;">
                                                   </div>
                                        </div>
                                        <div style="float: right !important;" class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test Mark</label>
                                            <div class="form-line">
                                                <input  class="col-md-6 form-control col-md-6" type="text" name="test_mark" id="test_mark" value="{{$qb['test_mark']}}" required style="width:100%;">
                                                   </div>
                                        </div>
                                        <div style="float: right !important;" class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Test Time (in seconds)</label>
                                            <div class="form-line">
                                                <input  class="col-md-6 form-control col-md-6" type="text" name="test_time" id="test_time" value="{{$qb['test_time']}}" required style="width:100%;"  onkeypress="return isNumber(event)">
                                                   </div>
                                        </div>
                                       
                                    </div> 
                                    <hr>

                                    <!-- Start Question types -->

                                    <?php 
                                 
                                    $select_test = $qb['selected_test'];
                                     $v = array(); 
                                     $marks = array(); 

                                    foreach ($select_test as $key => $value) {
                                        $item = $value->question_bank_item_id;
                                        $mark = $value->mark;
                                        array_push($v,$item);
                                       
                                        $aMemberships[$item] = $mark; 
                                    } 

                                 
                                    ?>
                                    @if(!empty($qb['new_test_list']) && count($qb['new_test_list'])>0)
                                        @foreach($qb['new_test_list'] as $qid=>$qtype) 
                                     
                                            @if(isset($qtype))  
                                                <div class="form-group form-float float-left col-md-12"><label class="form-label">{{$qtype['question_type']}}</label></div>@php($i=1)
                                                @foreach($qtype['question_bank'] as $item) 
                                                    <?php  $checked ='';  $test_mark = '';  ?> 
                                                    @if (in_array($item->id, $v))
                                                    <?php    $checked = "checked" ; 
                                                    if(array_key_exists($item->id, $aMemberships)) {
                                                        $test_mark = $aMemberships[$item->id] ;
                                                     } 
                                                    ?>
                                                    
                                              
                                                    <div class="row">
                                                        <div class="form-group form-float float-left col-md-2">
                                                            <input type="checkbox" {{$checked}} class="questions_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}} newquestion" name="question_item_id[{{$item->id}}]"
                                                            data-qbid="{{$qtype['question_bank_id']}}"  data-qtyid="{{$qtype['question_type_id']}}"
                                                            id="question_item_id_{{$item->id}}" value="{{$item->id}}">
                                                            <input class="newmarks_{{$qtype['question_bank_id']}}_{{$qtype['question_type_id']}}"  data-qbid="{{$qtype['question_bank_id']}}" data-qtyid="{{$qtype['question_type_id']}}"  type="text" name="marks[{{$item->id}}]" value="{{ $test_mark}}"  id="marks_{{$item->id}}" style="width: 40%;" min="1" maxlength="3" max="200" placeholder="mark">
                                                        </div>
                                                        <div class="form-group form-float float-left col-md-10">
                                                             {!! $item->question !!} - {!! $item->answer !!}
                                                        </div>
                                                    </div>
                                                    @endif 
                                                    @php($i++)
                                                
                                                          
                                                @endforeach
                                          
                                            @endif 
                                        @endforeach
                                    @endif
                                    <!-- End Question Types --> 
                                    <button type="submit" class="btn btn-success center-block" id="Submit">Submit</button>
                                </form>
                                
                                </div>
                            
                            </div>
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
            // $("#datepicker_from").datepicker('setDate', new Date())
            // $("#datepicker_to").datepicker('setDate', new Date())

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

                           swal('Success','Test List Saved Successfully','success');

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

