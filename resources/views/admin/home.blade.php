@extends('layouts.admin_master')
@section('dashboard', 'active')
@section('content')  
<?php 
use App\Http\Controllers\CommonController;
$user_type = Auth::User()->user_type;
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Dashboard', 'active'=>'active']];
$session_module = session()->get('module'); 
?>

<style type="text/css">
        .actinput {
            background-color: white; 
            border-radius: 50px;
        }
        .photos {
            background-color: unset;
            width: 50%;
            border-radius: 50px;
        }
        .submitact {
            border-color: #fff;
            border-radius: 20%;
            border-style: hidden;
            background: #f8f6f6;
        }
        input[type=file] {
          display: block;
          color: red;
          font-style: oblique;
        }
        input[type=file]::file-selector-button {
          /*display: none;
           visibility:hidden;*/ 
        }
        .activityimage img {
            width: 70px;
            height: auto; /*200px;*/
            border-radius: 3%;
        }
        .editact {
            width: 20px;
            height: 20px !important;
        }
        .deleteact {
            width: 20px;
            height: 20px !important;
        }
        .likeact {
            cursor: pointer;
        }
        .w-15 {
            width: 15px !important;
        }
        .offerolympiaimg {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #fff !important;
            min-height: 322px !important;
            max-height: 322px !important;
            max-width: 712px !important;
            overflow-y: auto;
        }

        .offerolympia {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #000;
            overflow-y: auto;
        }

        .postsms .offerolympia {
          color: #000 !important;
        }
        .ml-15 {
            margin-left: 9rem !important;
        }

        blockquote {
            background-color: transparent;
            border-left: .2rem solid #007bff;
            margin: 1.5em .7rem;
            padding: .5em .7rem; 
        }

        .card-body {
          max-height: 375px;
          overflow-y: auto;
        }

        .post {
              padding-bottom: 10%;
        }

        .greentick {
            color: #A3D10C;
        }

        .redcross {
            color: #dc3545;
        }

        .yellow {
            color:#ffa300;
        }

        .bg-info {
          background-color: #17a2b89e !important;
        }  

        .bg-success {
          background-color: #28a745bf !important;
        }

        .bg-warning {
          background-color: #ffc107b5 !important;
          color: #fff !important;
        }

        .bg-danger {
          background-color: #dc3545cc !important;
        } 

        .shadow {
          box-shadow: 0 8px 15px rgba(0, 0, 0, 0.35)  !important;
        }

        .title {
          text-align: center;
          background: #ff6f61;
          color: #fff;
          padding: .5%;
          margin-left: .8%;
          margin-right: .8%;
          border-radius: 10px;
        }

        .ta-right {
          text-align: right;
        }

        .ta-center { 
          float: none;
          text-align: center;
        }

        .info-box-text {
          font-weight: 500;
        }

        .img-center { 
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 50%; 
        }

        .widget-user .widget-user-image { 
          top: 50px !important;
        }

        .widget-user .widget-user-header { 
            height: 107px !important; 
        }

        .widget-user .widget-user-image>img {
            border: 3px solid #fff !important;
            height: 90px !important;
            width: 90px !important;
        }

        .module_updated_status td { 
            text-wrap-mode: nowrap;
        } 
</style>

<meta name="csrf-token" content="{{ csrf_token() }}"> 
  @section('pagetitle', 'Dashboard') 

  @if(in_array($user_type, ['SUPER_ADMIN']))  
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3">

        <?php $url = URL('/').'/admin/schools'; ?>
        <div class="info-box elevation-3" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
          <span class="info-box-icon bg-info elevation-2"><i class="fas fa-user"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Schools</span>
          </div><div class="info-box-content">
            <span class="info-box-number ta-right">{{$schools_count}}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

    </div>

    <div class="row">
      <div class="col-md-4 float-left"> <h5 class="title"> SMS Credits </h5>  
        <div class="card">
          <div class="card-content collapse show">
            <div class=" card-dashboard">
              <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                <div class="table-responsicve">
                  <table class="table table-striped table-bordered smscredits">
                    <thead><th>School Name</th><th>Available Credits</th><th> </th></thead>
                    <tbody>
                      @if(!empty($school_credits))
                        @foreach($school_credits as $credits)

                          @if($credits->available_credits <= 1000) @php($style = 'background:red !important; color:white;')
                          @elseif($credits->available_credits > 1000 && $credits->available_credits <= 2000)  @php($style = 'background:yellow !important; color:red;')
                          @elseif($credits->available_credits > 2000) @php($style = 'background:green !important; color:white;')
                          @endif

                          <tr style="{{$style}}"><td>{{$credits->name}}</td><td>{{$credits->available_credits}}</td>
                            <td><a href="{{URL('/')}}/admin/smscredits?id={{$credits->school_id}}"  title="View Credit" target="_blank"><i class="fas fa-eye mr-1"></i></a></td>
                          </tr>
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>  

      <div class="col-md-8 float-left"> <h5 class="title"> Last Logged In Schools </h5>
        <div class="card">
        <div class="card-content collapse show">
        <div class=" card-dashboard">
        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
          <div class="table-responsicve">
            <table class="table table-striped table-bordered lastlogged">
              <thead><th>School Name</th><th>Email</th><th>Mobile</th><th>Last Logged At</th></thead>
              <tbody>
                @if(!empty($last_logged_in))
                  @foreach($last_logged_in as $logged)
                    <tr><td>{{$logged->name}}</td><td>{{$logged->email}}</td><td>{{$logged->mobile}}</td><td>{{$logged->last_login_date}}</td></tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div></div></div></div>
      </div>
    </div>

    <div class="row mb-3 mt-2 ">
      <div class="col-md-12"> <h5 class="title"> Modules </h5> </div> 
      <div class="col-md-12">
        <div class="card elevation-3" style="height: 100% !important;"> 
          <!-- /.card-header -->
          <div class="card body p-0">
            <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
              <div class="d-flex activity activityimage"> 
                  <p class="mt-2 ms-3"><input type="date" name="module_date" id="module_date" class="form-control" onchange="loadModuleStatus();" value="{{date('Y-m-d')}}"> </p> 
                  <p style="margin-left: 2%; margin-top: 1%;"><i class="far fa-dot-circle greentick" aria-hidden="true"></i> - Approved; <i class="far fa-dot-circle yellow" aria-hidden="true"></i> - Yet to Approve; <i class="far fa-dot-circle redcross" aria-hidden="true"></i> - Not Done</p> 
              </div>
              <div class="d-flex activity activityimage module_status">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-3 mt-2 ">
      <div class="col-md-12"> <h5 class="title"> Modules - Last Updated At </h5> </div> 
      <div class="col-md-12">
        <div class="card elevation-3" style="height: 100% !important;"> 
          <!-- /.card-header -->
          <div class="card body p-0">
            <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 "> 
              <div class="d-flex activity activityimage module_updated_status">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

  @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']))  

    @if($user_type == 'SCHOOL')
      @if($sms_available_credits <= 1000) @php($style = 'background:red !important;')
      @elseif($sms_available_credits > 1000 && $sms_available_credits <= 2000)  @php($style = 'background:yellow !important; color:red;')
      @elseif($sms_available_credits > 2000) @php($style = 'background:green !important;')
      @endif
    
      <div class="row">
        <div class="col-md-12"> <h5 class="title" style="{{$style}}">  <b>{{$sms_available_credits}} SMS Credits Available</b> </h5> </div>
      </div>
    @endif

    @if((isset($session_module['Fees Collection'])) || ($user_type == 'SCHOOL'))
        <div class="row">
          <div class="col-md-12"> <h5 class="title">Fee Collection</h5> </div>
          <div class="col-md-6"> 
            <div class="col-md-6 col-6 float-left">
              <!-- small box -->
              <div class="small-box bg-primary shadow">
                <div class="inner">

                  <h5>Total Amount</h5>
                  <h4><b>{{CommonController::price_format($overallAmount)}}</b></h4>
                </div>
                <div class="icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>

              </div>
            </div> 

            <?php $url = URL('/').'/admin/fee_report/collection'; ?> 
            <div class="col-md-6 col-6 float-left" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <!-- small box -->
              <div class="small-box bg-success shadow">
                <div class="inner">

                  <h5>Fee Collected</h5>
                  <h4><b>{{CommonController::price_format($overall_fee_collected)}}</b></h4>
                </div>
                <div class="icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>

              </div>
            </div> 

            <?php $url = URL('/').'/admin/conwai_fee_report/collection'; ?> 
            <div class="col-md-6 col-6 float-left" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <!-- small box -->
              <div class="small-box bg-warning shadow">
                <div class="inner">

                  <h5>Concession Amount</h5>
                  <h4><b>{{CommonController::price_format($overall_fee_concession)}}</b></h4>
                </div>
                <div class="icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>

              </div>
            </div> 

            <?php $url = URL('/').'/admin/waiver_fee_report/collection'; ?> 
            <div class="col-md-6 col-6 float-left" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <!-- small box -->
              <div class="small-box bg-info shadow">
                <div class="inner">

                  <h5>Waiver Amount</h5>
                  <h4><b>{{CommonController::price_format($overall_fee_waiver)}}</b></h4>
                </div>
                <div class="icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>

              </div>
            </div>  

            <div class="col-md-6 col-6 float-left">
              <!-- small box -->
              <div class="small-box bg-secondary shadow">
                <div class="inner">

                  <h5>Over Due Amount</h5>
                  <h4><b>{{CommonController::price_format($overdueAmount)}}</b></h4>
                </div>
                <div class="icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>

              </div>
            </div>  
            
            <?php $url = URL('/').'/admin/pending_fee_report/collection'; ?> 
            <div class="col-md-6 col-6 float-left" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <!-- small box -->
              <div class="small-box bg-danger shadow">
                <div class="inner">

                  <h5>Over All Pending</h5>
                  <h4><b>{{CommonController::price_format($overallOutstanding)}}</b></h4>
                </div>
                <div class="icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>

              </div>
            </div>  

          </div>
          <div class="col-md-6">
            <div id="chartContainer" style="height: 120%; width: 100%; margin-top: -10%;"></div>
          </div>
        </div> 
    @endif
    @if((isset($session_module['Scholars'])) || ($user_type == 'SCHOOL'))
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <?php $url = URL('/').'/admin/student'; ?>
            <div class="info-box elevation-3" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <span class="info-box-icon bg-info elevation-2"><i class="fas fa-user"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Scholars</span>
              </div><div class="info-box-content">
                <span class="info-box-number ta-right">{{$students_count}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <div class="col-12 col-sm-6 col-md-3">
          <?php $url = URL('/').'/admin/student?app=yes'; ?>
            <div class="info-box elevation-3" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <span class="info-box-icon bg-info elevation-2"><i class="fas fa-user-check"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">App Installed</span>
              </div><div class="info-box-content">
                <span class="info-box-number ta-right">{{$students_installed_count}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <div class="col-12 col-sm-6 col-md-3">
            <?php $url = URL('/').'/admin/staffs'; ?>
            <div class="info-box mb-3 elevation-3"  style="cursor: pointer;" onclick="window.location.href='{{$url}}'">
              <span class="info-box-icon bg-danger elevation-2"><i class="fas fa-user-graduate"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Staffs</span>
              </div><div class="info-box-content">
                <span class="info-box-number ta-right">{{$teachers_count}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <div class="col-12 col-sm-6 col-md-3"> 
            <div class="info-box mb-3 elevation-3" >
              <span class="info-box-icon bg-info elevation-2"><i class="fas fa-sms"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Credit</span>
              </div><div class="info-box-content">
                <span class="info-box-number ta-right" style="text-wrap: nowrap;">{{$sms_available_credits}} / {{$sms_total_credits}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>
  
        </div>   
    @endif
    @if((isset($session_module['Communication'])) || ($user_type == 'SCHOOL'))
        <div class="row mb-3 mt-2 "> 
          <div class="col-md-12"> <h5 class="title"> Communication </h5> </div>
          <div class="col-md-6 ">
            <div class="card elevation-3" style="height: 100% !important;">
              <div class="card-header">
                <h3 class="card-title ta-center">Posts</h3> 
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0"><?php //echo "<pre>"; print_r($posts_arr); exit; ?>
                  @foreach($posts_arr as $ak => $post)
                  @php($id = $post['id'])
                  <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
                      <div class="d-flex activity activityimage">
                        <?php $shortcode = $post['posted_user']['is_shortname']; ?>
                        @if(empty($post['posted_user']['profile_image']))
                        <svg class="col-md-2" height="100" width="100">
                          <defs>
                            <linearGradient id="grad1">
                              <stop offset="0%" stop-color="#FF6F61" />
                              <stop offset="100%" stop-color="#FF6F61" />
                            </linearGradient>
                          </defs>
                          <ellipse cx="30" cy="40" rx="30" ry="30" fill="url(#grad1)" />
                          <text fill="#ffffff" font-size="35" font-family="Verdana" x="5" y="55">{{$shortcode}}</text>
                          Sorry, your browser does not support inline SVG.
                        </svg>
                        @else 
                        <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle col-md-2">
                        @endif 
                          <!-- <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle"> -->
                          <p class="mt-2 ms-3"><b>{{$post['post_category']}} </b><br> @if(!empty($post['posted_user']['name_code'])) {{$post['posted_user']['name_code']}} @else {{$shortcode}} @endif <br> 
                          Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p> 
                      </div>
                      <div class="activitycontent mt-3">
                          <p>{{$post['title']}}</p>
                      </div>  
                      <?php 
                        if(isset($post['post_theme'])) {
                          $img = $post['post_theme']['is_image'];
                        } else {
                          $img = '';
                        }
                        $style = "background-image:url('".$img."'); background-size: cover;  background-repeat: no-repeat;";
                        $class = "offerolympiaimg";
                        if(!empty($post['image_attachment'])) {
                          $style = ''; $class = 'offerolympia';
                        }
                        if(empty($img)) {
                          $style .= " color:#000 !important; ";
                        }
                        $ogg = '';
                        if(!empty($post['media_attachment'])) {
                          $infoext = pathinfo($post['media_attachment']);
                          $ogg = $infoext['filename']. '.ogg';
                        }

                        $vogg = '';
                        if(!empty($post['video_attachment'])) {
                          $infoext = pathinfo($post['video_attachment']);
                          $vogg = $infoext['filename']. '.ogg';
                        }

                       ?>
                      <div class="activitycontent {{$class}}" style="{{$style}}">
                          <p>{!! $post['message'] !!}</p>
                      </div>  
                      @if(!empty($post['media_attachment']))
                      <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                        <audio controls>
                          <source src="{{$ogg}}" type="audio/ogg">
                          <source src="{{$post['is_attachment']}}" type="audio/mpeg">
                        Your browser does not support the audio element.
                        </audio>
                      </div>
                      @endif
                      @if(!empty($post['image_attachment']))
                      <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                         @foreach($post['is_image_attachment'] as $imga)
                            <img src="{{$imga['img']}}" height="100" width="100">
                         @endforeach
                      </div>
                      @endif
                      @if(!empty($post['files_attachment']))
                        <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                         @foreach($post['is_files_attachment'] as $imga)
                            <a href="{{$imga['img']}}" target="_blank"><img src="{{asset('/public/images/freefile.png')}}" height="30" width="30"></a>
                         @endforeach
                        </div>
                      @endif
                      @if(!empty($post['video_attachment']))
                      <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                      <video width="400" controls>
                        <source src="{{$post['is_video_attachment']}}" type="video/mp4">
                        <source src="{{$vogg}}" type="video/ogg">
                        Your browser does not support HTML video.
                      </video>
                      </div>
                      @endif
                      <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                          <div class=" ">
                             <div class="likeact float-left" id="likeact_{{$id}}" > <a href="#" javascript="void(0);">
                               <p>{{$post['sent_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['users_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">  {{$post['acknowledged_count']}} <img class="editact w-15" src="{{asset('/public/images/image 2269 (1).png')}}"></p> 
                               </a>
                              </div>  
                              <p class=" float-right">{{$post['is_created_ago']}}</p>

                          </div> 
                      </div>
                  </div>
                  @endforeach   
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card elevation-3" style="height: 100% !important;">
              <div class="card-header">
                <h3 class="card-title ta-center">SMS</h3> 
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                @foreach($postsms_arr as $ak => $post)
                @php($id = $post['id']) 
                <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 postsms">
                    <div class="d-flex activity activityimage">

                      <?php $shortcode = $post['posted_user']['is_shortname']; ?>
                      @if(empty($post['posted_user']['profile_image']))
                      <svg class="col-md-2" height="100" width="100">
                        <defs>
                          <linearGradient id="grad1">
                            <stop offset="0%" stop-color="#FF6F61" />
                            <stop offset="100%" stop-color="#FF6F61" />
                          </linearGradient>
                        </defs>
                        <ellipse cx="30" cy="40" rx="30" ry="30" fill="url(#grad1)" />
                        <text fill="#ffffff" font-size="35" font-family="Verdana" x="5" y="55">{{$shortcode}}</text>
                        Sorry, your browser does not support inline SVG.
                      </svg>
                      @else 
                      <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle col-md-2">
                      @endif 

                      <!-- @if(isset($post['posted_user']) && !empty($post['posted_user'])) 
                        <img src="{{$post['posted_user']['is_profile_image']}}" class="img-responsive img-circle">
                      @else 
                        <img src="{{URL('/')}}/public/image/default.png" class="img-responsive img-circle">
                      @endif -->
                        <p class="mt-2 ms-3"><b>{{$post['post_category']}} </b><br> 
                          @if(!empty($post['posted_user']['name_code'])) {{$post['posted_user']['name_code']}} @else {{$shortcode}} @endif <br> 
                        Notify At: {{date('d M, Y h:i A', strtotime($post['notify_datetime']))}}</p> 
                        <?php if(strtotime($post['notify_datetime']) > strtotime(date('Y-m-d H:i:s'))) {  ?>
                        <a href="{{URL('/')}}/admin/editpostsms?id={{$post['id']}}" title="Edit post" style="padding-left:60%;display: none;"><img class="editact w-15" src="{{asset('/public/images/edit 1.png')}}"></a> 
                        
                        <a href="#" onclick="deletepostsms({{$id}})"  title="Delete post" style="padding-left:1%;"><img class="deleteact w-15" src="{{asset('/public/images/delete.png')}}"></a>
                        <?php } ?>
                    </div>  
                    <div class="activitycontent offerolympia" >
                        <p>{!! $post['content'] !!}</p>
                    </div>  
                    <div class="col-md-12 justify-content-between likeicon mt-3 ms-4">
                        <div class=""> 
                          <div class="likeact float-left" id="likeact_{{$id}}" > <a href="{{URL('/')}}/admin/postsmsstatus?id={{$post['id']}}" target="_blank">
                            <p>{{$post['sent_count']}} <img class="editact w-15" src="{{asset('/public/images/check.png')}}"> / {{$post['users_count']}} <img class="editact w-15" src="{{asset('/public/images/read.png')}}">   
                            </p> 
                            </a>
                          </div> 
                          <p>{{$post['is_created_ago']}}</p> 
                        </div> 
                    </div>
                </div>
                @endforeach 
              </div>
            </div>
          </div>
        </div>
    @endif
    @if((isset($session_module['Homeworks'])) || ($user_type == 'SCHOOL'))
        <div class="row mb-3 mt-2 ">
          <div class="col-md-12"> <h5 class="title"> Homework </h5> </div> 
          <div class="col-md-12">
            <div class="card elevation-3" style="height: 100% !important;"> 
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
                  <div class="d-flex activity activityimage"> 
                      <p class="mt-2 ms-3"><input type="date" name="homework_date" id="homework_date" class="form-control" onchange="loadHomeworkStatus();" value="{{date('Y-m-d')}}"> </p> 
                      <p style="margin-left: 2%; margin-top: 1%;"><i class="far fa-dot-circle greentick" aria-hidden="true"></i> - Approved; <i class="far fa-dot-circle yellow" aria-hidden="true"></i> - Yet to Approve; <i class="far fa-dot-circle redcross" aria-hidden="true"></i> - Not Done</p>
                  </div>
                  <div class="d-flex activity activityimage homework_status">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    @endif
    @if((isset($session_module['Scholar Attendance'])) || ($user_type == 'SCHOOL'))
        <div class="row mb-3 mt-2 "> 
          <div class="col-md-12"> <h5 class="title"> Attendance </h5> </div> 
          <div class="col-md-12">
            <div class="card elevation-3" style="height: 100% !important;"> 
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="col-md-12 post mt-4 ms-md-5 ms-sm-2 ">
                  <div class="d-flex activity activityimage"> 
                      <p class="mt-2 ms-3"><input type="date" name="attendance_date" id="attendance_date" class="form-control" onchange="loadAttendanceStatus();" value="{{date('Y-m-d')}}"> </p> 
                      <p style="margin-left: 2%; margin-top: 1%;"><i class="far fa-dot-circle greentick" aria-hidden="true"></i> - Approved; <i class="far fa-dot-circle yellow" aria-hidden="true"></i> - Yet to Approve; <i class="far fa-dot-circle redcross" aria-hidden="true"></i> - Not Done</p>
                  </div>
                  <div class="d-flex activity activityimage attendance_status">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    @endif
    @if((isset($session_module['Scholars'])) || ($user_type == 'SCHOOL'))
        <div class="row">
          <div class="col-md-12"> <h5 class="title"> Birthday </h5> </div> 
          <!-- Left col -->
            <div class="col-md-6 mb-5">
            <div class="card elevation-3" style="height: 100% !important;">
              <div class="card-header">
              <h3 class="card-title ta-center">Scholar</h3> 
              </div>
              <!-- /.card-header -->
              <div class="card-body p-2">
                @if($student_birthdays->isNotEmpty())
                @foreach($student_birthdays as $userlist)
                <?php  $newDate = date("d M", strtotime($userlist->dob));  ?>

                <div class="col-md-6 mb-3 float-left">
                  <?php $url = URL('/').'/admin/view_student?id='.$userlist->id; ?>
                  <div class="card card-widget widget-user elevation-3" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">

                    <div class="widget-user-header bg-info">
                      <h3 class="widget-user-username">{{$userlist->name}}</h3>
                      <h5 class="widget-user-desc"> </h5>
                    </div>
                    <div class="widget-user-image">
                      <img class="img-circle elevation-2" src="{{$userlist->is_profile_image}}" alt="{{$userlist->name}}">
                    </div>
                    <div class="card-footer">
                      <div class="row">
                        <div class="col-sm-6 border-right">
                          <div class="description-block">
                            <h5 class="description-header">{{$newDate}}</h5> 
                          </div> 
                        </div>   
                        <div class="col-sm-6 ">
                          <div class="description-block">
                            <h5 class="description-header">{{$userlist->class_name}}  {{$userlist->section_name}}</h5> 
                          </div> 
                        </div> 
                      </div> 
                    </div>
                  </div> 
                </div>  
                @endforeach 
                @else 
                <img src="{{asset('/public/images/birthday.jpg')}}" class="img-center" >
                @endif
              <!-- /.users-list -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
              <a href="{{URL::to('/')}}/admin/student">View All Scholars</a>
              </div>
              <!-- /.card-footer -->
            </div>
          </div>
          

          <div class="col-md-6 mb-5">
            <div class="card elevation-3" style="height: 100% !important;">
              <div class="card-header">
              <h3 class="card-title ta-center">Staff</h3> 
              </div>
              <!-- /.card-header -->
              <div class="card-body p-2">  
                @if($staff_birthdays->isNotEmpty())
                @foreach($staff_birthdays as $userlist)
                <?php  $newDate = date("d M", strtotime($userlist->dob));  ?>

                <div class="col-md-6 mb-3 float-left">
                  <?php $url = URL('/').'/admin/view_staff?id='.$userlist->id; ?>
                  <div class="card card-widget widget-user elevation-3" style="cursor: pointer;" onclick="window.location.href='{{$url}}'">

                    <div class="widget-user-header bg-info">
                      <h3 class="widget-user-username">{{$userlist->name}}</h3>
                      <h5 class="widget-user-desc"> </h5>
                    </div>
                    <div class="widget-user-image">
                      <img class="img-circle elevation-2" src="{{$userlist->is_profile_image}}" alt="{{$userlist->name}}">
                    </div>
                    <div class="card-footer">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="description-block">
                            <h5 class="description-header">{{$newDate}}</h5> 
                          </div> 
                        </div>   
                      </div> 
                    </div>
                  </div> 
                </div> 
                @endforeach  
                @else 
                <img src="{{asset('/public/images/birthday.jpg')}}" class="img-center" >
                @endif
              <!-- /.users-list -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
              <a href="{{URL::to('/')}}/admin/staffs">View All Staffs</a>
              </div>
              <!-- /.card-footer -->
            </div>
          </div>
        </div>
    @endif
  @endif
@endsection



@section('scripts') 

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      @if(in_array($user_type, ['SUPER_ADMIN'])) 
      $('.lastlogged').DataTable({
        "order":[[3, 'desc']],
      });
      $('.smscredits').DataTable({
        "order":[[1, 'asc']],
      });

      @endif

      @if(!in_array($user_type, ['GUESTUSER', 'STUDENT', 'SUPER_ADMIN']))  
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Title', 'Amount'],
          ['Collected', {{$collected_percent}}],
          ['Concession', {{$concession_percent}}],
          ['Waiver', {{$waiver_percent}}],
          ['Pending', {{$pending_percent}}],
          ['Overdue', {{$due_percent}}], 
        ]);

        var options = {
          backgroundColor: 'transparent',
          /*title: 'My Daily Activities',(dc3545, e35864)  (28a745, 5eb66c)  (ffc107, ffcb46)*/
          is3D: true,
          slices: {
            0: { color: '#5eb66c' },
            1: { color: '#ffcb46' },
            2: { color: '#17a2b8' },
            3: { color: '#ff4800' },
            4: { color: '#e35864' },
          }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chartContainer'));
        chart.draw(data, options);
      }
      @endif
    </script>

<!-- <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>  
<script type="text/javascript" src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script> -->
<script type="text/javascript">
  
  /*window.onload = function() {

    var options = {
      title: {
        text: "Fee Collection"
      },
      data: [{
          type: "pie",
          startAngle: 45,
          showInLegend: "true",
          legendText: "{label}",
          indexLabel: "{label} ({y})",
          /*yValueFormatString:"#,##0.#"%"",* /
          dataPoints: [
            { label: "Collected", y: {{$collected_percent}}, color:"#28a745" },
            { label: "Concession", y: {{$concession_percent}}, color:"#007bff" },
            { label: "Pending", y: {{$pending_percent}}, color:"#dc3545" },
            { label: "Overdue", y: {{$due_percent}}, color:"#ff0000" }, 
          ]
      }]
      /*data: [
      {        
        type: "doughnut",
        indexLabelPlacement: "outside",        
        radius: "90%",  //change the radius here. 
        dataPoints: [
          { x: 10, y: {{$collected_percent}}, label: "Collected" },
          { x: 20, y: {{$concession_percent}}, label: "Concession"},
          { x: 30, y: {{$pending_percent}}, label: "Pending"},
          { x: 40, y: {{$due_percent}}, label: "Overdue"},     
        ]
      }
      ]* /
    };
    $("#chartContainer").CanvasJSChart(options);

    $('.canvasjs-chart-credit').css('display', 'none');

  }*/

  function loadHomeworkStatus()  {
      var hwdate = $('#homework_date').val();
      if(hwdate != '' && hwdate != null) {
          var request = $.ajax({
              type: 'post',
              url: " {{ URL::to('admin/load/homeworkstatus') }}",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                  hwdate: hwdate 
              },
              dataType: 'json',
              encode: true
          });
          request.done(function(response) {
              if (response.status == "SUCCESS") {
                  $('.homework_status').html(response.data);
              } else { 
                  swal("Oops!", response.message, "error");
              }

          });
          request.fail(function(jqXHR, textStatus) {

              swal("Oops!", "Sorry,Could not process your request", "error");
          });
      }   else {
          swal("Oops!", "Please select the Date", "error");
      }
  }

  function loadAttendanceStatus()   {
      var attdate = $('#attendance_date').val();
      if(attdate != '' && attdate != null) {
          var request = $.ajax({
              type: 'post',
              url: " {{ URL::to('admin/load/attendancestatus') }}",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                  attdate: attdate 
              },
              dataType: 'json',
              encode: true
          });
          request.done(function(response) {
              if (response.status == "SUCCESS") {
                  $('.attendance_status').html(response.data);
              } else { 
                  swal("Oops!", response.message, "error");
              }

          });
          request.fail(function(jqXHR, textStatus) {

              swal("Oops!", "Sorry,Could not process your request", "error");
          });
      }   else {
          swal("Oops!", "Please select the Date", "error");
      }
  }

  function loadModuleStatus()  {
      var moddate = $('#module_date').val();
      if(moddate != '' && moddate != null) {
          var request = $.ajax({
              type: 'post',
              url: " {{ URL::to('admin/load/modulestatus') }}",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                  moddate: moddate 
              },
              dataType: 'json',
              encode: true
          });
          request.done(function(response) {
              if (response.status == "SUCCESS") {
                  $('.module_status').html(response.data);
              } else { 
                  swal("Oops!", response.message, "error");
              }

          });
          request.fail(function(jqXHR, textStatus) {

              swal("Oops!", "Sorry,Could not process your request", "error");
          });
      }   else {
          swal("Oops!", "Please select the Date", "error");
      }
  }

  function loadModuleLastUpdatedStatus()  {  
          var request = $.ajax({
              type: 'post',
              url: " {{ URL::to('admin/load/moduleupdatedstatus') }}",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }, 
              dataType: 'json',
              encode: true
          });
          request.done(function(response) {
              if (response.status == "SUCCESS") {
                  $('.module_updated_status').html(response.data);
              } else { 
                  swal("Oops!", response.message, "error");
              }

          });
          request.fail(function(jqXHR, textStatus) {

              swal("Oops!", "Sorry,Could not process your request", "error");
          });
       
  }

  @if($user_type == "SCHOOL")
    @if((isset($session_module['Homeworks'])) || ($user_type == 'SCHOOL'))
    loadHomeworkStatus();
    @endif
    @if((isset($session_module['Scholar Attendance'])) || ($user_type == 'SCHOOL'))
    loadAttendanceStatus();
    @endif
  @endif

  @if(in_array($user_type, ['SUPER_ADMIN'])) 
  loadModuleStatus();
  loadModuleLastUpdatedStatus();
  @endif
</script>


@endsection
