@extends('layouts.teacher_master')
@section('communication_settings', 'active')
@section('master_scholars', 'active')
@section('menuopenc', 'active menu-is-opening menu-open') 
<?php
$breadcrumb = [['url' => URL('/teacher/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Sections', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">
    <style>
        .dropdown-menu.show {
            display: block;
            width: 100%;
            top: 30px !important;
            left: auto !important;
            padding: 20px;
        }

        .checkbox input[type="checkbox"] {
            width: 20px !important;

        }

        .select2-container--default .select2-selection--single {
            height: 45px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-top: 8px;
        }

        .select2-selection__choice {
            color: #000 !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #f8fafa;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }

        .row.merged20 {
            margin: 0px 0px !important;
        }

        .sidecoderight {
            padding-top: 40px !important;
        }

        body {
            margin-left: 0px !important;
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

            .vanilla-calendar {
                width: 100% !important;
            }
        }

        .option-container {
            padding: 10px;
            margin: 5px;
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
    </style> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Edit Post for Scholars
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('teacher/post_update_message') }}" method="post" id="post_communication"
                            class="post_communication">
                            <input type="hidden" name="post_id" id="post_id" value="{{$post['id']}}">
                            @csrf
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" required value="{{$post['title']}}">
                                <span class="text-danger error-text title_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Message:</label>
                                <textarea type="text" id="message" name="message" class="form-control" required>{{$post['message']}}</textarea>
                                <span class="text-danger error-text message_error"></span>
                            </div>

                            <div class="form-group">
                                <label for="title">Title (Push Notification):</label>
                                <input type="text" maxlength="75" class="form-control" id="title_push" name="title_push" required value="{{$post['title_push']}}">
                                <span class="text-danger error-text title_push_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Message (Push Notification):</label>
                                <textarea name="message_push" maxlength="150" class="form-control" rows="2" required> {{$post['message_push']}}</textarea>
                                <span class="text-danger error-text message_push_error"></span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category:</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="" disabled selected>Select a category</option>
                                            @foreach ($get_category as $category)
                                                @php($selected = '')
                                                @if($post['category_id'] == $category->id)
                                                @php($selected = 'selected')
                                                @endif
                                                <option value="{{ $category->id }}" {{$selected}} >{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch">Batch:</label>
                                        <select class="form-control" id="batch" name="batch" required>
                                            @php($selected = '')
                                            @if($post['batch'] == '2023-2024')
                                            @php($selected = 'selected')
                                            @endif
                                            <option value="2023-2024" {{$selected}} >2023-2024</option> 
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Post For:</label>  
                                    
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <div class="option-container">
                                        <label class="btn btn-outline-primary">
                                            <input data-toggle="modal" data-target="#exampleModalCenter" type="radio"
                                                name="post_type" autocomplete="off" value="1" @if($post['post_type'] == 1) checked @endif> Class & Sections
                                        </label>


                                    </div>
                                    <div class="option-container">
                                        <label class="btn btn-outline-primary">
                                            <input data-toggle="modal" data-target="#exampleModalCenter1" type="radio"
                                                name="post_type" autocomplete="off" value="2" @if($post['post_type'] == 2) checked @endif>
                                            Specific Scholars
                                        </label>
                                    </div>
                                    <div class="option-container">
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="post_type" autocomplete="off" value="3" @if($post['post_type'] == 3) checked @endif> All
                                            Scholars
                                        </label>
                                    </div>
                                    <div class="option-container">
                                        <label class="btn btn-outline-primary">
                                            <input data-toggle="modal" data-target="#exampleModalCenter2" type="radio"
                                                name="post_type" autocomplete="off" value="4" @if($post['post_type'] == 4) checked @endif> Group
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="media_type">Media Attachment: 
                                        <span title="Image">&#128247;</span>
                                        <span title="PDF">&#128196;</span>
                                        <span title="Video">&#127916;</span>
                                        <span title="File">&#128190;</span>
                                    </label>
                                    <input type="file" class="form-control" id="media_attach" name="media_attach" >
                                    <span class="text-danger error-text media_attach_error"></span>
                                </div>
                                
                            </div>

                            <div class="form-group">
                                <label for="batch">Background and Text Colour:</label>
                                <select class="form-control" id="bg_color" name="bg_color" required>
                                    <option value="" disabled selected>Select Background</option>
                                    @foreach ($get_background as $theme)
                                        @php($selected = '')
                                        @if($post['background_id'] == $theme->id)
                                        @php($selected = 'selected')
                                        @endif
                                        <option value="{{ $theme->id }}" {{$selected}}>{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <!-- Hidden field to send '0' if checkbox is not checked -->
                                    <input type="hidden" name="req_ack" value="0">
                                    <input class="form-check-input" type="checkbox" value="1" name="req_ack"
                                        id="defaultCheck1" @if($post['request_acknowledge'] == 1) checked @endif>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Request Acknowledgment
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value=""
                                        id="sendLaterCheckbox" checked>
                                    <label class="form-check-label" for="sendLaterCheckbox">
                                        Send Later
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="scheduleInput" style="display: none;">
                                <label for="scheduleDateTime">Schedule at:</label>
                                <input type="text" name="schedule_date" class="form-control" id="datetime-picker"
                                    placeholder="Select Date and Time" required value="{{$post['notify_datetime']}}">
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>


                                <!-- Modal1 -->
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Post Recevier</h5>

                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="exampleInputEmail1"
                                                aria-describedby="emailHelp" placeholder="Search..">
                                        </div>
                                        <div class="form-group mt-3">
                                            <select class="form-control" id="exampleFormControlSelect1">
                                                <option selected>Choose...</option>
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4</option>
                                            </select>
                                        </div>
                                        <div class="scrollable-form">
                                            @foreach($get_sections as $section)
                                            <input type="checkbox" id="section_{{$section->id}}" name="section_post[]" value="{{$section->id}}">
                                            <label for="">{{$section->is_class_name}}-{{$section->section_name}}</label><br>
                                            @endforeach      
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtnSection">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="doneBtnSection">Done</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!-- Modal2 -->
                            <div class="modal fade" id="exampleModalCenter1" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Recevier</h5>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    aria-describedby="emailHelp" placeholder="Search..">
                                            </div>

                                            <div class="scrollable-form">
                                                @foreach($get_student as $student)
                                                <input type="checkbox" id="student_{{$student->user_id}}" name="student_post[]" value="{{$student->user_id}}">
                                                <label for=""> {{$student->is_student_name}}-({{$student->is_class_name}}-{{$student->is_section_name}})</label><br>
                                                @endforeach  
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtnStudent">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="doneBtnStudent">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal3 -->
                            <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Recevier</h5>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="searchGroup" aria-describedby="emailHelp" placeholder="Search..">
                                            </div>
                                            <div class="scrollable-form">
                                                @foreach($get_groups as $group)
                                                <input type="checkbox" class="groupCheckbox" id="group_{{$group->id}}" name="group_post[]" value="{{$group->id}}">
                                                <label for="group_{{$group->id}}" class="groupName"> {{$group->group_name}}</label><br>
                                                @endforeach  
                                                <div id="noResults" style="display: none;">No Matching record</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeBtn">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="doneBtn">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    
    </section>


@endsection

@section('scripts')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store the initial state of checkboxes
        let initialCheckboxState = [];
    
        // Save the initial state when the modal is opened
        $('#exampleModalCenter').on('shown.bs.modal', function () {
            initialCheckboxState = [];
            $('input[name="section_post[]"]').each(function() {
                initialCheckboxState.push($(this).prop('checked'));
            });
        });
    
        // Handle the Done button click
        document.getElementById('doneBtnSection').addEventListener('click', function() {
            $('#exampleModalCenter').modal('hide');
        });
    
        // Handle the Close button click
        document.getElementById('closeBtnSection').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="section_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleModalCenter').modal('hide');
        });
    });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store the initial state of checkboxes
        let initialCheckboxState = [];
    
        // Save the initial state when the modal is opened
        $('#exampleModalCenter1').on('shown.bs.modal', function () {
            initialCheckboxState = [];
            $('input[name="student_post[]"]').each(function() {
                initialCheckboxState.push($(this).prop('checked'));
            });
        });
    
        // Handle the Done button click
        document.getElementById('doneBtnStudent').addEventListener('click', function() {
            $('#exampleModalCenter1').modal('hide');
        });
    
        // Handle the Close button click
        document.getElementById('closeBtnStudent').addEventListener('click', function() {
            // Uncheck all checkboxes
            $('input[name="student_post[]"]').each(function() {
                $(this).prop('checked', false);
            });
            $('#exampleModalCenter1').modal('hide');
        });
    });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store the initial state of checkboxes
            let initialCheckboxState = [];
        
            // Save the initial state when the modal is opened
            $('#exampleModalCenter2').on('shown.bs.modal', function () {
                initialCheckboxState = [];
                $('input[name="group_post[]"]').each(function() {
                    initialCheckboxState.push($(this).prop('checked'));
                });
            });
        
            // Handle the Done button click
            document.getElementById('doneBtn').addEventListener('click', function() {
                $('#exampleModalCenter2').modal('hide');
            });
        
            // Handle the Close button click
            document.getElementById('closeBtn').addEventListener('click', function() {
                // Uncheck all checkboxes
                $('input[name="group_post[]"]').each(function() {
                    $(this).prop('checked', false);
                });
                $('#exampleModalCenter2').modal('hide');
            });
        });
        </script> 
    



    <script src="{{ asset('public/js/select2.full.min.js') }}"></script>

    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            $('#sendLaterCheckbox').change(function() {
                if ($(this).is(':checked')) {
                    $('#scheduleInput').show();
                } else {
                    $('#scheduleInput').hide();
                }
            });
        });
    </script>

    <script>
        $('#scheduleInput').show();
        flatpickr('#datetime-picker', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: "{{$post['notify_datetime']}}",
            // Add more options as needed
        });
    </script>
    <script>
        $(function() {

            CKEDITOR.replace('message', {
                height: 100 // Set the height in pixels
            });


            $(".post_communication").on("submit", function(e) {

                e.preventDefault();

                var data = new FormData(this);
                data.set('message', CKEDITOR.instances['message'].getData());

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
                                       location.href = "{{URL('/')}}/teacher/posts";
                                   }
                                );

 

                            }

                        }
                    }
                });
            });
        });
    </script>

@endsection
