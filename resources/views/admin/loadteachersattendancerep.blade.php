<?php  //echo "<pre>"; print_r($teachers); //exit;?>
    @if(!empty($teachers) && count($teachers)>0)
      <thead style="background: #a3d10c;color: #fff;text-align: center;">
        <tr>
          <th scope="col">Name</th><th scope="col">Emp No</th>
          <th scope="col">Email</th>
          <th scope="col">Mobile</th>
          <th scope="col">Profile </th>
          @for($i=1; $i<=$lastdate; $i++)
            <th colspan="2" scope="col">{{$i}}</th>
          @endfor 
          {{-- <th scope="col">Action</th> --}}
        </tr>
    </thead>
    <tbody style="text-align: center;">

        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            @for($i=1; $i<=$lastdate; $i++)
            <th>FN</th>
            <th>AN</th>
            @endfor 
            {{-- <th></th> --}}
        </tr>
    
        @foreach($teachers as $teacher)
        <?php
        $holiday = $teacher['holidays_list'];
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
    
            <tr> <th scope="row">{{$teacher['name']}}</th> <th scope="row">{{$teacher['emp_no']}}</th>
                <th scope="row">{{$teacher['email']}}</th>
                <th scope="row">{{$teacher['mobile']}}</th>
                <th scope="row"><img src="{{$teacher['is_profile_image']}}" width="50" height="50"></th>
            @for($i=1; $i<=$lastdate; $i++)
                @if(isset($teacher['teacherdailyattendance']) && !empty($teacher['teacherdailyattendance']) && is_array($teacher['teacherdailyattendance']))
                    @php($day = 'day_'.$i) 
                    @php($day_an = 'day_'.$i.'_an') 
                    @if($teacher['teacherdailyattendance'][$day] == 1)
                        <td><i class="fa fa-check greentick" aria-hidden="true"></i></td> 
                    @elseif($teacher['teacherdailyattendance'][$day] == 2)
                        <td><i class="fa fa-times redcross" aria-hidden="true"></i></td>
                    @else 
                        <?php $todate = date('Y-m-d');
                            $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
                        @if($current > $todate)
                            <td></td>
                        @else 
                        @if(in_array($i,$v) || in_array($i,$day_1) || in_array($i,$day_2))
                        <td>H</td>
                        @else
                            <td id="{{$teacher['id']}}_{{$i}}"> 
                                <p onclick="putattendance({{$teacher['id']}}, 1, {{$i}},'fn', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                                <p onclick="putattendance({{$teacher['id']}}, 2, {{$i}},'fn', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                            </td> 
                            @endif
                        @endif
                    @endif
                    @if($teacher['teacherdailyattendance'][$day_an] == 1)
                    <td><i class="fa fa-check greentick" aria-hidden="true"></i></td> 
                @elseif($teacher['teacherdailyattendance'][$day_an] == 2)
                    <td><i class="fa fa-times redcross" aria-hidden="true"></i></td>
                @else 
                    <?php $todate = date('Y-m-d');
                        $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
                    @if($current > $todate)
                        <td></td>
                    @else 
                    @if(in_array($i,$v) || in_array($i,$day_1) || in_array($i,$day_2))
                        <td>H</td>
                        @else
                        <td id="{{$teacher['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$teacher['id']}}, 1, {{$i}},'an', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$teacher['id']}}, 2, {{$i}},'an', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
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
                    @if(in_array($i,$v) || in_array($i,$day_1) || in_array($i,$day_2))
                        <td>H</td>
                        <td>H</td>
                        @else
                        <td id="{{$teacher['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$teacher['id']}}, 1, {{$i}},'fn', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$teacher['id']}}, 2, {{$i}},'fn', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                        </td>
                        <td id="{{$teacher['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$teacher['id']}}, 1, {{$i}},'an', this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$teacher['id']}}, 2, {{$i}},'an', this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                        </td>
                        @endif 
                    @endif
                @endif
            @endfor 
            {{-- <td><a target="_blank" href="{{URL('/')}}/admin/teacher_entryattendance/{{$teacher['enc_id']}}/{{$monthyear}}" style="padding-top:0px;"><i class="fa fa-pencil" aria-hidden="true"></i></a></td> --}}
            </tr>
        @endforeach
    </tbody>
    @else 
    <tbody style="text-align: center;"><tr><td>No Students</td></tr>
    @endif  
    