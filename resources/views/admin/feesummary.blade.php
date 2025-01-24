@extends('layouts.admin_master')
@section('fees_mod', 'active')
@section('fee_summary', 'active')
@section('menuopenfeemod', 'active menu-is-opening menu-open') 

<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Fee Summary', 'active' => 'active']];
?>
@section('content')
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

    </style>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Fee Summary
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
                                            <table class="table table-striped table-bordered tblcountries">
                                              <thead>
                                                <tr>
                                                  <th>Category</th>
                                                  <th>Item</th>
                                                  <th>Amount</th>
                                                  <th>Due Date</th>
                                                  <th>Paid Amount</th>
                                                  <th>Paid Date</th> 
                                                  <th>Concession Amount</th>
                                                  <th>Concession Date</th> 
                                                  <th>Collected By</th> 
                                                  <th>Collected Date</th>  
                                                </tr>
                                              </thead>

                                              <tfoot>
                                                  <th></th> <th></th> <th></th>
                                                  <th></th> <th></th> <th></th> 
                                                  <th></th> <th></th> <th></th> 
                                                  <th></th> 
                                              </tfoot>

                                              <tbody>

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


@endsection

@section('scripts') 


<script>

    $(function() {
        var table = $('.tblcountries').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            "ajax": {
                "url":"{{URL('/')}}/admin/feesummary/datatables/",
                data: function ( d ) { 
                    var batch = $('#batch').val();
                    var student_id = $('#student_id').val();
                    $.extend(d, {batch:batch, student_id:student_id});

                } 
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'item_name', name: 'item_name'},
                { data: 'amount', name:'amount'},
                { data: 'due_date', name: 'due_date'},
                { data: 'amount_paid', name: 'amount_paid'},
                { data: 'paid_date', name:'paid_date'}, 
                { data: 'concession_amount', name: 'concession_amount'},
                { data: 'concession_date', name:'concession_date'}, 
                { data: 'creator_name', name:'fees_payment_details.created_by'}, 
                { data: 'created_at', name:'fees_payment_details.created_at'}, 

            ],
            "order":[[5, 'asc']],
            dom: 'Blfrtip',
            buttons: [
                {

                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-warning btn-md ml-3',
                    action: function (e, dt, node, config) {
                        $.ajax({
                            "url":"{{URL('/')}}/admin/scholar_fee_summary_excel/",   
                            "data": dt.ajax.params(),
                            "type": 'get',
                            "success": function(res, status, xhr) {
                                var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                var csvURL = window.URL.createObjectURL(csvData);
                                var tempLink = document.createElement('a');
                                tempLink.href = csvURL;
                                tempLink.setAttribute('download', 'Scholar_Fee_Summary.xls');
                                tempLink.click();
                            }
                        });
                    }
                },

            ],
            
        });

        $('.tblcountries tfoot th').each( function (index) {
           // if(index != 0 && index != 3) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //}
        } );

        $('#status_id').on('change', function() {
            table.draw();
        });

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

  
</script>


@endsection



















