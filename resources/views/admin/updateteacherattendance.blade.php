@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_tattendance', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Edit Teachers Attendance', 'active'=>'active']];
?>
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
        font-size: 10px;
        padding: 5px 4px 4px 4px;
    }

    .redcrossboxharizondal {
        color: #fff;
        background: #dc3545;
        font-size: 12px;
        padding: 4px;
        margin-top: 0px;
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
@section('content') 


<meta name="csrf-token" content="{{ csrf_token() }}"> 
<section class="content">
    <!-- Exportable Table -->
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4  style="font-size: 20px;" class="card-title">Teachers Attendance Entry
               
              </h4>        
                
            </div> 
            <div class="card-content collapse show">
              <div class="card-body card-dashboard">
                <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                    @if(!empty($players))
                    @foreach($players as $player)
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <input type="hidden" name="player_id" id="player_id" value="{{$player['id']}}"> 
                        <label style="padding-bottom: 10px;">Teacher Name</label>
                        <span type="text" class="form-control" style="margin: 0px 0 23px !important;padding: 7px 22px !important;">{{$player['name']}}</span>
                    </div>
                    <div class="col-md-4">
                        <label style="padding-bottom: 10px;" >Select Month</label>
                         <input type="month" readonly class="form-control" style="margin: 0px 0 23px !important;padding: 18px 22px !important;" name="monthyear" id="monthyear" value="{{$monthyear}}">
                    </div>
                    <div class="col-md-2" style="display: none">
                        <button type="submit" class="btn" style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px" onclick="loadPlayerattendanceEntries()">Search </button>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                <div class="row">
                    <table class="table table-bordered">
                      <thead style="background: #A3D10C;color: #fff;text-align: center;">
                        <tr>
                          <th scope="col">Date</th>
                          <th scope="col">Entry</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody style="text-align: center;" id="attendanceentries">
                        @include('admin.teachersattendanceentries',['player'=>$player])
                        <tr>
                      </tbody>
                    </table>
                </div>
                    @endforeach
                @endif
                </div>
              </div>
            </div> 
          </div>
        </div>
      </div>
</section> 
    <!-- =====>> End My Account <<=====
    =========================== -->
@endsection
@section('scripts')
<script type="text/javascript">
    $(function() {
    });
        function putattendance(teacherid, mode, day, obj){
            // alert(section_id);
         
            var monthyear = $('#monthyear').val();
            var slot = $('#slot').val();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/update/teacherattendance')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    monthyear:monthyear,teacherid:teacherid,mode:mode,day:day,slot:slot
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                if(response.status == 1) {
                    if(mode == 1) {
                        $(obj).parents('tr').find('#'+teacherid+'_'+day).html('<p class="greencherck"><i class="fa fa-check" aria-hidden="true"></i> Present</p>');
                    }   else {
                        $(obj).parents('tr').find('#'+teacherid+'_'+day).html('<p class="redcherck"><i class="fa fa-times" aria-hidden="true"></i> Absent</p>');
                    }
                } else {
                    swal("Oops!", response.message, "error");
                }
            });
            request.fail(function (jqXHR, textStatus) {
                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }
        function loadPlayerattendanceEntries(){
            var monthyear = $('#monthyear').val();
            var player_id = $('#player_id').val();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('load/playerattendanceentries')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    monthyear:monthyear,player_id:player_id
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                if(response.status == 1) {
                    $('#attendanceentries').html(response.data);
                } else {
                    $('#attendanceentries').html(response.message);
                }
            });
            request.fail(function (jqXHR, textStatus) {
                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }
</script>
@endsection