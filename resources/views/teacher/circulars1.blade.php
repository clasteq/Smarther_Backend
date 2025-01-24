@extends('layouts.teacher_master')
@section('mastersettings', 'active')
@section('master_circulars', 'active')
<?php
$breadcrumb = [['url' => URL('/teacher/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Slot', 'active' => 'active']];
?>
@section('content')


<meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">      
                          
                </div> 
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr>
                                  <th>Title</th>
                                  <th>Classes</th> 
                                  <th>Date</th>
                                  <th>Image</th> 
                                  <th>Status</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr><th></th><th></th><th></th>
                                      <th></th><th></th><th></th> 
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
        $('#addbtn').on('click', function () {
                $('#style-form')[0].reset();
            });
        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url": '{{route("circularlist.data")}}',
                },
                columns: [
                    { data: 'circular_title',  name: 'circular_title'},
                    { data: 'class_ids',  name: 'class_ids'},
                    { data: 'circular_date',  name: 'circular_date'},  
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {
                            if(data.circular_image != '' || data.circular_image != null){
                                var tid = data.is_circular_image;
                                return '<img src="'+tid+'" height="50" width="50">';
                            }   else {
                                return '';
                            }
                        },

                    }, 
                    { data: 'status',  name: 'status'},
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            return '<a href="#" onclick="loadCircular('+tid+')" title="Edit Circular"><i class="fas fa-edit"></i></a>'; 
                        },

                    },
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 1 },
                    { "orderable": false, "targets": 3 },
                    { "orderable": false, "targets": 5 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 1 && index != 3 && index != 5) {
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
            } ); 

            $("#datepicker_from").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left"
            });

            $("#edit_circular_date").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: "bottom left"
            }); 

        });
 


    </script>

@endsection