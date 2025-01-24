@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fee_summary', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')



<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Fee Summary', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">
    <style>

    .abs {
        top: -10px !important;
        left: 12px !important;

    }

    .collectionprofile img {
        height: auto;
        width: 2.1rem;
    }

    .border-box {
        border: 1px solid #ccc;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.3);
        padding: 2px;
        border-radius: 5px;
        margin-left: px;
        width: 100%;
    }

    .fees {}

    .fees img {
        height: auto;
        width: 2.1rem;
    }

    .schoolproducts {
        margin-left: 20px;

    }

    .schoolproducts p {
        background-color: rgb(212, 6, 6);
        padding: 8px;
        border-radius: 50px;
        color: white;
    }

    .feescollection {
        display: flex;
        justify-content: flex-end;
    }

    .feescollection i {
        font-size: 20px;
    }

    .feesborder {
        border: 1px solid #ccc;

        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.3);
        padding: 10px;
        border-radius: 5px;
    }

    .termfees span {
        color: rgb(165, 165, 165)
    }

    .totalcollection i {
        background-color: rgb(179, 215, 231);
        padding: 10px;
        border-radius: 60%;
    }

    .concen i {
        background-color: rgb(243, 102, 77);
        padding: 10px;
        border-radius: 60%;
    }

    .paid i {
        background-color: rgb(112, 233, 88);
        padding: 10px;
        border-radius: 60%;
    }

    .balance i {
        background-color: rgb(233, 159, 23);
        padding: 10px;
        border-radius: 60%;
    }

    .radiocheck {
        padding-left: 1.25rem !important;
    }

    .name_filter{

        position: absolute;
        background: white;
        border: 1px solid #ccc;
        z-index: 1000;
        width:100%;

    }

    </style>


    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Fee Summary
                        </h4>
                    </div>

                        <div class=" d-flex justify-content-center mt-3 mb-3">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <label
                                        class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Batch</label>
                                    <div class="form-group">
                                        <select class="form-control" id="batchSelect" name="batch" required style="height:50px;">
                                            <option value="">Select Batch</option>
                                            @if(!empty($get_batches))
                                                @foreach($get_batches as $batches)
                                                    <option value="{{$batches['academic_year']}}">{{$batches['display_academic_year']}}</option>
                                                @endforeach
                                            @endif 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Class
                                        Filter</label>
                                    <div class="form-group">
                                        <select class="form-control" id="classSelect" name="batch" required style="height:50px;">
                                        <option value="">Class Filter</option>
                                        @foreach ($get_classes as $classes )
                                            <option value="{{$classes->id}}">{{$classes->class_name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <label class="d-block position-absolute abs top-0 start-50 translate-middle-x bg-white px-3">Scholar Name</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="studentName" placeholder="Scholar Name" style="height:50px;">
                                            <div id="suggestions" class="name_filter"></div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-nfone" id="show_data">

                            <div class="container">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="mt-3 mb-3 d-flex">
                                            <div class="collectionprofile mt-2">
                                                <img src="{{ asset('/public/image/avatar5.png') }}" class="img-circle elevation-2"
                                                    alt="User Image">
                                            </div>
                                            <div class="colllist" style="margin-left: 10px;">
                                                <span style="font-size:20px;" id="student_name"> </span><br>
                                                <span style="color: rgb(167, 166, 166)"><span id="class_name"></span>-<span id="section_name"></span>, Adm no<span id="admission_no"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-3 border-box" style="max-width: 23%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="totalcollection mt-2">
                                                        <i class="fas fa-rupee-sign" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;">&#8377;69,750 </span><br>
                                                        <span style="color: rgb(167, 166, 166)">Total</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-box" style="max-width: 23%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="concen mt-2">
                                                        <i class="far fa-calendar-alt" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;">&#8377;4,550 </span><br>
                                                        <span style="color: rgb(167, 166, 166)">Concession</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-box" style="max-width: 23%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="paid mt-2">
                                                        <i class="far fa-calendar-alt" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;">&#8377;16,450 </span><br>
                                                        <span style="color: rgb(167, 166, 166)">Paid</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-box" style="max-width: 23%; margin:10px;">
                                                <div class="mt-1 mb-1 d-flex">
                                                    <div class="balance mt-2">
                                                        <i class="far fa-calendar-alt" style="color: #ffffff;"></i>
                                                    </div>
                                                    <div class="colllist" style="margin-left: 10px; ">
                                                        <span style="font-size:15px;">&#8377;48,750</span><br>
                                                        <span style="color: rgb(167, 166, 166)">Balance</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <section>

                            <form action="{{ url('admin/post_pay_fee') }}" method="post" id="post_pay_fee"
                            class="post_pay_fee">
                            @csrf

                            <input type="hidden" name="student_id" id="student_id" value="">
                            <input type="hidden" name="school_id" id="school_id" value="">

                            <div class="container">
                                <div class="row mt-3 mb-3" id=" ">

                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                        <div class="table-responsicve">
                                            <table class="table table-striped table-bordered tblcountries">
                                              <thead>
                                                <tr>
                                                  <th>Category</th>
                                                  <th>Item</th>
                                                  <th>Amount</th>
                                                  <th>Due Date</th>
                                                  <th>Paid Amount</th>
                                                  <th>Paid Date</th> 
                                                </tr>
                                              </thead>

                                              <tfoot>
                                                  <th></th><th></th> <th></th>
                                                  <th></th> <th></th> <th></th> 
                                              </tfoot>

                                              <tbody>

                                              </tbody>
                                            </table>
                                        </div>
                                    </div> 
                                </div>
                            </div>
  

                        </form>

                        </section>

                        </div>
                </div>
            </div>

        </div>

    </section>


@endsection

@section('scripts') 


<script>
    $('[data-widget="pushmenu"]').PushMenu("collapse");
    $(function() {
        var table = $('.tblcountries').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            "ajax": {
                "url":"{{URL('/')}}/admin/scholar_feesummary/datatables/",
                data: function ( d ) {
                    var class_id = $('#classSelect').val();
                    var batch = $('#batchSelect').val();
                    var student_id = $('#student_id').val();
                    $.extend(d, {class_id:class_id, batch:batch, student_id:student_id});

                } 
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'item_name', name: 'item_name'},
                { data: 'amount', name:'amount'},
                { data: 'due_date', name: 'due_date'},
                { data: 'amount_paid', name: 'amount_paid'},
                { data: 'paid_date', name:'paid_date'}, 

            ],
            "order":[[5, 'asc']], 
            
        });

        $('.tblcountries tfoot th').each( function (index) {
           // if(index != 0 && index != 3) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //}
        } );

        $('#status_id').on('change', function() {
            table.draw();
        });

        // Apply the search
        table.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                            .search( this.value )
                            .draw();
                }
            } );
        } ); 

        // Event listener for the input field to trigger search on input
        $('#studentName').on('input', function() {
            const nameValue = $(this).val();
            if (nameValue.length >= 2) {
                searchStudentNames(nameValue);
            }
        });

        // Handle the selection of a student name from suggestions
        $(document).on('click', '.suggestion-item', function() {
            const studentId = $(this).data('id');
            $('#studentName').val($(this).text());
            $('#suggestions').empty();
            sendStudentId(studentId);   
        });

        function searchStudentNames(name) {
            var class_id = $('#classSelect').val();
            var batch = $('#batchSelect').val();
            $.ajax({
                type: 'GET',
                url: " {{ URL::to('/admin/search_student') }}",
                dataType: 'json',
                data: {
                    name: name, class_id:class_id, batch:batch
                },
                success: function(data) {
                    displaySuggestions(data.students);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function displaySuggestions(students) {
            const suggestionsDiv = $('#suggestions');


            suggestionsDiv.empty();

            if (students.length === 0) {
                suggestionsDiv.append('<p>No students found.</p>');
                return;
            }

            students.forEach(student => {
                const suggestionItem = $('<div class="suggestion-item"></div>')
                    .html(`<strong>${student.is_student_name} [${student.is_class_name}-${student.is_section_name}]</strong> <br> Adm No: ${student.admission_no}`)
                    .attr('data-id', student.user_id)
                    .css({ padding: '5px', cursor: 'pointer' });
                suggestionsDiv.append(suggestionItem);
            });


        }


        function sendStudentId(studentId) {

            const batch = $('#batchSelect').val();  
            $('#student_id').val(studentId);
            $('.tblcountries').DataTable().ajax.reload();
        }
    });

  
</script>


@endsection



















