@extends('layouts.admin_master')
@section('mapsettings', 'active')
@section('master_subject_mapping', 'active')
@section('menuopenmap', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers Subject Mapping', 'active' => 'active']];
?>
@section('content')



    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title">Subject Mapping
                            <a href="{{URL('/')}}/admin/add/subjectmapping"><button  class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class="row">
                            <div class="row col-md-12">
                             <div class="form-group col-md-3 d-none" >
                                 <label class="form-label">Status</label>
                                 <select class="form-control" name="status_id" id="status_id">
                                     <option value="" >All</option>
                                     <option value="ACTIVE" selected>ACTIVE</option>
                                     <option value="INACTIVE" >INACTIVE</option>
                                 </select>
                             </div>
                         </div>
                         <div class="form-group form-float float-left col-md-3">
                            <label class="form-label">Class<span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control course_id" id="class_id" name="class_id"
                                    onchange="loadClassSection(this.value)" required>
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
                        <div class="form-group form-float float-left col-md-3">
                            <label class="form-label">Section <span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control" onchange="loadsubjects(this.value)" name="section_id" id="section_dropdown" required>

                                </select>
                            </div>
                        </div>

                        <div class="form-group form-float float-left col-md-3">
                            <label class="form-label">Subject <span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control" name="subject_id" id="subject_dropdown" required>

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
                                                <th>Teacher Name</th>
                                                <th class="no-sort">Handling Classes</th>
                                                <th class="no-sort">Action</th>
                                                {{-- <th>Class Teacher</th>
                                                <th>Section</th>
                                                <th>Subject</th> 
                                               <th>Status</th>--}}

                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                {{-- <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th> --}}

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
        $(function() {
            $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/subject_mapping/datatables/",   
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        var class_id  = $('#class_id').val();
                        var section_id  = $('#section_dropdown').val();
                        var subject_id  = $('#subject_dropdown').val();
                        $.extend(d, {status:status,class_id:class_id,section_id:section_id,subject_id:subject_id});

                    }
                },
                columns: [
                    {
                        data: 'name',
                        name: 'users.name'
                    },
                    {
                        data: 'handling_classes',
                        name: 'handling_classes'
                    }, 
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            var url = "{{URL('/')}}/admin/edit/subject_mapping/"+tid ;
                            return '<a href="'+url+'"  title="Edit"><i class="fas fa-edit"></i></a>';
                        },

                    },
                //     {
                //         data: 'class_name',
                //         name: 'classes.class_name'
                //     },
                //     {
                //         data: 'section_name',
                //         name: 'sections.section_name'
                //     },
                //   {
                //         data: 'subject_name',
                //         name: 'subjects.subject_name'
                //     },
                    /*{
                        data: 'status',
                        name: 'subject_mapping.status'
                    },*/

                ],
                "order" : 0,
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]


            });


            $('.tblcountries tfoot th').each(function(index) {
                if (index == 0) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });

            $('#status_id,#class_id,#section_dropdown,#subject_dropdown').on('change', function() {
                table.draw();
            });

            // Apply the search
            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
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

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == "FAILED") {

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

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                        } else if (response.status == "FAILED") {

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

        function loadTeacher(id,teacher_id) {
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/subject_mapping') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {


                $('#id').val(response.data.id);
                $('#edit_class_id').val(response.data.class_id);
                $('#edit_teacher_id').val(response.data.teacher_id);
                var val = response.data.class_id;
                var selectedid = response.data.section_id;
                loadClassSection(val, selectedid);
                var val = response.data.section_id;
                var selectedid = response.data.subject_id;
                loadsubjects(val,selectedid)
                $('#edit_status').val(response.data.status);

                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


        function loadClassSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#section_dropdown').html(
                            '<option value="">-- Select Section --</option>');
                    $.each(res.section, function(key, value) {
                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

           function loadsubjects(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var section_id = val;
            var selid = selectedid;
            var selval = selectedval;
            $("#subject_dropdown,#edit_subject_dropdown").html('');
            $.ajax({
                url: "{{ url('admin/fetch-subject') }}",
                type: "POST",
                data: {
                    section_id: section_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#subject_dropdown').html(
                            '<option value="">-- Select Subject --</option>');
                    $.each(res.subjects, function(key, value) {
                        var selected = '';
                        if(selectedid != '' && selectedid == value
                            .id) {
                            selected = ' selected ';
                        }
                        $("#subject_dropdown,#edit_subject_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }
    </script>
@endsection
