@extends('layouts.admin_master')
@section('sch_settings', 'active')
@section('master_students', 'active')
@section('menuopensch', 'active menu-is-opening menu-open')
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
                        <div class="col-md-3">
                            <div class="box box-primary border">
                                <div class="box-body box-profile">
                                  <img class="profile-user-img img-responsive img-circle" style="margin-left: 30%;" src="{{$user_details['is_profile_image']}}" alt="User profile picture">

                                  <h3 class="profile-username text-center">{{$user_details['name']}}</h3>

                                  <p class="text-muted text-center">Employee No : {{$user_details['teachers']['emp_no']}}</p> 

                                  <ul class="list-group list-group-unbordered">
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
                                    <li class="list-group-item">
                                      <b>Anniversary</b> <a class="float-right">{{$user_details['teachers']['is_anniversary']}}</a>
                                    </li>
                                  </ul>
 
                                </div>
                                <!-- /.box-body -->
                            </div> 
                        </div>
                        <div class="col-md-9">
                            <div class="box border">
                                <div class="box-body ">
                                    <div class="row">
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Full Name </label>
                                            <div class="form-line">{{$user_details['name']}} {{$user_details['last_name']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Gender </label>
                                            <div class="form-line">{{$user_details['gender']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Date of Birth </label>
                                            <div class="form-line">
                                            @if(!empty($user_details['dob']))
                                            {{date('d-M-Y', strtotime($user_details['dob']))}}
                                            @endif
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Last Login Date </label>
                                            <div class="form-line">@if(!empty($user_details['last_login_date']))
                                            {{date('d-M-Y', strtotime($user_details['last_login_date']))}}
                                            @endif</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Last App opened Date </label>
                                            <div class="form-line">@if(!empty($user_details['last_app_opened_date']))
                                            {{date('d-M-Y', strtotime($user_details['last_app_opened_date']))}}
                                            @endif</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Joined Date </label>
                                            <div class="form-line">@if(!empty($user_details['teachers']['date_of_joining']))
                                            {{date('d-M-Y', strtotime($user_details['teachers']['date_of_joining']))}}
                                            @endif</div>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="box border">
                                <div class="box-body ">
                                    <div class="row">
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Father Name </label>
                                            <div class="form-line">{{$user_details['teachers']['father_name']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Post Details </label>
                                            <div class="form-line">{{$user_details['teachers']['post_details']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Qualification</label>
                                            <div class="form-line">{{$user_details['teachers']['qualification']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Experience </label>
                                            <div class="form-line">{{$user_details['teachers']['exp']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Address </label>
                                            <div class="form-line">{{$user_details['teachers']['address']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Country </label>
                                            <div class="form-line">{{$user_details['is_country_name']}}
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">State </label>
                                            <div class="form-line">{{$user_details['is_state_name']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">District </label>
                                            <div class="form-line">{{$user_details['is_district_name']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Pincode </label>
                                            <div class="form-line">{{$user_details['teachers']['pincode']}}</div>
                                        </div> 
                                    </div>
                                </div>
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
                            <h4>Invalid SCholar Request</h4>
                        </div> 
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection