@extends('layouts.admin_master')
@section('fees_mod', 'active')
@section('fee_summary', 'active')
@section('menuopenfeemod', 'active menu-is-opening menu-open') 

<?php use App\Http\Controllers\AdminController;
$slug_name = (new AdminController())->school; ?>
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Fee Concession', 'active' => 'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}"> 
    <style> 

        .collectionprofile img {
            height: auto;
            width: 2.1rem;
        }
    </style>
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:20px;" class="card-title">Fee Concession </h4>
                        <a href="#" data-toggle="modal" data-target="#smallModal" id="addbanner"><button class="btn btn-primary" style="float: right;">Add</button></a> 
                    </div>
 
                        <div id="show_data">

                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mt-3 mb-3 d-flex">
                                            <div class="collectionprofile mt-2">
                                                <img src="{{ asset('/public/image/avatar5.png') }}" class="img-circle elevation-2"
                                                    alt="User Image">
                                            </div>
                                            <div class="colllist" style="margin-left: 10px;">
                                                <span style="font-size:20px;" id="student_name"> {{$studentdetails['is_student_name']}}  </span><br>
                                                <span style="color: rgb(167, 166, 166)"><span id="class_name">{{$studentdetails['is_class_name']}} </span>-<span id="section_name">{{$studentdetails['is_section_name']}} </span>, Admission Number - <span id="admission_no">{{$studentdetails['admission_no']}} </span></span>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>

                        <section> 

                            <input type="hidden" name="student_id" id="student_id" value="{{$student_id}}">
                            <input type="hidden" name="batch" id="batch" value="{{$batch}}">

                            <div class="container">
                                <div class="col-md-12 mt-3 mb-3" id=" ">

                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                        <div class="table-responsicve">
                                            <table class="table table-striped table-bordered tblcountries">
                                              <thead>
                                                <tr>
                                                  <th>Category</th>
                                                  <th>Item</th> 
                                                  <th>Concession Amount</th>
                                                  <th>Concession Date</th> 
                                                </tr>
                                              </thead>

                                              <tfoot>
                                                  <th></th> <th></th> <th></th>
                                                  <th></th>  
                                              </tfoot>

                                              <tbody>

                                              </tbody>
                                            </table>
                                        </div>
                                    </div> 
                                </div>
                            </div> 

                        </section>

                        </div>
                </div>
            </div>

        </div>

    </section>

    <div class="modal fade in" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Concession</h4>
                </div>

                <form id="style-form" enctype="multipart/form-data"
                                  action="{{url('/admin/save/fee_concessions')}}"
                                  method="post">

                        {{csrf_field()}}
                        <input type="hidden" name="feeconcession_student_id" id="feeconcession_student_id" value="{{$student_id}}"> 
                    <div class="modal-body"> 
                         <div class="row fee_concessions_list"></div>
                    </div>
                    <div class="modal-footer">
                       <button type="sumbit" class="btn btn-link waves-effect" id="add_style">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts') 


<script>
    $('[data-widget="pushmenu"]').PushMenu("collapse");
    $('#addbanner').on('click', function() {
        $('#style-form')[0].reset();  
        var batch = $('#batch').val();
        var student_id = $('#student_id').val();
        var request = $.ajax({
            type: 'post',
            url: " {{URL::to('admin/load/feeconcessions')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                batch:batch,student_id:student_id
            },
            dataType:'json',
            encode: true
        });
        request.done(function (response) { 
            if (response.status == "SUCCESS") {
                $('.fee_concessions_list').html(response.data); 
            } else {
                $('.fee_concessions_list').html('No List');  
            }
            $('#smallModal-2').modal('show');

        });
        request.fail(function (jqXHR, textStatus) {

            swal("Oops!", "Sorry,Could not process your request", "error");
        });

    });

    function checkbalance($obj) {
        var max_val = $($obj).attr('max');  max_val = parseInt(max_val); console.log(max_val)
        var entered_val = $($obj).val();  entered_val = parseInt(entered_val);   console.log(entered_val)
        if(entered_val > max_val) {
            $($obj).val(max_val);
        }
    }

    $(function() {
        var table = $('.tblcountries').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            "ajax": {
                "url":"{{URL('/')}}/admin/feeconcessions/datatables/",
                data: function ( d ) { 
                    var batch = $('#batch').val();
                    var student_id = $('#student_id').val();
                    $.extend(d, {batch:batch, student_id:student_id});

                } 
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'item_name', name: 'item_name'}, 
                { data: 'concession_amount', name: 'concession_amount'},
                { data: 'concession_date', name:'concession_date'}, 

            ], 
            
        });

        $('.tblcountries tfoot th').each( function (index) {
           // if(index != 0 && index != 3) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //}
        } );

        $('#status_id').on('change', function() {
            table.draw();
        });

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
        } );   

        $('#add_style').on('click', function () {

            var options = {

                beforeSend: function (element) {

                    $("#add_style").text('Processing..');

                    $("#add_style").prop('disabled', true);

                },
                success: function (response) {



                    $("#add_style").prop('disabled', false);

                    $("#add_style").text('SUBMIT');

                    if (response.status == "SUCCESS") {

                       swal('Success',response.message,'success');

                       $('.tblcountries').DataTable().ajax.reload();

                       $('#smallModal').modal('hide');

                    }
                    else if (response.status == "FAILED") {

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



















