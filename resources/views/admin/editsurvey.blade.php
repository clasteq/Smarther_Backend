@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_survey', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
//$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Add Post', 'active' => 'active']];
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?>
@section('content')
@if((isset($session_module['Posts']) && ($session_module['Posts']['add'] == 1)) || ($user_type == 'SCHOOL'))
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

  <!-- include libraries BS
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.css" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.5/umd/popper.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.js"></script> --> 
  <style>
        .row.merged20 {
            margin: 0px 0px !important;
        }

        .sidecoderight {
            padding-top: 40px !important;
        }

        .nnsec {
            margin-left: -14px;
            margin-right: 10px;
            border-right: 1.5px solid #ecebeb85;
            padding-top: 40px !important;
        }

        @media screen and (max-width: 700px) {
            .nnsec {
                margin-left: 0px !important;
                margin-right: 0px !important;
                border-right: 0px solid #ecebeb85 !important;
                padding-top: 20px !important;
            }

            .row.merged20 {
                padding: 0px 0px !important;
            } 
        }

        .btn input[type="radio"] {
            display: none;
        }

        .scrollable-form {
            height: 200px;
            /* Adjust height as needed */
            overflow-y: scroll;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .scrollable-form {
            max-height: 200px;
            overflow-y: auto;
        }
        #noResults {
            color: red;
            font-weight: bold;
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

        .imgatt {
            width: 30px; height:30px;  cursor: pointer;
        }

        .fileatt {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        /*.fileatt + .imgatt {
            font-size: 1.25em;
            font-weight: 700;
            color: white;
            /*background-color: #007bff;* /
            display: inline-block;
        }

        .fileatt:focus + .imgatt,
        .fileatt + .imgatt:hover {
            /*background-color: #007bff;* /
        }*/


        .fileatt + img + label {
            font-size: 1em; 
            color: #000;
            background-color: #fff;
            display: inline-block;
            cursor: pointer;  
            padding: 2px;  
            border-radius: 5px;
            font-weight: 400 !important;
        }

        .fileatt:focus + img + label,
        .fileatt +  img + label:hover {
            background-color: #007bff;
        }

        .offerolympiaimg {
            margin-right: 3px !important;
            padding: 32px !important;
            color: #fff !important;
            min-height:100% !important;
            max-height:100% !important;
            max-width: 100% !important;
            max-width: 100% !important;
            overflow-y: auto;
        }
  </style> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Update Survey
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($survey) && !empty($survey))
                        <form action="{{ url('admin/update/surveypost') }}" method="post" id="post_survey"
                            class="post_survey">
                            <input type="hidden" name="id" value="{{$survey['id']}}">
                            @csrf
                            <div class="form-group">
                                <label for="title">Question:</label>
                                <input type="text" class="form-control" name="survey_question" required minlength="3" maxlength="250" value="{{$survey['survey_question']}}"> 
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 1:</label>
                                <input type="text" class="form-control" name="survey_option1" required minlength="3" maxlength="250" value="{{$survey['survey_option1']}}">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 2:</label>
                                <input type="text" class="form-control" name="survey_option2" required minlength="3" maxlength="250" value="{{$survey['survey_option2']}}">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 3:</label>
                                <input type="text" class="form-control" name="survey_option3"   minlength="3" maxlength="250" value="{{$survey['survey_option3']}}">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Option 4:</label>
                                <input type="text" class="form-control" name="survey_option4"   minlength="3" maxlength="250" value="{{$survey['survey_option4']}}">
                            </div>
                            <div class="form-group col-md-6 float-left">
                                <label>Expiry Date:</label>
                                <input type="date" class="form-control" name="expiry_date" required min="{{date('Y-m-d')}}" value="{{$survey['expiry_date']}}">
                            </div>  

                            <button type="submit" class="btn btn-primary float-right" id="send">Save</button>
 
 
                        </form>
                        @else 
                        <div class="col-md-12">Invalid Survey</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    
    </section>
@else 
@include('admin.notavailable') 
@endif
@endsection
@section('scripts') 
 
    <script>
        $(function() {
 

            $(".post_survey").on("submit", function(e) {

                e.preventDefault();

                var data = new FormData(this);
                //data.set('message', CKEDITOR.instances['message'].getData());
                //const data = editor.getData();
                //data.set('message', myEditor.getData());

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: data,
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(document).find('span.error-text').text('');
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

                                //  $(document).find('span.error-text').text('');

                                swal({
                                       title: "Success", 
                                       text: response.message, 
                                       type: "success"
                                     },
                                   function(){ 
                                       location.href = "{{URL('/')}}/admin/survey";
                                   }
                                );

 

                            } else {
                                swal('Oops',response.message,'warning');
                            }

                        }
                    }
                });
            });
        });

   
    </script>

@endsection

