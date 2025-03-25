<?php  $c = [];  $p = []; $tm = []; $pm = []; //echo "<pre>"; print_r($students); exit;
if(!empty($subjects)) {
    foreach($subjects as $sk => $sv) {
      $c[$sv->is_subject_id] = $sv->is_subject_name; 
      $p[$sv->is_subject_id] = $sv->is_practical;
      $tm[$sv->is_subject_id] = $sv->theory_mark;
      $pm[$sv->is_subject_id] = $sv->practical_mark;
    }
} //echo "<pre>"; print_r($subjects); exit;

?>
  @if(!empty($students) && count($students)>0)
    <thead style="background: #a3d10c;color: #fff;text-align: center;">
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Admission No</th>
        @foreach( $c as $key=>$value){
        <th scope="col" colspan="4">{{$value}}</th>
        @endforeach 
      </tr>
      <tr>
        <th colspan="2"></th>
        @foreach( $c as $key=>$value){
        <th>Is Absent</th>
        @if($p[$key] == 1)
          <th>Theory Marks</th><th>Practical Marks</th>
        @else
          <th colspan="2">Marks</th>
        @endif
        <th>Remarks</th>
        @endforeach 
      </tr>  
  </thead>
  <tbody style="text-align: center;">
  
      @foreach($students as $student)  
          
          <tr id="{{ $student['id'] }}" class="{{ $student['id'] }}"> 
              <th scope="row">{{$student['name']}}</th> <th scope="row">{{$student['admission_no']}}</th>
              <?php  
              foreach( $c as $key=>$value){
                  $total_marks = $totalmarks; $marks = $theory_marks = $practical_marks = $remarks = $grade = $checked = ''; 
                  $is_absent = 0;  $theory_mark_max = $tm[$key]; $practical_mark_max = $pm[$key];

                  if (isset($student['marks']) && isset($student['marks'][$key]) && !empty($student['marks'][$key])) {
 
                      $total_marks = $student['marks'][$key]['total_marks']; 
 
                      $theory_marks = $student['marks'][$key]['theory_marks'];
                      if($theory_marks > 0) {} else { $theory_marks = ''; }
                      $practical_marks = $student['marks'][$key]['practical_marks'];
                      if($practical_marks > 0) {} else { $practical_marks = ''; }
                      $marks = $student['marks'][$key]['marks'];
                      if($marks > 0) {} else { $marks = ''; }
                      $remarks = $student['marks'][$key]['remarks'];
                      $grade = $student['marks'][$key]['grade'];

                      $is_absent = $student['marks'][$key]['is_absent'];
                      $checked = ($is_absent == 1) ? 'checked' : '';
                  } 
                  ?> 
                       <td class="d-none"><input type="text" name="total_marks[{{$student['id']}}][{{ $key }}]" id="total_marks_{{$student['id']}}_{{ $key }}"
                                      class="form-control" style="padding: 18px 22px !important;" value="{{ $total_marks }}"
                                      minlength="1" maxlength="3" onkeypress="return isNumber(event)"> </td>
                       <td><input type="checkbox" name="is_absent[{{$student['id']}}][{{ $key }}]" id="is_absent_{{$student['id']}}_{{ $key }}"
                                      class="form control" style="padding: 18px 22px !important;" value=1 {{$checked}} onchange="chkmark({{ $student['id'] }},{{$key}});"></td> 

                        @if($p[$key] == 1)
                          <td><input type="text" name="theory_marks[{{$student['id']}}][{{ $key }}]" id="theory_marks_{{$student['id']}}_{{ $key }}" class="form-control"  value="{{ $theory_marks }}"
                                      minlength="1" maxlength="3" onkeypress="return isNumber(event)" max="{{$theory_mark_max}}"></td>
                          <td><input type="text" name="practical_marks[{{$student['id']}}][{{ $key }}]" id="practical_marks_{{$student['id']}}_{{ $key }}" class="form-control"  value="{{ $practical_marks }}"
                                      minlength="1" maxlength="3" onkeypress="return isNumber(event)" max="{{$practical_mark_max}}"></td>
                        @else
                          <td  colspan="2"><input type="text" name="marks[{{$student['id']}}][{{ $key }}]" id="marks_{{$student['id']}}_{{ $key }}"
                                      class="form-control"  value="{{ $marks }}"
                                      minlength="1" maxlength="3" onkeypress="return isNumber(event)" max="{{$theory_mark_max}}"></td>
                        @endif

                       
                       <td><input type="text" name="remarks[{{$student['id']}}][{{ $key }}]" id="remarks_{{$student['id']}}_{{ $key }}"
                                      class="form-control" value="{{ $remarks }}"
                                      minlength="1" maxlength="50"></td>
                       <td style="display:none;"><input type="text" name="grade[{{$student['id']}}][{{ $key }}]" id="grade_{{$student['id']}}_{{ $key }}"
                                      class="form-control" style="padding: 18px 22px !important;" value="{{ $grade }}"
                                      minlength="1" maxlength="50"></td>
                       <td  style="display:none;"><button type="submit" name="submit[{{ $key }}]" id="submit_{{ $key }}" data-key="{{ $key }}" 
                          data-name="{{$value}}" data-student="{{$student['name']}}" data-student_id="{{$student['id']}}" class="btn submit d-none" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;" onclick="updateMarkEntry({{ $student['id'] }},{{$key}})">Update </button></td> 
                  
                  <?php 
              }
              ?> 
          </tr>  
    @endforeach 
    <tr> <td colspan="6"> <button type="button" name="submitfull" id="submitfull"
                          class="btn submitfull" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;"
                          onclick="updateStudentMarkEntry()">Update </button> </td></tr>
  </tbody>
  @else 
  <tbody style="text-align: center;"><tr><td>No Students</td></tr>
  @endif  
  