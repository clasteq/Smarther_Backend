@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_sections', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Sections', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('public/css/select2.min.css') }}"> 
<style>
    .dropdown-menu.show {
        display: block;
        width: 100%;
        top: 30px !important;
        left: auto !important;
        padding: 20px;
    }
    .checkbox input[type="checkbox"] {
            width: 20px !important;
            
    }
     .select2-container--default .select2-selection--single {
        height: 45px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-top: 8px; 
    }
    .select2-selection__choice {
        color: #000 !important;
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
    .row.merged20 {
        margin: 0px 0px !important;
    }

    .sidecoderight {
        padding-top: 40px !important ;
    }
    body{
            margin-left: 0px !important;
    }

    .nnsec{
        margin-left: -14px;margin-right: 10px;border-right: 1.5px solid #ecebeb85;padding-top:40px !important;
    }
    @media screen and (max-width: 700px){
        .nnsec{
            margin-left: 0px !important;margin-right: 0px !important;border-right: 0px solid #ecebeb85!important;padding-top:20px !important;
        }
        .row.merged20 {
            padding: 0px 0px !important;
        }
        .vanilla-calendar {
            width: 100% !important;
        }
    }
</style>
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Sections
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class="row">
                            <div class="row col-md-12">
                             <div class="form-group col-md-3 " >
                                 <label class="form-label">Status</label>
                                 <select class="form-control" name="status_id" id="status_id">
                                     <option value="" >All</option>
                                     <option value="ACTIVE" selected>ACTIVE</option>
                                     <option value="INACTIVE">INACTIVE</option>
                                 </select>
                             </div>
                             <div class=" col-md-3 d-none">
                                <label class="form-label" >Subjects </label>
                                <div class="form-line">
                                    <select class="form-control" name="subject_id" id="subject_id" >
                                        <option value="">Select Subject</option>
                                        @if (!empty($subject))
                                            @foreach ($subject as $subjects)
                                                <option value="{{ $subjects->id }}">
                                                    {{ $subjects->subject_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    </select>
                                </div>
                            </div>
                         </div>
        
                     </div>

                    
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries" id="section_table">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Class Name</th>
                                                <th>Section Name</th>
                                                <!-- <th>Subjects</th> 
                                                <th>Position</th>-->
                                                <th>Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <!-- <th></th> 
                                                <th></th>-->
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
                    <h4 class="modal-title" id="smallModalLabel">Add Sections</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data" action="{{ url('/admin/save/sections') }}"
                    method="post">

                    {{ csrf_field() }}

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control" name="class_id" required>
                                        <option value=""> Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="section_name" required minlength="1"
                                        maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Subjects</label>
                                <div class="form-line">
                                    <select class="form-control select2" multiple="multiple" name="mapped_subjects[]">
                                        <option value="" disabled>Subjects</option>
                                        @if (!empty($subject))
                                            @foreach ($subject as $sub)
                                                <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" value="99" min="1">
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
                    <h4 class="modal-title" id="smallModalLabel">Edit Sections</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/save/sections') }}"
                    method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control" name="class_id" id="edit_class_id" required>
                                        <option value=""> Select Class</option>
                                        @if (!empty($classes))
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Section Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control "name="section_name"
                                        id="edit_section_name" required minlength="1" maxlength="200">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Subjects</label>
                                <div class="form-line">
                                    <select class="form-control select2" multiple="multiple" name="mapped_subjects[]" id="edit_mapped_subjects">
                                        <option value="" disabled>Subjects</option>
                                        @if (!empty($subject))
                                            @foreach ($subject as $sub)
                                                <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position"
                                         min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" required>
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
            $('.select2').select2(); 
        });
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/sections/datatables/", 
                    data: function ( d ) {
                        var subject  = $('#subject_id').val();
                        var status = $('#status_id').val();
                        $.extend(d, {subject:subject,status:status});

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadSection(' + tid +
                                ')" title="Edit Section"><i class="fas fa-edit"></i></a>';
                        },

                    },
                    {
                        data: 'class_name',
                        name: 'classes.class_name'
                    },
                    {
                        data: 'section_name',
                        name: 'sections.section_name'
                    },
                    /*{
                        data: 'is_subject_name',
                        name: 'sections.is_subject_name'
                    },
                    {
                        data: 'position',
                        name: 'sections.position'
                    },*/
                    {
                        data: 'status',
                        name: 'sections.status'
                    },
                  
                ],
                "order":[[1, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
              
            });

            $('.tblcountries tfoot th').each(function(index) {
                if (index != 0 && index != 3) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search' + title + '" />');
                }
            });

            $('#subject_id').on('change', function() {
                table.draw();
            });

            $('#status_id').on('change', function() {
                table.draw();
            });
            // Apply the search
            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#style-form").ajaxForm(options);
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

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                        } else if (response.status == 0) {

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

        function loadSection(id) {
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('/admin/edit/sections') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    code: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {

                $('#id').val(response.data.id);
                $('#edit_class_id').val(response.data.class_id);
                $('#edit_section_name').val(response.data.section_name);
                /*var mapped_subjects = response.data.mapped_subjects;
                if(mapped_subjects != '' && mapped_subjects != null) {
                    var maps = mapped_subjects.split(',');
                    $('#edit_mapped_subjects').val(maps).select2();
                }*/
 
                $('#edit_status').val(response.data.status);
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }
    </script>

@endsection
