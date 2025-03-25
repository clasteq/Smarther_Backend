@extends('layouts.admin_master')
@section('communication_settings', 'active')
@section('master_rewards', 'active')
@section('menuopencomn', 'active menu-is-opening menu-open') 
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Categories', 'active'=>'active']];
?>
@section('content')

<?php 
$user_type = Auth::User()->user_type;
$session_module = session()->get('module'); //echo "<pre>"; print_r($session_module); exit;
?> 
@if((isset($session_module['Remarks']) && ($session_module['Rewards']['list'] == 1)) || ($user_type == 'SCHOOL'))
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header"> 
                    <div class="row"> 
                        <div class="form-group col-md-2">
                            <label class="form-label">Class </label>
                            <div class="form-line">
                                <select class="form-control" name="classid" id="classid" onchange="loadClassSectionHw(this.value);" >
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $course)
                                            <option value="{{ $course->id }}">{{ $course->class_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="form-label">Section </label>
                            <div class="form-line">
                                <select class="form-control section_id" name="sectionid" id="sectionid"  onchange="loadstudents(this.value,classid.value)">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div> 
                        <div class=" col-md-2 form-group">
                            <label class="form-label" >Students <span class="manstar">*</span></label>
                            <div class="form-line">
                                <select class="form-control" name="student_id" id="student_id" >

                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-2 " >
                            <label class="form-label">From</label>
                            <input class="date_range_filter date form-control col-md-10" type="text" id="datepicker_from"  />
                        </div>
                        <div class="form-group col-md-2 " >
                            <label class="form-label">To</label>
                            <input class="date_range_filter date form-control" type="text" id="datepicker_to"  />
                        </div>  
                        <div class="form-group col-md-2 " >
                            <label class="form-label"></label> 
                            <button class="btn btn-danger "  id="clear_style">Clear Filter</button>
                        </div>
                    </div>
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Class</th>
                                  <th>Section</th>
                                  <th>Scholar</th>
                                  <th>Description</th>
                                  <th>Created By</th>
                                  <th>Is Notified</th> 
                                  <th>Action</th> 
                                </tr>
                              </thead> 
                              <tbody>

                              </tbody>
                            </table>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </section> 
    <input type="hidden" name="getFetchSectionURL" id="getFetchSectionURL"  value="{{ url('admin/fetch-section') }}">
@else 
@include('admin.notavailable') 
@endif

@endsection

@section('scripts')

    <script> 
        $(function() {

            $('#clear_style').on('click', function () {
                $('.card-header').find('input').val('');
                $('.card-header').find('select').val('');
                tabledraw();
            }); 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/rewards/datatables/", 
                    data: function ( d ) {
                        var classid  = $('#classid').val();
                        var sectionid  = $('#sectionid').val();
                        var student_id  = $('#student_id').val(); 
                        var minDateFilter  = $('#datepicker_from').val();
                        var maxDateFilter  = $('#datepicker_to').val();
                        $.extend(d, {classid:classid, sectionid:sectionid, minDateFilter:minDateFilter, maxDateFilter:maxDateFilter, student_id:student_id});

                    }
                },
                columns: [
                    { data: 'created_at',  name: 'user_remarks.created_at'},
                    { data: 'class_name',  name: 'classes.class_name'},
                    { data: 'section_name',  name: 'sections.section_name'},
                    { data: 'name',  name: 'users.name'},
                    { data: 'remark_description',  name: 'user_remarks.remark_description'},
                    { data: 'posted_user.name',  name: 'user_remarks.created_by'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) { 
                            var remark_notify = data.remark_notify;
                            if(remark_notify == 1) return 'Yes'; else return 'No'; 
                        },

                    },
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            @if((isset($session_module['Remarks']) && ($session_module['Remarks']['delete'] == 1)) || ($user_type == 'SCHOOL'))
                            var tid = data.id;
                            return ' <a href="#" onclick="deletedata(' + tid +')" title="Delete Remark"><i class="fas fa-trash"></i></a>';
                            @else
                            return '';
                            @endif
                        },

                    },


                ],
                "order":[[0, 'desc']],
                "columnDefs": [ { "orderable": false, "targets": 1 }, { "orderable": false, "targets":2 }, 
                    { "orderable": false, "targets":3 },  { "orderable": false, "targets":5 }, { "orderable": false, "targets":6 }, { "orderable": false, "targets":7 } ],
                dom: 'Blfrtip',
                "buttons": [
                    {

                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-warning btn-md ml-3',
                        action: function (e, dt, node, config) {
                            $.ajax({
                                "url":"{{URL('/')}}/admin/rewards_excel/",   
                                "data": dt.ajax.params(),
                                "type": 'get',
                                "success": function(res, status, xhr) {
                                    var csvData = new Blob([res], {type: 'text/xls;charset=utf-8;'});
                                    var csvURL = window.URL.createObjectURL(csvData);
                                    var tempLink = document.createElement('a');
                                    tempLink.href = csvURL;
                                    tempLink.setAttribute('download', 'Rewards.xls');
                                    tempLink.click();
                                }
                            });
                        }
                    },

                ],
               
            });

            $('#status_id').on('change', function() {
                table.draw();
            });

            $('#classid').on('change', function() {
                table.draw();
            }); 

            $('#sectionid').on('change', function() {
                table.draw();
            }); 

            $('#student_id').on('change', function() {
                table.draw();
            });

            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            $("#datepicker_to").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            }).change(function() {
                tabledraw();
            }).keyup(function() {
                tabledraw();
            });

            function tabledraw() { 
                var minDateFilter  = $('#datepicker_from').val();
                var maxDateFilter  = $('#datepicker_to').val();
                if(new Date(maxDateFilter) < new Date(minDateFilter))
                {
                    alert('To Date must be greater than From Date');
                    return false;
                } 
                table.draw();
            }

            /*$('.tblcountries tfoot th').each( function (index) {
                if( index != 6 && index != 7) {
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
            } );*/ 
        });
 

        function deletedata(id){
            swal({
                title : "",
                text : "Are you sure to delete?",
                type : "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
            },
            function(isConfirm){
                if (isConfirm) {
                    var request = $.ajax({
                        type: 'post',
                        url: " {{URL::to('/admin/delete/rewards')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            id:id,
                        },
                        dataType:'json',
                        encode: true
                    });
                    request.done(function (response) {
                        if (response.status == 1) {

                            swal('Success',response.message,'success');

                            $('.tblcountries').DataTable().ajax.reload();
                        }
                        else{
                            swal('Oops',response.message,'error'); 
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {

                        swal("Oops!", "Sorry,Could not process your request", "error");
                    });
                }
            })


        }
        function loadClassSectionHw(val, selectedid, selectedval) {

            selectedid = selectedid || " ";
            selectedval = selectedval || " ";
            var class_id = val;
            var selid = selectedid;
            var selval = selectedval;

            $('.section_id').html(
                        '<option value="-1"> All Section </option>');
            $.ajax({
                url: $('#getFetchSectionURL').val(),
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {

                    $('.section_id').html(
                        '<option value="-1"> All Section </option>');
                    /*if (selid != null && selval != null) {
                        $("#edit_section_dropdown").append('<option selected value="' + selid + '">' + selval +
                            '  </option>');
                    }*/
                    $.each(res.section, function(key, value) {
                      var selected = '';
                      if (selid != null && selval != null) {
                           if(selid == value.id) {
                            selected = ' selected ';
                           }
                      }
                        $(".section_id").append('<option value="' + value
                            .id + '" '+selected+'>' + value.section_name + '</option>');
                    });
                }
            });
        }

        function loadstudents(section_id,class_id) { 
            $("#student_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-student') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    section_id: section_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#student_id').html(
                        '<option value="">-- Select Student --</option>');
                
                    $.each(res.student, function(key, value) {
                         $("#student_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }
    </script>

@endsection
