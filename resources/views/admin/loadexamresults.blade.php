<?php  $c = []; $total_marks = ''; $rank = '-';  $grade = '-';//echo "<pre>"; print_r($students); exit;
if(!empty($subjects)) {
    foreach($subjects as $sk => $sv) {
      $c[$sv->is_subject_id] = $sv->is_subject_name;
    }
}
//echo "<pre>"; print_r($subjects); exit;
?>
  @if(!empty($students) && count($students)>0)
    <thead style="background: #a3d10c;color: #fff;text-align: center;">
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Admission No</th>
        @foreach( $c as $key=>$value)
        <th scope="col">{{$value}}</th>
        @endforeach  
        @if(isset($in_subject_id) && ($in_subject_id > 0)) 
        @else
        <th scope="col">Total</th>
        @endif
        <th scope="col">Rank</th>
        <th scope="col">Grade</th>  
      </tr>
  </thead>
  <tbody style="text-align: center;">
  
      @foreach($students as $student)  
      <tr id="{{ $student['id'] }}" class="{{ $student['id'] }}"> 
          <th scope="row">{{$student['name']}}</th> 
          <th scope="row">{{$student['admission_no']}}</th>
          <?php   foreach( $c as $key=>$value){
          $total_marks = 0; // $totalmarks; 
          $marks = $remarks = $grade = $checked = ''; $is_absent = $rank = 0;
          /*if (isset($student['marksentry']) && isset($student['marksentry']['marksentryitems']) && !empty($student['marksentry']['marksentryitems'])) {
              $total_marks = $student['marksentry']['marksentryitems'][0]['total_marks'];
              $marks = $student['marksentry']['marksentryitems'][0]['marks'];
              $remarks = $student['marksentry']['marksentryitems'][0]['remarks'];
              $grade = $student['marksentry']['marksentryitems'][0]['grade'];
          } */

          if (isset($student['marks']) && isset($student['marks'][$key]) && !empty($student['marks'][$key])) {
              $total_marks = $student['marks'][$key]['marks'];// $student['marks'][$key]['total_marks'];
              
              $remarks = $student['marks'][$key]['remarks'];
              $grade = $student['marks'][$key]['grade'];

              $is_absent = $student['marks'][$key]['is_absent'];
              $checked = ($is_absent == 1) ? 'checked' : '';
              $marks = ($is_absent == 1) ? 'A' : $student['marks'][$key]['marks']; 
              $rank = ($is_absent == 1) ? '' : $student['marks'][$key]['rank'];
              if($rank > 0) {} else { $rank = 0; }
              if(!empty($grade)) {} else { $grade = '-'; }
          } 
          if(isset($in_subject_id) && ($in_subject_id > 0)) {} else {
            if (isset($student['marksentry']) && isset($student['marksentry']['rank']) && !empty($student['marksentry']['marks'])) {
              $rank = $student['marksentry']['rank'];
              $grade = $student['marksentry']['grade'];
              $total_marks = $student['marksentry']['marks'];
            }
          }
          if($rank > 0) {} else { $rank = '-'; }
          if(!empty($grade)) {} else { $grade = '-'; }
          if($total_marks > 0) {} else { $total_marks = ''; }
          ?> 
          <th scope="row">{{ $marks }}</th>
          <?php 
          }  if(isset($in_subject_id) && ($in_subject_id > 0)) {} else { ?>
          <th scope="row">{{ $total_marks }}</th> <?php  } ?>
          <th scope="row">{{ $rank }}</th> 
          <th scope="row">{{ $grade }}</th> 
       
      </tr>
 
    @endforeach  
  </tbody>
  @else 
  <tbody style="text-align: center;"><tr><td>No Details</td></tr>
  @endif  
  