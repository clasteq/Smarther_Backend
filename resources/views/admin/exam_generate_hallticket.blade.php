@extends('layouts.admin_master')
@section('academic_settings', 'active')
@section('master_exams', 'active')
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
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Exam - Generate Hall Ticket 
                  </h4>
                  <div class="row">
                    <div class="row col-md-12"> 
                        <div class="form-group col-md-3 " >
                            <input type="hidden" name="exam_id" id="exam_id" value="{{$exam_id}}">
                             <label class="form-label">Exam</label>
                             <span class="form-control" > {{$examinations[0]->exam_name}} </span>
                        </div>
                        <div class="form-group col-md-3 " >
                            <input type="hidden" name="class_id" id="class_id" value="{{$class_id}}">
                             <label class="form-label">Class</label>
                             <span class="form-control" > {{$exams_details_arr[0]['class_names']}} </span>
                        </div>
                     <div class="form-group col-md-3 " >
                         <label class="form-label">Sections</label>
                         <select class="form-control" name="section_id" id="section_id">
                             <option value="0" >All</option>
                             @if(!empty($sections))
                                @foreach($sections as $section)
                                    <option value="{{$section->id}}">{{$section->section_name}}</option>
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
                                  <th>Admission No</th>
                                  <th>Scholar Name</th> 
                                  <th class="no-sort">Image</th>
                                  <th>Class</th>
                                  <th>Section</th>  
                                  <th class="no-sort">Action</th>
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
          </div>
    </section> 

@else 
@include('admin.notavailable') 
@endif

@endsection

@section('scripts')

    <script> 
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/examgeneratescholars/datatables/", 
                    data: function ( d ) {
                        var exam_id  = $('#exam_id').val();
                        var class_id  = $('#class_id').val();
                        var section_id  = $('#section_id').val(); 
                        $.extend(d, {exam_id:exam_id, class_id:class_id, section_id:section_id});

                    }
                },
                columns: [
                    { data: 'admission_no',  name: 'users.admission_no'},
                    { data: 'name',  name: 'users.name'},
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
                            if (data.profile_image != '' || data.profile_image != null) {
                                var tid = data.is_profile_image;
                                return '<img src="' + tid + '" height="50" width="50">';
                            } else {
                                return '';
                            }
                        },

                    },
                    { data: 'class_name',  name: 'classes.class_name'},
                    { data: 'section_name',  name: 'sections.section_name'},   
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            var tid = {{$exam_id}};
                            var is_finished = {{$exams_details_arr[0]['is_finished']}};
                            var publish_status = "{{$exams_details_arr[0]['publish_status']}}";
                            if(is_finished == 1) { return ''; } else { 
                                if(publish_status == 'PUBLISHED') {  
                                    var url = "{{URL('/')}}/admin/download/examhallticket?id="+tid+"&student_id="+data.id;
                                    var hurl = '&nbsp;&nbsp;<a href="'+url+'"  title="Generate Hallticket" target="_blank"><i class="fas fa-download mr-3"></i></a>';
                                }   else {
                                    var hurl = '';
                                } 
                                return hurl; 
                            }
                        },

                    },

                ],
                "order":[[0, 'asc']],
                "columnDefs": [ 
                    { "orderable": false, "targets": 5 }, { "orderable": false, "targets": 2}
                ]
               
            });

            $('.tblcountries tfoot th').each( function (index) {
                if( index != 5 && index != 2) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#section_id').on('change', function() {
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
 

    </script>

@endsection
