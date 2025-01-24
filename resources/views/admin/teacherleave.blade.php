@extends('layouts.admin_master')
@section('staattendance_settings', 'active')
@section('master_teacherleave', 'active')
@section('menuopenstaatt', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers Leave Report', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Teachers Leave
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
                             <label class="form-label">Teacher Name</label>
                             <select class="form-control" name="teacher_id" id="teacher_id">
                                 <option value="" >All</option>
                               @if(!@empty($teacher))
                               @foreach ($teacher as $teachers)
                               <option value={{$teachers['id']}} >{{$teachers['name']}}</option>
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
                                    <table class="table table-striped table-bordered tblcategory">
                                        <thead>
                                            <tr>
                                                {{-- <th>S.no</th> --}}
                                                <th>Action</th>
                                                <th>Teacher Name</th>
                                                <th>Title</th>
                                                <th>Duration</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Leave Reason</th>
                                                <th>Leave Reason File</th>
                                                <th>Leave Apply File</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                {{-- <th></th> --}}
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

            var table = $('.tblcategory').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/teacherleavelist/datatables/",  
                    data: function ( d ) {
                        var teacher_id  = $('#teacher_id').val();
                        var minDateFilter  = $('#datepicker_from').val();
                        var maxDateFilter  = $('#datepicker_to').val();
                        $.extend(d, {
                        teacher_id:teacher_id,
                        minDateFilter:minDateFilter,
                        maxDateFilter:maxDateFilter
                       });

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {
       
                            var tid = data.id;
                            var url = "{{URL('/')}}/admin/edit_teacherleave?id="+tid;
                            return '<a href="'+url+'"  title="Update Leave  "><i class="fas fa-edit"></i></a>';
                        },
                    },
                    {
                        data: 'is_teacher_name',
                        name : 'users.name'
                    },
                    {
                        data: 'title',
                        name : 'teacher_leave.title'
                    },
                    {
                        data: 'duration',
                        'name' : 'teacher_leave.duration'
                    },
                    {
                        data: 'is_from_date',
                        name : 'teacher_leave.from_date'
                    },
                    {
                        data: 'is_to_date',
                        name : 'teacher_leave.to_date'
                    },
                    {
                        data: 'description',
                        name : 'teacher_leave.description'
                    },

                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var descriptionfile = data.descriptionfile;
                            var is_descriptionfile = data.is_descriptionfile;
                            if (descriptionfile != null && descriptionfile != '') {
                                return '<a href="' + is_descriptionfile +
                                    '" target="_blank" title="Description file" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var leave_apply_file = data.leave_apply_file;
                            var is_leave_apply_file = data.is_leave_apply_file;
                            if (leave_apply_file != null && leave_apply_file != '') {
                                return '<a href="' + is_leave_apply_file +
                                    '" target="_blank" title="Leave Attachement" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data: 'status',
                        name : 'teacher_leave.status'
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
                                "url":"{{URL('/')}}/admin/admin_teacherleavelist_excel/",  
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Teacher_Leave.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],

            });

            $('.tblcategory tfoot th').each(function(index) {

                if (index != 7 && index != 8 && index != 0 ) {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });

            $('#teacher_id').on('change', function() {
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
    </script>

@endsection
