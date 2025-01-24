 <?php  //echo "<pre>"; print_r($students); //exit;?>
 
@if(!empty($students) && count($students)>0)
  <thead style="background: #a3d10c;color: #fff;text-align: center;">
    <tr>
      <th scope="col">Name</th><th scope="col">Admission No</th>
      <th scope="col">Mobile</th> <th scope="col">Profile</th>
      <th scope="col">Total</th>   <th scope="col">Presence</th>   <th scope="col">Absence</th>   
      <th scope="col">Boys</th>   <th scope="col">Presence</th>   <th scope="col">Absence</th>   
      <th scope="col">Girls</th>   <th scope="col">Presence</th>   <th scope="col">Absence</th>   
    </tr>
</thead>
<tbody style="text-align: center;">

    @foreach($students as $student) 
        <tr>
            <th scope="row">{{$student['name']}}</th> 
            <th scope="row">{{$student['admission_no']}}</th>
            <th scope="row">{{$student['mobile']}}</th> 
            <th scope="row"><img src="{{$student['is_profile_image']}}" height="50" width="50"></th> 
        </tr>
    @endforeach
   
</tbody>
@else 
<tbody style="text-align: center;"><tr><td>No Students</td></tr>
@endif  
