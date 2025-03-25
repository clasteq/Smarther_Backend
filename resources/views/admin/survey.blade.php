@extends('layouts.admin_master')
@section('communication_settings', 'active')
@section('master_survey', 'active')
@section('menuopencomn', 'active menu-is-opening menu-open') 
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Categories', 'active'=>'active']];
?>
@section('content')

<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@if((isset($session_module['Survey']) && ($session_module['Survey']['list'] == 1)) || ($user_type == 'SCHOOL'))
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Survey
                    @if((isset($session_module['Survey']) && ($session_module['Survey']['add'] == 1)) || ($user_type == 'SCHOOL'))
                    <a href="{{url('/admin/addsurvey')}}" ><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>
                    @endif
                  </h4>
                  <div class="row">
                    <div class="row col-md-12">
                     <div class="form-group col-md-3 " >
                         <label class="form-label">Is Display</label>
                         <select class="form-control" name="status_id" id="status_id">
                             <option value="" >All</option>
                             <option value="YES">Yes</option>
                             <option value="NO">No</option>
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
                                  <th>Question</th>
                                  <th>Option 1</th>
                                  <th>Option 2</th>
                                  <th>Option 3</th>
                                  <th>Option 4</th>
                                  <th>Status</th>
                                  <th>Created At</th>
                                  <th>Action</th>

                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Survey</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/survey')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Question</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_question" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 1</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option1" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 2</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option2" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 3</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option3" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 4</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option4" required minlength="3" maxlength="250">
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Display</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" required>
                                      <option value="YES">Yes</option>
                                      <option value="NO">No</option>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Survey</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/survey')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Question</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_question" id="survey_question" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 1</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option1" id="survey_option1" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 2</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option2" id="survey_option2" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 3</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option3" id="survey_option3" required minlength="3" maxlength="250">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Option 4</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="survey_option4" id="survey_option4" required minlength="3" maxlength="250">
                                </div>
                            </div> 
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Is Display</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="status" required>
                                      <option value="YES">Yes</option>
                                      <option value="NO">No</option>
                                    </select>
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
@include('admin.notavailable') 
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
                    "url":"{{URL('/')}}/admin/survey/datatables/", 
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    { data: 'survey_question',  name: 'survey_question'},
                    { data: 'survey_option1',  name: 'survey_option1'},
                    { data: 'survey_option2',  name: 'survey_option2'},
                    { data: 'survey_option3',  name: 'survey_option3'},
                    { data: 'survey_option4',  name: 'survey_option4'},
                    { data: 'status',  name: 'status'},
                    { data: 'created_at',  name: 'created_at'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            @if((isset($session_module['Survey']) && ($session_module['Survey']['edit'] == 1)) || ($user_type == 'SCHOOL'))
                            var tid = data.id;
                            return '<a href="#" onclick="loadSurvey('+tid+')" title="Edit Survey"><i class="fas fa-edit"></i></a> <a href="#" onclick="deletedata(' + tid +')" title="Delete Survey"><i class="fas fa-trash"></i></a>';
                            @else
                            return '';
                            @endif
                        },

                    },

                ],
                "order":[[6, 'desc']],
                "columnDefs": [ { "orderable": false, "targets": 6 } ],
               
            });

            $('.tblcountries tfoot th').each( function (index) {
                if( index != 6 && index != 7) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#status_id').on('change', function() {
                table.draw();
            });
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

                        $("#add_style").text('SAVE');

                        if (response.status == 'SUCCESS') {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                        }
                        else if (response.status == 'FAILED') {

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

                        if (response.status == 'SUCCESS') {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == 'FAILED') {

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

        function loadSurvey(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/admin/edit/survey')}}",
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
                $('#survey_question').val(response.data.survey_question);
                $('#survey_option1').val(response.data.survey_option1);
                $('#survey_option2').val(response.data.survey_option2);
                $('#survey_option3').val(response.data.survey_option3);
                $('#survey_option4').val(response.data.survey_option4);
                $('#status').val(response.data.status);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function deletedata(id){
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
                        url: " {{URL::to('/admin/delete/survey')}}",
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
                        if (response.status == 1) {

                            swal('Success',response.message,'success');

                            $('.tblcountries').DataTable().ajax.reload();
                        }
                        else{
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
