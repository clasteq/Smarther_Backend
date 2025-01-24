<?php 
namespace App\Imports;

ini_set('max_execution_time', 300);

use App\Bulk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use DB;
use App\Models\User;
use App\Models\Student; 
use App\Models\StudentAcademics;

use App\Http\Controllers\CommonController;
use Hash;
use Auth;

use PhpOffice\PhpSpreadsheet\Shared\Date;  

class BulkScholars implements ToModel,WithHeadingRow
{   

    public function model(array $row) {  
        // dd($row);  exit;  
        try{ 
            //echo "<pre>"; print_r($row);
            if(!empty(trim($row['admission_number'])) && !empty(trim($row['name'])) && !empty(trim($row['phone_number'])) && !empty(trim($row['class'])) && !empty(trim($row['section']))) { 
                
                $error = [];   $date = date('Y-m-d H:i:s');

                $data1 = [];
                $data1['name'] = trim($row['name']);  
                $data1['mobile'] = trim($row['phone_number']); 
                $data1['phone_number_verify_status'] = trim($row['phone_number_verify_status']); 

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
                $data1['gender'] = strtoupper(trim($row['gender'])); 

                $date_of_joining = trim($row['date_of_joining']); 
                $type = gettype($date_of_joining);
                if(!empty($date_of_joining)) {
                    if($type != 'string' && $type == 'integer') { 
                        $date_of_joining = Date::excelToDateTimeObject($date_of_joining)->format('Y-m-d');
                    } 
                    $date_of_joining  = date('Y-m-d', strtotime($date_of_joining));
                }
 
                if(!empty($date_of_joining) && strtotime($date_of_joining) > 0) { 
                    $date_of_joining = date('Y-m-d', strtotime($date_of_joining));
                }  //echo $date_of_joining; exit;
                $data1['joined_date'] = $date_of_joining; 

                $id = 0;   $user_id = 0;
                

                /*$admission_no_chk = DB::table('students')->where('admission_no', $row['admission_number'])
                        ->where('school_id', Auth::User()->id)->first(); */
                
                $admission_no_chk = DB::table('users')->where('admission_no', trim($row['admission_number']))
                        ->where('school_college_id', Auth::User()->id)->first(); 

                if(!empty($admission_no_chk)){ 
                    /*if(!empty($row['email'])) {
                        $exists = DB::table('users')->where('email', trim($row['email']))
                            ->where('school_college_id', Auth::User()->id)
                            ->where('id', '!=', $admission_no_chk->id)
                            ->first();
                        if (!empty($exists)) { 
                            $error[] = $row['email'].' already exists'; 
                        }  else {
                            if(!empty(($row['email']))) {
                                $data1['email'] = trim($row['email']);  
                            }
                        }
                    }*/

                    $id = $admission_no_chk->id; 
                    $user_id = $admission_no_chk->id;

                }   else {   // insert

                    $id = 0; 
                }
                
 
                $country_code = '91';
                $data1['code_mobile'] = $country_code.trim($row['phone_number']);
                $data1['admission_no'] = trim($row['admission_number']);
                $data1['email'] = trim($row['email']);  
                
                if ($id > 0) {
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
                    $data1['created_by'] = Auth::User()->id;

                    $data1['user_type'] = "STUDENT";

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
                }    //echo $user_id."<pre>";  print_r($data1);
 
                if(!empty(trim($row['class']))) {
                    $class_id = DB::table('classes')->where('school_id', Auth::User()->id)
                        ->where('class_name', trim($row['class']))->value('id');
                    if($class_id > 0) { } else { 
                        $class_id = DB::table('classes')->insertGetId(['school_id'=>Auth::User()->id, 
                            'class_name'=>trim($row['class']), 
                            'status'=>'ACTIVE', 'position'=>99, 'created_at'=>date('Y-m-d H:i:s')]);

                        DB::table('classes')->where('id', $class_id)->update(['position'=>$class_id]);
                    }
                } 
 
                if(!empty(trim($row['section']))) {
                    $section_id = DB::table('sections')->where('school_id', Auth::User()->id)
                        ->where('class_id', $class_id)->where('section_name', trim($row['section']))->value('id');
                    if($section_id > 0) {} else {
                        $section_id = DB::table('sections')->insertGetId(['school_id'=>Auth::User()->id, 
                            'class_id'=>$class_id, 'section_name'=>trim($row['section']),
                            'status'=>'ACTIVE', 'position'=>99, 'created_at'=>date('Y-m-d H:i:s')]);

                        DB::table('sections')->where('id', $section_id)->update(['position'=>$section_id]);
                    }
                }

                $data = [];
                $data['user_id'] = $id; 
                $data['school_id'] = Auth::User()->id; 
                $data['admission_no'] = trim($row['admission_number']); 

                $data['emis_id'] = trim($row['emis_id']); 
                $data['name_in_tamil'] = trim($row['name_in_tamil']); 
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                $data['father_name'] = trim($row['father_name']);
                $data['father_occupation'] = trim($row['father_occupation']);
                $data['father_education'] = trim($row['father_education']);
                $data['mother_name'] = trim($row['mother_name']);
                $data['mother_occupation'] = trim($row['mother_occupation']);
                $data['mother_education'] = trim($row['mother_education']);
                $data['guardian_name'] = trim($row['guardian_name']);
                $data['guardian_occupation'] = trim($row['guardian_occupation']);
                $data['aadhar_number'] = trim($row['aadhar_number']);
                $data['address'] = trim($row['address']);
                $data['pincode'] = trim($row['pin_code']);
                $data['bloodgroup'] = trim($row['blood_group']);
                $data['religion'] = trim($row['religion']);
                $data['medium_of_instruction'] = trim($row['medium_of_instruction']);

                $data['admission_no'] = trim($row['admission_number']);
                
                $data['community'] = trim($row['community']);
                $data['disability'] = trim($row['disability_group_name']);
                $data['group_code'] = trim($row['groupcode']);

 
                $data['mother_tongue'] = trim($row['mother_tounge']);   
                $data['medium_1'] = trim($row['medium_1']);
                $data['medium_2'] = trim($row['medium_2']);
                $data['medium_3'] = trim($row['medium_3']);
                $data['medium_4'] = trim($row['medium_4']);
                $data['medium_5'] = trim($row['medium_5']);
                $data['medium_6'] =  trim($row['medium_6']); 
                $data['medium_7'] = trim($row['medium_7']);
                $data['medium_8'] = trim($row['medium_8']);
                $data['medium_9'] = trim($row['medium_9']);
                $data['medium_10'] = trim($row['medium_10']);
                $data['medium_11'] = trim($row['medium_11']);
                $data['medium_12'] =  trim($row['medium_12']); 

                if($user_id > 0) {
                    $student = Student::where('user_id', $user_id)->first();
                    if(empty($student)){
                        Student::insertGetId($data);
                    }else{
                        Student::where('user_id', $user_id)->update($data);
                    } 
                }   else {
                    $data['user_id'] =  $id; 
                    Student::insertGetId($data);
                }

                if ($user_id > 0) {
                    $academics = StudentAcademics::where('user_id', $user_id)->first();
                    if(empty($academics)){
                        $academics = new StudentAcademics();
                    }else{
                        $academics->updated_at = date('Y-m-d H:i:s');
                        $academics->updated_by = Auth::User()->id;
                    }
                } else {
                    $academics = new StudentAcademics();
                    $academics->created_at = date('Y-m-d H:i:s');
                    $academics->created_by = Auth::User()->id;
                }

                $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->orderby('id', 'asc')->first();
                if(!empty($settings)) {} else {
                    $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
                }
                $school_id = Auth::User()->id;
                $acadamic_year = $settings->acadamic_year;
                $from_month = $acadamic_year.'-'.'06';
                $to_year = $acadamic_year + 1;
                $to_month = $to_year.'-'.'04';

                $academics->school_id = $school_id;
                $academics->user_id = $user_id;
                $academics->academic_year = $acadamic_year;
                $academics->from_month = $from_month;
                $academics->to_month = $to_month;
                $academics->class_id = $class_id;
                $academics->section_id = $section_id;
                $academics->status = 'ACTIVE';

                $academics->save();
            }

        } catch(\Illuminate\Database\QueryException $ex){ 
            return response()->json(['status' => 'FAILED', 'message' => dd($ex->getMessage())]);//dd();  
        }
    }

 
}