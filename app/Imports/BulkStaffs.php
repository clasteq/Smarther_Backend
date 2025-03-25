<?php 
namespace App\Imports;

ini_set('max_execution_time', 300);

use App\Bulk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use DB;
use App\Models\User;
use App\Models\Teacher;  

use App\Http\Controllers\CommonController;
use Hash;
use Auth;

use PhpOffice\PhpSpreadsheet\Shared\Date;  

class BulkStaffs implements ToModel,WithHeadingRow
{   

    public function model(array $row) {  
        // dd($row);  exit;
        try{
        
           
            if(!empty($row['employee_number'])) { 
                
                $error = [];   $date = date('Y-m-d H:i:s');

                $data1 = [];
                $user_role  = $row['user_role'];  
                $user_role = trim($user_role);
                if(empty($user_role)) {
                    $user_role = 'TEACHER';
                }

                if(!empty(trim($user_role))) {
                    $role_id = DB::table('userroles')->where('school_id', Auth::User()->id)
                        ->where('user_role', trim($user_role))->value('id');
                    if($role_id > 0) { } else { 

                        // Last Order id
                        $lastorderid = DB::table('userroles')
                            ->orderby('id', 'desc')->select('id')->limit(1)->get();

                        if($lastorderid->isNotEmpty()) {
                            $lastorderid = $lastorderid[0]->id;
                            $lastorderid = $lastorderid + 1;
                        }   else {
                            $lastorderid = 1;
                        }

                        $append = str_pad($lastorderid,3,"0",STR_PAD_LEFT);

                        $ref_code = CommonController::$code_prefix.'UR'.$append;

                        $role_id = DB::table('userroles')->insertGetId(['school_id'=>Auth::User()->id, 
                            'user_role'=>trim($user_role), 'ref_code' => $ref_code, 
                            'status'=>'ACTIVE',  'created_at'=>date('Y-m-d H:i:s')]); 
                    }

                    if($role_id > 0) {  
                        $user_type = DB::table('userroles')->where('school_id', Auth::User()->id)
                        ->where('id', $role_id)->value('ref_code');  
                    } else {
                        $user_type = 'TEACHER';
                    }
                } 

                $data1['name'] = $row['name'];  
                $data1['mobile'] = $row['phone_number']; 
                $data1['phone_number_verify_status'] = $row['phone_number_verify_status']; 

                $date_of_birth = $row['date_of_birth']; 
                $type = gettype($date_of_birth);
                if(!empty($date_of_birth)) {
                    if($type != 'string' && $type == 'integer') { 
                        $date_of_birth = Date::excelToDateTimeObject($date_of_birth)->format('Y-m-d');
                    }
                    $date_of_birth  = date('Y-m-d', strtotime($date_of_birth));
                }
                if(!empty($date_of_birth) && strtotime($date_of_birth) > 0) {
                    $date_of_birth = date('Y-m-d', strtotime($date_of_birth));
                }   
                $data1['dob'] = $date_of_birth; 
                $data1['gender'] = strtoupper($row['gender']); 

                $date_of_joining = $row['date_of_joining'];  
                $type = gettype($date_of_joining);
                if(!empty($date_of_joining)) {
                    if($type != 'string' && $type == 'integer') { 
                        $date_of_joining = Date::excelToDateTimeObject($date_of_joining)->format('Y-m-d');
                    } 
                    $date_of_joining  = date('Y-m-d', strtotime($date_of_joining));
                }
                if(!empty($date_of_joining) && strtotime($date_of_joining) > 0) {
                    $date_of_joining = date('Y-m-d', strtotime($date_of_joining));
                }   

                $data1['joined_date'] = $date_of_joining; 

                $id = 0;   $user_id = 0; 
                
                $emp_no_chk = DB::table('teachers')->where('emp_no', $row['employee_number'])
                        ->where('school_id', Auth::User()->id)->first(); 

                if(!empty($emp_no_chk)){ 
                    /*$exists = DB::table('users')->where('email', $row['email'])
                        ->where('school_college_id', Auth::User()->id)
                        ->where('id', '!=', $emp_no_chk->user_id)
                        ->first();
                    if (!empty($exists)) { 
                        $error[] = $row['email'].' already exists'; 
                    }  else {
                        if(!empty($row['email'])) {
                            $data1['email'] = $row['email'];  
                        }
                    }*/

                    $id = $emp_no_chk->user_id; 
                    $user_id = $emp_no_chk->user_id;

                }   
 
                $country_code = '91';
                $data1['code_mobile'] = $country_code.$row['phone_number']; 
                $data1['email'] = $row['email'];  
                
                if ($id > 0) {
                    $user_id = $id;
                    $users = User::find($id);
                    $data1['updated_at'] = $date;
                    $data1['updated_by'] = Auth::User()->id;

                    User::where('id', $id)->update($data1);
                } else {
                    $users = new User;

                    $def_expiry_after =  CommonController::getDefExpiry();
                    $data1['api_token_expiry'] = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                    $data1['api_token'] = User::random_strings(30);
                    $data1['last_login_date'] = date('Y-m-d H:i:s');
                    $data1['last_app_opened_date'] = date('Y-m-d H:i:s');
                    $data1['user_source_from'] = 'ADMIN'; 
                    $data1['created_at'] = $date;
                    $data1['joined_date'] = $date;
                    $data1['created_by'] = Auth::User()->id;

                    $data1['user_type'] = $user_type; //"TEACHER";

                    $lastjobid = DB::table('users')
                        ->where('created_at', 'like', date('Y-m-d') . '%')
                        ->orderby('id', 'desc')->count();
                    $lastjobid = $lastjobid + 1;
                    $append = str_pad($lastjobid, 6, "0", STR_PAD_LEFT);
                    $reg_no = date('ymd') . $append;

                    $data1['reg_no'] = $reg_no;
                    $data1['password'] = Hash::make('123456');
                    $data1['school_college_id'] = Auth::User()->id;

                    $data1['passcode'] = '123456';
                    $country_code = '91';
                    $data1['country_code'] = $country_code;

                    $id = User::insertGetId($data1); 
                    $user_id = $id;
                }   

                $data = [];
                $data['user_id'] = $id; 
                $data['school_id'] = Auth::User()->id;  
                $data['name_in_tamil'] = $row['name_in_tamil'];   
                $data['aadhar_number'] = $row['aadhar_number'];
                $data['address'] = $row['address'];
                $data['pincode'] = $row['pin_code']; 
                $data['date_of_joining'] = $date_of_joining; 
                $data['emp_no'] = $row['employee_number']; 
                $data['department_name'] = $row['department'];
                $data['designation'] = $row['designation']; 
                $data['bloodgroup'] = $row['blood_group'];

                if($user_id > 0) { 
                    $teach = Teacher::where('user_id', $user_id)->first();
                    if(empty($teach)){
                        Teacher::insertGetId($data);
                    }else{ 
                        Teacher::where('user_id', $user_id)->update($data);
                    } 
                }  
            }

        } catch(\Illuminate\Database\QueryException $ex){ 
            return response()->json(['status' => 'FAILED', 'message' => dd($ex->getMessage())]);//dd();  
        }
    }

 
}