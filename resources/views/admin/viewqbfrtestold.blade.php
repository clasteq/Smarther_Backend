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
                                            <div class="form-line"> <input class="fom-control"  type="text" name="test_name" id="test_name" value="{{$test_name}}" required style="width:100%;">
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
                                             <input type="hidden" name="chapter_id[]" id="chapter_id" value="{{$qb['chapter_id']}}">
                                            <div class="form-group form-float float-left col-md-12"><label class="form-label">{{$qtype['question_type']}}</label></div>@php($i=1)
                                            @foreach($qtype['qb_items'] as $item) <?php //echo "<pre>"; print_r($item); exit;?>  
                                                <div class="row">
                                                    <div class="form-group form-float float-left col-md-2">
                                                        <input type="checkbox" name="question_item_id[{{$item->id}}]" id="question_item_id_{{$item->id}}" value="1">
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

    </script>
 

@endsection

