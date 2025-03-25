@if(isset($schools) && !empty($schools) && count($schools) > 0)
<div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
    <table class="table table-striped table-bordered tblcountries" style="width:100% !important">
        <thead>
            <tr>
                <th >Modules</th> 
                @foreach ($schools as $school) 
                    <th style=" word-wrap: break-word;overflow-wrap: break-word;">{{$school->name}}</th> 
                @endforeach 
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $mk => $module) 
            <tr>
                <th style=" word-wrap: break-word;overflow-wrap: break-word;">{{strtoupper($module)}}</th> 
                @foreach ($schools as $school) 
                    <td > 
                        @if(isset($module_status[$school->id][$mk])) {{$module_status[$school->id][$mk]}} 
                        @endif
                    </td> 
                @endforeach 
            </tr>
            @endforeach 
        </tbody>
    </table>
</div>
@endif    