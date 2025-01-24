@extends('layouts.admin_master')
@section('staattendance_settings', 'active')
@section('master_tattendance', 'active')
@section('menuopenstaatt', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Teachers Attendance', 'active' => 'active']];
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
                  <h4 class="card-title" style="font-size: 20px;">Teachers Daily Attendance  </h4> 
                  
                        <div class="row"> 
                           
                            <div class="col-md-4" hidden>
                                <br>
                                <label style="padding-bottom: 10px;">Academic Year</label>
                                <input type="month" class="form-control"
                                    style="margin: 0px 0 23px !important;padding: 18px 22px !important;"
                                    name="monthyear" id="monthyear" value="{{ $monthyear }}" minlength="4" maxlength="4" onkeypress="return isNumber(event)">
                            </div> 

                            <div class=" col-md-3">
                                <br>
                                <label class="form-label" >Date </label>
                                <div class="form-line">
                                    <input class="date_range_filter date form-control" name="date" type="text" id="datepicker_from"  />
                                </div>
                            </div>

                            <div class="col-md-2" style="padding-top: 10px;">
                               <br>
                                <button type="submit"  class="btn signupBtn"
                                    style="background:#A3D10C;border-radius: 6px;padding: 8px 13px;margin-top:22px;"
                                    onclick="loadTeacherattendance()">Submit </button>
                            </div>
                            <div class="col-md-1"></div>

                        </div>
                        
                          
                <br>
                
                <div class="col-md-12" id="attendanceentries">
                   @include('admin.loadteachers_dailyattendance')
                </div>

            </div>          
                      
              </div>
            </div>
          </div>
    </section> 
 
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                // maxDate: '0',   
              });
            //   $("#datepicker_from").datepicker({maxDate: new Date()});
            $("#datepicker_from").datepicker('setDate', new Date())
            // checkSession();
            // checkanSession();
        });

        function putattendance(teacherid, mode, day, obj) {
            var monthyear = $('#monthyear').val(); 
            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/update/teacherattendance') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear,
                    teacherid: teacherid,
                    mode: mode,
                    day: day, 
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == 1) {
                    if (mode == 1) {
                        $(obj).parent('td').html('<i class="fa fa-check greentick" aria-hidden="true"></i>');
                    } else {
                        $(obj).parent('td').html('<i class="fa fa-times redcross" aria-hidden="true"></i>');
                    }
                } else {
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function loadTeacherattendance() {
            var monthyear = $('#monthyear').val(); 
            var date = $('#datepicker_from').val();
            date = $.trim(date);
            monthyear = $.trim(monthyear); 

            if(monthyear == '') {
                swal("Oops!", "Please select the Academic Year", "error");
                return false;
            }

            if(date == '') {
                swal("Oops!", "Please select the Academic Year", "error");
                return false;
            }


            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/load/dailyteacherattendance') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    monthyear: monthyear, 
                    date : date
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {
                if (response.status == "SUCCESS") {
                    $('#attendanceentries').html(response.data);
                } else {
                    $('#attendanceentries').html(response.message);
                }

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        function excelFunction(slot) {

            var monthyear = $('#monthyear').val();
            var slot = $('#slot').val(); 

            $.ajax({
                "url":"{{URL('/')}}/admin/tattendencereport/excel/",  
                type: 'get',
                data: {
                    monthyear: monthyear,
                    slot: slot
                },
                "success": function(res, status, xhr) {
                    var csvData = new Blob([res], {
                        type: 'application/xls;charset=utf-8;'
                    });
                    var csvURL = window.URL.createObjectURL(csvData);
                    var tempLink = document.createElement('a');
                    tempLink.href = csvURL;
                    tempLink.setAttribute('download', 'Students Attendence.xls');
                    tempLink.click();
                }
            });
        }


        function checkSession(){
  
    if($('#fn_chk').prop('checked')){
       $.ajax({
             url: "{{ url('admin/fetch_teachers') }}",
             type: "POST",
             data: {
                _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
            $.each(res.teachers, function(key, value) {
                $("input.fn_section[value='" + value.id + "']").prop('checked', true);
             });


             }
         });

 }else{
  
       $.ajax({
             url: "{{ url('admin/fetch_teachers') }}",
             type: "POST",
             data: {
                  _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
            $.each(res.teachers, function(key, value) {
                $("input.fn_section[value='" + value.id + "']").prop('checked', false);
             });


             }
         });
 }
}

function checkanSession(){
   if($('#an_chk').prop('checked')){
     $.ajax({
           url: "{{ url('admin/fetch_teachers') }}",
           type: "POST",
           data: {
              _token: '{{ csrf_token() }}'
           },
           dataType: 'json',
           success: function(res) {
          $.each(res.teachers, function(key, value) {
              $("input.an_section[value='" + value.id + "']").prop('checked', true);
           });


           }
       });

}else{
     $.ajax({
           url: "{{ url('admin/fetch_teachers') }}",
           type: "POST",
           data: {
            _token: '{{ csrf_token() }}'
           },
           dataType: 'json',
           success: function(res) {
          $.each(res.teachers, function(key, value) {
              $("input.an_section[value='" + value.id + "']").prop('checked', false);
           });


           }
       });
}
}

function saveTimetable() {
        //$('#edit_style').on('click', function() {
             var options = {

                beforeSend: function(element) {

                    $("#edit_style").text('Processing..');

                    $("#edit_style").prop('disabled', true);

                },
                success: function(response) {

                    $("#edit_style").prop('disabled', false);

                    $("#edit_style").text('SUBMIT');

                    if (response.status == "SUCCESS") {

                        swal({title: "Success", text: response.message, type: "success"},
                            function(){
                                window.location.reload();
                            }
                        );

                    } else if (response.status == "FAILED") {

                        swal('Oops', response.message, 'warning');

                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {

                    $("#edit_style").prop('disabled', false);

                    $("#edit_style").text('SUBMIT');

                    swal('Oops', 'Something went to wrong.', 'error');

                }
            };
            $("#edit-style-form").ajaxForm(options);
        }

        function check_main_fn(){
            $.ajax({
             url: "{{ url('admin/fetch_teachers') }}",
             type: "POST",
             data: {
                  _token: '{{ csrf_token() }}'
             },
             dataType: 'json',
             success: function(res) {
            $.each(res.teachers, function(key, value) {
                $("input.fn_chk[value='" + value.id + "']").prop('checked', false);
             });


             }
         });
        }

    </script>
@endsection
