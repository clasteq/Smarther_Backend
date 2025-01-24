@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_studentsabsent', 'active')
@section('menuopenatt', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Students Absent report', 'active'=>'active']];
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
    </style>
@endsection
@section('content')
    <link rel="stylesheet" href="{{asset('/public/css/bootstrap-datepicker.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Students Absence Report</h4>  
                  <br><br> 
                        <div class="row"> 
                            <div class="form-group col-md-3">
                                <label style="">Date</label>
                                <input type="text" name="daterange" id="date"  class="form-control"/>
                                 
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Class Name</label>
                                <select class="form-control" onchange="loadSection(this.value)" name="class_id" id="class_id">
                                    <option value="" >All</option>
                                    @if(!@empty($class))
                                    @foreach ($class as $classes)
                                    <option value={{$classes['id']}} >{{$classes['class_name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Section Name</label>
                                <select class="form-control" onchange="fetch_student(this.value,class_id.value)" name="section_id" id="section_id">
                               
                                </select>
                            </div>
                         <div class="form-group col-md-3 " >
                             <label class="form-label">Student Name</label>
                             <select class="form-control" name="student_id" id="student_id">
                               
                             </select>
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
                                            <th class="no-sort">Student</th>
                                            <th class="no-sort">Class</th>
                                            <th class="no-sort">Section</th>
                                            <th class="no-sort">Father Mobile</th>
                                            <th class="no-sort">Mother Mobile</th>
                                            <th class="no-sort">Forenoon</th>
                                            <th class="no-sort">Afternoon</th>
                                            
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

var currentDate = new Date().toISOString().slice(0, 10);

// Set the value of the input field to the current date
document.getElementById("date").value = currentDate;

    $(function() {

        var table = $('.tblcountries').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            "ajax": {
                "url":"{{URL('/')}}/admin/studentleavereports/datatables/",   
                data: function ( d ) {
                    var student_id  = $('#student_id').val();
                    var class_id  = $('#class_id').val();
                    var section_id  = $('#section_id').val();
                    var date  = $('#date').val();
                //    var maxDateFilter  = $('#datepicker_to').val();
                console.log('class_id',class_id);
                console.log('section_id',section_id);
                console.log('student_id',student_id);

                    $.extend(d, {
                    student_id:student_id,
                    class_id:class_id,
                    section_id: section_id,
                    date:date,
                    });

                }
            },
            columns: [
               
            {
                    data: 'is_student_name',
                    name: 'attendance_approval.is_student_name'
                },
                {
                    data: 'is_class_name',
                    name: 'attendance_approval.is_class_name'

                },
                {
                    data: 'is_section_name',
                    name: 'attendance_approval.is_section_name'
                },
                {
                    data: 'mobile',
                    name: 'users.mobile'
                },
                {
                    data: 'mobile1',
                    name: 'users.mobile1'
                },
                {
                    data: 'is_fn_status',
                    name: 'attendance_approval.fn_status'
                },
                {
                    data: 'is_an_status',
                    name: 'attendance_approval.an_status'
                }
            ],
            dom: 'Blfrtip',
            buttons: [],
            "order": [],
            "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }

            ],
            dom: 'Blfrtip',
            buttons: [
                {

                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-warning btn-md ml-3',
                    action: function (e, dt, node, config) {
                        $.ajax({
                            "url":"{{URL('/')}}/admin/admin_studentleavereports_excel/",   
                            "data": dt.ajax.params(),
                            "type": 'get',
                            "success": function(res, status, xhr) {
                                var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                var csvURL = window.URL.createObjectURL(csvData);
                                var tempLink = document.createElement('a');
                                tempLink.href = csvURL;
                                tempLink.setAttribute('download', 'Student_Leave.xls');
                                tempLink.click();
                            }
                        });
                    }
                },

            ],

        });


        $('.tblcountries tfoot th').each(function(index) {
            if ( index != 0 && index != 1 && index != 2 && index != 3 && index != 4  && index != 5 && index != 6 ) {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
            }
        });

        // $('.tblcategory tfoot th').each(function(index) {

        //     if (index != 6 && index != 7 && index != 12) {
        //         var title = $(this).text();
        //         $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        //     }
        // });

        $('#class_id').on('change', function() {
            table.draw();
        });
        $('#student_id').on('change', function() {
            table.draw();
        });
        $('#section_id').on('change', function() {
            table.draw();
        });

        $("#date").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        }).change(function() {
            tabledraw();
        }).keyup(function() {
            tabledraw();
        });

     

        function tabledraw() {
           
            table.draw();

        }


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
        $('#addtopics').on('click', function() {
            $('#style-form .course_id').trigger('change');
        });


    });

    function loadSection(val) {

var class_id = val;
$("#section_id").html('');
$.ajax({
url: "{{ url('admin/fetch-section') }}",
type: "POST",
data: {
    class_id: class_id,
    _token: '{{ csrf_token() }}'
},
dataType: 'json',
success: function(res) {
    $('#section_id').html(
        '<option value="">-- Select Section --</option>');
   
    $.each(res.section, function(key, value) {
        // alert(value.id)
        $("#section_id").append('<option value="' + value
            .id + '">' + value.section_name + '</option>');
    });
}
});
}

function fetch_student(val,class_id) {
var section_id = val;
$("#student_id").html('');
$.ajax({
url: "{{ url('admin/fetch-student') }}",
type: "POST",
data: {
    class_id: class_id,
    section_id:section_id,
    _token: '{{ csrf_token() }}'
},
dataType: 'json',
success: function(res) {
    $('#student_id').html(
        '<option value="">-- Select Student --</option>');

    $.each(res.student, function(key, value) {
         $("#student_id").append('<option value="' + value
            .id + '">' + value.name + '</option>');
    });
}
});
}




</script>

@endsection