@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fee_receipts_cancelled', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open') 
@section('content')

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

                            <div class="col-md-3">
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

                            <div class="col-md-3">
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

                            <div class=" col-md-3">
                                <label class="form-label" >Section</label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_id" required onchange="loadstudents(this.value,class_id.value)">

                                    </select>
                                </div>
                            </div>
                            
                            <div class=" col-md-3">
                                <label class="form-label">Students </label>
                                <div class="form-line">
                                    <select class="form-control" name="student_id" id="student_id" >

                                    </select>
                                </div>
                            </div>
 
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Collection From</label>
                                <input class="date_range_filter date form-control" type="date" id="datepicker_from"  />
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Collection To</label>
                                <input class="date_range_filter date form-control" type="date" id="datepicker_to"  />
                            </div> 
                            
                            <div class="form-group col-md-3 d-none" >
                                <label class="form-label">Collected Amount</label>
                                <label class=" form-control" type="text" id="collected_amount"></label>
                            </div>

                            <div class="form-group col-md-3 " >
                                <label class="form-label"></label> 
                                <button class="btn btn-danger mt-3"  id="clear_style">Clear Filter</button>
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
                                              <th>Batch</th>  
                                              <th>Class</th>
                                              <th>Section</th>
                                              <th>Scholar</th>
                                              <th>Admin No</th>
                                              <th>Receipt#</th>
                                              <th>Receipt Date</th>
                                              <th>Amount</th>
                                              <th>Cancel Date</th>
                                              <th class="no-sort">Cancel Type</th>
                                              <th>Cancel Reason</th>
                                              <th>Cancel Remark</th>
                                              <th>Cancelled By</th> 
                                              <th>Receipt Name</th>
                                              <th>Account</th> 
                                              <th class="no-sort">Payment Mode</th>
                                              <th class="no-sort">View</th>
                                              <th>Created By</th> 
                                            </tr>
                                        </thead>
                                        <!-- <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th>
                                                <th></th><th></th><th></th>
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

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/fees_receipts_cancelled_report/datatables/",  
                    data: function ( d ) { 
                        var batch = $('#batch').val();
                        var class_id = $('#class_id').val();
                        var section_id = $('#section_id').val();
                        var student_id  = $('#student_id').val();
                        var fee_category  = $('#fee_category').val();
                        var fee_item_id = $('#fee_item_id').val();
                        var minDateFilter  = $('#datepicker_from').val();
                        var maxDateFilter  = $('#datepicker_to').val();
                        var fee_type = $('#fee_type').val();
                        $.extend(d, {  
                            batch:batch, class_id:class_id,  section_id:section_id,  student_id:student_id, 
                            fee_category:fee_category,   fee_item_id:fee_item_id, minDateFilter:minDateFilter, 
                            maxDateFilter:maxDateFilter, fee_type:fee_type
                        });
                    }
                },
                columns: [
                    { data: 'batch', name: 'batch'},
                    { data: 'class_name', name: 'class_name'},
                    { data: 'section_name', name:'section_name'},
                    { data: 'name', name: 'scholar.name'},
                    { data: 'admission_no', name: 'scholar.admission_no'},
                    { data: 'receipt_no', name: 'receipt_no'},
                    { data: 'receipt_date', name: 'receipt_date'},
                    { data: 'amount', name:'amount'},
                    { data: 'cancel_date', name:'cancel_date'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            var cancel_type = data.cancel_type;
                            if(cancel_type == 1) { 
                                return 'Non Refundable'; 
                            }  else if(cancel_type == 2) { 
                                return 'Refundable';
                            } 
                        }, 
                    },    
                    { data: 'fee_cancel_reason', name:'fee_cancel_reasons.cancel_reason'},
                    { data: 'cancel_remark', name:'cancel_remark'},
                    { data: 'cancellor_name', name:'cancellor.name'},
                    { data: 'receipt_head_name', name: 'receipt_heads.name'},
                    { data: 'account_name', name: 'accounts.account_name'},
                    { data: 'payment_name', name: 'payment_modes.name'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            var is_pdf = data.is_pdf;
                            if(is_pdf != '' && is_pdf != null) { 
                                return '<a target="_blank" href="'+is_pdf+'" title="View Receipt"><i class="fas fa-file-pdf fa-2x text-red"></i></a>'; 
                            }  else {
                                return '';
                            } 
                        }, 
                    },   
                    { data: 'creator_name', name:'creator.name'},  

                ],
                "order" : [[6,'desc']],
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
                                "url":"{{URL('/')}}/admin/fees_receipts_cancelled_report_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Fee_Receipts_Cancelled_Report.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],
                "dataSrc": function (json){
                    if(json.overall_fee_collected){
                        $("#collected_amount").html(json.overall_fee_collected);
                    }
                    return json.data;
                }

            });

            table.on( 'xhr', function () {
                var json = table.ajax.json(); 
                 $("#collected_amount").html(json.overall_fee_collected); 
            } );


            /*$('.tblcountries tfoot th').each(function(index) {
                if (index != 0 && index != 5 && index != 20) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });*/ 

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
            });

            $('#fee_type').on('change', function() {
                table.draw(); ;//table.draw();
            });

            $("#datepicker_from")/*.datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            })*/.change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            $("#datepicker_to")/*.datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            })*/.change(function() {
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

            $('#clear_style').on('click', function () {
                $('.card-header').find('input').val('');
                $('.card-header').find('select').val('');
                $('#batch').val({{$batch}});
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
