@extends('layouts.admin_master')
@section('report_settings', 'active')
@section('master_teacherleave', 'active')
@section('menuopenr', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/teacherleavelist'), 'name'=>'Leaves', 'active'=>''], ['url'=>'#', 'name'=>'Update Teachers Leave', 'active'=>'active'] ];
?>
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">
            <div class="panel">
                <!-- Panel Heading -->
                <div class="panel-heading">
                    <!-- Panel Title -->
                    
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="panel-title" style="font-size: 20px;">Update Teachers Leave
                                </h4>
                                <div class="row"><div class="col-md-12">
                                    <div class="row">
                                        <form name="students_mark" id="students_mark" method="post" action="{{URL('/')}}/admin/edit/teacherleave">
                                            {{csrf_field()}}
                                            <input type="hidden" name="leave_id" value="{{$qb['id']}}">
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Student</label>
                                            <div class="form-line"> {{$qb['name']}} {{$qb['admission_no']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave Title</label>
                                            <div class="form-line">{{$qb['title']}} </div>
                                        </div>
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Duration</label>
                                            <div class="form-line">{{$qb['duration']}} </div>
                                        </div>

                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave Type</label>
                                            <div class="form-line">{{$qb['leave_type']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave Date</label>
                                            <div class="form-line">{{date('Y-m-d',strtotime($qb['from_date']))}} </div>
                                        </div>
                                        {{-- <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave End Date</label>
                                            <div class="form-line">{{$qb['leave_end_date']}}</div>
                                        </div> --}}
                                        @if(!empty($qb['description']))
                                         <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave Reason</label>
                                            <div class="form-line">{{$qb['description']}} </div>
                                        </div>
                                        @elseif(!empty($qb['descriptionfile']))
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave Reason Attachment</label>
                                            <div class="form-line"> 
                                         <a href="{{$qb['is_descriptionfile']}}" target="_blank" title="Leave Attachement" class="btn btn-info">View</a></div>
                                            {{-- <div class="form-line">{{$qb['leave_reason']}} </div> --}}
                                        </div>
                                        @endif
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Status</label>
                                             <div class="form-line">
                                                <select class="form-control col-sm-6" name="status_id" id="status_id" >
                                                     {{-- <option value="">All</option> --}}
                                                    <option <?php  if($qb['status'] == "PENDING"){
                                                       echo  "selected";
                                                      } ?> value="PENDING">PENDING</option>
                                                    <option <?php  if($qb['status'] == "APPROVED"){
                                                        echo  "selected";
                                                       } ?> value="APPROVED">APPROVED</option>
                                                    <option  <?php  if($qb['status'] == "REJECTED"){
                                                        echo  "selected";
                                                       } ?> value="REJECTED">REJECTED</option>
                                                </select>
                                                
                                            </div>
                                        
                                        </div>
                                        <div style="visibility: hidden" class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Leave Reason Attachment</label>
                                            <div class="form-line"> 
                                         <a href="{{$qb['is_descriptionfile']}}" target="_blank" title="Leave Attachement" class="btn btn-info">View</a></div>
                                            {{-- <div class="form-line">{{$qb['leave_reason']}} </div> --}}
                                        </div>
                                   
                                   
                                        <div  class="form-group form-float float-left col-md-6">
                                           
                                            <button type="submit" class="btn btn-success center-block float-left" id="Submit">Submit</button>
                                        </div>

                                        <div  class="form-group form-float float-right col-md-6">
                                           
                                            <a href="{{url('/admin/teacherleavelist')}}" class="btn btn-info waves-effect">BACK</a>
                                        </div>
                                      
                                      

                                    </form>
                                      
                                    </div>
                                   
                                    <!-- End Question Types -->
                                </div></div>

                                {{-- <a href="{{url('/admin/studentstestlist')}}" class="btn btn-info waves-effect">BACK</a> --}}
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


            $('#Submit').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        if (response.status == "SUCCESS") {
                            swal({title: "Success", text: response.message, type: "success"},
                            function(){
                                
                                window.location.reload();
                            }
                        );

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#students_mark").ajaxForm(options);
            });
        });

        $(".noofquest").keyup(function(){
  
  var qbid =  $(this).data('qbid');
  var total_ques = parseInt($('.tot_mark_'+qbid).val(), 10) || 0;
  var tot_question = parseInt(this.value, 10) || 0;
  if(total_ques < tot_question){
    $('.noofquest_'+qbid).val('0');
    swal('Oops','Maximum Mark for this Question is '+total_ques,'error');
  }
  
});
        

    </script>


@endsection

