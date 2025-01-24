@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_classes', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Classes', 'active'=>'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Classes</h4> 
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/updateclasses')}}"
                                  method="post">

                        {{csrf_field()}}
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th> </th>
                                  <th>Class Name</th> 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th></tr>
                              </tfoot>
                              <tbody>
                              @if(!empty($classes)) 
                                @foreach($classes as $class)
                                    @php($selected = '')
                                    @if(in_array($class->id, $sclassarr))
                                    @php($selected = ' checked ')
                                    @endif
                                    <tr><th><input type="checkbox" name="class[{{$class->id}}]" id="class_{{$class->id}}" {{$selected}}></th><th>{{$class->class_name}} <input type="hidden" name="position[{{$class->id}}]" value="{{$class->position}}"></th></tr>
                                @endforeach
                              @endif
                              </tbody>
                            </table>
                        </div>
                        <div>
                            <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                            <?php $burl=URL('/')."/admin/classes"; ?>
                            <button type="button" class="btn btn-link waves-effect" onclick="window.location.href='{{$burl}}'">BACK</button>
                        </div>
                    </div>
                    </form>
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
             
            $('#add_style').on('click', function () {

                var options = {

                    beforeSend: function (element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function (response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == 'SUCCESS') {

                           swal('Success',response.message,'success');  

                        }
                        else if (response.status == 'FAILED') {

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
        }); 

    </script>

@endsection
