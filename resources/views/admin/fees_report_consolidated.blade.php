@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fee_report', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open') 
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style type="text/css">
    .input-container input {
        border: none;
        box-sizing: border-box;
        outline: 0;
        padding: .75rem;
        position: relative;
        width: 100%;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        background: transparent;
        bottom: 0;
        color: transparent;
        cursor: pointer;
        height: auto;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        width: auto;
    }
</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title"><!-- Fee Collection Report   --></h4>
                        <div class="row">  

                            <div class=" col-md-3"> 
                                <div class="form-line">
                                    <select class="form-control" name="fee_filter" id="fee_filter" >
                                        <!-- <option value="">Select Fee Filter </option>  -->
                                        <option value="ACCOUNT">Account</option> 
                                        <option value="CATEGORY">Category</option> 
                                        <option value="ITEM">Item</option> 
                                        <option value="TERM">Term</option> 
                                        <option value="PAYMENTMODE">Payment Mode</option> 
                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3"> 
                                <div class="form-line">
                                    <select class="form-control" name="fee_type" id="fee_type" >
                                            <option value="">Select Fee Type </option> 
                                            <option value="COLLECTED">Collected</option> 
                                            <option value="CONCESSION">Concession</option> 
                                            <option value="WAIVER">Waiver</option> 
                                            <!-- <option value="OVERDUE">Over Due</option> 
                                            <option value="PENDING">Pending</option>  -->
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div name="fee_dates" id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>

                            <div class="form-group col-md-2 " > 
                                <label class=" form-control" type="text" id="collected_amount" readonly></label>
                            </div>

                            <div class="form-group col-md-3 " > 
                                <button class="btn btn-danger mt-3"  id="apply_style">Apply Filter</button>
                            </div> 

                            <div class="form-group col-md-3 " > 
                                <button class="btn btn-danger mt-3"  id="clear_style">Clear Filter</button>
                            </div> 

                            <div class="col-md-3 d-none">
                                <label class="from-label">Batch</label>
                                <select class="form-control batch" name="batch" id="batch">
                                    <option value="">Select Batch</option>
                                    @if(!empty($get_batches))
                                        @foreach($get_batches as $batches)
                                            @php($selected = '') 
                                            @if($batch == $batches['academic_year']) @php($selected = 'selected') @endif
                                            <option value="{{$batches['academic_year']}}" {{$selected}}>{{$batches['display_academic_year']}}</option>
                                        @endforeach
                                    @endif 
                                </select>
                            </div>    

                            <div class="col-md-3 d-none">
                                <label class="from-label">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadSection(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($get_classes))
                                        @foreach ($get_classes as $class)
                                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class=" col-md-3 d-none">
                                <label class="form-label" >Section</label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_id" required onchange="loadstudents(this.value,class_id.value)">

                                    </select>
                                </div>
                            </div>
                            
                            <div class=" col-md-3 d-none">
                                <label class="form-label">Students </label>
                                <div class="form-line">
                                    <select class="form-control" name="student_id" id="student_id" >

                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3 d-none">
                                <label class="form-label">Fee Item </label>
                                <div class="form-line">
                                    <select class="form-control" name="fee_item_id" id="fee_item_id" >
                                        <option value="">Select Fee Item </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-3  d-none" >
                                <label class="form-label">Collection From</label>
                                <input class="date_range_filter date form-control" type="date" id="datepicker_from"  />
                            </div>
                            <div class="form-group col-md-3  d-none" >
                                <label class="form-label">Collection To</label>
                                <input class="date_range_filter date form-control" type="date" id="datepicker_to"  />
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
                                                  <th>Name</th>  
                                                  <th class="no-sort">Type</th>
                                                  <th>Amount</th> 
                                            </tr>
                                        </thead>
                                        <!-- <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th> 
                                            </tr>
                                        </tfoot> -->
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    

    <script>
        function resetAllValues() {
            $('.card-header').find('input').val('');
            $('.card-header').find('select').val('');
        }

        function loadstudents(section_id,class_id) { 
            $("#student_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-student') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    section_id: section_id,
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

        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });
        $(function() {


            var start = moment().subtract(0, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

            $('#fee_category').on('change', function(){
                var selected_category = $(this).val(); 
                $('#fee_item_id').empty();
        
                $.ajax({
                    type: 'get',
                    url: " {{ URL::to('/admin/filter_fee_item') }}",
                    dataType: 'json',
                    data: {'selected_category': selected_category},
                    success: function(data){
                        $('#fee_item_id').append('<option value="" readonly required>Select Fee Item</option>');
                        if (data.filter_data.length === 0) {
                            $('#fee_item_id').append('<option value="" readonly required>No items found</option>');
                        } else {
                     //   $('#fee_filters').html('<option value="" readonly required>Payment Against</option>');
                        $.each(data.filter_data, function(key, value) {
                            $("#fee_item_id").append('<option value="' + value.id + '"> ' + value.item_name + '</option>');
                        });

                    }
                    }
                });
            });

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/fees_report/datatables/",  
                    data: function ( d ) { 
                        /*var batch = $('#batch').val();
                        var class_id = $('#class_id').val();
                        var section_id = $('#section_id').val();
                        var student_id  = $('#student_id').val();*/
                        var fee_filter  = $('#fee_filter').val();
                        var fee_type = $('#fee_type').val();
                        var dateFilter  = $('#reportrange span').html(); 
                        $.extend(d, {  
                            fee_filter:fee_filter, fee_type:fee_type,  dateFilter:dateFilter
                        });
                    }
                },
                columns: [
                    { data: 'account_name'},
                    { data: 'fee_type'},
                    { data: 'total_amount'}, 

                ],
                "order" : [[0,'asc']],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }],
                dom: 'Blfrtip',
                buttons: [
                    {

                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-warning btn-md ml-3',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                "url":"{{URL('/')}}/admin/fee_report/collection_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Fee_Report.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],
                "dataSrc": function (json){
                    if(json.overall_fee_collected){
                        $("#collected_amount").html(json.total_amount);
                    }
                    return json.data;
                }

            });

            table.on( 'xhr', function () {
                var json = table.ajax.json(); 
                 $("#collected_amount").html(json.total_amount); 
            } );     

            $('#fee_filter,#fee_type,#reportrange').on('change', function() {
                table.draw(); ;//table.draw();
            });


            /*$('.tblcountries tfoot th').each(function(index) {
                if (index != 0 && index != 5 && index != 20) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });

            $('#batch').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#class_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#section_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#student_id').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#fee_category').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $('#fee_item_id').on('change', function() {
                table.draw(); ;//table.draw();
            }); */  

            // Apply the search
            /*table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            }); */

            $('#apply_style').on('click', function () { 
                table.draw();
            }); 

            $('#clear_style').on('click', function () {
                $('.card-header').find('input').val('');
                $('.card-header').find('select').val('');
                $('#fee_filter').val('ACCOUNT');

                var start = moment().subtract(0, 'days');
                var end = moment();
                cb(start,end);
                table.draw();
            }); 
        }); 
  
 
        
        function loadSection(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

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
                         $("#section_id").append('<option value="' + value
                            .id + '">' + value.section_name + '</option>');
                    });
                }
            });
        }

    </script>
@endsection
