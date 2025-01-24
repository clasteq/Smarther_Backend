<?php 

  //echo "<pre>"; print_r($students);exit;
?>

  <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/dailyattendace') }}" method="post">    
    <input hidden  name="new_date" type="text" id="new_date" value="{{$new_date}} " />
    <input type="hidden" name="tclass_id" id="tclass_id" value="{{$class_id}}">
    <input type="hidden" name="tsection_id" id="tsection_id" value="{{$section_id}}">

  @if(!empty($students) && count($students)>0)
    <div class="row">
      <div class=" col-md-2">
        <label class="form-label">Total Boys</label>
        <div class="form-line">
            <input class="form-control" name="total_boys" value="{{$total_boys}}" type="text" id="total_boys"  />
        </div>
      </div>
      <div class=" col-md-2">
        <label class="form-label">Total Girls</label>
        <div class="form-line">
            <input class="form-control" name="total_girls" value="{{$total_girls}}" type="text" id="total_girls"  />
        </div>
      </div>

      <div class=" col-md-8">

         <table class="table table-bordered table-striped">
            <tr>
              <th></th><th colspan="2">Present</th><th colspan="2">Absent</th>
            </tr>
            <tr>
              <th></th><th>Boys</th><th>Girls</th><th>Boys</th><th>Girls</th>
            </tr>
            <tr>
              <th>FN</th><th>{{$total_boys_present_fn}}</th><th>{{$total_girls_present_fn}}</th>
                         <th>{{$total_boys_absent_fn}}</th><th>{{$total_girls_absent_fn}}</th>
            </tr>
            <tr>
              <th>AN</th><th>{{$total_boys_present_an}}</th><th>{{$total_girls_present_an}}</th>
                         <th>{{$total_boys_absent_an}}</th><th>{{$total_girls_absent_an}}</th>
            </tr>
         </table>
   
      </div>

    </div>
    <br>
    {{ csrf_field() }} 
    <table class="table table-striped table-bordered tblcountries" >
    
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
      <thead style="background: #a3d10c;color: #fff;text-align: center;">
        <tr>
            <th scope="col">S.No</th>
          <th scope="col">Name</th>
          <th scope="col">Admission No</th>
          <th>Mobile</th>
          <th>Profile </th>
          <th>FN<br><input type="checkbox" {{$fn_checked}}  name="fn_chk" onclick="checkSession()" id="fn_chk" ></th>
          <th>AN<br><input type="checkbox" {{$an_checked}} name="an_chk" id="an_chk" onclick="checkanSession()"></th>
          {{-- @for($i=1; $i<=$lastdate; $i++)
            <th scope="col">{{$i}}</th>
          @endfor  --}}
          {{-- <th scope="col">Action</th> --}}
        </tr>
    </thead>
   
    <tbody style="text-align: center;">
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
                            $checked = "checked";
                        }
                        $an_checked = '';
                        if(isset($student['dailyattendance']['day_'.$day.'_an']) && $student['dailyattendance']['day_'.$day.'_an'] == 1){
                            $an_checked = "checked";
                        } 

                        ?>
                        <tr>
                            <th>{{$i}}<input type="hidden"  name="student_id[{{$student['id']}}]" value="{{$student['id']}}" id="student_id" ></th>
                            <th scope="row">{{$student['name']}}</th> 
                            <th scope="row">{{$student['admission_no']}}</th>
                            <th>{{$student['mobile']}}</th>
                            <th><img height="50"  width="50"  src="{{$student['is_profile_image']}}"></th>
                            <th><input type="checkbox"  {{$checked}} class="fn_section" name="fn_section[{{$student['id']}}]" value="{{$student['id']}}" id="fn_section" ></th>
                            <th><input type="checkbox"  class="an_section" {{$an_checked}}  name="an_section[{{$student['id']}}]" value="{{$student['id']}}" id="an_section" ></th>
                     
                        </tr>
      
                        <?php

                        $i++; ?>
                    @endforeach
    <?php       }  else{ ?>
    
                    <tbody style="text-align: center;"><tr><td colspan="7" >Today is Holiday</td></tr>
    <?php       }  ?>
    <?php   //}   else{   ?>
   
                <!-- <tbody style="text-align: center;"><tr><td colspan="7" >Today is Saturday</td></tr> -->
    <?php   //}    
        }   else{   ?>
   
                <tbody style="text-align: center;"><tr><td colspan="7" >Today is Sunday</td></tr>
    <?php   }   ?>
    @else 
    <tbody style="text-align: center;"><tr><td>No Students</td></tr>
    @endif  
    </tbody>
</table>
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
  if(!in_array($day,$v)){
    ?>
<div class="modal-footer">
    @if($appstatus == 1) @php($appst = "Approved") @php($appclass = "btn-success") 
    @else @php($appst = "Approve")  @php($appclass = "btn-info") @endif
    <button type="sumbit" class="btn {{$appclass}} waves-effect"
        id="edit_style" onclick="saveTimetable();"> {{$appst}}  </button> 
</div>
<?php
}
?>

</form>
