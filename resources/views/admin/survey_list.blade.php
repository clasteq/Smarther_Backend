@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_survey', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
@section('content')
<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@if((isset($session_module['Survey']) && ($session_module['Survey']['list'] == 1)) || ($user_type == 'SCHOOL'))
<meta name="csrf-token" content="{{ csrf_token() }}"> 
<?php if($_SERVER['HTTP_HOST'] != "localhost") { ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> 
<?php } else { ?>
<script src="http://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> 
<?php } ?>

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
                        @if((isset($session_module['Survey']['add']) && ($session_module['Survey']['add'] == 1)) || ($user_type == 'SCHOOL'))
                        <a href="{{url('/admin/addsurvey')}}" id="addbanner"><button class="btn btn-primary" style="float: right;">Add</button></a> 
                        @endif
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
                            <div class="form-group col-md-3 " >
                                <label class="form-label"></label> 
                                <button class="btn btn-danger "  id="clear_style">Clear Filter</button>
                            </div>
                        </div>

                    </div>

                    <div > 
                        
            <input type="hidden" name="pagename" id="pagename" value="communcation_survey">
            <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
                    </div>
                    <div class="col-md-10 ml-15 elevation-3" style="    background: #ebd2cf;max-height: 500px;  overflow-y: auto;scrollbar-width: thin; scrollbar-color: #a9a9a9 transparent;">
                    @include('admin.postsurvey_list')   
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</section>

@else 
@include('admin.notavailable') 
@endif


    <div class="modal fade" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Post Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="card-body">


                    <div id="show_table_result">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblpoststatus">
                                <thead>
                                    <tr> 
                                      <th>Name</th> 
                                      <th class="no-sort">Read Date</th>  
                                      <th class="no-sort">Sent Date</th>
                                      <th class="no-sort">Acknowledged Status</th>
                                      <th class="no-sort">App Installed</th>
                                      <th>Mobile</th> 
                                      <th>Class</th> 
                                      <th>Section</th>  
                                    </tr>
                                </thead>
                                <tfoot>
                                      <tr><th></th><th></th><th></th>
                                          <th></th><th></th><th></th>
                                          <th></th><th></th>
                                      </tr>
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



@endsection

@section('scripts') 

    <script> 

            $('#clear_style').on('click', function () {
                $('.card-header').find('input').val('');
                $('.card-header').find('select').val('');
                filterposts();
            }); 

        function deleteactivity(id){
            $('#filter_pagename').val($('#pagename').val());
            swal({
                    title: "Do you want to delete this from your Survey?",
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
                        url: " {{URL::to('admin/delete/survey')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            post_id:id,
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if(response.status == 1)   {
                             swal('Success',response.message,'success');
                             filterProducts();
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
 

        //function updatestatus(obj, id) {
        function confirmactivity(id, status) {
            var str = "Are you sure you want to change?";
            if(status == "ACTIVE") {
                str = "Are you sure to Confirm?";
            } else {
                str = "Are you sure to Reject?";
            }
            //var status = $(obj).val();
            swal({
                    title: str,
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
                      swal('Info',"Nothing done",'info');
                      
                      $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
                }else{
                        $('#filter_pagename').val($('#pagename').val());
                        var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('admin/update/posts')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            post_id:id,status:status
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if(response.status == 1)   {
                             swal('Success',response.message,'success');
                             filterProducts();
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


        function openpoststatus(pid) {
            if ($.fn.DataTable.isDataTable('.tblpoststatus')) {
                // Destroy the existing instance before reinitializing
                $('.tblpoststatus').DataTable().destroy();
            }
            $('#smallModal-2').modal('show');
            var tablefee = $('.tblpoststatus').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/poststatus/datatables?id="+pid, 
                },
                columns: [
                    { data: 'name',  name: 'users.name'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.notify != '' && data.notify != null && data.notify.read_status != null){
                                var tid = data.notify.read_status;
                                var tdate = data.notify.read_date;
                                if(tid == 1)
                                    return tdate;
                                else 
                                    return '-';
                            }   else {
                                return '-';
                            }
                        },

                    }, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.notify != '' && data.notify != null && data.notify.created_at != null){
                                var tid = data.notify.created_at; 
                                return tid;
                            }   else {
                                return '-';
                            }
                        },

                    }, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.notify != '' && data.notify != null && data.notify.is_acknowledged != null){
                                var tid = data.notify.is_acknowledged; 
                                if(tid == 1)
                                    return 'Acknowledged';
                                else 
                                    return 'Not Acknowledged';
                            }   else {
                                return '-';
                            }
                        },

                    }, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.is_app_installed == 1){ 
                                return 'Installed'; 
                            }   else {
                                return 'Not Installed';
                            }
                        },
                        name: 'users.is_app_installed'
                    }, 
                    { data: 'mobile',  name: 'users.mobile'}, 
                    { data: 'class_name',  name: 'classes.class_name'}, 
                    { data: 'section_name',  name: 'sections.section_name'}, 
                ],
                "order":[[0, 'asc']],
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
                                "url":"{{URL('/')}}/admin/poststatus_excel?id="+pid,
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'PostStatus.xls');
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
    </script>
 

@endsection


