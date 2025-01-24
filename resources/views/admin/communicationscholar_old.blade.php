@extends('layouts.admin_master')
@section('comn_settings', 'active')
@section('master_posts', 'active')
@section('menuopencomn', 'menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php  
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Sections', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}"> -->

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
 
  <!-- include summernote 
  <script type="text/javascript" src="/summernote-bs4.js"></script>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.css" />-->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.2/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.2/dist/summernote.min.js"></script>
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

        .cke_button__scayt {
            display: none !important;
            /* Or visibility: hidden; */
        }
        
        /*.note-popover .popover-content .dropdown-menu, .panel-heading.note-toolbar .dropdown-menu {
            min-width: 190px !important;
        }

        .note-popover .popover-content .note-color .dropdown-menu, .panel-heading.note-toolbar .note-color .dropdown-menu {
            min-width: 340px !important;
        }*/
    </style> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Create Post for Scholars
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('admin/post_new_message') }}" method="post" id="post_communication"
                            class="post_communication">

                            @csrf
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <span class="text-danger error-text title_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Message:</label>
                                <textarea type="text" id="message" name="message" class="form-control" rows="5"></textarea>
                                <span class="text-danger error-text message_error"></span>
                            </div>

                            <div class="form-group">
                                <label for="title">Title (Push Notification):</label>
                                <input type="text" maxlength="75" class="form-control" id="title_push" name="title_push" required>
                                <span class="text-danger error-text title_push_error"></span>
                            </div>
                            <div class="form-group">
                                <label>Message (Push Notification):</label>
                                <textarea name="message_push" maxlength="150" class="form-control" rows="2" required></textarea>
                                <span class="text-danger error-text message_push_error"></span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category:</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="" disabled selected>Select a category</option>
                                            @foreach ($get_category as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch">Batch:</label> <?php $acadamic_year = trim($acadamic_year); ?>
                                        <select class="form-control" id="batch" name="batch" required onchange="loadModalcontents();">
                                            <!-- <option value="2023-2024">2023-2024</option> -->
                                            <option value="">Select Batch</option>
                                            @if(!empty($get_batches))
                                                @foreach($get_batches as $batches)
                                                    @php($selected = '')
                                                    @if($acadamic_year == $batches['academic_year'])
                                                    @php($selected = 'selected')
                                                    @endif
                                                    <option value="{{$batches['academic_year']}}" {{$selected}}>{{$batches['display_academic_year']}}</option>
                                                @endforeach
                                            @endif

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

                            <div class=" imageupload justify-content-center " style="margin: 3%;">
                                <label  class="col-md-4" for="upload_image">
                                    <p>Image Attachment (jpg / jpeg / png)</p>
                                    <img src="{{asset('/public/images/image.png')}}"> 
                                    <input type="file" name="image_attachment[]" class="image" id="upload_image" multiple>   
                                    
                                </label> 

                                <label  class="col-md-4"  for="upload_image1">
                                    <p>Audio Attachment (mp3 / wav)</p>
                                    <img src="{{asset('/public/images/Audio.png')}}"> 
                                    <input type="file" name="media_attachment" class="image" id="upload_image1" >   
                                    
                                </label> 

                                <label   class="col-md-4" for="upload_image2">
                                    <p>Video Attachment (mp4 / wmv)</p>
                                    <img src="{{asset('/public/images/video.png')}}"> 
                                    <input type="file" name="video_attachment" class="image" id="upload_image2" >   
                                    
                                </label> 

                                <label   class="col-md-4" for="upload_image3">
                                    <p>Files Attachment (doc / pdf / xls / pptx)</p>
                                    <img src="{{asset('/public/images/pdf.png')}}"> 
                                    <input type="file" name="files_attachment[]" class="image" id="upload_image3" multiple>   
                                    
                                </label> 
                            </div>


                            <div class="col-md-8 d-none">
                                <div class="form-group">
                                    <label for="media_type">Media Attachment: 
                                        <span title="Image">&#128247;</span>
                                        <span title="PDF">&#128196;</span> 
                                        <span title="File">&#128266;</span>
                                    </label>
                                    <input type="file" class="form-control" id="media_attach" name="media_attach" >
                                    <span class="text-danger error-text media_attach_error"></span>
                                </div>  
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Youtube Video (ex: <b>https://www.youtube.com/watch?v=PY8xGz-lQK0</b>)</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="youtube_link"   placeholder="https://www.youtube.com/watch?v=PY8xGz-lQK0"> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="batch">Background and Text Colour:</label>
                                <select class="form-control" id="bg_color" name="bg_color" required>
                                    <option value="" disabled selected>Select Background</option>
                                    @foreach ($get_background as $theme)
                                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <!-- Hidden field to send '0' if checkbox is not checked -->
                                    <input type="hidden" name="req_ack" value="0">
                                    <input class="form-check-input" type="checkbox" value="1" name="req_ack"
                                        id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                        Request Acknowledgment
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value=""
                                        id="sendLaterCheckbox">
                                    <label class="form-check-label" for="sendLaterCheckbox">
                                        Send Later
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="scheduleInput" style="display: none;">
                                <label for="scheduleDateTime">Schedule at:</label>
                                <input type="text" name="schedule_date" class="form-control" id="datetime-picker"
                                    placeholder="Select Date and Time" required>
                            </div>

                            <button type="submit" class="btn btn-primary" id="send">Save</button>


                                <!-- Modal1 -->
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>

                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control searchInput" id="searchSection" placeholder="Search Sections..">
                                        </div> 
                                        <div class="scrollable-form exampleModalCenterscroll">
                                            @foreach($classes as $class)
                                                <div class="sectionItem">
                                                    <input type="checkbox" id="class_{{$class->id}}" name="class_post[]" value="{{$class->id}}">
                                                    <label for="class_{{$class->id}}">{{$class->class_name}} </label><br>
                                                </div>
                                            @endforeach  
                                            @foreach($get_sections as $section)
                                                <div class="sectionItem">
                                                    <input type="checkbox" id="section_{{$section->id}}" name="section_post[]" value="{{$section->id}}">
                                                    <label for="section_{{$section->id}}">{{$section->is_class_name}}-{{$section->section_name}}</label><br>
                                                </div>
                                            @endforeach  
                                            <div class="noResults" id="noSectionResults" style="display: none;">No Matching record</div>
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
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchStudent" placeholder="Search Students..">
                                            </div>

                                            <div class="form-group">
                                                <label style="padding-bottom: 10px;">Class</label>
                                                <select class="form-control course_id" name="class_id" id="class_id"
                                                        onchange="loadClassSection(this.value);loadspecificstudents(0,this.value)">
                                                    <option value="">Select Class</option>
                                                    @if (!empty($classes))
                                                        @foreach ($classes as $class)
                                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label" style="padding-bottom: 10px;">Section <span class="manstar">*</span></label>
                                                <div class="form-line"> <!-- loadClassSubjects(this.value); -->
                                                    <select class="form-control" name="section_id" id="section_dropdown" onchange="loadspecificstudents(this.value,class_id.value)">

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="scrollable-form exampleModalCenterscroll1">
                                                @foreach($get_student as $student)
                                                    <div class="studentItem" data-class_id="{{$student->class_id}}" data-section_id="{{$student->section_id}}">
                                                        <input type="checkbox" id="student_{{$student->user_id}}" name="student_post[]" value="{{$student->user_id}}">
                                                        <label for="student_{{$student->user_id}}" data-class_id="{{$student->class_id}}" data-section_id="{{$student->section_id}}">{{$student->is_student_name}}-({{$student->is_class_name}}-{{$student->is_section_name}})</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="noStudentResults" style="display: none;">No Matching record</div>
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
                                            <h5 class="modal-title" id="exampleModalLongTitle">Post Receiver</h5>

                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" class="form-control searchInput" id="searchGroup" placeholder="Search Groups..">
                                            </div>
                                            <div class="scrollable-form exampleModalCenterscroll2">
                                                @foreach($get_groups as $group)
                                                    <div class="groupItem">
                                                        <input type="checkbox" class="groupCheckbox" id="group_{{$group->id}}" name="group_post[]" value="{{$group->id}}">
                                                        <label for="group_{{$group->id}}">{{$group->group_name}}</label><br>
                                                    </div>
                                                @endforeach  
                                                <div class="noResults" id="noGroupResults" style="display: none;">No Matching record</div>
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
    $(document).ready(function() {

        $('.note-popover').css('display', 'none');

        // Function to filter list items based on search term
        function filterList(inputId, itemClass, noResultsId) {
            $(inputId).on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                var found = false;

                $(itemClass).each(function() {
                    var itemName = $(this).find('label').text().toLowerCase();
                    if (itemName.includes(searchTerm)) {
                        $(this).show();
                        found = true;
                    } else {
                        $(this).hide();
                    }
                });

                if (found) {
                    $(noResultsId).hide();
                } else {
                    $(noResultsId).show();
                }
            });
        }

        // Initialize the search functionality for groups, students, and sections
        filterList('#searchGroup', '.groupItem', '#noGroupResults');
        filterList('#searchStudent', '.studentItem', '#noStudentResults');
        filterList('#searchSection', '.sectionItem', '#noSectionResults');

        loadModalcontents();
    });  

    function loadModalcontents(){  
        var batch = $('#batch').val();
        var request = $.ajax({
            type: 'post',
            url: "{!! url('admin/post_load_contents') !!}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                batch:batch,
            },
            dataType:'json',
            encode: true
        });
         request.done(function (response) {

            if(response.status == 1){
                var html = '';
                 $.each(response.data,function(index1, val1){
                    html += '<div class="studentItem" data-class_id="'+val1.class_id+'" data-section_id="'+val1.section_id+'"> <input type="checkbox" id="student_'+val1.user_id+'" name="student_post[]" value="'+val1.user_id+'"> <label for="student_'+val1.user_id+'" data-class_id="'+val1.class_id+'" data-section_id="'+val1.section_id+'">'+val1.is_student_name+'-('+val1.is_class_name+'-'+val1.is_section_name+')</label><br> </div>';
                 });

                if(html!='') {
                    $('.exampleModalCenterscroll1').html(html);
                }
            }
            else if(response.status == 0){ 
                $('.exampleModalCenterscroll1').html('');
            }

         });

        request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });
    }

    function loadspecificstudents(section_id,class_id) {
        var itemClass = '.studentItem'; 
        $('.studentItem').hide();
        if(section_id > 0) {
            $('.studentItem[data-section_id='+section_id+']').show()
        }   else if(class_id > 0) {
            $('.studentItem[data-class_id='+class_id+']').show()
        }   else {
            $('.studentItem').show();
        }
    }
</script>


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

    <!-- <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> -->

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.23.0/ckeditor.min.js"></script> 

    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>-->

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
        flatpickr('#datetime-picker', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: new Date(),
            minDate: new Date(),
            // Add more options as needed
        });
    </script>
    <script>
        $(function() {

            $('#message').summernote({
                placeholder: '',
                tabsize: 2,
                height: 120,
                toolbar: [
                  ['style', ['style']],
                  ['font', ['bold', 'underline', 'clear']],
                  ['color', ['color']],
                  ['para', ['ul', 'ol', 'paragraph']],
                  ['table', ['table']],
                  ['insert', ['link']], 
                ]
              });
                $('.note-popover').css('display', 'none');
            /*const editorConfig = {
                toolbar: {
                    items: ['undo', 'redo', '|', 'selectAll', '|', 'bold', 'italic', '|', 'accessibilityHelp', '|', 'numberedList', 'bulletedList', '|', 'heading', '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|', 
                          'strikethrough', 'subscript', 'superscript' , 'table'  , 'link'],
                    shouldNotGroupWhenFull: false
                }, 
            };
            ClassicEditor
                .create(document.querySelector('#message'), editorConfig)
                .then(editor => { myEditor = editor; console.log(editor); })
                .catch(error => { console.error(error); }); */

            // CKEDITOR.replace('message');
            /*CKEDITOR.replace('message', {
                height: 200, // Set the height in pixels
                removePlugins: 'image,blockquote,about',  
                 scayt_autoStartup: true,
                  grayt_autoStartup: true,
                  // Limit the number of suggestions available in the context menu.
                  scayt_maxSuggestions: 3,
                  scayt_customerId: '1:Eebp63-lWHbt2-ASpHy4-AYUpy2-fo3mk4-sKrza1-NsuXy4-I1XZC2-0u2F54-aqYWd1-l3Qf14-umd',
                  scayt_sLang: 'en_US',
                  removeButtons: 'PasteFromWord'
            });*/


            $(".post_communication").on("submit", function(e) {

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
                                       location.href = "{{URL('/')}}/admin/posts";
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
