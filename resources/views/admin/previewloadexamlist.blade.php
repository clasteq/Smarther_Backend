<?php $session = ''; ?>
@if(!empty($classes))
<form id="edit-style-form" enctype="multipart/form-data" style="width:120%;overflow: hidden;
white-space: nowrap;
text-overflow: ellipsis;" action="{{ url('/admin/save/exams') }}" method="post">
  <input type="hidden" name="from_date" id="from_date" value="{{$start_date}}">
  <input type="hidden" name="last_date" id="last_date" value="{{$end_date}}">
  <input type="hidden" name="examname" id="examname" value="{{$exam_name}}">
  <input type="hidden" name="month_year" id="month_year" value="{{$monthyear}}">
   <input type="hidden" name="id" id="id" value="{{$exam_id}}">
    {{ csrf_field() }} 
    {{-- <div class="card-content collapse show">
        <div class="card-body card-dashboard"> --}}
            <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
    <table class="table table-striped table-bordered tblcountries" style="width:100% !important">
        <thead>
            <tr>
                <th >Classes</th>
                <th >Section</th>
                @if(!empty($start_date) && !empty($end_date) )
            
                @foreach ($periods as $periodtiming)
                {{-- @if($periodtiming != '00:00') --}}
                    <th style=" word-wrap: break-word;overflow-wrap: break-word;">{{$periodtiming}}</th>
                    {{-- @endif --}}
                @endforeach
                @endif
            </tr>
        </thead>
        <tfoot>

@if(!empty($classes))
    @foreach ($classes as $ck => $class)
        @if(isset($classes_arr[$ck]['subjects']) && is_array($classes_arr[$ck]['subjects']) && count($classes_arr[$ck]['subjects'])>0)    
            @foreach ($classes_arr[$ck]['subjects'] as $sk=>$subs) 
                <tr>

                    <td>{{ $class->class_name }}

                        <input type="hidden" name="tclass_id" id="tclass_id[]" value="{{$class->id}}">
                        <input type="hidden" name="tsection_id" id="tsection_id[]" value="{{$sk}}">
                    </td>
                    <td>{{ $sections[$sk] }}</td>

                    @foreach ($periods as $key => $periodtiming) 

                    <?php
                    $an_section = array();
                    $fn_section = array();

                           $orderdate = explode('-', $periodtiming);
                            $year = $orderdate[0];
                            $month   = $orderdate[1];
                            $day  = $orderdate[2];
                            $day = $day * 1;
                            $new_date = 'day_'.$day;
                           

                       
                    ?>
                  
                   <td>
                    {{-- <input type="text" id="an_chk" data-key="{{$key}}"> --}}
                     <select class="form-control course_id" style="width:100% !important;" name="subject_id[{{$class->id}}][{{$sk}}][{{$periodtiming}}]" disabled >
                                <option value="0">Select Subject</option>
                               @if(isset($class->is_mapped_subjects))
                               <?php 
                             
                               ?>
                                 @foreach ($subs as $subject)
                                 <?php $selected = ''; $session = '';
                                        
                                    if(isset($examsarr['exam_session_structure'][$class->id][$sk][$periodtiming])        && $examsarr['exam_session_structure'][$class->id][$sk][$periodtiming]['subject_id'] == $subject->id){
                                            $selected = ' selected ';  
                                        } 

                                    if(isset($examsarr['exam_session_structure'][$class->id][$sk][$periodtiming])) {
                                        $session = $examsarr['exam_session_structure'][$class->id][$sk][$periodtiming]['session'];  
                                    } 
                                
                                 /*if(isset($timetable[$class->id]) && isset($timetable[$class->id][$periodtiming])) {
                                    
                                    if($timetable[$class->id][$periodtiming] == $subject->id) {
                                         $selected = ' selected ';
                                         
                                     }
                                 }*/
                                 ?>
                                        <option value="{{$subject->id}}" {{$selected}}>
                                            {{ $subject->subject_name}}
                                        </option>
                                   <?php 
                                   array_push($an_section,$subject->id.'_an');
                                   array_push($fn_section,$subject->id.'_fn');


                                    ?>
                                      @endforeach
                                      @endif
                                   
                            </select>
                            <?php //echo "<pre>".$class->id.$periodtiming; print_r($examsarr['exam_session_structure']);  ?>
                            &nbsp;&nbsp;<?php if(!isset($timetable[$class->id][$periodtiming]) && !isset($timetable[$class->id])){?><input disabled type="radio" name="session[{{$class->id}}][{{$periodtiming}}]"  value="fn" @if($session == 'fn') checked @endif>@if($session == 'fn') <span class="blue">FN</span> @else FN @endif&nbsp;&nbsp;<input disabled type="radio"  name="session[{{$class->id}}][{{$sk}}][{{$periodtiming}}]" value="an" @if($session == 'an') checked @endif>@if($session == 'an') <span class="blue">AN</span> @else AN @endif<?php } ?>
                            <?php  
                        if(isset($timetable[$class->id]) && isset($timetable[$class->id][$periodtiming])) {
                       if(in_array($timetable[$class->id][$periodtiming],$fn_section)) {
                                     $checked = ' checked ';
                                     ?>
                                     <!-- <input type="radio" {{$checked}}  name="session[{{$class->id}}][{{$periodtiming}}]" value="fn">FN<?php } else{?><input disabled type="radio" name="session[{{$class->id}}][{{$periodtiming}}]" value="fn">FN<?php
                                     } if(in_array($timetable[$class->id][$periodtiming],$an_section)){ $an_checked = "checked"; ?>&nbsp;&nbsp;<input type="radio" {{$an_checked}}  name="session[{{$class->id}}][{{$periodtiming}}]" value="an">AN<?php  } else{?><input type="radio" disabled name="session[{{$class->id}}][{{$periodtiming}}]" value="an">AN -->
                         <?php }  } ?>
                            <br>
                            <?php $syllabus = '';
                                if(isset($examsarr['exam_session_structure'][$class->id][$sk][$periodtiming]))  { 
                                $syllabus = $examsarr['exam_session_structure'][$class->id][$sk][$periodtiming]['syllabus']; }?>
                            <textarea class="form-control" name="syllabus[{{$class->id}}][{{$sk}}][{{$periodtiming}}]" maxlength="255" disabled>{{$syllabus}}</textarea>
                        </td>
                     @endforeach
                </tr>
            @endforeach
        @endif
    @endforeach
@endif
        </tfoot>
        <tbody>

        </tbody>
    </table>
</div>
{{-- </div>
</div> --}}
    <div class="modal-footer" hidden>
        <button type="sumbit" class="btn btn-link waves-effect"
            id="edit_style" onclick="saveExams();">SAVE</button> 
    </div>

</form>
 @endif

  {{-- <input type="checkbox" id="is_internal" name="is_internal[{{$class->id}}][{{$key}}]" value="1"> <span>Internal Marks</span> --}}
                            {{-- <input type="radio" id="fn_section" name="fn_section" value="2"><label>FN</label> --}}
                            {{-- <input type="radio" name="">
                            <input type="radio"> --}}