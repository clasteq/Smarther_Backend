@extends('layouts.user_master')
@section('content')
<style type="text/css">
     
.about-bg {
    padding: 153px 0 50px !important;
}
</style>
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1> <span>Sections</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Page Hero <<===== 
    =========================== -->


    <!-- ===========================
    =====>> My Account <<===== -->
    <section id="my-account-area" class="pt-10 pb-10">
        <div class="container">
            <div class="row">
            	<div class="col-md-12"><a href="#" data-toggle="modal" data-target="#smallModal" id="addbtn"><button class="btn btn-primary" style="float: right;">Add</button></a>  </div>
            </div>
            <div class="row" style="width:100%; overflow-x:scroll;">
                <table class="table table-striped table-bordered tblcountries" style="width:100%">
                  <thead>
                    <tr>
                      <th>Class Name</th> 
                      <th>Section Name</th> 
                      <th>Position</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                      <tr><th></th><th></th><th></th>
                          <th></th><th></th> 
                      </tr>
                  </tfoot>
                  <tbody>
                    
                  </tbody>
                </table> 
            </div>
        </div>

        <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Section</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/save/sections')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select  class="form-control" name="class_id" required>
                                        <option value=""> Select Class</option>
                                        @if(!empty($classes))
                                            @foreach($classes as $class)
                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="section_name" required minlength="1" maxlength="200">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Section</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/save/sections')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select  class="form-control" name="class_id" id="edit_class_id" required>
                                        <option value=""> Select Class</option>
                                        @if(!empty($classes))
                                            @foreach($classes as $class)
                                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="section_name" id="edit_section_name" required minlength="1" maxlength="200">
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
    </section>
    <!-- =====>> End My Account <<===== 
    =========================== -->
@endsection

@section('scripts')
<script type="text/javascript">
$(function() { 

			$('#addbtn').on('click', function () {
				$('#style-form')[0].reset();
			});

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("sections.data")}}',
                },
                columns: [ 
                    { data: 'class_name',  name: 'school_classes.class_name'}, 
                    { data: 'section_name',  name: 'school_class_Sections.section_name'}, 
                    { data: 'position',  name: 'school_class_Sections.position'},
                    { data: 'status',  name: 'school_class_Sections.status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            return '<a href="#" onclick="loadSection('+tid+')" title="Edit Section"><i class="fas fa-edit"></i></a>'; 
                        },

                    },
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 4 }, 
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if( index != 4) {
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

                        if (response.status == 1) {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                        }
                        else if (response.status == 0) {

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

                        if (response.status == 1) {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == 0) {

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
 
        function loadSection(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('edit/sections')}}",
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
                $('#edit_class_id').val(response.data.class_id); 
                $('#edit_section_name').val(response.data.section_name); 
                $('#edit_status').val(response.data.status); 
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

</script>
@endsection