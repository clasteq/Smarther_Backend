<?php  $c = []; $total_marks = ''; $rank = '-';  $grade = '-';  
//echo "<pre>"; print_r($subjects); exit;
?>
  @if(!empty($exams) && count($exams)>0)
  @foreach($exams as $ek=>$exam) 
  <div class=" col-md-6 float-left">
  <table class="table table-striped table-bordered ">
    <thead style="background: #a3d10c;color: #fff;text-align: center;">
        <tr>  <th colspan="4" scope="col">{{$exam->exam_name}}</th> </tr> 
        <tr>  <th scope="col">Subject</th> <th scope="col">Mark</th> <th scope="col">Rank</th> <th scope="col">Grade</th> </tr> 
    </thead>
    <tbody>  
      @if(!empty($exam->exam_result) && count($exam->exam_result)>0) 
      @foreach($exam->exam_result as $erk=>$exam_result)<?php // echo "<pre>"; print_r($exam_result);  ?>
        @if(!empty($exam_result['marksentry']) && count($exam_result['marksentry'])>0)  
          @if(!empty($exam_result['marksentry']['marksentryitems']) && count($exam_result['marksentry']['marksentryitems'])>0) 
          @foreach($exam_result['marksentry']['marksentryitems'] as $mk=>$marksentryitems) 
            <tr>  <td scope="col">{{$marksentryitems['subject_name']}}</td> 
                  <td scope="col">
                    @if($marksentryitems['is_absent'] == 1)
                    Absent
                    @else 
                    {{$marksentryitems['marks']}} / {{$marksentryitems['total_marks']}}
                    @endif</td> 
                  <td scope="col">{{$marksentryitems['rank']}}</td> 
                  <td scope="col">{{$marksentryitems['grade']}}</td> 
            </tr>  
          @endforeach
            <tr><th scope="col">Total</th>
                <th scope="col">{{$exam_result['marksentry']['marks']}} / {{$exam_result['marksentry']['total_marks']}}</th>
                <th scope="col">{{$exam_result['marksentry']['rank']}}</th>
                <th scope="col">{{$exam_result['marksentry']['grade']}}</th>
            </tr>
            <tr><th scope="col"><h3>{{$exam_result['marksentry']['pass_type']}}</h3></th>
                <th scope="col" colspan="3" class="text-center"><h3>{{$exam_result['marksentry']['remarks']}}</h3></th>  
            </tr>
          @else
          <tr>  <td colspan="4" scope="col">No Details</td> </tr> 
          @endif
        @else
        <tr>  <td colspan="4" scope="col">No Details</td> </tr> 
        @endif
      @endforeach
      @else
      <tr>  <td colspan="4" scope="col">No Details</td> </tr>
      @endif
    </tbody>
  </table>
  </div>
  @endforeach
  @else 
  No Details
  @endif
 