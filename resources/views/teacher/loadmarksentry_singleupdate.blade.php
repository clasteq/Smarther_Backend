<?php //echo "<pre>"; print_r($students); exit;
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
        <th scope="col">Total Marks</th>
        <th scope="col">Marks</th>
        <th scope="col">Remarks</th>
        <th scope="col" style="display:none;">Grade</th> 
        <th scope="col" style="display:none;">Action</th>
      </tr>
  </thead>
  <tbody style="text-align: center;">
  
      @foreach($students as $student) 
      <?php foreach( $c as $key=>$value){
          $total_marks = $marks = $remarks = $grade = '';
          /*if (isset($student['marksentry']) && isset($student['marksentry']['marksentryitems']) && !empty($student['marksentry']['marksentryitems'])) {
              $total_marks = $student['marksentry']['marksentryitems'][0]['total_marks'];
              $marks = $student['marksentry']['marksentryitems'][0]['marks'];
              $remarks = $student['marksentry']['marksentryitems'][0]['remarks'];
              $grade = $student['marksentry']['marksentryitems'][0]['grade'];
          } */
       
          if (isset($student['marks']) && isset($student['marks'][$key]) && !empty($student['marks'][$key])) {
              $total_marks = $student['marks'][$key]['total_marks'];
              $marks = $student['marks'][$key]['marks'];
              $remarks = $student['marks'][$key]['remarks'];
              $grade = $student['marks'][$key]['grade'];
          }    
          ?> 
          
          <tr id="{{ $student['id'] }}" class="{{ $student['id'] }}"> <th scope="row">{{$student['name']}}</th> <th scope="row">{{$student['admission_no']}}</th>
            <th scope="row">{{$value}}</th>
               <td><input type="text" name="total_marks[{{ $key }}]" id="total_marks_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $total_marks }}"
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="text" name="marks[{{ $key }}]" id="marks_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $marks }}"
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="text" name="remarks[{{ $key }}]" id="remarks_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $remarks }}"
                              minlength="1" maxlength="50"></td>
               <td><input type="text" name="grade[{{ $key }}]" id="grade_{{ $key }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $grade }}"
                              minlength="1" maxlength="50"></td>
               <td><button type="submit" name="submit[{{ $key }}]" id="submit_{{ $key }}"
                          class="btn submit" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;"
                          onclick="updateMarkEntry({{ $student['id'] }},{{$key}})">Update </button></td> 
          </tr> 
          <?php 
      }
      ?> 
 
    @endforeach 
  </tbody>
  @else 
  <tbody style="text-align: center;"><tr><td>No Students</td></tr>
  @endif  
  