@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_PeriodsTiming', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
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
                        <h4 style="font-size: 20px;" class="card-title">Periods Timing
                            <a href="{{URL('/')}}/admin/add/periods" ><button id="addbtn"
                                class="btn btn-primary" style="float: right;">Add</button></a>
                        </h4>
                       
                    {{-- </div> --}}
                        <div class="row"> 
                          
                            <div class="col-md-3">
                                <label class="from-label">Class</label>
                                <select class="form-control course_id" name="class_id" id="class_id">
                                    <option value="">Select Class</option>
                                    @if (!empty($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
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
                                                <th>Action</th>
                                                <th>Class</th>
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
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
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

@endsection

@section('scripts')

    <script>
        $('#addbtn').on('click', function() {
            $('#style-form')[0].reset();
        });
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/periods/datatables/",   
                    data: function ( d ) {
                        var class_id  = $('#class_id').val();
                        $.extend(d, {class_id:class_id});

                    }
                },
                columns: [
                    {
                        data: null,
                        "render": function(data, type, row, meta) {

                            var tid = data.id;
                            var url = "{{URL('/')}}/admin/edit/period_timing?id="+tid;
                            return '<a href="'+url+'" ><i class="fas fa-edit"></i></a>';
                        },

                    },
                    {
                        data: 'is_class_name',
                        name: 'class_id'
                    },
                    {
                        data: 'period_1',
                        name: 'period_1'
                    },
                    {
                        data: 'period_2',
                        name: 'period_2'
                    },
                    {
                        data: 'period_3',
                        name: 'period_3'
                    },
                    {
                        data: 'period_4',
                        name: 'period_4'
                    },
                    {
                        data: 'period_5',
                        name: 'period_5'
                    },
                    {
                        data: 'period_6',
                        name: 'period_6'
                    },
                   
                    {
                        data: 'period_7',
                        name: 'period_7'
                    },
                    {
                        data: 'period_8',
                        name: 'period_8'
                    },
                   
                ],
                "order":[[1, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
               
            });

            $('.tblcountries tfoot th').each(function(index) {
                if (index != 0) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                }
            });
            $('#class_id').on('change', function() {
                table.draw();
            });

            // Apply the search
            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#add_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#add_style").text('Processing..');

                        $("#add_style").prop('disabled', true);

                    },
                    success: function(response) {



                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal').modal('hide');

                        } else if (response.status == "FAILED") {

                            swal('Oops', response.message, 'warning');

                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        $("#add_style").prop('disabled', false);

                        $("#add_style").text('SUBMIT');

                        swal('Oops', 'Something went to wrong.', 'error');

                    }
                };
                $("#style-form").ajaxForm(options);
            });
            $('#edit_style').on('click', function() {

                var options = {

                    beforeSend: function(element) {

                        $("#edit_style").text('Processing..');

                        $("#edit_style").prop('disabled', true);

                    },
                    success: function(response) {

                        $("#edit_style").prop('disabled', false);

                        $("#edit_style").text('SUBMIT');

                        if (response.status == "SUCCESS") {

                            swal('Success', response.message, 'success');

                            $('.tblcountries').DataTable().ajax.reload();

                            $('#smallModal-2').modal('hide');

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
            });
        });

        function loadCountry(id) {

            var request = $.ajax({
                type: 'post',
                url: " {{ URL::to('admin/edit/slot') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                },
                dataType: 'json',
                encode: true
            });
            request.done(function(response) {

                $('#id').val(response.data.id);
                $('#edit_slot_name').val(response.data.slot_name);
                $('#edit_from_time').val(response.data.from_time);
                $('#edit_to_time').val(response.data.to_time);
                $('#edit_position').val(response.data.position);
                $('#edit_status').val(response.data.status);
                $('#smallModal-2').modal('show');

            });
            request.fail(function(jqXHR, textStatus) {

                swal("Oops!", "Sorry,Could not process your request", "error");
            });
        }
    </script>

@endsection
