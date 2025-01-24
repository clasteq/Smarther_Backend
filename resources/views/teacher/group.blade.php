@extends('layouts.teacher_master')
@section('communication_settings', 'active')
@section('master_group', 'active')
@section('menuopencomn', 'active menu-is-opening menu-open') 
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Group', 'active'=>'active']];
?><link rel="stylesheet" href="{{asset('public/css/select2.min.css') }}"> 
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .dropdown-menu.show {
            display: block;
            width: 100%;
            top: 30px !important;
            left: auto !important;
            padding: 20px;
        } 
        .select2-container--default .select2-selection--single {
            height: 45px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-top: 8px;
        }
        .select2-container{
            width:100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #f8fafa;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }
           
        .scrollable-form {
            max-height: 200px;
            overflow-y: auto;
        }
        #noResults {
            color: red;
            font-weight: bold;
        }

    </style>

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Group
                    <a href="#" data-toggle="modal" data-target="#smallModal"><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>
                  </h4>
                  <div class="row">
                    <div class="row col-md-12">
                     <div class="form-group col-md-3 " >
                         <label class="form-label">Status</label>
                         <select class="form-control" name="status_id" id="status_id">
                             <option value="" >All</option>
                             <option value="ACTIVE">ACTIVE</option>
                             <option value="INACTIVE">INACTIVE</option>
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
                                  <th>Group Name</th> 
                                  <th>Status</th>

                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th> 
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
                    <h4 class="modal-title" id="smallModalLabel">Add Group</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/teacher/save/group')}}"
                                  method="post">

                        {{csrf_field()}}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Group Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="group_name" required minlength="3" maxlength="200">
                                </div>
                            </div>
                            
                            <div class="form-group  col-md-12">
                                <input type="text" class="form-control searchInput" id="searchStudent" placeholder="Search Students..">
                            </div>
                            <div class="scrollable-form  col-md-12">
                                @foreach($get_student as $student)
                                    <div class="studentItem">
                                        <input type="checkbox" id="student_{{$student->user_id}}" name="student_id[]" value="{{$student->user_id}}">
                                        <label for="student_{{$student->user_id}}">{{$student->is_student_name}}-({{$student->is_class_name}}-{{$student->is_section_name}})</label><br>
                                    </div>
                                @endforeach  
                                <div class="noResults" id="noStudentResults" style="display: none;">No Matching record</div>
                            </div>

                            <div class="form-group form-float float-left col-md-12">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Group</h4> 
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/teacher/save/group')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Group Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="group_name" id="group_name" required minlength="3" maxlength="200">
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <input type="text" class="form-control searchInput" id="searchStudent" placeholder="Search Students..">
                            </div>
                            <div class="scrollable-form col-md-12">
                                @foreach($get_student as $student)
                                    <div class="studentItem">
                                        <input type="checkbox" id="student_{{$student->user_id}}" name="student_id[]" value="{{$student->user_id}}">
                                        <label for="student_{{$student->user_id}}">{{$student->is_student_name}}-({{$student->is_class_name}}-{{$student->is_section_name}})</label><br>
                                    </div>
                                @endforeach  
                                <div class="noResults" id="noStudentResults" style="display: none;">No Matching record</div>
                            </div>
 
                            <div class="form-group form-float float-left col-md-12">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status"  id="status" required>
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
    <script src="{{asset('public/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Function to filter list items based on search term
            function filterList(inputId, itemClass, noResultsId) {
                $(inputId).on('input', function() {
                    var searchTerm = $(this).val().toLowerCase();
                    var found = false;

                    $(itemClass).each(function() {
                        var itemName = $(this).find('label').text().toLowerCase();
                        if (itemName.includes(searchTerm)) {
                            $(this).show();
                            found = true;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (found) {
                        $(noResultsId).hide();
                    } else {
                        $(noResultsId).show();
                    }
                });
            }

            // Initialize the search functionality for groups, students, and sections 
            filterList('#style-form #searchStudent', '#style-form .studentItem', '#style-form #noStudentResults'); 
            filterList('#edit-style-form #searchStudent', '#edit-style-form .studentItem', '#edit-style-form #noStudentResults'); 
        });
    </script>

    <script>
        $('#addbtn').on('click', function () {
            $('#style-form')[0].reset();
        });
        $('.select2').select2();
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/teacher/group/datatables/", 
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
                            return '<a href="#" onclick="loadGroup('+tid+')" title="Edit Group"><i class="fas fa-edit"></i></a>';
                        },

                    },
                    { data: 'group_name',  name: 'group_name'}, 
                    { data: 'status',  name: 'status'},

                ],
                "order":[[1, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
               
            });

            $('.tblcountries tfoot th').each( function (index) {
                if( index != 0 && index != 2) {
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

                        $("#edit_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            });
        });

        function loadGroup(id){
            $('#edit-style-form')[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/teacher/edit/group')}}",
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
                $('#group_name').val(response.data.group_name);
                $('#status').val(response.data.status); 

                if($.trim(response.data.members) != '') {
                    var members = response.data.members;
                    members = members.split(',');
                    $.each( members, function( key, value ) {
                        $('#edit-style-form #student_'+value).prop('checked', 'checked');
                    });
                }

                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadstudents(section_id,class_id) { 
            //$(".student_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-student') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    section_id: section_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    /*$('.student_id').html(
                        '<option value="">-- Select Student --</option>');*/
                
                    $.each(res.student, function(key, value) {
                         $(".student_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }
    </script>

@endsection
