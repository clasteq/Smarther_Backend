<?php $v = array();  //echo "<pre>"; print_r($teachers); //exit;?>
    <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/teacher_dailyattendace') }}" method="post">    
        <input hidden  name="new_date" type="text" id="new_date" value="{{$new_date}} " />
               
        {{ csrf_field() }} 
        <table class="table table-striped table-bordered tblcountries">
    @if(!empty($teachers) && count($teachers)>0)
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
          <th scope="col">Email</th>
          <th scope="col">Emp No</th>
          <th scope="col">Mobile</th>
          <th scope="col">Profile</th>
          <th scope="col">FN<br><input type="checkbox" {{$fn_checked}} onclick="checkSession()"  name="fn_chk"  id="fn_chk" ></th>
          <th scope="col">AN<br><input type="checkbox" {{$an_checked}} name="an_chk" id="an_chk"  onclick="checkanSession()"></th>
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
                        // echo $new_date * 1;
                        $leave_date = $year.'-'.$month.'-'.$day;
                        if(!in_array($leave_date,$sundays)){
                     if(!in_array($leave_date,$saturdays)){
                    if(!in_array($day,$v)){
      
      ?>
        @foreach($teachers as $teacher)
        <?php
    
          $checked = '';
         if((isset($teacher['teacherdailyattendance']['day_'.$day])) && $teacher['teacherdailyattendance']['day_'.$day] == 1){
        $checked = "checked";
       }
       $an_checked = '';
       if((isset($teacher['teacherdailyattendance']['day_'.$day.'_an'])) && $teacher['teacherdailyattendance']['day_'.$day.'_an'] == 1){
        $an_checked = "checked";
       }
      
          
    ?>
    
            <tr>
                 <th scope="row">{{$i}}</th>
                 <th scope="row">{{$teacher['name']}}</th>
                 <th scope="row">{{$teacher['email']}}</th>
                 <th scope="row">{{$teacher['emp_no']}}</th>
                 <th scope="row">{{$teacher['mobile']}}</th>
                 <th scope="row"><img height="50"  width="50" src="{{$teacher['is_profile_image']}}"></th>
                 <th><input type="checkbox" {{$checked}} class="fn_section" name="fn_section[{{$teacher['id']}}]" value="{{$teacher['id']}}" onclick="check_main_fn()" id="fn_section" ></th>
                 <th><input type="checkbox" class="an_section" {{$an_checked}}  name="an_section[{{$teacher['id']}}]" value="{{$teacher['id']}}"  onclick="check_main_an()" id="an_section" ></th>
          
            </tr>
            <?php $i++; ?>
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
        } 
        else{
         ?>
 
 <tbody style="text-align: center;"><tr><td colspan="7" >Today is Saturday</td></tr>
       <?php 
       }
       ?>
         <?php 
        } 
        else{
         ?>
 
 <tbody style="text-align: center;"><tr><td colspan="7" >Today is Sunday</td></tr>
       <?php 
       }
       ?>
       
    </tbody>
    @else 
    <tbody style="text-align: center;"><tr><td>No Students</td></tr>
    @endif  
</table>
<?php 
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