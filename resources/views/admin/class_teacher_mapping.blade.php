@extends('layouts.admin_master')
@section('mapsettings', 'active')
@section('master_class_teachers', 'active')
@section('menuopenmap', 'active menu-is-opening menu-open')
<?php
$breadcrumb = [['url' => URL('/admin/home'), 'name' => 'Home', 'active' => ''], ['url' => '#', 'name' => 'Class Teachers', 'active' => 'active']];
?>
@section('content')



    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <!-- Exportable Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-headerd">
                        <h4 style="font-size:20px;" class="card-title"><!-- Class Teachers
                            <a href="#" data-toggle="modal" data-target="#smallModal"><button id="addbtn"
                                    class="btn btn-primary" style="float: right;">Add</button></a> -->
                        </h4> 
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
                                <div class="table-responsicve">
                                    <table class="table table-striped table-bordered tblcountries">
                                        <thead>
                                            <tr> 
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Teacher</th> 
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($sections))
                                                @foreach($sections as $key => $section)
                                                    <tr> 
                                                        <td>{{$section->class_name}}</td>
                                                        <td>{{$section->section_name}}</td>
                                                        <td>
                                                            <select class="form-control" name="teacher_id" id="teacher_id_{{ $section->id }}">
                                                                <option value="" >Select</option>
                                                                @if(!empty($teacher))
                                                                    @foreach($teacher as $teach)
                                                                    @php($selected = '')
                                                                    @if($section->teacher_id == $teach->id)
                                                                    @php($selected = 'selected')
                                                                    @endif
                                                                    <option value="{{$teach->id}}" {{$selected}}>{{$teach->name}} {{$teach->mobile}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </td> 
                                                        <td><button type="submit" name="submit[{{ $section->id }}]" id="submit_{{ $section->id }}" class="btn submit" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;" onclick="updateClassTeacher({{ $section->id }})">Update </button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr> 
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

       function updateClassTeacher(section_id) {
            var teacher_id = $('#teacher_id_'+section_id).val(); 
            teacher_id = $.trim(teacher_id);  
            if(teacher_id == '') {
                swal({
                    title : "",
                    text : "Are you sure to reset the Class teacher?",
                    type : "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                },
                function(isConfirm){
                    if (isConfirm) {
                        var request = $.ajax({
                            type: 'post',
                            url: " {{ URL::to('admin/save/ctutors_mapping') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                section_id: section_id,
                                teacher_id: teacher_id
                            },
                            dataType: 'json',
                            encode: true
                        });
                        request.done(function(response) {
                            if (response.status == 'SUCCESS') {
                                 swal("Success!", response.message, "success");
                            } else {
                                swal("Oops!", response.message, "error");
                            }

                        });
                        request.fail(function(jqXHR, textStatus) {

                            swal("Oops!", "Sorry,Could not process your request", "error");
                        });
                    }  
                })
            } else {
                var request = $.ajax({
                    type: 'post',
                    url: " {{ URL::to('admin/save/ctutors_mapping') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        section_id: section_id,
                        teacher_id: teacher_id
                    },
                    dataType: 'json',
                    encode: true
                });
                request.done(function(response) {
                    if (response.status == 'SUCCESS') {
                         swal("Success!", response.message, "success");
                    } else {
                        swal("Oops!", response.message, "error");
                    }

                });
                request.fail(function(jqXHR, textStatus) {

                    swal("Oops!", "Sorry,Could not process your request", "error");
                });
            } 
            
        }

    </script>
@endsection
