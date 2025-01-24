@extends('layouts.admin_master')
@section('report_settings', 'active') 
@section('master_studentstrength', 'active')
@section('menuopenr', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Banner', 'active'=>'active']];

 $years = range(2020, strftime("%Y", time()));  
?>
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Students Strength</h4>        
                    <div class="row">
                        <div class=" col-md-3">
                            <label class="form-label" >Class </label>
                            <div class="form-line">
                                <select class="form-control" name="cls_id" id="cls_id"  onchange="loadSection(this.value)">
                                    <option value="">Select Class</option>
                                   @if(@isset($classes))
                                   @foreach ($classes as $class)
                                   <option value="{{$class->id}}">{{$class->class_name}}</option>   
                                   @endforeach
                                   @endif
                                </select> 
                            </div>
                        </div>

                        <div class="form-group col-md-3 " >
                            <label class="form-label">Section </label>
                            <select class="form-control" name="section_id" id="section_id">
                           
                            </select>
                        </div>

                        <div class="form-group col-md-3 " >
                            <label class="form-label">Year </label> 
                            <select class="form-control" name="acadamic_year" id="acadamic_year">
                                <option value="">Select Year</option>
                                   @if(@isset($years))
                                   @foreach ($years as $year)
                                   <option value="{{$year}}" @if($year == date('Y')) selected @endif>{{$year}}</option>   
                                   @endforeach
                                   @endif
                            </select> 
                        </div>
                    </div>
               
                <div class="card-content collapse show">
                  <div class="card-body card-dashboard">
                    <div style="width: 90%; overflow-x: scroll; padding-left: -10px;">
                        <div class="table-responsicve">
                            <table class="table table-striped table-bordered tblcountries">
                              <thead>
                                <tr> 
                                  <th>Class</th> 
                                  <th>Section</th>  
                                  <th class="no-sort">Boys</th>
                                  <th class="no-sort">Girls</th>
                                  <th class="no-sort">Total</th>
                                  <th class="no-sort">Academic Year</th> 
                                </tr>
                              </thead>
                              
                              <tbody>
                                
                              </tbody>
                              <tfoot>
                                  <tr> 
                                  <th>Class</th> 
                                  <th>Section</th>  
                                  <th class="no-sort">Boys</th>
                                  <th class="no-sort">Girls</th>
                                  <th class="no-sort">Total</th>
                                  <th class="no-sort">Academic Year</th> 
                                </tr>
                              </tfoot>
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
        function loadSection(val) { 
            var class_id = val;
            $("#section_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-section') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#section_id').html(
                        '<option value="">-- Select Section --</option>');
                   
                    $.each(res.section, function(key, value) {
                        // alert(value.id)
                        $("#section_id").append('<option value="' + value
                            .id + '">' + value.section_name + '</option>');
                    });
                }
            });
        }

        $(function() { 

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/studentstrength/datatables/",   
                    data: function ( d ) { 
                        var class_id  = $('#cls_id').val();
                        var section_id  = $('#section_id').val(); 
                        var acadamic_year = $('#acadamic_year').val(); 
                        $.extend(d, { 
                            class_id:class_id,
                            section_id: section_id, 
                            acadamic_year: acadamic_year
                        });

                    }
                },
                columns: [ 
                    { data: 'class_name',  name: 'classes.class_name'},  
                    { data: 'section_name',  name: 'sections.section_name'},
                    { data: 'boys',  name: 'boys'}, 
                    { data: 'girls',  name: 'girls'},
                    { data: 'total',  name: 'total'},  
                    { data: 'academic_year',  name: 'academic_year'}, 
                ],
                "order": [],
                "columnDefs": [ 
                    {
                          "targets": 'no-sort',
                          "orderable": false,
                    }
                ]

            });

            /*$('.tblcountries tfoot th').each( function (index) {
                var title = $(this).text();
                if(index <= 1)
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            } );*/

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

            $('#cls_id').on('change', function() {
                table.draw();
            }); 
            $('#section_id').on('change', function() {
                table.draw();
            });
            $('#acadamic_year').on('change', function() {
                table.draw();
            });

        });
 
    </script>

@endsection
