@extends('layouts.admin_master')
@section('academic_settings', 'active')
@section('master_examination_settings', 'active')
@section('menuopena', 'active menu-is-opening menu-open') 
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Exams', 'active'=>'active']];
?>
@section('content')

<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@if((isset($session_module['Exams']) && ($session_module['Exams']['list'] == 1)) || ($user_type == 'SCHOOL'))
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" name="school_code" id="school_code" value="{{$school_id}}">
    <input type="hidden" name="loadclasses" id="loadclasses" value="{!! URL('admin/fetch-term-classes') !!}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Exam Settings
                    @if((isset($session_module['Exams']) && ($session_module['Exams']['add'] == 1)) || ($user_type == 'SCHOOL'))
                    <a href="{!! URL('admin/examsettings') !!}" ><button class="btn btn-primary" id="addbtn" style="float: right;">Add</button></a>
                    @endif
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

                    <div class="col-md-3">
                        <label class="form-label">Term</label>
                        <select class="form-control term_id" name="term_id" id="sel_term_id"
                                onchange="loadClasses(this.value);">
                            <option value="">Select Term</option>
                            @if (!empty($terms))
                                @foreach ($terms as $term)
                                    <option value="{{ $term->id }}" >
                                        {{ $term->term_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                     
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select class="form-control course_id class_id" name="class_id" id="class_id"
                                onchange="loadClassSection(this.value);">
                            <option value="">Select Class</option>
                            @if (!empty($classes))
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" >
                                        {{ $class->class_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class=" col-md-3">
                        <label class="form-label">Section <span class="manstar">*</span></label>
                        <div class="form-line"> <!-- loadClassSubjects(this.value); -->
                            <select class="form-control" name="section_id" id="section_dropdown"    >

                            </select>
                        </div>
                    </div>
                     <div class="form-group col-md-3 " >
                         <label class="form-label">Scheduled Status</label>
                         <select class="form-control" name="schedule_status_id" id="schedule_status_id">
                             <option value="" >All</option>
                             <option value="SCHEDULED">Scheduled</option>
                             <option value="UNSCHEDULED">Un Scheduled</option>
                         </select>
                     </div>
                     <div class="form-group col-md-3 " >
                         <label class="form-label">Publish Status</label>
                         <select class="form-control" name="publish_status_id" id="publish_status_id">
                             <option value="" >All</option>
                             <option value="PUBLISHED">Published</option>
                             <option value="UNPUBLISHED">Un Published</option>
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
                                  <th>Term</th>   
                                  <th>Exam</th>
                                  <th>Class</th> 
                                  <th>Sections</th>
                                  <th>Start Date</th> 
                                  <th>End Date</th> 
                                  <th>Schedule Status</th>
                                  <th>Publish Status</th>
                                  <th>Status</th>
                                  <th class="no-sort nowrap">Action</th>
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th><th></th>
                                      <th></th><th></th><th></th> 
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
          </div>
    </section> 

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
                    "url":"{{URL('/')}}/admin/examination_settings/datatables/", 
                    data: function ( d ) { 
                        var status  = $('#status_id').val();
                        var schedule_status  = $('#schedule_status_id').val();
                        var publish_status  = $('#publish_status_id').val(); 
                        var class_id  = $('#class_id').val(); 
                        var section_id = $('#section_dropdown').val(); 
                        $.extend(d, {status:status, schedule_status:schedule_status, publish_status:publish_status, 
                        class_id:class_id, section_id:section_id});

                    }
                },
                columns: [
                    { data: 'term_name',  name: 'terms.term_name'},
                    { data: 'exam_name',  name: 'examinations.exam_name'},
                    { data: 'class_names',  name: 'class_names'},
                    { data: 'section_names',  name: 'section_names'},
                    { data: 'exam_startdate',  name: 'exam_startdate'},
                    { data: 'exam_enddate',  name: 'exam_enddate'},   
                    { data: 'schedule_status',  name: 'schedule_status'},
                    { data: 'publish_status',  name: 'publish_status'},
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            var tid = data.examination_id;
                            var is_finished = data.is_finished;
                            //if(is_finished == 1) { return ''; } else {
                                if(data.exam_id > 0) {
                                    var url = "{{URL('/')}}/admin/edit/examsettings?id="+data.exam_id;
                                    var surl = '&nbsp;&nbsp;<a href="'+url+'"  title="Edit Exam Settings"><i class="fas fa-cog mr-3"></i></a>';
                                } else {
                                    var surl = '';
                                }  

                                if(data.publish_status == 'PUBLISHED') {
                                    var url = "{{URL('/')}}/admin/download/examhallticket?id="+data.exam_id;
                                    var durl = '&nbsp;&nbsp;<a href="'+url+'"  title="Download Hallticket" target="_blank"><i class="fas fa-download mr-3"></i></a>';

                                    var url = "{{URL('/')}}/admin/generate/examhallticket?id="+data.exam_id;
                                    var hurl = '&nbsp;&nbsp;<a href="'+url+'"  title="Generate Hallticket" target="_blank"><i class="fas fa-cloud-download-alt mr-3"></i></a>';
                                }   else {
                                    var hurl = ''; var durl = '';
                                }

                                return  surl+durl+hurl; 
                            //}
                        },

                    },

                ],
                "order":[[4, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": 1 },
                    { "orderable": false, "targets": 2 },
                    { "orderable": false, "targets": 5 },
                    { "orderable": false, "targets": 6 },
                    { "orderable": false, "targets": 7 },
                    { "orderable": false, "targets": 8 },
                ]
               
            });

            $('.tblcountries tfoot th').each( function (index) {
                if( index != 8) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );  

            $('#status_id, #publish_status_id, #schedule_status_id, #class_id, #section_dropdown').on('change', function() {
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
        });

        function loadExam(id){

            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/admin/edit/examinations')}}",
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
                $('#edit_term_id').val(response.data.term_id); 
                $('#edit_exam_name').val(response.data.exam_name); 
                $('#edit_status').val(response.data.status); 
                //$('#edit_schedule_status').val(response.data.schedule_status);  
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }


    </script>

@endsection
