@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_postsms', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
@section('content')
<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@if((isset($session_module['SMS']) && ($session_module['SMS']['list'] == 1)) || ($user_type == 'SCHOOL'))
<meta name="csrf-token" content="{{ csrf_token() }}">

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
            width: 20%; /*70px;*/
            height: auto; /*200px;*/
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
        .offerolympia {
            margin-right: 25px !important;
            padding: 32px !important; 
            min-height: 172px !important;
            max-height: 372px !important;
            min-width: 712px !important;
            overflow-y: auto;
        }
        .ml-15 {
            margin-left: 5rem !important;
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
        <div class="content container-fluid">

            <div class="panel"> 
                <div class="panel-body">


            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <div class="card-header"><!-- SMS for Scholars -->
                        @if((isset($session_module['SMS']) && ($session_module['SMS']['add'] == 1)) || ($user_type == 'SCHOOL'))
                        <a href="{{url('/admin/addpostsms')}}" id="addbanner"><button class="btn btn-primary" style="float: right;">Add</button></a> 
                        @endif
                        <div class="row">
                            <div class="form-group col-md-2 " >
                                <label class="form-label mr-1">Search</label>
                                <input class="form-control " type="text" id="search"  placeholder="Search" />
                            </div>
                            <div class="form-group col-md-2 " >
                                <label class="form-label mr-1">From</label>
                                <input class="date_range_filter date form-control " type="text" id="datepicker_from" placeholder="From Date" />
                            </div>
                            <div class="form-group col-md-2 " >
                                <label class="form-label mr-1">To</label>
                                <input class="date_range_filter date form-control " type="text" id="datepicker_to" placeholder="To Date" />
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label mr-1">Categories</label>
                                <div class="form-line">
                                    <select class="form-control " name="category_id" id="category_id" >
                                        <option value="">All Categories</option>
                                     @if (!empty($categories))
                                         @foreach ($categories as $cat)
                                         <option value={{$cat->id}}>{{$cat->name}}</option>  
                                         @endforeach
                                     @endif 
                                        <option value="Deleted">Deleted</option>
                                        <option value="Inactive">Rejected</option>
                                    </select> 
                                </div>
                            </div>

                            <div class="form-group col-md-2 " >
                                <label class="form-label"></label> 
                                <button class="btn btn-danger mt-3"  id="clear_style">Clear Filter</button>
                            </div>
                        </div>
                    </div>

                    <div >
                         
                        <input type="hidden" name="pagename" id="pagename" value="communcation_postsms">
                        <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
                    </div>
                    <div class="col-md-9 ml-15 elevation-3" style="    background: #ebd2cf;max-height: 500px;  overflow-y: auto;scrollbar-width: thin; scrollbar-color: #a9a9a9 transparent;">
                    @include('admin.postsms_list')   
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
                    <h4 class="modal-title" id="smallModalLabel">SMS Status</h4>
                </div>
                <div class="card-body">


                    <div id="show_table_result">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsive">

                            <table class="table table-striped table-bordered tblpoststatus">
                                <thead>
                                    <tr> 
                                      <th>Name</th> 
                                      <th>Mobile</th> 
                                      <th>Class</th> 
                                      <th>Section</th>  
                                      <th>Delivered Date</th>  
                                      <th>Sent Date</th>
                                      <th>App Installed</th> 
                                    </tr>
                                </thead>
                                <tfoot>
                                      <tr><th></th><th></th><th></th>
                                          <th></th><th></th><th></th>
                                          <th></th> 
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
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
      <script>
        $('#clear_style').on('click', function () {
            $('.card-header').find('input').val('');
            $('.card-header').find('select').val('');
            filterposts();
        });
        
        function deletepostsms(id){
            $('#filter_pagename').val($('#pagename').val());
            swal({
                    title: "Do you want to delete this from your Post SMS?",
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
                        url: " {{URL::to('admin/delete/postsms')}}",
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

        function updatesmsstatus(obj, id) {
            var status = $(obj).val();
            swal({
                    title: "Are you sure you want to change?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-info",
                    cancelButtonColor: "btn-danger",
                    confirmButtonText: "Yes!",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false
                    
                    
                   
            },function(inputValue){
                if(inputValue===false) {
                      swal('Info',"Nothing done",'info');
                      
                      $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
                }else{
                        $('#filter_pagename').val($('#pagename').val());
                        var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('admin/update/postsms')}}",
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

        function opensmsstatus(pid) {
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
                    "url":"{{URL('/')}}/admin/postsmsstatus/datatables?id="+pid, 
                },
                columns: [
                    { data: 'name',  name: 'name'}, 
                    { data: 'mobile',  name: 'users.mobile'}, 
                    { data: 'class_name',  name: 'classes.class_name'}, 
                    { data: 'section_name',  name: 'sections.section_name'}, 
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
                            if(data.notify != '' && data.notify != null && data.notify.notify_date != null){
                                var tid = data.notify.notify_date; 
                                return tid;
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

                    }, 
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
                                "url":"{{URL('/')}}/admin/smsstatus_excel?id="+pid,
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'SmsStatus.xls');
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
                        url: " {{URL::to('admin/update/postsms')}}",
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
    </script>
 

@endsection

