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
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                <img src="{{ $receipt['is_school']->profile_image }}" alt="School Logo">
            </div>
            <div class="schoolname">
                <h1>{{ $receipt['is_school']->name }}</h1>
                <span>{{ $receipt['is_school']->address }}</span>
            </div>
        </div>
        <hr>
        <div class="content">
            <h3>Receipt</h3>
            <div class="studentdetails">
                <div class="left">
                    <p>Name: {{ $receipt['is_student']->name }}</p>
                    <p>Standard: {{ $receipt['is_student']->class_name }} - {{ $receipt['is_student']->section_name }}</p>
                    <p>Receipt No: {{ $receipt['receipt_no'] }}</p>
                </div>
                <div class="right">
                    <p>Admission No: {{ $receipt['is_student']->admission_no }}</p>
                    <p>Roll No: @if($receipt['is_student']->roll_no > 0) {{ $receipt['is_student']->roll_no }} @endif</p>
                    <p>Date: {{ date('Y-m-d', strtotime($receipt['created_at'])) }}</p>
                </div>
            </div> 
            @if(isset($receipt['feepayments']) && count($receipt['feepayments'])>0)
            <table>
                <tr>
                    <th>S.No</th>
                    <th>Bill Item</th>
                    <th>Amount Paid</th>
                </tr>
                @php($i = 1)
                @foreach($receipt['feepayments'] as $payments)
                <tr>
                    <td>{{$i}} @php($i = $i+1)</td>
                    <td>{{$payments['is_item_name']}}</td>
                    <td>@if($payments['amount_paid'] > 0) {{ number_format($payments['amount_paid'],2) }} @endif</td>
                </tr> 
                @endforeach
            </table>
            @endif
            <div class="paid">
                <img src="{{ asset('/public/image/paid.jpg') }}" alt="Paid">
            </div>
            <div class="paymentmethod">
                <p>Payment Mode: {{$receipt['is_payment_mode']}}</p>
                <p>Received with thanks a sum of {{number_format($receipt['amount'],2)}}/- ({{$receipt['is_amount_words']}} only) as payment on {{ date('d-M-Y', strtotime($receipt['created_at'])) }} </p>
            </div>
        </div>
    </div>
</body>

</html>
