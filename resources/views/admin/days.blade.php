@extends('layouts.admin_master')
@section('mastersettings', 'active')
@section('master_days', 'active')
@section('menuopenm', 'active menu-is-opening menu-open')
<?php   use App\Http\Controllers\AdminController;  $slug_name = (new AdminController())->school; ?>
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Days', 'active'=>'active']];
?>
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Days   
                  </h4>        
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th>Day Name</th>  
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th>  </tr>
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
        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/days/datatables/",
                },
                columns: [ 
                    { data: 'day_name',  name: 'day_name'},  
                ],
                "columnDefs": [ 
                ]

            });

            $('.tblcountries tfoot th').each( function (index) { 
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' ); 
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
            } ); 
        });


    </script>

@endsection
