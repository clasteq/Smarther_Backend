@if(isset($classes) && !empty($classes) && count($classes) > 0)
<div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
    <table class="table table-striped table-bordered tblcountries" style="width:100% !important">
        <thead>
            <tr>
                <th >Att</th> 
                @foreach ($classes as $class) 
                    <th style=" word-wrap: break-word;overflow-wrap: break-word;">{{$class->class_name}}</th> 
                @endforeach 
            </tr>
        </thead>
        <tbody>
            @foreach ($section_colmns as $section) 
            <tr>
                <th style=" word-wrap: break-word;overflow-wrap: break-word;">{{strtoupper($section)}}</th> 
                @foreach ($classes as $class) 
                    <td > 
                        @if($hw[$class->id][$section] == 3)  
                            <i class="far fa-dot-circle greentick" aria-hidden="true"></i>
                        @elseif($hw[$class->id][$section] == 2)
                            <i class="far fa-dot-circle yellow" aria-hidden="true"></i>
                        @elseif($hw[$class->id][$section] == 1)
                            <i class="far fa-dot-circle redcross" aria-hidden="true"></i>
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