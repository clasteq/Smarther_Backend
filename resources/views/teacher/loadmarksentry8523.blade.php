<?php //echo "<pre>"; print_r($students); exit;?>
  @if(!empty($students) && count($students)>0)
    <thead style="background: #a3d10c;color: #fff;text-align: center;">
      <tr>
        <th scope="col">Name</th><th scope="col">Admission No</th>
        <th scope="col">Subject Name</th>
        <th scope="col">Total Marks</th><th scope="col">Marks</th>
        <th scope="col">Remarks</th><th scope="col">Grade</th> 
        <th scope="col">Action</th>
      </tr>
  </thead>
  <tbody style="text-align: center;">
  
      @foreach($students as $student) 
      <?php 
          $total_marks = $marks = $remarks = $grade = '';
          if (isset($student['marksentry']) && isset($student['marksentry']['marksentryitems']) && !empty($student['marksentry']['marksentryitems'])) {
              $total_marks = $student['marksentry']['marksentryitems'][0]['total_marks'];
              $marks = $student['marksentry']['marksentryitems'][0]['marks'];
              $remarks = $student['marksentry']['marksentryitems'][0]['remarks'];
              $grade = $student['marksentry']['marksentryitems'][0]['grade'];
          } 
       
            
          // echo "<pre>";print_r($sub_id);
          //   // $array = get_object_vars($sub_id);
          //   echo "<pre>";echo $sub_id[0]->subject_id;
          // //   // echo $student['marksentry']['marksentryitems']['subject_id'];
            // exit;
          ?>
             @foreach ($subject as $k=>$sub)
           @if(isset($sub_id) && !empty($sub_id))
          
          <tr> <th scope="row">{{$student['name']}}</th> <th scope="row">{{$student['admission_no']}}</th>
            <th scope="row">{{$sub['is_subject_name']}}</th>
               <td><input type="text" name="total_marks[{{ $sub_id[$k]->subject_id }}]" id="total_marks_{{  $sub_id[$k]->subject_id  }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{  $sub_id[$k]->total_marks}}"
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="text" name="marks[{{ $sub_id[$k]->subject_id }}]" id="marks_{{ $sub_id[$k]->subject_id }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $sub_id[$k]->marks }}"
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="text" name="remarks[{{ $sub_id[$k]->subject_id }}]" id="remarks_{{ $sub_id[$k]->subject_id }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $sub_id[$k]->remarks }}"
                              minlength="1" maxlength="50"></td>
               <td><input type="text" name="grade[{{ $sub_id[$k]->subject_id }}]" id="grade_{{$sub_id[$k]->subject_id }}"
                              class="form-control" style="padding: 18px 22px !important;" value="{{ $sub_id[$k]->grade}}"
                              minlength="1" maxlength="50"></td>
               <td><button type="submit" name="submit[{{ $sub_id[$k]->subject_id }}]" id="submit_{{ $sub_id[$k]->subject_id }}"
                          class="btn submit" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;"
                          onclick="updateMarkEntry({{ $student['id'] }},{{ $sub_id[$k]->subject_id }})">Update </button></td> 
          </tr>
          @else
          <tr> <th scope="row">{{$student['name']}}</th> <th scope="row">{{$student['admission_no']}}</th>
            <th scope="row">{{$sub['is_subject_name']}}</th>
               <td><input type="text" name="total_marks[{{ $sub['subject_id'] }}]" id="total_marks_{{ $sub['subject_id'] }}"
                              class="form-control" style="padding: 18px 22px !important;" value=""
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="text" name="marks[{{ $sub['subject_id'] }}]" id="marks_{{ $sub['subject_id'] }}"
                              class="form-control" style="padding: 18px 22px !important;" value=""
                              minlength="1" maxlength="3" onkeypress="return isNumber(event)"></td>
               <td><input type="text" name="remarks[{{ $sub['subject_id'] }}]" id="remarks_{{ $sub['subject_id'] }}"
                              class="form-control" style="padding: 18px 22px !important;" value=""
                              minlength="1" maxlength="50"></td>
               <td><input type="text" name="grade[{{ $sub['subject_id'] }}]" id="grade_{{$sub['subject_id'] }}"
                              class="form-control" style="padding: 18px 22px !important;" value=""
                              minlength="1" maxlength="50"></td>
               <td><button type="submit" name="submit[{{ $sub['subject_id'] }}]" id="submit_{{ $sub['subject_id'] }}"
                          class="btn submit" style="background:#ffc107;border-radius: 6px;padding: 8px 13px;color:#fff;"
                          onclick="updateMarkEntry({{ $student['id'] }},{{ $sub['subject_id'] }})">Update </button></td> 
          </tr>
          @endif
          
          @endforeach
      @endforeach
  </tbody>
  @else 
  <tbody style="text-align: center;"><tr><td>No Students</td></tr>
  @endif  
  