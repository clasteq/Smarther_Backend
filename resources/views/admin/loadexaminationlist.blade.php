<?php   $session = ''; $classes_arr = $classes_arr[0];
$is_mapped_subjects = (isset($classes_arr['is_mapped_subjects'])) ? $classes_arr['is_mapped_subjects'] : [];
$examsarr = (isset($examsarr)) ? $examsarr : [];
$examination_session_structure = (isset($examsarr['examination_session_structure'])) ? $examsarr['examination_session_structure'] : []; 
if(empty($section_id)) {
    $section_id = (isset($examsarr['section_ids'])) ? $examsarr['section_ids'] : '';  
} //echo "<pre>"; print_r($examsarr); exit;
?>
@if(!empty($classes))
<form id="edit-style-form" enctype="multipart/form-data"  action="{{ url('/admin/save/examsettings') }}" method="post">
    <input type="hidden" name="examination_id" id="examination_id" value="{{$examination_id}}">
    <input type="hidden" name="exam_id" id="exam_id" value="{{$exam_id}}">
    <input type="hidden" name="from_date" id="from_date" value="{{$start_date}}">
    <input type="hidden" name="last_date" id="last_date" value="{{$end_date}}">
    <input type="hidden" name="month_year" id="month_year" value="{{$monthyear}}">
    <input type="hidden" name="class_id" id="class_id" value="{{$class_id}}">
    <input type="hidden" name="section_id" id="section_id" value="{{$section_id}}"> 
    {{ csrf_field() }}  
    <div class="card-content collapse show">
    <div class="card-body card-dashboard">
    <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
        <div class="table-responsicve">

        <table class="table table-striped table-bordered tblcountries" style="width:100% !important">
            <thead>
                <tr>
                    <th width="8%">Result In</th><th width="8%">Rank Settings</th><th width="8%">Grade Settings</th>  
                    <th width="8%">Rank Type</th><th width="8%">Rank Include Failures</th><th width="8%">Include Practical </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="resultin" id="resultin" class="form-control textwidth"> 
                            <option value="1" @if(isset($examsarr['result_in']) && ($examsarr['result_in'] ==  1)) selected @endif >Both</option>
                            <option value="2" @if(isset($examsarr['result_in']) && ($examsarr['result_in'] ==  2)) selected @endif>Rank</option>
                            <option value="3" @if(isset($examsarr['result_in']) && ($examsarr['result_in'] ==  3)) selected @endif>Grade</option>
                        </select> 
                    </td>
                    <td>
                        <select name="rank_settings" id="rank_settings" class="form-control textwidth"> 
                            <option value="1" @if(isset($examsarr['rank_settings']) && ($examsarr['rank_settings'] ==  1)) selected @endif>Both</option>
                            <option value="2" @if(isset($examsarr['rank_settings']) && ($examsarr['rank_settings'] ==  2)) selected @endif>Each Subject</option>
                            <option value="3" @if(isset($examsarr['rank_settings']) && ($examsarr['rank_settings'] ==  3)) selected @endif>Total Subject</option>
                        </select> 
                    </td>
                    <td>
                        <select name="grade_settings" id="grade_settings" class="form-control textwidth"> 
                            <option value="1" @if(isset($examsarr['grade_settings']) && ($examsarr['grade_settings'] ==  1)) selected @endif>Both</option>
                            <option value="2" @if(isset($examsarr['grade_settings']) && ($examsarr['grade_settings'] ==  2)) selected @endif>Each Subject</option>
                            <option value="3"  @if(isset($examsarr['grade_settings']) && ($examsarr['grade_settings'] ==  3)) selected @endif>Total Subject</option>
                        </select> 
                    </td>
                    <td>
                        <select name="rank_type" id="rank_type" class="form-control textwidth">  
                            <option value="SKIP"  @if(isset($examsarr['rank_type']) && ($examsarr['rank_type'] ==  "SKIP")) selected @endif>Skip</option>
                            <option value="CONTINUOUS"  @if(isset($examsarr['rank_type']) && ($examsarr['rank_type'] ==  "CONTINUOUS")) selected @endif>Continuous</option>
                        </select> 
                    </td>
                    <td>
                        <select name="rankincludefailures" id="rankincludefailures" class="form-control textwidth">  
                            <option value="YES"   @if(isset($examsarr['rankincludefailures']) && ($examsarr['rankincludefailures'] ==  "YES")) selected @endif>Yes</option>
                            <option value="NO"  @if(isset($examsarr['rankincludefailures']) && ($examsarr['rankincludefailures'] ==  "NO")) selected @endif>No</option>
                        </select> 
                    </td>
                    <td>
                        <select name="include_practicals" id="include_practicals" class="form-control textwidth" onchange="loadprac();">  
                            <option value="YES"   @if(isset($examsarr['include_practicals']) && ($examsarr['include_practicals'] ==  "YES")) selected @endif>Yes</option>
                            <option value="NO"  @if(isset($examsarr['include_practicals']) && ($examsarr['include_practicals'] == "NO")) selected @endif>No</option>
                        </select> 
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-striped table-bordered tblcountries" style="width:90% !important">
            <thead>
                <tr><th width="10%">Subject</th><th width="18%">Exam Date</th>
                    <th width="8%">Theory Mark</th><th width="8%">Theory Pass Mark</th><th width="8%">Theory Session</th> 
                    <th width="8%" class="is_prac">Is Practical</th><th width="8%" class="is_prac">Practical Type</th>
                    <th width="8%" class="is_prac">Practical Date</th><th width="8%" class="is_prac">Practical Mark</th>
                    <th width="8%" class="is_prac">Practical Pass Mark</th>
                    <th width="8%" class="is_prac">Practical Session</th><th width="80%">Syllabus</th> 
                    </tr>
            </thead>
            <tbody>
                @if(is_array($is_mapped_subjects) && count($is_mapped_subjects)>0)
                @foreach($is_mapped_subjects as $sub)
                <tr>
                <th style=" word-wrap: break-word;overflow-wrap: break-word;">{{$sub->subject_name}}
                <input type="hidden" name="subject_id[{{$sub->id}}]" id="subject_id_{{$sub->id}}" value="{{$sub->id}}"></th>

                <td>
                    <select name="exam_date[{{$sub->id}}]" id="exam_date_{{$sub->id}}" class="form-control textwidth">
                        <option value="">Select Exam Date</option>
                        @if(!empty($datesArray))
                        @foreach($datesArray as $dt)
                        @php($selected = '')
                        @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['exam_date'] ==  $dt))  @php($selected = 'selected')  @endif
                        <option value="{{$dt}}" {{$selected}}>{{date('d-m-Y', strtotime($dt))}}</option>
                        @endforeach
                        @endif
                    </select>
                </td>
                <td>
                    @php($maxmark = '')
                    @if(isset($examination_session_structure[$sub->id]))  @php($maxmark = $examination_session_structure[$sub->id]['theory_mark'] )  @endif
                    <input type="text" class="textwidth" name="maxmark[{{$sub->id}}]" id="maxmark_{{$sub->id}}" onkeypress="return isNumber(event);" value="{{$maxmark}}"> 
                </td>
                <td>@php($theory_pass_mark = '')
                    @if(isset($examination_session_structure[$sub->id]))  @php($theory_pass_mark = $examination_session_structure[$sub->id]['theory_pass_mark'] )  @endif
                    <input type="text" class="textwidth" name="theorypassmark[{{$sub->id}}]" id="theorypassmark_{{$sub->id}}" onkeypress="return isNumber(event);" value="{{$theory_pass_mark}}"> 
                </td>
                <td>
                    <select name="session[{{$sub->id}}]" id="session_{{$sub->id}}" class="form-control textwidth"> 
                        <option value="fn" @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['session'] ==  'fn')) selected  @endif>FN</option>
                        <option value="an" @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['session'] ==  'an')) selected  @endif>AN</option>
                    </select> 
                </td>
                <td class="is_prac">
                    @php($checked = '')
                    @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['is_practical'] ==  1))  @php($checked = 'checked')  @endif
                    <input type="checkbox" class="textwidth" name="ispractical[{{$sub->id}}]" id="ispractical_{{$sub->id}}" value="1" {{$checked}}> 
                </td>
                <td class="is_prac">
                    <select name="practical_type[{{$sub->id}}]" id="practical_type_{{$sub->id}}" class="form-control textwidth">
                        <option value="0">Select Practical Type</option> 
                        <option value="1" @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['practical_type'] ==  1)) selected @endif >ORAL</option> 
                        <option value="2" @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['practical_type'] ==  2)) selected  @endif >PRACTICAL</option> 
                    </select>
                </td>
                <td class="is_prac">
                    <select name="practical_date[{{$sub->id}}]" id="practical_date_{{$sub->id}}" class="form-control textwidth">
                        <option value="">Select Practical Date</option>
                        @if(!empty($datesArray))
                        @foreach($datesArray as $dt)
                        @php($selected = '')
                        @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['practical_date'] ==  $dt))  @php($selected = 'selected')  @endif
                        <option value="{{$dt}}" {{$selected}}>{{date('d-m-Y', strtotime($dt))}}</option>
                        @endforeach
                        @endif
                    </select>
                </td>
                <td class="is_prac">@php($practical_mark = '')
                    @if(isset($examination_session_structure[$sub->id]))  @php($practical_mark = $examination_session_structure[$sub->id]['practical_mark'] )  @endif
                    <input type="text" class="textwidth" name="practicalmark[{{$sub->id}}]" id="practicalmark_{{$sub->id}}" onkeypress="return isNumber(event);" value="{{$practical_mark}}"> 
                </td>
                <td class="is_prac">@php($practical_pass_mark = '')
                    @if(isset($examination_session_structure[$sub->id]))  @php($practical_pass_mark = $examination_session_structure[$sub->id]['practical_pass_mark'] )  @endif
                    <input type="text" class="textwidth" name="practicalpassmark[{{$sub->id}}]" id="practicalpassmark_{{$sub->id}}" onkeypress="return isNumber(event);" value="{{$practical_pass_mark}}"> 
                </td>
                <td class="is_prac">
                    <select name="psession[{{$sub->id}}]" id="psession_{{$sub->id}}" class="form-control textwidth"> 
                        <option value="fn" @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['psession'] ==  'fn')) selected  @endif>FN</option>
                        <option value="an" @if(isset($examination_session_structure[$sub->id]) && ($examination_session_structure[$sub->id]['psession'] ==  'an')) selected  @endif>AN</option>
                    </select> 
                </td>
                <td>@php($syllabus = '')
                    @if(isset($examination_session_structure[$sub->id]))  @php($syllabus = $examination_session_structure[$sub->id]['syllabus'] )  @endif
                    <textarea class="form-control textwidth" name="syllabus[{{$sub->id}}]" id="syllabus_{{$sub->id}}" maxlength="255">{{$syllabus}}</textarea>
                </td> 
                
                </tr>
                @endforeach
                @endif 
            </tbody>  
        </table>
        </div> 
        <div class="modal-footer" style="width:90% !important">
            <div class="form-group form-float float-left col-md-2">
                <label class="form-label">Schedule Status</label>
                <div class="form-line">
                    <select class="form-control" name="schedule_status" id="edit_schedule_status">
                      <option value="SCHEDULED" @if(isset($examsarr['schedule_status']) && ($examsarr['schedule_status'] ==  "SCHEDULED")) selected @endif >Scheduled</option>
                      <option value="UNSCHEDULED" @if(isset($examsarr['schedule_status']) && ($examsarr['schedule_status'] ==  "UNSCHEDULED")) selected @endif >Un Scheduled</option>
                    </select>
                </div>
            </div>    
            <div class="form-group form-float float-left col-md-2">
                <label class="form-label">Publish Status</label>
                <div class="form-line">
                    <select class="form-control" name="publish_status" id="edit_publish_status">
                      <option value="PUBLISHED" @if(isset($examsarr['publish_status']) && ($examsarr['publish_status'] ==  "PUBLISHED")) selected @endif >Published</option>
                      <option value="UNPUBLISHED" @if(isset($examsarr['publish_status']) && ($examsarr['publish_status'] ==  "UNPUBLISHED")) selected @endif >Un Published</option>
                    </select>
                </div>
            </div>    
            <button type="sumbit" class="btn btn-link waves-effect" id="edit_style" onclick="saveExaminations();">SAVE</button> 
        </div>
    </div>
    </div> 
    </div>  
</form>
 @endif 