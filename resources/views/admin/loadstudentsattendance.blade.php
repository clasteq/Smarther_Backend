 <?php  //echo "<pre>"; print_r($students); //exit;?>
 
@if(!empty($students) && count($students)>0)
  <thead style="background: #a3d10c;color: #fff;text-align: center;">
    <tr>
      <th scope="col">Name</th><th scope="col">Admission No</th>
      @for($i=1; $i<=$lastdate; $i++)
        <th scope="col">{{$i}}</th>
      @endfor 
      <th scope="col">Action</th>
    </tr>
</thead>
<tbody style="text-align: center;">

    @foreach($students as $student)

    <?php
            $holiday = $student['holidays_list'];
            $v = array(); 
           foreach ($holiday as $key => $value) {
           $item = $value->holiday;
           array_push($v,$item);
              }
?>
        <tr>
             <th scope="row">{{$student['name']}}</th> 
            <th scope="row">{{$student['admission_no']}}</th>
        @for($i=1; $i<=$lastdate; $i++)
        
            @if(isset($student['attendance']) && !empty($student['attendance']) && is_array($student['attendance']))
                @php($day = 'day_'.$i) 
           
                @if($student['attendance'][$day] == 1)
                    <td><i class="fa fa-check greentick" aria-hidden="true"></i></td> 
                @elseif($student['attendance'][$day] == 2)
                    <td><i class="fa fa-times redcross" aria-hidden="true"></i></td>
                @else 
                    <?php $todate = date('Y-m-d');
                        $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
                    @if($current > $todate)
                        <td></td>
                    @else 
                        @if($student['class_id'] == $class_id)
                        @else  
                        @endif
                    @if(in_array($i,$v))
                    <td></td>
                    @else
                     <td id="{{$student['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$student['id']}}, 1, {{$i}}, this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$student['id']}}, 2, {{$i}}, this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                        </td> 
                        @endif
                    @endif
                @endif

                
            @else 
            <?php $todate = date('Y-m-d');
                $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i)));?>
                @if($current > $todate)
                    <td></td>
                @else 
                    @if($student['class_id'] == $class_id)
                    @else <!-- 
                    <td></td> -->
                    @endif
                    @if(in_array($i,$v))
                    <td></td>
                    @else
                    <td id="{{$student['id']}}_{{$i}}"> 
                        <p onclick="putattendance({{$student['id']}}, 1, {{$i}}, this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                        <p onclick="putattendance({{$student['id']}}, 2, {{$i}}, this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                    </td> 
                    @endif
                @endif
            @endif
        @endfor 
        <td><a target="_blank" href="{{URL('/')}}/admin/entryattendance/{{$student['enc_id']}}/{{$monthyear}}/{{$class_id}}/{{$section_id}}" style="padding-top:0px;"><i class="fa fa-pencil" aria-hidden="true"></i></a></td>
        </tr>
    @endforeach
   
</tbody>
@else 
<tbody style="text-align: center;"><tr><td>No Students</td></tr>
@endif  
