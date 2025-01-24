@extends('layouts.admin_master')
@section('questionbank_settings', 'active')
@section('master_questionbank', 'active')
@section('menuopenq', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Question Bank', 'active'=>'active']];
?>
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Question Bank</h4> 
                  <a href="{{URL('/')}}/admin/add/questionbank"><button id="addbtn" class="btn btn-primary" style="float: right;">Create Question Bank</button></a>  
                </div>   
                <div class="card-header" style="display: none;">
                  <h4 style="font-size:20px;" class="card-title">Question Bank</h4> 
                    <a href="{{URL('/')}}/admin/add/questionbank"><button id="addbtn" class="btn btn-primary" style="float: right;">Create Question Bank</button></a>  

                   <br><br>
                    <form method="post" action="{{URL::to('/admin/export/questionbank')}}">
                        {{csrf_field()}}   
                        <input type="hidden" name="selected_ids" id="selected_ids">
                        <button class=" btn btn-info" id="export_qb_btn">Export Question Bank</button>
                  
                </form>
                    <form method="post" name="qb_import" id="qb_import" action="{{URL::to('/admin/import/questionbank')}}">
                            {{csrf_field()}}   
                    <div class="row mt-5">
                        <input type="file" name="importqb" id="importqb" required>
                        <button class=" btn btn-info" id="import_qb_btn">Import Question Bank</button>
                        ( Export file and make changes and Upload the file )
                    </div>
                    </form>
                        
                          
                </div> 

                <div class="row"> <div class="col-md-12"> 
                    <div class=" col-md-3 float-left">
                        <label class="form-label" >Class </label>
                        <div class="form-line">
                            <select class="form-control" onchange="fetchclasssubject(this.value); loadClassTerms(this.value);" name="class_id" id="class_id" >
                                <option value="">All</option>
                                @if (!empty($class))
                                @foreach ($class as $classes)
                               
                                <option value={{$classes->id}}>{{$classes->class_name}}</option>

                                @endforeach
                            @endif
                            </select> 
                        </div>
                    </div>

                    <div class=" col-md-3 float-left">
                        <label class="form-label" >Subject</label>
                        <div class="form-line">
                            <select class="form-control" name="subject_id" id="subject_id" >
                             </select> 
                        </div>
                    </div>

                    <div class=" col-md-3 float-left">
                        <label class="form-label" >Terms </label>
                        <div class="form-line">
                            <select class="form-control term_id" name="term_id" id="term_id" required >
                                <option value="">Select Term</option> 
                            </select>
                        </div>
                    </div>
                </div> </div>

                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                    <form method="post" name="qb_export" id="qb_export" action="{{URL::to('/admin/export/questionbank')}}">
                            {{csrf_field()}}    
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr> 
                                 <th style="width: 15% !important;"></th>
                                 <th style="width: 15% !important;">View</th>                                 
                                  <th style="width: 10% !important;">Term</th>
                                  <th style="width: 10% !important;">Class</th>
                                  <th style="width: 15% !important;">Subject</th> 
                                  <th style="width: 15% !important;">Chapter</th> 
                                  <th style="width:25%">Question Bank Name</th> 
                                  <th style="width: 10% !important;">Notes</th>
                                  <th style="width: 15% !important;">Action</th>
                                 
                                 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr>
                                      <th style="width: 15% !important;"></th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
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
                    </form>
                    </div>
                    
                  </div>
                </div> 
              </div>
            </div>
          </div>
    </section> 
@endsection

@section('scripts')

    <script> 

        function fetchclasssubject(class_id) {
            $("#subject_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-class-subject') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#subject_id').html(
                        '<option value="">-- Select Subject --</option>');
                
                    $.each(res.subjects, function(key, value) {
                         $("#subject_id").append('<option value="' + value
                            .id + '">' + value.subject_name + '</option>');
                    });
                }
            });
        }

        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/questionbank/datatables/",   
                    data: function ( d ) { 
                        var class_id  = $('#class_id').val();
                        var subject_id  = $('#subject_id').val(); 
                        var term_id  = $('#term_id').val();
                        $.extend(d, {class_id:class_id, subject_id:subject_id, term_id:term_id});

                    }
                },
                columns: [ 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                          
                         return '<input type="checkbox" name="exportqb[]" id="exportqb_'+tid+'" value="'+tid+'" />'; 
                        },

                    },
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            var vurl = "{{URL('/')}}/admin/view/questionbank?id="+tid;
                            return '<a href="'+vurl+'"  title="View Question Bank"><i class="fas fa-eye"></i></a> '; 
                        },
                    },                  
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'class_name',  name: 'class_name'},
                    { data: 'subject_name',  name: 'subject_name'},
                    { data: 'chaptername',  name: 'chaptername'},
                    { data: 'qb_name',  name: 'qb_name'}, 
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var notes = data.notes;
                            var is_notes_file = data.is_notes_file;
                           if (notes != null && notes != '') {
                                return '<a href="' + is_notes_file +
                                    '" target="_blank" title="Notes" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            var url = "{{URL('/')}}/admin/edit/questionbank?id="+tid;
                            var durl = "{{URL('/')}}/admin/delete/questionbank/"+tid;
                         return '<a href="'+url+'"  title="Edit Question Bank"><i class="fas fa-edit"></i></a> &nbsp;&nbsp;<a href="#" onclick="deleteQb('+tid+')"  title="Delete Question Bank"><i class="fas fa-trash"></i></a>'; 
                        },


                    }
                 
                ],
                "order":[],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 1 },
                    { "orderable": false, "targets": 8 },
                    { "orderable": false, "targets": 7 },
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 0 && index != 7 && index != 8 && index != 1) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#class_id,#subject_id,#term_id').on('change', function() {
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


            $button = $('#export_qb_btn');
            $button.on('click', function () { 
                var lengthchked = $('input:checkbox:checked').length;  
                if(lengthchked > 0 ) {

                    var myCheckboxes = new Array();
                    $("input:checkbox:checked").each(function() {
                       myCheckboxes.push($(this).val());
                    });

                    $('#selected_ids').val(myCheckboxes)

                }   else {
                    swal('Oops',"Please select the Question banks to export",'warning');
                    return false;
                }
            }); 

            $button = $('#import_qb_btn');
            $button.on('click', function () { 
                var options = {

                    beforeSend: function (element) {

                        $("#import_qb_btn").text('Processing..');

                        $("#import_qb_btn").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#import_qb_btn").prop('disabled', false);

                        $("#import_qb_btn").text('Import Question Bank');

                        if (response.status == "SUCCESS") {

                           swal('Success','Question Bank Uploaded Successfully','success');

                           window.location.reload();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#import_qb_btn").prop('disabled', false);

                        $("#import_qb_btn").text('Import Question Bank');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#qb_import").ajaxForm(options);

                   
            }); 
        });

        function deleteQb(id){
            swal({
                title : "",
                text : "Are you sure to delete?",
                type : "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
            },
            function(isConfirm){
                if (isConfirm) {
                    var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('/admin/delete/questionbank')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            id:id,
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {

                        swal('Success',response.message,'success');

                        $('.tblcountries').DataTable().ajax.reload();

                    });
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });
                }
            })


        }
   

    </script>

@endsection
