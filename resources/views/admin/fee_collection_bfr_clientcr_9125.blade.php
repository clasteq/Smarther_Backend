@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fees_collection', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')



<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
/*$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Fee Collections', 'active' => 'active']];*/
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
        margin-left: 10px !important;
        margin-top: 10px !important;
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

    .delbtn {
        position: relative;
        top: -10px;
        left: -7px;
    }
    </style>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Fee Collections
                        </h4>
                    </div>

                        <div class=" d-flex justify-content-center mt-3 mb-3">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <label
                                        class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Batch</label>
                                    <div class="form-group">
                                        <select class="form-control" id="batchSelect" name="batch" required style="height:50px;">
                                            <option value="">Select Batch</option>
                                            @if(!empty($get_batches))
                                                @foreach($get_batches as $batches)
                                                    <option value="{{$batches['academic_year']}}" @if($batch == $batches['academic_year']) selected @endif>{{$batches['display_academic_year']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Class Filter</label>
                                    <div class="form-group">
                                        <select class="form-control" id="classSelect" name="batch" required style="height:50px;">
                                        <option value="">Class Filter</option>
                                        @foreach ($get_classes as $classes )
                                            <option value="{{$classes->id}}">{{$classes->class_name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Scholar Name</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="studentName" placeholder="Scholar Name" style="height:50px;" required>
                                            <div id="suggestions" class="name_filter"></div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-none" id="show_data">

                            <div class="container">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mt-3 mb-3 d-flex">
                                            <div class="collectionprofile mt-2">
                                                <img src="{{ asset('/public/image/avatar5.png') }}" class="img-circle elevation-2"
                                                    alt="User Image">
                                            </div>
                                            <div class="colllist" style="margin-left: 10px;">
                                                <span style="font-size:20px;" id="student_name"> </span><br>
                                                <span style="color: rgb(167, 166, 166)"><span id="class_name"></span>-<span id="section_name"></span>, <br>Admission no - <span id="admission_no"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-3 border-box" style="max-width: 22%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="totalcollection mt-2">
                                                        <i class="fas fa-rupee-sign" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;" id="scholar_fees_total"> 69,750 </span><br>
                                                        <span style="color: rgb(167, 166, 166)">Total</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-box" style="max-width: 22%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="concen mt-2">
                                                        <i class="fas fa-rupee-sign" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;" id="scholar_fees_concession"> 4,550 </span><br>
                                                        <span style="color: rgb(167, 166, 166)">Concession</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-box" style="max-width: 22%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="paid mt-2">
                                                        <i class="fas fa-rupee-sign" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;" id="scholar_fees_paid"> 16,450 </span><br>
                                                        <span style="color: rgb(167, 166, 166)">Paid</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-box" style="max-width: 22%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="balance mt-2">
                                                        <i class="fas fa-rupee-sign" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;" id="scholar_fees_balance"> 48,750</span><br>
                                                        <span style="color: rgb(167, 166, 166)">Balance</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col d-flex justify-content-end align-items-center">
                                                <!-- <a href="javascript:void(0);" onclick="openfeesummary();" data-toggle="tooltip" data-placement="top" title="Fee Summary"><i class="fas fa-file-alt mx-2"></i></a> -->

                                                <a href="javascript:void(0);" data-toggle="modal" data-target="#largeModal-2" data-toggle="tooltip" data-placement="top" title="Fee Summary" onclick="loadfeesummary() ;">
                                                    <i class="fas fa-file-alt mx-2"></i>
                                                </a>

                                                <a href="javascript:void(0);" data-toggle="modal" data-target="#largeModal-1" data-toggle="tooltip" data-placement="top" title="Fee Status">
                                                    <i class="fas fa-folder mx-2"></i>
                                                </a>

                                                <!-- <a href="javascript:void(0);" data-toggle="modal" data-target="#largeModal-3" data-toggle="tooltip" data-placement="top" title="Fee Receipts" onclick="loadfeereceipts() ;">
                                                    <i class="fas fa-file-alt mx-2"></i>
                                                </a> -->

                                                <a href="javascript:void(0);" onclick="openfeereceipts();" data-toggle="tooltip" data-placement="top" title="Fee Receipts"><i class="fas fa-list-alt mx-2"></i></a>

                                                <!-- <a href="javascript:void(0);" onclick="openfeeconcessions();" data-toggle="tooltip" data-placement="top" title="Fee Concessions"><i class="fas fa-tags mx-2"></i></a>  -->
                                                <a href="javascript:void(0);" data-toggle="modal" data-target="#largeModal-7" data-toggle="tooltip" data-placement="top" title="Fee Concessions"   onclick="loadfeeconcessionsummary() ;"><i class="fas fa-tags mx-2"></i></a>

                                                <a href="javascript:void(0);" onclick="openfeeadditions();" data-toggle="tooltip" data-placement="top" title="Fee Additions"><i class="fas fa-folder-plus mx-2"></i></a>
                                                <a href="javascript:void(0);" data-toggle="modal" data-target="#largeModal-6" data-toggle="tooltip" data-placement="top" title="Fee Waiver"   onclick="loadfeewaiversummary() ;">
                                                    <i class="fas fa-money-bill-wave mx-2"></i>
                                                </a>
                                                {{-- <i class="fas fa-user-friends mx-2" data-toggle="tooltip" data-placement="top" title="Group"></i> --}}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <section>

                                <form action="{{ url('admin/post_pay_fee') }}" method="post" id="post_pay_fee"
                                class="post_pay_fee">
                                    @csrf

                                    <input type="hidden" name="student_id" id="student_id" value="">
                                    <input type="hidden" name="school_id" id="school_id" value="">

                                    <div class="col-md-12">
                                        <div class="col-md-12 mt-3 mb-3" id="results">

                                            {{-- Results will fetch --}}

                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex  mt-3 mb-3 float-left" >
                                        <div class="col-md-3">
                                            <div class="position-relative">
                                                <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Date</label>
                                                <div class="form-group">
                                                    <input type="text" name="paid_date" class="form-control datetime-picker" placeholder="Select Date" required style="background-color: white;height:50px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="position-relative" >
                                                <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Received Amount</label>
                                                <div class="form-group">
                                                    <input type="number" min="1" max="9999999" class="form-control floating-label-input amount-input" placeholder="Amount" name="paid_amount" id='pay_amount' required style="height:50px;">
                                                </div>
                                                <input type="hidden" id="total_balance" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="exampleInputEmail1">Payment Mode</label>
                                            <div class="row">
                                                @foreach($get_payment_mode as $mode)
                                                    <div class="col-md-4">
                                                        <div class="form-check radiocheck">
                                                            <input type="radio" class="form-check-input" id="paymentMode{{$mode->id}}" name="payment_mode" value="{{$mode->id}}" {{ $loop->first ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="paymentMode{{$mode->id}}">{{$mode->name}}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <textarea class="form-control" name="payment_remark" id="exampleInputTextarea1" placeholder="Remark"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-center  mb-3">
                                        <button id="send" type="submit" class="btn btn-primary">Save</button>
                                    </div>

                                </form>

                            </section>

                        </div>
                </div>
            </div>

        </div>

    </section>

    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Variable Concession</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/feeconcession')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="feeconcession_student_id" id="feeconcession_student_id">
                    <input type="hidden" name="feeconcession_item_id" id="feeconcession_item_id">
                    <input type="hidden" name="feebalance_amount" id="feebalance_amount">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Concession Amount</label>
                                <div class="form-line">
                                    <input type="text" class="form-control " name="concession_amount" id="concession_amount" required min="0" max=""  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="edit_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="smallModal-3" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Fees</h4>
                </div>

                <form id="edit-style-form-3" enctype="multipart/form-data"
                                  action="{{url('/admin/save/addfees')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="feeadd_student_id" id="feeadd_student_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Fee Type</label>
                                <select class="form-control" id="fee_type" name="fee_type" required style="height:50px;" onchange="loadadditionalFeesItems();">
                                    <option value="">Select</option>
                                    <option value="2">Variable</option>
                                    <option value="3">Optional</option>
                                </select>
                            </div>
                            <div id="show_data_add"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="edit_style_3">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Fee status modal    --}}

    <div class="modal fade" id="largeModal-1" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Fee Records</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/admin/fee_collection/fee_cancel') }}" method="post"  class="post_cancel_receipt">
                    {{ csrf_field() }}

                    <div class="card-body">


                        <div id="show_table_result">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fee Item</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Balance</th>
                                            <th>Concession</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsDiv">
                                        <!-- Table rows will be added here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                            <div class="table-responsive">
                                <div class="div">
                                    <h5>Cancelled Records</h5>
                                </div>
                                <table class="table table-bordered table-striped w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fee Item</th>
                                            <th>Due Date</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Cancelled By</th>
                                            <th>Cancelled Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsCancelDiv">
                                        <!-- Table rows will be added here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="largeModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Fee Summary</h4>
                </div>
                <div class="card-body">


                    <div id="show_table_result">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblfeesummary w-100">
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
                                      <th>Processed By</th>
                                      <th>Processed Date</th>
                                      <th>Action</th>
                                    </tr>
                                </thead>

                              <tfoot>
                                  <th></th> <th></th> <th></th>
                                  <th></th> <th></th> <th></th>
                                  <th></th> <th></th> <th></th>
                                  <th></th> <th></th>
                              </tfoot>

                              <tbody>

                              </tbody>
                            </table>
                        </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="largeModal-3" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Fee Receipts</h4>
                </div>
                <div class="card-body">


                    <div id="show_table_result">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblfeereceipts example1 w-100">
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

                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>

                </div>

                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Cancel Receipts</h4>
                </div>
                <div class="card-body">


                    <div id="show_table_result">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblcancelledfeereceipts example2 w-100">
                                <thead>
                                    <tr>
                                      <th>Receipt#</th>
                                      <th>Receipt Date</th>
                                      <th>Amount</th>
                                      <th>Created By</th>
                                      <th>Created Date</th>
                                      <th>Canceled By</th>
                                      <th>Canceled Date</th>

                                    </tr>
                                </thead>

                                <tfoot>
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
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>

                </div>
            </div>
        </div>
    </div>


    {{-- Fee waiver Modal --}}

    <div class="modal fade" id="largeModal-6" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Fee Waiver</h4>
                    <button type="button" data-toggle="modal" data-target="#fee_waiver_sub" id="addbanner" class="btn btn-primary ml-auto">Add</button>
                </div>
                <div class="card-body">

                    <div>
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblfeewaiver w-100">
                                <thead>
                                    <tr>
                                      <th>Date</th>
                                      <th>Fee Category</th>
                                      <th>Waiver Category</th>
                                      <th>Waiver Amount</th>
                                      <th>Remarks</th> 
                                      <th>Added by </th>
                                    </tr>
                                </thead>

                                <tbody >
                                    <!-- Table rows will be added here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>

                </div> 
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>

                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="fee_waiver_sub" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel"><i class="fas fa-plus"></i> Add Fee Waiver</h4>

                </div>

            <form id="edit-fee-waiver" enctype="multipart/form-data" action="{{ url('/admin/fee_collection/post_fee_waiver') }}" method="post"  class="post_fee_waiver">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                <div class="card-body">

                    <div class=" d-flex  mt-3 mb-3">
                        <div class="col-md-6">
                            <div class="position-relative">
                                <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Date</label>
                                <div class="form-group">
                                    <input type="text" name="waiver_date" class="form-control datetime-picker2" placeholder="Select Date" required style="background-color: white;height:50px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative">
                                <label
                                    class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Category</label>
                                <div class="form-group">
                                    <select class="form-control" id="waiverCategory" name="waiver_category" required style="height:50px;">
                                        <option value="">Select Category</option>
                                        @if(!empty($get_waiver_category))
                                            @foreach($get_waiver_category as $cat)
                                                <option value="{{$cat['id']}}">{{$cat['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="waiver_content">



                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn btn-primary" id="edit_style_feewaive">SAVE</button>

                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- Fee Waiver Modal ENd --}}

    <div class="modal fade" id="smallModal-2feerecep" tabindex="-1" role="dialog" style="z-index: 999999999;">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Receipt Cancel</h4>
                </div>

                <form id="edit-style-form-feerecep" enctype="multipart/form-data" action="{{ url('/admin/fee_collection/fee_cancel') }}" method="post"  class="post_cancel_receipt">
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
                        </div>
                        <div class="card-body">
                            <div class=" d-flex justify-content-center mt-3 mb-3">
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <label
                                            class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Cancel Date</label>
                                            <div class="form-group">
                                                <input type="date" name="cancel_date" class="form-control " placeholder="Select Date" required style="background-color: white;height:50px;" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
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
                        <button type="submit" class="btn btn-primary" id="edit_style_feerecep">SAVE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Fee receipts page --> 
    
    <div class="modal fade" id="smallModal-openfeereceipts" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel"> Fee Receipts</h4>

                </div>

            <form id="frm_openfeereceipts" enctype="multipart/form-data" action="{{ url('/admin/fee_collection/post_fee_waiver') }}" method="post"  class="post_fee_waiver">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">

                <div class="card-body" id="show_data_openfeereceipts">

                     
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button> 

                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Delete Consession Amount -->
    <div class="modal fade in" id="smallModal-22" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Delete Concession</h4>
                </div>

                <form id="delete-style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/delete/feeconcession')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="delete_feeconcession_student_id" id="delete_feeconcession_student_id">
                    <input type="hidden" name="delete_feeconcession_item_id" id="delete_feeconcession_item_id">
                    <input type="hidden" name="delete_feebalance_amount" id="feebalance_amount">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Delete Concession Amount</label>
                                <div class="form-line">
                                    <input type="text" class="form-control " name="delete_concession_amount" id="delete_concession_amount" required min="0" max=""  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="delete_style">SUBMIT</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Delete Waiver Amount -->
    <div class="modal fade in" id="smallModal-23" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Delete Waiver</h4>
                </div>

                <form id="delete-waiver-form" enctype="multipart/form-data"
                                  action="{{url('/admin/delete/feewaiver')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="delete_feewaiver_student_id" id="delete_feewaiver_student_id">
                    <input type="hidden" name="delete_feewaiver_item_id" id="delete_feewaiver_item_id">
                    <input type="hidden" name="delete_feewaiver_amount" id="delete_feewaiver_amount">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Delete Waiver Amount</label>
                                <div class="form-line">
                                    <input type="text" class="form-control " name="delete_waiver_amount" id="delete_waiver_amount" required min="0" max=""  onkeypress="return isNumber(event, this)">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="delete_style_waiver">SUBMIT</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Fee Concession Modal --}}

    <div class="modal fade" id="largeModal-7" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Fee Concession</h4>
                    <button type="button" data-toggle="modal" data-target="#fee_concession_sub" id="addbannerconcession" class="btn btn-primary ml-auto">Add</button>
                </div>
                <div class="card-body">

                    <div>
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblfeeconcession w-100">
                                <thead>
                                    <tr> 
                                      <th>Fee Category</th>
                                      <th>Fee Item</th>
                                      <th>Concession Amount</th>
                                      <th>Concession Date</th>  
                                    </tr>
                                </thead>

                                <tbody >
                                    <!-- Table rows will be added here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>

                </div> 
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fee_concession_sub" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel"><i class="fas fa-plus"></i> Add Fee Concession</h4>

                </div>

                <form id="feeconcession-style-form" enctype="multipart/form-data"  action="{{url('/admin/save/fee_concessions')}}" method="post">

                        {{csrf_field()}}
                        <input type="hidden" name="feeconcession_student_id" id="add_feeconcession_student_id" > 
                    <div class="modal-body"> 
                        <div class="row fee_concessions_list">
                             <div class="col-md-12 mt-3 mb-3" id=" "> 
                                <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                    <div class="table-responsicve">
                                        <table class="table table-striped table-bordered">
                                          <thead>
                                            <tr> 
                                              <th>Item</th> 
                                              <th>Balance Amount</th>
                                              <th></th> 
                                              <th>Concession Amount</th>
                                              <th>Concession Remarks</th>
                                            </tr>
                                          </thead>  
                                          <tbody id="fee_concessions_list">
                                          </tbody>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="add_style_feeconcession">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    {{-- Fee Concession Modal ENd --}}


    <!-- Delete Concession / Waiver added record -->
    <div class="modal fade in" id="smallModal-conwaidelete" tabindex="-1" role="dialog" style="z-index: 111111111111111111;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Delete Concession / Waiver</h4>
                </div>

                <form id="delete-conwaidelete-form" enctype="multipart/form-data"
                                  action="{{url('/admin/delete/feeconwaiverrecord')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="delete_conwaiver_student_id" id="delete_conwaiver_student_id">
                    <input type="hidden" name="delete_conwaiver_item_id" id="delete_conwaiver_item_id">
                    <input type="hidden" name="delete_conwaiver_addedid" id="delete_conwaiver_addedid">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Delete Reason</label>
                                <div class="form-line">
                                    <input type="text" class="form-control " name="delete_reason" id="delete_reason" required minlength="3" maxlength="100">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="delete_conwaiver_added">SUBMIT</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div> 
@endsection

@section('scripts')





<script>

    $('[data-widget="pushmenu"]').PushMenu("collapse");

    $(function() {

        $('#edit_style').on('click', function () {

            var feeconcession_student_id = $('#feeconcession_student_id').val();

            var options = {

                beforeSend: function (element) {

                    $("#edit_style").text('Processing..');

                    $("#edit_style").prop('disabled', true);

                },
                success: function (response) {

                    $("#edit_style").prop('disabled', false);

                    $("#edit_style").text('SAVE');

                    if (response.status == "SUCCESS") {

                       swal('Success',response.message,'success');

                       $('#smallModal-2').modal('hide');

                       sendStudentId(feeconcession_student_id);

                    }
                    else if (response.status == "FAILED") {

                        swal('Oops',response.message,'warning');

                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#edit_style").prop('disabled', false);

                    $("#edit_style").text('SAVE');

                    swal('Oops','Something went to wrong.','error');

                }
            };
            $("#edit-style-form").ajaxForm(options);
        });

        $(".post_pay_fee").on("submit", function(e) {

            e.preventDefault();

            var data = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: data,
                processData: false,
                dataType: 'json',
                contentType: false,
                beforeSend: function() {

                    $("#send").text('Processing..');
                    $("#send").prop('disabled', true);
                },

                success: function(response) {

                    if (response.status == 0) {
                        $("#send").text('Save');
                        $("#send").prop('disabled', false);
                        // $.each(response.error,function(prefix, val){
                        //     $('span.'+prefix+'_error').text(val[0]);
                        // });
                        swal('Oops', response.message, 'warning');

                    } else {
                        if (response.status == 1) {
                            $("#send").text('Save');
                            $("#send").prop('disabled', false);
                            //  $(document).find('span.error-text').text('');

                            /*swal('Success', response.message, 'success');

                            window.location.reload();*/

                            swal({
                                   title: "Success",
                                   text: "Payment Updated Successfully",
                                   type: "success"
                                 },
                               function(){
                                    var stuid = $('#student_id').val();
                                    sendStudentId(stuid);
                               }
                            );

                        }

                    }
                }
            });
        });

        $('#edit_style_3').on('click', function () {

            var feeconcession_student_id = $('#student_id').val();

            var options = {

                beforeSend: function (element) {

                    $("#edit_style_3").text('Processing..');

                    $("#edit_style_3").prop('disabled', true);

                },
                success: function (response) {

                    $("#edit_style_3").prop('disabled', false);

                    $("#edit_style_3").text('SAVE');

                    if (response.status == "SUCCESS") {

                       swal('Success',response.message,'success');

                       $('#smallModal-3').modal('hide');

                       sendStudentId(feeconcession_student_id);

                    }
                    else if (response.status == "FAILED") {

                        swal('Oops',response.message,'warning');

                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#edit_style_3").prop('disabled', false);

                    $("#edit_style_3").text('SAVE');

                    swal('Oops','Something went to wrong.','error');

                }
            };
            $("#edit-style-form-3").ajaxForm(options);
        });


        $(".post_fee_waiver").on("submit", function(e) {

            e.preventDefault();

            var data = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: data,
                processData: false,
                dataType: 'json',
                contentType: false,
                beforeSend: function() {

                    $("#send").text('Processing..');
                    $("#send").prop('disabled', true);
                },

                success: function(response) {

                    if (response.status == 'FAILED') {
                        $("#send").text('Save');
                        $("#send").prop('disabled', false);

                        swal('Oops', response.message, 'warning');

                    } else {
                        if (response.status == "SUCCESS") {
                            $("#send").text('Save');
                            $("#send").prop('disabled', false);

                            swal({
                                title: "Success",
                                text: response.message,
                                type: "success"
                                },
                            function(){
                                $('#largeModal-6').modal('hide');
                                $('#fee_waiver_sub').modal('hide');

                                    var stuid = $('#student_id').val();
                                    sendStudentId(stuid);
                            }
                            );

                        }

                    }
                }
            });
        });


    });
</script>



<script>
function initializeDatepickers() {
            flatpickr('.datetime-picker', {
                enableTime: false,
                dateFormat: "Y-m-d",
                defaultDate: new Date(),
                maxDate: new Date()
            });
        }

$(document).ready(function() {

    // Initialize all tooltips
    $('[data-toggle="tooltip"]').tooltip();

    @if($student_id > 0 && !empty($student_text))  
        $('#studentName').val('{{$student_text}}');
        sendStudentId({{$student_id}});
    @endif

        // Hide tooltip after click
        $('[data-toggle="tooltip"]').on('click', function() {
        $(this).tooltip('hide');
        });

        // Initial call to initialize datepickers on page load
    //     initializeDatepickers();



    // Event listener for the input field to trigger search on input
    $('#studentName').on('input', function() {
        const nameValue = $(this).val();
         // Clear suggestions if input length is 0
         if (nameValue.length === 0) {
            $('#suggestions').empty();
            return;
        }

        // Trigger search if input length is 1 or more
        if (nameValue.length >= 1) {
            searchStudentNames(nameValue);
        }
    });

    // Handle the selection of a student name from suggestions
    $(document).on('click', '.suggestion-item', function() {
        const studentId = $(this).data('id');
        $('#studentName').val($(this).text());
        $('#suggestions').empty();
        sendStudentId(studentId);


    //    $('#student_name').text(student.is_student_name);


    });
});

function searchStudentNames(name) {
    var class_id = $('#classSelect').val();
    var batch = $('#batchSelect').val();
    $.ajax({
        type: 'GET',
        url: " {{ URL::to('/admin/search_student') }}",
        dataType: 'json',
        data: {
            name: name, class_id:class_id, batch:batch
        },
        success: function(data) {
            displaySuggestions(data.students);
        },
        error: function(error) {
            console.error('Error:', error);
        }
    });
}

function displaySuggestions(students) {
    const suggestionsDiv = $('#suggestions');


    suggestionsDiv.empty();

    if (students.length === 0) {
        suggestionsDiv.append('<p>No students found.</p>');
        return;
    }

    students.forEach(student => {
        const suggestionItem = $('<div class="suggestion-item"></div>')
            .html(`<strong>${student.is_student_name} [${student.is_class_name}-${student.is_section_name}]</strong> <br> Adm No: ${student.admission_no}`)
            .attr('data-id', student.user_id)
            .css({ padding: '5px', cursor: 'pointer' });
        suggestionsDiv.append(suggestionItem);
    });


}

function sendStudentId(studentId) {
    $('#show_data').addClass('d-none');
    $('#post_pay_fee')[0].reset();

    const batch = $('#batchSelect').val();


    $.ajax({
        type: 'GET',
        url: " {{ URL::to('/admin/filter_collections') }}",

        dataType: 'json',
        data: {
            student_id: studentId,
            batch: batch // Include batch in the request
        },
        success: function(data) {
            // Assuming data contains the student details
            const student = data.student;
            const cancelled_records = data.cancelled_records;
            const student_detail = data.student_detail;

          // Remove d-none class to show the data
          $('#show_data').removeClass('d-none');



            // Update the student's name
            $('#student_name').text(data.student_detail.is_student_name);
            $('#class_name').text(data.student_detail.is_class_name);
            $('#section_name').text(data.student_detail.is_section_name);
            $('#admission_no').text(data.student_detail.admission_no);
            $('#student_id').val(data.student_detail.user_id);
            $('#school_id').val(data.student_detail.school_id);

            $('#scholar_fees_total').text(data.feedata.scholar_fees_total);
            $('#scholar_fees_concession').text(data.feedata.scholar_fees_concession);
            $('#scholar_fees_paid').text(data.feedata.scholar_fees_paid);
            $('#scholar_fees_balance').text(data.feedata.scholar_fees_balance);

            // Display other details if needed
            displayResults(student);
            displayTable(student,cancelled_records);

            loadFeeWaiver(student,student_detail)
            loadFeeConcession(student,student_detail)
        },
        error: function(error) {
            console.error('Error:', error);
        }
    });
}

function displayResults(student) {

    flatpickr('.datetime-picker2', {
                enableTime: false,
                dateFormat: "Y-m-d",
                defaultDate: new Date(),
                maxDate: new Date()
            });


    const resultsDiv = $('#results');
    resultsDiv.empty();



    if (!student) {
        resultsDiv.append('<p>No student details found.</p>');
        return;
    }

    // Loop through each student
    student.forEach(student => {
        // Initialize HTML string for student details
        let studentDetails = '';

        // Loop through each fee item of the student
        student.fee_items.forEach(feeItem => {
            // Determine if the checkbox should be disabled and if the paid label should be shown
            let checkboxDisabled = '';
            let paidLabel = '<span class="badge bg-warning">Pending</span>';
            let paidInfo = ''; let feeitempaid = '';  let balanceInfo = '';  let feeitemconcession = ''; 
            let concessionInfo = ''; let feeitemcancel = ''; let waiverinfo = '';

            if (feeItem.payment_status_flag == 1) { // Fully paid
                checkboxDisabled = 'disabled';
                paidLabel = '<span class="badge bg-success">Paid</span>';
            } else if (feeItem.payment_status_flag == 2) { // Partially paid
                paidLabel = '<span class="badge bg-warning">Partial</span>';
            } else if (feeItem.payment_status_flag == 3) { // On Due
                paidLabel = '<span class="badge bg-warning">Due</span>';
            } else if (feeItem.payment_status_flag == 4) { // Over Due
                paidLabel = '<span class="badge bg-warning">Over Due</span>';
            } else if (feeItem.payment_status_flag == 5) { // Over Due
                paidLabel = '<span class="badge bg-warning">Pending</span>';
            } else if (feeItem.payment_status_flag == 6) { // Deleted
                paidLabel = '<span class="badge bg-danger">Deleted</span>';
                checkboxDisabled = 'disabled';
            }

            if(feeItem.paid_amount > 0) {

                paidInfo = `<span class="badge bg-info m-1">Paid: &#8377;${feeItem.paid_amount}</span> `; 

            }

            if(feeItem.balance_amount > 0) {

                balanceInfo = `<span class="bg-warning p-1">Balance: &#8377;${feeItem.balance_amount}</span><br><br>`;

                feeitemconcession = '<div class="feescollection float-right"> <i class="fas fa-tags" style="color: #919191;"  onclick="loadConcession('+feeItem.id+', '+feeItem.balance_amount+')" ></i> </div>';
            }

            if(feeItem.paid_amount == 0 && student.fee_type != 1) {
                feeitemcancel = '<div class="feescollection float-left"> <i class="fas fa-trash mr-2" style="color: #0f0;"  onclick="loadDeleteItem('+feeItem.id+', '+feeItem.fee_structure_id+')" ></i> </div>';
            }

            if(feeItem.paid_amount == 0 && student.fee_type == 1 && (feeItem.balance_amount == feeItem.amount)) {
                feeitemcancel = '<div class="feescollection float-left"> <i class="fas fa-trash mr-2" style="color: #f00;"  onclick="loadDeleteFeeItem('+feeItem.id+', '+feeItem.fee_structure_id+')" ></i> </div>';
            }

            if(feeItem.concession_amount > 0) {
                concessionInfo = `<div class="form-group form-float float-left" > <span class="bg-success p-1 m-1">Concession: &#8377;${feeItem.concession_amount}</span> </div>`;
                /*concessionInfo += `<span class="image img_1" onclick="deleteConcession('`+feeItem.id+`', '`+feeItem.fee_structure_id+`', '`+feeItem.concession_amount+`');"><i class="btn-delete fas fa-trash float-right delbtn"></i></span>`;*/
            }
            if(feeItem.waiver_amount > 0) {
                waiverinfo = `<div class="form-group form-float float-left" >  <span class="bg-success p-1 m-1">Waiver: &#8377;${feeItem.waiver_amount}</span> </div>`;
                /*waiverinfo += `<span class="image img_1" onclick="deleteWaiver('`+feeItem.id+`', '`+feeItem.fee_structure_id+`', '`+feeItem.waiver_amount+`');"><i class="btn-delete fas fa-trash float-right delbtn"></i></span>`;*/
            }

            // Append fee item details to student details HTML max-width: 23%;
            studentDetails += `
                <div class="col-md-3 float-left feesborder" style="margin:2px;min-height: 200px !important;min-width: 280px ! important; max-width: 280px !important;    max-height: 200px !important; overflow-y: auto; overflow-x: clip;">
                    <div class="d-flex justify-content-between feescheck">
                        <h4>${feeItem.fee_item.item_name} - <small>${paidLabel} ${paidInfo}</small> </h4>
                        <input type="checkbox" class="fee-checkbox" name="fee_structure_item_id[]" id="exampleCheck1" value="${feeItem.id}" data-balance="${feeItem.balance_amount}" ${checkboxDisabled}>
                    </div>
                    <div class="d-flex termfees col-md-12">
                        <div class=" col-md-6">
                            <span>${feeItem.due_date}</span>
                            <span style="text-wrap: nowrap;">${feeItem.is_term_name} - ${feeItem.fee_item.is_category_name}</span>
                        </div>
                        <div class="schoolproducts  col-md-6">
                            <p>&#8377;${feeItem.amount}</p>
                        </div>
                    </div>
                    ${balanceInfo} 
                    ${concessionInfo}  ${waiverinfo}
                    ${feeitemcancel}
                    ${feeitemconcession}
                </div>
            `;
        });

        // Append student details HTML to resultsDiv
        resultsDiv.append(studentDetails);

    });


    // Add event listener to update total balance on checkbox change
    $('.fee-checkbox').on('change', function() {

        updateTotalBalance();

        initializeDatepickers();
    });

    // Add event listener to validate the amount input
    $('#pay_amount').on('input', function() {


        validateAmount();
    });
}

function displayTable(student,cancelled_records){


    const resultsDiv = document.getElementById('resultsDiv');
    const resultsCancelDiv = document.getElementById('resultsCancelDiv');
      //  const studentName = response.student_detail.is_student_name;

       // Clear previous contents
    resultsDiv.innerHTML = '';
    resultsCancelDiv.innerHTML = '';

      student.forEach(student => {
        student.fee_items.forEach(feeItem => {
            let checkboxDisabled = '';
            let paidLabel = '<span class="badge bg-warning">Pending</span>';
            let balanceInfo = '';
            let feeitemconcession = '';
            let concessionInfo = '';
            let feeitemcancel = '';

            switch (feeItem.payment_status_flag) {
                case 1:
                    checkboxDisabled = 'disabled';
                    paidLabel = '<span class="badge bg-success">Paid</span>';
                    break;
                case 2:
                    paidLabel = '<span class="badge bg-warning">Partial</span>';
                    break;
                case 3:
                    paidLabel = '<span class="badge bg-warning">Due</span>';
                    break;
                case 4:
                    paidLabel = '<span class="badge bg-danger">Over Due</span>';
                    break;
            }

            if (feeItem.balance_amount > 0) {
                balanceInfo = `&#8377;${feeItem.balance_amount}`;
                feeitemconcession = `<i class="fas fa-tags" style="color: #919191;" onclick="loadConcession(${feeItem.id}, ${feeItem.balance_amount})"></i>`;
            }

            // Determine if the delete icon should be enabled or disabled
            if (student.fee_post_type === 5) {
                if (feeItem.paid_amount === 0) {
                    feeitemcancel = `<i class="fas fa-trash" style="color: #f00;" onclick="loadDeleteAddonItem(${feeItem.id}, ${feeItem.fee_structure_id})"></i>`;
                } else {
                    feeitemcancel = `<i class="fas fa-trash" style="color: #ccc;"></i>`;
                }
            } else {
                feeitemcancel = `<i class="fas fa-trash" style="color: #ccc;"></i>`;
            }

            if (feeItem.concession_amount > 0) {
                concessionInfo = `&#8377;${feeItem.concession_amount}`;
            }

            resultsDiv.innerHTML += `
                <tr>
                    <td>${feeItem.fee_item.item_name}</td>
                    <td>${paidLabel}</td>
                    <td>${feeItem.due_date}</td>
                    <td>${feeItem.fee_item.is_category_name}</td>
                    <td>&#8377;${feeItem.amount}</td>
                    <td>${balanceInfo}</td>
                    <td>${concessionInfo}</td>
                    <td>
                        ${feeitemcancel}
                    </td>
                </tr>
            `;
            });
        });

        cancelled_records.forEach(cancelled_records => {


            resultsCancelDiv.innerHTML += `
                <tr>
                    <td>${cancelled_records.fee_item}</td>
                    <td>${cancelled_records.due_date}</td>
                    <td>${cancelled_records.amount}</td>
                    <td>${cancelled_records.category}</td>
                    <td>${cancelled_records.cancelled_by}</td>
                    <td>${cancelled_records.cancelled_date}</td>
                </tr>
            `;

            });




}

function loadConcession(itemid, balanceamount){

    if(itemid > 0 && balanceamount > 0) {
        $('#feeconcession_student_id').val($('#student_id').val());
        $('#feeconcession_item_id').val(itemid);
        $('#feebalance_amount').val(balanceamount);
        $('#concession_amount').val(balanceamount);
        $('#concession_amount').attr('max', balanceamount);
        $('#smallModal-2').modal('show');
    }   else {
        swal("Oops!", "Sorry, No Consession available", "error");
    }
}

function deleteConcession(itemid, structureid, conamount){

    if(itemid > 0 && structureid > 0 && conamount > 0) {
        $('#delete_feeconcession_student_id').val($('#student_id').val());
        $('#delete_feeconcession_item_id').val(itemid); 
        $('#delete_concession_amount').val(conamount);
        $('#delete_concession_amount').attr('max', conamount);
        $('#smallModal-22').modal('show');
    }   else {
        swal("Oops!", "Sorry, No Consession available", "error");
    }
}

function deleteWaiver(itemid, structureid, conamount){

    if(itemid > 0 && structureid > 0 && conamount > 0) {
        $('#delete_feewaiver_student_id').val($('#student_id').val());
        $('#delete_feewaiver_item_id').val(itemid); 
        $('#delete_waiver_amount').val(conamount);
        $('#delete_waiver_amount').attr('max', conamount);
        $('#smallModal-23').modal('show');
    }   else {
        swal("Oops!", "Sorry, No Consession available", "error");
    }
}


function updateTotalBalance() {
    let totalBalance = 0;

    // Loop through each checked checkbox and sum up the balance amounts
    $('.fee-checkbox:checked').each(function() {
        totalBalance += parseFloat($(this).data('balance'));
    });

    // Update the total balance input field
    $('#pay_amount').val(totalBalance.toFixed(0));


    $('#total_balance').val(totalBalance.toFixed(0));




}

function validateAmount() {
    let enteredAmount = parseFloat($('#pay_amount').val());
    let totalBalance = parseFloat($('#total_balance').val());

    console.log(enteredAmount);
    console.log(totalBalance);

    if (enteredAmount > totalBalance) {

      swal('Oops', 'The entered amount exceeds the total balance.', 'warning');

        // Reset the input value to the maximum allowed amount
        $('#pay_amount').val(totalBalance.toFixed(0));
    }
}

function openfeesummary() {
    var batch = $('#batchSelect').val();
    var student_id = $('#student_id').val();
    //window.location.href = "{{URL('/')}}/admin/feesummary?batch="+batch+"&student_id="+student_id;
    var MyWindow = window.open("{{URL('/')}}/admin/feesummary?batch="+batch+"&student_id="+student_id,'MyWindow','width=970,height=700'); return false;
}

function openfeereceipts() {
    var batch = $('#batchSelect').val();
    var student_id = $('#student_id').val(); 

    $.ajax({
        type: 'post',
        url: " {{ URL::to('/admin/load/openfeereceipts') }}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        data: {
            student_id: student_id,
            batch: batch, 
        },
        success: function(response) {

            $('#show_data_openfeereceipts').html('');
            if(response.status == 'SUCCESS') {
                $('#show_data_openfeereceipts').html(response.data);
            }   else {
                $('#show_data_openfeereceipts').html('');
            }

        },
        error: function(error) {
            console.error('Error:', error);
        }
    });

    $('#smallModal-openfeereceipts').modal('show');
    /*var MyWindow = window.open("{{URL('/')}}/admin/feereceipts?batch="+batch+"&student_id="+student_id,'MyWindow','width=970,height=700'); return false;*/
}


function openfeeconcessions() {
    var batch = $('#batchSelect').val();
    var student_id = $('#student_id').val();
    //window.location.href = "{{URL('/')}}/admin/feeconcessions?batch="+batch+"&student_id="+student_id;
    var MyWindow = window.open("{{URL('/')}}/admin/feeconcessions?batch="+batch+"&student_id="+student_id,'MyWindow','width=970,height=500'); return false;
}

function openfeeadditions() {
    $('#edit-style-form-3')[0].reset();
    $('#show_data_add').html('');
    $('#feeadd_student_id').val($('#student_id').val());
    $('#smallModal-3').modal('show');
}

$(document).ready(function() {
    $('#total_balance').val(0); // Initialize total balance to 0 on page load
});

function loadadditionalFeesItems() {
    var batch = $('#batchSelect').val();
    var student_id = $('#student_id').val();
    var fee_type = $('#fee_type').val();

    $.ajax({
        type: 'post',
        url: " {{ URL::to('/admin/load/additional_feesitems') }}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        data: {
            student_id: student_id,
            batch: batch,
            fee_type:fee_type
        },
        success: function(response) {

            $('#show_data_add').html('');
            if(response.status == 'SUCCESS') {
                $('#show_data_add').html(response.data);
            }   else {
                $('#show_data_add').html('');
            }

        },
        error: function(error) {
            console.error('Error:', error);
        }
    });
}

function checkbalance($obj) {
    var max_val = $($obj).attr('max');  max_val = parseInt(max_val); console.log(max_val)
    var entered_val = $($obj).val();  entered_val = parseInt(entered_val);   console.log(entered_val)
    if(entered_val > max_val) {
        $($obj).val(max_val);
    }
}

function loadDeleteItem(itemid, fee_structure_id) {
    var student_id = $('#student_id').val();
    swal({
            title: "Do you want to delete this Item?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-info",
            cancelButtonColor: "btn-danger",
            confirmButtonText: "Yes!",
            cancelButtonText: "No"

    },function(inputValue){
        if(inputValue===false) {
              swal('Success',"Delete Cancelled",'success');

              $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
        }else{
                var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/delete/addfees')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    itemid:itemid, student_id:student_id, fee_structure_id:fee_structure_id
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                swal({
                       title: "Success",
                       text: "Item Deleted Successfully",
                       type: "success"
                     },
                   function(){
                        var stuid = $('#student_id').val();
                        sendStudentId(stuid);
                   }
                );
            });

            request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });



        }
    });
}

function loadDeleteAddonItem(itemid, fee_structure_id) {
    var student_id = $('#student_id').val();
    swal({
            title: "Do you want to delete this Added Fees Item?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-info",
            cancelButtonColor: "btn-danger",
            confirmButtonText: "Yes!",
            cancelButtonText: "No"

    },function(inputValue){
        if(inputValue===false) {
              swal('Success',"Delete Cancelled",'success');

              $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
        }else{
                var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/delete/add_on_fees')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    itemid:itemid, student_id:student_id, fee_structure_id:fee_structure_id
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                swal({
                       title: "Success",
                       text: "Item Deleted Successfully",
                       type: "success"
                     },
                   function(){
                        var stuid = $('#student_id').val();
                        sendStudentId(stuid);
                   }
                );
            });

            request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });



        }
    });
}

function loadDeleteFeeItem(itemid, fee_structure_id) {
    var student_id = $('#student_id').val();
    swal({
            title: "Do you want to delete this Fee Item?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-info",
            cancelButtonColor: "btn-danger",
            confirmButtonText: "Yes!",
            cancelButtonText: "No"

    },function(inputValue){
        if(inputValue===false) {
              swal('Success',"Delete Cancelled",'success');

              $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
        }else{
                var request = $.ajax({
                type: 'post',
                url: " {{URL::to('admin/delete/mandatoryfees')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    itemid:itemid, student_id:student_id, fee_structure_id:fee_structure_id
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                swal({
                       title: "Success",
                       text: "Item Deleted Successfully",
                       type: "success"
                     },
                   function(){
                        var stuid = $('#student_id').val();
                        sendStudentId(stuid);
                   }
                );
            });

            request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });



        }
    });
}



function loadfeesummary() {
    if ($.fn.DataTable.isDataTable('.tblfeesummary')) {
        // Destroy the existing instance before reinitializing
        $('.tblfeesummary').DataTable().destroy();
    }

    var tablefee = $('.tblfeesummary').DataTable({
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
            {
                data:null,
                "render": function ( data, type, row, meta ) {
                    var textline = '';
                    if(data.concession_amount > 0) {
                        if(data.is_concession == 1) {
                            textline = 'C: ';
                        }   else if(data.is_waiver == 1) {
                            textline = 'W: ';
                        }
                        if(data.cancel_status != 0){ 
                            textline += '<span style="color:red;">-'+data.concession_amount +'</span>';
                        }   else {
                            textline += '<span style="color:green;">+'+data.concession_amount +'</span>';
                        }
                    } 
                    return textline;
                }, name: 'concession_amount'
            }, 
            {
                data:null,
                "render": function ( data, type, row, meta ) {
                    var ddate = ''; 
                    if(data.concession_amount > 0) {
                        if(data.is_concession == 1) {
                            ddate =  data.concession_date;
                        }   else if(data.is_waiver == 1) {
                            ddate =  data.is_waiver_date;
                        } 
                    } 
                    return ddate;
                }, name: 'concession_date'
            },  
            { data: 'creator_name', name:'fees_payment_details.created_by'},
            { data: 'created_at', name:'fees_payment_details.updated_at'},
            {
                data:null,
                "render": function ( data, type, row, meta ) {

                    var tid = data.id;
                    if((data.is_concession == 1 || data.is_waiver == 1) && (data.cancel_status == 0)) {
                        return '<a  class="ml-2" style="cursor:pointer" onclick="deletefeedata('+tid+')" title="Delete Fee Data"><i class="fas fa-times text-red"></i></a>';
                    } else {
                        return '';
                    }
                    
                },

            },
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
    // Apply the search
    tablefee.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                        .search( this.value )
                        .draw();
            }
        } );
    } );
}
 

function loadSection(id) {
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

        $('#receipt_id').val(response.data.id);

        $('#receipt_no').text(response.data.receipt_no);

        $('#receipt_date').text(response.data.receipt_date);
        $('#receipt_amount').text(response.data.amount);

        $('#largeModal-3').modal('hide');

        $('#smallModal-2feerecep').modal('show');

    });
    request.fail(function(jqXHR, textStatus) {

        swal("Oops!", "Sorry,Could not process your request", "error");
    });
}



        $('#edit_style_feerecep').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style_feerecep").text('Processing..');

                        $("#edit_style_feerecep").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style_feerecep").prop('disabled', false);

                        $("#edit_style_feerecep").text('SAVE');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success');

                            /*$('.example1').DataTable().ajax.reload();
                            $('.example2').DataTable().ajax.reload();*/

                            openfeereceipts();

                            //$('#largeModal-3').modal('show');
                            $('#smallModal-2feerecep').modal('hide');
                            var stuid = $('#student_id').val();
                            sendStudentId(stuid);
                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#edit_style_feerecep").prop('disabled', false);

                        $("#edit_style_feerecep").text('SAVE');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#edit-style-form-feerecep").ajaxForm(options);
        });
</script>

<script>

function loadFeeWaiver(student, student_detail) { 
    const resultsDiv = document.getElementById('waiver_content');

    //const resultFeeWaiver = document.getElementById('resultFeeWaiver');

    // Clear previous contents
    resultsDiv.innerHTML = '';
    //resultFeeWaiver.innerHTML = '';

    student.forEach((student, studentIndex) => {console.log(student); console.log(studentIndex);
        student.fee_items.forEach((feeItem, feeIndex) => {
            const overallAmount = feeItem.amount;
            const balanceAmount = feeItem.balance_amount;
            const concessionAmount = feeItem.concession_amount;
            const showAmount=overallAmount-concessionAmount;

            const checkboxDisabled = balanceAmount === 0 ? 'disabled' : '';
            
            let cardHTML = `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="form-group row align-items-center mb-1">
                            <div class="col-md-6">
                                <input type="hidden" name="feebalance_amount[${feeItem.id}]" value="${balanceAmount}">
                                <input type="hidden" name="feeconcession_student_id[${feeItem.id}]" value="${student_detail.user_id}">
                                <input type="hidden" name="feeconcession_item_id[${feeItem.id}]" value="${feeItem.id}">
                                <label for="admissionAmount" class="form-label"><strong>${feeItem.fee_item.item_name}</strong></label>
                                <div>
                                    <p class="form-control-plaintext"><i class="fas fa-rupee-sign"></i> ${feeItem.amount}</p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="number" min="0" class="form-control waiver-amount-input" name="waiver_amount[${feeItem.id}]" value="${balanceAmount}" placeholder="Waiver Amount" data-balance="${balanceAmount} data-concession="${concessionAmount}" data-overall="${overallAmount}">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check text-center">
                                    <input type="checkbox" class="form-check-input" name="waiver_checkbox[${feeItem.id}]" data-value="${studentIndex}-${feeIndex}" ${checkboxDisabled} value="${feeItem.id}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-1">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="waiver_remarks[${feeItem.id}]" placeholder="Remarks">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            resultsDiv.innerHTML += cardHTML;
        });
    });



    /*student.forEach((student, studentIndex) => {
        student.fee_items.forEach((feeItem, feeIndex) => {
            const balanceAmount = feeItem.balance_amount;
            const checkboxDisabled = balanceAmount === 0 ? 'disabled' : '';
            let loadHtml = `
                <tr>
                    <td>${feeItem.fee_item.item_name}</td>
                    <td>${feeItem.fee_item.item_name}</td>
                    <td>${feeItem.due_date}</td>
                    <td>${feeItem.fee_item.is_category_name}</td>
                    <td>&#8377;${feeItem.amount}</td>

                </tr>
            `;
            resultFeeWaiver.innerHTML += loadHtml;
        });
    });*/



    // Add event listeners to all waiver amount inputs
    document.querySelectorAll('.waiver-amount-input').forEach(input => {
        input.addEventListener('input', function() {
            const overallAmount = parseFloat(this.getAttribute('data-overall'));
            const balanceAmount = parseFloat(this.getAttribute('data-balance'));
            const concessionAmount = parseFloat(this.getAttribute('data-concession'));
            const showAmount=balanceAmount-concessionAmount;
            const enteredAmount = parseFloat(this.value);
            if (enteredAmount > balanceAmount) {
                // Show swal error
                swal({
                    title: "Error",
                    text: `Waiver amount must be lesser or equal to Rs. ${balanceAmount}`,
                    icon: "error",
                    button: "Ok",
                });

                // Reset the input value to the balance amount
                this.value = balanceAmount;
            }
        });
    });
}



function loadfeewaiversummary() {
    if ($.fn.DataTable.isDataTable('.tblfeewaiver')) {
        // Destroy the existing instance before reinitializing
        $('.tblfeewaiver').DataTable().destroy();
    }
    
    var tblfeewaiver = $('.tblfeewaiver').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        "ajax": {
            "url":"{{URL('/')}}/admin/feewaiversummary/datatables/",
            data: function ( d ) {
                var batch = $('#batch').val();
                var student_id = $('#student_id').val();
                $.extend(d, {batch:batch, student_id:student_id});

            }
        },
        columns: [
            { data: 'is_waiver_date', name: 'is_waiver_date'},
            { data: 'item_name', name: 'item_name'},
            { data: 'waiver_category_name', name: 'waiver_categories.name'},
            { data: 'concession_amount', name:'concession_amount'},
            { data: 'is_waiver_remarks', name: 'is_waiver_remarks'}, 
            { data: 'creator_name', name: 'creator.name'}, 
        ], 
        

    }); 
 
}

 
function loadfeeconcessionsummary() {
    if ($.fn.DataTable.isDataTable('.tblfeeconcession')) {
        // Destroy the existing instance before reinitializing
        $('.tblfeeconcession').DataTable().destroy();
    }
    
    var tblfeeconcession = $('.tblfeeconcession').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        "ajax": {
            "url":"{{URL('/')}}/admin/feeconcessions/datatables/",
            data: function ( d ) {
                var batch = $('#batch').val();
                var student_id = $('#student_id').val();
                $.extend(d, {batch:batch, student_id:student_id});

            }
        },
        columns: [
            { data: 'name', name: 'name'},
            { data: 'item_name', name: 'item_name'},
            { data: 'concession_amount', name: 'concession_amount'},
            { data: 'concession_date', name:'concession_date'},   
        ], 
        

    }); 
 
}

function loadFeeConcession(student, student_detail) { 

    $('#feeconcession-style-form')[0].reset();  
    var batch = $('#batch').val();
    var student_id = $('#student_id').val();
    $('#add_feeconcession_student_id').val(student_id);
    
    const resultsDiv = document.getElementById('fee_concessions_list');

    //const resultFeeWaiver = document.getElementById('resultFeeWaiver');

    // Clear previous contents
    resultsDiv.innerHTML = '';
    //resultFeeWaiver.innerHTML = '';

    student.forEach((student, studentIndex) => {console.log(student); console.log(studentIndex);
        student.fee_items.forEach((feeItem, feeIndex) => {
            const overallAmount = feeItem.amount;
            const balanceAmount = feeItem.balance_amount;
            const concessionAmount = feeItem.concession_amount;
            const showAmount=overallAmount-concessionAmount;

            const checkboxDisabled = balanceAmount === 0 ? 'disabled' : '';
            if(balanceAmount > 0) {
                var cardHTML = `<tr> 
                                <td>${feeItem.fee_item.item_name}</td>
                                <td>${balanceAmount}</td>
                                <td><input type="checkbox" name="concessions[${feeItem.id}]" ${checkboxDisabled}></td>
                                <td><input type="text" class="form-control concession_amount" name="concession_amount[${feeItem.id}]"  minlength="1" maxlength="5" min="0" max="${balanceAmount}"  onkeypress="return isNumber(event, this);" onkeyup=" checkbalance(this);" ${checkboxDisabled}></td>
                                <td><input type="text" class="form-control" name="concession_remarks[${feeItem.id}]" minlength="3" maxlength="50"></td>
                            </tr>`; 
           

                resultsDiv.innerHTML += cardHTML;
            }
                
        });
    }); 

    // Add event listeners to all waiver amount inputs
    document.querySelectorAll('.waiver-amount-input').forEach(input => {
        input.addEventListener('input', function() {
            const overallAmount = parseFloat(this.getAttribute('data-overall'));
            const balanceAmount = parseFloat(this.getAttribute('data-balance'));
            const concessionAmount = parseFloat(this.getAttribute('data-concession'));
            const showAmount=balanceAmount-concessionAmount;
            const enteredAmount = parseFloat(this.value);
            if (enteredAmount > balanceAmount) {
                // Show swal error
                swal({
                    title: "Error",
                    text: `Waiver amount must be lesser or equal to Rs. ${balanceAmount}`,
                    icon: "error",
                    button: "Ok",
                });

                // Reset the input value to the balance amount
                this.value = balanceAmount;
            }
        });
    });
}

/*    $('#addbannerconcession').on('click', function() {
        $('#feeconcession-style-form')[0].reset();  
        var batch = $('#batch').val();
        var student_id = $('#student_id').val();
        $('#add_feeconcession_student_id').val(student_id);
        var request = $.ajax({
            type: 'post',
            url: " {{URL::to('admin/load/feeconcessions')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                batch:batch,student_id:student_id
            },
            dataType:'json',
            encode: true
        });
        request.done(function (response) { 
            if (response.status == "SUCCESS") {
                $('.fee_concessions_list').html(response.data); 
            } else {
                $('.fee_concessions_list').html('No List');  
            }
            $('#fee_concession_sub').modal('show');

        });
        request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });

    });*/

    $('#add_style_feeconcession').on('click', function () {

        var options = {

            beforeSend: function (element) {

                $("#add_style_feeconcession").text('Processing..');

                $("#add_style_feeconcession").prop('disabled', true);

            },
            success: function (response) {



                $("#add_style_feeconcession").prop('disabled', false);

                $("#add_style_feeconcession").text('SAVE');

                if (response.status == "SUCCESS") {

                   swal('Success',response.message,'success');

                   $('.tblfeeconcession').DataTable().ajax.reload();

                   $('#fee_concession_sub').modal('hide');

                    var stuid = $('#student_id').val();
                    sendStudentId(stuid);

                }
                else if (response.status == "FAILED") {

                    swal('Oops',response.message,'warning');

                }

            },
            error: function (jqXHR, textStatus, errorThrown) {

                $("#add_style_feeconcession").prop('disabled', false);

                $("#add_style_feeconcession").text('SAVE');

                swal('Oops','Something went to wrong.','error');

            }
        };
        $("#feeconcession-style-form").ajaxForm(options);
    });


        $('#delete_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#delete_style").text('Processing..');

                        $("#delete_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#delete_style").prop('disabled', false);

                        $("#delete_style").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success'); 
                            $('#smallModal-22').modal('hide');
                            var stuid = $('#student_id').val();
                            sendStudentId(stuid);
                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#delete_style").prop('disabled', false);

                        $("#delete_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#delete-style-form").ajaxForm(options);
        });

        $('#delete_style_waiver').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#delete_style_waiver").text('Processing..');

                        $("#delete_style_waiver").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#delete_style_waiver").prop('disabled', false);

                        $("#delete_style_waiver").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success'); 
                            $('#smallModal-23').modal('hide');
                            var stuid = $('#student_id').val();
                            sendStudentId(stuid);
                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#delete_style_waiver").prop('disabled', false);

                        $("#delete_style_waiver").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#delete-waiver-form").ajaxForm(options);
        });
   
        $('#delete_conwaiver_added').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#delete_conwaiver_added").text('Processing..');

                        $("#delete_conwaiver_added").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#delete_conwaiver_added").prop('disabled', false);

                        $("#delete_conwaiver_added").text('SUBMIT');

                        if (response.status == 1) {

                            swal('Success', response.message, 'success'); 
                            $('#smallModal-conwaidelete').modal('hide');
                            var stuid = $('#student_id').val();
                            sendStudentId(stuid);
                            loadfeesummary();
                        } else if (response.status == 0) {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#delete_conwaiver_added").prop('disabled', false);

                        $("#delete_conwaiver_added").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#delete-conwaidelete-form").ajaxForm(options);
        });

        function deletefeedata(id){
           
            swal({
                    title: "Do you want to delete this from fee summary?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-info",
                    cancelButtonColor: "btn-danger",
                    confirmButtonText: "Yes!",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: true
                    
                    
                   
            },function(inputValue){
                if(inputValue===true) {
                    var stuid = $('#student_id').val();
                    $('#delete_conwaiver_student_id').val(stuid); 
                    $('#delete_conwaiver_addedid').val(id);
                    $('#smallModal-conwaidelete').modal('show');
                }  
           }); 
        }
</script>

@endsection

















