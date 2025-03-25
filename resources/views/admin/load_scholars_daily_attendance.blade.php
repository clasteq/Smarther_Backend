<?php 

  //echo "<pre>"; print_r($students);exit;
?>

<form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/daily_attendace') }}" method="post">    
<input hidden  name="new_date" type="text" id="new_date" value="{{$new_date}} " />
<input type="hidden" name="tclass_id" id="tclass_id" value="{{$class_id}}">
<input type="hidden" name="tsection_id" id="tsection_id" value="{{$section_id}}">

@if(!empty($students) && count($students)>0)
 
    {{ csrf_field() }} 
    <?php 

         $orderdate = explode('-', $new_date);
         $year = $orderdate[0];
         $month   = $orderdate[1];
         $day  = $orderdate[2];
         $day = $day * 1;
         $fn_checked = '';
         if($fn_chk > 0){
          $fn_checked = "checked";
         }
         $an_checked = '';
         if($an_chk > 0){
          $an_checked = "checked";
         }
  
    ?>

    <div class="row"><div class="col-md-12">
        <label for="full_chk">Full Day </label><input class="ml-3" type="radio" {{$fn_checked}}  name="att_chk" id="full_chk" value="1" checked onclick="movefn(1)"> 
        <label for="fn_chk" class="ml-5">Fore Noon </label><input class="ml-3" type="radio" {{$fn_checked}}  name="att_chk" id="fn_chk" value="2" onclick="movefn(2)">  
        <label for="an_chk" class="ml-5">After Noon </label><input class="ml-3" type="radio" {{$an_checked}} name="att_chk" id="an_chk" value="3" onclick="movefn(3)"> 
    </div></div>
    <div class="row"><div class="col-md-12">
    <div class="col-md-8 float-left">
        <h3>Present :</h3>
        <div class="row users-list border rounded clearfix" id="presentlist">
                <?php

                    $i = 1;
                    $holiday = $holidays;
                    $v = array(); 
                    foreach ($holidays as $key => $value) {
                        $item = $value->holiday;
                        array_push($v,$item);
                    }
                    $leave_date = $year.'-'.$month.'-'.$day;
                    if(!in_array($leave_date,$sundays)){
                        //if(!in_array($leave_date,$saturdays)){
                            if(!in_array($day,$v)){  ?>
                                @foreach($students as $key=>$student)
                
                                    <?php 
                                    $checked = '';
                                    if(isset($student['dailyattendance']['day_'.$day]) && $student['dailyattendance']['day_'.$day] == 1) {
                                        $checked = 1;
                                    }
                                    if(isset($student['dailyattendance']['day_'.$day]) && $student['dailyattendance']['day_'.$day] == 2) {
                                        $checked = 2;
                                    }

                                    $an_checked = '';
                                    if(isset($student['dailyattendance']['day_'.$day.'_an']) && $student['dailyattendance']['day_'.$day.'_an'] == 1){
                                        $an_checked = 1;
                                    } 
                                    if(isset($student['dailyattendance']['day_'.$day.'_an']) && $student['dailyattendance']['day_'.$day.'_an'] == 2){
                                        $an_checked = 2;
                                    } 
                                    ?> 

                                    <div class="col-md-2 border rounded text-center float-left m-3" id="lisection_{{$student['id']}}">
                                        <img src="{{$student['is_profile_image']}}" alt="User Image" style="width: 50px;height: 50px;">
                                        <a class="users-list-name" href="#" tooltip="" title="{{$student['mobile']}}">{{$student['name']}} <br/> {{$student['father_name']}} <!-- {{$student['admission_no']}} --> </a>

                                        <input type="hidden" name="attendance_type[{{$student['id']}}]" value="p" id="attendance_type">

                                        <input type="hidden"  name="student_id[{{$student['id']}}]" value="{{$student['id']}}" id="student_id" >

                                        <input type="checkbox"  checked  class="present_section" name="present_section[{{$student['id']}}]" value="{{$student['id']}}" id="present_section_{{$student['id']}}"  onclick="mv(this, {{$student['id']}});" data-fn="{{$checked}}" data-an="{{$an_checked}}">

                                        <!-- <input type="checkbox"  {{$checked}} class="fn_section" name="fn_section[{{$student['id']}}]" value="{{$student['id']}}" id="fn_section"  onclick="mv(this);">

                                        <input type="checkbox"  class="an_section" {{$an_checked}}  name="an_section[{{$student['id']}}]" value="{{$student['id']}}" id="an_section"  onclick="mv(this);"> -->
                                    </div>
                  
                                    <?php

                                    $i++; ?>
                                @endforeach
                <?php       }  else{ ?>
                
                                <div > Today is Holiday </div>
                <?php       }  ?>
                <?php   //}   else{   ?>
               
                            <!-- <tbody style="text-align: center;"><tr><td colspan="7" >Today is Saturday</td></tr> -->
                <?php   //}    
                    }   else{   ?>
               
                            <div>Today is Sunday</div>
                <?php   }   ?>
        </div>
    </div>
    <div class="col-md-2 float-left"></div>
    <div class="col-md-3 float-left">
        <h3>Absent :</h3>
        <div class="row users-list border rounded clearfix" id="absentlist">
              
        </div>
    </div>
    </div></div>
    <?php 
    $holiday = $holidays;
                $v = array(); 
               foreach ($holidays as $key => $value) {
               $item = $value->holiday;
               array_push($v,$item);
                  }
                 
    $orderdate = explode('-', $new_date);
         $year = $orderdate[0];
         $month   = $orderdate[1];
         $day  = $orderdate[2];
         $day = $day * 1;
    
    if(!in_array($day,$v)){    ?>
    <div class="modal-footer">
        @if($appstatus == 1) @php($appst = "Approved") @php($appclass = "btn-success") 
        @else @php($appst = "Approve")  @php($appclass = "btn-info") @endif 
        <button type="sumbit" class="btn {{$appclass}} waves-effect"
            id="edit_style" onclick="saveTimetable();"> Save  </button> 
    </div>
    <?php } ?>
@else 
No Students
@endif
</form>
