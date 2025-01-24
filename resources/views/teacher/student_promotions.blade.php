@extends('layouts.teacher_master')
@section('master_settings', 'active')
@section('master_promotions', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Student Promotions', 'active'=>'active']];
?>
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>
        .form-control:focus {
            color: #495057;
            background-color: #fff !important;
            border: none;
            outline: 0;
            box-shadow: 0 0 0 0.2rem #dee2e6 !important;
        }

        .greentick {
            color: #A3D10C;
        }

        .redcross {
            color: #dc3545;
        }

        .greentickbox {
            color: #fff;
            background: #007bff;
            font-size: 10px;
            padding: 4px;
            cursor: pointer;
        }

        .redcrossbox {
            color: #fff;
            background: #dc3545;
            font-size: 13px;
            padding: 4px;
            margin-top: 5px;
            cursor: pointer;
        }

        .greentickboxharizondal {
            color: #fff;
            background: #007bff;
            font-size: 10px;
            padding: 5px 4px 4px 4px;
        }

        .redcrossboxharizondal {
            color: #fff;
            background: #dc3545;
            font-size: 12px;
            padding: 4px;
            margin-top: 0px;
        }

        .rowcen {
            padding-left: 6px;
            margin-top: 7px;
        }

        @media only screen and (max-width: 600px) {
            .my-account-form {
                overflow-x: scroll !important;
            }

        }
    </style>
@endsection
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Students Promotions</h4>  
                  <br><br> 
                        <div class="row"> 
                      
                            <div class="col-md-3">
                                <label style="padding-bottom: 10px;">From Class <span class="manstar">*</span></label>
                                <select class="form-control course_id" name="class_id" id="class_id"
                                        onchange="loadClassSection(this.value);loadtoclass(this.value)">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">From Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="section_id" id="section_dropdown" required>

                                    </select>
                                </div>
                            </div>


                            
                            <div class="col-md-3">
                                <label style="padding-bottom: 10px;">To Class <span class="manstar">*</span></label>
                                <select class="form-control course_id" name="toclass_id" id="toclass_id"
                                        onchange="loadToClassSection(this.value)">
                                 
                                </select>
                            </div>

                            <div class=" col-md-3">
                                <label class="form-label" style="padding-bottom: 10px;">To Section <span class="manstar">*</span></label>
                                <div class="form-line">
                                    <select class="form-control" name="tosection_id" id="tosection_id" required>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2" hidden>
                                <button type="submit" class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px"
                                    onclick="loadpromotions()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                     
                          
                </div> 
                
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                            <div class="table-responsicve">
                                <table class="table table-striped table-bordered tblcountries" >
                                    <thead>
                                        <tr>
                                            <th scope="col">Action</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Admission No</th>
                                            <th scope="col">Class</th>
                                            <th scope="col">Section</th>
                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                           
                                        </tr>
                                    </tfoot>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                   <div class="col-sm-12">
                    <div class="col-sm-10">

                    </div>
                    <div style="float:right" class="col-sm-2">
                    <button type="submit" onclick="updatestudent_promotions()" class="btn btn-success center-block float-right" id="add_style">Submit</button> 
                    
                </div>
                <br><br>
                </div>
                </div>
                 
              </div>
            </div>
          </div>
    </section> 
 
@endsection

@section('scripts')
    <script type="text/javascript">
        // $(function() {});
        $(function() {  

var table = $('.tblcountries').DataTable({
    processing: true,
    serverSide: true,
    responsive: false,
    "ajax": {
        "url": '{{route("promotion.data")}}',
        data: function ( d ) {       
            var class_id  = $('#class_id').val(); 
            var section_id  = $('#section_dropdown').val();
            $.extend(d, { class_id:class_id, section_id:section_id});
        }

    },
    columns: [ 
        {
            data:null,
            "render": function ( data, type, row, meta ) {

                var tid = data.id;  
                return '<input type="checkbox" name="qbid[]" id="qbid_'+tid+'" value="'+tid+'" />'; 
            },

        },
        { data: 'name',  name: 'users.name'},
        { data: 'admission_no',  name: 'students.admission_no'},
        { data: 'class_name',  name: 'classes.class_name'},
        { data: 'section_name',  name: 'sections.section_name'}, 
       
    ],
    "order": [],
                "columnDefs": [
                    {
                        "orderable": false,
                        "targets": 0
                    },
                    {
                        "orderable": false,
                        "targets": 3
                    },
                    {
                        "orderable": false,
                        "targets": 4
                    }
                ]

});

$('.tblcountries tfoot th').each( function (index) {
    if(index != 0 && index != 3 && index != 4) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    }
} );

$('#class_id').on('change', function() {
                table.draw();
            });

$('#section_dropdown').on('change', function() {
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

});
        
      

          function updatestudent_promotions() { 

                 var class_id = $('#toclass_id').val();
                 var section_id = $('#tosection_id').val();
                 var from_class_id  = $('#class_id').val(); 
                 var from_section_id  = $('#section_dropdown').val();

            if(from_class_id == ''){

                swal("Oops!", "Please select the From Class", "error");
                return false;
            }      
            if(from_section_id == ''){

                swal("Oops!", "Please select the From Section", "error");
                return false;
            }
            if(class_id == '') {
                swal("Oops!", "Please select the To Class", "error");
                return false;
            }
            if(section_id == ''){

                swal("Oops!", "Please select the To Section", "error");
                return false;
            }
         
            
                 var lengthchked = $('input:checkbox:checked').length;  
                if(lengthchked > 0 ) {

                    var myCheckboxes = new Array();
                    $("input:checkbox:checked").each(function() {
                       myCheckboxes.push($(this).val());
                    });
                     $.ajax({
                        "url": "{{URL::to('/teacher/update/promotions')}}",
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            qbid:myCheckboxes,
                            class_id:class_id,
                            section_id:section_id
                        },
                        "success": function(res, status, xhr) {
                            $('.tblcountries').DataTable().ajax.reload();
                        }
                    });

                
                }   else {
                    swal('Oops',"Please select the Question banks to export",'warning');
                    return false;
                }
             } 


         function loadtoclass(val) {

            var class_id = val;
           

            $("#toclass_id").html('');
            $.ajax({
                url: "{{ url('teacher/fetch-to-class') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#toclass_id').html(
                        '<option value="">-- Select To Class --</option>');
                   
                    $.each(res.classes, function(key, value) {
                        $("#toclass_id").append('<option value="' + value
                            .id + '">' + value.class_name + '</option>');
                    });
                }
            });
        }


        function loadToClassSection(val) {

var class_id = val;

$("#tosection_id").html('');
$.ajax({
    url: "{{ url('teacher/fetch-section-all') }}",
    type: "POST",
    data: {
        class_id: class_id,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {
        $('#tosection_id').html(
            '<option value="">-- Select To Section --</option>');
      
        $.each(res.section, function(key, value) {
            $("#tosection_id").append('<option value="' + value
                .id + '">' + value.section_name + '</option>');
        });
    }
});
}


function loadClassSection(val) {

var class_id = val;

$("#section_dropdown").html('');
$.ajax({
    url: "{{ url('teacher/fetch-section') }}",
    type: "POST",
    data: {
        class_id: class_id,
        _token: '{{ csrf_token() }}'
    },
    dataType: 'json',
    success: function(res) {

        $('#section_dropdown').html(
            '<option value="">-- Select Section --</option>');
        $.each(res.section, function(key, value) {
            $("#section_dropdown").append('<option value="' + value
                .id + '">' + value.section_name + '</option>');
        });
    }
});
}

    </script>

@endsection
