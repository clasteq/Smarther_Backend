@extends('layouts.teacher_master')
@section('test_settings', 'active')
@section('master_testlist', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/teacher/home'), 'name'=>'Home', 'active'=>''], ['url'=>'#', 'name'=>'Test list', 'active'=>'active']];
?>
@section('content') 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 style="font-size: 20px;" class="card-title">Test List 
                    <a href="{{URL('/')}}/teacher/add/testlist"><button id="addbtn" class="btn btn-primary" style="float: right;">Create Test</button></a>  
                  </h4>        
                  <div class="row">
                    <div class=" col-md-3">
                        <label class="form-label" style="padding-bottom: 10px;">Class </label>
                        <div class="form-line">
                            <select class="form-control" name="class_id" id="class_id" onchange="loadmappedclassSubjects(this.value);">
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
                        <label class="form-label" style="padding-bottom: 10px;">Subject</label>
                        <div class="form-line">
                            <select class="form-control" name="subject_id" id="subject_id" >
                                <option value="">All</option>
                             @if (!empty($subject))
                                 @foreach ($subject as $subjects)
                              
                                 <option value={{$subjects->id}} >{{$subjects->subject_name}}</option>

                                 @endforeach
                             @endif
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
                                </tr>
                              </thead>
                              <tfoot>
                                  <tr>
                                    <th></th><th></th><th></th>
                                      <th></th><th></th> 
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
                    "url":"{{URL('/')}}/teacher/testlist/datatables/",  
                    data: function ( d ) {
                        var class_id  = $('#class_id').val();
                        var subject_id = $('#subject_id').val();
                        $.extend(d, {class_id:class_id,subject_id:subject_id});

                    }
                },
                columns: [ 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id; 
                            var url = "{{URL('/')}}/teacher/edittestlist?id="+tid;
                            var vurl = "{{URL('/')}}/teacher/view/testlist?id="+tid;
                            return '<a href="'+vurl+'" target="_blank" title="View Test"><i class="fas fa-eye"></i></a> &nbsp; <a href="'+url+'"  target="_blank" title="Edit Test"><i class="fas fa-edit"></i></a>'; 
                        },

                    },
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'class_name',  name: 'class_name'},
                    { data: 'subject_name',  name: 'subject_name'},
                    { data: 'test_name',  name: 'test_name'}, 
                    
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 4 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 0) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                }
            } );

            $('#class_id').on('change', function() {
                table.draw();
            });
            $('#subject_id').on('change', function() {
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
        });
   

        
        function loadmappedclassSubjects(val)
{
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
