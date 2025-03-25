@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fees_structure', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')
<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Add Fee Structure', 'active' => 'active']];
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

    .scrollable-form {
        max-height: 200px;
        overflow-y: auto;
    }
    #noResults {
        color: red;
        font-weight: bold;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 50px;
    }
               
    </style>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title"> Add Fee Structure
                        </h4>
                    </div> -->
                    <div class="card-body">
                        <form action="{{ url('admin/post_fee_structure') }}" method="post" id="post_fee_structure"
                            class="post_fee_structure">

                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="position-relative" >
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Batch</label>
                                        <div class="form-group">
                                            <select class="form-control" id="batch" name="batch" required style="height:50px;"> 
                                                <option value="">Select Batch</option>
                                                @if(!empty($get_batches))
                                                    @foreach($get_batches as $batches)
                                                        <option value="{{$batches['academic_year']}}" @if($batch == $batches['academic_year']) selected @endif>{{$batches['display_academic_year']}}</option>
                                                    @endforeach
                                                @endif 
                                            </select>
                                        </div>
                                    </div>  
                                </div>
                               
                                <div class="col-md-3">
                                    <div class="position-relative" >
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Fee Category</label>
                                        <div class="form-group">
                                            <select class="form-control" id="fee_category" name="category_id" required style="height:50px;">
                                                <option value="">Select Fee Category</option>
                                                @foreach ($get_fee_category as $fee )
                                                <option value="{{$fee->id}}">{{$fee->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative" >
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Fee Type</label>
                                        <div class="form-group">
                                            <select class="form-control" id="fee_type" name="fee_type" required style="height:50px;">
                                                <option value="1">Mandatory</option>
                                                <option value="2">Variable</option>
                                                <option value="3">Optional</option>
                                            </select>
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative">
                                        <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Fee Post Type</label>
                                        <div class="form-group">
                                            <select class="form-control" id="fee_post_type" name="fee_post_type" required style="height:50px;">
                                                <option value="1">Class</option>
                                                <option value="2">Section</option>
                                                <option value="3">All</option>
                                                <option value="4">Group</option>
                                                <option value="5">Scholar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" id="class_list_container">
                                    <div class="form-group">
                                        <select class="select2" multiple="multiple" name="class_list[]" data-placeholder="Select Class" data-dropdown-css-class="select2-info" style="height:50px;"  >
                                        @foreach ($get_classes as $classes )
                                            <option value="{{$classes->id}}">{{$classes->class_name}}</option>
                                        @endforeach
                                        </select>
                                        
                                    </div>
                                   
                                </div>
                                <div class="col-md-3" id="section_list_container" style="display: none;">
                                    <div class="form-group">
                                        <select class="select2" multiple="multiple" name="section_list[]" data-placeholder="Select Section" data-dropdown-css-class="select2-info" style="height:50px;">
                                        @foreach ($get_sections as $section)
                                            <option value="{{$section->id}}">{{$section->is_class_name}}-{{$section->section_name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="group_list_container" style="display: none;">
                                    <div class="form-group">
                                        <select class="select2" multiple="multiple" name="group_list[]" data-placeholder="Select Group" data-dropdown-css-class="select2-info" style="height:50px;">
                                        @foreach ($get_groups as $group)
                                            <option value="{{$group->id}}">{{$group->group_name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="scholar_list_container" style="display: none;">
                                    <div class="form-group">
                                        <select class="select2" name="scholar_list" data-placeholder="Select Scholar" data-dropdown-css-class="select2-info" style="height:50px;">
                                        @foreach ($get_student as $student)
                                            <option value="{{$student->user_id}}">{{$student->name}} - {{$student->mobile}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                        <div id="payment-types-container"> 
                                            <div class="payment-type-row">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="position-relative" >
                                                            <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Fee Term</label>
                                                            <div class="form-group">
                                                               <select class="form-control" id="fee_term_id" name="fee_term[]" required style="height:50px;">
                                                                   <option value="">Select Term</option>
                                                                   @if(!empty($get_fee_terms))
                                                                        @foreach($get_fee_terms as $terms)
                                                                            <option value="{{$terms->id}}">{{$terms->name}}</option>
                                                                        @endforeach
                                                                   @endif
                                                                </select>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="position-relative" >
                                                            <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Fee Item</label>
                                                            <div class="form-group">
                                                               <select class="form-control" id="fee_filters" name="fee_item[]" id="pay_type" required style="height:50px;">
                                                                   
                                                                </select>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="position-relative" >
                                                            <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Gender</label>
                                                            <div class="form-group">
                                                               <select class="form-control payment-type-select" name="gender[]" id="pay_type" required style="height:50px;">
                                                                    <option value="1" selected>All</option>
                                                                    <option value="2">Boys</option>
                                                                    <option value="3">Girls</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="position-relative" >
                                                            <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Amount</label>
                                                            <div class="form-group">
                                                                <input type="number" min="1" max="9999999" class="form-control floating-label-input amount-input" placeholder="Amount" name="fee_amount[]" id='pay_amount' required style="height:50px;">
                                                            </div>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="position-relative">
                                                            <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Due Date</label>
                                                            <div class="form-group">
                                                                <input type="date" name="due_date[]" class="form-control" placeholder="Select Date" required style="background-color: white;height:50px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group row">
                                                            <div class="col-md-2 mr-3">
                                                                <button type="button" class="btn btn-success add-payment-type"><i class="fas fa-plus"></i></button>
                                                            </div>
                                                            <div class="col-md-2 ml-3">
                                                                <button type="button" class="btn btn-danger delete-payment-type"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>              
                                            </div>
                                        </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
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


<script src="{{asset('public/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() { 
            $('.select2').select2(); 
        });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.datetime-picker', {
            enableTime: false   ,
            dateFormat: "Y-m-d",
            defaultDate: new Date(),
            // Add more options as needed
        });
    });
</script>
  
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentTypesContainer = document.getElementById('payment-types-container');
    
        function initializeDatepickers() {
            flatpickr('.datetime-picker', {
                enableTime: false,
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });
        }
    
        // Initial call to initialize datepickers on page load
        initializeDatepickers();
    
        paymentTypesContainer.addEventListener('click', function(event) {
            const clickedButton = event.target.closest('button');
            if (!clickedButton) return;
    
            if (clickedButton.classList.contains('delete-payment-type')) {
                if (paymentTypesContainer.querySelectorAll('.payment-type-row').length > 1) {
                    clickedButton.closest('.payment-type-row').remove();
                } else {
                    clickedButton.disabled = true;
                    clickedButton.classList.add('disabled');
                }
            } else if (clickedButton.classList.contains('add-payment-type')) {
                const newPaymentTypeRow = paymentTypesContainer.querySelector('.payment-type-row').cloneNode(true);
    
                newPaymentTypeRow.querySelectorAll('input, select, textarea').forEach(function(element) { 
                    element.value = '';
                    element.classList.remove('flatpickr-input');
                });

                // Set the default value of the select element to "All"
                newPaymentTypeRow.querySelector('.payment-type-select').value = "1";
    
                paymentTypesContainer.appendChild(newPaymentTypeRow);
    
                // Reinitialize datepickers for the newly added row
                //initializeDatepickers();
    
                const deleteButtons = paymentTypesContainer.querySelectorAll('.delete-payment-type');
                deleteButtons.forEach(function(button) {
                    button.disabled = false;
                    button.classList.remove('disabled');
                });
            }
        });
    });
    </script>

{{-- <script>
    $(document).ready(function(){
        $('#fee_category').on('change', function(){
            var selected_category = $(this).val();
    
            $.ajax({
                type: 'get',
                url: " {{ URL::to('/admin/filter_fee_item') }}",
                dataType: 'json',
                data: {'selected_category': selected_category},
                success: function(data){

                    $('#fee_filters').empty();

                    if (data.filter_data.length === 0) {
                        $('#fee_filters').append('<option value="" readonly required>No items found</option>');
                    } else {
                 //   $('#fee_filters').html('<option value="" readonly required>Payment Against</option>');
                    $.each(data.filter_data, function(key, value) {
                        $("#fee_filters").append('<option value="' + value.id + '"> ' + value.item_name + '</option>');
                    });

                }
                }
            });
        });
    });
</script>--}}

    
<script> 
    $(document).ready(function(){
        var initialCategory = ''; // Initialize empty initially
        $('#fee_category').on('change', function(){
            var selected_category = $(this).val();
            var previous_category = $('#fee_category').data('previous');
            var selected_category = $(this).val();
        var previous_category = $('#fee_category').data('previous');
        if (initialCategory === '') {
            initialCategory = selected_category; // Store initial category if it's empty
        }
            if ($('#payment-types-container .payment-type-row').length > 1) {
                swal({
                    title: "Are you sure?",
                    text: "Existing data will be cleared.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-info",
                    cancelButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                },
                function(inputValue){
                    if(inputValue === false) {
                        swal('Cancelled', "Your data is safe", 'info');
                        $('#fee_category').val(initialCategory);
                    } else {
                        // Clear all rows except the first one
                        $('#payment-types-container .payment-type-row').not(':first').remove();
                        // Clear the remaining first row's inputs
                        $('#payment-types-container .payment-type-row:first').find('input, select, textarea').val('');
                        // Proceed with the AJAX call
                        filterFeeItems(selected_category);
                    }
                });
            } else {
                // Proceed with the AJAX call
                filterFeeItems(selected_category);
            }
            $('#fee_category').data('previous', selected_category);
        });
        function filterFeeItems(selected_category) {
            $.ajax({
                type: 'get',
                url: "{{ URL::to('/admin/filter_fee_item') }}",
                dataType: 'json',
                data: {'selected_category': selected_category},
                success: function(data){
                    $('#fee_filters').empty();
                    if (data.filter_data.length === 0) {
                        $('#fee_filters').append('<option value="" readonly required>No items found</option>');
                    } else {
                        $.each(data.filter_data, function(key, value) {
                            $("#fee_filters").append('<option value="' + value.id + '"> ' + value.item_name + '</option>');
                        });
                    }
                }
            });
        }
    });
    
</script>
    
<script>
    $(function() {
   
       $(".post_fee_structure").on("submit", function(e) {
           e.preventDefault();

    
           // Submit the form using AJAX
           var formData = new FormData(this);
            var feeTerms = formData.getAll('fee_term[]');
            var feeItems = formData.getAll('fee_item[]');
            var genders = formData.getAll('gender[]');
            // Function to check for duplicate pairs in two arrays
            /*function hasDuplicatePairs(arr1, arr2) {
                let pairs = arr1.map((item, index) => item + '-' + arr2[index]);
                return (new Set(pairs)).size !== pairs.length;
            }*/

            /*function hasDuplicatePairs(arr1, arr2, arr3) {
                let pairSet = new Set();
                let duplicates = [];

                // Loop through each index in the arrays
                for (let i = 0; i < arr1.length; i++) {
                    // Create pairs for arr1-arr2, arr1-arr3, and arr2-arr3
                    let pair1 = `${arr1[i]},${arr2[i]}`;
                    let pair2 = `${arr1[i]},${arr3[i]}`;
                    let pair3 = `${arr2[i]},${arr3[i]}`;

                    // Check each pair for duplicates
                    [pair1, pair2, pair3].forEach(pair => {
                        if (pairSet.has(pair)) {
                            // If the pair is already in the set, it's a duplicate
                            duplicates.push(pair);
                        } else {
                            // Otherwise, add the pair to the set
                            pairSet.add(pair);
                        }
                    });
                }

                return duplicates;
            }*/

            function findDuplicateCombinations(arr1, arr2, arr3) {
                let combinations = {}; // Object to store unique combinations as keys
                let duplicates = [];   // Array to store any duplicate combinations

                // Assume the arrays have the same length, iterate over each index
                for (let i = 0; i < arr1.length; i++) {
                    // Create a unique identifier for each combination
                    let combo = `${arr1[i]}-${arr2[i]}-${arr3[i]}`;
                    
                    // Check if this combination already exists in the combinations object
                    if (combinations[combo]) {
                        duplicates.push(combo); // If exists, add to duplicates
                    } else {
                        combinations[combo] = true; // If not, mark this combination as seen
                    }
                }

                // Return true if duplicates are found, or list of duplicate combinations
                return duplicates.length > 0 ? duplicates : false;
            }

            /*// Check for duplicate fee_item and gender pairs
            duplicates = hasDuplicatePairs(feeTerms, feeItems, genders);*/
            duplicates = findDuplicateCombinations(feeTerms, feeItems, genders);
            console.log(duplicates)
            if (duplicates.length > 0) {
                swal('Oops', 'Same Fee Term, Item and Gender combination cannot be repeated.', 'warning');
                return; // Prevent form submission
            }

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
                                   location.reload();
                               }
                            ); 
                            
                           /*swal('Success', response.message, 'success');
                             window.location.reload();*/
                       }
                   }
               }
           });
        });
    });
      
</script>   

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const feePostType = document.getElementById('fee_post_type');
        const classListContainer = document.getElementById('class_list_container');
        const sectionListContainer = document.getElementById('section_list_container');
        const groupListContainer = document.getElementById('group_list_container');
        const scholarListContainer = document.getElementById('scholar_list_container');

        const classListSelect = classListContainer.querySelector('select');
        const sectionListSelect = sectionListContainer.querySelector('select');
        const groupListSelect = groupListContainer.querySelector('select');
        const scholarListSelect = scholarListContainer.querySelector('select');

        function resetSelect(selectElement) {
            selectElement.selectedIndex = -1; // Deselect all options
            // Alternatively, if you want to clear all selections for a multiple select:
            Array.from(selectElement.options).forEach(option => option.selected = false);
        }

        feePostType.addEventListener('change', function () {
            const selectedValue = this.value;

            // Hide all containers initially
            classListContainer.style.display = 'none';
            sectionListContainer.style.display = 'none';
            groupListContainer.style.display = 'none';
            classListSelect.disabled = false;

            // Reset all select elements
            resetSelect(classListSelect);
            resetSelect(sectionListSelect);
            resetSelect(groupListSelect);
            resetSelect(scholarListSelect);

            if (selectedValue == '1') {
                classListContainer.style.display = 'block';
            } else if (selectedValue == '2') {
                sectionListContainer.style.display = 'block';
            } else if (selectedValue == '3') {
                classListContainer.style.display = 'block';
                classListSelect.disabled = true;
            } else if (selectedValue == '4') {
                groupListContainer.style.display = 'block';
            } else if (selectedValue == '5') {
                scholarListContainer.style.display = 'block';
            }
        });

        // Trigger change event on page load to set the initial state
        feePostType.dispatchEvent(new Event('change'));
    });
</script>
@endsection
