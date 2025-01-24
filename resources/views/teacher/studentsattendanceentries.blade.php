
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<style>
    .form-control:focus {
        color: #495057;
        background-color: #fff !important;
        border: none;
        outline: 0;
        box-shadow: 0 0 0 0.2rem #dee2e6 !important;
    }

    .greentick {
        color: #A3D10C;
    }

    .redcross {
        color: #dc3545;
    }

    .greentickbox {
        color: #fff;
        background: #007bff;
        font-size: 10px;
        padding: 4px;
        cursor: pointer;
    }

    .redcrossbox {
        color: #fff;
        background: #dc3545;
        font-size: 13px;
        padding: 4px;
        margin-top: 5px;
        cursor: pointer;
    }

    .greentickboxharizondal {
        color: #fff;
        background: #007bff;
        font-size: 15px;
        padding: 5px 4px 4px 4px;
    }

    .redcrossboxharizondal {
        color: #fff;
        background: #dc3545;
        font-size: 15px;
        padding: 5px 4px 4px 4px;
       
    }

    .rowcen {
        padding-left: 6px;
        margin-top: 7px;
    }

    @media only screen and (max-width: 600px) {
        .my-account-form {
            overflow-x: scroll !important;
        }

    }
</style>
@endsection
@for($i=1; $i<=$lastdate; $i++)
<?php
$holiday = $player['holidays_list'];
$v = array(); 
foreach ($holiday as $key => $value) {
$item = $value->holiday;
array_push($v,$item);
  }
?>

<tr>
       <th scope="row">{{$i}}</th>
    @if(isset($player['attendance']) && !empty($player['attendance']) && is_array($player['attendance']))
        @php($day = 'day_'.$i)
        @if($player['attendance'][$day] == 1)
        <td  id="{{$player['id']}}_{{$i}}"><p class="greencherck"><i class="fa fa-check" aria-hidden="true"></i> Present</p></td>
        @elseif($player['attendance'][$day] == 2)
        <td  id="{{$player['id']}}_{{$i}}"><p class="redcherck"><i class="fa fa-times" aria-hidden="true"></i> Absent</p></td>
        @else
        <td  id="{{$player['id']}}_{{$i}}"></td>
        @endif
        <?php $todate = date('Y-m-d');
            $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i))); ?>
        @if($current > $todate)
            <td></td>
        @else
        @if(in_array($i,$v))
        <td></td>
        @else
            <td>
                <div class="row rac">
                    <i class="fa fa-check greentickboxharizondal" aria-hidden="true" onclick="putattendance({{$player['id']}}, 1, {{$i}}, this)"></i>&nbsp;&nbsp;
                    <i class="fa fa-times redcrossboxharizondal" aria-hidden="true" onclick="putattendance({{$player['id']}}, 2, {{$i}}, this)"></i>
                </div>
            </td>
            @endif
        @endif
    @else
 
        @php($day = 'day_'.$i)
        <td  id="{{$player['id']}}_{{$i}}"></td>
        <?php $todate = date('Y-m-d');
            $current = date('Y-m-d', strtotime(date($monthyear.'-'.$i)));?>
        @if($current > $todate)
            <td></td>
        @else
        @if(in_array($i,$v))
        <td></td>
        @else
        <td> 
            
            
            <div class="row rac">
                <i class="fa fa-check greentickboxharizondal" aria-hidden="true" onclick="putattendance({{$player['id']}}, 1, {{$i}}, this)"></i>&nbsp;&nbsp;
                <i class="fa fa-times redcrossboxharizondal" aria-hidden="true" onclick="putattendance({{$player['id']}}, 2, {{$i}}, this)"></i>
            </div>
        </td>
        @endif
        @endif
    @endif
</tr>
@endfor