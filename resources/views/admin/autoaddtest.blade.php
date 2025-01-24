@extends('layouts.admin_master')
@section('test_settings', 'active')
@section('master_autoaddtestlist', 'active')
@section('menuopent', 'active menu-is-opening menu-open')
<?php  
$breadcrumb = [['url'=>URL('/admin/home'), 'name'=>'Home', 'active'=>''], ['url'=>URL('/admin/testlist'), 'name'=>'Tests', 'active'=>''], ['url'=>'#', 'name'=>'Add Automatic Test', 'active'=>'active'] ];
?>
@section('content')
 
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <!-- Exportable Table -->
        <div class="content container-fluid"> 
            <div class="panel"> 
                <!-- Panel Heading -->
                <div class="panel-heading"> 
                </div>
                <div class="panel-body">  
                    <div class="row"> 
                        <div class="col-xs-12 col-md-12"> 
                        <div class="card"> 
                            <div class="card-body">
                                <h4 style="font-size: 20px;" class="panel-title">Add Test</h4>
                                <div class="row"><div class="col-md-12">
                                    <form name="frm_questionbank" id="frm_questionbank" method="post" action="{{url('/admin/view/qbfrautotest')}}"> 
                                    {{csrf_field()}}
                                    <div class="row">
                                      
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Class</label>
                                            <div class="form-line">
                                                <select class="form-control" name="class_id" id="class_id" required onchange="loadClassSubjects(this.value,'','',1); loadClassTerms(this.value);">
                                                    <option value="">Select Class</option>
                                                    @if(!empty($classes))
                                                        @foreach($classes as $class)
                                                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Subject</label>
                                            <div class="form-line">
                                                <select class="form-control" name="subject_id" id="subject_id" required>
                                                    <option value="">Select Subject</option>
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Term</label>
                                            <div class="form-line">
                                                <select class="form-control" name="term_id" id="term_id" required>
                                                    <option value="">Select Term</option>
                                                    @if(!empty($terms))
                                                        @foreach($terms as $term)
                                                            <option value="{{$term->id}}">{{$term->term_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6">
                                            <label class="form-label">Create Test Papers</label>
                                            <div class="form-line">
                                                <select class="form-control" name="test_papers" id="test_papers" required onchange="loadmodepaper();">
                                                    <option value="NO">NO</option> 
                                                    <option value="YES">YES</option> 
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float float-left col-md-6"> 
                                            <div class="form-line">
                                                <button class="btn btn-success center-block" type="button" name="createqbfrtest" id="createqbfrtest">Submit</button>
                                            </div>
                                        </div>
                                    </div> 
                                    <hr>
                                    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                        <div class="table-responsicve">
                                            <table class="table table-striped table-bordered tblcountries">
                                              <thead>
                                                <tr> 
                                                  <th>Term</th>
                                                  <th>Class</th>
                                                  <th>Subject</th> 
                                                  <th>Chapter</th> 
                                                  <th>Action</th>
                                                </tr>
                                              </thead>
                                              <tfoot>
                                                  <tr><th></th><th></th><th></th>
                                                      <th></th><th></th> 
                                                  </tr>
                                              </tfoot>
                                              <tbody>
                                                
                                              </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success center-block float-right" id="Submit">Submit</button> 
                                    </form>
                                </div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" name="autotest" id="autotest" value="{{url('/admin/view/qbfrautotest')}}">
<input type="hidden" name="autotestpaper" id="autotestpaper" value="{{url('/admin/view/qbfrautotestpapers')}}">
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
      <script>
        function loadmodepaper() {
            var $test_papers = $('#test_papers').val();
            if($test_papers == 'YES') {
                $('#frm_questionbank').attr('action', $('#autotestpaper').val());
            }   else {
                $('#frm_questionbank').attr('action', $('#autotest').val());
            }
        }

        $(function() {  

            var table = $('.tblcountries').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                "ajax": {
                    "url":"{{URL('/')}}/admin/qbfrtest/datatables/",  
                    data: function ( d ) {       
                        var class_id  = $('#class_id').val(); 
                        var subject_id  = $('#subject_id').val();
                        var term_id  = $('#term_id').val(); 
                        $.extend(d, { class_id:class_id, subject_id:subject_id, term_id:term_id});
                    }

                },
                columns: [ 
                    { data: 'term_name',  name: 'term_name'},
                    { data: 'class_name',  name: 'class_name'},
                    { data: 'subject_name',  name: 'subject_name'},
                    { data: 'chaptername',  name: 'chaptername'}, 
                    {
                        data:null,
                        "render": function ( data, type, row, meta ) {

                            var tid = data.id;  
                            return '<input type="checkbox" name="qbid[]" id="qbid_'+tid+'" value="'+tid+'" />'; 
                        },

                    },
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 4 }
                ]

            });

            $('.tblcountries tfoot th').each( function (index) {
                if(index != 4) {
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

            $("#createqbfrtest").on('click', function () { 
                table.draw();
            }); 
  
        });

    </script>
 

@endsection

