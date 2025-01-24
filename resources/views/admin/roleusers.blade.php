@extends('layouts.admin_master')
@section('rolesettings', 'active')
@section('masterrole_roleusers', 'active')
@section('menuopenur', 'active menu-is-opening menu-open')
@section('content')
<?php 
use App\Http\Controllers\AdminRoleController;

$rights = AdminRoleController::getRights();

?>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Role Users 
                    @if($rights['rights']['add'] == 1)
                        <a href="#" data-toggle="modal" data-target="#smallModal" id="addroleuser"><button class="btn btn-primary" style="float: right;">Add </button></a> 
                    @endif
                  </h4>        
                          
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                      <div class="table-responsicve">
                    <table class="table table-striped table-bordered tblcategory">
                      <thead>
                        <tr>
                          <th>Role</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Mobile</th>
                          <th>Joined Date</th>
                          <th>Status</th>
                          <th class="no-sort">Action</th>
                        </tr>
                      </thead>
                      <tfoot>
                          <tr>
                              <th></th><th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                            </tr>
                      </tfoot>
                      <tbody>
                        
                      </tbody>
                      
                    </table></div>
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
                    <h4 class="modal-title" id="smallModalLabel">Role User Details</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{url('/admin/save/roleusers')}}"  method="post">

                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Role</label>
                                <div class="form-line">
                                    <select name="userrole" id="userrole" class="form-control" required>
                                        <option value="">Select User Role</option>
                                        @if(!empty($roles))
                                            @foreach($roles as $k => $v)
                                                <option value="{{$v->ref_code}}">{{$v->user_role}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" id="edit_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Last name </label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname" id="edit_last_name">
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
                            <div class="form-group form-float float-left col-md-6 d-none img_profile_image">
                                <div class="form-line">
                                    <img src="" id="img_profile_image" height="100" width="100">
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
 
@endsection

@section('scripts')

    <script>

        $(function() {
            @if($rights['rights']['list'] == 1)
            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": "{{URL('/')}}/admin/roleusers/datatables/",
                },
                columns: [
                    { data: 'user_role', 'name':'userroles.user_role'},
                    { data: 'name', 'name':'users.name'},
                    { data: 'email', 'name':'users.email'},
                    { data: 'mobile', 'name':'users.mobile'}, 
                    { data: 'created_at', 'name':'users.created_at'},
                    { data: 'status', 'name':'users.status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            @if($rights['rights']['edit'] == 1)
                                return '<a href="#" onclick="loadRoleUser('+tid+')" title="Edit Role User"><i class="fas fa-edit"></i></a>';
                            @else 
                                return '';
                            @endif
                        },

                    },
                ], 
                "order": [],
                "columnDefs": [ {
                      "targets": 'no-sort',
                      "orderable": false,
                } ]

            });

            $('.tblcategory tfoot th').each( function (index) {
                var title = $(this).text();
                if(index <= 4) {
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            // Apply the search
            table.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                                .search( this.value )
                                .draw();
                    }
                } );
            } );
            @endif
            $('#addroleuser').on('click', function () {
                $("#style-form")[0].reset();
            });

            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcategory').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                           $('#id').val('');
                           $("#style-form")[0].reset();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });

        });

        function loadRoleUser(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/roleusers')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    code:id,
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {

                $('#id').val(response.data.id);
                $('#userrole').val(response.data.user_type); 
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
                $('#edit_father_name').val(response.data.father_name);
                $('#edit_address').val(response.data.address);
                /*$('#edit_subject').val(response.data.teachers.is_subject_id);

                $('#edit_class').val(response.data.teachers.is_class_id);
                $('#edit_class_tutor').val(response.data.class_tutor);
                var val = response.data.class_tutor;
                var selectedid = response.data.section_id;
                loadClassSection(val, selectedid);*/

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
                if(response.data.profile_image != '' && response.data.profile_image != null){
                    $('#img_profile_image').attr('src', response.data.is_profile_image);
                    $('.img_profile_image').removeClass('d-none');
                }   else {
                    $('.img_profile_image').addClass('d-none');
                }

                $('#smallModal').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

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

                    $('#state-dropdown,#edit_state_dropdown').html(
                        '<option value="">-- Select State --</option>');
                    $.each(res.states, function(key, value) {
                        var selected = '';
                        if (selid != null && selid == value.id) {
                             selected = 'selected';
                        }
                        $("#state-dropdown,#edit_state_dropdown").append('<option value="' + value.id + '" '+selected+'>' + value.state_name + '</option>');
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


            $("#edit_districts-dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-districts') }}",
                type: "POST",
                data: {
                    state_id: idState,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) { 
                    $.each(res.districts, function(key, value) {
                        var selected = '';
                        if (selid != null && selid == value.id) {
                             selected = 'selected';
                        }
                        $("#districts-dropdown,#edit_districts-dropdown").append('<option value="' + value.id + '" '+selected+'>' + value.district_name + '</option>');
                    });
                }
            });
        }
    </script>

@endsection
