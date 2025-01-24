@extends('layouts.admin_master')
@section('usersettings', 'active')
@section('master_students', 'active')
@section('menuopenu', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Students', 'active'=>'active']];
?>
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Students  
                    <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn" class="btn btn-primary" style="float: right;">Add</button></a>  
                  </h4>        
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th>Action</th>
                                  <th>Joined Date</th>
                                  <th>Roll No</th>
                                  <th>Name</th>
                                  <th>Last Name</th>
                                  <th>Class</th>
                                  <th>Section</th>
                                  <th>Picture</th>
                                  <th>Email</th>
                                  <th>Mobile</th>
                                  <th>Gender</th>
                                  <th>DOB</th> 
                                  <th>Address</th>
                                  <th>State</th>
                                  <th>City</th>
                                  <th>Status</th> 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Student</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/students')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Last name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="lastname">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control course_id" name="class_id" onchange="myFunction(this.value)"
                                        required>
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
                                    <select class="form-control" name="section_id" id="section_dropdown">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Roll No</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Email</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="alias_name">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Picture</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Mobile</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="country_flag" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Address</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="currency_symbol" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">State</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="currency" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">City</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="alias_currency">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Country</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/countries')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Code</label>
                                <div class="form-line">
                                    <input type="text" class="form-control " name="code" id="edit_code" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Phone Code</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="phonecode" id="edit_phonecode" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" id="edit_name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Name in Arabic</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="alias_name" id="edit_alias_name">
                                </div>
                            </div>
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Flag</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="country_flag" id="edit_country_flag">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Currency Symbol</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="currency_symbol" id="edit_currency_symbol" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Currency</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="currency" id="edit_currency" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Currency in Arabic</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="alias_currency" id="edit_alias_currency">
                                </div>
                            </div>
                            
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position" required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status"  id="edit_status" required>
                                      <option value="ACTIVE">ACTIVE</option>
                                      <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <div class="form-line">
                                    <img src="" id="img_country_flag" height="100" width="100">
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
        $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
            });
        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("countries.data")}}',
                },
                columns: [
                    { data: 'code',  name: 'code'},
                    { data: 'name',  name: 'name'},
                    /*{ data: 'alias_name',  name: 'alias_name'},*/
                    { data: 'phonecode',  name: 'phonecode'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.country_flag != '' || data.country_flag != null){
                                var tid = data.is_country_flag;
                                return '<img src="'+tid+'" height="50" width="50">';
                            }   else {
                                return '';
                            }
                        },

                    },
                    { data: 'currency',  name: 'currency'},
                    /*{ data: 'alias_currency',  name: 'alias_currency'},*/
                    { data: 'currency_symbol',  name: 'currency_symbol'},
                    { data: 'position',  name: 'position'},
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            return '<a href="#" onclick="loadCountry('+tid+')" title="Edit Country"><i class="fas fa-edit"></i></a>'; 
                        },

                    },
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 3 },
                    { "orderable": false, "targets": 8 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 3 && index != 8) {
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
            } ); 
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

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

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
            $('#edit_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

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

                        $("#edit_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            }); 
        });
 
        function loadCountry(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/countries')}}",
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
                $('#edit_code').val(response.data.code);
                $('#edit_name').val(response.data.name);
                $('#edit_alias_name').val(response.data.alias_name);
                $('#edit_phonecode').val(response.data.phonecode);
                $('#edit_currency').val(response.data.currency);
                $('#edit_alias_currency').val(response.data.alias_currency);
                $('#edit_currency_symbol').val(response.data.currency_symbol);
                $('#edit_status').val(response.data.status);
                $('#img_country_flag').attr('src', response.data.is_country_flag);
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
