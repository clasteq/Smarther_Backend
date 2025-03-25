@extends('layouts.admin_master')
@section('dashboard', 'active')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Student', 'active' => 'active']];
?>
@section('content')
<style type="text/css">
    .box.box-primary {
        border-top: 5px solid #3c8dbc !important;
    }

    .box {
        position: relative;
        border-radius: 3px;
        background: #ffffff;
        border-top: 3px solid #d2d6de;
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .profile-user-img {
        height: 100px !important;
    }
</style> 
<meta name="csrf-token" content="{{ csrf_token() }}"> 
<section class="content">
    <!-- Exportable Table -->
    <div class="content container-fluid">
        @if(!empty($user_details))
            <div class="card">
                <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary border">
                                <div class="box-body box-profile p-3">
                                  <img class="profile-user-img img-responsive img-circle" style="margin-left: 50%;" src="{{$user_details['is_profile_image']}}" alt="User profile picture">

                                  <h3 class="profile-username text-center">{{$user_details['name']}}</h3>

                                  <p class="text-muted text-center">Slugname : {{$user_details['slug_name']}}</p> 

                                  <ul class="list-group list-group-unbordered"> 
                                    <li class="list-group-item">
                                      <b>Display Name</b> <a class="float-right">{{$user_details['display_name']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Email</b> <a class="float-right">{{$user_details['email']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Mobile</b> <a class="float-right">{{$user_details['mobile']}}</a>
                                    </li> 
                                    <li class="list-group-item">
                                      <b>Password</b> <a class="float-right">{{$user_details['passcode']}}</a>
                                    </li> 
                                    <li class="list-group-item">
                                      <b>Status</b> <a class="float-right">{{$user_details['status']}}</a>
                                    </li> 
                                  </ul>
 
                                </div>
                                <!-- /.box-body -->
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        @else 
            <div class="card">
                <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Invalid Profile</h4>
                        </div> 
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection