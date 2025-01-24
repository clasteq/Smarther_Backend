@extends('layouts.admin_master')
@section('test_settings', 'active')
@section('master_test', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/tests'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'View Test', 'active'=>'active'] ];
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
              
                </div>
              
                @if(count($qb) > 0)
                <div class="panel-body">  
                    <div class="row"> 
                        <div class="col-xs-12 col-md-12"> 
                        <div class="card"> 
                            <div class="card-body">
                                <h4 style="font-size: 20px;" class="panel-title">View Test
                                    @php($testname = $qb['test_name'])
                                 <button onclick="ExportToDoc('exportContent','{{$testname}}');" class="btn btn-info waves-effect">Export as .doc</button>
                                </h4>
                                <div class="row"  id="exportContent">
                                    <link rel="stylesheet" href="{{asset('/public/dist/css/adminlte.min.css')}}">
                                    <table class="table table-striped table-bordered" style="width:98%">
                                        <tbody>
                                            <tr>
                                                <td><label class="form-label">From Date</label>
                                                    <div class="form-line"> {{$qb['from_date']}} </div>
                                                </td>
                                                <td>
                                                    <label class="form-label">To Date</label>
                                                    <div class="form-line">{{$qb['to_date']}} </div>
                                                </td>
                                                <td>
                                                    <label class="form-label">Class</label>
                                                    <div class="form-line"> {{$qb['class_name']}} </div>
                                                </td>
                                                <td>
                                                    <label class="form-label">Sections</label>
                                                    <div class="form-line">{{$qb['is_section_names']}} </div>
                                                </td>
                                                <td>
                                                    <label class="form-label">Term</label>
                                                    <div class="form-line">{{$qb['term_name']}} </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label class="form-label">Subject</label>
                                                    <div class="form-line">{{$qb['subject_name']}} </div>
                                                </td>
                                                <td>
                                                    <label class="form-label">Test</label>
                                                    <div class="form-line">{{$qb['test_name']}}</div>
                                                </td> 
                                                <td>
                                                    <label class="form-label">Test Mark</label>
                                                    <div class="form-line">{{$qb['test_mark']}} </div>
                                                </td>
                                                <td>
                                                    <label class="form-label">Test Time (in seconds)</label>
                                                    <div class="form-line">{{$qb['test_time']}} </div>
                                                </td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                    <div class="col-md-12"> 
                                    
                                    <hr>
                                   
                                    <!-- Start Question types -->
                                    @if(!empty($qb['new_test_items']) && count($qb['new_test_items'])>0)
                                        @foreach($qb['new_test_items'] as $qid=>$qtype)  
                                        @if(isset($qtype))  
                                            <div class="form-group form-float float-left col-md-12"><label class="form-label">{{$qtype['question_type']}}</label></div>@php($i=1)
                                            <?php //echo "<pre>"; print_r($item); exit;?>  

                                            @foreach($qtype['tt_items'] as $item) 
                                            @if($item->question_type_id != 16)
                                                <div class="form-group form-float float-left col-md-12">
                                                    {{$i}})   {!! $item->question !!} -  {!! $item->answer !!} 
                                                </div> 
                                                @elseif ($item->question_type_id == 16)
                                                <?php $fileurl = config("constants.APP_IMAGE_URL").'image/questionbank/'.$item->question; ?>
                                                <div class="form-group form-float float-left col-md-12">
                                                    {{$i}})   <img src="{{$item->question}}" height="150" width="150"> &nbsp;&nbsp;&nbsp; -  &nbsp;&nbsp;{{$item->answer}}
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
                No Test Details
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
 
    <script>
        function ExportToDoc(element, filename = ''){
            var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML to Word Document with JavaScript</title></head><body>";

            var footer = "</body></html>";

            var html = header+document.getElementById(element).innerHTML+footer;

            var blob = new Blob(['\ufeff', html], {
                type: 'application/msword'
            });
            
            // Specify link url
            var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
            
            // Specify file name
            filename = filename?filename+'.docx':'document.docx';
            
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

