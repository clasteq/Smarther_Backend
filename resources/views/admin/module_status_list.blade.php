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
                        @if(isset($module_status[$school->id][$mk]))
                            @if($module_status[$school->id][$mk] == 3)  
                                <i class="far fa-dot-circle greentick" aria-hidden="true"></i>
                            @elseif($module_status[$school->id][$mk] == 2)
                                <i class="far fa-dot-circle yellow" aria-hidden="true"></i>
                            @elseif($module_status[$school->id][$mk] == 1)
                                <i class="far fa-dot-circle redcross" aria-hidden="true"></i>
                            @else 
                                <i class="far fa-dot-circle" aria-hidden="true"></i>
                            @endif
                        @else 
                            <i class="far fa-dot-circle" aria-hidden="true"></i>
                        @endif
                    </td> 
                @endforeach 
            </tr>
            @endforeach 
        </tbody>
    </table>
</div>
@endif    