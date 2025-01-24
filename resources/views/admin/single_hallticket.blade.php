<html>

<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap');

        .invoice-box {
            max-width: 650px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-family: "Roboto", sans-serif;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .header .logo {
            display: table-cell;
            vertical-align: top;
            width: 15%;
        }

        .header .logo img {
            width: 100%;
        }

        .header .schoolname {
            display: table-cell;
            vertical-align: top;
            text-align: center;
        }

        .header .schoolname h1 {
            margin: 0;
            margin-top: 12px;
        }

        .header .schoolname span {
            display: block;
            text-align: center;
            color: rgb(105, 105, 105);
            margin-top: 10px;

        }

        .content h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .studentdetails {
            width: 140%;
            margin-bottom: 20px;
            line-height: 1.9;
        }

        .studentdetails .left,
        .studentdetails .right {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .studentdetails p {
            font-size: 15px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:last-child td {
            background-color: #f2f2f2;
        }

        .paymentmethod p {
            font-size: 15px;
        }

        .paid {
            text-align: right;
            margin: 20px 0;
        }

        .paid img {
            width: 100%;
            max-width: 20%;
        }

        .page { width: 100%; height: 100%; }
    </style>
</head>

<body>
    @if(isset($scholars_arr) && count($scholars_arr)>0)
    @foreach($scholars_arr as $scholar)
    <div class="invoice-box page">
        <div class="header">
            <div class="logo">
                <img src="{{ $logo }}" alt="School Logo" height="100" width="100">
            </div>
            <div class="schoolname">
                <h1>{{ $is_school->name }}</h1>
                <span>{{ $is_school->address }}</span>
            </div>
        </div>
        <hr>
        <div class="content">
            <h3>Hall Ticket</h3>
            <div class="studentdetails">
                <div class="left">
                    <p>Student Name: {{$scholar['name']}}</p>
                    <p>Class & Sec: {{$scholar['is_class_name']}} -  {{$scholar['is_section_name']}}</p>
                    <p>Adm. No:  {{$scholar['admission_no']}}</p>
                    <p>Exam Name: {{$examinations_arr[0]->exam_name}}</p>
                </div>
                <div class="right">
                    <p><img src="{{ $logo }}" alt="Student Photo"  height="100" width="100"></p>
                </div>
            </div> 
            @if(isset($exams_details_arr) && isset($exams_details_arr[0]['examination_session_structure']) && count($exams_details_arr[0]['examination_session_structure'])>0)
            <table>
                <tr>
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Subject</th>
                    <th>Sign</th>
                </tr>
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
                    <td>{{strtoupper($session['session'])}}</td>
                    <td>{{$session['subject_name']}} - Practical</td>
                    <td> </td>
                </tr> 
                @endif 
                @endforeach
            </table>
            @endif 
        </div>
    </div>
    @endforeach
    @endif
</body>

</html>
