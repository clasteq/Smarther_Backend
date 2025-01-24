@extends('layouts.teacher_master')
@section('dashboard', 'active')
@section('content')
    <?php
    $breadcrumb = [['url' => URL('/teacher/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Dashboard', 'active' => 'active']];
    ?>
@section('pagetitle', 'Dashboard')
<style type="text/css">
    .imgicons {
        background: #ccc;
        border: 1px #ccc;
        border-radius: 15PX;
        height:140px;
        padding: 12px;
    }
</style>
<div id="teacherdashboard" class="content">
    <div class="row">
        <div class="col-md-2 col-sm-3 col-xs-4 mr-2"> 

                <a href="{{ URL('/teacher/student') }}">
                    <img  src="{{ asset('/public/image/teacher/logo/new_student.png') }}" style="height:140px" />
                </a>
                <!-- /.info-box-content --> 
            <!-- /.info-box -->
        </div>

        <div class="col-md-2 col-sm-3 col-xs-4 mr-2">
          <a href="{{ URL('/teacher/homework') }}">
              <img  src="{{ asset('/public/image/teacher/logo/home_wrk.png') }}" style="height:140px"/>
          </a>
      </div>

        <div class="col-md-2 col-sm-3 col-xs-4 mr-2">
          <a href="{{ URL('/teacher/circulars') }}">
              <img  src="{{ asset('/public/image/teacher/logo/circular_2.png') }}" style="height:140px"/>
          </a>
      </div>

      <div class="col-md-2 col-sm-3 col-xs-4 mr-2">
        <a href="{{ URL('/teacher/studentsleave') }}">
            <img  src="{{ asset('/public/image/teacher/logo/studentleave.png') }}" style="height:140px"/>
        </a>
    </div>

    <div class="col-md-2 col-sm-3 col-xs-4 mr-2">
        <a href="{{ URL('/teacher/tleave') }}">
            <img  src="{{ asset('/public/image/teacher/logo/leave_apply.png') }}" style="height:140px"/>
        </a>
    </div>
    </div>
</div>
@endsection



@section('scripts')


@endsection
