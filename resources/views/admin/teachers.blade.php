@extends('layouts.admin_master')
@section('sta_settings', 'active')
@section('master_teachers', 'active')
@section('menuopensta', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers', 'active' => 'active']];
?>
@section('content')



    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title">Teachers
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>

                            <a href="{{URL('/')}}/admin/import_teachers" ><button id="addbtn" class="btn btn-primary" style="float: right;margin-right: 10px;">Import</button></a>
                        </h4>
                        <div class="row"> 
                          
                            {{-- <div class="col-md-3">
                                <label class="from-label">Class</label>
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

                            <div class=" col-md-3">
                                <label class="form-label" >Section</label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_id" required>

                                    </select>
                                </div>
                            </div>
                          --}}
                            <div class="col-md-3">
                              
                                <div class="form-line">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status_id" id="status_id">
                                        <option value="" >All</option>
                                        <option value="ACTIVE" selected>ACTIVE</option>
                                        <option value="INACTIVE" >IN ACTIVE</option>
                                    </select>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries">
                                        <thead>
                                            <tr>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Photo</th>
                                                <th>Gender</th>
                                                <th>Email</th>
                                                <th>Password</th>
                                                <th>Mobile</th>
                                                <th>Date of Birth</th>
                                                <th>Emp No</th>
                                                <th>Date of Joning</th>
                                                <th>Qualification</th>
                                                <th>Experience</th>
                                                <th>Post Details</th>
                                                {{-- <th>Subject Handling</th>
                                                <th>Class Handling</th> --}}
                                                {{-- <th>Class Teacher</th>
                                                <th>Section</th> --}}
                                                <th>Father Name</th>
                                                <th>Address</th>
                                                <th>Country</th>
                                                <th>State</th>
                                                <th>City</th>
                                                <th>Status</th> 
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                {{-- <th></th>
                                                <th></th> --}}
                                                {{-- <th></th>
                                                <th></th> --}}
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Teachers</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/teachers') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Last name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Password <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="password" minlength="6" maxlength="20" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Emp No <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="emp_no" required minlength="4" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Date of Joining <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="date" class="form-control" max="<?php echo date("Y-m-d"); ?>" name="date_of_joining" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Gender <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Date of Birth <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="date" required class="form-control" max="<?php echo date("Y-m-d"); ?>" name="dob" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Photo </label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Post Details </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="post_details" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Qualification </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="qualification" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Experience </label>
                                <div class="form-line">
                                    <input type="text" onkeypress="return isNumber(event, this)" class="form-control" name="exp" >
                                </div>
                            </div>

                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class Handing <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control course_id" multiple="multiple" name="class_id[]"
                                        required>
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))

                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject Handling <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control course_id" multiple="multiple" name="subject_id[]"
                                        required>
                                        <option value="">Select Subject</option>
                                        @if (!empty($subjects))

                                            @foreach ($subjects as $course)
                                                <option value="{{ $course->id }}">{{ $course->subject_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div> --}}

                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class Teacher  <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control course_id" id="class_tutor" name="class_tutor"
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
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div> --}}
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Father Name </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="father_name" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Address </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="address" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Country </label>
                                <div class="form-line">
                                    <select class="form-control" name="country" onchange="myFunction(this.value)">
                                        <option value="" disabled selected>--Select Country--</option>
                                        @foreach ($countries as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
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
                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">City </label>
                                <div class="form-line">
                                    <select id="edit_districts-dropdown" class="form-control" name="city_id" >
                                    </select>
                                </div>
                            </div>

                            <div class="form-group form-float float-right col-md-6">
                                <label class="form-label">Status </label>
                                <div class="form-line">
                                    <select class="form-control" name="status" required>
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

    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Teachers</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/teachers') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" id="edit_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Last name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname" id="edit_last_name"
                                        required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email" id="edit_email">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Password <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="edit_password" name="password" minlength="6" maxlength="20">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile" id="edit_mobile" required minlength="10"  maxlength="10"  onkeypress="return isNumber(event, this)">
                                    
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Emp No <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="emp_no" id="edit_emp_no" required minlength="4" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Date of Joining <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="date" class="form-control" max="<?php echo date("Y-m-d"); ?>" name="date_of_joining"
                                        id="edit_date_of_joining" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Gender <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="gender" id="edit_gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Date of Birth <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="date" class="form-control" max="<?php echo date("Y-m-d"); ?>" name="dob" id="edit_dob" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Photo </label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Post Details </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="post_details"
                                        id="edit_post_details" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Qualification </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="qualification"
                                        id="edit_qualification" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Experience</label>
                                <div class="form-line">
                                    <input type="text" class="form-control"  onkeypress="return isNumber(event, this)" name="exp" id="edit_exp" >
                                </div>
                            </div>


                            {{-- <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control " multiple="multiple" name="class_id[]" required
                                        id="edit_class">
                                        <option value="">Select Class</option>
                                        @if (!empty($classes))

                                            @foreach ($classes as $course)
                                                <option value="{{ $course->id }}">{{ $course->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control course_id" multiple="multiple" name="subject_id[]"
                                        id="edit_subject" required>
                                        <option value="">Select Subject</option>
                                        @if (!empty($subjects))

                                            @foreach ($subjects as $course)
                                                <option value="{{ $course->id }}">{{ $course->subject_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class Teacher  <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control course_id" id="edit_class_tutor" name="class_tutor"
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
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="edit_section_dropdown">

                                    </select>
                                </div>
                            </div> --}}
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Father Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="father_name" id="edit_father_name" >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Address </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="address" id="edit_address"
                                        >
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
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
                            <div class="form-group form-float float-left col-md-6">
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
                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">City </label>
                                <div class="form-line">
                                    <select id="districts-dropdown" class="form-control" name="city_id">
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group form-float float-right col-md-4">
                                <label class="form-label">Status </label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" >
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <img src="" id="img_profile_image" height="100" width="100">
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
        </div>
    </div>

@endsection

@section('scripts')

    <script>
          $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/teachers/datatables/",   
                    // data: function ( d ) {
                    //     var subject  = $('#sub_id').val();
                    //     $.extend(d, {subject:subject});

                    // }
                    data: function ( d ) {
                        var status_id  = $('#status_id').val();
                        var section_id = $('#section_id').val();
                        var class_id = $('#class_id').val();
                        $.extend(d, { 
                            status_id:status_id,
                            section_id:section_id,
                            class_id:class_id    
                        });
                    }
                },
                columns: [
                    {
                        data: 'name',
                        name: 'users.name'
                    },
                    {
                        data: 'last_name',
                        name: 'users.last_name'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            if (data.profile_image != '' || data.profile_image !=
                                null) {
                                var tid = data.is_profile_image;
                                return '<img src="' + tid + '" height="50" width="50">';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'gender',
                        name: 'users.gender'
                    },
                    {
                        data: 'email',
                        name: 'users.email'
                    },
                    {
                        data: 'passcode',
                        name: 'passcode'
                    },
                    {
                        data: 'mobile',
                        name: 'users.mobile'
                    },
                    {
                        data: 'dob',
                        name: 'users.dob'
                    },
                    {
                        data: 'emp_no',
                        name: 'emp_no'
                    },
                    {
                        data: 'date_of_joining',
                        name: 'date_of_joining'
                    },
                    {
                        data: 'qualification',
                        name: 'qualification'
                    },
                    {
                        data: 'exp',
                        name: 'exp'
                    },
                    {
                        data: 'post_details',
                        name: 'post_details'
                    },
                    // {
                    //     data: 'teachers.is_subjects_handling',
                    //     name: 'teachers.is_subjects_handling'
                    // },
                    // {
                    //     data: 'teachers.is_classes_handling',
                    //     name: 'teachers.is_classes_handling'
                    // },
                    // {
                    //     data: 'class_name',
                    //     name: 'classes.class_name'
                    // },
                    // {
                    //     data: 'section_name',
                    //     name: 'sections.section_name'
                    // },
                    {
                        data: 'father_name',
                        name: 'father_name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'country_name',
                        name: 'countries.name'
                    },
                    {
                        data: 'is_state_name',
                        name: 'state_name'
                    },
                    {
                        data: 'is_district_name',
                        name: 'district_name'
                    },
                    {
                        data: 'status',
                        name: 'users.status'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadTeacher(' + tid +
                                ')" title="Edit Country"><i class="fas fa-edit"></i></a>';
                        },

                    },

                ],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]


            });


            $('.tblcountries tfoot th').each(function(index) {
                // if (index != 19 && index != 0) {
                //     var title = $(this).text();
                //     $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                // }
                 if (index != 2  && index != 18 && index != 19) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });

            // $('#sub_id').on('change', function() {
            //     table.draw();
            // });

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

                        $("#add_style").text('SUBMIT');

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

                        $("#add_style").text('SUBMIT');

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

                        $("#edit_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });
        });

        function loadTeacher(id) {

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/teachers') }}",
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


                $('#id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_last_name').val(response.data.last_name);
                $('#edit_gender').val(response.data.gender);
                $('#edit_email').val(response.data.email);
                $('#edit_dob').val(response.data.dob);
                $('#edit_mobile').val(response.data.mobile);
                $('#edit_password').val(response.data.passcode);
                $('#edit_emp_no').val(response.data.emp_no);
                $('#edit_date_of_joining').val(response.data.date_of_joining);
                $('#edit_qualification').val(response.data.qualification);
                $('#edit_exp').val(response.data.exp);
                $('#edit_post_details').val(response.data.post_details);
                $('#edit_subject').val(response.data.teachers.is_subject_id);

                $('#edit_class').val(response.data.teachers.is_class_id);
                $('#edit_class_tutor').val(response.data.class_tutor);
                $('#edit_father_name').val(response.data.father_name);
                $('#edit_address').val(response.data.address);
                var val = response.data.class_tutor;
                var selectedid = response.data.section_id;
                loadClassSection(val, selectedid);

                $('#edit_country-dropdown').val(response.data.country);

                var val = response.data.country;
                var selectedid = response.data.state_id;
                var selectedval = response.data.is_state_name;
                myFunction(val, selectedid, selectedval);

                $('#edit_state_dropdown').val(response.data.state_id);
                var val = response.data.state_id;
                var selectedid = response.data.city_id;
                var selectedval = response.data.is_district_name;
                stateFunction(val, selectedid, selectedval);

                $('#edit_districts-dropdown').val(response.data.city_id);
                $('#edit_status').val(response.data.status);
                $('#img_profile_image').attr('src', response.data.is_profile_image);
                $('#smallModal-2').modal('show');

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

                    $('#section_dropdown').html(
                            '<option value="">-- Select Section --</option>');
                    $.each(res.section, function(key, value) {
                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
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
    </script>
@endsection
