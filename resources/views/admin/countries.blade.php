@extends('layouts.admin_master')
@section('master_settings', 'active')
@section('master_countries', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$user_type = Auth::User()->user_type;
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Country', 'active'=>'active']];
?>
@section('content')
@if($user_type == "SUPER_ADMIN")
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title"><!-- Countries -->
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
                                  <th>Code</th>
                                  <th>Name</th>
                                  <th>Phone Code</th>
                                  <th>Flag</th>
                                  <th>Currency</th>
                                  <th>Currency Symbol</th>
                                  <th>Position</th>
                                  <th>Status</th>
                                  <th class="no-sort">Action</th>
                                </tr>
                              </thead><!-- 
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
                    <h4 class="modal-title" id="smallModalLabel">Add Country</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/countries')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Code</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="code" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Phone Code</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="phonecode" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Name in Arabic</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="alias_name">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Flag</label>
                                <div class="form-line">
                                    <input type="file" class="form-control" name="country_flag" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Currency Symbol</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="currency_symbol" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Currency</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="currency" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Currency in Arabic</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="alias_currency">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" required min="1">
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": { 
                    "url":"{{URL('/')}}/admin/countries/datatables/",
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
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
                "order":[],
                "columnDefs": [
                    { "orderable": false, "targets": 4 },
                    { "orderable": false, "targets": 8 },
                    { "orderable": false, "targets": 'no-sort', },
                ]

            });

            /*$('.tblcountries tfoot th').each( function (index) {
                if(index != 4 && index != 0 && index != 8) {
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
            } );*/

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
