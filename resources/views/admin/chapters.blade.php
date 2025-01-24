@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_chapters', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')

<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Chapters', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title">Chapters
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class=" col-md-3 float-left">
                                    <label class="form-label" >Status </label>
                                    <div class="form-line">
                                        <select class="form-control" name="status_id" id="status_id" >
                                            <option value="">All</option>
                                            <option value="ACTIVE" selected>ACTIVE</option>
                                            <option value="INACTIVE">INACTIVE</option>
                                        </select> 
                                    </div>
                                </div>

                                <div class=" col-md-3 float-left">
                                    <label class="form-label" >Class </label>
                                    <div class="form-line">
                                        <select onchange="loadClassSubject(this.value); loadClassTerms(this.value);" class="form-control course_id" name="class_id" id="classid" required >
                                            <option value="">Select Class</option>
                                            @if(!empty($classes)) 
                                                @foreach($classes as $course) 
                                                    <option value="{{$course->id}}">{{$course->class_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class=" col-md-3 float-left">
                                    <label class="form-label" >Subject </label>
                                    <div class="form-line">
                                        <select  class="form-control subject_id" name="subject_id" id="subjectid" required >
                                            <option value="">Select Subject</option> 
                                        </select>
                                    </div>
                                </div>

                                <div class=" col-md-3 float-left">
                                    <label class="form-label" >Terms </label>
                                    <div class="form-line">
                                        <select class="form-control term_id" name="term_id" id="termid" required >
                                            <option value="">Select Term</option> 
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
                                    <table class="table table-striped table-bordered tblcountries">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Class</th>
                                                <th>Subject</th>
                                                <th>Term</th>
                                                <th>Code</th>
                                                <th>Chapter</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                               
                                            </tr>
                                        </thead>
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
                    <h4 class="modal-title" id="smallModalLabel">Add Chapter</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/chapters')}}"
                                  method="post">

                        {{csrf_field()}}
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">

                                    <select onchange="loadClassSubject(this.value); loadClassTerms(this.value);" class="form-control course_id" name="class_id" required >
                                        <option value="">Select Class</option>
                                      @if(!empty($classes))

                                        @foreach($classes as $course)

                                            <option value="{{$course->id}}">{{$course->class_name}}</option>
                                        @endforeach
                                      @endif
                                    </select>

                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-line">

                                    <select class="form-control course_id" id="subject_id" name="subject_id" required >
                                       </select>

                                </div>
                            </div>
                            <div class="row col-md-12">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Terms</label>
                                <div class="form-line">

                                    <select class="form-control term_id" id="term_id" name="term_id" required >
                                    </select>

                                </div>
                            </div>
                            </div>
                            <div class="form-group form-float float-left col-md-5"> 
                                <label class="form-label">Chapter Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="chaptername[]" required>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-5">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position[]" required min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-2">
                                <label class="form-label">   </label>
                                <div class="form-line">
                                    <button type="button" class="btn btn-success center-block plus" id="plus">+</button>
                                </div>
                            </div>
                        </div> 
                        <div id="cloneditems" class="row"></div>
                        <div id="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" required>
                                      <option value="ACTIVE">ACTIVE</option>
                                      <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="row">
                        <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div id="cloneitem" style="display: none;">
        <div class="col-md-12" id="@@cloneitem@@">
            <div class="form-group form-float float-left col-md-5"> 
                <label class="form-label">Chapter Name</label>
                <div class="form-line">
                    <input type="text" class="form-control" name="chaptername[]" required>
                </div>
            </div>
            <div class="form-group form-float float-left col-md-5">
                <label class="form-label">Position</label>
                <div class="form-line">
                    <input type="number" class="form-control" name="position[]" required min="1">
                </div>
            </div>
            <div class="form-group form-float float-left col-md-2">
                <label class="form-label">   </label>
                <div class="form-line">
                    <button type="button" class="btn btn-danger center-block minus" id="minus" onclick="deletehtml('@@cloneitem@@');">X</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Chapter</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/chapters')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Class</label>
                                <div class="form-line">
                                    <select class="form-control" onchange="loadClassSubject(this.value); loadClassTerms(this.value);" id="edit_class_id" name="class_id" required >
                                        <option value="">Select Class</option>
                                      @if(!empty($classes))
                                        @foreach($classes as $course)
                                            <option value="{{$course->id}}" >{{$course->class_name}}</option>
                                        @endforeach
                                      @endif
                                    </select>

                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-line">
                                    <select class="form-control " id="edit_subject_id" name="subject_id" required >
                                     </select>
                                </div>
                            </div>
                            <div class="row col-md-12">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Terms</label>
                                <div class="form-line">

                                    <select class="form-control term_id" id="edit_term_id" name="term_id" required >
                                    </select>

                                </div>
                            </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Chapter Name</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="chaptername" id="edit_chaptername" required>
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

    <script>
        $('#addbtn').on('click', function () {
            $('#style-form')[0].reset();
            $('#cloneditems').html(''); 
        });

        $('.plus').on('click', function () {  
            var cloneitem = $('#cloneitem').html(); 
            var i = $('#cloneditems').find('input[name="chaptername[]"]').length;
            cloneitem = cloneitem.replace(/@@cloneitem@@/g, "cloneitem_"+i);
             
            $('#cloneditems').append(cloneitem); 
        });

        function deletehtml($obj) { 
            $('#'+$obj).remove();  
        }

        $(function() {

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/chapters/datatables/",   
                    data: function ( d ) {
                        var status  = $('#status_id').val();
                        var class_id  = $('#classid').val();
                        var subject_id  = $('#subjectid').val(); 
                        var term_id  = $('#termid').val();
                        $.extend(d, {status:status, class_id:class_id, subject_id:subject_id, term_id:term_id});

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            var ref_code = data.ref_code;

                              return '<a href="#" onclick="loadChapter('+tid+')" title="Edit Chapter" ><i class="fas fa-edit"></i></a>';


                        },

                    },
                    { data: 'is_class_name', name: 'is_class_name'},
                    { data: 'is_subject_name', name: 'is_subject_name'},
                    { data: 'is_term_name', name: 'is_term_name'},
                    { data: 'ref_code', name: 'ref_code'},
                    { data: 'chaptername', name: 'chaptername'},
                    { data: 'position', name: 'position'},
                    { data: 'status', name: 'status'},
                    
                ],
                "order":[[5, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                dom: 'Blfrtip',
                buttons: [

                ],

            });

            $('.tblcountries tfoot th').each( function () {
                var title = $(this).text();
                var index=$(this).index();
                if(index !=0 && index !=1 && index !=2 && index !=3 && index != 7){
                  $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#status_id,#classid,#subjectid,#termid').on('change', function() {
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


            $('#addchapter').on('click', function () {
                $('#style-form .course_id').trigger('change');
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

                        if (response.status == "SUCCESS") {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

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

                           $('.tblcountries').DataTable().ajax.reload();

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

        function loadChapter(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/edit/chapters')}}",
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
                $('#edit_subject_id').val(response.data.subject_id);
                $('#edit_chaptername').val(response.data.chaptername);
                $('#edit_position').val(response.data.position);
                $('#edit_status').val(response.data.status);

                loadClassSubject(response.data.class_id, response.data.subject_id, response.data.is_subject_name);
                loadClassTerms(response.data.class_id, response.data.term_id, response.data.is_term_name);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


        function loadClassSubject(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;


            $("#subject_id,#edit_subject_id,.subject_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-class-subject') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#subject_id,#edit_subject_id,.subject_id').html(
                        '<option value="">-- Select Subject --</option>');

                    if (selid != null && selval != null) {
                        /*$("#edit_subject_id").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');*/
                    }
                    $.each(res.subjects, function(key, value) {
                        var selected = '';
                        if (selid != null && selval != null && selid == value.id) { 
                            selected = ' selected';
                        }
                        $("#subject_id,#edit_subject_id,.subject_id").append('<option value="' + value.id + '" '+ selected+'>' + value.subject_name + '</option>');
                    });
                }
            });
        }


    </script>

@endsection

