@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('pending_fee_report', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open') 
@section('content')


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title"><!-- Fee Pending Report  --> </h4>
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
                                <label class="form-label">Pending Amount</label>
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
                                                  <th>Total Fees</th> 
                                                  <th>Concession Fees</th>
                                                  <th>Paid Fees</th>
                                                  <th>Balance Fees</th> 
                                                  <th>Deleted Fees</th>  
                                                  <th class="no-sort">Action</th>   
                                            </tr>
                                        </thead>
                                        <!-- <tfoot>
                                            <tr>
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
                    "url":"{{URL('/')}}/admin/pending_fee_report/collection/datatables/",  
                    data: function ( d ) { 
                        var batch = $('#batch').val();
                        var class_id = $('#class_id').val();
                        var section_id = $('#section_id').val();
                        var student_id  = $('#student_id').val(); 
                        $.extend(d, {  
                            batch:batch, class_id:class_id,  section_id:section_id,  student_id:student_id 
                        });
                    }
                },
                columns: [
                    { data: 'academic_year', name: 'student_class_mappings.academic_year'},
                    { data: 'class_name', name: 'classes.class_name'},
                    { data: 'section_name', name:'sections.section_name'}, 
                    { data: 'scholar_name', name: 'users.name'},
                    { data: 'admission_no', name: 'users.admission_no'},
                    { data: 'total_fees', name: 'student_class_mappings.total_fees'},
                    { data: 'concession_fees', name: 'student_class_mappings.concession_fees'},
                    { data: 'paid_fees', name: 'student_class_mappings.paid_fees'},
                    { data: 'balance_fees', name: 'student_class_mappings.balance_fees'},
                    { data: 'deleted_fees', name: 'student_class_mappings.deleted_fees'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.user_id;
                            var url = "{{url('/admin/fee_collection')}}?student_id="+tid+"&batch="+data.academic_year;
                            return '<a href="'+url+'" title="View Fees" target="_blank"><i class="fas fa-eye"></i></a>';
                            
                        },

                    },

                ],
                "order" : [[8,'desc']],
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
                                "url":"{{URL('/')}}/admin/pending_fee_report/collection_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Pending_Fee_Report.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ], 
            });

            table.on( 'xhr', function () {
                var json = table.ajax.json(); 
                 $("#collected_amount").html(json.overall_fee_pending); 
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
                @if(!empty($batch))
                $('#batch').val('{{$batch}}');
                @endif
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
