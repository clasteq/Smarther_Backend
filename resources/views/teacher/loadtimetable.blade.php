<?php 
// echo  "<pre>";print_r($timetable);
//     exit;

?>
<br>
@if(!empty($days))
<form id="edit-style-form" enctype="multipart/form-data" action="{{ url('/teacher/save/timetable') }}" method="post">
    <input type="hidden" name="tclass_id" id="tclass_id" value="{{$class_id}}">
    <input type="hidden" name="tsection_id" id="tsection_id" value="{{$section_id}}">
    {{ csrf_field() }} 
    <table class="table table-striped table-bordered tblcountries">
        <thead>
            <tr>
                <th>Days</th>
                @if(!empty($periods) )
                @foreach ($periods as $periodtiming)
                 @if($periodtiming != '00:00' && $periodtiming != '')
                    <th>{{$periodtiming}}</th>
                    @endif
                @endforeach
                @endif
            </tr>
        </thead>
        <tfoot>

        @if(!empty($days))
            @foreach ($days as $day)
                <tr>

                    <td>{{ $day->day_name }}</td>

                    @foreach ($periods as $key => $periodtiming)
                    
                    @if($periodtiming != '00:00' && $periodtiming != '')
                        <td><select class="form-control course_id" name="subject_id[{{$day->id}}][{{$key}}]"
                                >
                                <option value="0">Select Subject</option>
                                @if (!empty($subjects))
                                    @foreach ($subjects as $subject)
                                        <?php $selected = '';
                                        if(isset($timetable[$day->id]) && isset($timetable[$day->id][$key])) {
                                            if($timetable[$day->id][$key] == $subject->id) {
                                                $selected = ' selected ';
                                            }
                                        }
                                        ?>
                                        <option value="{{ $subject->id }}" {{$selected}}>
                                            {{ $subject->subject_name }}
                                        </option>
                                      
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        @endif
        </tfoot>
        <tbody>

        </tbody>
    </table>

    
    <div class="modal-footer">
        <button type="sumbit" class="btn btn-link waves-effect"
            id="edit_style" onclick="saveTimetable();">SAVE</button> 
    </div>

</form>
 @endif