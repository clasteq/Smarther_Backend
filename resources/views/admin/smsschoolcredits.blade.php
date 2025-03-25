@extends('layouts.admin_master')
@section('communication_settings', 'active')
@section('master_smsschoolcredits', 'active')
@section('menuopencomn', 'active menu-is-opening menu-open')  
<?php
$user_type = Auth::User()->user_type;
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'SMS School Credits', 'active'=>'active']];
?>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($user_type == "SUPER_ADMIN")
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title"><!-- SMS Credits -->

                    <div class="row col-md-12">
                        <div class="form-inline col-md-12 " >
                            <label class="form-label mr-1">School</label>
                            <select class="form-control" name="school_id" id="school_id">
                             <option value="" >All</option>
                             @if(!empty($schools))
                                @foreach($schools as $school)
                                    <option value="{{$school->id}}">{{$school->name}}</option>
                                @endforeach
                             @endif 
                         </select>
                        </div> 
                    </div>  
                  </h4> 
                </div>
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr> 
                                  <th>School Name</th>
                                  <th>Total Credits</th>  
                                  <th>Available Credits</th>  
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <!-- <tfoot>
                                  <tr><th></th><th></th><th></th></tr>
                              </tfoot> -->
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
    @else 
    <section class="content">
        @include('admin.notavailable')
    </section>
    @endif
@endsection

@section('scripts')

    <script> 
        $(function() {
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/smsschoolcredits/datatables/", 
                    data: function ( d ) {
                        var school_id  = $('#school_id').val();
                        $.extend(d, {school_id:school_id}); 
                    }
                },
                columns: [
                    /*{
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;
                            return '<a href="#" onclick="loadCategory('+tid+')" title="Edit smscredits"><i class="fas fa-edit"></i></a>';
                        },

                    },*/
                    { data: 'name',  name: 'users.name'},
                    { data: 'is_total_credits',  name: 'total_credits'}, 
                    { data: 'available_credits',  name: 'available_credits'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.school_id; var vurl = "{{URL('/')}}/admin/smscredits?id="+tid;
                            return '<a href="'+vurl+'"  title="View Credit History" target="_blank"><i class="fas fa-eye mr-1"></i></a>'; 
                        },

                    },
                ],
                "order":[[2, 'asc']],
                "columnDefs": [
                   /* { "orderable": false, "targets": 0 }*/
                ],
               
            });

            /*$('.tblcountries tfoot th').each( function (index) {
                //if( index != 2 && index != 3) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                //}
            } );*/

            $('#school_id').on('change', function() {
                table.draw();
            });
            // Apply the search
            /*table.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                                .search( this.value )
                                .draw();
                    }
                } );
            } ); */
        });
 
    </script>

@endsection
