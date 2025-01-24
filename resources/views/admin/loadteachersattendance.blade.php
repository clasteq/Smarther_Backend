 <?php  //echo "<pre>"; print_r($teachers); //exit;?>
@if(!empty($teachers) && count($teachers)>0)
  <thead style="background: #a3d10c;color: #fff;text-align: center;">
    <tr>
      <th scope="col">Name</th><th scope="col">Emp No</th>
      @for($i=1; $i<=$lastdate; $i++)
        <th scope="col">{{$i}}</th>
      @endfor 
      <th scope="col">Action</th>
    </tr>
</thead>
<tbody style="text-align: center;">

    @foreach($teachers as $teacher)
    <?php
    $holiday = $teacher['holidays_list'];
    $v = array(); 
   foreach ($holiday as $key => $value) {
   $item = $value->holiday;
   array_push($v,$item);
      }
?>

        <tr> <th scope="row">{{$teacher['name']}}</th> <th scope="row">{{$teacher['emp_no']}}</th>
        @for($i=1; $i<=$lastdate; $i++)
            @if(isset($teacher['teacherattendance']) && !empty($teacher['teacherattendance']) && is_array($teacher['teacherattendance']))
                @php($day = 'day_'.$i) 
                @if($teacher['teacherattendance'][$day] == 1)
                    <td><i class="fa fa-check greentick" aria-hidden="true"></i></td> 
                @elseif($teacher['teacherattendance'][$day] == 2)
                    <td><i class="fa fa-times redcross" aria-hidden="true"></i></td>
                @else 
                    <?php $todate = date('Y-m-d');
                        $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
                    @if($current > $todate)
                        <td></td>
                    @else 
                    @if(in_array($i,$v))
                    <td></td>
                    @else 
                        <td id="{{$teacher['id']}}_{{$i}}"> 
                            <p onclick="putattendance({{$teacher['id']}}, 1, {{$i}}, this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                            <p onclick="putattendance({{$teacher['id']}}, 2, {{$i}}, this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
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
                @if(in_array($i,$v))
                <td></td>
                @else   
                    <td id="{{$teacher['id']}}_{{$i}}"> 
                        <p onclick="putattendance({{$teacher['id']}}, 1, {{$i}}, this)"><i class="fa fa-check greentickbox" aria-hidden="true"></i></p>
                        <p onclick="putattendance({{$teacher['id']}}, 2, {{$i}}, this)"><i class="fa fa-times redcrossbox" aria-hidden="true"></i></p>
                    </td>
                    @endif 
                @endif
            @endif
        @endfor 
        <td><a target="_blank" href="{{URL('/')}}/admin/teacher_entryattendance/{{$teacher['enc_id']}}/{{$monthyear}}" style="padding-top:0px;"><i class="fa fa-pencil" aria-hidden="true"></i></a></td>
        </tr>
    @endforeach
</tbody>
@else 
<tbody style="text-align: center;"><tr><td>No Students</td></tr>
@endif  
