@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_PeriodsTiming', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'PeriodsTiming', 'active' => 'active']];
?>
@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
   
    <section class="content">
        <!-- Exportable Table -->
       
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size: 20px;" class="card-title">Edit Periods
                          
                        </h4>
                        </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <form id="style-form" enctype="multipart/form-data"
                                        action="{{ url('/admin/save/period_timing') }}" method="post">

                                        {{ csrf_field() }}

                                        <div class="row"> 
                          
                                            <div class="col-md-3">
                                                <label class="from-label">Class</label>
                                                <input type="hidden" name="class_id" id="class_id" value="{{$period['class_id']}}">
                                                <span class="form-control course_id">{{$period['is_class_name']}}</span> 
                                            </div>
                                        </div>
<br>
                                        <table class="table table-striped table-bordered tblcountries">
                                            <thead>
                                                <tr>
                                                    <th>Period 1</th>
                                                    <th>Period 2</th>
                                                    <th>Period 3</th>
                                                    <th>Period 4</th>
                                                    <th>Period 5</th>
                                                    <th>Period 6</th>
                                                    <th>Period 7</th>
                                                    <th>Period 8</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    {{-- @foreach ($periods as $periodtiming) --}}
                                                        <input type="hidden" name="id" value="{{$period['id']}}" id="id">
                                                        <th><input type="time" @if($period['period_1'] != '00:00') value="{{date('H:i', strtotime($period['period_1']))}}" @else value="" @endif  name="period_1"></th>
                                                        <th><input type="time"   @if($period['period_2'] != '00:00') value="{{date('H:i', strtotime($period['period_2']))}}" @else value="" @endif  name="period_2"></th>
                                                        <th><input type="time"   @if($period['period_3'] != '00:00') value="{{date('H:i', strtotime($period['period_3']))}}" @else value="" @endif  name="period_3"></th>
                                                        <th><input type="time"   @if($period['period_4'] != '00:00') value="{{date('H:i', strtotime($period['period_4']))}}" @else value="" @endif  name="period_4"></th>
                                                        <th><input type="time"  @if($period['period_5'] != '00:00') value="{{date('H:i', strtotime($period['period_5']))}}" @else value="" @endif  name="period_5"></th>
                                                        <th><input type="time"   @if($period['period_6'] != '00:00') value="{{date('H:i', strtotime($period['period_6']))}}" @else value="" @endif  name="period_6"></th>
                                                        <th><input type="time"   @if($period['period_7'] != '00:00') value="{{date('H:i', strtotime($period['period_7']))}}" @else value="" @endif  name="period_7"></th>
                                                        <th><input type="time"   @if($period['period_8'] != '00:00') value="{{date('H:i', strtotime($period['period_8']))}}" @else value="" @endif  name="period_8"></th>
                                                    {{-- @endforeach --}}
                                                </tr>
                                            </tfoot>
                                            <tbody>

                                            </tbody>
                                        </table>

                                        <div class="modal-footer">
                                            <button type="sumbit" id="Submit" class="btn btn-primary"
                                                id="edit_style">Save</button>
                                           
                                        </div>

                                    </form>
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

      <script>

        $(function() {
          
            $('#Submit').on('click', function () {
             var options = {

                    beforeSend: function (element) {

                        $("#Submit").text('Processing..');

                        $("#Submit").prop('disabled', true);

                    },
                    success: function (response) {

                        $("#Submit").prop('disabled', false);

                        $("#Submit").text('Save');

                        if (response.status == "SUCCESS") {

                           swal('Success','Period Saved Successfully','success').then(function() {
                            window.location.replace("{{ URL('/') }}/admin/period_timing");
});
                     
                     

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
                $("#style-form").ajaxForm(options);
            });   
        });

    </script>
 

@endsection