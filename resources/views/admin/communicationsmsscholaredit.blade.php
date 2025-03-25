@extends('layouts.admin_master')
@section('communication_settings', 'active')
@section('master_sms_scholars', 'active')
@section('menuopenc', 'active menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Sections', 'active' => 'active']];
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
            padding: 1px;
            margin: 2px;
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

    
        .border {
            border: 1px solid #ced4da; /* Border color */
            border-radius: 0.25rem; /* Border radius */
        }

        .abs{
            top: -10px !important;
            left: 12px !important;
        }


        #dynamicContent input {
        margin-bottom: 5px; /* Adjust spacing between input boxes */
    }
                            
    </style>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title"><i class="fas fa-plus"></i> Edit SMS for Scholars
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        $content_vars = $post['content_vars'];
                        if(!empty($content_vars)) {
                            $content_vars = unserialize($content_vars); 
                        } else {
                            $content_vars = [];
                        }
                        ?>
                        <form action="{{ url('admin/post_update_sms_scholar') }}" method="post" id="post_communication_sms_scholar"
                            class="post_communication_sms_scholar">
                            <input type="hidden" name="post_id" id="post_id" value="{{$post['id']}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="template">Template:</label>
                                        <select class="form-control" id="template" name="template" required onchange="loadtemplatecontent();">
                                            <option value="" disabled>Select Template</option>
                                            @foreach ($get_template as $template)
                                                @php($selected = '')
                                                @if($post['template_id'] == $template->id)
                                                @php($selected = 'selected')
                                                @endif
                                                <option value="{{ $template->id }}" data-content="{{ $template->content }}"  {{$selected}} >{{ $template->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                                <option value="{{ $category->id }}" {{$selected}}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" id="hidecontent">
                                       
                                        <textarea id="hidee" class="form-control" rows="5" readonly>Content</textarea>
                                    </div>
                                    <div class="form-group" id="contentTextareaDiv" style="display: none;">
                                        <label for="dummycontent">Content:</label>
                                        <div id="dynamicContent"></div>
                                    </div>
                                    <input type="hidden" id="final_content" value="" name="final_content">
                                </div>

                                {{-- <div class="col-md-6">
                                    <div class="form-group" id="hidecontent">
                                       
                                        <textarea id="hidee" class="form-control" rows="5" readonly></textarea>
                                    </div>
                                </div> --}}
                                
                                <div class="col-md-6"></div>
                               
                                <!-- <div class="col-md-6">
                                    <label>Post For:</label>
                                    <div class="form-group">
                                       
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <div class="option-container">
                                                <label class="btn btn-outline-primary">
                                                    <input data-toggle="modal" data-target="#exampleModalCenter" type="radio"
                                                        name="post_type" autocomplete="off" value="1" checked> Class & Sections
                                                </label>
        
        
                                            </div>
                                            <div class="option-container">
                                                <label class="btn btn-outline-primary">
                                                    <input data-toggle="modal" data-target="#exampleModalCenter1" type="radio"
                                                        name="post_type" autocomplete="off" value="2">
                                                    Specific Scholars
                                                </label>
                                            </div>
                                            <div class="option-container">
                                                <label class="btn btn-outline-primary">
                                                    <input type="radio" name="post_type" autocomplete="off" value="3"> All
                                                    Scholars
                                                </label>
                                            </div>
                                            <div class="option-container">
                                                <label class="btn btn-outline-primary">
                                                    <input data-toggle="modal" data-target="#exampleModalCenter2" type="radio"
                                                        name="post_type" autocomplete="off" value="4"> Group
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                
                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Smart Sms</label>
                                        <div class="form-group border p-3">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="smart_yes" value="1" name="smart_sms" class="custom-control-input" checked>
                                                <label class="custom-control-label" for="smart_yes">Yes</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="smart_no" value="0" name="smart_sms" class="custom-control-input">
                                                <label class="custom-control-label" for="smart_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative ">
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Send Type</label>
                                        <div class="form-group border p-3">
                                            
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="immediate"  class="custom-control-input" value="1" name="send_type" checked>
                                                        <label class="custom-control-label" for="immediate">Immediate</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="sendLater"  class="custom-control-input" name="send_type" value="2">
                                                        <label class="custom-control-label" for="sendLater">Send Later</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="form-group" id="scheduleInput" style="display: none;">
                                            <label for="scheduleDateTime">Schedule at:</label>
                                            <input type="text" name="schedule_date" class="form-control" id="datetime-picker" placeholder="Select Date and Time" required>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch">Batch:</label>
                                        <select class="form-control" id="batch" name="batch" required>
                                            <option value="2023-2024">2023-2024</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-italic text-bold">** Credit Required:0, Credit Balance:33 </p>
                                </div>
                            </div>
                           
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
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
    



  

    <script>
        $(document).ready(function() {
            $('input[name="send_type"]').change(function() {
                if ($('#sendLater').is(':checked')) {
                    $('#scheduleInput').show();
                } else {
                    $('#scheduleInput').hide();
                }
            });
        });
    </script>

    <script>
        flatpickr('#datetime-picker', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: new Date(),
            // Add more options as needed
        });
    </script>
  

<script>
    $(function() {
    
        $(".post_communication_sms_scholar").on("submit", function(e) {
            e.preventDefault();

            // Get the original content
            var originalContent = $('#template option:selected').data('content');
            
            // Get the values from input boxes
            var inputValues = $('#dynamicContent input').map(function() {
                return this.value;
            }).get();

            // Replace '#' symbols in the original content with the input values
            var updatedContent = originalContent;
            for (var i = 0; i < inputValues.length; i++) {
                updatedContent = updatedContent.replace('#', inputValues[i]);
            }

        
            // Assign the updated content to a hidden input field
            $('#final_content').val(updatedContent);

            // Submit the form using AJAX
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                    $("#send").text('Processing..');
                    $("#send").prop('disabled', true);
                },
                success: function(response) {
                    if (response.status == 0) {
                        $("#send").text('Save');
                        $("#send").prop('disabled', false);
                        swal('Oops', response.message, 'warning');
                    } else {
                        if (response.status == 1) {
                            swal({
                                   title: "Success", 
                                   text: response.message, 
                                   type: "success"
                                 },
                               function(){ 
                                   location.href = "{{URL('/')}}/admin/postsms";
                               }
                            ); 
                        }
                    }
                }
            });
        });

        // This script handles the dynamic content generation when the template changes
        //document.getElementById('template').addEventListener('change', function() {
        /*function loadtemplatecontent() {
            $('#hidee').hide();

            var selectedOption = $('#template  option:selected'); // this.options[this.selectedIndex];
            var content = selectedOption.attr('data-content');  // selectedOption.getAttribute('data-content');
            // Parse the content to replace '#' symbols with input boxes
            var parsedContent = parseContent(content);
            // Set the parsed content to the content container
            document.getElementById('dynamicContent').innerHTML = parsedContent;
            // Show the content textarea div
            document.getElementById('contentTextareaDiv').style.display = 'block';
        }*/

        //});

        function parseContent(content) {
            // Replace '#' symbols with input boxes
            return content.split('#').join('<input type="text" class="form-control" style="width: 200px;" placeholder="Input" >');
        }
    });



        function loadtemplatecontent() {
            $('#hidee').hide();

            var selectedOption = $('#template  option:selected'); // this.options[this.selectedIndex];
            var text = selectedOption.attr('data-content'); 

            var selval = $('#template').val(); selval = parseInt(selval);
            var postval = <?php echo $post['template_id']; ?>;  postval = parseInt(postval);

            if(selval == postval){
                var values = <?php echo json_encode($content_vars); ?>; //["John", "Math", "Algebra", "John1", "Math1", "Algebra1"]; // Array of values for input boxes 
            }   else {
                var values = [];
            }
            var parts = text.split("#");
            
            var newHtml = ""; var valcount = values.length;
            for (let i = 0; i < parts.length - 1; i++) {
                if(valcount > 0) {
                    newHtml += parts[i] + `<input type="text" class="form-control" style="width: 200px;" placeholder="Input"  value="${values[i]}" name="vars[]"  />`;
                } else {
                    newHtml += parts[i] + `<input type="text" class="form-control" style="width: 200px;" placeholder="Input" name="vars[]"  />`;
                }
            }
            newHtml += parts[parts.length - 1]; // Append the last part of the text
            
            document.getElementById('dynamicContent').innerHTML = newHtml;
            document.getElementById('contentTextareaDiv').style.display = 'block';

        }



        @if($post['template_id']>0)
        loadtemplatecontent();
        @endif

</script>

@endsection
