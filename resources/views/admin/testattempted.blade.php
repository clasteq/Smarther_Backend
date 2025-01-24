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
                    <h4 style="font-size:20px;" class="card-title">Students Test Attepts</h4>
                    <br><br>
                    <div class="row">
                        <div class="row col-md-12"> 
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Class: </label>
                                <span type="text">{{$tests->class_name}}</span>
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Subject: </label>
                                <span type="text" >{{$tests->subject_name}}</span>
                            </div> 
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Term: </label>
                                <span type="text">{{$tests->term_name}}</span>
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Test: </label>
                                <span type="text" >{{$tests->test_name}}</span>
                            </div>  
                        </div>

                    </div>
                    <div class="row">
                        <input type="hidden" name="test_id" id="test_id" value="{{$test_id}}">
                        <div class="row col-md-12">
                            <div class="form-group col-md-3 " >
                                <label class="form-label">From</label>
                                <input class="date_range_filter date form-control" type="text" id="datepicker_from"  />
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">To</label>
                                <input class="date_range_filter date form-control" type="text" id="datepicker_to"  />
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
                                    <th>Student</th>
                                    <th>Admission No</th> 
                                    <th>Father Mobile</th> 
                                    <th>Alternate Mobile</th> 
                                    <th>Attempted Count</th>
                                 
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
@endsection

@section('scripts')

    <script>
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("admin_testattempted.data")}}',
                    data: function ( d ) {
                       var minDateFilter  = $('#datepicker_from').val();
                       var maxDateFilter  = $('#datepicker_to').val();
                        var test_id  = $('#test_id').val(); 
                        $.extend(d, {
                        minDateFilter:minDateFilter,
                        maxDateFilter:maxDateFilter,
                        test_id:test_id,
                        });

                    }
                },
                columns: [
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.test_id;
                            var uid = data.user_id;
                            var vurl = "{{URL('/')}}/admin/view/studentstestattempts/"+uid+"/"+tid;  
                           return  '<a href="'+vurl+'" target="_blank"  title="View Test"><i class="fas fa-eye"></i></a>';

                        },

                    }, 
                    { data: 'student_name',  name: 'users.name'},
                    { data: 'admission_no',  name: 'admission_no'},
                    { data: 'mobile',  name: 'mobile'},
                    { data: 'mobile1',  name: 'mobile1'},
                    { data: 'test_attempted',  name: 'test_attempted'}, 
                  
                ],
                "order":[],
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
                                "url": '{{route("studenttestlist_excel.data")}}',
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
