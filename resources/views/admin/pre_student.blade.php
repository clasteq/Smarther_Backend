@extends('layouts.admin_master')
@section('sch_settings', 'active')
@section('master_pre_student', 'active')
@section('menuopensch', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Pre Admission Students', 'active' => 'active']];
?>
@section('content')


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title">Pre Admission Students
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a> 
                        </h4>
                        <div class="row"> 
                          
                            <div class="col-md-3">
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
                                                <th>Photo</th>
                                                <th>First Name</th> 
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Primary Mobile</th>
                                                <th>Father Name</th> 
                                                <th>Status</th> 
                                                <th class="no-sort nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th>  
                                                <th></th><th></th><th></th> 
                                                <th></th><th></th> 
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
                    <h4 class="modal-title" id="smallModalLabel">Add Pre Admission Students</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/pre_student') }}"
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
                                <label class="form-label">Last name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname" >
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">Email </label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>  

                            <div class="form-group form-float float-left col-md-6">
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
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Primary Mobile <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Alternate Mobile </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile1"  minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
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
                                    <input type="date" class="form-control" max="<?php echo date("Y-m-d"); ?>" name="dob" required>
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Photo </label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image" >
                                </div>
                            </div>


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
                                    <select class="form-control" name="country" onchange="myFunction(this.value)"
                                        >
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
                                <label class="form-label">City</label>
                                <div class="form-line">
                                    <select id="edit_districts-dropdown" class="form-control" name="city_id" >
                                    </select>
                                </div>
                            </div>

                            <br>
                            <div class="form-group form-float float-right col-md-6">
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

    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Pre Admission Student</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/pre_student') }}"  method="post">

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
                                <label class="form-label">Last name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname" id="edit_last_name">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">Email </label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email" id="edit_email">
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-6">
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
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="edit_section_dropdown" required>

                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Primary Mobile <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile" id="edit_mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Alternate Mobile </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile1" id="edit_mobile1"  minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)" >
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
                                    <input type="date" class="form-control" name="dob" id="edit_dob" required>
                                </div>
                            </div> 

                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Photo </label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image">
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Father Name </label>
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
                                    <select id="districts-dropdown" class="form-control" name="city_id" >
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group form-float float-right col-md-6">
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
        </div>
    </div>

    <div class="modal fade in" id="smallModal-3" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Move Student</h4>
                </div>

                <form id="move-style-form" enctype="multipart/form-data" action="{{ url('/admin/savemove/pre_student') }}"  method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Admission No <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="admission_no"
                                        id="move_admission_no" required minlength="4" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class <span class="manstar">*</span></label>
                                <div class="form-line">

                                    <select class="form-control " name="class_id" id="move_class_id"
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
                                    <select class="form-control" name="section_id" id="move_section_dropdown" required>

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
                    "url":"{{URL('/')}}/admin/pre_student/datatables/",  
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
                    { data: 'name', name: 'pre_admission_students.name' },
                    { data: 'class_name', name: 'classes.class_name' },
                    { data: 'section_name',  name: 'sections.section_name' },  
                    { data: 'mobile',  name: 'mobile' }, 
                    {  data: 'father_name', name: 'pre_admission_students.father_name' },  
                    { data: 'status', name: 'pre_admission_students.status' }, 
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            /*<a href="#" onclick="deleteStudent(' + tid +
                                ')" title="Delete Students"><i class="fas fa-trash"></i></a>*/
                            var tid = data.id;
                            return '<a href="#" onclick="loadStudent(' + tid +
                                ')" title="Edit Scholar"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="#" onclick="deleteStudent(' + tid +
                                ')" title="Delete Scholar"><i class="fas fa-trash"></i></a>&nbsp;&nbsp;<a href="#" onclick="moveStudent(' + tid +
                                ')" title="Move Scholar"><i class="fas fa-suitcase"></i></a>';
                        },

                    },

                ],
                order:[[1, 'asc']],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }, { "targets": 'nowrap', "className": 'nowrap', },]


            });


            $('.tblcountries tfoot th').each(function(index) {
                if ( index != 0 && index != 7 ) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
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
            $('#move_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#move_style").text('Processing..');

                        $("#move_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#move_style").prop('disabled', false);

                        $("#move_style").text('SUBMIT');

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

                        $("#move_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#move-style-form").ajaxForm(options);
            });
        });

        function loadStudent(id) {
            $('#edit-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/pre_student') }}",
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
                $('#edit_mobile1').val(response.data.mobile1)
                $('#edit_dob').val(response.data.dob); 
                $('#edit_mobile').val(response.data.mobile); 
                $('#edit_class_id').val(response.data.class_id); 
                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.section_name;
                loadClassSection(val, selectedid, selectedval);

                $('#edit_section_dropdown').val(response.data.section_id); 
                $('#edit_father_name').val(response.data.father_name);
                $('#edit_address').val(response.data.address);

                $('#edit_country-dropdown').val(response.data.country_id);

                var val = response.data.country_id;
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
                $('#img_profile_image').attr('src', response.data.is_profile_image);
                $('#smallModal-2').modal('show');

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
                        url: " {{URL::to('/admin/delete/pre_students')}}",
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
                url: " {{ URL::to('admin/edit/pre_student') }}",
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
                $('#move-style-form #move_class_id').val(response.data.class_id); 
                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.section_name;
                loadClassSection1(val, selectedid, selectedval);

                $('#move-style-form #move_section_dropdown').val(response.data.section_id);  
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


        function loadClassSection1(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown,#move_section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {  
                    $('#section_dropdown,#edit_section_dropdown,#move_section_dropdown').html(
                        '<option value="">-- Select Section --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown,#move_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                        var selected = '';
                        if(selid ==  value.id) {
                            selected = 'selected';
                        }
                        $("#section_dropdown,#edit_section_dropdown,#move_section_dropdown").append('<option value="' + value
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
