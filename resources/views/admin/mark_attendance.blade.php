@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_mark_attendance', 'active')
@section('menuopenatt', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Mark Attendance', 'active'=>'active']];
?>
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


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

        .rounded {
            border-radius: 1rem !important;
        }

        #absentlist .rounded {
            max-width: 50%;
        }

        .modal-full {
            min-width: 95%;
            margin: 10;
        }
        .modal-full .modal-body {
            overflow-y: auto;
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
                  <h4 style="font-size:20px;" class="card-title">Mark Attendance</h4>  
                  <br><br> 
                        <div class="row"> 
                            <div class="col-md-2" hidden>
                                <label style="padding-bottom: 10px;">Academic Year</label>
                                <input type="month" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="monthyear" id="monthyear" value="{{ date('Y-m') }}" minlength="4" maxlength="7">
                            </div>
                            <div class=" col-md-2">
                                <label class="form-label" style="padding-bottom: 10px;">Date </label>
                                <div class="form-line">
                                    <input class="date_range_filter date form-control" name="date" type="date" id="datepicker_from"  value="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                             <div class="col-md-2">
                                <label style="padding-bottom: 10px;">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                @if ($class_id == $class->id) selected @endif>
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class=" col-md-2">
                                <label class="form-label" style="padding-bottom: 10px;">Section </label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-2">
                                <label class="form-label" style="padding-bottom: 10px;">Session </label>
                                <div class="form-line">
                                    <select class="form-control" name="session_id" id="session_id" required>
                                        <option value="1">Full Day</option>
                                        <option value="2">Fore Noon</option>
                                        <option value="3">After Day</option>
                                    </select>
                                </div>
                            </div>

                         
                            <div class="col-md-2"> 
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:40px"
                                    onclick="loadMarkedattendance()">Submit </button>
                            </div>

                            <div class="col-md-2"> 
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:40px"
                                    onclick="loadMarkedattendance()">ADD </button>
                            </div>

                        </div>
                     
                          
                </div> 
                        
                       <!-- <div class="col-md-12" id="attendanceentries">   </div> -->
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                    <div class="table-responsicve">
                                        <table class="table table-striped table-bordered tblcountries">
                                            <thead>
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Section</th>
                                                    <th>Strength</th> 
                                                    <th>Present</th>
                                                    <th>Absent</th>
                                                    <th>Present %</th> 
                                                    <th>Status</th> 
                                                    <th class="no-sort nowrap">Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot class="d-none">
                                                <tr>
                                                    <th></th><th></th><th></th>  
                                                    <th></th><th></th><th></th> 
                                                    <th></th><th></th> 
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
          </div>
    </section> 

    <div class="modal fade " id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Approve Attendance</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="approve-style-form" enctype="multipart/form-data" action="{{ url('/admin/approve/scholarattendance') }}"  method="post">

                    {{ csrf_field() }} 
                    <input type="hidden" name="attids" id="attids">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label style="padding-bottom: 10px;">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                @if ($class_id == $class->id) selected @endif>
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">Section </label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">Session </label>
                                <div class="form-line">
                                    <select class="form-control" name="gender" id="gender" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option> 
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="position-relative">
                                    <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Scholar Name</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="studentName" placeholder="Scholar Name" style="height:50px;" required>
                                            <div id="suggestions" class="name_filter"></div>
                                        </div>
                                </div>
                            </div>         
                        </div>

                        <div class="d-none" id="show_data"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="sumbit" class="btn btn-link waves-effect" id="move_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <input type="hidden" name="getFetchSectionURL" id="getFetchSectionURL"  value="{{ url('admin/fetch-section') }}">
@endsection

@section('scripts')
    <script type="text/javascript">

        function loadMarkedattendance() {
            location.href = "{{URL('/')}}/admin/student_daily_attendance";
        }

        $(function() {

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/load/scholar_marked_attendance",  
                    data: function ( d ) {
                        var status_id  = $('#status_id').val();
                        var section_id = $('#section_id').val();
                        var class_id = $('#class_id').val();
                        var session_id = $('#session_id').val();
                        $.extend(d, { 
                            status_id:status_id,
                            section_id:section_id,
                            class_id:class_id,
                            session_id:session_id 
                        });
                    }
                },
                columns: [ 
                    { data: 'class_name', name: 'classes.class_name' },
                    { data: 'section_name',  name: 'sections.section_name' },  
                    { data: 'strength',  name: 'strength' }, 
                    {  data: 'present', name: 'present' },  
                    { data: 'absent', name: 'absent' },
                    { data: 'present_percentage', name: 'present_percentage' }, 
                    {
                        data: null,
                        "render": function(data, type, row, meta) {  

                            return 'Approvestatus';
                        },

                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) { 
                            var tid = data.id; 
                            var section_id =  data.section_id; 
                            var class_id =  data.class_id; 
                            var vurl = "{{URL('/')}}/admin/student_daily_attendance?class_id="+class_id+"&section_id="+section_id; 

                            return '  <a href="'+vurl+'"  title="View Attendance"  ><i class="fas fa-eye mr-1"></i></a> &nbsp;&nbsp;<a href="#" onclick="approveattendance(' + tid +
                                ')" title="Approve Attendance"><i class="fas fa-calendar-check mr-1"></i></a> &nbsp;&nbsp;<a href="#" onclick="moveStudent(' + tid +
                                ')" title="Move Scholar"><i class=" fas fa-arrow-alt-circle-down mr-1"></i></a>';
                        },

                    },

                ],
                order:[[0, 'asc']],
                "columnDefs": [
                    { "targets": 'no-sort', "orderable": false, },
                    { "targets": 'nowrap', "className": 'nowrap', },
                ], 
                dom: 'Bfrtip',
                "buttons": [
                    {

                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-warning btn-md ml-3',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                "url":"{{URL('/')}}/admin/student_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Scholars.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],


            });


            /*$('.tblcountries tfoot th').each(function(index) {
                if ( index != 1 && index != 7 && index != 8 && index != 9) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });*/ 

            $('#session_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#section_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#class_id').on('change', function() {
                table.draw(); ;//table.draw();
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

            // Event listener for the input field to trigger search on input
            $('#studentName').on('input', function() {
                const nameValue = $(this).val();
                 // Clear suggestions if input length is 0
                 if (nameValue.length === 0) {
                    $('#suggestions').empty();
                    return;
                }

                // Trigger search if input length is 1 or more
                if (nameValue.length >= 1) {
                    searchStudentNames(nameValue);
                }
            });

            // Handle the selection of a student name from suggestions
            $(document).on('click', '.suggestion-item', function() {
                const studentId = $(this).data('id');
                $('#studentName').val($(this).text());
                $('#suggestions').empty();
                addStudentId(studentId);


            //    $('#student_name').text(student.is_student_name);


            });
  
        });

        function searchStudentNames(name) {
            var section_id = $('#approve-style-form #section_dropdown').val();
            var class_id = $('#approve-style-form #class_id').val();
            var batch = ''; 
            var is_absentees = 1;
            var gender = $('#approve-style-form #gender').val();  
            $.ajax({
                type: 'GET',
                url: " {{ URL::to('/admin/search_student') }}",
                dataType: 'json',
                data: {
                    name: name, class_id:class_id, batch:batch, section_id:section_id, gender:gender, is_absentees:is_absentees
                },
                success: function(data) {
                    displaySuggestions(data.students);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function displaySuggestions(students) {
            const suggestionsDiv = $('#suggestions');


            suggestionsDiv.empty();

            if (students.length === 0) {
                suggestionsDiv.append('<p>No students found.</p>');
                return;
            }

            students.forEach(student => {
                var father_name = '';
                if(student.father_name != '' && student.father_name != null) {
                    father_name = student.father_name;
                }   else {
                    father_name = '-';
                }

                const suggestionItem = $('<div class="suggestion-item"></div>')
                    .html(`<strong>${student.is_student_name} [${student.is_class_name}-${student.is_section_name}]</strong> <br> Father: ${father_name} Mobile ${student.mobile} Adm No: ${student.admission_no}`)
                    .attr('data-id', student.user_id)
                    .css({ padding: '5px', cursor: 'pointer' });
                suggestionsDiv.append(suggestionItem);
            });


        }

        function addStudentId(studentId) {
            var attids = $('#attids').val();
            attids += ','+studentId;
            $('#attids').val(attids);
            $('#show_data').addClass('d-none');   
            $.ajax({
                type: 'POST',
                url: " {{ URL::to('/admin/addabsentstudent') }}",

                dataType: 'json',
                data: {
                    student_id: studentId, attids:attids,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    // Assuming data contains the student details
                    const content = data.content;  
                    $('#show_data').removeClass('d-none'); 
                    $('#show_data').html(content); 
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function loadClassSections(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval; 
            $("#section_dropdown,#edit_section_dropdown").html('');
            $.ajax({
                url: $('#getFetchSectionURL').val(),
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json', 
                success: function(res) {

                    $('#section_dropdown,#edit_section_dropdown').html(
                        '<option value="">-- Select Section --</option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $("#section_dropdown,#edit_section_dropdown").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                    loadStudentattendance();
                }
            });
        }
 
 
        function approveattendance() {
            $('#smallModal').modal('show');
        }
    </script>
@endsection
