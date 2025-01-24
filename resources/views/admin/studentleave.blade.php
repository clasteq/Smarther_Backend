@extends('layouts.admin_master')
@section('attendance_settings', 'active')
@section('master_studentleave', 'active')
@section('menuopenatt', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Students Leave Report', 'active' => 'active']];
?>
@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
    <!-- Exportable Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 style="font-size:20px;" class="card-title">Students
                        Leave
                    </h4>
                   <br><br>
                    <div class="row">
                        <div class="row col-md-12">
                            <div class="form-group col-md-3 " >
                                <label class="form-label">From</label>
                                <input class="date_range_filter date form-control" type="text" id="datepicker_from"  />
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">To</label>
                                <input class="date_range_filter date form-control" type="text" id="datepicker_to"  />
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
                </div>
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                            <div class="table-responsicve">
                                <table class="table table-striped table-bordered tblcountries">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Student name</th>
                                            <th>Class Name</th>
                                            <th>Section Name</th>
                                            <th>Leave date</th>
                                            <th>Leave End Date</th>
                                            <th>Leave Starttime</th>
                                            <th>Leave Endtime</th>
                                            <th>Leave Type</th>
                                            <th>Leave Reason</th>
                                            <th>Leave Attachment</th>
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
        $(function() {

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/studentsleavelist/datatables/",   
                    data: function ( d ) {
                        var student_id  = $('#student_id').val();
                        var class_id  = $('#class_id').val();
                        var section_id  = $('#section_id').val();
                        var minDateFilter  = $('#datepicker_from').val();
                       var maxDateFilter  = $('#datepicker_to').val();
                        $.extend(d, {
                        student_id:student_id,
                        class_id:class_id,
                        section_id: section_id,
                        minDateFilter:minDateFilter,
                        maxDateFilter:maxDateFilter});

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
       
                            var tid = data.id;
                            var url = "{{URL('/')}}/admin/edit_leave?id="+tid;
                            return '<a href="'+url+'"  title="Update Leave  "><i class="fas fa-edit"></i></a>';
                        },
                    },
                    {
                        data: 'is_student_name',
                        name: 'users.name'
                    },
                    {
                        data: 'is_class_name',
                        name: 'classes.class_name'

                    },
                    {
                        data: 'is_section_name',
                        name: 'sections.section_name'
                    },
                    {
                        data: 'leave_date',
                        name: 'leaves.leave_date'
                    },
                    {
                        data: 'leave_end_date',
                        name: 'leaves.leave_end_date'
                    },
                    {
                        data: 'leave_starttime',
                        name: 'leaves.leave_starttime'
                    },
                    {
                        data: 'leave_endtime',
                        name: 'leaves.leave_endtime'
                    },
                    {
                        data: 'leave_type',
                        name: 'leaves.leave_type'
                    },
                    {
                        data: 'leave_reason',
                        name: 'leaves.leave_reason'
                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var leave_attachment = data.leave_attachment;
                            var is_leave_attachment = data.is_leave_attachment;
                            if (leave_attachment != null && leave_attachment != '') {
                                return '<a href="' + is_leave_attachment +
                                    '" target="_blank" title="Leave Attachement" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'status',
                        name : 'leaves.status'
                    },

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
                                "url":"{{URL('/')}}/admin/admin_studentleave_excel/",   
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
                if (index != 0 && index != 1 && index != 2 && index != 3 && index != 10) {
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

            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            $("#datepicker_to").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            function tabledraw() {
                var minDateFilter  = $('#datepicker_from').val();
                var maxDateFilter  = $('#datepicker_to').val();
                if(new Date(maxDateFilter) < new Date(minDateFilter))
                {
                    alert('To Date must be greater than From Date');
                    return false;
                }
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
