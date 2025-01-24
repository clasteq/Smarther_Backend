@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_faq', 'active')
@section('menuopen', 'menu-is-opening menu-open')
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">FAQ  
                    <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary" style="float: right;">Add</button></a> 
                  </h4>        
                  <div class="row">
                    <div class="row col-md-12">
                     <div class="form-group col-md-3 " >
                         <label class="form-label">Status</label>
                         <select class="form-control" name="status_id" id="status_id">
                             <option value="" >All</option>
                             <option value="ACTIVE" >ACTIVE</option>
                             <option value="INACTIVE" >INACTIVE</option>
                         </select>
                     </div>
                 </div>   
                </div>
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered tblcategory">
                      <thead>
                        <tr>
                         <th>Action</th>
                          <th>Type</th>  
                          <th>Question</th>
                          <th>Answer</th>
                          <th>Position</th>
                          <th>Status</th>
                         
                        </tr>
                      </thead>
                      <tfoot>
                          <tr><th></th><th></th><th></th>
                              <th></th><th></th><th></th>
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
    </section>

    <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add FAQ</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/faq')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Question</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="question" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Answer</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="answer" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">For</label>
                                <div class="form-line">
                                    <select type="text" class="form-control" name="faq_type" required>
                                        <option value="STUDENT" selected>Student</option>
                                        <option value="TEACHER">Teacher</option> 
                                    </select>
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
                    <h4 class="modal-title" id="smallModalLabel">Edit FAQ</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/faq')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Question</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="question" id="question" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Answer</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="answer" id="answer" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Type</label>
                                <div class="form-line">
                                    <select type="text" class="form-control" name="faq_type" id="faq_type" required>
                                        <option value="STUDENT" selected>Student</option>
                                        <option value="TEACHER">Teacher</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="position" required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="status" required>
                                      <option value="ACTIVE">ACTIVE</option>
                                      <option value="INACTIVE">INACTIVE</option>
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

@endsection

@section('scripts')

    <script>

        $(function() { 
            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/faq/datatables/",   
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        $.extend(d, {status:status});

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                                return '<a href="#" onclick="loadFaq('+tid+')" title="Edit FAQ"><i class="fas fa-edit"></i></a>';
                             
                        },

                    },
                    { data: 'faq_type'},
                    { data: 'question'},
                    { data: 'answer'},
                    { data: 'position'},
                    { data: 'status'},
                   
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 4 }
                ]

            });

            $('.tblcategory tfoot th').each( function (index) {
                if(index != 0 && index != 5){
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

                        $("#add_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcategory').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

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

                           $('.tblcategory').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                           $("#edit-style-form")[0].reset();

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

        function loadFaq(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/faq')}}",
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
                $('#faq_type').val(response.data.faq_type);
                $('#question').val(response.data.question);
                $('#answer').val(response.data.answer);
                $('#status').val(response.data.status);
                $('#position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
