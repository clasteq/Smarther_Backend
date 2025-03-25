@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_posts', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
@section('content')
<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?>  
<meta name="csrf-token" content="{{ csrf_token() }}"> 

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<style type="text/css">
        .actinput {
            background-color: white; 
            border-radius: 50px;
        }
        .photos {
            background-color: unset;
            width: 50%;
            border-radius: 50px;
        }
        .submitact {
            border-color: #fff;
            border-radius: 20%;
            border-style: hidden;
            background: #f8f6f6;
        }
        input[type=file] {
          display: block;
          color: red;
          font-style: oblique;
        }
        input[type=file]::file-selector-button {
          /*display: none;
           visibility:hidden;*/ 
        }
        .activityimage img {
            width: 70px;
            height: 100px; /*auto; */
            border-radius: 3%;
        }
        .editact {
            width: 20px;
            height: 20px !important;
        }
        .deleteact {
            width: 20px;
            height: 20px !important;
        }
        .likeact {
            cursor: pointer;
        }
        .w-15 {
            width: 15px !important;
        }

        .offerolympiaimg {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #fff !important;
            min-height:100% !important;
            max-height:100% !important;
            /*max-width: 712px !important;*/
            max-width: 100% !important;
            overflow-y: auto;
        }

        .offerolympia {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #000;
            overflow-y: auto;
        } 
        .ml-15 {
            margin-left: 6rem !important;
        }

        blockquote {
            background-color: transparent;
            border-left: .2rem solid #007bff;
            margin: 1.5em .7rem;
            padding: .5em .7rem; 
        }

        .center {
            text-align: center;
        }

        .carousel-control-next-icon {
            background: #000 !important;
            color: #fff !important;
            width: 25px;
            height: 25px;
            font-weight: bolder;
        }
        .carousel-control-prev-icon {
            background: #000 !important;
            color: #fff !important;
            width: 25px;
            height: 25px;
            font-weight: bolder;
        }
        .carousel-indicators {
            background: #000 !important;
            color: #fff !important;
            font-weight: bolder;
        }
        .carousel-inner { 
            display: flex;
            align-items: center;
        }
        .modal-full {
            min-width: 95%;
            margin: 10;
        }
        .modal-full .modal-body {
            overflow-y: auto;
        }
        .receiverslist {
            max-height: 90px;
            overflow-y: auto;
            scrollbar-width: thin; 
        } 
</style>

<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel"> 
                <div class="panel-body">


            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <div class="card-header">  
                        <div class="row">
                            <div class="form-inline col-md-2 " >
                                <label class="form-label mr-1">Search: </label>
                                <input class="form-control col-md-6" type="text" id="search" placeholder="Search" />
                            </div>
                            <div class="form-inline col-md-3 " >
                                <label class="form-label mr-1">From: </label>
                                <input class="date_range_filter date form-control col-md-6" type="text" id="datepicker_from" placeholder="From Date" />
                            </div>
                            <div class="form-inline col-md-3 " >
                                <label class="form-label mr-1">To: </label>
                                <input class="date_range_filter date form-control col-md-6" type="text" id="datepicker_to" placeholder="To Date"  />
                            </div> 
                        </div>

                    </div>

                    <div > 
                        
            <input type="hidden" name="pagename" id="pagename" value="staff_notifications">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
                    </div>
                    <div class="col-md-10 ml-15 elevation-3" style="    background: #ebd2cf;max-height: 500px;  overflow-y: auto;scrollbar-width: thin; scrollbar-color: #a9a9a9 transparent;">
                    @include('admin.postnotifications_list')   
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


        $('#category_id').on('change', function() {
            filterposts();
        }); 

        $('#search').on('keyup', function() {
            filterposts();
        });

        $("#datepicker_from").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        }).change(function() {
            filterposts();
        }).keyup(function() {
            filterposts();
        });

        $("#datepicker_to").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        }).change(function() {
            filterposts();
        }).keyup(function() {
            filterposts();
        });

        function filterposts() {
            $('#filter_pagename').val($('#pagename').val());
            var minDateFilter  = $('#datepicker_from').val();
            var maxDateFilter  = $('#datepicker_to').val();
            if(new Date(maxDateFilter) < new Date(minDateFilter))
            {
                alert('To Date must be greater than From Date');
                return false;
            }
             
            $('#filter_from_date').val(minDateFilter);
            $('#filter_to_date').val(maxDateFilter);
            $('#filter_category_id').val($('#category_id').val());
            $('#filter_search').val($('#search').val());

            filterProducts();
        } 

        function updatesurvey($postid, $optionid) {
            $('#filter_pagename').val($('#pagename').val());
            swal({
                    title: "Are you sure to submit?",
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
                if(inputValue===false) {
                      swal('Info',"Cancelled Delete",'info');
                      
                      $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
                }else{
                        $('#filter_pagename').val($('#pagename').val());
                        var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('admin/update/survey')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            post_id:$postid, option_id:$optionid
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if(response.status == 'SUCCESS')   {
                             swal('Success',response.message,'success');
                             filterposts();
                         } else {
                             swal('warning',response.message,'warning');
                         }
                    
                    });
        
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });  
                }
            }); 
        }
    </script>
 

@endsection


