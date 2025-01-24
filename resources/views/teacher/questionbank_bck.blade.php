@extends('layouts.teacher_master')
@section('questionbank_settings', 'active')
@section('master_questionbank', 'active')
@section('menuopenq', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Question Bank', 'active'=>'active']];
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
                   
                    <a href="{{URL('/')}}/teacher/add/questionbank"><button id="addbtn" class="btn btn-primary" style="float: right;">Create Question Bank</button></a>  
<br><br>
<form method="post" action="{{URL::to('/admin/export/questionbank')}}">
    {{csrf_field()}}   
    <input type="hidden" name="selected_ids[]" id="selected_ids">
    <button class=" btn btn-info" id="export_qb_btn">Export Question Bank</button>

</form>
                    <form method="post" name="qb_import" id="qb_import" action="{{URL::to('/teacher/import/questionbank')}}">
                            {{csrf_field()}}   
                    <div class="row mt-5">
                        <input type="file" name="importqb" id="importqb" required>
                        <button class=" btn btn-info" id="import_qb_btn">Import Question Bank</button>
                        ( Export file and make changes and Upload the file )
                    </div>
                    </form>
                     
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                    <form method="post" name="qb_export" id="qb_export" action="{{URL::to('/teacher/export/questionbank')}}">
                            {{csrf_field()}}    
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr> 
                                  <th width="10%">Action</th>
                                  <th>Term</th>
                                  <th>Class</th>
                                  <th>Subject</th> 
                                  <th>Chapter</th> 
                                  <th>Notes</th>
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th> <th></th>
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
        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("teacher_questionbank.data")}}',
                },
                columns: [ 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            var url = "{{URL('/')}}/teacher/edit/questionbank/"+tid;
                            var vurl = "{{URL('/')}}/teacher/view/questionbank/"+tid;
                            return '<a target="_blank" href="'+vurl+'"  title="View Question Bank"><i class="fas fa-eye"></i></a> <a target="_blank" href="'+url+'"  title="Edit Question Bank"><i class="fas fa-edit"></i></a> <input type="checkbox" name="exportqb[]" id="exportqb_'+tid+'" value="'+tid+'" />'; 
                        },

                    },
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'class_name',  name: 'class_name'},
                    { data: 'subject_name',  name: 'subject_name'},
                    { data: 'chaptername',  name: 'chaptername'}, 
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var notes = data.notes;
                            var is_notes_file = data.is_notes_file;
                        //    alert(is_notes_file)
                            if (notes != null && notes != '') {
                                return '<a href="' + is_notes_file +
                                    '" target="_blank" title="Notes" class="btn btn-info">View</a>';
                            } else {
                                return '';
                            }
                        },

                    },
                   
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 0 && index != 5) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

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
   

    </script>

@endsection
