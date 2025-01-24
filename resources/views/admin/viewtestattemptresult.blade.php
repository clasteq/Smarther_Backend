@extends('layouts.admin_master')
@section('report_settings', 'active')
@section('master_studenttestattempts', 'active')
@section('menuopenr', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Student Test Result', 'active'=>'active']];
?>
@section('content')
<style>
    .success-message{
        color:green;
    }
    .error-message{
        color:red;
    }
</style>
 

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
    <!-- Exportable Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 style="font-size:20px;" class="card-title">Students Test Attempts </h4>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($attempts))
    <div class="row">
        <div class="row col-md-12">
            <div class="form-group col-md-3 " >
                <label class="form-label">Student</label>
                <span class="form-control" type="text">{{$attempts[0]->student_name}}</span>
            </div>
            <div class="form-group col-md-3 " >
                <label class="form-label">Admission Number</label>
                <span class="form-control" type="text" >{{$attempts[0]->admission_no}}</span>
            </div> 
            <div class="form-group col-md-3 " >
                <label class="form-label">Class</label>
                <span class="form-control" type="text">{{$attempts[0]->class_name}}</span>
            </div>
            <div class="form-group col-md-3 " >
                <label class="form-label">Subject</label>
                <span class="form-control" type="text" >{{$attempts[0]->subject_name}}</span>
            </div> 
            <div class="form-group col-md-3 " >
                <label class="form-label">Term</label>
                <span class="form-control" type="text">{{$attempts[0]->term_name}}</span>
            </div>
            <div class="form-group col-md-3 " >
                <label class="form-label">Test</label>
                <span type="text" >{{$attempts[0]->test_name}}</span>
            </div> 
          
            <div class="form-group col-md-3 " >
                <label class="form-label">Test Attempts Count</label>
                <span class="form-control" type="text" >{{count($attempts)}}</span>
            </div> 
     </div>

    </div>
    @endif
</section>

  <!-- Main content -->
<div class="card">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" id="outerTab" role="tablist">
        @if(!empty($attempts))
        @foreach($attempts as $k=>$att)
        @php($k = $k+1)
        @php($active = '')
        @if($k == 1) @php($active = 'active') @else @php($active = '') @endif
        <li class="nav-item">
            <a class="nav-link {{$active}}" data-toggle="tab" href="#tab-{{$k}}" aria-controls="tab-{{$k}}" role="tab" aria-expanded="true">Attempt-{{$k}}</a>
        </li>
        @endforeach
        @endif 
        <li class="nav-item">
            <a class="nav-link {{$active}}" data-toggle="tab" href="#tab-ovaerall" aria-controls="tab-ovaerall" role="tab" aria-expanded="true">Overall</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$active}}" data-toggle="tab" href="#tab-revision" aria-controls="tab-revision" role="tab" aria-expanded="true">Revision</a>
        </li>
    </ul>
  </div>
  <div class="card-body tab-content">

    @if(!empty($attempts))
    @foreach($attempts as $k=>$att)
    @php($kk = $k)
    @php($k = $k+1)
    @php($active = '')
    @if($k == 1) @php($active = 'active') @else @php($active = '') @endif 

    <?php 
    $attempts_array = $attempts->toArray();
    ?>

    
    @php($wrong_cnt  = [])
    @php($attres  = []) 
    @if(!empty($attempts))
        @foreach($attempts as $k1=>$att1)  
            @if(isset($attempts_array[$k1]['test_items']))
                @php($wrongcnt  = 0)
                @foreach($attempts_array[$k1]['test_items'] as $qi=>$quest)
                    @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0)
     
                        @foreach($quest['tt_items'] as $qtt=>$vtt) 

                        <?php $attempts_array[$k1]['test_items'][$qi]['tt_items'][$qtt]->wrongcnt[$k1]  = $wrongcnt; ?>

                        @if($vtt->student_mark  > 0) <!-- sd --> @else @php($vtt->student_mark  = 0) @endif
                        @if($vtt->student_mark > 0)  
                        <?php     $attempts_array[$k1]['test_items'][$qi]['tt_items'][$qtt]->atts[$k1]  = 1; 
                        $attres[$vtt->question_bank_item_id][$k1] = 1; 
                        $wrong_cnt[$vtt->question_bank_item_id][$k1] = $wrongcnt; ?>
                        @else 
                            <?php $attempts_array[$k1]['test_items'][$qi]['tt_items'][$qtt]->wrongcnt[$k1]  = $wrongcnt+1;
                            $wrong_cnt[$vtt->question_bank_item_id][$k1] = $wrongcnt+1;  ?>
                            @if(!empty($vtt->student_answer))  
                                <?php     $attempts_array[$k1]['test_items'][$qi]['tt_items'][$qtt]->atts[$k1]  = 2; 
                                $attres[$vtt->question_bank_item_id][$k1] = 2;  ?>
                            @else   
                                <?php     $attempts_array[$k1]['test_items'][$qi]['tt_items'][$qtt]->atts[$k1]  = 3;  
                                $attres[$vtt->question_bank_item_id][$k1] = 3; ?>
                            @endif
                        @endif
                        @endforeach     <?php // echo $attres[$vtt->question_bank_item_id][$k1]."==".$wrong_cnt[$vtt->question_bank_item_id][$k1]."<br>"; ?>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif 

    <?php   //echo "<pre>";  print_r($attres);print_r($attempts_array); exit; ?>

    <div class="tab-pane {{$active}}" id="tab-{{$k}}" role="tabpanel">
        <div class="row col-md-12">
        <span class="form-control col-md-6">Test Grade : {{$att->student_grade}} </span>
        <span class="form-control col-md-6">Duration : {{$att->duration}}  </span>
        <span class="form-control col-md-6">Test Mark : {{$att->test_mark}} </span>
        <span class="form-control col-md-6">Student Mark : {{$att->student_mark}} </span>
        <span class="form-control col-md-6">Total No of Questions : {{$att->total_questions}} </span>
        <span class="form-control col-md-6">Attempted No of Questions : {{$att->attempeted_question}} </span>
        <span class="form-control col-md-6">Date : {{date('d-m-Y H:i', strtotime($att->test_date))}} </span> 
        </div>
        <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs card-header-tabs" id="innerTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#tab-{{$k}}-1" aria-controls="tab-{{$k}}-1" role="tab" aria-expanded="true">Correct</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#tab-{{$k}}-2" aria-controls="tab-{{$k}}-2" role="tab">Wrong</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#tab-{{$k}}-3" aria-controls="tab-{{$k}}-3" role="tab">All</a>
                </li>
              </ul>
            </div>
            <div class="card-body tab-content">
                <div class="tab-pane active" id="tab-{{$k}}-1" role="tabpanel">
                @if(isset($attempts_array[$kk]['test_items']))
                @foreach($attempts_array[$kk]['test_items'] as $qi=>$quest)
                    @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0)

                    @php($count  = 0)
                    @foreach($quest['tt_items'] as $qtt=>$vtt)
                    @if($vtt->student_mark  > 0) <!-- sd --> @else @php($vtt->student_mark  = 0) @endif
                    @if($vtt->student_mark > 0)
                    @php($count  = $count + 1)
                    @endif
                    @endforeach

                    @if($count > 0)
                    <h6>{{$quest['question_type']}} </h6><br>
                    @foreach($quest['tt_items'] as $qtt=>$vtt)
                        @php($qtt = $qtt + 1)
                        @if($vtt->student_mark  > 0) <!-- sd --> @else @php($vtt->student_mark  = 0) @endif
                        @if($vtt->student_mark > 0)
                            <b>{{$qtt}}. {{$vtt->question}}</b>
                            <br>
                            <b> Student Answer: {{$vtt->student_answer}} </b>
                            <br>
                            Correct Answer : {{$vtt->answer}}
                            <br>
                            Mark : <span style="color:green;"> {{$vtt->student_mark}} ( out of {{$vtt->mark}} )</span> <br><br>
                        @endif
                    @endforeach
                    @endif
                    @endif
                @endforeach
                @endif
                </div>
                <div class="tab-pane" id="tab-{{$k}}-2" role="tabpanel">
                @if(isset($attempts_array[$kk]['test_items']))
                @foreach($attempts_array[$kk]['test_items'] as $qi=>$quest)
                    @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0)

                    @php($count  = 0)
                    @foreach($quest['tt_items'] as $qtt=>$vtt)
                    @if($vtt->student_mark  > 0) <!-- sd --> @else @php($vtt->student_mark  = 0) @endif
                    @if($vtt->student_mark == 0)
                    @php($count  = $count + 1)
                    @endif
                    @endforeach
                    
                    @if($count > 0)
                    <h6>{{$quest['question_type']}}</h6><br>
                    @foreach($quest['tt_items'] as $qtt=>$vtt)
                        @php($qtt = $qtt + 1)
                        @if($vtt->student_mark  > 0) <!-- sd --> @else @php($vtt->student_mark  = 0) @endif
                        @if($vtt->student_mark == 0)
                            <b>{{$qtt}}. {{$vtt->question}}</b>
                            <br>
                            <b> Student Answer: {{$vtt->student_answer}} </b>
                            <br>
                            Correct Answer : {{$vtt->answer}}
                            <br>
                            Mark : <span style="color:red;"> {{$vtt->student_mark}} ( out of {{$vtt->mark}} )</span> <br><br>
                        @endif
                    @endforeach
                    @endif

                    @endif
                @endforeach
                @endif
                </div>
                <div class="tab-pane" id="tab-{{$k}}-3" role="tabpanel">
                @if(isset($attempts_array[$kk]['test_items']))
                @foreach($attempts_array[$kk]['test_items'] as $qi=>$quest)
                    @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0)
                    <h6>{{$quest['question_type']}}</h6><br>
                    @foreach($quest['tt_items'] as $qtt=>$vtt)
                        @php($qtt = $qtt + 1)
                        @if($vtt->student_mark > 0) 
                            @php($color = 'green')
                        @else 
                            @php($vtt->student_mark = 0) 
                            @php($color = 'red')
                        @endif 
                            <b>{{$qtt}}. {{$vtt->question}}</b>
                            <br>
                            <b> Student Answer: {{$vtt->student_answer}} </b>
                            <br>
                            Correct Answer : {{$vtt->answer}}
                            <br>
                            Mark : <span style="color:{{$color}};">{{$vtt->student_mark}} ( out of {{$vtt->mark}} )</span> <br> 
                            <?php 
                                //echo $vtt[$question_bank_item_id]; 
                                if(isset($attres[$vtt->question_bank_item_id])) {  
                                    foreach($attres[$vtt->question_bank_item_id] as $ak => $anstype){ 
                                        if($anstype == 1) {
                                            echo '<i class="fas fa-check-circle" style="color:green;"></i>';
                                        }   else if($anstype == 2) {
                                            echo '<i class="fas fa-times-circle" style="color:red;"></i>';
                                        } else {
                                            echo '<i class="fas fa-circle" style="color:orange;"></i>';
                                        }
                                    }
                                }
                            ?><br><br>
                    @endforeach
                    @endif
                @endforeach
                @endif
                </div>
            </div>
        </div>  
    </div>

    @endforeach
    @endif  

    <div class="tab-pane {{$active}}" id="tab-ovaerall" role="tabpanel">
        <div class="row col-md-12">
            @if(!empty($attempts))
                @foreach($attempts as $k=>$att)  
                <div class="row col-md-8">
                    <span class="form-control col-md-6" style="font-weight: bold;">Attempt-{{$k+1}} </span>
                    <span class="form-control col-md-6">Date : {{date('d-m-Y H:i', strtotime($att->test_date))}} </span> 
                    <span class="form-control col-md-6">Attended Questions : </span>
                    <span class="form-control col-md-6">{{$att->attempeted_question}} / {{$att->total_questions}} </span>
                    <span class="form-control col-md-6">Student Mark :  </span>
                    <span class="form-control col-md-6">{{$att->student_mark}} / {{$att->test_mark}} </span> 
                    <span class="form-control col-md-12"> &nbsp; </span>
                </div>
                @endforeach
            @endif  
        </div> 
    </div>

    <div class="tab-pane {{$active}}" id="tab-revision" role="tabpanel">
        <?php $wrong_array = []; $ind = 0; ?>
        @if(!empty($attempts))
        @foreach($attempts as $k1=>$att1)  @if( $k1 == 0)
        @if(isset($attempts_array[$kk]['test_items']))
        @foreach($attempts_array[$kk]['test_items'] as $qi=>$quest)
            @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0) 
            @foreach($quest['tt_items'] as $qtt=>$vtt)
             <?php $wrong_array[$qtt] = $vtt->wrong;  ?>
            @endforeach
            @endif 
            <?php   arsort($wrong_array);

        /*foreach($wrong_array as $x => $x_value) {
          echo "Key=" . $x . ", Value=" . $x_value;
          echo "<br>";
        }*/ ?> 
            @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0)
            <h6>{{$quest['question_type']}}</h6><br>

            @if(isset($quest['tt_items']) && !empty($quest['tt_items']) && count($quest['tt_items'])>0) 
            @foreach($quest['tt_items'] as $qtt=>$vtt)
             <?php $wrong_array[$qtt] = $vtt->wrong;  ?>
            @endforeach
            


           <!--  foreach($quest['tt_items'] as $qtt=>$vtt) -->
            @foreach($wrong_array as $qtt=>$vtt1)
                @if(isset($quest['tt_items'][$qtt]))
                @php($vtt = $quest['tt_items'][$qtt])

                @php($qtt = $qtt + 1)
                @if($vtt->student_mark > 0) 
                    @php($color = 'green')
                @else 
                    @php($vtt->student_mark = 0) 
                    @php($color = 'red')
                @endif 
                    <b>{{$qtt}}. {{$vtt->question}}</b>
                    <br>
                    <b> Student Answer: {{$vtt->student_answer}} </b>
                    <br>
                    Correct Answer : {{$vtt->answer}}
                    <br>
                    Mark : <span style="color:{{$color}};">{{$vtt->student_mark}} ( out of {{$vtt->mark}} )</span> <br> 
                    <?php 
                        //echo $vtt[$question_bank_item_id]; 
                        if(isset($attres[$vtt->question_bank_item_id])) {  
                            foreach($attres[$vtt->question_bank_item_id] as $ak => $anstype){ 
                                if($anstype == 1) {
                                    echo '<i class="fas fa-check-circle" style="color:green;"></i>';
                                }   else if($anstype == 2) {
                                    echo '<i class="fas fa-times-circle" style="color:red;"></i>';
                                } else {
                                    echo '<i class="fas fa-circle" style="color:orange;"></i>';
                                }
                            }
                        }
                    ?><br><br>
                @endif
            @endforeach
            @endif
            @endif
        @endforeach
        @endif
        @endif
        @endforeach
        @endif
    </div>
  </div>
</div>


@endsection
