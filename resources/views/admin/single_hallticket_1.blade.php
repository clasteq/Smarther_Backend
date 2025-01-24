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
            table th {
                background-color: #c0bdbd;
                text-align: center;
            }
            table td {
                height: 20px;
                text-align: center;
            }
            main { width: 100%; height: 90%; }
        </style>
    </head>
    <body>
        @if(isset($scholars_arr) && count($scholars_arr)>0)
        @foreach($scholars_arr as $scholar)
        <main>
            <div style="width:90%;margin: auto;margin-top: 25px;">
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
            <h1 style="text-align: center;text-decoration: underline;padding-top: 2px;padding-bottom: 20px;">Hall Ticket</h1>
            <div style="width:90%;margin: auto;">
                <div style="width:49%;display:inline-block;">
                    <div><span style="font-size: 20px;font-weight: 700;width: 160px;display: inline-block;">Student Name</span> : {{$scholar['name']}}</div>
                    <div><span style="font-size: 20px;font-weight: 700;width: 160px;display: inline-block;">Class & Sec</span> : {{$scholar['is_class_name']}} - {{$scholar['is_section_name']}}</div>
                    <div><span style="font-size: 20px;font-weight: 700;width: 160px;display: inline-block;">Adm. No</span> : {{$scholar['admission_no']}}</div>
                    <div><span style="font-size: 20px;font-weight: 700;width: 160px;display: inline-block;">Exam Name</span> : {{$examinations_arr[0]->exam_name}}</div>
                </div>
                <div style="width:50%;display:inline-block;">
                    <img src="{{ $photo }}" style="width:100px; height: 100px; margin-top: 0px; margin-left:70% ;" >
                </div>
            </div>
            @if(isset($exams_details_arr) && isset($exams_details_arr[0]['examination_session_structure']) && count($exams_details_arr[0]['examination_session_structure'])>0)
            <div style="width:90%;margin: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>S. No</th>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Subject</th>
                            <th>Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($i = 1)
                        @foreach($exams_details_arr[0]['examination_session_structure'] as $session)
                        <tr>
                            <td>{{$i}} @php($i = $i+1)</td>
                            <td>{{date('d-m-Y', strtotime($session['exam_date']))}}</td>
                            <td>{{strtoupper($session['session'])}}</td>
                            <td>{{$session['subject_name']}}</td>
                            <td> </td>
                        </tr>
                        @if($session['is_practical'] == 1) 
                        <tr>
                            <td>{{$i}} @php($i = $i+1)</td>
                            <td>{{date('d-m-Y', strtotime($session['practical_date']))}}</td>
                            <td>{{strtoupper($session['psession'])}}</td>
                            <td>{{$session['subject_name']}} - Practical</td>
                            <td> </td>
                        </tr> 
                        @endif 
                        @endforeach 
                    </tbody>
                </table>
            </div>
            @endif 
        </main> 
        @endforeach
        @endif
    </body> 
</html>
