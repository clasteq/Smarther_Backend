@extends('layouts.admin_master')
@section('user_settings', 'active')
@section('master_schools', 'active')
@section('menuopenu', 'active menu-is-opening menu-open')
<?php  
$user_type = Auth::User()->user_type;
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Schools', 'active'=>'active']];
$session_module = session()->get('module');
//echo "<pre>"; print_r($session_module); exit;
?>
@section('content') 
    @if($user_type == "SUPER_ADMIN")
    <style type="text/css">
        .modal-content {
            width: 112% !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title"><!-- Schools   -->

                    <div class="row col-md-12">
                        <div class="form-inline col-md-3 " >
                            <label class="form-label mr-1">Status</label>
                            <select class="form-control" name="status_id" id="status_id">
                                <option value="" >All</option>
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div>
                        <div class="form-inline col-md-8 float-right " ></div>
                        <div class="form-inline col-md-1 float-right " >
                        @if($user_type == 'SUPER_ADMIN')
                        <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>
                        @endif
                        </div>
                    </div> 
                  </h4>                 
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th>Joined Date</th>
                                  <th>Reference No</th>
                                  <th>Name</th> 
                                  <th>Code</th> 
                                  <th>Picture</th>
                                  <th>Email</th>
                                  <th>Mobile</th> 
                                  <th>Address</th>
                                  <th>State</th>
                                  <th>City</th>
                                  <th>Status</th> 
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <!-- <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
                                      <th></th><th></th><th></th> 
                                  </tr>
                              </tfoot> -->
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
                    <h4 class="modal-title" id="smallModalLabel">Add School</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/schools')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control name mt-4" name="name" required>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Slug Name </label> {{config("constants.APP_URL")}}slugname/admin
                                <div class="form-line">
                                    <input type="text" class="form-control slug_name" name="slug_name" required>
                                </div>
                            </div>   
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Code</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name_code" pattern="[a-zA-Z]*" required minlength="2" maxlength="4">
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">SMS Display Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control alphaonly" pattern="[a-zA-Z0-9 .{1,}]*" name="display_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Reference No</label>
                                <div class="form-line">
                                    <input type="text" class="form-control alphaonly" pattern="[a-zA-Z0-9 .{1,}]*" name="admission_no" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Password</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="password" required minlength="6" maxlength="12">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Picture</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image" required>
                                </div>
                            </div> 
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Address</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="address" required>
                                </div>
                                <input type="hidden" name="country_id" value="1">
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">State</label>
                                <div class="form-line">
                                    <select class="form-control state_id" name="state_id" required onchange="loadDistricts1(this)">
                                        <option value="">Select State</option>
                                        @if(!empty($states))
                                        @foreach($states as $sk => $sv)
                                        <option value="{{$sv->id}}">{{$sv->state_name}}</option>
                                        @endforeach
                                        @endif 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">City</label>
                                <div class="form-line"> 
                                    <select class="form-control city_id district_id" name="city_id" required >
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit School</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/schools')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control name" name="name" id="name" required>
                                </div>
                            </div>   
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Slug Name </label> 
                                <div class="form-line">
                                    <input type="text" class="form-control slug_name" name="slug_name" id="slug_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">School Admin Link </label>
                                <div class="form-line">
                                    <span id="copy_link">{{config("constants.APP_URL")}}<span class="disp_slug_name">slugname</span>/admin</span>  
                                    <a href="javascript: void(0);" class="btn btn-info d-none" onclick="copyLink();">Copy link</a>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">School Teacher Link </label>
                                <div class="form-line">
                                    <span id="copy_link">{{config("constants.APP_URL")}}<span class="disp_slug_name">slugname</span>/admin</span>  
                                    <a href="javascript: void(0);" class="btn btn-info d-none" onclick="copyLink();">Copy link</a>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Code</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name_code" pattern="[a-zA-Z]*" required id="name_code" minlength="2" maxlength="4">
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">SMS Display Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control alphaonly" pattern="[a-zA-Z0-9 .{1,}]*" name="display_name" id="display_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Reference No</label>
                                <div class="form-line">
                                    <input type="text" class="form-control alphaonly" pattern="[a-zA-Z0-9 .{1,}]*"  name="admission_no" id="admission_no" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mobile"  id="mobile" required minlength="10" maxlength="10"  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Password</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="password"  id="password" required  minlength="6" maxlength="12">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Picture</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="profile_image"  >
                                </div>
                            </div> 
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Address</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="address"  id="address" required>
                                </div>
                                <input type="hidden" name="country_id" value="1">
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">State</label>
                                <div class="form-line">
                                    <select class="form-control state_id" name="state_id" id="state_id" required  onchange="loadDistricts1(this)">
                                        <option value="">Select State</option>
                                        @if(!empty($states))
                                        @foreach($states as $sk => $sv)
                                        <option value="{{$sv->id}}">{{$sv->state_name}}</option>
                                        @endforeach
                                        @endif 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 ">
                                <label class="form-label">City</label>
                                <div class="form-line">
                                    <input type="hidden" name="hdistrictid" id="hdistrictid">
                                    <select class="form-control city_id district_id" name="city_id"  id="city_id" required >
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status"  id="status" required>
                                      <option value="ACTIVE">ACTIVE</option>
                                      <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Picture</label>
                                <div class="form-line">
                                    <img  name="is_profile_image" id="is_profile_image" height="150" width="150">
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
    @else 
    <section class="content">
        @include('admin.notavailable')
    </section>
    @endif
@endsection

@section('scripts')

    <script>
        $('#addbtn').on('click', function () {
            $('#style-form')[0].reset();
        });
        $(".name").on('keyup', function () { 
            generatetitle($(this));
        });

        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/schools/datatables/",  
                    data: function ( d ) {
                        var status  = $('#status_id').val(); 
                        $.extend(d, {status_id:status});

                    }
                },
                columns: [ 
                    { data: 'joined_date',  name: 'users.joined_date'},
                    { data: 'admission_no',  name: 'users.admission_no'},
                    { data: 'name',  name: 'users.name'},
                    { data: 'name_code',  name: 'users.name_code'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.profile_image != '' || data.profile_image != null){
                                var tid = data.is_profile_image;
                                return '<img src="'+tid+'" height="50" width="50">';
                            }   else {
                                return '';
                            }
                        },

                    },
                    { data: 'email',  name: 'users.email'},  
                    { data: 'mobile',  name: 'users.mobile'}, 
                    { data: 'address',  name: 'schools.address'}, 
                    { data: 'state_name',  name: 'states.state_name'}, 
                    { data: 'district_name',  name: 'districts.district_name'}, 
                    { data: 'status',  name: 'users.status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            return '<a href="#" onclick="loadSchool('+tid+')" title="Edit School"><i class="fas fa-edit"></i></a> <a href="#" onclick="loginSchool('+tid+')" title="Login School"><i class="fas fa-unlock"></i></a>'; 
                        },

                    },
                ],
                "columnDefs": [ 
                    { "orderable": false, "targets": 4 },
                    { "orderable": false, "targets": 11 }
                ]

            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if(index != 4 && index != 0 && index != 10) {
                    var title = $(this).text();
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
            } ); */

            $('#status_id').on('change', function() {
                table.draw();
            });


            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SAVE');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#style-form").ajaxForm(options);
            }); 
            $('#edit_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SAVE');

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SAVE');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            }); 
        });
 
        function loadSchool(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/schools')}}",
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
                $('#admission_no').val(response.data.admission_no);
                $('#display_name').val(response.data.display_name);
                $('#name').val(response.data.name); 
                $('#name_code').val(response.data.name_code); 
                $('.disp_slug_name').text(response.data.slug_name);
                $('#slug_name').val(response.data.slug_name);
                $('#email').val(response.data.email);
                $('#mobile').val(response.data.mobile);
                $('#password').val(response.data.passcode);
                $('#address').val(response.data.address);
                $('#state_id').val(response.data.state_id);
                $('#hdistrictid').val(response.data.city_id);
                $('#city_id').val(response.data.city_id);

                $('#state_id').trigger('change');

                $('#is_profile_image').attr('src', response.data.is_profile_image);
                $('#status').val(response.data.status);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function generatetitle($obj) { 
            var event_name = $( $obj ).val();  console.log(event_name) 
            events_slug = $.trim(event_name); 
            events_slug = events_slug.toLowerCase();
            var regex = / +/gm;
            var subst = ' ';

            events_slug = events_slug.replace(regex, subst); 
            events_slug = events_slug.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''); 
            
            regex = /-+/gm;
            subst = '-';
            events_slug = events_slug.replace(regex, subst); 
            /*result1 = result1.toLowerCase();
            result2 = result2.toLowerCase();

            var finalstr = result;
            if(result1 != '') {
                finalstr += ' '+result1;
            }

            if(duration == 1) {
                finalstr += ' for hourly rent ';   
            }   else if(duration == 2) {
                finalstr += ' for daily rent '; 
            }   else if(duration == 3) {
                finalstr += ' for monthly rent '; 
            }

            if(result2 != '') {
                finalstr += ' '+result2;
            }*/

            $('.slug_name').val(events_slug);
        }

        function copyLink() {
              // Get the text field
              var copyText = document.getElementById("copy_link").innerText; 

               // Copy the text inside the text field
              //navigator.clipboard.writeText(copyText);

              navigator.clipboard
                  .writeText(copyText)
                  .then(() => {
                    alert("successfully copied");
                  })
                  .catch(() => {
                    alert("something went wrong");
                  });

              //swal('Copied the link',copyText,'success');
              // Alert the copied text
              return false;
            }

        function loginSchool(id) {
            swal({
                title : "",
                text : "Are you sure to Exit Super Admin and Logged into School?",
                type : "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
            },
            function(isConfirm){
                if (isConfirm) {
                    var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('/admin/loginschool')}}",
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
                            window.location.href = "{{URL::to('admin/home')}}";
                        } else{
                            swal('Oops',response.message,'error'); 
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });
                }
            })
        }
    </script>

@endsection
