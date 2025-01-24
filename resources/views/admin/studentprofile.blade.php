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

                                  <p class="text-muted text-center">Admission No : {{$user_details['admission_no']}}</p>
                                  <p class="text-muted text-center"> {{$user_details['userdetails']['is_class_name']}} -  {{$user_details['userdetails']['is_section_name']}}</p>

                                  <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                      <b>Email</b> <a class="float-right">{{$user_details['email']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Mobile</b> <a class="float-right">{{$user_details['mobile']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Emergency Contact</b> <a class="float-right">{{$user_details['emergency_contact_no']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Status</b> <a class="float-right">{{$user_details['status']}}</a>
                                    </li>
                                  </ul>
 
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <div class="box box-primary border">
                                <div class="box-body box-profile">   

                                  <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                       Religion  <a class="float-right">{{$user_details['userdetails']['religion']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                       Community  <a class="float-right">{{$user_details['userdetails']['community']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                       Caste <a class="float-right">{{$user_details['userdetails']['caste']}}</a>
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
                                            <div class="form-line">@if(!empty($user_details['joined_date']))
                                            {{date('d-M-Y', strtotime($user_details['joined_date']))}}
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
                                            <div class="form-line">{{$user_details['userdetails']['father_name']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Blood Group </label>
                                            <div class="form-line">{{$user_details['userdetails']['is_blood_group']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Emis id </label>
                                            <div class="form-line">{{$user_details['userdetails']['emis_id']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Aadhar Number </label>
                                            <div class="form-line">{{$user_details['userdetails']['aadhar_number']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Address </label>
                                            <div class="form-line">{{$user_details['userdetails']['address']}}</div>
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
                                            <div class="form-line">{{$user_details['userdetails']['pincode']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Identification Mark 1 </label>
                                            <div class="form-line">{{$user_details['userdetails']['identification_mark_1']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Identification Mark 2</label>
                                            <div class="form-line">{{$user_details['userdetails']['identification_mark_2']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Stay </label>
                                            <div class="form-line">{{$user_details['userdetails']['stay_type']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Transport </label>
                                            <div class="form-line">{{$user_details['userdetails']['transport']}}</div>
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