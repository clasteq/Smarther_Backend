 
 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">
    <style>

    .abs {
        top: -10px !important;
        left: 12px !important;

    }

    .collectionprofile img {
        height: auto;
        width: 2.1rem;
    }

    .border-box {
        border: 1px solid #ccc;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.3);
        padding: 2px;
        border-radius: 5px;
        margin-left: px;
        width: 100%;
    }

    .fees {}

    .fees img {
        height: auto;
        width: 2.1rem;
    }

    .schoolproducts {
        margin-left: 20px;

    }

    .schoolproducts p {
        background-color: rgb(212, 6, 6);
        padding: 8px;
        border-radius: 50px;
        color: white;
    }

    .feescollection {
        display: flex;
        justify-content: flex-end;
    }

    .feescollection i {
        font-size: 20px;
    }

    .feesborder {
        border: 1px solid #ccc;

        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.3);
        padding: 10px;
        border-radius: 5px;
    }

    .termfees span {
        color: rgb(165, 165, 165)
    }

    .totalcollection i {
        background-color: rgb(179, 215, 231);
        padding: 10px;
        border-radius: 60%;
    }

    .concen i {
        background-color: rgb(243, 102, 77);
        padding: 10px;
        border-radius: 60%;
    }

    .paid i {
        background-color: rgb(112, 233, 88);
        padding: 10px;
        border-radius: 60%;
    }

    .balance i {
        background-color: rgb(233, 159, 23);
        padding: 10px;
        border-radius: 60%;
    }

    .radiocheck {
        padding-left: 1.25rem !important;
    }

    .name_filter{
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        z-index: 1000;
        width:100%;
    }

    .modal-full {
    min-width: 95%;
    margin: 10;
    }

    .modal-full .modal-body {
        overflow-y: auto;
    }
    </style>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Fee Receipts
                        </h4>
                    </div>

                        <div id="show_data">

                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mt-3 mb-3 d-flex">
                                            <div class="collectionprofile mt-2">
                                                <img src="{{ asset('/public/image/avatar5.png') }}" class="img-circle elevation-2"
                                                    alt="User Image">
                                            </div>
                                            <div class="colllist" style="margin-left: 10px;">
                                                <span style="font-size:20px;" id="student_name"> {{$studentdetails['is_student_name']}}  </span><br>
                                                <span style="color: rgb(167, 166, 166)"><span id="class_name">{{$studentdetails['is_class_name']}} </span>-<span id="section_name">{{$studentdetails['is_section_name']}} </span>, Admission Number - <span id="admission_no">{{$studentdetails['admission_no']}} </span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <section>

                            <input type="hidden" name="student_id" id="student_id" value="{{$student_id}}">
                            <input type="hidden" name="batch" id="batch" value="{{$batch}}">

                            <div class="container">
                                <div class="col-md-12 mt-3 mb-3" id=" ">

                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                        <div class="table-responsicve">
                                            <table class="table table-striped table-bordered tblcountries example1">
                                              <thead>
                                                <tr>
                                                  <th>Receipt#</th>
                                                  <th>Receipt Date</th>
                                                  <th>Amount</th>
                                                  <th>Receipt Name</th>
                                                  <th>Account</th>
                                                  <th>Cancel</th>
                                                  <th>View</th>
                                                  <th>Created By</th>
                                                  <th>Created Date</th>
                                                </tr>
                                              </thead>

                                              <tfoot>
                                                  <th></th> <th></th> <th></th>
                                                  <th></th> <th></th> <th></th>
                                                  <th></th> <th></th><th></th>
                                              </tfoot>        
                                              <tbody>
                                                    @if(!empty($fee_receipts))
                                                        @foreach($fee_receipts as $receipt)
                                                            <tr>
                                                                <td>{{$receipt->receipt_no}}</td>
                                                                <td>{{$receipt->receipt_date}}</td>
                                                                <td>{{$receipt->amount}}</td>
                                                                <td>{{$receipt->is_receipthead_name}}</td>
                                                                <td>{{$receipt->is_account_name}}</td>
                                                                <td><a href="#" onclick="loadSection('{{$receipt->id}}')" title="Edit Receipt"><i class="fas fa-times text-red"></i></a></td>
                                                                <td><a target="_blank" href="{{$receipt->is_pdf}}" title="View Receipt"><i class="fas fa-file-pdf fa-2x text-red"></i></a></td>
                                                                <td>{{$receipt->is_created_name}}</td>
                                                                <td>{{$receipt->formatted_created_at}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                              </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </section>

                        </div>
                </div>
            </div>

        </div>

    </section>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Cancel Receipts
                        </h4>
                    </div>

                        <div id="show_data">


                        <section>
                            <div class="container">
                                <div class="col-md-12 mt-3 mb-3" id=" ">

                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                        <div class="table-responsicve">
                                            <table class="table table-striped table-bordered tblcountries example2">
                                              <thead>
                                                <tr>
                                                  <th>Receipt#</th>
                                                  <th>Receipt Date</th>
                                                  <th>Amount</th>
                                                  <th>Created By</th>
                                                  <th>Created Date</th>
                                                  <th>Cancelled By</th>
                                                  <th>Cancelled Date</th>

                                                </tr>
                                              </thead>

                                              <tfoot>
                                                  <th></th> <th></th> <th></th>
                                                  <th></th> <th></th> <th></th>
                                                  <th></th>
                                              </tfoot>

                                              <tbody>              
                                                    @if(!empty($cancelled_fee_receipts))
                                                        @foreach($cancelled_fee_receipts as $receipt)
                                                            <tr>
                                                                <td>{{$receipt->receipt_no}}</td>
                                                                <td>{{$receipt->receipt_date}}</td>
                                                                <td>{{$receipt->amount}}</td>
                                                                <td>{{$receipt->is_created_name}}</td>
                                                                <td>{{$receipt->formatted_created_at}}</td> 
                                                                <td>{{$receipt->is_canceled_name}}</td>
                                                                <td>{{$receipt->formatted_cancel_date}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                              </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </section>

                        </div>
                </div>
            </div>

        </div>

    </section>
    <!-- <div class="modal fade" id="smallModal-2feerecep" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Receipt Cancel</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/fee_collection/fee_cancel') }}" method="post"  class="post_cancel_receipt">
                    {{ csrf_field() }}
                    <input type="hidden" name="receipt_id" id="receipt_id">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Receipt Number</strong><br>
                                     <span id="receipt_no"></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Receipt Date</strong><br>
                                     <span id="receipt_date"></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Receipt Amount</strong><br>
                                     <span id="receipt_amount"></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Student Name</strong><br>
                                     <span >{{$studentdetails['is_student_name']}}</span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Class & Section</strong><br>
                                     <span >{{$studentdetails['is_class_name']}}-{{$studentdetails['is_section_name']}}</span></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Admission No</strong><br>
                                     <span>{{$studentdetails['admission_no']}}</span></p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class=" d-flex justify-content-center mt-3 mb-3">
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <label
                                            class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Cancel Date</label>
                                            <div class="form-group">
                                                <input type="date" name="cancel_date" class="form-control datetime-picker" placeholder="Select Date" required style="background-color: white;height:50px;">
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Cancel Type</label>
                                        <div class="form-group">
                                            <select class="form-control" name="cancel_type" required style="height:50px;">
                                            <option value="">Select Type</option>
                                            <option value="1">Non Refundable</option>
                                            <option value="2">Refundable</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Remarks</label>
                                            <div class="form-group">
                                                <input type="text" name="remarks" class="form-control" placeholder="Remarks" style="height:50px;" required>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Cancel Reason</label>
                                        <div class="form-group">
                                            <select class="form-control"  name="cancel_reason" required style="height:50px;">
                                                <option value="">Select Reason</option>
                                                @if(!empty($cancel_reason))
                                                    @foreach($cancel_reason as $reason)
                                                        <option value="{{$reason['id']}}">{{$reason['cancel_reason']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
                        <button type="submit" class="btn btn-primary" id="edit_style">SAVE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  -->

<script>

    $(function() {

        /*function initializeDatepickers() {
            flatpickr('.datetime-picker', {
                enableTime: false,
                dateFormat: "Y-m-d",
                defaultDate: new Date(),
                maxDate: new Date()
            });
        }

        // Initial call to initialize datepickers on page load
        initializeDatepickers();*/


    /*    var table = $('.example1').DataTable({

                processing: false,
                serverSide: true,
                responsive: true,
                "lengthChange": false,
                "pageLength": 3,  // Limit to 5 rows
                "ajax": {
                "url":"{{URL('/')}}/admin/feereceipts/datatables/",
                    data: function ( d ) {
                        var batch = $('#batch').val();
                        var student_id = $('#student_id').val();
                        $.extend(d, {batch:batch, student_id:student_id});

                    }
                },
                columns: 
                [{ data: 'receipt_no', name: 'receipt_no'},
                { data: 'receipt_date', name: 'receipt_date'},
                { data: 'amount', name: 'amount'},
                { data: 'is_receipthead_name', name: 'is_receipthead_name'},
                { data: 'is_account_name', name: 'is_account_name'},
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        var tid = data.id;
                        // Define the onclick method to open the modal directly
                        return '<a href="#" onclick="loadSection(' + tid + ')" title="Edit Section"><i class="fas fa-times text-red"></i></a>';
                    }
                },
                {
                    data: null,
                    "render": function(data, type, row, meta) {
                        var pdf = data.receipt_pdf;
                        if(pdf != '' && pdf != null) {
                            var is_pdf = data.is_pdf;
                            return '<a target="_blank" href="'+is_pdf+'" title="View Receipt"><i class="fas fa-file-pdf fa-2x text-red"></i></a>';
                        }   else {
                            return '';
                        }
                        
                    },

                },
                { data: 'is_created_name', name: 'is_created_name'},
                { data: 'formatted_created_at', name: 'formatted_created_at'},



            ],
            "order": [],
            "columnDefs": [

            {
                "targets": 'no-sort',
                "orderable": false,
            }
            ],
            //  dom: 'Bfrtip',
            //  "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

        });
        $('#example1 tfoot').insertAfter('#example1 thead');
        $('#example1 tfoot th').each( function () {
            var title = $(this).text();

            if(($(this).index() != 0)&& ($(this).index() != 8)&& ($(this).index() != 9)){
                $(this).html( '<input class="btn" type="text" style="width:100%;border-color:#6c757d; cursor: auto;" placeholder="Search '+title+'" />' );

            }
        } );

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




        var table = $('.example2').DataTable({

            processing: false,
            serverSide: true,
            responsive: true,
            "lengthChange": false,
            "pageLength": 3,  // Limit to 5 rows
            "ajax": {
            "url":"{{URL('/')}}/admin/cancelfeereceipts/datatables/",
                data: function ( d ) {
                    var batch = $('#batch').val();
                    var student_id = $('#student_id').val();
                    $.extend(d, {batch:batch, student_id:student_id});

                }
            },
            columns: [  
            { data: 'receipt_no', name: 'receipt_no'},
            { data: 'receipt_date', name: 'receipt_date'},
            { data: 'amount', name: 'amount'},

            { data: 'is_created_name', name: 'is_created_name'},
            { data: 'formatted_created_at', name: 'formatted_created_at'},

            { data: 'is_canceled_name', name: 'is_canceled_name'},
            { data: 'formatted_cancel_date', name: 'formatted_cancel_date'},



            ],
            "order": [],
            "columnDefs": [

            {
                "targets": 'no-sort',
                "orderable": false,
            }
            ],
                //  dom: 'Bfrtip',
                //  "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

        });
        $('#example1 tfoot').insertAfter('#example1 thead');
        $('#example1 tfoot th').each( function () {
            var title = $(this).text();

            if(($(this).index() != 0)&& ($(this).index() != 8)&& ($(this).index() != 9)){
                $(this).html( '<input class="btn" type="text" style="width:100%;border-color:#6c757d; cursor: auto;" placeholder="Search '+title+'" />' );

            }
        } );

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
    */





    });


    /*function loadSection(id) {
         //   $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('/admin/fee_collection/cancel_fee_receipt') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    code: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {

                $('#id').val(response.data.id);

                $('#receipt_no').text(response.data.receipt_no);

                $('#receipt_date').text(response.data.receipt_date);
                $('#receipt_amount').text(response.data.amount);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }*/



        $('#edit_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            $('.example1').DataTable().ajax.reload();
                            $('.example2').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
        });



</script> 