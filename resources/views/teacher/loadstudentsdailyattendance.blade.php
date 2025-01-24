<?php 

// echo "<pre>"; echo $lastdate;
//  exit;
?>

  <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/dailyattendace') }}" method="post">    
    <input hidden  name="new_date" type="text" id="new_date" value="{{$new_date}} " />
    <input type="hidden" name="tclass_id" id="tclass_id" value="{{$class_id}}">
    <input type="hidden" name="tsection_id" id="tsection_id" value="{{$section_id}}">
    
    {{ csrf_field() }} 
    <table class="table table-striped table-bordered tblcountries" >
    @if(!empty($students) && count($students)>0)
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
                    // if(!in_array($leave_date,$saturdays)){
                  if(!in_array($day,$v)){

?>
        @foreach($students as $key=>$student)
    
        <?php
               

       
        $checked = '';
         if($student['dailyattendance']['day_'.$day] == 1){
        $checked = "checked";
       }
       $an_checked = '';
       if($student['dailyattendance']['day_'.$day.'_an'] == 1){
        $an_checked = "checked";
       }

      
      

    ?>
            <tr>
                <th>{{$i}}</th>
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
<?php 
           } 
           else{
            ?>
    
    <tbody style="text-align: center;"><tr><td colspan="7" >Today is Holiday</td></tr>
          <?php 
          }
          ?>
            <?php 
          //}  else{
           ?>
   
   <!-- <tbody style="text-align: center;"><tr><td colspan="7" >Today is Saturday</td></tr> -->
         <?php 
         //}
         ?>
           <?php 
          } 
          else{
           ?>
   
   <tbody style="text-align: center;"><tr><td colspan="7" >Today is Sunday</td></tr>
         <?php 
         }
         ?>
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
    <button type="sumbit" class="btn btn-link waves-effect"
        id="edit_style" onclick="saveTimetable();">SAVE</button> 
</div>
<?php
}
?>

</form>
