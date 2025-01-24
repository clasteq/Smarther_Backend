<?php //echo "<pre>"; print_r($total_working_days); exit;
//  echo "<pre>"; print_r($students); exit;
 ?>

@if(isset($total_working_days) && !empty($total_working_days))  
    <div class="row">
       <div class="col-md-4">Total days : {{$total_working_days['total_days']}}</div>
       <div class="col-md-4">Total Working days : {{$total_working_days['total_working_days']}}</div>
       <div class="col-md-4">Total Leave days : {{$total_working_days['total_leave_days']}}</div>
    </div> 
    <div class="row">
       <div class="col-md-4">Total days from Opening : {{$total_working_days['totstart_days']}}</div>
       <div class="col-md-4">Total Working days : {{$total_working_days['totstart_working_days']}}</div>
       <div class="col-md-4">Total Leave days : {{$total_working_days['totstart_leave_days']}}</div>
    </div> 
@endif
<div class=" tableWrap">
<table class="table table-striped table-bordered tblcountries table-fixed "> 
    @if(!empty($students) && count($students)>0)
      <thead style="background: #a3d10c;color: #fff;text-align: center;">
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Admission No</th>
          <th>Mobile</th>
          <th>Profile</th>
          <!-- <th colspan="2">From Start</th> -->
          <th colspan="2">{{$monthyear}}</th>
          @for($i=1; $i<=$lastdate; $i++)
            <th colspan="2" scope="col">{{$i}}</th>
          @endfor 
          {{-- <th scope="col">Action</th> --}}
        </tr>
        <tr style="position: sticky; top: 72px;">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <!-- <th> P </th>
            <th> A </th> -->
            <th> P </th>
            <th> A </th>
            @for($i=1; $i<=$lastdate; $i++)
            <th>FN</th>
            <th>AN</th>
            @endfor 
            {{-- <th></th> --}}
        </tr>
    </thead>
    <tbody style="text-align: center;">
        
    
        @foreach($students as $student)
    
        <?php
                $holiday = $student['holidays_list'];
                $total_attendance_detail = $student['total_attendance_detail'];
                $v = array(); 
                foreach ($holiday as $key => $value) {
                    $item = $value->holiday;
                    array_push($v,$item);
                }

                $day_1 = array();
 
                foreach ($saturdays as $key => $day) {
                    $saturday = $day;
                    $expo_sat = explode('-',$saturday);
                    $expo = $expo_sat[2];
                    array_push($day_1,$expo);
                }

                $day_2 = array();
                foreach ($sundays as $key => $day1) {
                    $sunday = $day1;
                    $expo_sat = explode('-',$sunday);
                    $expo = $expo_sat[2];
                    array_push($day_2,$expo);
                }

         
        ?>
            <tr>
                 <th scope="row">{{$student['name']}}</th> 
                <th scope="row">{{$student['admission_no']}}</th>
                <th scope="row"> {{$student['mobile']}} </th>
                <th scope="row"><img src="{{$student['is_profile_image']}}" height="50" width="50"></th>
                <!-- <th scope="row">   </th>
                <th scope="row">   </th> -->
                <th scope="row"> {{$total_attendance_detail['present_days']}}  </th>
                <th scope="row"> {{$total_attendance_detail['absent_days']}}  </th>
            @for($i=1; $i<=$lastdate; $i++)
            
                @if(isset($student['dailyattendance']) && !empty($student['dailyattendance']) && is_array($student['dailyattendance']))
                    @php($day = 'day_'.$i) 
                    @php($day_an = 'day_'.$i.'_an') 
               
                    @if($student['dailyattendance'][$day] == 1)
                        <td><i class="fa fa-check greentick" aria-hidden="true"></i></td>
                    @elseif($student['dailyattendance'][$day] == 2)
                    <td><i class="fa fa-times redcross" aria-hidden="true"></i></td>

                        @else 
                        <?php 
                            $todate = date('Y-m-d');
                            $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
                        @if($current > $todate)
                            <td></td>
                        @else 
                            @if($student['class_id'] == $class_id)
                            @else  
                            @endif
                        @if(in_array($i,$v) || in_array($i,$day_1) || in_array($i,$day_2))
                        <td>H</td>
                        @else
                         <td id="{{$student['id']}}_{{$i}}"> 
                                <p onclick="putattendance({{$student['id']}}, 1, {{$i}},'fn', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                                <p onclick="putattendance({{$student['id']}}, 2, {{$i}},'fn', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                            </td> 
                            @endif
                        
                        @endif
                    @endif
                        @if($student['dailyattendance'][$day_an] == 1)
                        <td><i class="fa fa-check greentick" aria-hidden="true"></i></td>
                    @elseif($student['dailyattendance'][$day_an] == 2)
                    <td><i class="fa fa-times redcross" aria-hidden="true"></i></td>
                  

                    @else 
                        <?php 
                            $todate = date('Y-m-d');
                            $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
                        @if($current > $todate)
                            <td></td>
                        @else 
                            @if($student['class_id'] == $class_id)
                            @else  
                            @endif
                        @if(in_array($i,$v) || in_array($i,$day_1) || in_array($i,$day_2))
                        <td>H</td>
                        @else
                         <td id="{{$student['id']}}_{{$i}}"> 
                                <p onclick="putattendance({{$student['id']}}, 1, {{$i}},'an', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                                <p onclick="putattendance({{$student['id']}}, 2, {{$i}},'an',this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                            </td> 
                            
                            @endif
                        
                        @endif
                    @endif
    
                    
                @else 
                <?php $todate = date('Y-m-d');
                    $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i)));?>
                    @if($current > $todate)
                        <td></td>
                        <td></td>
                    @else 
                        @if($student['class_id'] == $class_id)
                        @else <!-- 
                        <td></td> -->
                        @endif
                        @if(in_array($i,$v) || in_array($i,$day_1) || in_array($i,$day_2))
                        <td>H</td>
                        <td>H</td>
                        @else
                        <td id="{{$student['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$student['id']}}, 1, {{$i}},'fn', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$student['id']}}, 2, {{$i}},'fn', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                        </td>
                        <td id="{{$student['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$student['id']}}, 1, {{$i}},'an', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$student['id']}}, 2, {{$i}},'an', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                        </td>
                         
                        @endif
                    @endif
                @endif
            @endfor 
            {{-- <td><a target="_blank" href="{{URL('/')}}/admin/entryattendance/{{$student['enc_id']}}/{{$monthyear}}/{{$class_id}}/{{$section_id}}" style="padding-top:0px;"><i class="fa fa-pencil" aria-hidden="true"></i></a></td> --}}
            </tr>
        @endforeach
       
    </tbody>
    @else 
    <tbody style="text-align: center;"><tr><td>No Students</td></tr>
    @endif  
</table>
</div>