@extends('layouts.teacher_master')
@section('comn_settings', 'active')
@section('master_postsms', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
@section('content')
 
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
            width: 70px;
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
</style>

<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel"> 
                <div class="panel-body">


            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <div class="card-header">SMS for Scholars
                        <a href="{{url('/teacher/addpostsms')}}" id="addbanner"><button class="btn btn-primary" style="float: right;">Add</button></a> 

                        <div class="row">
                            <div class="form-group col-md-3 " >
                                <label class="form-label">Search</label>
                                <input class="form-control" type="text" id="search"  />
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">From</label>
                                <input class="date_range_filter date form-control" type="text" id="datepicker_from"  />
                            </div>
                            <div class="form-group col-md-3 " >
                                <label class="form-label">To</label>
                                <input class="date_range_filter date form-control" type="text" id="datepicker_to"  />
                            </div>
                            <div class=" col-md-3">
                                <label class="form-label">Categories</label>
                                <div class="form-line">
                                    <select class="form-control" name="category_id" id="category_id" >
                                        <option value="">All</option>
                                     @if (!empty($categories))
                                         @foreach ($categories as $cat)
                                         <option value={{$cat->id}}>{{$cat->name}}</option>  
                                         @endforeach
                                     @endif
                                    </select> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div >
                         
                        <input type="hidden" name="pagename" id="pagename" value="communcation_postsms">
                        <input type="hidden" name="loadsection" id="loadsection" value=".posts .pagination_section">
                    </div>
                    <div class="col-md-6 ml-15" >
                    @include('teacher.postsms_list')   
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
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
      <script>

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
                    closeOnCancel: false
                    
                    
                   
            },function(inputValue){
                if(inputValue===false) {
                      swal('Info',"Cancelled Delete",'info');
                      
                      $( ".confirm.btn.btn-lg.btn-primary" ).trigger( "click" );
                }else{
                        $('#filter_pagename').val($('#pagename').val());
                        var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('teacher/delete/postsms')}}",
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
    </script>
 

@endsection

