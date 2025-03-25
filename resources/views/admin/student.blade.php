@extends('layouts.admin_master')
@section('sch_settings', 'active')
@section('master_students', 'active')
@section('menuopensch', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Student', 'active' => 'active']];
?><?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@section('content')
    <link rel="stylesheet" href="{{asset('public/css/select2.min.css') }}"> 
    <style type="text/css">
        .modal-full {
            min-width: 95%;
            margin: 10;
        }
        .modal-full .modal-body {
            overflow-y: auto;
        }
        .dataTables_filter {
           display: block !important;
        }
    </style>
    <style>
        .dropdown-menu.show {
            display: block;
            width: 100%;
            top: 30px !important;
            left: auto !important;
            padding: 20px;
        } 
        .select2-container--default .select2-selection--single {
            height: 45px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-top: 8px;
        }
        .select2-container{
            width:100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #f8fafa;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }
           
        .scrollable-form {
            max-height: 200px;
            overflow-y: auto;
        }
        #noResults {
            color: red;
            font-weight: bold;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            border-color: #006fe6 !important;
        }

        #myTabs li {
                padding: 10px
        }

        #myTabs li.active {
            border: 1px solid #FF6F61;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title"><!-- Students  -->

                            <div class="row col-md-12">
                                <div class="form-group col-md-2 " >
                                    <label class="form-label mr-1">Class</label>
                                    <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadSection(this.value)">
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-2 " >
                                    <label class="form-label mr-1">Section</label>
                                    <select class="form-control" name="section_id" id="section_id" required> 
                                        <option value="" >Select Section</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2 " >
                                    <label class="form-label mr-1">Status</label>
                                    <select class="form-control" name="status_id" id="status_id">
                                        <option value="" >All</option>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div> 
                                <div class="form-group col-md-2"> 
                                    <label class="form-label">Is App Installed</label>
                                    <select class="form-control" name="is_app_installed" id="is_app_installed">
                                        <option value="" >All</option>
                                        <option value="yes" @if($app == 'yes') selected @endif>Yes</option>
                                        <option value="no" >No</option>
                                    </select> 
                                </div>


                                <div class="form-group col-md-2" >
                                    <label class="form-label"></label> 
                                    <button class="btn btn-danger "  id="clear_style">Clear Filter</button>
                                </div> 

                                <div class="form-group col-md-2"> 
    
                                @if( ($user_type == 'SCHOOL'))
                                <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>

                                <a href="{{URL('/')}}/admin/import_students" ><button id="addbtn" class="btn btn-primary" style="float: right;margin-right: 10px;">Import</button></a>
                                @endif

                                </div>
                            </div> 
                            
                        </h4>
                        <div class="row">    

                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries">
                                        <thead>
                                            <tr>
                                                <th>Admn. No.</th>
                                                <th>Photo</th>
                                                <th>First Name</th> 
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Primary Mobile</th>
                                                <th>Father Name</th> 
                                                <th>Status</th>
                                                <th class="no-sort">App Installed</th>
                                                <th class="no-sort nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="d-none">
                                            <tr>
                                                <th></th><th></th><th></th>  
                                                <th></th><th></th><th></th> 
                                                <th></th><th></th><th></th> 
                                                <th></th> 
                                            </tr>
                                        </tfoot>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Students</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/student') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-12 ">
                                <div class="form-group form-float float-left col-md-9">
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Name <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Last name</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="lastname" >
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4 ">
                                        <label class="form-label">Email </label>
                                        <div class="form-line">
                                            <input type="email" class="form-control" name="email">
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Password <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="password" minlength="6" maxlength="12" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Roll No </label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="roll_no"  minlength="4" maxlength="10"  onkeypress="return isNumber(event, this)">
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Admission No <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="admission_no" required minlength="4" maxlength="10">
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Class <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <select class="form-control course_id" name="class_id"
                                                onchange="loadClassSection(this.value)" required>
                                                <option value="">Select Class</option>
                                                @if (!empty($classes))
                                                    @foreach ($classes as $course)
                                                        <option value="{{ $course->id }}">{{ $course->class_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Section <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <select class="form-control" name="section_id" id="section_dropdown" required>

                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group form-float float-left col-md-4">
                                        <label class="form-label">Primary Mobile <span class="manstar">*</span></label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-3">
                                    <div class="d-flex justify-content-center mb-4">
                                        <?php $defimg = config("constants.APP_IMAGE_URL"). 'image/default.png'; ?>
                                        <input type="hidden" name="defimg" id="defimg" value="{{$defimg}}">
                                        <img id="selectedAvatar" src="{{$defimg}}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Image" />
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                            <label class="form-label text-white m-1" for="customFile2">Choose file</label>
                                            <input type="file" class="form-control d-none" id="customFile2" name="profile_image" onchange="displaySelectedImage(event, 'selectedAvatar')" />
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Alternate Mobile </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile1"  minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Emergency Contact Number </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="emergency_contact_no"  minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>
                            
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Gender <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Date of Birth <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="date" class="form-control" max="<?php echo date("Y-m-d"); ?>" name="dob" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Joined Date</label>
                                <div class="form-line">
                                    <input type="date" class="form-control" max="<?php echo date("Y-m-d"); ?>" name="joined_date" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-3 d-none">
                                <label class="form-label">Photo </label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_images" >
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Father Name </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="father_name" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Address </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="address" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Country </label>
                                <div class="form-line">
                                    <select class="form-control" name="country" onchange="myFunction(this.value)"
                                        >
                                        <option value="" disabled selected>--Select Country--</option>
                                        @foreach ($countries as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">State </label>
                                <div class="form-line">
                                    <div class="form-group form-float">

                                        <div class="form-line">
                                            <select id="state-dropdown" class="form-control" name="state_id"
                                                onchange="stateFunction(this.value)" >
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-3 ">
                                <label class="form-label">City</label>
                                <div class="form-line">
                                    <select id="edit_districts-dropdown" class="form-control" name="city_id" >
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-3">
                                <label class="form-label">Pincode </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="pincode" maxlength="6" onkeypress="return isNumber(event);">
                                </div>
                            </div>
                            <br>
                            <div class="form-group form-float float-right col-md-3">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" >
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <div class="modal fade in" id="smallModal-Allinone" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Student Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <input type="hidden" name="id" id="edit_allinone_id">
                </div>
                <div class="modal-body">
                    <div class="row">
        <div class="container-fluid">
            <ul class="nav nav-tabs" id="myTabs">
                <li class="active"><a href="#basic_details" id="libasic" data-url="basic">Basic Details</a></li>
                <li><a href="#additional_details" data-url="additional">Additional Details</a></li>
                <li><a href="#sibling_details" data-url="sibling">Siblings</a></li>
                <li><a href="#reward_details"  id="lireward" data-url="reward" >Remarks</a></li>
            </ul>
          
            <div class="tab-content">
                <div class="tab-pane active" id="basic_details">
                    <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/student') }}"  method="post">

                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="edit_id">

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group form-float float-left col-md-12 ">
                                    <div class="form-group form-float float-left col-md-9"> 

                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Name <span class="manstar">*</span></label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="name" id="edit_name" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Last name</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="lastname" id="edit_last_name">
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-4 ">
                                            <label class="form-label">Email </label>
                                            <div class="form-line">
                                                <input type="email" class="form-control" name="email" id="edit_email">
                                            </div>
                                        </div>

                                        <div class="form-group form-float float-left col-md-4 ">
                                            <label class="form-label">Password</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="password" id="edit_password" minlength="6" required maxlength="12">
                                            </div>
                                        </div>

                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Roll No </label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="roll_no" id="edit_roll_no"
                                                     minlength="4" maxlength="10"  onkeypress="return isNumber(event, this)">
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Admission No <span class="manstar">*</span></label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="admission_no"
                                                    id="edit_admission_no" required minlength="4" maxlength="10">
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Class <span class="manstar">*</span></label>
                                            <div class="form-line">

                                                <select class="form-control " name="class_id" id="edit_class_id"
                                                    onchange="loadClassSection(this.value)" required>
                                                    <option value="">Select Class</option>
                                                    @if (!empty($classes))
                                                        @foreach ($classes as $course)
                                                            <option value="{{ $course->id }}">{{ $course->class_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Section <span class="manstar">*</span></label>
                                            <div class="form-line">
                                                <select class="form-control" name="section_id" id="edit_section_dropdown" required>

                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group form-float float-left col-md-4">
                                            <label class="form-label">Primary Mobile <span class="manstar">*</span></label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="mobile" id="edit_mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group form-float float-left col-md-3">
                                        <div class="d-flex justify-content-center mb-4">
                                            <?php $defimg = config("constants.APP_IMAGE_URL"). 'image/default.png'; ?>
                                            <input type="hidden" name="defimg" id="defimg" value="{{$defimg}}">
                                            <img id="edit_selectedAvatar" src="{{$defimg}}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Image" />
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                                <label class="form-label text-white m-1" for="edit_customFile2">Choose file</label>
                                                <input type="file" class="form-control d-none" id="edit_customFile2" name="profile_image" onchange="editdisplaySelectedImage(event, 'edit_selectedAvatar')" />
                                            </div>
                                        </div>
                                    </div>
                                </div> 

                                <div class="form-group form-float float-left col-md-12" id="mobile_scholars"></div> 

                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Alternate Mobile </label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="mobile1" id="edit_mobile1"  minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)" >
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Emergency Contact Number </label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="emergency_contact_no"  minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)" id="edit_emergency_contact_no"  >
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Gender <span class="manstar">*</span></label>
                                    <div class="form-line">
                                        <select class="form-control" name="gender" id="edit_gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="MALE">Male</option>
                                            <option value="FEMALE">Female</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Date of Birth <span class="manstar">*</span></label>
                                    <div class="form-line">
                                        <input type="date" class="form-control" name="dob" id="edit_dob" required>
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Joined Date</label>
                                    <div class="form-line">
                                        <input type="date" class="form-control" name="joined_date" id="edit_joined_date" >
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-12 d-none">
                                    <label class="form-label">Photo </label>
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="profile_images">
                                    </div>
                                </div>


                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Father Name </label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="father_name" id="edit_father_name" >
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Address </label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="address" id="edit_address"
                                            >
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Country </label>
                                    <div class="form-line">
                                        <select class="form-control" id="edit_country-dropdown"
                                            onchange="myFunction(this.value)" name="country" >
                                            <option value="" disabled selected>--Select Country--</option>
                                            @foreach ($countries as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">State </label>
                                    <div class="form-line">
                                        <div class="form-group form-float">

                                            <div class="form-line">
                                                <select id="edit_state_dropdown" onchange="stateFunction(this.value)"
                                                    class="form-control" name="state_id" >
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-3 ">
                                    <label class="form-label">City </label>
                                    <div class="form-line">
                                        <select id="districts-dropdown" class="form-control" name="city_id" >
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-3">
                                    <label class="form-label">Pincode </label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="pincode" id="edit_pincode"  maxlength="6" onkeypress="return isNumber(event);"
                                            >
                                    </div>
                                </div>
                                <br>
                                <div class="form-group form-float float-right col-md-3">
                                    <label class="form-label">Status </label>
                                    <div class="form-line">
                                        <select class="form-control" name="status" id="edit_status" >
                                            <option value="ACTIVE">ACTIVE</option>
                                            <option value="INACTIVE">INACTIVE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6 d-none">
                                    <div class="form-line">
                                        <img src="" id="img_profile_image" height="100" name="profile_image"
                                            width="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="sumbit" class="btn btn-link waves-effect" id="edit_style">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>

                    </form>
                </div>
                <div class="tab-pane" id="additional_details">
                        
                    <form id="edit-additional-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/student_details') }}"  method="post">

                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="student_id">

                        <div class="modal-body">
                            <div class="row"> 
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Personal Identification Mark 1</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="identification_mark_1" id="edit_identification_mark_1" minlength="2" maxlength="50">
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Personal Identification Mark 2</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="identification_mark_2" id="edit_identification_mark_2" minlength="2" maxlength="50">
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-6 ">
                                    <label class="form-label">Aadhar Number</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="aadhar_number" id="edit_aadhar_number" minlength="12" maxlength="12" onkeypress="return isNumber(event);">
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-6 ">
                                    <label class="form-label">EMIS </label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="emis_id" id="edit_emis_id">
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Type</label>
                                    <div class="form-line">
                                        <select class="form-control" name="stay_type" id="edit_stay_type">
                                            <option value="DAYSCHOLAR" selected>Dayscholar</option>
                                            <option value="HOSTEL">Hostel</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Transport</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="transport" id="edit_transport">
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Blood Group</label>
                                    <div class="form-line">
                                        <select class="form-control" name="blood_group" id="edit_blood_group">
                                            <option value="">Select Blood Group </option>
                                            @if(!empty($blood_groups))
                                                @foreach($blood_groups as $bgrp)
                                                    <option value="{{$bgrp->id}}">{{$bgrp->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Religion</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="religion" id="edit_religion" minlength="3" maxlength="50">
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Community</label>
                                    <div class="form-line"> 
                                        <input type="text" class="form-control" name="community" id="edit_community" minlength="3" maxlength="50"> 
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Caste</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="caste" id="edit_caste" minlength="3" maxlength="50"> 
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="sumbit" class="btn btn-link waves-effect" id="edit_additional_style">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>

                    </form>

                </div>
                <div class="tab-pane" id="sibling_details">
                    
                    <form id="siblingstaff-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/scholarsiblingstaff')}}"
                                  method="post">

                        {{csrf_field()}} 
                        <div class="modal-body">
                            <input type="hidden" name="id" id="ss_student_id">
                            <div class="row"> 
                                <div class="form-group form-float float-left col-md-12">
                                    <label class="form-label">Students</label>
                                    <div class="form-line">
                                        <select class="form-control select2" name="student_id[]" id="edit_student_id"  multiple>
                                            @foreach($get_student as $student)
                                            <option value="{{$student->user_id}}">{{$student->is_student_name}}-({{$student->is_class_name}}-{{$student->is_section_name}})</option>
                                            @endforeach  
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-12">
                                    <label class="form-label">Staffs</label>
                                    <div class="form-line">
                                        <select class="form-control select2" name="staff_id[]" id="edit_staff_id"  multiple>
                                            @foreach($get_staff as $staff)
                                            <option value="{{$staff->id}}">{{$staff->name}} - {{$staff->mobile}}</option>
                                            @endforeach  
                                        </select>
                                    </div>
                                </div>
      
                            </div>
                        </div>
                        <div class="modal-footer"> 
                            <button type="sumbit" class="btn btn-link waves-effect" id="edit_siblingstaff_style">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>

                    </form>

                </div>
                <div class="tab-pane" id="reward_details">
                    
                    <form id="reward-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/scholarreward')}}"
                                  method="post">

                        {{csrf_field()}} 
                        <div class="modal-body">
                            <input type="hidden" name="id" id="rew_student_id">
                            <div class="row"> 
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Reward / Remark</label>
                                    <div class="form-line">
                                        <select class="form-control" name="remark_type" id="edit_remark_type" required> 
                                            <option value="REWARD">Reward</option> 
                                            <option value="REMARK">Remark</option> 
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Post Notification</label>
                                    <div class="form-line">
                                        <input type="checkbox" class="form-control" name="remark_notify" value="1">
                                    </div>
                                </div>

                                <div class="form-group form-float float-left col-md-12">
                                    <label class="form-label">Description</label>
                                    <div class="form-line">
                                        <textarea class="form-control" name="remark_description" id="edit_remark_description" required></textarea>
                                    </div>
                                </div>
        
                            </div>
                        </div>
                        <div class="modal-footer"> 
                            <button type="sumbit" class="btn btn-link waves-effect" id="edit_reward_style">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Student</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                
            </div>
        </div>
    </div>

    <div class="modal fade in" id="smallModal-3" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Move Student</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="move-style-form" enctype="multipart/form-data" action="{{ url('/admin/savemove/student') }}"  method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Alumni Status <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="alumni_status" id="alumni_status" required>
                                        <option value="">Move To</option>
                                        <option value="TC">TC</option>
                                        <option value="DISCONTINUE">DISCONTINUE</option>
                                    </select>
                                </div>
                            </div>          
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="sumbit" class="btn btn-link waves-effect" id="move_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="smallModal-4" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Student Additional Details</h4> 
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                
            </div>
        </div>
    </div>

    <div class="modal fade in" id="smallModal-5" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Sibling / Staff Details</h4> 
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    
    <script src="{{asset('public/js/select2.full.min.js') }}"></script>
    <script>
        $('.select2').select2();
        $(document).ready(function() {
            // Function to filter list items based on search term
            function filterList(inputId, itemClass, noResultsId) {
                $(inputId).on('input', function() {
                    var searchTerm = $(this).val().toLowerCase();
                    var found = false;

                    $(itemClass).each(function() {
                        var itemName = $(this).find('label').text().toLowerCase();
                        if (itemName.includes(searchTerm)) {
                            $(this).show();
                            found = true;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (found) {
                        $(noResultsId).hide();
                    } else {
                        $(noResultsId).show();
                    }
                });
            }

            // Initialize the search functionality for groups, students, and sections  
            filterList('#siblingstaff-style-form #searchStudent', '#siblingstaff-style-form .studentItem', '#siblingstaff-style-form #noStudentResults'); 
            filterList('#siblingstaff-style-form #searchStaff', '#siblingstaff-style-form .staffItem', '#siblingstaff-style-form #noStaffResults');
        });
    </script>

    <script>
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
            $('#selectedAvatar').attr('src', $('#defimg').val());
        });
        $('#clear_style').on('click', function () {
            $('.card-header').find('input').val('');
            $('.card-header').find('select').val('');
            $('.tblcountries').DataTable().ajax.reload();
        }); 
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/student/datatables/",  
                    data: function ( d ) {
                        var status_id  = $('#status_id').val();
                        var section_id = $('#section_id').val();
                        var class_id = $('#class_id').val();
                        var is_app_installed = $('#is_app_installed').val();
                        $.extend(d, { 
                            status_id:status_id,
                            section_id:section_id,
                            class_id:class_id,
                            is_app_installed:is_app_installed 
                        });
                    }
                },
                columns: [
                    { data: 'admission_no', name: 'students.admission_no' },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            if (data.profile_image != '' || data.profile_image != null) {
                                var tid = data.is_profile_image;
                                return '<img src="' + tid + '" height="50" width="50">';
                            } else {
                                return '';
                            }
                        },

                    },
                    { data: 'name', name: 'users.name' },
                    { data: 'class_name', name: 'classes.class_name' },
                    { data: 'section_name',  name: 'sections.section_name' },  
                    { data: 'mobile',  name: 'mobile' }, 
                    {  data: 'father_name', name: 'students.father_name' },  
                    { data: 'status', name: 'users.status' },
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.is_app_installed == 1){ 
                                return 'Yes'; 
                            }   else {
                                return '-';
                            }
                        }, name: 'users.is_app_installed'

                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            /*<a href="#" onclick="deleteStudent(' + tid +
                                ')" title="Delete Students"><i class="fas fa-trash"></i></a>*/
                            var tid = data.id;
                            var vurl = "{{URL('/')}}/admin/view_student?id="+tid;
                            /*return '<a href="#" onclick="loadStudentAllinone(' + tid +')" title="Edit Scholar"><i class="fas fa-user mr-1"></i></a>&nbsp;&nbsp;  <a href="#" onclick="loadStudent(' + tid +')" title="Edit Scholar"><i class="fas fa-edit mr-1"></i></a>&nbsp;&nbsp;<a href="#" onclick="loadStudentAdditionals(' + tid +
                                ')" title="Edit Additional Details"><i class="fas fa-list mr-1"></i></a>&nbsp;&nbsp;<a href="#" onclick="loadStudentSiblingStaffs(' + tid +
                                ')" title="Edit Sibling Staffs Details"><i class="fas fa-users mr-1"></i></a>&nbsp;&nbsp;<a href="'+vurl+'"  title="View Scholar" target="_blank"><i class="fas fa-eye mr-1"></i></a>&nbsp;&nbsp;<a href="#" onclick="deleteStudent(' + tid +
                                ')" title="Delete Scholar"><i class="fas fa-trash mr-1"></i></a>&nbsp;&nbsp;<a href="#" onclick="moveStudent(' + tid +
                                ')" title="Move Scholar"><i class="fas fa-suitcase mr-1"></i></a>';*/

                            return '<a href="#" onclick="loadStudentAllinone(' + tid +')" title="Edit Scholar"><i class="fas fa-edit mr-1"></i></a>&nbsp;&nbsp; <a href="#" onclick="addRemark(' + tid +')" title="Add Remark"><i class="fas fa-user-tag mr-1"></i></a>&nbsp;&nbsp; <a href="'+vurl+'"  title="View Scholar"  ><i class="fas fa-eye mr-1"></i></a>&nbsp;&nbsp;<a href="#" onclick="deleteStudent(' + tid +
                                ')" title="Delete Scholar"><i class="fas fa-trash mr-1"></i></a>&nbsp;&nbsp;<a href="#" onclick="moveStudent(' + tid +
                                ')" title="Move Scholar"><i class="fas fa-suitcase mr-1"></i></a>';
                        },

                    },

                ],
                order:[[0, 'asc']],
                "columnDefs": [
                    { "targets": 'no-sort', "orderable": false, },
                    { "targets": 'nowrap', "className": 'nowrap', },
                ], 
                dom: 'Bfrtip',
                "buttons": [
                    {

                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-warning btn-md ml-3',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                "url":"{{URL('/')}}/admin/student_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Scholars.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],


            });


            /*$('.tblcountries tfoot th').each(function(index) {
                if ( index != 1 && index != 7 && index != 8 && index != 9) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });*/

            $('#is_app_installed').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#status_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#section_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#class_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            // Apply the search
            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });
            $('#edit_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            //$('#smallModal-2').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });
            $('#edit_additional_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_additional_style").text('Processing..');

                        $("#edit_additional_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_additional_style").prop('disabled', false);

                        $("#edit_additional_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            //$('.tblcountries').DataTable().ajax.reload();

                            //$('#smallModal-4').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_additional_style").prop('disabled', false);

                        $("#edit_additional_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-additional-style-form").ajaxForm(options);
            });

            $('#edit_siblingstaff_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_siblingstaff_style").text('Processing..');

                        $("#edit_siblingstaff_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_siblingstaff_style").prop('disabled', false);

                        $("#edit_siblingstaff_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            //$('.tblcountries').DataTable().ajax.reload();

                            //$('#smallModal-5').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_siblingstaff_style").prop('disabled', false);

                        $("#edit_siblingstaff_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#siblingstaff-style-form").ajaxForm(options);
            });

            $('#edit_reward_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_reward_style").text('Processing..');

                        $("#edit_reward_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_reward_style").prop('disabled', false);

                        $("#edit_reward_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success'); 

                            $("#reward-style-form")[0].reset();

                            $('#smallModal-Allinone').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_reward_style").prop('disabled', false);

                        $("#edit_reward_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#reward-style-form").ajaxForm(options);
            });

            $('#move_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#move_style").text('Processing..');

                        $("#move_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#move_style").prop('disabled', false);

                        $("#move_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal-3').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#move_style").prop('disabled', false);

                        $("#move_style").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#move-style-form").ajaxForm(options);
            });
        });

        function loadStudentAllinone(id) { 
            $('#edit_allinone_id').val(id);
            $('#myTabs li').removeClass('active');
            /*$('#libasic').addClass('active');
            loadStudent(id);*/
            $('#libasic').trigger("click");
            $('#smallModal-Allinone').modal('show');
        }

        function addRemark(id) {
            $('#edit_allinone_id').val(id);
            $('#lireward').trigger("click");
            $('#smallModal-Allinone').modal('show');
        }

        function loadStudent(id) {
            $('#edit-style-form')[0].reset();
            $("#mobile_scholars").html('');
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/student') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                $('#edit_id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_last_name').val(response.data.last_name);
                $('#edit_gender').val(response.data.gender);
                $('#edit_email').val(response.data.email);
                $('#edit_password').val(response.data.passcode);
                $('#edit_mobile1').val(response.data.mobile1)
                $('#edit_dob').val(response.data.dob);
                $('#edit_joined_date').val(response.data.joined_date);
                $('#edit_mobile').val(response.data.mobile);
                if(response.data.roll_no != 0) {
                    $('#edit_roll_no').val(response.data.roll_no);
                }
                $('#edit_class_id').val(response.data.class_id);
                $('#edit_emergency_contact_no').val(response.data.emergency_contact_no);
                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.section_name;
                loadClassSection(val, selectedid, selectedval);

                $('#edit_section_dropdown').val(response.data.section_id);
                $('#edit_admission_no').val(response.data.admission_no);
                $('#edit_father_name').val(response.data.father_name);
                $('#edit_address').val(response.data.address);
                $('#edit_pincode').val(response.data.pincode);
                $('#edit_country-dropdown').val(response.data.country);

                var val = response.data.country;
                var selectedid = response.data.state_id;
                var selectedval = response.data.state_name;
                myFunction(val, selectedid, selectedval);

                $('#edit_state_dropdown').val(response.data.state_id);
                var val = response.data.state_id;
                var selectedid = response.data.city_id;
                var selectedval = response.data.district_name;
                stateFunction(val, selectedid, selectedval);
                $('#edit_districts-dropdown').val(response.data.city_id);

                $('#edit_status').val(response.data.status);
                $('#edit_selectedAvatar').attr('src', response.data.is_profile_image);

                /* Mobile Scholars */
                if(response.data.mobile_scholars != '' && response.data.mobile_scholars != null) {
                    $.each(response.data.mobile_scholars, function(key, value) { 

                        var htmlcontent = '<label>Mobile Scholars: </label><br/>'; 
                        htmlcontent += '<div class="studentItem col-md-3 float-left border"> <label>'+value.is_student_name+' - '+value.is_class_name+' - '+value.is_section_name+' - '+value.admission_no+'</label> </div>';

                        $("#mobile_scholars").html(htmlcontent);
                    });
                }

                //$('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


        function deleteStudent(id){
            swal({
                title : "",
                text : "Are you sure to delete?",
                type : "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
            },
            function(isConfirm){
                if (isConfirm) {
                    var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('/admin/delete/students')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            id:id,
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if (response.status == "SUCCESS") {

                        swal('Success',response.message,'success');

                        $('.tblcountries').DataTable().ajax.reload();
                        }
                        else{
                            swal('Oops',response.message,'error');

                        //   $('.tblcountries').DataTable().ajax.reload();
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });
                }
            })


        }

        function moveStudent(id) {
            $('#move-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/move/student') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                $('#move-style-form #id').val(response.data.id);
                $('#alumni_status').val(response.data.alumni_status);   
                $('#smallModal-3').modal('show');

            });
            request.fail(function(jqXHR, textStatus) { 
                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


        function myFunction(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var idCountry = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#state-dropdown,#edit_state_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-states') }}",
                type: "POST",
                data: {
                    country_id: idCountry,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#state-dropdown').html(
                            '<option value="">-- Select State --</option>');
                    if (selid != null && selval != null) {

                        $("#edit_state_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');

                    } else {
                        $('#state-dropdown').html(
                            '<option value="">-- Select State --</option>');
                    }
                    $.each(res.states, function(key, value) {
                        $("#state-dropdown,#edit_state_dropdown").append('<option value="' + value
                            .id + '">' + value.state_name + '</option>');
                    });
                }
            });
        }

        function stateFunction(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";

            var idState = val;


            var selid = selectedid;
            var selval = selectedval;


            $("#districts-dropdown,#edit_districts-dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-districts') }}",
                type: "POST",
                data: {
                    state_id: idState,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    if (selid != null && selval != null) {
                        $("#edit_districts-dropdown").append('<option selected value="' + selid + '">' +
                            selval + '</option>');
                    } else {
                        $('#districts-dropdown,#edit_districts-dropdown').html(
                            '<option value="">-- Select City --</option>');
                    }
                    $.each(res.districts, function(key, value) {
                        $("#districts-dropdown,#edit_districts-dropdown").append('<option value="' +
                            value
                            .id + '">' + value.district_name + '</option>');
                    });
                }
            });
        }


        function loadClassSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#section_dropdown,#edit_section_dropdown').html(
                        '<option value="">-- Select Section --</option>');
                    if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }
                    $.each(res.section, function(key, value) {
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '">' + value.section_name + '</option>');
                    });
                }
            });
        }

        
        function loadSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#section_id').html(
                        '<option value="">-- Select Section --</option>');
                
                    $.each(res.section, function(key, value) {
                         $("#section_id").append('<option value="' + value
                            .id + '">' + value.section_name + '</option>');
                    });
                }
            });
        }

        
        function loadStudentAdditionals(id) {
            $('#edit-additional-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/student_additional') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                $('#student_id').val(response.data.id);
                $('#edit_identification_mark_1').val(response.data.identification_mark_1);
                $('#edit_identification_mark_2').val(response.data.identification_mark_2);
                $('#edit_aadhar_number').val(response.data.aadhar_number);
                $('#edit_emis_id').val(response.data.emis_id);
                $('#edit_stay_type').val(response.data.stay_type);
                $('#edit_transport').val(response.data.transport)
                $('#edit_blood_group').val(response.data.bloodgroup);
                $('#edit_religion').val(response.data.religion);
                $('#edit_community').val(response.data.community); 
                $('#edit_caste').val(response.data.caste);   
                //$('#smallModal-4').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        
        function loadStudentSiblingStaffs(id) {
            $('#siblingstaff-style-form')[0].reset();
            $('#edit_student_id').val('').trigger('change'); 
            $('#edit_staff_id').val('').trigger('change'); 
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/student_siblingstaffs') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                $('#ss_student_id').val(response.data.id);  
                if($.trim(response.data.sibling_ids) != '') {
                    var sibling_ids = response.data.sibling_ids;
                    sibling_ids = sibling_ids.split(',');
                    $('#edit_student_id').val(sibling_ids).trigger('change'); 
                } 
 
                if($.trim(response.data.staff_child_ids) != '') {
                    var staff_child_ids = response.data.staff_child_ids;
                    staff_child_ids = staff_child_ids.split(',');
                    $('#edit_staff_id').val(staff_child_ids).trigger('change'); 
                } 

                //$('#smallModal-5').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function displaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            } else {
                $('#selectedAvatar').attr('src', $('#defimg').val());
            }
        }

        function editdisplaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            } else {
                $('#edit_selectedAvatar').attr('src', $('#defimg').val());
            }
        } 


        $('#myTabs a').click(function (e) {
            e.preventDefault();
            $('#myTabs li').removeClass('active')
            var url = $(this).attr("data-url");
            $(this).parent('li').addClass('active');
            var href = this.hash;
            var pane = $(this);
            
            var allinone_id = $('#edit_allinone_id').val();
 
            // ajax load from data-url
            $(href).load(url,function(result){       
                if(url == 'basic')  {
                    loadStudent(allinone_id);
                } else if(url == 'additional') {
                    loadStudentAdditionals(allinone_id);
                } else if(url == 'sibling') {
                    loadStudentSiblingStaffs(allinone_id);
                } else if(url == 'reward')  {
                    $('#reward-style-form')[0].reset();
                    $("#rew_student_id").val(allinone_id);
                }
                pane.tab('show');
            });
        });

        // load first tab content
        /*$('#home').load($('.active a').attr("data-url"),function(result){
          $('.active a').tab('show');
        });*/

    </script>
@endsection
