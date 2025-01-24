@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_oa_student_attendance', 'active')
@section('menuopenatt', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Student Attendance', 'active'=>'active']];
?>
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.0/css/fixedHeader.dataTables.css">

    <style>
        .form-control:focus {
            color: #495057;
            background-color: #fff !important;
            border: none;
            outline: 0;
            box-shadow: 0 0 0 0.2rem #dee2e6 !important;
        }

        .greentick {
            color: #A3D10C;
        }

        .redcross {
            color: #dc3545;
        }

        .greentickbox {
            color: #fff;
            background: #007bff;
            font-size: 10px;
            padding: 4px;
            cursor: pointer;
        }

        .redcrossbox {
            color: #fff;
            background: #dc3545;
            font-size: 13px;
            padding: 4px;
            margin-top: 5px;
            cursor: pointer;
        }

        .greentickboxharizondal {
            color: #fff;
            background: #007bff;
            font-size: 10px;
            padding: 5px 4px 4px 4px;
        }

        .redcrossboxharizondal {
            color: #fff;
            background: #dc3545;
            font-size: 12px;
            padding: 4px;
            margin-top: 0px;
        }

        .rowcen {
            padding-left: 6px;
            margin-top: 7px;
        }

        @media only screen and (max-width: 600px) {
            .my-account-form {
                overflow-x: scroll !important;
            }

        }

        /* Set a fixed scrollable wrapper */
        .tableWrap {
          height: 500px;
          border: 2px solid black;
          overflow: auto;
        }
        /* Set header to stick to the top of the container. */
        thead tr th {
          position: sticky;
          top: 0;
        }

        /* If we use border,
        we must use table-collapse to avoid
        a slight movement of the header row */
        table {
         border-collapse: collapse;
        }

        /* Because we must set sticky on th,
         we have to apply background styles here
         rather than on thead */
        th {
          padding: 16px;
          padding-left: 15px;
          border-left: 1px dotted rgba(200, 209, 224, 0.6);
          border-bottom: 1px solid #e8e8e8;
          background: #A3D10C;
          text-align: left;
          /* With border-collapse, we must use box-shadow or psuedo elements
          for the header borders */
          box-shadow: 0px 0px 0 2px #e8e8e8;
        }

        /* Basic Demo styling */
        table {
          width: 100%;
          font-family: sans-serif;
        }
        table td {
          padding: 16px;
        }
        tbody tr {
          border-bottom: 2px solid #e8e8e8;
        }
        thead {
          font-weight: 500;
          color: rgba(0, 0, 0, 0.85);
        }
        tbody tr:hover {
          background: #e6f7ff;
        }

        .dataTables_filter {
            display: block !important;
        }
        .bold {
            font-weight: bold;
        }
        .dtfh-floatingparent  .tblcountries {
            left: 0% !important;
        }
    </style>
@endsection
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Students Attendance Report</h4>  
                  <br><br> 
                        <div class="row"> 
                            <div class="col-md-4">
                                <label style="padding-bottom: 10px;">date</label>
                                <input type="date" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="cdate" id="cdate" value="{{ $cdate }}">
                            </div>
                            <div class="col-md-4">
                                <label style="padding-bottom: 10px;">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class=" col-md-4">
                                <label class="form-label" style="padding-bottom: 10px;">Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-none">
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                    onclick="loadStudentattendanceoa()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                     
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve" id="attendanceoverall">
                            <table style="table">
                                <thead>
                                    <tr> 
                                        <th>Attendance</th> <th>Total</th> <th colspan="2">Present</th> <th colspan="2">Absent</th>
                                    </tr> 
                                    <tr> 
                                        <th colspan="2"> </th> <th>FN</th> <th>AN</th> <th>FN</th> <th>AN</th>
                                    </tr>  
                                    <tr>
                                        <th>Overall Students</th> <th id="oa_students">{{$oa_students}}</th> 
                                        <th id="att_oap_fn">{{$att_oap_fn}}</th> <th id="att_oap_an">{{$att_oap_an}}</th> 
                                        <th id="att_oaa_fn">{{$att_oaa_fn}}</th> <th id="att_oaa_an">{{$att_oaa_an}}</th> 
                                    </tr>
                                    <tr>  
                                        <th>Overall Boys</th> <th id="oa_boys">{{$oa_boys}}</th> 
                                        <th  id="att_bp_fn">{{$att_bp_fn}}</th> <th id="att_bp_an">{{$att_bp_an}}</th> 
                                        <th id="att_ba_fn">{{$att_ba_fn}}</th> <th id="att_ba_an">{{$att_ba_an}}</th>
                                    </tr> 
                                    <tr>  
                                        <th>Overall Girls</th> <th id="oa_girls">{{$oa_girls}}</th> 
                                        <th id="att_gp_fn">{{$att_gp_fn}}</th> <th id="att_gp_an">{{$att_gp_an}}</th> 
                                        <th id="att_ga_fn">{{$att_ga_fn}}</th> <th id="att_ga_an">{{$att_ga_an}}</th>
                                    </tr>  
                                </thead> 
                            </table>
                        </div>
                        <form method="post" name="frm_attendanceentries" id="frm_attendanceentries" action="{{URL::to('/owners/approve/oaAttendance')}}">
                        <div class="clearfix"> &nbsp;</div> 
                        <div class="table-responsicve" id="attendanceentries">
                            
                            <table class="table table-striped table-bordered tblcountries">
                                <thead>
                                    <tr> 
                                        <th>Class</th> 
                                        <th>Section</th>
                                        <th></th>
                                        <th colspan="6">Overall</th>
                                        <th colspan="6">Boys</th>
                                        <th colspan="6">Girls</th>
                                    </tr>
                                    <tr> 
                                        <th> </th> 
                                        <th> </th>
                                        <th> </th>
                                        <th colspan="2">Total</th>
                                        <th colspan="2">Present</th>
                                        <th colspan="2">Absent</th>
                                        <th colspan="2">Total</th>
                                        <th colspan="2">Present</th>
                                        <th colspan="2">Absent</th>
                                        <th colspan="2">Total</th>
                                        <th colspan="2">Present</th>
                                        <th colspan="2">Absent</th>
                                    </tr>
                                    <tr> 
                                        <th> </th> 
                                        <th> </th>
                                        <th></th>
                                        <th class="bold">FN</th>
                                        <th class="bold">AN</th>
                                        <th>FN</th>
                                        <th>AN</th>
                                        <th>FN</th>
                                        <th>AN</th>
                                        <th class="bold">FN</th>
                                        <th class="bold">AN</th>
                                        <th>FN</th>
                                        <th>AN</th>
                                        <th>FN</th>
                                        <th>AN</th>
                                        <th class="bold">FN</th>
                                        <th class="bold">AN</th>
                                        <th>FN</th>
                                        <th>AN</th>
                                        <th>FN</th>
                                        <th>AN</th>
                                    </tr>
                                </thead>   
                                <tfoot>
                                    <tr><td></td><td></td><td></td>
                                        <td class="bold"></td>
                                        <td class="bold"></td><td></td><td></td>
                                        <td class="bold"></td>
                                        <td class="bold"></td><td></td><td></td>
                                        <td class="bold"></td>
                                        <td class="bold"></td>
                                        <td></td><td></td><td></td>
                                        <td></td><td></td><td></td>
                                        <td></td><td></td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                
                                </tbody>
                            </table>
                            
                        </div>
                        <div class="clearfix"> <button class="btn btn-success" id="approveOaAttendance">Approve</button></div>
                        </form>
                    </div>
                  </div>
                </div> 
              </div>
            </div>
          </div>
    </section> 
 
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.0/js/dataTables.fixedHeader.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.0/js/fixedHeader.dataTables.js"></script>

    <script type="text/javascript"> 
        $(function() {
       
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                fixedColumns: true,
                fixedHeader: true, 
                responsive: false,
                paging:   false,
                searching: false,
                "ordering": false, 
                "ajax": {/*$total = $oa_boys + $oa_girls;*/
                    "url":"{{URL('/')}}/admin/oa_student_attendance/datatables/",  
                    data: function ( d ) {
                        var cdate  = $('#cdate').val();
                        var class_id = $('#class_id').val();
                        var section_dropdown  = $('#section_dropdown').val(); 
                        $.extend(d, {cdate:cdate,class_id:class_id,
                        section_dropdown:section_dropdown});

                    }
                },

                columns: [  
                    { data: 'class_name',  name: 'class_name'},  
                    { data: 'section_name',  name: 'section_name'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) { 
                            var section_id = data.id; var class_id = data.class_id;
                            return ' ';
                             
                        },

                    }, 

                    { data: 'attendance.total',  name: 'attendance.total'},  
                    { data: 'attendance.total',  name: 'attendance.total'},  
                    { data: 'attendance.tot_p_fn',  name: 'attendance.tot_p_fn'},
                    { data: 'attendance.tot_p_an',  name: 'attendance.tot_p_an'},
                    { data: 'attendance.tot_a_fn',  name: 'attendance.tot_a_fn'},
                    { data: 'attendance.tot_a_an',  name: 'attendance.tot_a_an'}, 
                   
                    { data: 'attendance.tot_b_fn',  name: 'attendance.tot_b_fn'},
                    { data: 'attendance.tot_b_an',  name: 'attendance.tot_b_an'},
                    { data: 'attendance.att_bp_fn',  name: 'attendance.att_bp_fn'},  
                    { data: 'attendance.att_bp_an',  name: 'attendance.att_bp_an'},
                    { data: 'attendance.att_ba_fn',  name: 'attendance.att_ba_fn'}, 
                    { data: 'attendance.att_ba_an',  name: 'attendance.att_ba_an'},

                    { data: 'attendance.tot_g_fn',  name: 'attendance.tot_g_fn'},
                    { data: 'attendance.tot_g_an',  name: 'attendance.tot_ga_fn'}, 
                    { data: 'attendance.att_gp_fn',  name: 'attendance.att_gp_fn'},  
                    { data: 'attendance.att_gp_an',  name: 'attendance.att_gp_an'},
                    { data: 'attendance.att_ga_fn',  name: 'attendance.att_ga_fn'}, 
                    { data: 'attendance.att_ga_an',  name: 'attendance.att_ga_an'}, 
                    
                    
                ],
                "order": [],
                "columnDefs": [
                
                    { "targets": 'no-sort', "orderable": false, },
                    { className: 'bold', targets: [0,2,3,8,9,14,15] }
                ],
                "fnDrawCallback": function( oSettings ) {
                    var overall = oSettings.json.overall;
                    $('#oa_students').text(overall.oa_students);
                    $('#att_oap_fn').text(overall.att_oap_fn);
                    $('#att_oap_an').text(overall.att_oap_an);
                    $('#att_oaa_fn').text(overall.att_oaa_fn);
                    $('#att_oaa_an').text(overall.att_oaa_an);
                    $('#oa_boys').text(overall.oa_boys);
                    $('#att_bp_fn').text(overall.att_bp_fn);
                    $('#att_bp_an').text(overall.att_bp_an);
                    $('#att_ba_fn').text(overall.att_ba_fn);
                    $('#att_ba_an').text(overall.att_ba_an);
                    console.log( oSettings.json );
                }

            }); 

            $('.tblcountries tfoot th').each( function (index) {
                var title = $(this).text();
                if(index != 1 && index != 4)
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
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

            $('#class_id').on('change', function() {
                table.draw();
            });
            $('#section_dropdown').on('change', function() {
                table.draw();
            });
            $('#cdate').on('change', function() {
                table.draw();
            });


            $('#approveOaAttendance').on('click', function () {

                var lengthchked = $('input:checkbox:checked').length;
                if(lengthchked > 0) {

                    var options = {

                        beforeSend: function (element) {

                            $("#approveOaAttendance").text('Processing..');

                            $("#approveOaAttendance").prop('disabled', true);

                        },
                        success: function (response) {



                            $("#approveOaAttendance").prop('disabled', false);

                            $("#approveOaAttendance").text('SUBMIT');

                            if (response.status == "SUCCESS") {

                               swal('Success',response.message,'success');

                               $('.tblcountries').DataTable().ajax.reload();

                               $('#smallModal').modal('hide');

                            }
                            else if (response.status == "FAILED") {

                                swal('Oops',response.message,'warning');

                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            $("#approveOaAttendance").prop('disabled', false);

                            $("#approveOaAttendance").text('SUBMIT');

                            swal('Oops','Something went to wrong.','error');

                        }
                    };
                    $("#frm_attendanceentries").ajaxForm(options);

                } else {
                    swal('Oops',"Please select the sections need to Approve",'warning');
                    return false;
                }
            });

        } );

        function checkSession(){ 

            if($('#att_chk').prop('checked')){
                var checked_val = this.value; 
                $("input.att_section").prop('checked', true);
                      
            }else{
                var checked_val = this.value; 
                $("input.att_section").prop('checked', false);
            }
        }
    </script>
@endsection
