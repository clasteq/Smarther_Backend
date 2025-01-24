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
                        <h4 style="font-size: 20px;" class="card-title">Add Periods
                          
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
                                                <select class="form-control course_id" name="class_id" id="class_id" >
                                                    <option value="">Select Class</option>
                                                    @if (!empty($classes))
                                                        @foreach ($classes as $class)
                                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
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
                                                        <input type="hidden" name="id" id="id">
                                                        <th><input type="time"  name="period_1"></th>
                                                        <th><input type="time" name="period_2"></th>
                                                        <th><input type="time" name="period_3"></th>
                                                        <th><input type="time" name="period_4"></th>
                                                        <th><input type="time" name="period_5"></th>
                                                        <th><input type="time" name="period_6"></th>
                                                        <th><input type="time" name="period_7"></th>
                                                        <th><input type="time" name="period_8"></th>
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

                           swal('Success','Period Saved Successfully','success');

                        //    window.location.href = 'admin/period_timing';
                           window.location.replace("{{ URL('/') }}/admin/period_timing");

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