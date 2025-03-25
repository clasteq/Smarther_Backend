@extends('layouts.admin_master')
@section('sch_settings', 'active')
@section('master_students', 'active')
@section('menuopensch', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Student', 'active' => 'active']];
?>
@section('content')
<style type="text/css">
    .box.box-primary {
        border-top: 5px solid #3c8dbc !important;
    }

    .box {
        position: relative;
        border-radius: 3px;
        background: #ffffff;
        border-top: 3px solid #d2d6de;
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .profile-user-img {
        height: 100px !important;
    }

    #myTabs li {
        padding: 10px
    }

    #myTabs li.active {
        border: 1px solid #FF6F61;
    }

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
        padding: 3px;
        border-radius: 15px;
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
    .no-wrap {
        text-wrap: nowrap; 
    }
</style> 
<meta name="csrf-token" content="{{ csrf_token() }}"> 
<section class="content">
    <!-- Exportable Table -->
    <div class="content container-fluid">
        @if(!empty($user_details))
            <div class="card">
                <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-primary border">
                                <div class="box-body box-profile">
                                  <img class="profile-user-img img-responsive img-circle" style="margin-left: 30%;" src="{{$user_details['is_profile_image']}}" alt="User profile picture">
                                  <input type="hidden" name="batchSelect" id="batchSelect" value="{{$batch}}">
                                  <h3 class="profile-username text-center">{{$user_details['name']}}</h3>

                                  <p class="text-muted text-center">Admission No : {{$user_details['admission_no']}}</p>
                                  <p class="text-muted text-center"> {{$user_details['userdetails']['is_class_name']}} -  {{$user_details['userdetails']['is_section_name']}}</p>

                                  <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                      <b>Email</b> <a class="float-right">{{$user_details['email']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Mobile</b> <a class="float-right">{{$user_details['mobile']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Emergency Contact</b> <a class="float-right">{{$user_details['emergency_contact_no']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                      <b>Status</b> <a class="float-right">{{$user_details['status']}}</a>
                                    </li>
                                  </ul>
 
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <div class="box box-primary border">
                                <div class="box-body box-profile">   

                                  <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                       Religion  <a class="float-right">{{$user_details['userdetails']['religion']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                       Community  <a class="float-right">{{$user_details['userdetails']['community']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                       Caste <a class="float-right">{{$user_details['userdetails']['caste']}}</a>
                                    </li> 
                                  </ul>
 
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="container-fluid">
                                <ul class="nav nav-tabs" id="myTabs">
                                    <li class="active"><a href="#basic_details" id="libasic" data-url="basic">Basic Details</a></li> 
                                    <li><a href="#remark_details"  id="liremark" data-url="remark" >Remarks</a></li>
                                    <li><a href="#reward_details"  id="lireward" data-url="reward" >Rewards</a></li>
                                    <li><a href="#fees_details"  id="lifees" data-url="fees" onclick="feesStudentId({{$user_details['id']}});">Fees</a></li>
                                    <li><a href="#exam_details"  id="liexam" data-url="exam" onclick="examResultsStudentId({{$user_details['id']}});">Exam Results</a></li>
                                </ul>
                              
                                <div class="tab-content">
                                    <div class="tab-pane active" id="basic_details">

                                        <div class="box border">
                                            <div class="box-body ">
                                                <div class="row">
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Full Name </label>
                                                        <div class="form-line">{{$user_details['name']}} {{$user_details['last_name']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Gender </label>
                                                        <div class="form-line">{{$user_details['gender']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Date of Birth </label>
                                                        <div class="form-line">
                                                        @if(!empty($user_details['dob']))
                                                        {{date('d-M-Y', strtotime($user_details['dob']))}}
                                                        @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Last Login Date </label>
                                                        <div class="form-line">@if(!empty($user_details['last_login_date']))
                                                        {{date('d-M-Y', strtotime($user_details['last_login_date']))}}
                                                        @endif</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Last App opened Date </label>
                                                        <div class="form-line">@if(!empty($user_details['last_app_opened_date']))
                                                        {{date('d-M-Y', strtotime($user_details['last_app_opened_date']))}}
                                                        @endif</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Joined Date </label>
                                                        <div class="form-line">@if(!empty($user_details['joined_date']))
                                                        {{date('d-M-Y', strtotime($user_details['joined_date']))}}
                                                        @endif</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="box border">
                                            <div class="box-body ">
                                                <div class="row">
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Father Name </label>
                                                        <div class="form-line">{{$user_details['userdetails']['father_name']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Blood Group </label>
                                                        <div class="form-line">{{$user_details['userdetails']['is_blood_group']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Emis id </label>
                                                        <div class="form-line">{{$user_details['userdetails']['emis_id']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Aadhar Number </label>
                                                        <div class="form-line">{{$user_details['userdetails']['aadhar_number']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Address </label>
                                                        <div class="form-line">{{$user_details['userdetails']['address']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Country </label>
                                                        <div class="form-line">{{$user_details['is_country_name']}}
                                                        </div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">State </label>
                                                        <div class="form-line">{{$user_details['is_state_name']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">District </label>
                                                        <div class="form-line">{{$user_details['is_district_name']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Pincode </label>
                                                        <div class="form-line">{{$user_details['userdetails']['pincode']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Identification Mark 1 </label>
                                                        <div class="form-line">{{$user_details['userdetails']['identification_mark_1']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Identification Mark 2</label>
                                                        <div class="form-line">{{$user_details['userdetails']['identification_mark_2']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Stay </label>
                                                        <div class="form-line">{{$user_details['userdetails']['stay_type']}}</div>
                                                    </div>
                                                    <div class="form-group form-float float-left col-md-6">
                                                        <label class="form-label">Transport </label>
                                                        <div class="form-line">{{$user_details['userdetails']['transport']}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane" id="remark_details">
                                        <div class="col-md-12"> 
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                                        <div class="table-responsicve">
                                                            <table class="table table-striped table-bordered tblremarks">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Description</th>
                                                                        <th>Is Notify</th>  
                                                                        <th>Created By</th>  
                                                                        <th>Action</th>  
                                                                    </tr>
                                                                </thead> 
                                                                <tbody>
                                                                    @if(isset($user_remarks) && !empty($user_remarks))
                                                                        @foreach($user_remarks as $remark)
                                                                            <tr>
                                                                                <td>{{$remark->created_at}}</td>
                                                                                <td>{{$remark->remark_description}}</td>
                                                                                <td>@if($remark->remark_notify == 1) Yes @else No @endif</td>  
                                                                                <td>{{$remark->posted_user->name}}</td> 
                                                                                <td><a href="#" onclick="deleteremarks({{$remark->id}})" title="Delete Remark"><i class="fas fa-trash"></i></a></td>  
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="tab-pane" id="reward_details">
                                        <div class="col-md-12"> 
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                                        <div class="table-responsicve">
                                                            <table class="table table-striped table-bordered tblrewards">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Description</th>
                                                                        <th>Is Notify</th>  
                                                                        <th>Created By</th>  
                                                                        <th>Action</th>   
                                                                    </tr>
                                                                </thead> 
                                                                <tbody>
                                                                    @if(isset($user_rewards) && !empty($user_rewards))
                                                                        @foreach($user_rewards as $remark)
                                                                            <tr>
                                                                                <td>{{$remark->created_at}}</td>
                                                                                <td>{{$remark->remark_description}}</td>
                                                                                <td>@if($remark->remark_notify == 1) Yes @else No @endif</td>   
                                                                                <td>{{$remark->posted_user->name}}</td> 
                                                                                <td><a href="#" onclick="deleterewards({{$remark->id}})" title="Delete Remark"><i class="fas fa-trash"></i></a></td>  
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="tab-pane" id="fees_details">
                                        <div class="col-md-12"> 
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <div style="width: 100%;  ">
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
                                                            <div class="col-md-12 mt-3 mb-3" id="results"> 

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="tab-pane" id="exam_details">
                                        <div class="col-md-12"> 
                                            <div class="card-content collapse show">
                                                <div class="card-body card-dashboard">
                                                    <div style="width: 100%;  "> 
                                                        <div class="col-md-12">
                                                            <div class="col-md-12 mt-3 mb-3" id="examresults"> 

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                        </div>
 
                        
                    </div>
                </div>
            </div>
        @else 
            <div class="card">
                <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Invalid SCholar Request</h4>
                        </div> 
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
     
    $('.tblremarks').DataTable();
      
    $('.tblrewards').DataTable();
    

    $('#myTabs a').click(function (e) {
        e.preventDefault();
        $('#myTabs li').removeClass('active')
        var url = $(this).attr("data-url");
        $(this).parent('li').addClass('active');
        var href = this.hash;
        var pane = $(this); 

        // ajax load from data-url
        $(href).load(url,function(result){      
            pane.tab('show');
        });
    });

    // load first tab content
    $('#basic_details').load($('.active a').attr("data-url"),function(result){
      $('.active a').tab('show');
    });

    function deleteremarks(id){
        swal({
            title : "",
            text : "Are you sure to delete?",
            type : "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
        },
        function(isConfirm){
            if (isConfirm) {
                var request = $.ajax({
                    type: 'post',
                    url: " {{URL::to('/admin/delete/remarks')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        id:id,
                    },
                    dataType:'json',
                    encode: true
                });
                request.done(function (response) {
                    if (response.status == 1) {

                        swal('Success',response.message,'success');

                        window.location.reload();
                    }
                    else{
                        swal('Oops',response.message,'error'); 
                    }

                });
                request.fail(function (jqXHR, textStatus) {

                    swal("Oops!", "Sorry,Could not process your request", "error");
                });
            }
        })


    }

    function deleterewards(id){
        swal({
            title : "",
            text : "Are you sure to delete?",
            type : "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
        },
        function(isConfirm){
            if (isConfirm) {
                var request = $.ajax({
                    type: 'post',
                    url: " {{URL::to('/admin/delete/rewards')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        id:id,
                    },
                    dataType:'json',
                    encode: true
                });
                request.done(function (response) {
                    if (response.status == 1) {

                        swal('Success',response.message,'success');

                        window.location.reload();
                    }
                    else{
                        swal('Oops',response.message,'error'); 
                    }

                });
                request.fail(function (jqXHR, textStatus) {

                    swal("Oops!", "Sorry,Could not process your request", "error");
                });
            }
        })


    }

    function feesStudentId(studentId) { 

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

                $('#scholar_fees_total').text(data.feedata.scholar_fees_total);
                $('#scholar_fees_concession').text(data.feedata.scholar_fees_concession);
                $('#scholar_fees_paid').text(data.feedata.scholar_fees_paid);
                $('#scholar_fees_balance').text(data.feedata.scholar_fees_balance);

                // Display other details if needed
                displayResults(student); 
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    function displayResults(student) { 

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
                let paidInfo = ''; let feeitempaid = '';  let balanceInfo = '';  let feeitemconcession = '';  let feeitemwaiver = ''; 
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

                    paidInfo = `<span class="badge bg-info m-1">P: &#8377;${feeItem.paid_amount}</span>`; 

                }

                if(feeItem.balance_amount > 0) {

                    balanceInfo = `<p><span style="text-wrap: nowrap; color: #fff;">B: &#8377;${feeItem.balance_amount}</span> </p>`;

                    feeitemconcession = '<div class="feescollection float-right"> <i class="fas fa-tags" style="color: #919191;"  onclick="loadConcession('+feeItem.id+', '+feeItem.balance_amount+')" ></i> </div>';

                    feeitemwaiver = '<div class="feescollection float-right"> <i class="fas fa-tags" style="color: #919191;"  onclick="loadWaiver('+feeItem.id+', '+feeItem.balance_amount+')" ></i> </div>';
                }

                if(feeItem.paid_amount == 0 && student.fee_type != 1) {
                    feeitemcancel = '<div class="feescollection float-left d-none"> <i class="fas fa-trash mr-2" style="color: #0f0;"  onclick="loadDeleteItem('+feeItem.id+', '+feeItem.fee_structure_id+')" ></i> </div>';
                }

                if(feeItem.paid_amount == 0 && student.fee_type == 1 && (feeItem.balance_amount == feeItem.amount)) {
                    feeitemcancel = '<div class="feescollection float-left d-none"> <i class="fas fa-trash mr-2" style="color: #f00;"  onclick="loadDeleteFeeItem('+feeItem.id+', '+feeItem.fee_structure_id+')" ></i> </div>';
                }

                if(feeItem.concession_amount > 0) {
                    concessionInfo = ` <span class="badge bg-success m-1">C: &#8377;${feeItem.concession_amount}</span> `;
                    /*concessionInfo += `<span class="image img_1" onclick="deleteConcession('`+feeItem.id+`', '`+feeItem.fee_structure_id+`', '`+feeItem.concession_amount+`');"><i class="btn-delete fas fa-trash float-right delbtn"></i></span>`;*/
                }
                if(feeItem.waiver_amount > 0) {
                    waiverinfo = ` <span class="badge bg-primary m-1">W: &#8377;${feeItem.waiver_amount}</span> `;
                    /*waiverinfo += `<span class="image img_1" onclick="deleteWaiver('`+feeItem.id+`', '`+feeItem.fee_structure_id+`', '`+feeItem.waiver_amount+`');"><i class="btn-delete fas fa-trash float-right delbtn"></i></span>`;*/
                }

                // Append fee item details to student details HTML max-width: 23%;
                studentDetails += `
                    <div class="col-md-4 float-left feesborder" style="margin:2px;min-height: 200px !important;min-width: 280px ! important; max-width: 300px !important;    max-height: 200px !important; overflow-y: auto; overflow-x: clip;">

                        <div class="d-flex justify-content-between feescheck">
                            <h6>${feeItem.fee_item.item_name} - ${feeItem.fee_item.is_category_name}</h6>
                        </div>
                        <div class="d-flex termfees col-md-12">

                            <div class=" col-md-4"> <small>${paidLabel} </small> </div>
                            <div class="schoolproducts text-right col-md-6">
                                ${balanceInfo}
                            </div> 
                            
                        </div>
                        <div class="d-flex termfees col-md-12">
                            <div class=" col-md-4">
                                <span class="no-wrap">T:&#8377;${feeItem.amount}</span> 
                            </div>
                            <div class="col-md-6 text-right">
                                ${paidInfo} 
                            </div> 
                        </div>
                        <div class="d-flex termfees col-md-12">
                            <div class=" col-md-4">
                                <span style="text-wrap: nowrap;">${feeItem.is_term_name}</span>
                            </div>
                            <div class="col-md-6 text-right">
                                ${concessionInfo}
                            </div>  
                        </div> 
                        <div class="d-flex termfees col-md-12">
                            <div class=" col-md-4">
                                <span class="no-wrap">${feeItem.due_date}</span> 
                            </div>
                            <div class=" text-right col-md-6">
                                ${waiverinfo}
                            </div>  
                        </div>  
                    </div>
                `;
            });

            // Append student details HTML to resultsDiv    ${feeitemcancel}
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
 
    function examResultsStudentId(studentId) { 

        const batch = $('#batchSelect').val(); 
        $.ajax({
            type: 'GET',
            url: " {{ URL::to('/admin/filter_examresults') }}",

            dataType: 'json',
            data: {
                student_id: studentId,
                batch: batch // Include batch in the request
            },
            success: function(response) {
                $('#examresults').html(response.data);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }
</script>
@endsection