@extends('layouts.user_master')
@section('content') 
<style type="text/css">
     
.about-bg {
    padding: 153px 0 50px !important;
}
</style>
    <!-- ===========================
    =====>> Page Hero <<===== -->
    <section id="page-hero" class="about-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title text-center">
                        <h1> <span>Test Questions</span></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =====>> End Page Hero <<===== 
    =========================== -->


    <!-- ===========================
    =====>> My Account <<===== -->
    <section id="my-account-area" class="pt-10 pb-10">
        <div class="container">
            <div class="row">
            	<div class="col-md-12"><a href="#" data-toggle="modal" data-target="#smallModal" id="addbtn"><button class="btn btn-primary" style="float: right;">Add</button></a>  </div>
                
            </div>
            <div class="row" style="width:100%; overflow-x:scroll;">
                <table class="table table-striped table-bordered tblcountries" style="width:100%">
                  <thead>
                    <tr>
                      <th>Test Name</th> 
                      <th>Test Classes</th> 
                      <th>Question</th> 
                      <th>Answer 1</th> 
                      <th>Answer 2</th> 
                      <th>Answer 3</th> 
                      <th>Answer 4</th> 
                      <th>Correct Option</th> 
                      <th>Position</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                      <tr><th></th><th></th><th></th>
                          <th></th><th></th><th></th>
                          <th></th><th></th><th></th>
                          <th></th><th></th> 
                      </tr>
                  </tfoot>
                  <tbody>
                    
                  </tbody>
                </table>
            </div>
        </div> 

        <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="smallModalLabel">Add Test Question</h4>
                    </div>

                    <form id="style-form" enctype="multipart/form-data"
                                      action="{{url('/save/questions')}}"
                                      method="post">

                            {{csrf_field()}}

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Test</label>
                                    <div class="form-line">
                                        <select  class="form-control" name="test_class_id" required>
                                            <option value=""> Select Test</option>
                                            @if(!empty($tests))
                                                @foreach($tests as $test)
                                                    <option value="{{$test->id}}">{{$test->test_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Question</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="question" required minlength="1" maxlength="200">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Answer 1</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="answer_1" required minlength="1" maxlength="200">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Answer 2</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="answer_2" required minlength="1" maxlength="200">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Answer 3</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="answer_3" required minlength="1" maxlength="200">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Answer 4</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="answer_4" required minlength="1" maxlength="200">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Correst Answer</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="correct_answer" required minlength="1" maxlength="200">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6 d-none">
                                    <label class="form-label">Mark</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="mark" min="1" value="1">
                                    </div>
                                </div>  
                                <div class="form-group form-float float-left col-md-6 d-none">
                                    <label class="form-label">Position</label>
                                    <div class="form-line">
                                        <input type="number" class="form-control" name="position" value="1" min="1">
                                    </div>
                                </div>
                                <div class="form-group form-float float-left col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-line">
                                        <select class="form-control" name="status" required>
                                          <option value="ACTIVE">ACTIVE</option>
                                          <option value="INACTIVE">INACTIVE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>

                    </form>
                </div>
            </div>
        </div> 
    <div class="modal fade in" id="smallModal-2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Edit Test Question</h4>
                </div>

                <form id="edit-style-form" enctype="multipart/form-data"
                                  action="{{url('/save/questions')}}"
                                  method="post">

                        {{csrf_field()}}
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Test</label>
                                <div class="form-line">
                                    <select  class="form-control" name="test_class_id" id="edit_test_class_id" required>
                                        <option value=""> Select Test</option>
                                        @if(!empty($tests))
                                            @foreach($tests as $test)
                                                <option value="{{$test->id}}">{{$test->test_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Question</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="question" id="edit_question" required minlength="1" maxlength="200">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Answer 1</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="answer_1" id="edit_answer_1" required minlength="1" maxlength="200">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Answer 2</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="answer_2" id="edit_answer_2" required minlength="1" maxlength="200">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Answer 3</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="answer_3" id="edit_answer_3" required minlength="1" maxlength="200">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Answer 4</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="answer_4" id="edit_answer_4" required minlength="1" maxlength="200">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Correst Answer</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="correct_answer" id="edit_correct_answer" required minlength="1" maxlength="200">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Mark</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" name="mark" id="edit_mark" min="1" value="1">
                                </div>
                            </div>  
                            <div class="form-group form-float float-left col-md-6 d-none">
                                <label class="form-label">Position</label>
                                <div class="form-line">
                                    <input type="number" class="form-control" name="position" id="edit_position" value="1" min="1">
                                </div>
                            </div>
                            <div class="form-group form-float float-left col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-line">
                                    <select class="form-control" name="status" id="edit_status" required>
                                      <option value="ACTIVE">ACTIVE</option>
                                      <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="edit_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div> 
    </section>
    <!-- =====>> End My Account <<===== 
    =========================== -->
@endsection

@section('scripts')
<script type="text/javascript">
$(function() { 

			$('#addbtn').on('click', function () {
				$('#style-form')[0].reset();
			});

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("questions.data")}}',
                },
                columns: [ 
                    { data: 'test_name',  name: 'test_name'}, 
                    { data: 'class_names',  name: 'class_names'}, 
                    { data: 'question',  name: 'test_questions.question'},
                    { data: 'answer_1',  name: 'test_questions.answer_1'},
                    { data: 'answer_2',  name: 'test_questions.answer_2'}, 
                    { data: 'answer_3',  name: 'test_questions.answer_3'}, 
                    { data: 'answer_4',  name: 'test_questions.answer_4'},
                    { data: 'correct_answer',  name: 'test_questions.correct_answer'}, 
                    { data: 'position',  name: 'test_questions.position'},
                    { data: 'status',  name: 'test_questions.status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            return '<a href="#" onclick="loadTestQuestion('+tid+')" title="Edit Test Question"><i class="fas fa-edit"></i></a>'; 
                        },

                    },
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }, 
                    { "orderable": false, "targets": 1 }, 
                    { "orderable": false, "targets": 10 }, 
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index !=0 && index !=1 && index !=10) {
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
            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == 1) {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal').modal('hide');

                        }
                        else if (response.status == 0) {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#style-form").ajaxForm(options);
            }); 
            $('#edit_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == 1) {

                           swal('Success',response.message,'success');

                           $('.tblcountries').DataTable().ajax.reload();

                           $('#smallModal-2').modal('hide');

                        }
                        else if (response.status == 0) {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#edit-style-form").ajaxForm(options);
            }); 
        });
 
        function loadTestQuestion(id){
            $("#edit-style-form")[0].reset();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('edit/questions')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    code:id,
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {

                $('#id').val(response.data.id); 
                $('#edit_test_class_id').val(response.data.test_class_id); 
                $('#edit_question').val(response.data.question); 
                $('#edit_answer_1').val(response.data.answer_1); 
                $('#edit_answer_2').val(response.data.answer_2); 
                $('#edit_answer_3').val(response.data.answer_3); 
                $('#edit_answer_4').val(response.data.answer_4); 
                $('#edit_correct_answer').val(response.data.correct_answer); 
                $('#edit_status').val(response.data.status); 
                $('#edit_position').val(response.data.position);
                $('#smallModal-2').modal('show');

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

</script>
@endsection