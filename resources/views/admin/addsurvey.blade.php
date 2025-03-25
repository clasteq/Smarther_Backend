@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_survey', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
//$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Add Post', 'active' => 'active']];
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?>
@section('content')
@if((isset($session_module['Posts']) && ($session_module['Posts']['add'] == 1)) || ($user_type == 'SCHOOL'))
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

  <!-- include libraries BS
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.css" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.5/umd/popper.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.js"></script> --> 
  <style>
        .row.merged20 {
            margin: 0px 0px !important;
        }

        .sidecoderight {
            padding-top: 40px !important;
        }

        .nnsec {
            margin-left: -14px;
            margin-right: 10px;
            border-right: 1.5px solid #ecebeb85;
            padding-top: 40px !important;
        }

        @media screen and (max-width: 700px) {
            .nnsec {
                margin-left: 0px !important;
                margin-right: 0px !important;
                border-right: 0px solid #ecebeb85 !important;
                padding-top: 20px !important;
            }

            .row.merged20 {
                padding: 0px 0px !important;
            } 
        }

        .btn input[type="radio"] {
            display: none;
        }

        .scrollable-form {
            height: 200px;
            /* Adjust height as needed */
            overflow-y: scroll;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .scrollable-form {
            max-height: 200px;
            overflow-y: auto;
        }
        #noResults {
            color: red;
            font-weight: bold;
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

        .imgatt {
            width: 30px; height:30px;  cursor: pointer;
        }

        .fileatt {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        /*.fileatt + .imgatt {
            font-size: 1.25em;
            font-weight: 700;
            color: white;
            /*background-color: #007bff;* /
            display: inline-block;
        }

        .fileatt:focus + .imgatt,
        .fileatt + .imgatt:hover {
            /*background-color: #007bff;* /
        }*/


        .fileatt + img + label {
            font-size: 1em; 
            color: #000;
            background-color: #fff;
            display: inline-block;
            cursor: pointer;  
            padding: 2px;  
            border-radius: 5px;
            font-weight: 400 !important;
        }

        .fileatt:focus + img + label,
        .fileatt +  img + label:hover {
            background-color: #007bff;
        }

        .offerolympiaimg {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #fff !important;
            min-height:100% !important;
            max-height:100% !important;
            max-width: 100% !important;
            max-width: 100% !important;
            overflow-y: auto;
        }
  </style> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Create Survey
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('admin/save/survey') }}" method="post" id="post_survey"
                            class="post_survey">

                            @csrf
                            <div class="form-group">
                                <label for="title">Question:</label>
                                <input type="text" class="form-control" name="survey_question" required minlength="3" maxlength="250"> 
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 1:</label>
                                <input type="text" class="form-control" name="survey_option1" required minlength="3" maxlength="250">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 2:</label>
                                <input type="text" class="form-control" name="survey_option2" required minlength="3" maxlength="250">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 3:</label>
                                <input type="text" class="form-control" name="survey_option3"   minlength="3" maxlength="250">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 4:</label>
                                <input type="text" class="form-control" name="survey_option4"   minlength="3" maxlength="250">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Expiry Date:</label>
                                <input type="date" class="form-control" name="expiry_date" required min="{{date('Y-m-d')}}">
                            </div>
                             
                            <div class="col-md-12 float-left">
                                <div class="form-group">
                                    <label>Post For:</label>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input type="radio" name="post_type" autocomplete="off" value="3"> All
                                                Scholars
                                            </label>
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleModalCenter" type="radio"
                                                    name="post_type" autocomplete="off" value="1" > Class & Sections
                                            </label>


                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleModalCenter1" type="radio"
                                                    name="post_type" autocomplete="off" value="2">
                                                Specific Scholars
                                            </label>
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleModalCenter2" type="radio"
                                                    name="post_type" autocomplete="off" value="4"> Group
                                            </label>
                                        </div>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label>Post For:</label>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input type="radio" name="post_type_staff" autocomplete="off" value="3"> All
                                                Staffs
                                            </label>
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleStaffCenter" type="radio"
                                                    name="post_type_staff" autocomplete="off" value="1" > Class Teacher
                                            </label>  
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleStaffCenter3" type="radio"
                                                    name="post_type_staff" autocomplete="off" value="2" > Role
                                            </label>  
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleStaffCenter4" type="radio"
                                                    name="post_type_staff" autocomplete="off" value="5" > Department
                                            </label>  
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleStaffCenter2" type="radio"
                                                    name="post_type_staff" autocomplete="off" value="4"> Group
                                            </label>
                                        </div>
                                        <div class="option-container mr-3">
                                            <label class="btn btn-outline-primary">
                                                <input data-toggle="modal" data-target="#exampleStaffCenter1" type="radio"
                                                    name="post_type_staff" autocomplete="off" value="6">
                                                Specific Staffs
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             

                            <a href="javascript:void(0);" onclick="showAdvanced()" class="d-none">Advanced in Details</a>

                            <div class="advanced col-md-12 float-left" id="advanced" style="display: none;">
                                <div class="form-group">
                                    <label for="title">Title (Push Notification):</label>
                                    <input type="text" maxlength="75" class="form-control" id="title_push" name="title_push">
                                    <span class="text-danger error-text title_push_error"></span>
                                </div>
                                <div class="form-group">
                                    <label>Message (Push Notification):</label>
                                    <textarea name="message_push" id="message_push" maxlength="150" class="form-control" rows="2"></textarea>
                                    <span class="text-danger error-text message_push_error"></span>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="batch">Batch:</label> <?php $acadamic_year = trim($acadamic_year); ?>
                                            <select class="form-control" id="batch" name="batch" required onchange="loadModalcontents();">
                                                <!-- <option value="2023-2024">2023-2024</option> -->
                                                <option value="">Select Batch</option>
                                                @if(!empty($get_batches))
                                                    @foreach($get_batches as $batches)
                                                        @php($selected = '')
                                                        @if($acadamic_year == $batches['academic_year'])
                                                        @php($selected = 'selected')
                                                        @endif
                                                        <option value="{{$batches['academic_year']}}" {{$selected}}>{{$batches['display_academic_year']}}</option>
                                                    @endforeach
                                                @endif

                                            </select>
                                        </div>
                                    </div> 
                                </div>
 
                            </div>
                              

                            <button type="submit" class="btn btn-primary float-right" id="send">Save</button>

                            <!-- Scholars -->
                            <!-- Modal1 -->
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                        <button type="button" class="close" data-dismiss="modal" id="closeBtnSectionx">&times;</button>

                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control searchInput" id="searchSection" placeholder="Search Sections..">
                                        </div> 
                                        <div class="scrollable-form exampleModalCenterscroll">
                                            @foreach($classes as $class)
                                                <div class="sectionItem">
                                                    <input type="checkbox" id="class_{{$class->id}}" name="class_post[]" value="{{$class->id}}">
                                                    <label for="class_{{$class->id}}">{{$class->class_name}} </label><br>
                                                </div>
                                            @endforeach  
                                            @foreach($get_sections as $section)
                                                <div class="sectionItem">
                                                    <input type="checkbox" id="section_{{$section->id}}" name="section_post[]" value="{{$section->id}}">
                                                    <label for="section_{{$section->id}}">{{$section->is_class_name}}-{{$section->section_name}}</label><br>
                                                </div>
                                            @endforeach  
                                            <div class="noResults" id="noSectionResults" style="display: none;">No Matching record</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtnSection">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="doneBtnSection">Done</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!-- Modal2 -->
                            <div class="modal fade" id="exampleModalCenter1" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                            <button type="button" class="close" data-dismiss="modal" id="closeBtnStudentx">&times;</button>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchStudent" placeholder="Search Students..">
                                            </div>

                                            <div class="form-group">
                                                <label style="padding-bottom: 10px;">Class</label>
                                                <select class="form-control course_id" name="class_id" id="class_id"
                                                        onchange="loadClassSection(this.value);loadspecificstudents(0,this.value)">
                                                    <option value="">Select Class</option>
                                                    @if (!empty($classes))
                                                        @foreach ($classes as $class)
                                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label" style="padding-bottom: 10px;">Section <span class="manstar">*</span></label>
                                                <div class="form-line"> <!-- loadClassSubjects(this.value); -->
                                                    <select class="form-control" name="section_id" id="section_dropdown" onchange="loadspecificstudents(this.value,class_id.value)">

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="scrollable-form exampleModalCenterscroll1">
                                                @foreach($get_student as $student)
                                                    <div class="studentItem" data-class_id="{{$student->class_id}}" data-section_id="{{$student->section_id}}">
                                                        <input type="checkbox" id="student_{{$student->user_id}}" name="student_post[]" value="{{$student->user_id}}">
                                                        <label for="student_{{$student->user_id}}" data-class_id="{{$student->class_id}}" data-section_id="{{$student->section_id}}">{{$student->is_student_name}}-({{$student->is_class_name}}-{{$student->is_section_name}})</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="noStudentResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtnStudent">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="doneBtnStudent">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal3 -->
                            <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5> 
                                            <button type="button" class="close" data-dismiss="modal" id="closeBtnx">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchGroup" placeholder="Search Groups..">
                                            </div>
                                            <div class="scrollable-form exampleModalCenterscroll2">
                                                @foreach($get_groups as $group)
                                                    <div class="groupItem">
                                                        <input type="checkbox" class="groupCheckbox" id="group_{{$group->id}}" name="group_post[]" value="{{$group->id}}">
                                                        <label for="group_{{$group->id}}">{{$group->group_name}}</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="noGroupResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtn">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="doneBtn">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Scholars -->


                            <!-- Staffs --> 
                            <!-- Modal1 -->
                            <div class="modal fade" id="exampleStaffCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                        <button type="button" class="close" data-dismiss="modal" id="closeBtnSectionx">&times;</button>

                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control searchInput" id="searchSectionStaff" placeholder="Search Sections..">
                                        </div> 
                                        <div class="scrollable-form exampleModalCenterscroll">
                                            @foreach($classes as $class)
                                                <div class="staffsectionItem">
                                                    <input type="checkbox" id="staff_class_{{$class->id}}" name="staff_class_post[]" value="{{$class->id}}">
                                                    <label for="staff_class_{{$class->id}}">{{$class->class_name}} </label><br>
                                                </div>
                                            @endforeach  
                                            @foreach($get_sections as $section)
                                                <div class="staffsectionItem">
                                                    <input type="checkbox" id="staff_section_{{$section->id}}" name="staff_section_post[]" value="{{$section->id}}">
                                                    <label for="staff_section_{{$section->id}}">{{$section->is_class_name}}-{{$section->section_name}}-
                                                    @if(isset($section->is_class_teacher))
                                                    {{$section->is_class_teacher->name}} {{$section->is_class_teacher->emp_no}}
                                                    @endif
                                                    </label><br>
                                                </div>
                                            @endforeach  
                                            <div class="noResults" id="staff_noSectionResults" style="display: none;">No Matching record</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="staff_closeBtnSection">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="staff_doneBtnSection">Done</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!-- Modal2 -->
                            <div class="modal fade" id="exampleStaffCenter1" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                        <button type="button" class="close" data-dismiss="modal" id="closeBtnSectionx">&times;</button>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchStaff" placeholder="Search Staff..">
                                            </div> 

                                            <div class="scrollable-form exampleStaffCenterscroll1">
                                                @foreach($get_staff as $staff)
                                                    <div class="staffItem">
                                                        <input type="checkbox" id="staff_{{$staff->user_id}}" name="staff_post[]" value="{{$staff->user_id}}">
                                                        <label for="staff_{{$staff->id}}">{{$staff->name}}-({{$staff->emp_no}})</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="staff_noStaffResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="staff_closeBtnStudent">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="staff_doneBtnStudent">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal3 -->
                            <div class="modal fade" id="exampleStaffCenter2" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                        <button type="button" class="close" data-dismiss="modal" id="closeBtnSectionx">&times;</button>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchStaffGroup" placeholder="Search Groups..">
                                            </div>
                                            <div class="scrollable-form exampleModalCenterscroll2">
                                                @foreach($get_groups as $group)
                                                    <div class="groupItem">
                                                        <input type="checkbox" class="groupCheckbox" id="staff_group_{{$group->id}}" name="staff_group_post[]" value="{{$group->id}}">
                                                        <label for="staff_group_{{$group->id}}">{{$group->group_name}}</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="staff_noGroupResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="staff_closeBtn">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="staff_doneBtn">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal4 -->
                            <div class="modal fade" id="exampleStaffCenter3" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                        <button type="button" class="close" data-dismiss="modal" id="closeBtnSectionx">&times;</button>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchRole" placeholder="Search Roles..">
                                            </div>
                                            <div class="scrollable-form exampleModalCenterscroll2">
                                                @foreach($get_roles as $role)
                                                    <div class="roleItem">
                                                        <input type="checkbox" class="groupCheckbox" id="role_{{$role->id}}" name="role_post[]" value="{{$role->id}}">
                                                        <label for="role_{{$role->id}}">{{$role->user_role}}</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="noRoleResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtnRole">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="doneBtnRole">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal5 -->
                            <div class="modal fade" id="exampleStaffCenter4" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>
                                        <button type="button" class="close" data-dismiss="modal" id="closeBtnSectionx">&times;</button>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchDepartment" placeholder="Search Department..">
                                            </div>
                                            <div class="scrollable-form exampleModalCenterscroll2">
                                                @foreach($get_departments as $department)
                                                    <div class="departmentItem">
                                                        <input type="checkbox" class="groupCheckbox" id="department_{{$department->id}}" name="department_post[]" value="{{$department->id}}">
                                                        <label for="department_{{$department->id}}">{{$department->department_name}}</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="noDepartmentResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtnDepartment">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="doneBtnDepartment">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Staffs --> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
    
    </section>
@else 
@include('admin.notavailable') 
@endif
@endsection
@section('scripts')

<script>

    function showAdvanced() {
        var x = document.getElementById("advanced");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    $(document).ready(function() {

        $('.note-popover').css('display', 'none');

        // Function to filter list items based on search term
        function filterList(inputId, itemClass, noResultsId) {
            $(inputId).on('input', function() {
                var searchTerm = $(this).val().toLowerCase(); 
                var found = false;

                if(inputId == '#searchStudent') {
                    var class_id = $('#class_id').val();
                    var section_id = $('#section_dropdown').val();
                }  else {
                    var class_id = 0;
                    var section_id = 0;
                }
 
                
                    $(itemClass).each(function() {
                        var itemName = $(this).find('label').text().toLowerCase();
                        if (itemName.includes(searchTerm)) {
                            //$(this).show();
                            $(this).removeClass('d-none');
                            found = true;

                            if(section_id > 0) {
                                if($(this).data('section_id') == section_id) {
                                    $(this).removeClass('d-none');
                                    found = true;
                                } else {
                                    $(this).addClass('d-none');
                                    found = false;
                                }
                                //$('.'+itemClass+'[data-section_id='+section_id+']').show()
                            }   else if(class_id > 0) {
                                if($(this).data('class_id') == class_id) {
                                    $(this).removeClass('d-none');
                                    found = true;
                                } else {
                                    $(this).addClass('d-none');
                                    found = false;
                                }
                                //$('.studentItem[data-class_id='+class_id+']').show()
                            } 

                        } else {
                            //$(this).hide();
                            $(this).addClass('d-none');
                        }
                    });


                if (found) {
                    $(noResultsId).addClass('d-none'); //$(noResultsId).hide();
                } else {
                    $(noResultsId).removeClass('d-none');    //$(noResultsId).show();
                }

                
            });
        }

        // Initialize the search functionality for groups, students, and sections
        filterList('#searchGroup', '.groupItem', '#noGroupResults');
        filterList('#searchStudent', '.studentItem', '#noStudentResults');
        filterList('#searchSection', '.sectionItem', '#noSectionResults'); 

        loadModalcontents();

        filterList('#searchStaffGroup', '.groupItem', '#staff_noGroupResults');
        filterList('#searchStaff', '.staffItem', '#noStaffResults');
        filterList('#searchStaffSection', '.staffsectionItem', '#staff_noSectionResults');
        filterList('#searchRole', '.roleItem', '#noRoleResults');
        filterList('#searchDepartment', '.departmentItem', '#noDepartmentResults');
        
        loadStaffModalcontents();
    });  

    function loadModalcontents(){  
        var batch = $('#batch').val();
        var request = $.ajax({
            type: 'post',
            url: "{!! url('admin/post_load_contents') !!}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                batch:batch,
            },
            dataType:'json',
            encode: true
        });
         request.done(function (response) {

            if(response.status == 1){
                var html = '';
                 $.each(response.data,function(index1, val1){
                    html += '<div class="studentItem" data-class_id="'+val1.class_id+'" data-section_id="'+val1.section_id+'"> <input type="checkbox" id="student_'+val1.user_id+'" name="student_post[]" value="'+val1.user_id+'"> <label for="student_'+val1.user_id+'" data-class_id="'+val1.class_id+'" data-section_id="'+val1.section_id+'">'+val1.is_student_name+'-('+val1.is_class_name+'-'+val1.is_section_name+')</label><br> </div>';
                 });

                if(html!='') {
                    $('.exampleModalCenterscroll1').html(html);
                }
            }
            else if(response.status == 0){ 
                $('.exampleModalCenterscroll1').html('');
            }

         });

        request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });
    }

    function loadStaffModalcontents(){  
        var batch = $('#batch').val();
        var request = $.ajax({
            type: 'post',
            url: "{!! url('admin/post_load_content_staffs') !!}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                batch:batch,
            },
            dataType:'json',
            encode: true
        });
         request.done(function (response) {

            if(response.status == 1){
                var html = '';
                 $.each(response.data,function(index1, val1){
                    html += '<div class="staffItem"> <input type="checkbox" id="staff_'+val1.id+'" name="staff_post[]" value="'+val1.id+'"> <label for="staff_'+val1.id+'">'+val1.name+'- '+val1.emp_no +'</label><br> </div>';
                 });

                if(html!='') {
                    $('.exampleStaffCenterscroll1').html(html);
                }
            }
            else if(response.status == 0){ 
                $('.exampleStaffCenterscroll1').html('');
            }

         });

        request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });
    }

    function loadspecificstudents(section_id,class_id) {
        var itemClass = '.studentItem'; 
        $('.studentItem').hide();
        if(section_id > 0) {
            $('.studentItem[data-section_id='+section_id+']').show()
        }   else if(class_id > 0) {
            $('.studentItem[data-class_id='+class_id+']').show()
        }   else {
            $('.studentItem').show();
        }
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store the initial state of checkboxes
        let initialCheckboxState = [];
    
        // Save the initial state when the modal is opened
        $('#exampleModalCenter').on('shown.bs.modal', function () {
            initialCheckboxState = [];
            $('input[name="section_post[]"]').each(function() {
                initialCheckboxState.push($(this).prop('checked'));
            });
        });

        $('#exampleStaffCenter').on('shown.bs.modal', function () {
            initialCheckboxState = [];
            $('input[name="staff_section_post[]"]').each(function() {
                initialCheckboxState.push($(this).prop('checked'));
            });
        });
    
        // Handle the Done button click
        document.getElementById('doneBtnSection').addEventListener('click', function() {
            $('#exampleModalCenter').modal('hide');
        }); 
        document.getElementById('staff_doneBtnSection').addEventListener('click', function() {
            $('#exampleStaffCenter').modal('hide');
        }); 
        document.getElementById('staff_doneBtnStudent').addEventListener('click', function() {
            $('#exampleStaffCenter1').modal('hide');
        }); 
        document.getElementById('staff_doneBtn').addEventListener('click', function() {
            $('#exampleStaffCenter2').modal('hide');
        }); 
        document.getElementById('doneBtnDepartment').addEventListener('click', function() {
            $('#exampleStaffCenter4').modal('hide');
        });
        document.getElementById('doneBtnRole').addEventListener('click', function() {
            $('#exampleStaffCenter3').modal('hide');
        });
    
        // Handle the Close button click
        document.getElementById('closeBtnSection').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="section_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('input[name="class_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleModalCenter').modal('hide');
        });

        document.getElementById('staff_closeBtnSection').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="staff_section_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('input[name="staff_class_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleStaffCenter1').modal('hide');
        });

        document.getElementById('staff_closeBtnStudent').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="staff_post[]"]').each(function() {
                $(this).prop('checked', false);
            }); 
            $('#exampleStaffCenter1').modal('hide');
        });

        
        document.getElementById('staff_closeBtn').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="staff_group_post[]"]').each(function() {
                $(this).prop('checked', false);
            }); 
            $('#exampleStaffCenter2').modal('hide');
        });

        /*document.getElementById('closeBtnSectionx1').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="section_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleModalCenter').modal('hide');
        });*/

    });
    document.getElementById('closeBtnDepartment').addEventListener('click', function() {
        // Uncheck all checkboxes 
        $('input[name="department_post[]"]').each(function() {
            $(this).prop('checked', false);
        });
        $('#exampleStaffCenter4').modal('hide');
    });
    document.getElementById('closeBtnRole').addEventListener('click', function() {
        // Uncheck all checkboxes 
        $('input[name="role_post[]"]').each(function() {
            $(this).prop('checked', false);
        });
        $('#exampleStaffCenter3').modal('hide');
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store the initial state of checkboxes
        let initialCheckboxState = [];
    
        // Save the initial state when the modal is opened
        $('#exampleModalCenter1').on('shown.bs.modal', function () {
            initialCheckboxState = [];
            $('input[name="student_post[]"]').each(function() {
                initialCheckboxState.push($(this).prop('checked'));
            });
        });
    
        // Handle the Done button click
        document.getElementById('doneBtnStudent').addEventListener('click', function() {
            $('#exampleModalCenter1').modal('hide');
        });
    
        // Handle the Close button click
        document.getElementById('closeBtnStudent').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="student_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleModalCenter1').modal('hide');
        });
        /*document.getElementById('closeBtnStudentx1').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="student_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleModalCenter1').modal('hide');
        });*/
    });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store the initial state of checkboxes
            let initialCheckboxState = [];
        
            // Save the initial state when the modal is opened
            $('#exampleModalCenter2').on('shown.bs.modal', function () {
                initialCheckboxState = [];
                $('input[name="group_post[]"]').each(function() {
                    initialCheckboxState.push($(this).prop('checked'));
                });
            });
        
            // Handle the Done button click
            document.getElementById('doneBtn').addEventListener('click', function() {
                $('#exampleModalCenter2').modal('hide');
            });
        
            // Handle the Close button click
            document.getElementById('closeBtn').addEventListener('click', function() {
                // Uncheck all checkboxes
                $('input[name="group_post[]"]').each(function() {
                    $(this).prop('checked', false);
                });
                $('#exampleModalCenter2').modal('hide');
            });
            /*document.getElementById('closeBtnx1').addEventListener('click', function() {
                // Uncheck all checkboxes
                $('input[name="group_post[]"]').each(function() {
                    $(this).prop('checked', false);
                });
                $('#exampleModalCenter2').modal('hide');
            });*/
        });
        </script> 
    
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store the initial state of checkboxes
            let initialCheckboxState = [];
        
            // Save the initial state when the modal is opened
            $('#exampleModalCenter3').on('shown.bs.modal', function () {
                initialCheckboxState = [];
                $('input[name="staff_post[]"]').each(function() {
                    initialCheckboxState.push($(this).prop('checked'));
                });
            });
        
            // Handle the Done button click
            /*document.getElementById('doneBtnStaff').addEventListener('click', function() {
                $('#exampleModalCenter3').modal('hide');
            });
        
            // Handle the Close button click
            document.getElementById('closeBtnStaff').addEventListener('click', function() {
                // Uncheck all checkboxes
                $('input[name="staff_post[]"]').each(function() {
                    $(this).prop('checked', false);
                });
                $('#exampleModalCenter3_label').removeClass('active');
                $('#exampleModalCenter3').modal('hide');
            });
            document.getElementById('closeBtnStaffx1').addEventListener('click', function() {
                // Uncheck all checkboxes
                $('input[name="staff_post[]"]').each(function() {
                    $(this).prop('checked', false);
                });
                $('#exampleModalCenter3').modal('hide');
            });*/
        });
        </script>


    <script src="{{ asset('public/js/select2.full.min.js') }}"></script>

    <!-- <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> -->

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.23.0/ckeditor.min.js"></script> 

    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>-->
 
 
    <script>
        $(function() {
 

            $(".post_survey").on("submit", function(e) {

                e.preventDefault();

                var data = new FormData(this);
                //data.set('message', CKEDITOR.instances['message'].getData());
                //const data = editor.getData();
                //data.set('message', myEditor.getData());

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: data,
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(document).find('span.error-text').text('');
                        $("#send").text('Processing..');
                        $("#send").prop('disabled', true);
                    },

                    success: function(response) {

                        if (response.status == 0) {
                            $("#send").text('Save');
                            $("#send").prop('disabled', false);
                            // $.each(response.error,function(prefix, val){
                            //     $('span.'+prefix+'_error').text(val[0]);
                            // });
                            swal('Oops', response.message, 'warning');

                        } else {
                            if (response.status == 1) {

                                //  $(document).find('span.error-text').text('');

                                swal({
                                       title: "Success", 
                                       text: response.message, 
                                       type: "success"
                                     },
                                   function(){ 
                                       location.href = "{{URL('/')}}/admin/survey";
                                   }
                                );

 

                            } else {
                                swal('Oops',response.message,'warning');
                            }

                        }
                    }
                });
            });
        });

        var inputs = document.querySelectorAll( '.fileatt' );
        Array.prototype.forEach.call( inputs, function( input )
        {
            var label    = input.nextElementSibling,
                labelVal = label.innerHTML;

            var span    = label.nextElementSibling,
                spanVal = span.innerHTML;

            input.addEventListener( 'change', function( e )
            {
                var fileName = '';
                if( this.files && this.files.length > 1 ) {
                    fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
                } else if( this.files && this.files.length == 1 ) { 
                    fileName = e.target.files[0].name; // e.target.value.split( '\'' ).pop();
                } else {
                    fileName = '';
                }

                $(label).css('border', '1px solid #007bff')
                /*if( fileName )
                    $(label).attr('title', fileName) 
                else 
                    $(label).attr('title', labelVal)*/
 
                if( fileName )
                    $(span).text( fileName) 
                else 
                    $(span).text( labelVal)
 
                if(fileName == '' && labelVal == '')  {
                    $(span).text( spanVal );
                    $(label).css('border', '0px solid #007bff')
                }
               /* if( fileName )
                    label.querySelector( 'span' ).innerHTML = fileName;
                else
                    label.innerHTML = labelVal;*/

                loadpreview();
            });
        });

        function loadbgtheme() {
            var bgtheme_id = $('#category').find(':selected').data('bgtheme_id');
            $('#bg_color').val(bgtheme_id);
            loadpreview();
        }

        function loadpreview() {
            var up = $('#upload_image').prop('files').length;
            var up1 = $('#upload_image1').prop('files').length;
            var up2 = $('#upload_image2').prop('files').length;
            var up3 = $('#upload_image3').prop('files').length;
            var content = ''; console.log(up+'&&'+up1+'&&'+up2+'&&'+up3)
            if(up == 0 && up  == 0 && up2 == 0 && up3 == 0) { console.log('if')
                content = $('#message').val();
                var bg_color = $('#bg_color').find(':selected').data('src');
                var text_color = $('#category').find(':selected').data('text_color');
                $('#preview_content').addClass('offerolympiaimg'); 
                if(text_color != '' && text_color != null) {
                    text_color = 'color:'+text_color+' !important;';
                } else {
                     text_color = '';
                }
                $('#preview_content').attr('style', 'background-image:url("'+bg_color+'"); background-size: cover;  background-repeat: no-repeat;'+text_color);
                $('#preview_content').html(content);
            } else { console.log('if')
                content = $('#message').val();
                var bg_color = $('#bg_color').find(':selected').data('src');
                $('#preview_content').removeClass('offerolympiaimg'); 
                $('#preview_content').attr('style', '');
                $('#preview_content').html(content); 
            }
        }

        $("#title").on('keyup', function () { 
            generatetitle($(this), '#title_push');
        });

        function generatetitle($obj, $destobj) { 
            var push_title = $( $obj ).val(); 
            push_title = $.trim(push_title);   

            $($destobj).val(push_title);
        }
    </script>

@endsection

