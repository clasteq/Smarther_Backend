@extends('layouts.teacher_master')
@section('mastersettings', 'active')
@section('master_students', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/teacher/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Student', 'active' => 'active']];
?>
@section('content')


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Students List
                        </h4>

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
                                                <th>Mobile</th>
                                                <th>Date of Birth</th>
                                                <th>Roll No</th>
                                                <th>Admission No</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Father Name</th>
                                                <th>Address</th>
                                                <th>Country</th>
                                                <th>State</th>
                                                <th>City</th>
                                                <th>Status</th>
                                                <th>Action</th>
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
    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Student</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/student') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" id="edit_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Last name</label>
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
                                <label class="form-label">Mobile</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="mobile" id="edit_mobile" required>
                                </div>
                            </div>

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Roll No</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="roll_no" id="edit_roll_no"
                                        required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Admission No</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="admission_no"
                                        id="edit_admission_no" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
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
                                <label class="form-label">Section</label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="edit_section_dropdown">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Gender</label>
                                <div class="form-line">
                                    <select class="form-control" name="gender" id="edit_gender" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <div class="form-line">
                                    <input type="date" class="form-control" name="dob" id="edit_dob" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Photo</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image">
                                </div>
                            </div>


                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Father Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="father_name" id="edit_father_name">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Address</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="address" id="edit_address"
                                        required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Country</label>
                                <div class="form-line">
                                    <select class="form-control" id="edit_country-dropdown"
                                        onchange="myFunction(this.value)" name="country" required>
                                        <option value="" disabled selected>--Select Country--</option>
                                        @foreach ($countries as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">State</label>
                                <div class="form-line">
                                    <div class="form-group form-float">

                                        <div class="form-line">
                                            <select id="edit_state_dropdown" onchange="stateFunction(this.value)"
                                                class="form-control" name="state_id">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">City</label>
                                <div class="form-line">
                                    <select id="districts-dropdown" class="form-control" name="city_id">
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group form-float float-right col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" required>
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
                    "url": '{{ route('studentlist.data') }}',
                },
                columns: [{
                        data: 'users.name',
                        name: 'name'
                    },
                    {
                        data: 'users.last_name',
                        name: 'last_name'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            if (data.users.profile_image != '' || data.users.profile_image !=
                                null) {
                                var tid = data.users.is_profile_image;
                                console.log('tid', tid);
                                return '<img src="' + tid + '" height="50" width="50">';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'users.gender',
                        name: 'gender'
                    },
                    {
                        data: 'users.email',
                        name: 'email'
                    },
                    {
                        data: 'users.mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'users.dob',
                        name: 'dob'
                    },
                    {
                        data: 'roll_no',
                        name: 'roll_no'
                    },
                    {
                        data: 'admission_no',
                        name: 'admission_no'
                    },
                    {
                        data: 'is_class_name',
                        name: 'is_class_name'
                    },
                    {
                        data: 'is_section_name',
                        name: 'is_section_name'
                    }, {
                        data: 'father_name',
                        name: 'father_name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'users.is_country_name',
                        name: 'is_country_name'
                    },
                    {
                        data: 'users.is_state_name',
                        name: 'is_state_name'
                    },
                    {
                        data: 'users.is_district_name',
                        name: 'is_district_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.user_id;
                            return '<a href="#" onclick="loadCountry(' + tid +
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
                if (index != 17) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
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

        function loadCountry(id) {

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

                console.log('response', response.data);
                $('#id').val(response.data.user_id);
                $('#edit_name').val(response.data.users.name);
                $('#edit_last_name').val(response.data.users.last_name);
                $('#edit_gender').val(response.data.users.gender);
                $('#edit_email').val(response.data.users.email);
                $('#edit_dob').val(response.data.users.dob);
                $('#edit_mobile').val(response.data.users.mobile);
                $('#edit_roll_no').val(response.data.roll_no);
                $('#edit_class_id').val(response.data.class_id);
                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                var selectedval = response.data.is_section_name;
                loadClassSection(val, selectedid, selectedval);

                $('#edit_section').val(response.data.section_id);
                $('#edit_admission_no').val(response.data.admission_no);
                $('#edit_father_name').val(response.data.father_name);
                $('#edit_address').val(response.data.address);

                $('#edit_country-dropdown').val(response.data.users.country);

                var val = response.data.users.country;
                var selectedid = response.data.users.state_id;
                var selectedval = response.data.users.is_state_name;
                myFunction(val, selectedid, selectedval);

                $('#edit_state_dropdown').val(response.data.users.state_id);
                var val = response.data.users.state_id;
                var selectedid = response.data.users.city_id;
                var selectedval = response.data.users.is_district_name;
                stateFunction(val, selectedid, selectedval);

                $('#edit_districts-dropdown').val(response.data.users.city_id);

                $('#edit_status').val(response.data.status);
                $('#img_profile_image').attr('src', response.data.users.is_profile_image);
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
            console.log('idCountry', idCountry);
            console.log('selval', selval);

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
                    console.log('res', res);

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
            console.log('idState', idState);

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
                    console.log('res', res);
                    
                        if(selid != null && selval != null){
                            $("#edit_districts-dropdown").append('<option selected value="' + selid + '">' + selval + '</option>');
                        }else{
                            $('#districts-dropdown,#edit_districts-dropdown').html(
                        '<option value="">-- Select City --</option>');
                        }
                        $.each(res.districts, function(key, value) {
                            $("#districts-dropdown,#edit_districts-dropdown").append('<option value="' + value
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
    </script>
@endsection
