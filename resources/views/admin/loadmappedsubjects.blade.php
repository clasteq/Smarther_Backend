
<div class="card-content collapse show">
    <div class="card-body card-dashboard">
        <div style="width: 100%; overflow-x: scroll; padding-left: -10px;">
            <div class="table-responsicve">
                <table class="table table-striped table-bordered tblcountries" >
                    <thead>
                        <tr>
                            <th style="text-align:center;" scope="col">Action</th>
                            <th scope="col">Class</th>
                            <th scope="col">Section</th>
                            <th scope="col">Subject</th>
                            
                        </tr>
                    </thead>
                    <tfoot>
                        <tbody>
                            @if(!empty($subjects))
                            @foreach ($subjects as $subject)
                            <tr>
                              
                                <td align="center"><a href="#" onclick="deletesubject('{{$subject->id}}','{{$subject->teacher_id}}')" title="Edit Country"><i class="fas fa-trash"></i></a></td>
                                <td>{{$subject->is_class_name}}</td>
                                <td>{{$subject->is_section_name}}</td>
                                <td>{{$subject->is_subject_name}}</td>
                           
                                
                            </tr>
                    
                      
                        @endforeach
                        @else
                        <tr>
                            <th>No Subject Mapped</th>
                        </tr>
                        @endif
                    </tbody>
                    </tfoot>
             
                </table>
            </div>
        </div>
    </div>
    