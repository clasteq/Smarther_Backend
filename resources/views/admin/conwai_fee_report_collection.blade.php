@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('conwai_fee_report', 'active')
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
                        <h4 style="font-size: 20px;" class="card-title"><!-- Fee Concession Report   --></h4>
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

                            <div class=" col-md-3">
                                <label class="form-label">Fee Category </label>
                                <div class="form-line">
                                    <select class="form-control" name="fee_category" id="fee_category" >
                                        <option value="">Select Fee Category </option>
                                        @if (!empty($get_fee_category))
                                            @foreach ($get_fee_category as $fee_category)
                                                <option value="{{$fee_category->id}}">{{$fee_category->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class=" col-md-3">
                                <label class="form-label">Fee Item </label>
                                <div class="form-line">
                                    <select class="form-control" name="fee_item_id" id="fee_item_id" >
                                        <option value="">Select Fee Item </option>
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

                            <div class="form-group col-md-3 " >
                                <label class="form-label">Concession Amount</label>
                                <label class=" form-control" type="text" id="collected_amount"></label>
                            </div>

                            <div class="form-group col-md-3 " >
                                <label class="form-label"></label> <!-- onclick="resetAllValues();" -->
                                <button class="btn btn-danger mt-3" id="clear_style">Clear Filter</button>
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
                                                  <th>Admission Number</th>
                                                  <th>Category</th>
                                                  <th>Item</th>
                                                  <th>Amount</th> 
                                                  <!-- <th>Concession / Waiver</th> -->
                                                  <th>Concession Amount</th>
                                                  <th>Concession Date</th> 
                                                  <th>Concession Remarks</th>  
                                                  <th>Collected By</th> 
                                                  <th>Collected Date</th>  
                                            </tr>
                                        </thead>
                                        <!-- <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th>
                                                <th></th><th></th><th></th>
                                                <th></th><th></th><th></th>
                                                <th></th><th></th><th></th>
                                                <th></th><th></th>  
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
                    "url":"{{URL('/')}}/admin/conwai_fee_report/collection/datatables/",  
                    data: function ( d ) { 
                        var batch = $('#batch').val();
                        var class_id = $('#class_id').val();
                        var section_id = $('#section_id').val();
                        var student_id  = $('#student_id').val();
                        var fee_category  = $('#fee_category').val();
                        var fee_item_id = $('#fee_item_id').val();
                        var minDateFilter  = $('#datepicker_from').val();
                        var maxDateFilter  = $('#datepicker_to').val();
                        $.extend(d, {  
                            batch:batch, class_id:class_id,  section_id:section_id,  student_id:student_id, 
                            fee_category:fee_category,   fee_item_id:fee_item_id, minDateFilter:minDateFilter, 
                            maxDateFilter:maxDateFilter
                        });
                    }
                },
                columns: [
                    { data: 'batch', name: 'batch'},
                    { data: 'class_name', name: 'class_name'},
                    { data: 'section_name', name:'section_name'},
                    { data: 'scholar_name', name: 'users.name'},
                    { data: 'admission_no', name: 'users.admission_no'},
                    { data: 'name', name: 'name'},
                    { data: 'item_name', name: 'item_name'},
                    { data: 'amount', name:'amount'}, 
                    /*{
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.is_concession == 1){ 
                                return 'Concession';
                            }   else {
                                return 'Waiver';
                            }
                        },

                    }, */
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.cancel_status == 0){ 
                                return '<span style="color:green;">+'+data.concession_amount +'</span>';
                            }   else {
                                return '<span style="color:red;">-'+data.concession_amount +'</span>';
                            }
                        }, name:'fees_payment_details.concession_amount'
                    }, 
                    { data: 'concession_date', name:'concession_date'},  
                    { data: 'concession_remarks', name: 'concession_remarks'},
                    { data: 'creator_name', name:'creator.name'}, 
                    { data: 'created_at', name:'fees_payment_details.created_at'}, 

                ],
                "order" : [[9,'desc']],
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
                                "url":"{{URL('/')}}/admin/conwai_fee_report/collection_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Fee_Concession_Report.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ], 
            });

            table.on( 'xhr', function () {
                var json = table.ajax.json(); 
                 $("#collected_amount").html(json.total_concession); 
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
