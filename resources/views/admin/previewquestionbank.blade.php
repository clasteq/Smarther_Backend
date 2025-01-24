@extends('layouts.admin_master')
@section('questionbank_settings', 'active')
@section('master_questionbank', 'active')
@section('menuopenq', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/questionbank'), 'name'=>'Question Bank', 'active'=>''], ['url'=>'#', 'name'=>'View Question Bank', 'active'=>'active'] ];
?>
@section('content')
 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid"> 
            <div class="panel"> 
                <!-- Panel Heading -->
                <div class="panel-heading"> 
                    <!-- Panel Title -->
                    @php($filename = $qb['qb_name'])
                    <button style="    margin-left: 90%;  margin-bottom: 10px;" class="btn btn-info " onclick="Export2Word('exportContent', '{{$filename}}');">Export as Doc</button>
                </div>
                @if(count($qb) > 0)  
                <div class="panel-body">  
                    <div class="row"> 
                        <div class="col-xs-12 col-md-12"> 
                        <div class="card"> 
                            <div class="card-body" id="exportContent">
                                <link rel="stylesheet" href="{{asset('/public/dist/css/adminlte.min.css')}}">
                                <h4 style="font-size:20px" class="panel-title">View Question Bank 
                                </h4> 
                                <div class="row"><div class="col-md-12"> 
                                    <div class="row">
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line"> {{$qb['class_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line">{{$qb['subject_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Chapter</label>
                                            <div class="form-line">{{$qb['chaptername']}}</div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line">{{$qb['term_name']}} </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Question Bank Name</label>
                                            <div class="form-line">{{$qb['qb_name']}}</div>
                                        </div>
                                    </div> 
                                    <hr>
                                    <!-- Start Question types -->
                                    @if(!empty($qb['questionbank_items']) && count($qb['questionbank_items'])>0)
                                        @foreach($qb['questionbank_items'] as $qid=>$qtype)  
                                        @if(isset($qtype))  
                                            <div class="form-group form-float float-left col-md-12"><label class="form-label">{{$qtype['question_type']}}</label></div>@php($i=1)
                                            @foreach($qtype['qb_items'] as $item) <?php //echo "<pre>"; print_r($item); exit;?>  
                                                @if($item->question_type_id != 16)
                                                <div class="form-group form-float float-left col-md-12">
                                                    {{$i}}) {!! $item->question !!} -  {!! $item->answer !!}<!--  <br> {!! $item->display_answer !!} {!! $item->option_1 !!} {!! $item->option_2 !!} {!! $item->option_3 !!} {!! $item->option_4 !!}  -->
                                                </div> 
                                                @elseif ($item->question_type_id == 16)
                                                <div class="form-group form-float float-left col-md-12">
                                                    @if(!empty($item->question))
                                                    <?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->question; ?>
                                                    @endif
                                                    {{$i}})&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <img src="{{ $fileurl }}" width="auto" height="auto" style="max-height: 300px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   -  {{$item->answer}} {{$item->display_answer}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @if(!empty($item->option_1))
                                                    <?php $option_1 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_1; ?>
                                                    <img src="{{ $option_1 }}" width="auto" height="auto" style="max-height: 300px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    @if(!empty($item->option_2))
                                                    <?php $option_2 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_2; ?>
                                                    <img src="{{ $option_2 }}" width="auto" height="auto" style="max-height: 300px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    @if(!empty($item->option_3))
                                                    <?php $option_3 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_3; ?>
                                                    <img src="{{ $option_3 }}"  width="auto" height="auto" style="max-height: 300px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    @if(!empty($item->option_4))
                                                    <?php $option_4 = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->option_4; ?>
                                                    
                                                    <img src="{{ $option_4 }}"  width="auto" height="auto" style="max-height: 300px;">
                                                    @endif
                                                 
                                                   
                                                        {{-- {{$item->option_1}} {{$item->option_2}} {{$item->option_3}} {{$item->option_4}}  --}}
                                                </div> 
                                                @endif 


                                                @if(!empty($item->hint_file))
                                                <div class="form-group form-float float-left col-md-12">
                                                <?php $hint_file = config("constants.APP_IMAGE_URL").'image/qb/'.$item->hint_file; ?>
                                                Hint File: 
                                                <img src="{{ $hint_file }}"  width="auto" height="90%" style="max-height: 300px;">
                                                </div>
                                                @endif

                                                @php($i++)
                                            @endforeach
                                        @endif 
                                        @endforeach
                                    @endif
                                    <!-- End Question Types --> 
                                </div></div>
                            </div>
                        </div>
                    </div>
                </div>
                @else 
                No Question Bank Details
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts') 
      <script>

        $(function() { 

            $('.plus').on('click', function () {
                var qtype = $(this).data('id');
                var i = $('#items_'+qtype).find('input[name="sno[]"]').length;
                var request = $.ajax({
                    type: 'post',
                    url: " {{URL::to('admin/clone/questiontype')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        code:qtype,i:i,
                    },
                    dataType:'json',
                    encode: true
                });
                request.done(function (response) { 
                    if(response.status == 'SUCCESS') {
                        $('#items_'+qtype).append(response.data);
                    }   else {
                        swal("Oops!", "Unable to clone the type", "error");
                    }
                });
                request.fail(function (jqXHR, textStatus) {

                    swal("Oops!", "Sorry,Could not process your request", "error");
                });
            });
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

                           swal('Success','Question bank Saved Successfully','success');

                           window.location.reload();

                        }
                        else if (response.status == "FAILED") {

                            swal('Oops',response.message,'warning');

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('SUBMIT');

                        swal('Oops','Something went to wrong.','error');

                    }
                };
                $("#frm_questionbank").ajaxForm(options);
            });   
        });

    </script>
 
    <script type="text/javascript">
        function Export2Word(element, filename = ''){
            var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
            var postHtml = "</body></html>";
            var html = preHtml+document.getElementById(element).innerHTML+postHtml;

            var blob = new Blob(['\ufeff', html], {
                type: 'application/msword'
            });
            
            // Specify link url
            var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
            
            // Specify file name
            filename = filename?filename+'.doc':'document.doc';
            
            // Create download link element
            var downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);
            
            if(navigator.msSaveOrOpenBlob ){
                navigator.msSaveOrOpenBlob(blob, filename);
            }else{
                // Create a link to the file
                downloadLink.href = url;
                
                // Setting the file name
                downloadLink.download = filename;
                
                //triggering the function
                downloadLink.click();
            }
            
            document.body.removeChild(downloadLink);
        }
    </script>
@endsection

