@extends('layouts.admin_master')
@section('report_settings', 'active')
@section('master_studentstest', 'active')
@section('menuopenr', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Students Test Report', 'active'=>'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Students Test List
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
            <label class="form-label">Class</label>
            <select class="form-control" name="class_id" onchange="fetch_student(this.value)" id="class_id">
                <option value="" >All</option>
                @if(!@empty($class))
                @foreach ($class as $classes)
                <option value={{$classes['id']}} >{{$classes['class_name']}}</option>
                @endforeach
                @endif
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
                                    <th>Test Date</th>
                                  <th>Student</th>
                                  <th>Admission No</th>
                                  <th>Term</th>
                                  <th>Class Name</th>
                                  <th>Subject</th>
                                  <th>Test</th>
                                 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th><th></th>
                                      <th></th><th></th><th></th>
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
                    "url":"{{URL('/')}}/admin/studentstestlist/datatables/",   
                    data: function ( d ) {
                       var minDateFilter  = $('#datepicker_from').val();
                       var maxDateFilter  = $('#datepicker_to').val();
                        var student_id  = $('#student_id').val();
                        var class_id  = $('#class_id').val();
                        $.extend(d, {
                        minDateFilter:minDateFilter,
                        maxDateFilter:maxDateFilter,
                        student_id:student_id,
                        class_id:class_id,
                        });

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            var vurl = "{{URL('/')}}/admin/view/studentstestlist?id="+tid;
                            var newvurl = "{{URL('/')}}/admin/edit/editstudentstestlist?id="+tid;
                           return  '<a href="'+vurl+'" targer="_blank"  title="View Test"><i class="fas fa-eye"></i></a> <a href="'+newvurl+'" targer="_blank"  title="Edit Test"><i class="fas fa-edit"></i></a>';

                        },

                    },
                    { data: 'test_date',  name: 'test_date'},
                    { data: 'student_name',  name: 'users.name'},
                    { data: 'admission_no',  name: 'admission_no'},
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'class_name',  name: 'class_name'},
                    { data: 'subject_name',  name: 'subject_name'},
                    { data: 'test_name',  name: 'test_name'},
                  
                ],
                "order":[[1, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                dom: 'Blfrtip',
                buttons: [
                    {

                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-warning btn-md ml-3',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                "url":"{{URL('/')}}/admin/studenttestlist_excel/",    
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Student Test.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 0) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#student_id').on('change', function() {
                table.draw();
            });

            $('#class_id').on('change', function() {
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

        
     
        function fetch_student(val) {
var class_id = val;
$("#student_id").html('');
$.ajax({
    url: "{{ url('admin/fetch-student') }}",
    type: "POST",
    data: {
        class_id: class_id,
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
