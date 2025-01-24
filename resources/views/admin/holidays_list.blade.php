<div class="col-md-5">
    <h3>Holidays :</h3>
    <table class="table table-bordered" id="table_source">
        <tbody>
            @if(!empty($holiday))
            @foreach($holiday as $hd)
            <tr>
                <td> {{$hd->holiday_date}} 
                    <input type="hidden" name="holiday_type[{{$hd->holiday_date}}]" id="holiday_type" value="h">
                    <input type="hidden" name="holiday_date[{{$hd->holiday_date}}]" id="holiday_date" value="{{$hd->holiday_date}}">
                </td>
                <td><input type="text" name="holiday_description[{{$hd->holiday_date}}]" id="holiday_description" value="{{$hd->holiday_description}}"></td>
                <td><a href="#/" class='move-row' onclick="mv(this);">>></a></td>
            </tr>
            @endforeach
            @endif 
        </tbody>
    </table>
</div><div class="col-md-2"></div>
<div class="col-md-5">
    <h3>Working Days :</h3>
    <table class="table table-bordered" id="table_dest">
        <tbody>
            @if(!empty($working_days))
            @foreach($working_days as $wd)
            <tr>
                <td> {{$wd}}
                    <input type="hidden" name="holiday_type[{{$wd}}]" id="holiday_type" value="w">
                    <input type="hidden" name="holiday_date[{{$wd}}]" id="holiday_date" value="{{$wd}}">
                </td>
                <td><input type="text" name="holiday_description[{{$wd}}]" id="holiday_description" value="{{$working_days1[$wd]['holiday_description']}}"></td>
                <td><a href="#/" class='move-row' onclick="mv(this);"><<</a></td>
            </tr>
            @endforeach
            @endif  
        </tbody>
    </table>
</div>