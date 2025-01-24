<?php  $c = []; //echo "<pre>"; print_r($students); exit;
if(!empty($subjects)) {
    foreach($subjects as $sk => $sv) {
      $c[$sv->is_subject_id] = $sv->is_subject_name;
    }
}

?>
  @if(!empty($students) && count($students)>0)
    <thead style="background: #a3d10c;color: #fff;text-align: center;">
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Admission No</th>
        <th scope="col">Subject Name</th>
        <th scope="col" class="d-none">Total Marks</th>
        <th scope="col">Is Absent</th>
        <th scope="col">Marks</th>
        <th scope="col">Remarks</th>
        <th scope="col" style="display:none;">Grade</th> 
        <th scope="col" style="display:none;">Action</th>
      </tr>
  </thead>
  <tbody style="text-align: center;">
  
      @foreach($students as $student)  
      <?php  
      foreach( $c as $key=>$value){
          $total_marks = $totalmarks; $marks = $remarks = $grade = $checked = ''; $is_absent = 0;
          /*if (isset($student['marksentry']) && isset($student['marksentry']['marksentryitems']) && !empty($student['marksentry']['marksentryitems'])) {
              $total_marks = $student['marksentry']['marksentryitems'][0]['total_marks'];
              $marks = $student['marksentry']['marksentryitems'][0]['marks'];
              $remarks = $student['marksentry']['marksentryitems'][0]['remarks'];
              $grade = $student['marksentry']['marksentryitems'][0]['grade'];
          } */

          if (isset($student['marks']) && isset($student['marks'][$key]) && !empty($student['marks'][$key])) {
              $total_marks = $student['marks'][$key]['total_marks'];
              $marks = $student['marks'][$key]['marks'];
              if($marks > 0) {} else { $marks = ''; }
              $remarks = $student['marks'][$key]['remarks'];
              $grade = $student['marks'][$key]['grade'];

              $is_absent = $student['marks'][$key]['is_absent'];
              $checked = ($is_absent == 1) ? 'checked' : '';
          } 
          ?>
          
          <tr id="{{ $student['id'] }}" class="{{ $student['id'] }}"> <th scope="row">{{$student['name']}}</th> <th scope="row">{{$student['admission_no']}}</th>
            <th scope="row">{{$value}}</th>
               <td class="d-none"><input type="text" name="total_marks[{{$student['id']}}][{{ $key }}]" id="total_marks_{{$student['id']}}_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $total_marks }}"
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="checkbox" name="is_absent[{{$student['id']}}][{{ $key }}]" id="is_absent_{{$student['id']}}_{{ $key }}"
                              class="form control" style="padding: 18px 22px !important;" value=1 {{$checked}} onchange="chkmark({{ $student['id'] }},{{$key}});"></td> 
               <td><input type="text" name="marks[{{$student['id']}}][{{ $key }}]" id="marks_{{$student['id']}}_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $marks }}"
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)" max="{{$total_marks}}"></td>
               <td><input type="text" name="remarks[{{$student['id']}}][{{ $key }}]" id="remarks_{{$student['id']}}_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $remarks }}"
                              minlength="1" maxlength="50"></td>
               <td style="display:none;"><input type="text" name="grade[{{$student['id']}}][{{ $key }}]" id="grade_{{$student['id']}}_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $grade }}"
                              minlength="1" maxlength="50"></td>
               <td  style="display:none;"><button type="submit" name="submit[{{ $key }}]" id="submit_{{ $key }}" data-key="{{ $key }}" 
                  data-name="{{$value}}" data-student="{{$student['name']}}" data-student_id="{{$student['id']}}" class="btn submit d-none" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;" onclick="updateMarkEntry({{ $student['id'] }},{{$key}})">Update </button></td> 
          </tr>
          <?php 
      }
      ?> 
 
    @endforeach 
    <tr> <td colspan="6"> <button type="button" name="submitfull" id="submitfull"
                          class="btn submitfull" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;"
                          onclick="updateStudentMarkEntry()">Update </button> </td></tr>
  </tbody>
  @else 
  <tbody style="text-align: center;"><tr><td>No Students</td></tr>
  @endif  
  