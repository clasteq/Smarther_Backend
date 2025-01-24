@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_changeholidays', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Change Holidays', 'active'=>'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"> 

    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Change Holidays 
                  </h4>
                    @if(empty($academic_start_date) || empty($academic_end_date))
                    <h5>Please update the Academic Start and End Dates in General Settings > Admin settings</h5>
                    @else 
                    <div class="row">
                        <div class=" col-md-3">
                            <label class="form-label" style="padding-bottom: 10px;">Month </label>
                            <div class="form-line">
                                <select class="form-control" name="yearmonth" id="yearmonth" onchange="loadHolidays();">
                                    <option value="">Select Month</option>
                                    @if (!empty($months))
                                        @foreach ($months as $yrmn)
                                            <option value="{{ $yrmn }}">{{ $yrmn }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select> 
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                        <form id="style-form"  action="{{url('/admin/save/changeholidays')}}" method="post">

                            {{csrf_field()}}
                        <div id="holidaysbox" class="row">
                            @include('admin.holidays_list')
                        </div>
                        
                        <div class="row">
                            <div class=" col-md-6">
                               <button type="submit" class="btn btn-link waves-effect" id="add_style">SAVE</button> 
                            </div>
                        </div>
                        </form>

                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        
                        <!-- <div class="card card-default">  
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-12">
                                    <div class="form-group">
                                      <label></label>
                                      <select class="duallistbox" name="departments[]" id="departments" required multiple> 
                                            @if (!empty($months))
                                        @foreach ($months as $yrmn)
                                            <option value="{{ $yrmn }}">{{ $yrmn }}
                                            </option>
                                        @endforeach
                                    @endif
                                      </select> 
                                    </div> 
                                  </div> 
                                </div> 
                              </div> 
                              <div class="card-footer">
                                 
                            </div>
                        </div> -->

                         
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </section> 
@endsection

@section('scripts')<!-- Bootstrap4 Duallistbox -->
 
 
    <script type="text/javascript">

        //Bootstrap Duallistbox
        //$('.duallistbox').bootstrapDualListbox()
 
        function mv(obj) {
            var tbl = $(obj).parents("table").attr('id'); 
            var tr = $(obj).parents("tr").remove().clone();

            if(tbl == 'table_source') { 
                tr.find("#holiday_type").val("w");
                tr.find(".move-row").text("<<");
                $("#table_dest tbody").append(tr);
            }   else { 
                tr.find("#holiday_type").val("h");
                tr.find(".move-row").text(">>");
                $("#table_source tbody").append(tr); 
            }
        }
   /*     
    $("#table_source .move-row").on("click", function() {
        var tr = $(this).parents("tr").remove().clone();
        tr.find(".move-row").text("-");
        $("#table_dest tbody").append(tr);
    });

    $("#table_dest .move-row").on("click", function() { 
        var tr = $(this).parents("tr").remove().clone();
        tr.find(".move-row").text("+");
        $("#table_source tbody").append(tr); alert('pp')
    });
 */



        function loadHolidays() {
            var yrmn = $('#yearmonth').val();
            var request = $.ajax({
                type: 'post',
                url: " {{URL::to('/admin/load/holidays')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    code:yrmn,
                },
                dataType:'json',
                encode: true
            });
            request.done(function (response) {
                if(response.status == 1) {
                    $('#holidaysbox').html(response.data);      
                }   else {
                    swal("Oops!", response.message, "error");
                }

            });
            request.fail(function (jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }

        $('#add_style').on('click', function () {

            var options = {

                beforeSend: function (element) {

                    $("#add_style").text('Processing..');

                    $("#add_style").prop('disabled', true);

                },
                success: function (response) {



                    $("#add_style").prop('disabled', false);

                    $("#add_style").text('SAVE');

                    if (response.status == 1) {

                       swal('Success',response.message,'success'); 

                    }
                    else if (response.status == 0) {

                        swal('Oops',response.message,'warning');

                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {

                    $("#add_style").prop('disabled', false);

                    $("#add_style").text('SAVE');

                    swal('Oops','Something went to wrong.','error');

                }
            };
            $("#style-form").ajaxForm(options);
        });

    </script>
@endsection
