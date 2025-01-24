<?php
namespace App\Imports;
use App\Bulk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use App\Models;
use App\Ratings;
use DB;
use App\User;
use App\Employees;
use App\Http\Controllers\CommonController;
use Hash;

class BulkImport implements ToModel,WithHeadingRow
{   

    public function model(array $row) { 
       // echo "<pre>"; print_r($row);  
        $data = [];
        $depts = ['planning'=>1, 'oqa'=>10, 'store'=>2, 'production'=>4, 'bridgeproduction'=>3, 'bridgewiringproduction'=>12, 
        'bridgetestingproduction'=>13, 'testing'=>8, 'dispatch'=>11, 'pqa'=>9];

        //echo "<pre>";
        if(!empty($row['email'])) { 
            $data = [];
            //print_r($row);
           //echo $row['email']."<br>". 
           $dob = $row['dob'];  
            if(!empty($dob) && ($dob > 0)) {
                $unix_date = ($dob - 25569) * 86400;
                $dob = 25569 + ($unix_date / 86400);
                $unix_date = ($dob - 25569) * 86400;
                $dob = gmdate("Y-m-d", $unix_date);
            }

            $joined_date = $row['joined_date'];  
            if(!empty($joined_date) && ($joined_date > 0)) {
                $unix_date = ($joined_date - 25569) * 86400;
                $joined_date = 25569 + ($unix_date / 86400);
                $unix_date = ($joined_date - 25569) * 86400;
                $joined_date = gmdate("Y-m-d", $unix_date);
            }
            
            $data = ['dob' => $dob, 'joined_date' => $joined_date]; 
            //print_r($data);

            User::where('email', $row['email'])->update($data);
        }
    }



    /*public function model(array $row) { 
       // echo "<pre>"; print_r($row);  
        $data = [];
        $depts = ['planning'=>1, 'oqa'=>10, 'store'=>2, 'production'=>4, 'bridgeproduction'=>3, 'bridgewiringproduction'=>12, 
        'bridgetestingproduction'=>13, 'testing'=>8, 'dispatch'=>11, 'pqa'=>9];
        if(!empty($row['department_name'])) {

            $dept_name = $row['department_name'];

            $re = '/(\s*)/m'; 
            $subst = '';
            $dept_name = preg_replace($re, $subst, $dept_name);
            $dept_name = strtolower($dept_name);
            $department_id = $depts[$dept_name];

            $user_type = 'FUJIROLE003';
            $usertype = $row['user_type'];
            if($usertype == 'TEAMMEMBER') {
                $user_type = 'FUJIROLE004';
            }

            if($usertype == 'TEAMLEADER') {
                $user_type = 'FUJIROLE003';   
            }

            $lastuserid = DB::table('users')
                ->where('created_at', 'like', date('Y-m-d').'%')
                ->orderby('id', 'desc')->select('id')->limit(1)->get();

            if($lastuserid->isNotEmpty()) {
                $lastuserid = $lastuserid[0]->id;
                $lastuserid = $lastuserid + 1;
            }   else {
                $lastuserid = 1;
            }

            $append = str_pad($lastuserid,4,"0",STR_PAD_LEFT);

            $reg_no = date('Ymd').$append;

            $date = date('Y-m-d H:i:s');
            $api_token = User::random_strings(30);
            $def_expiry_after =  CommonController::getDefExpiry();
            $api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));

            $tlid = 0;
            $team_leader_id = $row['team_leader_id'];
            if(!empty($team_leader_id)) {
                $tlid = DB::table('users')->where('employee_code', $team_leader_id)->value('id');
            }
            if(empty($tlid)) { $tlid = 0; }

            $data['user_type'] = $user_type;
            $data['reg_no'] = $reg_no;
            $data['employee_code'] = $row['emp_code'];
            $data['name'] = $row['name'];
            $data['email'] = $row['email'];
            $data['password'] = Hash::make('123456');
            $data['gender'] = $row['gender'];
            $data['dob'] = date('Y-m-d', strtotime($row['dob'])); 
            $data['mobile'] = $row['mobile']; 

            $data['last_login_date'] = date('Y-m-d');
            $data['last_app_opened_date'] = date('Y-m-d');
            $data['user_source_from'] = 'ADMIN';
            $data['api_token'] = $api_token;
            $data['api_token_expiry'] = $api_token_expiry;

            $data['joined_date'] = date('Y-m-d', strtotime($row['joined_date'])); 
            $data['created_by']  = 1;
            $data['created_at'] = date('Y-m-d');
            $data['status'] = 'ACTIVE';

            $userid = User::insertGetId($data);
            //$userid = 1; 
            $branch_id = 23;

            $arr = ['branch_id'=>$branch_id, 'user_id'=>$userid, 'team_leader_id'=>$tlid, 'department_id'=>$department_id, 'created_by'=>$branch_id, 'created_at'=>date('Y-m-d H:i:s')];

            //echo "<pre>"; print_r($data); print_r($arr);

            $exists = Employees::where('user_id', $userid)->first();
            if(!empty($exists)) {
                Employees::where('user_id', $userid)->update(['team_leader_id' => $tlid, 'branch_id'=>$branch_id, 'department_id'=>$department_id, 'updated_by'=>$branch_id, 'updated_at'=>date('Y-m-d H:i:s')]);
            }   else {
                Employees::insert(['branch_id'=>$branch_id, 'user_id'=>$userid, 'team_leader_id'=>$tlid, 'department_id'=>$department_id, 'created_by'=>$branch_id, 'created_at'=>date('Y-m-d H:i:s')]);
            }
        }
    }*/



	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    * /
    public function model(array $row) { // echo "<pre>"; print_r($row);  

        if(!empty($row['modal'])) {
            $model_name = trim($row['modal'], ' ');
            // echo "qq". $model_code = str_replace($model_name, ' ', '_');
            $model_code = trim($model_name, '_');
            $model_code = strtoupper($model_code); 
            $model_code = str_replace(' ', '_',$model_code);
            $position = 5;
            $status = 'ACTIVE';

            $exists = Models::where('model_code', $model_code)->first();

            if(!empty($exists)) {
                /*$model = Models::find($exists->id);
                $model->model_name = $model_name; 
                $model->model_code = $model_code; 
                $model->position = $position; 
                $model->status = $status; 
                $model->updated_by = 1; 
                $model->updated_at = date('Y-m-d H:i:s'); 
                $model->save();* /
            }   else {
                $model = new Models;
                $model->model_name = $model_name; 
                $model->model_code = $model_code; 
                $model->position = $position; 
                $model->status = $status; 
                $model->created_by = 1; 
                $model->created_at = date('Y-m-d H:i:s'); 
                $model->save();

                $id = $model->id;

                $model->position = $id;

                $model->save();
            }


            $rating_name = trim($row['rating'], ' ').' '.trim($row['val'], ' ');
            $rating = strtoupper($rating_name); 
            $rating_code = str_replace(' ', '_',$rating);

            $ratings = new Ratings;
            $ratings->model_id = $exists->id;
            $ratings->rating_name = $rating_name; 
            $ratings->rating_code = $rating_code; 
            $ratings->position = $position; 
            $ratings->status = $status; 
            $ratings->created_by = 1; 
            $ratings->created_at = date('Y-m-d H:i:s'); 
            $ratings->save();

            $id = $ratings->id;

            $ratings->position = $id;

            $ratings->save();
 
        }

    } */
}