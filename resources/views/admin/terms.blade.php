@extends('layouts.admin_master')
@section('settings', 'active')
@section('settings_terms', 'active')
@section('menuopen', 'menu-is-opening menu-open')
@section('content') 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid">

            <div class="panel"> 
                
                <div class="panel-body">


            <div class="row">

                <div class="col-xs-12 col-md-12">
            
                <div class="card">
                    <div class="card-header">Terms and Conditions
                    </div>

                    <div class="card-body">
                        <div class="row"><div class="col-md-12">
                            <form name="frm_terms" id="frm_terms" method="post" action="{{url('/admin/save/termscond')}}"> 
                                {{csrf_field()}}
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Terms and Conditions <span class="manstar">*</span></label>
                                    <textarea name="terms" id="terms" class="form-control">{{$terms}}</textarea>
                                </div>
                                
                            </div> 
                            <button type="submit" class="btn btn-success center-block" id="Submit">Submit</button> 
                            </form>
                        </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</section>
@endsection

@section('scripts')
<!-- <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> -->
<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
      <script>

        $(function() {
            //CKEDITOR.replace( 'terms' );

            const editorConfig = {
                toolbar: {
                    items: ['undo', 'redo', '|', 'selectAll', '|', 'bold', 'italic', '|', 'accessibilityHelp', '|', 'numberedList', 'bulletedList', '|', 'heading', '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|', 
                          'strikethrough', 'subscript', 'superscript' , 'table'  , 'link'],
                    shouldNotGroupWhenFull: false
                }, 
            };
            ClassicEditor
                .create(document.querySelector('#terms'), editorConfig)
                .then(editor => { console.log(editor); })
                .catch(error => { console.error(error); }); 
            
            $('#Submit').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                           swal('Success','The Terms and Conditions Saved Successfully','success');

                           window.location.reload();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#frm_terms").ajaxForm(options);
            });       
        });

    </script>

 
@endsection

