@extends('layouts.admin_master')
@section('academic_settings', 'active')
@section('master_exams', 'active')
@section('menuopena', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Exams', 'active'=>'active']];
?>
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Exams  
                    <a href="{{URL('/')}}/admin/add/exams" ><button id="addbtn" class="btn btn-primary" style="float: right;">Add</button></a>  
                  </h4>        
                  <div class="row">
                    <div class="row col-md-12">
                     <div class="form-group col-md-3 d-none" >
                         <label class="form-label">Status</label>
                         <select class="form-control" name="status_id" id="status_id">
                             <option value="" >All</option>
                             <option value="ACTIVE" >ACTIVE</option>
                             <option value="INACTIVE" >INACTIVE</option>
                         </select>
                     </div>

                     <div class="col-md-3">
                        <label class="from-label">Class</label>
                        <select class="form-control course_id" name="class_id" id="class_id">
                            <option value="">Select Class</option>
                            @if (!empty($classes))
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            @endif
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
                                  <th>Action</th>
                                  <th>Title</th>
                                  <th>Classes</th> 
                                  <th>Month Year</th>
                                  <th>Start Date</th> 
                                  <th>End Date</th> 
                                  <!-- <th>Status</th> -->
                                  
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th> 
                                     <!--  <th></th>  -->
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
   
@endsection

@section('scripts')

    <script>
        // $('#addbtn').on('click', function () {
        //         $('#style-form')[0].reset();
        //     });
        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/exams/datatables/",    
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        var class_id  = $('#class_id').val();
                        $.extend(d, {status:status,class_id:class_id});

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            return '<a href="{{URL('/')}}/admin/edit/exams?id='+data.id+'" title="Edit Exam"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="{{URL('/')}}/admin/view/exams?id='+data.id+'" title="Edit Exam"><i class="fas fa-eye"></i></a>'; 
                        },

                    },
                    { data: 'exam_name',  name: 'exam_name'},
                    { data: 'class_names',  name: 'class_names'},
                    { data: 'monthyear',  name: 'monthyear'},
                    { data: 'exam_startdate',  name: 'exam_startdate'},
                    { data: 'exam_enddate',  name: 'exam_enddate'},   
                    /*{ data: 'status',  name: 'status'},*/
                 
                ],
                "order":[],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 2 },
                    /*{ "orderable": false, "targets": 6 }*/
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 0 && index != 2 ) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#status_id').on('change', function() {
                table.draw();
            });

            $('#class_id').on('change', function() {
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

            $(".exam_startdate").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left"
            });

            $(".exam_enddate").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left"
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
 

    </script>

@endsection
