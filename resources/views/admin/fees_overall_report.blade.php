@extends('layouts.admin_master')
@section('feessettings', 'active')
@section('fee_overall', 'active')
@section('menuopenfee', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Overall Fees', 'active'=>'active']];
?>
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.0/css/fixedHeader.dataTables.css">

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

        /* Set a fixed scrollable wrapper */
        .tableWrap {
          height: 500px;
          border: 2px solid black;
          overflow: auto;
        }
        /* Set header to stick to the top of the container. */
        thead tr th {
          position: sticky;
          top: 0;
        }

        /* If we use border,
        we must use table-collapse to avoid
        a slight movement of the header row */
        table {
         border-collapse: collapse;
        }

        /* Because we must set sticky on th,
         we have to apply background styles here
         rather than on thead */
        th {
          padding: 16px;
          padding-left: 15px;
          border-left: 1px dotted rgba(200, 209, 224, 0.6);
          border-bottom: 1px solid #e8e8e8;
          background: #A3D10C;
          text-align: left;
          /* With border-collapse, we must use box-shadow or psuedo elements
          for the header borders */
          box-shadow: 0px 0px 0 2px #e8e8e8;
        }

        /* Basic Demo styling */
        table {
          width: 100%;
          font-family: sans-serif;
        }
        table td {
          padding: 16px;
        }
        tbody tr {
          border-bottom: 2px solid #e8e8e8;
        }
        thead {
          font-weight: 500;
          color: rgba(0, 0, 0, 0.85);
        }
        tbody tr:hover {
          background: #e6f7ff;
        }

        .dataTables_filter {
            display: block !important;
        }
        .bold {
            font-weight: bold;
        }
        .dtfh-floatingparent  .tblcountries {
            left: 0% !important;
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
                <div class="card-header d-none">
                  <h4 style="font-size:20px;" class="card-title"><!-- Students Attendance Report --></h4>   
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                         
                        
                        <div class="clearfix"> &nbsp;</div> 
                        <div class="table-responsicve" id="attendanceentries">
                            
                            <table class="table table-striped table-bordered tblcountries">
                                <thead>
                                    <tr> 
                                        <th>Class</th> 
                                        <th>Section</th>
                                        <th>Total Scholars</th>
                                        <th>Total Fees</th>
                                        <th>Collected</th>
                                        <th>Concession</th> 
                                        <th>Balance</th>
                                        <th>Paid %</th>
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
 
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.0/js/dataTables.fixedHeader.js"></script>
<script src="https://cdn.datatables.net/fixedheader/4.0.0/js/fixedHeader.dataTables.js"></script>

    <script type="text/javascript"> 
        $(function() {
       
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: false,
                fixedColumns: true,
                fixedHeader: true, 
                responsive: false,
                paging:   false,/*
                searching: false,
                "ordering": false, */
                "ajax": {/*$total = $oa_boys + $oa_girls;*/
                    "url":"{{URL('/')}}/admin/fees_overall_report/datatables/",  
                    data: function ( d ) { 
                        var class_id = $('#class_id').val();
                        var section_dropdown  = $('#section_dropdown').val(); 
                        $.extend(d, { class_id:class_id, section_dropdown:section_dropdown});

                    }
                },

                columns: [  
                    { data: 'class_name',  name: 'class_name'},  
                    { data: 'section_name',  name: 'section_name'},  
                    { data: 'total_scholars',  name: 'total_scholars'},  
                    { data: 'total_fees',  name: 'total_fees'},  
                    { data: 'total_collected',  name: 'total_collected'},
                    { data: 'total_concession',  name: 'total_concession'}, 
                    { data: 'total_balance',  name: 'total_balance'},  
                    { data: 'paid_percentage',  name: 'paid_percentage'},   
                ],
                "order": [],
                "columnDefs": [ 
                    { "targets": 'no-sort', "orderable": false, },
                    { className: 'bold', targets: [0,3,7] }
                ],  
            });   

            $('#class_id').on('change', function() {
                table.draw();
            });
            $('#section_dropdown').on('change', function() {
                table.draw();
            });  

        } );
 
    </script>
@endsection
