@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_sattendance', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Edit Student Attendance', 'active'=>'active']];
?>
@section('content') 


<meta name="csrf-token" content="{{ csrf_token() }}"> 
<section class="content">
    <!-- Exportable Table -->
    <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4  style="font-size: 20px;" class="card-title">Edit Students Attendance
               
              </h4>        
                
            </div> 
            <div class="card-content collapse show">
              <div class="card-body card-dashboard">
                <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                    <input type="hidden" name="class_id" id="class_id" value="{{$class_id}}">
                    <input type="hidden" name="section_id" id="section_id" value="{{$section_id}}">
                    
                    @if(!empty($players))
                    @foreach($players as $player)
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <input type="hidden" name="player_id" id="player_id" value="{{$player['id']}}">
                       <label style="padding-bottom: 10px;">Student Name</label>
                        <span type="text" class="form-control" style="margin: 0px 0 23px !important;padding: 7px 22px !important;">{{$player['name']}}</span>
                    </div>
                    <div class="col-md-4">
                        <label style="padding-bottom: 10px;">Select Month</label>
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
                        @include('admin.playersattendanceentries',['player'=>$player])
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
        function putattendance(student_id, mode, day, obj){
            var monthyear = $('#monthyear').val();
            var slot = $('#slot').val();
            var class_id = $('#class_id').val();
            var section_id = $('#section_id').val();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/update/studentattendance')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    monthyear:monthyear,student_id:student_id,mode:mode,day:day,slot:slot,class_id:class_id,section_id:section_id
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                if(response.status == 1) {
                    if(mode == 1) {
                        $(obj).parents('tr').find('#'+student_id+'_'+day).html('<p class="greencherck"><i class="fa fa-check" aria-hidden="true"></i> Present</p>');
                    }   else {
                        $(obj).parents('tr').find('#'+student_id+'_'+day).html('<p class="redcherck"><i class="fa fa-times" aria-hidden="true"></i> Absent</p>');
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