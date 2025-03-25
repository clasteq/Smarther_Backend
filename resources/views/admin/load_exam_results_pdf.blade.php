<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">  
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" /> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> -->
        <title>Hall Ticket</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            table th, table td {
                border: 1px solid #000;
                padding: 10px;
                text-align: left;
                font-size: 14px;
            }
            /*table th {
                background-color: #c0bdbd;
                text-align: center;
            }*/
            table td {
                height: 20px;
                text-align: center;
            }
            main { width: 100%; height: 90%; }
        </style>
    </head>
    <body>

        <?php  $c = []; $total_marks = ''; $rank = '-';  $grade = '-';//echo "<pre>"; print_r($students); exit;
        if(!empty($subjects)) {
            foreach($subjects as $sk => $sv) {
              $c[$sv->is_subject_id] = $sv->is_subject_name;
            }
        }
        //echo "<pre>"; print_r($subjects); exit;
        ?>

        @if(isset($students) && count($students)>0)
        <main>
            <div style="width:98%;margin: auto;margin-top: 25px;">
                <div style="border: 1px solid #000;padding: 15px 0px;">
                    <div style="width:5%;display:inline-block;"></div>
                    <div style="width:15%;display:inline-block;">
                        <img src="{{ $logo }}" style="width:100%;margin-top: 0px;">
                    </div>
                    <div style="width:65%;display:inline-block;">
                        <p style="color: #00b050;font-size: 25px;text-align: center;text-shadow: 0 0 1px #000000, 0 0 2px #000000;margin-bottom: 0px;font-weight: 600;">{{ $is_school->name }}</p>
                        <p style="text-align: center;font-size: 18px;color: #054a91;margin-bottom: 0px;font-weight: 600;">{{ $is_school->address }}</p> 
                    </div>
                    <div style="width:10%;display:inline-block;"></div>
                </div>
            </div> 
            <div style="width:98%;margin: auto;">
                <table>
                    <thead style="background: #fff;color: #000;text-align: center;">
                          <tr>
                            <th scope="col" style="text-align: left !important;">Name</th>
                            <th scope="col">Admn No</th>
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
                    <tbody> 
                         
                        @foreach($students as $student)  
                          <tr id="{{ $student['id'] }}" class="{{ $student['id'] }}"> 
                              <th scope="row" style="text-align: left !important;">{{$student['name']}}</th> 
                              <th scope="row" style="text-align: left !important;">{{$student['admission_no']}}</th>
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
                              <th scope="row" style="text-align: right; !important;">{{ $marks }}</th>
                              <?php 
                              }  if(isset($in_subject_id) && ($in_subject_id > 0)) {} else { ?>
                              <th scope="row" style="text-align: right; !important;">{{ $total_marks }}</th> <?php  } ?>
                              <th scope="row">{{ $rank }}</th> 
                              <th scope="row">{{ $grade }}</th> 
                           
                          </tr>
 
                        @endforeach  
                    </tbody>
                </table>
            </div> 
        </main> 
        @endif
    </body> 
</html>
