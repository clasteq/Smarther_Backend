@extends('layouts.teacher_master')
@section('test_settings', 'active')
@section('master_testlistpapers', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Test List', 'active'=>'active']];
?>
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size:20px;" class="card-title">Test List Papers
                    <a href="{{URL('/')}}/teacher/auto/testlistpapers"><button id="addbtn" class="btn btn-primary" style="float: right;">Create Test</button></a>  
                  </h4>        
                  <div class="row">
                    <div class="form-group col-md-3 " >
                        <label class="form-label">From</label>
                        <input class="date_range_filter date form-control" type="text" id="datepicker_from"  />
                    </div>
                    <div class="form-group col-md-3 " >
                        <label class="form-label">To</label>
                        <input class="date_range_filter date form-control" type="text" id="datepicker_to"  />
                    </div>
                    <div class=" col-md-3">
                        <label class="form-label" >Class </label>
                        <div class="form-line">
                            <select class="form-control" onchange="loadmappedclassSubjects(this.value)" name="class_id" id="class_id" >
                                <option value="">All</option>
                                @if (!empty($class))
                                @foreach ($class as $classes)
                               
                                <option value={{$classes->id}}>{{$classes->class_name}}</option>

                                @endforeach
                            @endif
                            </select>
                            </select>
                        </div>
                    </div>

                    <div class=" col-md-3">
                        <label class="form-label" >Subject</label>
                        <div class="form-line">
                            <select class="form-control" name="subject_id" id="subject_id" >
                             </select>
                            </select>
                        </div>
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
                                  <th>Term</th>
                                  <th>Class</th>
                                  <th>Subject</th> 
                                  <th>Test</th> 
                                  <th>No of papers</th> 
                                  <th>Created At</th> 
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr>
                                    <th></th><th></th><th></th>
                                    <th></th><th></th><th></th> 
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
        $(function() { 
            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/teacher/testlistpapers/datatables/",   
                    data: function ( d ) {
                        var class_id  = $('#class_id').val();
                        var subject_id = $('#subject_id').val();
                        var minDateFilter  = $('#datepicker_from').val();
                        var maxDateFilter  = $('#datepicker_to').val();
                        $.extend(d, {class_id:class_id,subject_id:subject_id,
                        minDateFilter:minDateFilter,
                        maxDateFilter:maxDateFilter});

                    }
                },
                columns: [ 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            /*var url = "{{URL('/')}}/admin/edittestlist/"+tid; &nbsp; <a href="'+url+'"  target="_blank" title="Edit Test"><i class="fas fa-edit"></i></a>*/
                            var vurl = "{{URL('/')}}/teacher/view/testlistpapers?id="+tid;
                            return '<a href="'+vurl+'" target="_blank" title="View Test Papers"><i class="fas fa-eye"></i></a>'; 
                        },

                    },
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'class_name',  name: 'classes.class_name'},
                    { data: 'subject_name',  name: 'subject_name'},
                    { data: 'test_name',  name: 'test_name'}, 
                    { data: 'no_of_papers',  name: 'no_of_papers'}, 
                    { data: 'created_at',  name: 'test_papers.created_at'}, 
                ],
                "order": [[6, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 0 && index != 6) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#class_id').on('change', function() {
                tabledraw();
            });
            $('#subject_id').on('change', function() {
                tabledraw();
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
        });
   


        function fetchclasssubject(class_id) {
            $("#subject_id").html('');
            $.ajax({
                url: "{{ url('admin/fetch-class-subject') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    $('#subject_id').html(
                        '<option value="">-- Select Subject --</option>');
                
                    $.each(res.subjects, function(key, value) {
                         $("#subject_id").append('<option value="' + value
                            .id + '">' + value.subject_name + '</option>');
                    });
                }
            });
        }


        function loadmappedclassSubjects(val)  {
            var class_id = val;
            $("#subject_id").html('');
            $.ajax({
                url: "{{ url('teacher/fetch-class-subject') }}",
                type: "POST",
                data: {
                    class_id: class_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {

                    $('#subject_id').html(
                            '<option value="">-- Select Subject --</option>');
                    $.each(res.subject, function(key, value) {
                        $("#subject_id").append('<option value="' + value
                            .id + '" >' + value.subject_name + '</option>');
                    });
                }
            });
        }

    </script>

@endsection
