<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use App\Models\User;
use App\Models\Classes; 
use App\Models\Chapters;
use App\Models\ChapterTopics;
use App\Models\Topics;
use App\Models\Countries;
use App\Models\States;
use App\Models\Districts;
use App\Models\Circulars;
use App\Models\Homeworks;
use App\Models\Subjects;
use App\Models\Events;
use App\Models\Leaves;
use App\Models\QuestionBanks;
use App\Models\QuestionBankItems;
use App\Models\QuestionTypes;
use App\Models\QuestionTypeSettings;
use App\Models\Tests;
use App\Models\TestItems;
use App\Models\StudentTests;
use App\Models\StudentTestAnswers;
use App\Models\Terms;
use App\Models\MarksEntry;
use App\Models\MarksEntryItems;
use App\Models\Notifications;
use App\Models\StudentAcademics;
use App\Models\Student;
use App\Models\ReceiptHead;
use App\Models\FeeCategory;
use App\Models\PaymentMode;
use App\Models\FeeCancelReason;
use App\Models\ConcessionCategory;
use App\Models\SchoolBankList;
use App\Models\FeeItems;
use App\Models\FeeTerm;
use App\Models\FeeStructureList;
use App\Models\FeeStructureItem;
use App\Models\FeesPaymentDetail;
use App\Models\Account;
use App\Models\ContactsList;
use App\Models\CommunicationGroup;

use Response;
use Log;
use Auth;
use DB;
use Input;
use Validator;
use Hash;
use Mail;
use Crypt;
use Carbon\Carbon;
use Session;

class ApiController extends Controller
{   
	
    public function __construct()    { 
        $site_on_off = CommonController::getSiteStatus();
        if($site_on_off != "ON") {
            echo json_encode(['status' => 3, 'data' => null, 'message' => "Under Maintenance"]);
            exit;
        }
    }

    /* To check all the mandatory parameters are given as the input for the api call
    Fn Name: checkParams
    return: Error Info / empty
    */
    public function checkParams($input = [], $requiredParams = [], $request = [], $isform=false) {
        $error = ''; 

        if($isform) {
            $input = $request->all();
        }

        if(empty($input) || empty($requiredParams)) {
            $error = "Please input all the required parameters ds";
            return $error;
        }
       // echo "<pre>"; print_r($input); print_r($requiredParams); exit;
        if(count($input)>0 && count($requiredParams)>0) {
            foreach($requiredParams as $key=>$value) {
                if($value == 'api_token') {
                    $api_token = $request->header('x-api-key');
                    if(empty($api_token)) {
                        $error .= ' Api key' . ', ';
                    }
                } else if(!isset($input[$value])) {
                    $error .= $value . ', ';
                }
            }
            if(!empty($error)) {
                $error .= ' parameters missing in input';
            }
        }   else {
            $error = "Please input all the required parameters";
        }
        return $error;
    }
  

    /* Mobile Number Verification using OTP
    Fn Name: otpVerification
    return: Success Message with the User info / Failure Message
    */
    public function otpVerification(Request $request)    {

        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['mobile', 'otp', 'school_id'];   // , 'api_token'

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $mobile = isset($input['mobile']) ? $input['mobile'] : '';

                $otp = isset($input['otp']) ? $input['otp'] : '';

                $school_id = isset($input['school_id']) ? $input['school_id'] : ''; 

                //$api_token = $request->header('x-api-key');

                $user = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                    ->where('status', 'ACTIVE')->orderby('id', 'asc')->first(); 
                if(!empty($user)) { 
                    if($user->otp == $otp) {
                        DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->where('status', 'ACTIVE')
                            ->update(['is_otp_verified' => 1]);

                        return response()->json(['status' => 1, 'message' => 'OTP verified successfully', 'data' => $mobile]);
                    }   else {
                        return response()->json(['status' => 0, 'message' => 'Invalid OTP']);
                    }
                    
                }   else {
                    return response()->json(['status' => 0, 'message' => 'Invalid User']);
                } 
                
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } 
    }
 

    /*  Resend OTP
    Fn Name: resendOtp
    return: Success Message with the User info and the OTP sent again / Failure Message
    */
    public function resendOtp(Request $request)   {
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['mobile',  'school_id'];   //, 'api_token'

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {
                $mobile = isset($input['mobile']) ? $input['mobile'] : ''; 

                $school_id = isset($input['school_id']) ? $input['school_id'] : ''; 

               // $mes = User::checkTokenExpiry($userid, $api_token);
                $status = 1; //$mes['status'];   
                $message = ''; //$mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'message' => $message]);
                }   else {
                    if($school_id > 0 && !empty($mobile)) { 
                        /*$id = User::where('id', $userid)->where('email', $email)->value('id');
                        if($id>0) {}
                        else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid email']);
                        }
                        CommonController::SendOTPEmail($userid, $email);  
                        return response()->json(['status' => 1, 'message' => 'OTP sent again', 'data' => $email]); */

                        $user = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->where('status', 'ACTIVE')->orderby('id', 'asc')->first(); 
                        if(!empty($user)) {
                            $postfields = [];
                            $otpGeneration  = CommonController::generateNumericOTP(4);
                            DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->where('status', 'ACTIVE')->where('id', $user->id)->update(['otp'=>$otpGeneration]);
                            CommonController::SendOTPSMS($user->code_mobile, $otpGeneration, $school_id);  
                            return response()->json(['status' => 1, 'message' => 'OTP sent successfully', 'data' => $mobile]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Invalid User']);
                        }

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid details']);
                    }
                }  
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    public function postUserLogin(Request $request) {
       // try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['email','password', 'fcm_token', 'device_id', 'device_type', 'school_id']; 

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $email = $input['email'];   

                $password = $input['password'];    

                $fcmtoken = $input['fcm_token'];      

                $device_id = $input['device_id'];

                $device_type = $input['device_type'];  

                $school_id = $input['school_id']; 

                if($school_id > 0)   {} else {
                    return response()->json(['status' => 0, 'message' => 'Invalid School']);
                }

                $regex = '/^[^0-9][_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

                //if (preg_match($regex, $email)) {
                    //if (Auth::attempt(['email' => $email, 'password' => $password, 'user_type' => 'STUDENT'])) {
                    if (Auth::attempt(['mobile' => $email, 'password' => $password, 'user_type' => 'STUDENT', 'school_college_id' =>$school_id, 'delete_status' => 0])) {
                        $user = User::where('mobile', $email)->whereIn('user_type', ['STUDENT'])->where('status', 'ACTIVE')
                            ->where('school_college_id', $school_id)->where('delete_status', 0)->orderby('id', 'asc')->first();
                        if(empty($user)) {
                            return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                        }
                        $chk_mapping = DB::table('student_class_mappings')->where('user_id',$user->id)->get()->count();
                        if($chk_mapping > 0){
                            if(!empty($user)) {
                                $user->fcm_id = $fcmtoken; 
                                $date = date('Y-m-d H:i:s'); 
                                $def_expiry_after =  CommonController::getDefExpiry();
                                $user->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                                //$user->api_token = User::random_strings(30);
                                $user->last_login_date = date('Y-m-d H:i:s'); 
                                $user->last_app_opened_date = date('Y-m-d H:i:s'); 
                                $user->user_source_from = $device_type;
                                $user->save(); 

                                DB::table('users_loginstatus')->insert([
                                    'user_id' => $user->id,
                                    'fcm_id' => $fcmtoken,
                                    'device_id' => $device_id,
                                    'device_type' => $device_type,
                                    'api_token_expiry' => $user->api_token_expiry,
                                    'created_at' => $date,
                                ]);

                                $user = CommonController::getUserDetails($user->id); 
                                $user->login_type = "Email"; 
                                return response()->json(['status' => 1, 'data' => $user, 'message' => 'Logged in successfully']); 
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'Your account is blocked. Please contact Admin']);
                            }
                        } else{
                            return response()->json(['status' => 0, 'message' => 'You are not mapped to class so that unable to Login']);
                        }
                    } /* else {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                    } */
                
                //} else { 
                    if(Auth::attempt(['admission_no' => $email, 'password' => $password, 'user_type' => 'STUDENT', 'school_college_id' =>$school_id, 'delete_status' => 0])){

                        $user = User::where('admission_no', $email)->whereIn('user_type', ['STUDENT'])->where('status', 'ACTIVE')
                            ->where('school_college_id', $school_id)->where('delete_status', 0)->orderby('id', 'asc')->first();
                        if(empty($user)) {
                            return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                        }
                        $chk_mapping = DB::table('student_class_mappings')->where('user_id',$user->id)->get()->count();
                        if($chk_mapping > 0){
                            if(!empty($user)) {
                                $user->fcm_id = $fcmtoken; 
                                $date = date('Y-m-d H:i:s'); 
                                $def_expiry_after =  CommonController::getDefExpiry();
                                $user->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                                $user->api_token = User::random_strings(30);
                                $user->last_login_date = date('Y-m-d H:i:s'); 
                                $user->last_app_opened_date = date('Y-m-d H:i:s'); 
                                $user->user_source_from = $device_type;
                                $user->save(); 

                                DB::table('users_loginstatus')->insert([
                                    'user_id' => $user->id,
                                    'fcm_id' => $fcmtoken,
                                    'device_id' => $device_id,
                                    'device_type' => $device_type,
                                    'api_token_expiry' => $user->api_token_expiry,
                                    'created_at' => $date,
                                ]);

                                $user = CommonController::getUserDetails($user->id);  
                                $user->login_type = "Admission_no";
                                return response()->json(['status' => 1, 'data' => $user, 'message' => 'Logged in successfully']); 
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'Your account is blocked. Please contact Admin']);
                            }

                        }
                        else{
                            return response()->json(['status' => 0, 'message' => 'You are not mapped to class so that unable to Login']);
                        }
                    }
                    else {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                    }  
                //}
            
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        /*}   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } */
    }

    /*  userLogout
    Fn Name: userLogout
    return: Success Message with the userLogout post / Failure Message
    */
    public function userLogout(Request $request)    {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'fcm_token', 'device_id', 'device_type']; // , 'api_token'

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $fcm_token = ((isset($input) && isset($input['fcm_token']))) ? $input['fcm_token'] : '';
                $device_id = ((isset($input) && isset($input['device_id']))) ? $input['device_id'] : '';
                $device_type = ((isset($input) && isset($input['device_type']))) ? $input['device_type'] : '';
                
                /*$api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];*/
                $status = 1; $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    if($userid > 0) {  
   
                        $user = DB::table('users')->where('id', $userid)->first();

                        if(!empty($user)) { 
                            $api_token = User::random_strings(30); 
                            DB::table('users')->where('id', $userid)
                                ->update(['api_token' => $api_token, 'updated_at' => date('Y-m-d H:i:s')]); 
                            DB::table('users_loginstatus')->insert([
                                'user_id' => $user->id,
                                'fcm_id' => $fcm_token,
                                'device_id' => $device_id,
                                'device_type' => $device_type,
                                'api_token_expiry' => $user->api_token_expiry,
                                'status' => 'LOGOUT',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);

                            return response()->json([
                                'status' => 1,
                                'message' => 'Logout successfully.',
                                'data' => null
                            ]);
                             
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Invalid user']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    } 

    /* Profile Change Password */
    public function postProfileChangePassword(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'new_password'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $new_password = ((isset($input) && isset($input['new_password']))) ? $input['new_password'] : '';  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
               else {
                    if($userid > 0 && !empty($new_password)) { 
                        $mobile = DB::table('users')->where('id', $userid)->value('mobile');
                        $school_id = DB::table('users')->where('id', $userid)->value('school_college_id');

                        $date = date('Y-m-d H:i:s'); 
                        $def_expiry_after =  CommonController::getDefExpiry();
                        $api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                        $api_token = User::random_strings(30);

                        DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->update(['password'=>Hash::make($new_password), 'passcode' => $new_password, 
                                    'api_token' => $api_token, 'api_token_expiry' => $api_token_expiry]);
                        
                        $user = CommonController::getUserDetails($userid);  
                        if($user) {
                            return response()->json(['status' => 1, 'message' => 'Password changed successfully', 'data' => $user]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Invalid User']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    } 

    /* Change Password */
    public function postChangePassword(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'new_password'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $new_password = ((isset($input) && isset($input['new_password']))) ? $input['new_password'] : '';  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
               else {
                    if($userid > 0 && !empty($new_password)) { 
                        $mobile = DB::table('users')->where('id', $userid)->value('mobile');
                        $school_id = DB::table('users')->where('id', $userid)->value('school_college_id');
                        DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->update(['password'=>Hash::make($new_password), 'passcode' => $new_password, 'is_password_changed' => 1]);
                        
                        $user = CommonController::getUserDetails($userid);  
                        if($user) {
                            return response()->json(['status' => 1, 'message' => 'Password changed successfully', 'data' => $user]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Invalid User']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    } 

    /* Forgot Password */
    public function postForgotPassword(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['mobile', 'school_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $mobile = ((isset($input) && isset($input['mobile']))) ? $input['mobile'] : '';  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
               else {
                    if($school_id > 0 && !empty($mobile)) { 
    
                        $user = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->where('status', 'ACTIVE')->orderby('id', 'asc')->first(); 
                        if(!empty($user)) {
                            $postfields = [];
                            $otpGeneration  = CommonController::generateNumericOTP(4);
                            DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->where('status', 'ACTIVE')->where('id', $user->id)->update(['otp'=>$otpGeneration]);
                            CommonController::SendOTPSMS($user->code_mobile, $otpGeneration, $school_id); 
                            return response()->json(['status' => 1, 'message' => 'OTP sent successfully', 'data' => $mobile]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Invalid User']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    } 

    /* Reset Password */
    public function postResetPassword(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['mobile', 'school_id', 'otp', 'new_password'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {
                $mobile = ((isset($input) && isset($input['mobile']))) ? $input['mobile'] : '';  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $otp = ((isset($input) && isset($input['otp']))) ? $input['otp'] : 0;  
                $new_password = ((isset($input) && isset($input['new_password']))) ? $input['new_password'] : '';  
 
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
               else {
                    if(!empty($mobile) && !empty($otp) && !empty($new_password) && ($school_id > 0)) { 
    
                        $user = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                            ->where('status', 'ACTIVE')->orderby('id', 'asc')->first(); 
                        if(!empty($user)) { 
                            if($user->otp == $otp) {
                                DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)
                                    ->where('status', 'ACTIVE')
                                    ->update(['password'=>Hash::make($new_password), 'passcode' => $new_password, 
                                        'is_password_changed' => 1, 'is_otp_verified' => 1]);


                                return response()->json(['status' => 1, 'message' => 'Password changed successfully', 'data' => $mobile]);
                            } else {
                                return response()->json(['status' => 0, 'message' => 'Invalid OTP']);
                            }
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Invalid User']);
                        }
 
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    } 
    
    /* get Mobile Scholars */
    public function getMobileScholars(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
               else {
                    if($userid > 0) { 
    
                        $user = DB::table('users')->where('id', $userid)->select('mobile', 'school_college_id')->first();
                        if(!empty($user)) {
                            $user = User::with('userdetails')->where('mobile', $user->mobile)->where('user_type', 'STUDENT')
                                ->where('status', 'ACTIVE')->where('delete_status', 0)
                                ->where('school_college_id', $user->school_college_id)
                                ->select('id', 'reg_no', 'user_type', 'state_id', 'city_id', 'country','admission_no',
                                    'name', 'last_name', 'email', 'gender', 'dob', 'country_code', 'mobile','code_mobile', 
                                    'mobile1','codemobile1', 'emergency_contact_no', 'last_login_date', 'last_app_opened_date', 
                                    'user_source_from', 'api_token', 'api_token_expiry', 'is_password_changed',
                                    'notification_status', 'joined_date', 'profile_image', 'status')->get(); 
                         
                            if($user->isNotEmpty()) {
                                return response()->json(['status' => 1, 'message' => 'Scholars list', 'data' => $user]);
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'No Scholars']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid User']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    /* Get Student Home Page Contents */
    public function getHomeContents(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
               else {
                    if($userid > 0) { 

                        DB::table('users')->where('id', $userid)->update(['last_app_opened_date' => date('Y-m-d H:i:s'), 
                        'is_app_installed' => 1]); 

                        $subjects = ''; $tom_timetable = '';

                        /*$user_details = DB::table('students')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  */

                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) {
                            /*$mapped_subjects = $user_details->mapped_subjects;
                            if(!empty($mapped_subjects)) {
                                $mapped_subjects = explode(',', $mapped_subjects);
                                $mapped_subjects = array_unique($mapped_subjects);
                                if(count($mapped_subjects)>0) {
                                    $subjects = DB::table('subjects')->whereIn('id', $mapped_subjects)->orderby('position', 'asc')->get();
                                }
                            } */

                            $subarr = [];
                            $subjects = DB::table('subjects')->where('status', 'ACTIVE')->select('id', 'subject_name', 'subject_colorcode')->get();
                            if($subjects->isNotEmpty()) {
                                foreach($subjects as $sub) {
                                    $subarr[$sub->id] = $sub->subject_name; 
                                }
                            }

                            $tomorrow = date('N', strtotime('+1 day'));
                            $tom_timetable = DB::table('timetables')->where('class_id', $user_details->class_id)
                                ->where('section_id', $user_details->section_id)
                                ->where('day_id', $tomorrow)
                                ->first();

                            $period_timings_arr = $timetable = [];
                            $period_timings = DB::table('period_timings')->where('class_id', $user_details->class_id)
                                //->where('id', 1)
                                ->first();
                            if(empty($period_timings)) {
                                $period_timings = DB::table('period_timings')->where('id', 1)->first();
                            }
                            if(!empty($period_timings)) {
                                $period_timings_arr['period_1'] = $period_timings->period_1;
                                $period_timings_arr['period_2'] = $period_timings->period_2;
                                $period_timings_arr['period_3'] = $period_timings->period_3;
                                $period_timings_arr['period_4'] = $period_timings->period_4;
                                $period_timings_arr['period_5'] = $period_timings->period_5;
                                $period_timings_arr['period_6'] = $period_timings->period_6;
                                $period_timings_arr['period_7'] = $period_timings->period_7;
                                $period_timings_arr['period_8'] = $period_timings->period_8;
                            }

                            if($tom_timetable) {  
                                // echo "<pre>"; print_r($period_timings_arr); exit;
                                $timetable[] = ['time' => $period_timings_arr['period_1'], 'subject'=>(isset($subarr[$tom_timetable->period_1])) ? $subarr[$tom_timetable->period_1] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_2'], 'subject'=>(isset($subarr[$tom_timetable->period_2])) ? $subarr[$tom_timetable->period_2] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_3'], 'subject'=>(isset($subarr[$tom_timetable->period_3])) ? $subarr[$tom_timetable->period_3] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_4'], 'subject'=>(isset($subarr[$tom_timetable->period_4])) ? $subarr[$tom_timetable->period_4] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_5'], 'subject'=>(isset($subarr[$tom_timetable->period_5])) ? $subarr[$tom_timetable->period_5] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_6'], 'subject'=>(isset($subarr[$tom_timetable->period_6])) ? $subarr[$tom_timetable->period_6] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_7'], 'subject'=>(isset($subarr[$tom_timetable->period_7])) ? $subarr[$tom_timetable->period_7] : ''];

                                $timetable[] = ['time' => $period_timings_arr['period_8'], 'subject'=>(isset($subarr[$tom_timetable->period_8])) ? $subarr[$tom_timetable->period_8] : '']; 
                                
                            }
                        } 

                        $circulars_qry = Events::where('status', 'ACTIVE')->where('approve_status', 'APPROVED')
                            ->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_ids)')
                            ->whereRAW('YEAR(circular_date) = "'.date('Y').'" ')
                            ->whereRAW('MONTH(circular_date) = "'.date('m').'" '); 

                        $circulars = $circulars_qry->orderby('circular_date', 'desc')
                            ->skip(0)->take(2)->get();
                        $current_date = date('Y-m-d');
                        // $mindate = date('Y-m-d', strtotime($mindate));
                        // $leave_qry;
                        $data = [];
                        //$query = DB::getQueryLog();          echo "<pre>"; print_r($query); 
                        //$query = DB::getQueryLog();          echo "<pre>"; print_r($query);
                        //if(!empty($subjects)) {
                            $data["subjects"] =$subjects;
                        /*}
                        if(!empty($timetable)) {*/
                            $data["timetable"] = $timetable;
                        /*}
                        if(!empty($circulars)) {*/
                            $data["calendar"] =$circulars;

                            //Today Events
                            /*
                            1 => Events,
                            2=> Circulars,
                            3 => Homeworks,
                            4 => Student Leaves,
                            5 => Test
                            */

                            $today_events = DB::table('events')->where('status', 'ACTIVE')->where('approve_status', 'APPROVED')
                            ->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_ids)')
                            ->whereDate('circular_date', date('Y-m-d'))->select('id','circular_title as title','type')->get();
                       if(!empty($today_events)){
                        $events = $today_events->toArray();
                        }
                             $today_circular = DB::table('circular')->where('status', 'ACTIVE')->where('approve_status', 'APPROVED')
                             ->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_ids)')
                             ->whereDate('circular_date', date('Y-m-d'))->select('id','circular_title as title','type')->get();
                             if(!empty($today_circular)){
                                $circular = $today_circular->toArray();
                               }

                               $today_tests = DB::table('tests')->where('status', 'ACTIVE')->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_id)')
                               ->whereDate('created_at', date('Y-m-d'))->where('is_self_test',0)->select('subject_id as id','test_name as title','type')->get();
                               if(!empty($today_tests)){
                                  $tests = $today_tests->toArray();
                                 }

                                 
                             $today_homework = DB::table('homeworks')
                                ->leftjoin('subjects', 'subjects.id', 'homeworks.subject_id')
                                ->where('homeworks.status', 'ACTIVE')
                                ->whereRaw('FIND_IN_SET('.$user_details->class_id.', homeworks.class_id)')
                                ->where('homeworks.section_id', $user_details->section_id)
                                ->whereDate('homeworks.created_at', date('Y-m-d'))
                                ->select('homeworks.id','subjects.subject_name as title','homeworks.type')
                                ->orderby('id', 'desc')->get();
                                 if(!empty($today_homework)){
                                    $homeworks = $today_homework->toArray();
                                   }

                        // $today_leave = DB::table('leaves')->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_id)')->whereRaw("'".$monthyear."' BETWEEN from_month and to_month")->select('id','hw_title')->get();
                          $today_leave =DB::table('leaves')->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_id)')->where('status', 'APPROVED')
                          ->where('student_id',$userid)
                          ->whereDate('created_at', date('Y-m-d'))->select('id','leave_reason as title','type')->get();
                          if(!empty($today_leave)){
                             $leaves = $today_leave->toArray();
                            }
                           $arr1 = array_merge($events, $circular,$homeworks,$leaves,$tests); 

                           $data['today_activity'] = $arr1;

                        $is_leave_today = DB::table('attendance_approval')
                            ->where('class_id', $user_details->class_id)
                            ->where('section_id', $user_details->section_id)
                            ->where('user_id', $userid)
                            ->where('admin_status', 1)
                            ->whereDate('date', date('Y-m-d'))->get();

                        if($is_leave_today->isNotEmpty()) {
                            $fn = $is_leave_today[0]->fn_status;
                            $an = $is_leave_today[0]->an_status;

                            if($fn == 2 && $an == 2) {
                                $is_leave_today = 2;
                            }   else if($fn == 1 || $an == 1) {
                                $is_leave_today = 1; 
                            }   else {
                                $is_leave_today = 0;
                            }
                            
                        }   else {
                            $is_leave_today = 0;
                        }
                        $data['is_leave_today'] = $is_leave_today;

                        $gender = DB::table('users')->where('users.id', $userid)->value('gender');   
                        if($gender == 'MALE') {
                            $gender = 'My son ';
                        }   elseif($gender == 'FEMALE') {
                            $gender = 'My daughter ';
                        } else {
                            $gender = '';
                        }

                        if ($is_leave_today == 0){
                            $data['is_leave_text'] = "";
                        } elseif ($is_leave_today == 1){
                            $data['is_leave_text'] = $gender. " is Present Today";
                        } elseif ($is_leave_today == 2){
                            $data['is_leave_text'] = $gender. " is Absent Today";
                        }   else {
                            $data['is_leave_text'] = "";
                        }

                        $notescount = Notifications::where('read_status', 0) 
                        ->where('user_id', $userid)->whereIn('type_no', [4,5]) 
                        ->select('id')->count();  
                        $data['notescount'] = $notescount;

                        if(count($data)>0) {
                            return response()->json(['status' => 1, 'message' => 'Home contents', 'data' => $data]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No home contents']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    /*  Communications  Post list Details  
    Fn Name: getPostCommunications
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getPostCommunications(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request); 

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0; 
                $category_id = ((isset($input) && isset($input['category_id']))) ? $input['category_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $from_date = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : ''; 
                $to_date = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : ''; 
                $type = ((isset($input) && isset($input['type']))) ? $input['type'] : 0; 
                if($type > 0) { } else { $type = 0; }
                // 0 - all, 1 - post, 2 - sms
                $api_token = $request->header('x-api-key'); 
                $limit = CommonController::$page_limit;
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message']; 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        /*$post_communications = Notifications::leftjoin('communication_posts', 'communication_posts.id', 'notifications.post_id')->where('notifications.user_id', $userid)
                            ->whereIn('notifications.type_no', [4,5])
                            ->where('communication_posts.delete_status', 0)
                            //->where('communication_posts.notify_datetime', '<=', date('Y-m-d H:i:s'))
                            ->select('communication_posts.*', 'notifications.type_no', 'notifications.type_id', 'notifications.id as notification_id', 'notifications.is_acknowledged')
                            ->orderby('communication_posts.notify_datetime', 'desc')->skip($page_no)->take($limit)->get();  */

                        if($type == 0) {

                            $post_communications = Notifications::leftjoin('communication_posts', 'communication_posts.id', 'notifications.post_id')->where('notifications.user_id', $userid)
                                ->whereIn('notifications.type_no', [4])
                                ->where('communication_posts.delete_status', 0)->where('communication_posts.status', 'ACTIVE')
                                //->where('communication_posts.notify_datetime', '<=', date('Y-m-d H:i:s'))
                                ->select('communication_posts.id', 'communication_posts.title', 'communication_posts.message', 
                                    'communication_posts.media_attachment', 'communication_posts.image_attachment', 
                                    'communication_posts.video_attachment', 'communication_posts.files_attachment',
                                     'communication_posts.youtube_link', 
                                    'communication_posts.background_id', 'communication_posts.request_acknowledge', 
                                    'communication_posts.notify_datetime', 'communication_posts.posted_by',  
                                    'communication_posts.category_id',
                                    'notifications.type_no', 'notifications.type_id', 'notifications.id as notification_id', 
                                    'notifications.is_acknowledged', DB::RAW('0 as is_sms_type'));

                            if(!empty(trim($from_date))) {
                                $from_date = date('Y-m-d', strtotime($from_date));
                                $post_communications->whereRaw('communication_posts.notify_datetime >= ?', [$from_date]);  
                            }
                            if(!empty(trim($to_date))) {
                                $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                                $post_communications->whereRaw('communication_posts.notify_datetime <= ?', [$to_date]); 
                            }
                            if($category_id > 0) { 
                                $post_communications->where('communication_posts.category_id', $category_id); 
                            } 

                            if(!empty(trim($search))) { 
                                $post_communications->whereRaw(' ( communication_posts.title like "%'.$search.'%" or communication_posts.message like "%'.$search.'%" ) '); 
                            }

                            $sms_communications = Notifications::leftjoin('communication_sms', 'communication_sms.id', 'notifications.post_id')
                                ->leftjoin('categories', 'categories.id', 'communication_sms.category_id')
                                ->where('notifications.user_id', $userid)
                                ->whereIn('notifications.type_no', [5])
                                ->where('communication_sms.delete_status', 0)->where('communication_sms.status', 'ACTIVE')
                                ->where('communication_sms.smart_sms', '!=', 2)
                                //->where('communication_posts.notify_datetime', '<=', date('Y-m-d H:i:s'))
                                ->select('communication_sms.id', 'categories.name as title', 'notifications.message', 
                                    DB::RAW('"" as media_attachment'), DB::RAW('"" as image_attachment'), 
                                    DB::RAW('"" as video_attachment'), DB::RAW('"" as files_attachment'), 
                                    DB::RAW('"" as youtube_link'), 
                                    DB::RAW('1 as background_id'), DB::RAW('0 as request_acknowledge'), 
                                    'communication_sms.notify_datetime', 'communication_sms.posted_by',  
                                    'communication_sms.category_id',
                                    'notifications.type_no', 'notifications.type_id', 'notifications.id as notification_id', 'notifications.is_acknowledged', DB::RAW('1 as is_sms_type'));
                            if(!empty(trim($from_date))) {
                                $from_date = date('Y-m-d', strtotime($from_date));
                                $sms_communications->whereRaw('communication_sms.notify_datetime >= ?', [$from_date]);  
                            }
                            if(!empty(trim($to_date))) {
                                $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                                $sms_communications->whereRaw('communication_sms.notify_datetime <= ?', [$to_date]); 
                            }
                            if($category_id > 0) { 
                                $sms_communications->where('communication_sms.category_id', $category_id); 
                            } 
                            if(!empty(trim($search))) { 
                                $sms_communications->whereRaw(' ( communication_sms.content like "%'.$search.'%" ) '); 
                            }

                            $sms_communications = $sms_communications->union($post_communications)
                            ->orderby('notify_datetime', 'desc')->skip($page_no)->take($limit)
                            ->get();

                        } else if($type == 1) {
                            $post_communications = Notifications::leftjoin('communication_posts', 'communication_posts.id', 'notifications.post_id')->where('notifications.user_id', $userid)
                                ->whereIn('notifications.type_no', [4])
                                ->where('communication_posts.delete_status', 0)->where('communication_posts.status', 'ACTIVE')
                                //->where('communication_posts.notify_datetime', '<=', date('Y-m-d H:i:s'))
                                ->select('communication_posts.id', 'communication_posts.title', 'communication_posts.message', 
                                    'communication_posts.media_attachment', 'communication_posts.image_attachment', 
                                    'communication_posts.video_attachment', 'communication_posts.files_attachment',
                                     'communication_posts.youtube_link', 
                                    'communication_posts.background_id', 'communication_posts.request_acknowledge', 
                                    'communication_posts.notify_datetime', 'communication_posts.posted_by',  
                                    'communication_posts.category_id',
                                    'notifications.type_no', 'notifications.type_id', 'notifications.id as notification_id', 
                                    'notifications.is_acknowledged', DB::RAW('0 as is_sms_type'));

                            if(!empty(trim($from_date))) {
                                $from_date = date('Y-m-d', strtotime($from_date));
                                $post_communications->whereRaw('communication_posts.notify_datetime >= ?', [$from_date]);  
                            }
                            if(!empty(trim($to_date))) {
                                $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                                $post_communications->whereRaw('communication_posts.notify_datetime <= ?', [$to_date]); 
                            }
                            if($category_id > 0) { 
                                $post_communications->where('communication_posts.category_id', $category_id); 
                            } 

                            if(!empty(trim($search))) { 
                                $post_communications->whereRaw(' ( communication_posts.title like "%'.$search.'%" or communication_posts.message like "%'.$search.'%" ) '); 
                            }

                            $sms_communications = $post_communications
                            ->orderby('notify_datetime', 'desc')->skip($page_no)->take($limit)
                            ->get();
                        } else if($type == 2) {
                            $sms_communications = Notifications::leftjoin('communication_sms', 'communication_sms.id', 'notifications.post_id')
                                ->leftjoin('categories', 'categories.id', 'communication_sms.category_id')
                                ->where('notifications.user_id', $userid)
                                ->whereIn('notifications.type_no', [5])
                                ->where('communication_sms.delete_status', 0)->where('communication_sms.status', 'ACTIVE')
                                ->where('communication_sms.smart_sms', '!=', 2)
                                //->where('communication_posts.notify_datetime', '<=', date('Y-m-d H:i:s'))
                                ->select('communication_sms.id', 'categories.name as title', 'notifications.message', 
                                    DB::RAW('"" as media_attachment'), DB::RAW('"" as image_attachment'), 
                                    DB::RAW('"" as video_attachment'), DB::RAW('"" as files_attachment'), 
                                    DB::RAW('"" as youtube_link'), 
                                    DB::RAW('1 as background_id'), DB::RAW('0 as request_acknowledge'), 
                                    'communication_sms.notify_datetime', 'communication_sms.posted_by',  
                                    'communication_sms.category_id',
                                    'notifications.type_no', 'notifications.type_id', 'notifications.id as notification_id', 'notifications.is_acknowledged', DB::RAW('1 as is_sms_type'));
                            if(!empty(trim($from_date))) {
                                $from_date = date('Y-m-d', strtotime($from_date));
                                $sms_communications->whereRaw('communication_sms.notify_datetime >= ?', [$from_date]);  
                            }
                            if(!empty(trim($to_date))) {
                                $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                                $sms_communications->whereRaw('communication_sms.notify_datetime <= ?', [$to_date]); 
                            }
                            if($category_id > 0) { 
                                $sms_communications->where('communication_sms.category_id', $category_id); 
                            } 
                            if(!empty(trim($search))) { 
                                $sms_communications->whereRaw(' ( communication_sms.content like "%'.$search.'%" ) '); 
                            }

                            $sms_communications = $sms_communications
                            ->orderby('notify_datetime', 'desc')->skip($page_no)->take($limit)
                            ->get();
                        }



                        if($sms_communications->isNotEmpty()) {
                            $ids = [];
                            foreach($sms_communications as $k=>$v) {
                                $ids[] = $v->notification_id;
                            }
                            Notifications::whereIn('id', $ids)->update([
                                'read_date' => date('Y-m-d H:i:s'),
                                'read_status' => 1,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);

                            return response()->json(['status' => 1, 'message' => 'Post list', 'data' => $sms_communications]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Posts']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    } 

    /*  Communications  Post acknowledge Details  
    Fn Name: acknowledgePostCommunications
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function acknowledgePostCommunications(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'post_id'];

            $error = $this->checkParams($input, $requiredParams, $request); 

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $post_id = ((isset($input) && isset($input['post_id']))) ? $input['post_id'] : 0;  
                $api_token = $request->header('x-api-key');  
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message']; 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $post_id > 0) {  

                        $post_communications = Notifications::leftjoin('communication_posts', 'communication_posts.id', 'notifications.post_id')->where('notifications.user_id', $userid)->where('notifications.type_no', 4)
                            ->where('communication_posts.delete_status', 0)->where('communication_posts.id', $post_id)->get();  

                        if($post_communications->isNotEmpty()) {

                            Notifications::leftjoin('communication_posts', 'communication_posts.id', 'notifications.post_id')->where('notifications.user_id', $userid)->where('notifications.type_no', 4)
                            ->where('communication_posts.delete_status', 0)->where('communication_posts.id', $post_id)
                            ->update(['is_acknowledged'=>1, 'updated_at'=>date('Y-m-d H:i:s')]);

                            $exp_count = 0;
                            $exp = DB::table('notifications')->where(['post_id' => $post_id, 'type_no' => 4, 'is_acknowledged'=>1])->select('id')->get();
                            if($exp->isNotEmpty()) {
                                $exp_count = count($exp);
                            }

                            DB::table('communication_posts')->where('id', $post_id)->update(['acknowledged_count' => $exp_count]);

                            return response()->json(['status' => 1, 'message' => 'Post Acknowledged']);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Posts']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    } 

                        

    /*  states list Details  
    Fn Name: getStates
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getStates(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            /*$requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);*/

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                /*$api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];*/
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  

                        $states_qry = States::where('status', 'ACTIVE');
                        if(!empty($search)) {
                            $states_qry->where('state_name', 'like', '%'.$search.'%');
                        }

                        $states = $states_qry->orderby('state_name', 'asc')->get();

                        if($states->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'States list', 'data' => $states]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No States']);
                        }
                   /* }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }*/
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    /*  cities list Details  
    Fn Name: getCities
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getCities(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['state_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $state_id = ((isset($input) && isset($input['state_id']))) ? $input['state_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                /*$api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message']; */

                $status = 1;   $message = ''; 

                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  
                        if($state_id > 0) { } else { $state_id = 0; }
                        $cities_qry = Districts::where('status', 'ACTIVE');
                        if($state_id > 0) {
                            $cities_qry->where('state_id',  $state_id);
                        }
                        if(!empty($search)) {
                            $cities_qry->where('district_name', 'like', '%'.$search.'%');
                        }

                        $districts = $cities_qry->orderby('district_name', 'asc')->get();

                        if($districts->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Cities list', 'data' => $districts]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Cities list']);
                        }
                    /*}   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }*/
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    /*  Circulars list Details  
    Fn Name: getCirculars
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getCirculars(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0; 
                $api_token = $request->header('x-api-key'); 
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 

                            $circulars_qry = Circulars::where('status', 'ACTIVE')->where('approve_status', 'APPROVED')
                                ->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_ids)'); 

                            $circulars = $circulars_qry->orderby('id', 'desc')
                                //->orderby('circular_date', 'asc')
                                ->skip($page_no)->take($limit)->get();

                            if($circulars->isNotEmpty()) {
                                return response()->json(['status' => 1, 'message' => 'Circulars list', 'data' => $circulars]);
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'No Circulars']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    /*  homeworks list Details  
    Fn Name: getHomeworks
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getHomeworks(Request $request)     {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $date = ((isset($input) && isset($input['date']))) ? $input['date'] : date('Y-m-d');  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $api_token = $request->header('x-api-key'); 
                
                if(empty($date)) {
                    $date = date('Y-m-d');
                }

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 

                            $hw_qry = Homeworks::whereDate('hw_date', $date)
                                ->where('class_id', $user_details->class_id)
                                ->where('section_id', $user_details->section_id) 
                                ->where('status', 'ACTIVE');
                            // if($subject_id > 0) {
                            //     $hw_qry->where('subject_id', $subject_id); 
                            // }

                            $hw = $hw_qry->orderby('id', 'desc')->get();

                            if($hw->isNotEmpty()) {
                                return response()->json(['status' => 1, 'message' => 'Homework list', 'data' => $hw]);
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'No Homework']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        /*}   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } */ 

    } 

    /*  homeworks list Details  based on date 
    Fn Name: getHomeworksDate
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getHomeworksDate(Request $request)     {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $date = ((isset($input) && isset($input['date']))) ? $input['date'] : date('Y-m-d');  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $cnt = ((isset($input) && isset($input['cnt']))) ? $input['cnt'] : 0; 
                $api_token = $request->header('x-api-key'); 
                
                if(empty($date)) {
                    $date = date('Y-m-d');
                }

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 
                            if($cnt >0) {
                                $startdate = date('Y-m-d', strtotime("-".$cnt." days"));
                            }   else {
                                $startdate = $date;
                            }
                            $enddate = date('Y-m-d', strtotime("+1 day".$date));
                            $hw_qry = Homeworks::whereDate('hw_date', '>=', $startdate)
                                ->where('status', 'ACTIVE')
                                ->whereDate('hw_date', '<=', $enddate)
                                ->where('class_id', $user_details->class_id)
                                ->where('section_id', $user_details->section_id); 

                            // if($subject_id > 0) {
                            //     $hw_qry->where('subject_id', $subject_id); 
                            // }

                            $hw = $hw_qry->orderby('position', 'asc')->get();

                            if($hw->isNotEmpty()) {
                                return response()->json(['status' => 1, 'message' => 'Homework list', 'data' => $hw]);
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'No Homework']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        /*}   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } */ 

    } 

    /*  classes list Details  
    Fn Name: getClasses
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getClasses(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['school_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                /*$api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message']; */
                $status = 1;   $message = ''; 

                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  
                        if($school_id > 0) { } else { $school_id = 0; }
                        $classes_qry = Classes::where('status', 'ACTIVE');
                        if($school_id > 0) {
                            $classes_qry->where('school_id',  $school_id);
                        }
                        if(!empty($search)) {
                            $classes_qry->where('class_name', 'like', '%'.$search.'%');
                        }

                        $classes = $classes_qry->orderby('class_name', 'asc')->get();

                        if($classes->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Classes list', 'data' => $classes]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Classes list']);
                        }
                    /*}   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }*/
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }  

    /*  student attendance Details  
    Fn Name: getAttendance
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getAttendance(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $monthyr = ((isset($input) && isset($input['monthyr']))) ? $input['monthyr'] : date('Y-m'); 
                if(empty($monthyr)) {
                    $monthyr = date('Y-m');  
                }
                list($year, $month) = explode('-', $monthyr);
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];  

                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {   

                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id', 'students.school_id')
                            ->where('students.user_id', $userid)->first();  
 
                        $data = []; 
                        $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                            ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')->where('school_college_id', $user_details->school_id)
                            ->select('holiday_date', 'holiday_description')->orderby('holiday_date', 'asc')->get();
                        $hd = [];
                        if($holidays->isNotEmpty()) {
                            foreach($holidays as $holi) {
                                $hd[] = $holi->holiday_date;
                            }
                        }

                        $leave_days = CommonController::getSundays($year, $month); 

                        $student_leaves = DB::table('leaves')->where('student_id', $userid)
                            ->where('class_id', $user_details->class_id)
                            ->where('section_id', $user_details->section_id)
                            ->where('status', 'APPROVED')
                            ->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))') 
                            ->select('leave_date', 'leave_end_date')
                            ->get();
                            //echo "<pre>"; print_r($student_leaves); exit;
                        $student_leaves_count = ($student_leaves->isNotEmpty()) ? count($student_leaves) : 0;
                        $slvs = [];
                        if($student_leaves->isNotEmpty()) {
                            foreach($student_leaves as $slv) { 
                                if(!empty($slv->leave_date)) {  
                                    if(strtotime($slv->leave_end_date) > 0) {
                                        $dates = CommonController::getDatesFromRange($slv->leave_date, $slv->leave_end_date, 'Y-m-d', $month, $year);  
                                    }   else { 
                                        $dates = [];
                                        $dates[] = $slv->leave_date;
                                    }  
                                    $slvs = array_merge($slvs, $dates);
                                }   else { 
                                    $slvs[] = $slv->leave_date;
                                }
                            }
                        }
                        $slvs = array_unique($slvs);
                        $slvs = array_filter($slvs);
                        $slvs = array_values($slvs);

                        $student_leaves = DB::table('leaves')->where('student_id', $userid)
                        ->where('class_id', $user_details->class_id)
                        ->where('section_id', $user_details->section_id)
                        ->where('status', 'APPROVED')
                        ->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))') 
                        ->select('leave_date as holiday_date', 'leave_end_date','leave_reason as holiday_description')
                        ->groupby('leave_date')->orderby('leave_date', 'asc')
                        ->get();
                        $leave_list = [];
                        if($student_leaves->isNotEmpty()) {
                            foreach($student_leaves as $v) {
                                $leave_list[] = $v->holiday_date;
                            }
                        }

                        $attendance_approval = DB::table('attendance_approval')->where('attendance_approval.fn_status',1)
                            ->where('attendance_approval.an_status',1)->where('attendance_approval.user_id',$userid)
                            ->where('attendance_approval.class_id',$user_details->class_id)
                            ->where('attendance_approval.section_id',$user_details->section_id)
                            ->whereRAW('(( YEAR(date) = "'.$year.'" AND MONTH(date) = "'.$month.'" ))') 
                            ->where('attendance_approval.admin_status',1)
                            ->select('date')->get();

                        $att = [];
                        if($attendance_approval->isNotEmpty()) {
                            foreach($attendance_approval as $ap) { 
                                if(!empty($ap->date) && strtotime($ap->date) > 0) {   
                                    $att[] = $ap->date;
                                }   
                            }
                        } 
                        //echo "<pre>"; print_r($att); print_r($slvs);
                        $approved = array_diff($att, $slvs); // echo "<pre>"; print_r($approved);
                        $approved = array_values($approved);


                        $holidays1 = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                            ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')->where('school_college_id', $user_details->school_id)
                            ->select('holiday_date', 'holiday_description');

                        $holidays_union = DB::table('leaves')->where('student_id', $userid)
                        ->where('class_id', $user_details->class_id)
                        ->where('section_id', $user_details->section_id)
                        ->where('status', 'APPROVED')
                        ->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))') 
                        ->select('leave_date as holiday_date','leave_reason as holiday_description')
                        ->groupby('leave_date')
                        ->union($holidays1)
                        ->orderby('holiday_date', 'asc')
                        ->get();
                        
                        $data['student_leaves'] = $slvs; 
                        $data['student_present_approved'] = $approved;
                        $data['student_leaves_list'] = []; //$student_leaves;

                        $data['holidays'] = $holidays_union; // $holidays;
                        //$data['leave_days'] = $leave_days;
                        $data['holidays_union'] = $holidays_union;

                        $hd_lvdays = array_merge($hd, $leave_days);
                        $hd_lvdays = array_unique($hd_lvdays);
                        $hd_lvdays = array_filter($hd_lvdays);
                        $hd_lvdays = array_values($hd_lvdays);

                        $data['leave_days'] = $hd_lvdays;

                        $noof_working_days = CommonController::countDays($year, $month, $hd_lvdays);
                        if($noof_working_days > 0) {}
                        else $noof_working_days = 0;

                        $data['noof_working_days'] = $noof_working_days;

                        $data['student_leaves_count'] = count($slvs); //$student_leaves_count;

                        $present_days = $noof_working_days - $student_leaves_count;

                        //$att_percentage = $present_days * ( 100 / $noof_working_days);

                        $att_percentage = (($noof_working_days - count($slvs)) / $noof_working_days ) * 100;

                        $data['att_percentage'] = number_format($att_percentage,2);

                        $data['present_days'] = $present_days;

                        if(count($data)>0) {
                            return response()->json(['status' => 1, 'message' => 'Calendar', 'data' => $data]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Calendar']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }  

    
    /*  student Present Details  
    Fn Name: getPresentDays
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getPresentDays(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $monthyr = ((isset($input) && isset($input['monthyr']))) ? $input['monthyr'] : date('Y-m'); 
                if(empty($monthyr)) {
                    $monthyr = date('Y-m');  
                }
                list($year, $month) = explode('-', $monthyr);
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];  

                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {   

                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  
 
                        $data = []; 
                        $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                            ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                            ->select('holiday_date', 'holiday_description')->get();
                        $hd = [];
                        if($holidays->isNotEmpty()) {
                            foreach($holidays as $holi) {
                                $hd[] = $holi->holiday_date;
                            }
                        }

                        $leave_days = CommonController::getSundays($year, $month); 

                        $student_leaves = DB::table('leaves')->where('student_id', $userid)
                            ->where('class_id', $user_details->class_id)
                            ->where('section_id', $user_details->section_id)
                            ->where('status', 'APPROVED')
                            ->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))') 
                            ->select('leave_date', 'leave_end_date')
                            ->get();
                            //echo "<pre>"; print_r($student_leaves); exit;
                        $student_leaves_count = ($student_leaves->isNotEmpty()) ? count($student_leaves) : 0;
                        $slvs = [];
                        if($student_leaves->isNotEmpty()) {
                            foreach($student_leaves as $slv) { 
                                if(!empty($slv->leave_date)) {  
                                    if(strtotime($slv->leave_end_date) > 0) {
                                        $dates = CommonController::getDatesFromRange($slv->leave_date, $slv->leave_end_date, 'Y-m-d', $month, $year);  
                                    }   else { 
                                        $dates = [];
                                        $dates[] = $slv->leave_date;
                                    }  
                                    $slvs = array_merge($slvs, $dates);
                                }   else { 
                                    $slvs[] = $slv->leave_date;
                                }
                            }
                        }
                        $slvs = array_unique($slvs);
                        $slvs = array_filter($slvs);
                        $slvs = array_values($slvs); 


                        $attendance_approval = DB::table('attendance_approval')->where('attendance_approval.fn_status',1)
                            ->where('attendance_approval.an_status',1)->where('attendance_approval.user_id',$userid)
                            ->where('attendance_approval.class_id',$user_details->class_id)
                            ->where('attendance_approval.section_id',$user_details->section_id)
                            ->whereRAW('(( YEAR(date) = "'.$year.'" AND MONTH(date) = "'.$month.'" ))') 
                            ->where('attendance_approval.admin_status',1)
                            ->select('date')->get();

                        $att = [];
                        if($attendance_approval->isNotEmpty()) {
                            foreach($attendance_approval as $ap) { 
                                if(!empty($ap->date) && strtotime($ap->date) > 0) {   
                                    $att[] = $ap->date;
                                }   
                            }
                        } 
                        //echo "<pre>"; print_r($att); print_r($slvs);
                        $approved = array_diff($att, $slvs);  
                        if(count($approved)>0) {
                            return response()->json(['status' => 1, 'message' => 'Attendance Approved', 'data' => $approved]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Attendance']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }  

    /*  Leaves list Details  
    Fn Name: getAppliedLeaves
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getAppliedLeaves(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $monthyr = ((isset($input) && isset($input['monthyr']))) ? $input['monthyr'] : date('Y-m'); 
                if(empty($monthyr)) {
                    $monthyr = date('Y-m');  
                }
                list($year, $month) = explode('-', $monthyr);
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0; 
                $type = ((isset($input) && isset($input['type']))) ? $input['type'] : 1; 
                if(empty($type)) {
                    $type = 1; // 1 - approved , 2 - pending list
                }
                $api_token = $request->header('x-api-key'); 
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 

                            $student_leaves = DB::table('leaves')->where('student_id', $userid)
                                ->where('class_id', $user_details->class_id); 
                            if($type == 1) {
                                $student_leaves->where('status', 'APPROVED');
                            } else {
                                $student_leaves->where('status', '!=', 'APPROVED');
                            }
                                
                            $student_leaves = $student_leaves->where('section_id', $user_details->section_id)
                                ->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))') 
                                ->select('leave_date', 'leave_end_date')
                                ->get();

                            $student_leaves_count = ($student_leaves->isNotEmpty()) ? count($student_leaves) : 0;
                            $slvs = []; 
                            if($student_leaves->isNotEmpty()) {
                                foreach($student_leaves as $slv) { 
                                    if(!empty($slv->leave_end_date) && strtotime($slv->leave_end_date)>0) {
                                        $dates = CommonController::getDatesFromRange($slv->leave_date, $slv->leave_end_date, 'Y-m-d', $month, $year);  
                                        $slvs = array_merge($slvs, $dates);
                                    }   else { 
                                        $slvs[] = $slv->leave_date;
                                    }
                                }
                            } 

                            if(count($slvs)>0) {
                                return response()->json(['status' => 1, 'message' => 'Leaves list', 'data' => $slvs]);
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'No Leaves']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    } 

    /*  Leaves list Details  
    Fn Name: getUnApprovedLeaves
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getUnApprovedLeaves(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $monthyr = ((isset($input) && isset($input['monthyr']))) ? $input['monthyr'] : ''; 
                /*if(empty($monthyr)) {
                    $monthyr = date('Y-m');  
                }*/
                
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0; 
                $type = ((isset($input) && isset($input['type']))) ? $input['type'] : 2; 
                if(empty($type)) {
                    $type = 2; // 1 - approved , 2 - pending list
                }
                $api_token = $request->header('x-api-key'); 
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 

                            $student_leaves = DB::table('leaves')->where('student_id', $userid)
                                ->where('class_id', $user_details->class_id); 
                            if($type == 1) {
                                $student_leaves->where('status', 'APPROVED');
                            } else {
                                $student_leaves->whereNotIn('status', ['APPROVED', 'CANCELLED']);
                            }
                                
                            $student_leaves->where('section_id', $user_details->section_id);
                            if(!empty($monthyr)) {
                                list($year, $month) = explode('-', $monthyr);
                                $student_leaves->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))');
                            }
                            $student_leaves = $student_leaves->select('leaves.*', DB::RAW('DATE_FORMAT(leave_date, "%d-%M-%Y") as leave_date_format'), DB::RAW('DATE_FORMAT(leave_end_date, "%d-%M-%Y") as leave_enddate_format')) 
                                ->get();
 
                            if($student_leaves->isNotEmpty()) { 
                                return response()->json(['status' => 1, 'message' => 'Leaves list', 'data' => $student_leaves]);
                            }  
                            return response()->json(['status' => 0, 'message' => 'No Leaves']); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }
    
    /*  Leave cancel
    Fn Name: postCancelLeave
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function postCancelLeave(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'leave_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $leave_id = ((isset($input) && isset($input['leave_id']))) ? $input['leave_id'] : 0;  
                $api_token = $request->header('x-api-key');  

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 

                            $student_leaves = DB::table('leaves')->where('student_id', $userid)
                                ->where('class_id', $user_details->class_id)
                                ->where('id', $leave_id)
                                ->where('status', '!=', 'APPROVED')
                                ->where('section_id', $user_details->section_id)  
                                ->get(); 
                            if($student_leaves->isNotEmpty()) {
                                foreach($student_leaves as $slv) {  
                                    DB::table('leaves')->where('student_id', $userid)->where('id', $leave_id)
                                        ->update(['status'=>'CANCELLED', 'updated_by'=>$userid, 
                                        'updated_at' => date('Y-m-d H:i:s')]);
                                    return response()->json(['status' => 1, 'message' => 'Leave Cancelled']);
                                }
                            } 
                            return response()->json(['status' => 0, 'message' => 'No Leaves']); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }



    /*  calendar list Details  
    Fn Name: getCalendar
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getCalendar(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $monthyr = ((isset($input) && isset($input['monthyr']))) ? $input['monthyr'] : date('Y-m'); 
                if(empty($monthyr)) {
                    $monthyr = date('Y-m');  
                }
                list($year, $month) = explode('-', $monthyr);
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0; 
                $api_token = $request->header('x-api-key'); 
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                        if(!empty($user_details)) { 

                            $circulars_qry = Events::where('status', 'ACTIVE')->where('approve_status', 'APPROVED')
                                ->whereRaw('FIND_IN_SET('.$user_details->class_id.', class_ids)')
                                ->whereRAW('YEAR(circular_date) = "'.$year.'" ')
                                ->whereRAW('MONTH(circular_date) = "'.$month.'" '); 

                            $circulars = $circulars_qry->orderby('circular_date', 'desc')
                                ->skip($page_no)->take($limit)->get();

                            if($circulars->isNotEmpty()) {
                                return response()->json(['status' => 1, 'message' => 'Events list', 'data' => $circulars]);
                            }   else {
                                return response()->json(['status' => 0, 'message' => 'No Events']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    /* Student apply Leave Details */
    public function postApplyLeave(Request $request)   { 
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $input = $request->all(); 

            $requiredParams = ['user_id', 'api_token', 'leave_reason', 'leave_date', 'leave_type' ]; //,   'leave_end_date', 'leave_attachment'

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) { 

                $leave_type = isset($input['leave_type']) ? $input['leave_type'] : 'FULL DAY';   
                // 'HALF MORNING','HALF AFTERNOON','FULL DAY','MORE THAN ONE DAY'

                $leave_reason = (isset($input['leave_reason'])) ? $input['leave_reason'] : '';

                $leave_date = (isset($input['leave_date'])) ? $input['leave_date'] : date('Y-m-d');
                if(empty($leave_date)) {
                    $leave_date = date('Y-m-d');
                } 

                if($leave_date == date('Y-m-d')) {
                    if(strtotime(date('H:i:s')) >= strtotime(date('17:00:00'))) {
                        return response()->json([ 'status' => 0, 'data' => null, 'message' => 'Cannot apply leave after 5 PM']);
                    }
                }

                $leave_end_date = (isset($input['leave_end_date'])) ? $input['leave_end_date'] : '';  

                $userid = $input['user_id'];   

                $api_token = $request->header('x-api-key');

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {  
                    
                    // echo $leave_date;
                    
                    list($year, $month, $date) = explode('-', $leave_date);
                    $sundays = CommonController::getSundays($year, $month); 
                    $saturdays = CommonController::getSaturdays($year, $month); 
                   $holidays = DB::table('holidays')->whereRAW('holiday_date = "'.$leave_date.'" ')->get();
                   $day = $date * 1;
                   $new_leave_date = $year.'-'.$month.'-'.$day;
                         if($holidays->isEmpty()){
                               if(!in_array($new_leave_date,$saturdays)){
                                if(!in_array($new_leave_date,$sundays)){

                    $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

                    if(!empty($user_details)) { 
                        $class_id = $user_details->class_id;
                        $section_id = $user_details->section_id;
                        $student_id = $userid; 

                        $ex = DB::table('leaves')->where('class_id', $class_id)->where('section_id', $section_id)
                            ->where('student_id', $student_id)->whereNotIn('status', ['CANCELLED', 'REJECTED'])
                            ->where('leave_date', $new_leave_date)->get();
                        if($ex->isNotEmpty()) {
                            return response()->json(['status' => 0, 'message' => 'Already leave applied for this date']);
                        }

                        $leave_starttime = '08 AM';
                        $leave_endtime = '04 PM';
                        if($leave_type == 'HALF MORNING') {
                            $leave_starttime = '08 AM';
                            $leave_endtime = '12.45 PM';
                        }   else if($leave_type == 'HALF AFTERNOON') {
                            $leave_starttime = '12.45 PM';
                            $leave_endtime = '04 PM';
                        } 

                        $leave_attachment = '';
                        $image = $request->file('leave_attachment');
                        if (!empty($image)) {
                            $ext = $image->getClientOriginalExtension();
                            $accepted_formats = ['mp3','mp4'];
                            if(!in_array($ext, $accepted_formats)) {
                                return response()->json(['status' => 0, 'message' => 'File format wrong. Please upload MP3,MP4 Files Only']);
                            }
                            
                            $leaves = rand() . time() . '.' . $image->getClientOriginalExtension();

                            $destinationPath = public_path('/image/leaves');

                            $image->move($destinationPath, $leaves);

                            $leave_attachment = $leaves;

                        }

                     

                       $id = DB::table('leaves')->insertGetId([
                            'student_id' => $student_id,
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'leave_date' => $new_leave_date,
                            'leave_end_date' => $leave_end_date,
                            'leave_starttime' => $leave_starttime,
                            'leave_endtime' => $leave_endtime,
                            'leave_type' => $leave_type,
                            'leave_reason' => $leave_reason,
                            'leave_attachment'  => $leave_attachment,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_by' => $userid,
                            'updated_by' => $userid
                        ]);
                    }
                    
                    $leave_details = Leaves::where('id',$id)->first();

                    if (!empty($leave_details)) { 
                        return response()->json(['status' => 1, 'data' => $leave_details, 'message' => 'Leave applied successfully']);
                    } else {
                        return response()->json(['status' => 0, 'message' => 'Something went to wrong']);
                    }
                }
                else{
                    return response()->json(['status' => 0, 'message' => 'The Day is Sunday..!']);
                }
                }
                else{
                    return response()->json(['status' => 0, 'message' => 'The Day is Saturday..!']);
                }
                }else{
                    return response()->json(['status' => 0, 'message' => 'The Day is Holiday..!']);
                }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } 

    }
    
    /*  Uset test Details  
    Fn Name: getUserDetails
    Input: user_id   
    return: Success Message saved / Failure Message
    */
    public function getUserDetails(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user = CommonController::getUserDetails($userid); 
                        return response()->json(['status' => 1, 'message' => 'User Details', 'data' => $user]);  

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    /* Update Student Details */
    public function postUpdateProfile(Request $request)   { 
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            //$input = $request->all(); 

            // $requiredParams = ['user_id', 'api_token', 'name', 'email', 'register_type' ]; //,   'profile_image', 'country_id'
            $requiredParams = ['user_id', 'api_token', 'name' ];
            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) { 

                $register_type = isset($input['register_type']) ? $input['register_type'] : 1;   
                // 1 > student, 2 > player

                $name = (isset($input['name'])) ? $input['name'] : '';

                $lastname = (isset($input['lastname'])) ? $input['lastname'] : ''; 

                $email = (isset($input['email'])) ? $input['email'] : ''; 

                $mobile = (isset($input['mobile'])) ? $input['mobile'] : ''; 

                $mobile1 = (isset($input['mobile1'])) ? $input['mobile1'] : '';

                $state_id = (isset($input['state_id'])) ? $input['state_id'] : 0;

                $city_id = (isset($input['city_id'])) ? $input['city_id'] : 0;

                $userid = $input['user_id'];  

                // Mobile number must not be start with 0
                if((!empty($mobile)) && substr( $mobile, 0, 1 ) === "0") {
                    return response()->json(['status' => 0, 'message' => 'Invalid mobile']);
                }

                if((!empty($mobile)) && (strlen($mobile)<8) || (strlen($mobile)>10)) {
                    return response()->json(['status' => 0, 'message' => 'Invalid mobile']);
                }

                if((!empty($mobile1)) && substr( $mobile1, 0, 1 ) === "0") {
                    return response()->json(['status' => 0, 'message' => 'Invalid alternative mobile']);
                }

                if((!empty($mobile1)) && (strlen($mobile1)<8) || (strlen($mobile1)>10)) {
                    return response()->json(['status' => 0, 'message' => 'Invalid alternative mobile']);
                }

                if((strlen($name)<2)) {
                    return response()->json(['status' => 0, 'message' => 'Invalid name. Please enter more than 2 Characters']);
                }                

                $api_token = $request->header('x-api-key');

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    $emailEx = DB::table('users')->where('email', $email)->where('id', '!=', $userid)->first(); //
                    if(!empty($emailEx)) {  // registered user 
                        return response()->json(['status' => 0, 'message' => 'Email already exists']); 
                    }
                    
                    $user = User::find($userid);
                    
                 
                    if($state_id > 0) { } else { $state_id = $user->state_id; }
                    if($city_id > 0) { } else { $city_id = $user->city_id; }
                    if($email != '') { } else { $email = $user->email; }

                 
                    $exMobile = $user->mobile;  
                    $exEmail = $user->email; 

                    $user->name = $name;
                    $user->last_name = $lastname;
                    $user->email   = $email;
                    $country_code = '91';
                    $country = Countries::where('status', 'ACTIVE')->where('phonecode', $country_code)->value('id');

                    $user->country = $country;
                    $user->country_code = $country_code;
                    if(!empty($mobile)) {
                        $user->mobile = $mobile; 
                        $user->code_mobile = $country_code.$mobile;
                    }
                    if(!empty($mobile1)) {
                        $user->mobile1 = $mobile1; 
                        $user->codemobile1 = $country_code.$mobile1;
                    }
                    $user->state_id = $state_id; 
                    $user->city_id = $city_id; 
                    // $user->school_id = $school_id; 
                    // $user->class_id = $class_id; 
                    // $user->academy_id =$academy_id;
                    $user->updated_at = date('Y-m-d H:i:s');   
                    $user->save();   

                    if ($exEmail != $email) {
                        // CommonController::SendOTPEmail($user->id, $email);   
                        $user = CommonController::getUserDetails($user->id); 
                        return response()->json(['status' => 6, 'data' => $user, 'message' => 'Email verification is pending.']);
                    } 

                    $user = CommonController::getUserDetails($user->id);
                    if (!empty($user)) { 
                        return response()->json(['status' => 1, 'data' => $user, 'message' => 'Profile details has been updated']);
                    } else {
                        return response()->json(['status' => 0, 'message' => 'Something went to wrong']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } 

    }

    /* Update users Profile Image */
    public function postUpdateProfileImage(Request $request)   {

        try {   
            $input = $request->all();

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {
                $userid = $input['user_id'];
                $api_token = $request->header('x-api-key');
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    $user = User::find($userid);
                    $reg_no = $user->reg_no;
                    /*  Profile image of the User */
                    $accepted_formats = ['jpeg', 'jpg', 'png'];
                    $image = $request->file('profile_image');
                    if (!empty($image) && $image != 'null') {
                        $ext = $image->getClientOriginalExtension();
                        if(!in_array($ext, $accepted_formats)) {
                            return response()->json(['status' => 0, 'message' => 'File format wrong. Please upload PNG,JPEG,JPG']);
                        }
          
                        $spdocsImage = $reg_no.'_'.rand().time() . '.' . $image->getClientOriginalExtension();

                        $destinationPath = public_path('/uploads/userdocs');

                        $image->move($destinationPath, $spdocsImage);

                        $user->profile_image =  $spdocsImage;   

                        $user->save(); 
                    }

                    $user = CommonController::getUserDetails($userid);
                    if(!empty($user)) {
                        return response()->json(['status' => 1, 'message' => 'User image updated', 'data' => $user]);
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid user']);
                    }
                    
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }


    /* Update users Profile Image */
    public function postDeleteProfileImage(Request $request)   {

        try {   
            $input = $request->all();

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {
                $userid = $input['user_id'];
                $api_token = $request->header('x-api-key');
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    $user = User::find($userid);
                    $user->profile_image =  '';   
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save(); 

                    $user = CommonController::getUserDetails($userid);
                    if(!empty($user)) {
                        return response()->json(['status' => 1, 'message' => 'User image removed', 'data' => $user]);
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid user']);
                    }
                    
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    /* Update users Profile Image */
    public function postDeleteUserAccount(Request $request)   {

        try {   
            $input = $request->all();

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {
                $userid = $input['user_id'];
                $api_token = $request->header('x-api-key');
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    $user = User::find($userid);
                    $user->status =  'DELETED';   
                    $user->is_otp_verified = 0;
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save(); 

                    $user = CommonController::getUserDetails($userid);
                    if(!empty($user)) {
                        return response()->json(['status' => 1, 'message' => 'User account deleted', 'data' => $user]);
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid user']);
                    }
                    
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Get Chapter List
    public function getChapters(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id','subject_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $subjectid = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $termid = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
               
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  

                        $chapter_qry = Chapters::where('status', 'ACTIVE')->where('subject_id',$subjectid);
                        if(!empty($search)) {
                            $chapter_qry->where('chaptername ', 'like', '%'.$search.'%');
                        }
                        if($termid>0) {
                            $chapter_qry->where('term_id',$termid);
                        }

                        $chapters = $chapter_qry->orderby('chaptername', 'asc')->get();

                        if($chapters->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Chapter list', 'data' => $chapters]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Chapter']);
                        }
                   
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

     //Get ChpaterTopics List
     public function getChaptersTopics(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id','chapter_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $chapterid = ((isset($input) && isset($input['chapter_id']))) ? $input['chapter_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
               
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  

                        $chaptertopic_qry = ChapterTopics::where('status', 'ACTIVE')->where('chapter_id',$chapterid);
                        if(!empty($search)) {
                            $chaptertopic_qry->where('chapter_topic_name ', 'like', '%'.$search.'%');
                        }

                        $chapterstopics = $chaptertopic_qry->orderby('chapter_topic_name', 'asc')->get();

                        if($chapterstopics->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'ChapterTopics list', 'data' => $chapterstopics]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No ChapterTopics']);
                        }
                   
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    //Subject Book List Api
    public function getSubjectBookList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user_details = DB::table('students')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  
                        $subjects = '';
                        if(!empty($user_details)) {
                            Subjects::$class_id = $user_details->class_id;
                            $mapped_subjects = $user_details->mapped_subjects;
                            if(!empty($mapped_subjects)) {
                                $mapped_subjects = explode(',', $mapped_subjects);
                                $mapped_subjects = array_unique($mapped_subjects);
                                if(count($mapped_subjects)>0) {
                                    $subjects = Subjects::with('books')->whereIn('id', $mapped_subjects)->orderby('position', 'asc')->get();

                                    if($subjects->isNotEmpty()) {
                                        return response()->json(['status' => 1, 'message' => 'Subjects Details', 'data' => $subjects]);  
                                    }
                                }
                            } 
                        } 


                        return response()->json(['status' => 0, 'message' => 'Subjects Details', 'data' => []]);  

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
 
    }

    //Get Topics List
    public function getTopics(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id','subject_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $subjectid = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $chapterid = ((isset($input) && isset($input['chapter_id']))) ? $input['chapter_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
               
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  

                        $topic_qry = Topics::where('status', 'ACTIVE')->where('subject_id',$subjectid);
                        if(!empty($search)) {
                            $topic_qry->where('topic_title ', 'like', '%'.$search.'%');
                        }
                        if(!empty($chapterid)) {
                            $topic_qry->where('chapter_id', $chapterid);
                        }

                        $topics = $topic_qry->orderby('topic_title', 'asc')->get();

                        if($topics->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Topics list', 'data' => $topics]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Topics']);
                        }
                   
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    //Get Topics View List
    public function getTopicView(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id','topic_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $topicid = ((isset($input) && isset($input['topic_id']))) ? $input['topic_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
               
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  

                        $topic_qry = Topics::where('status', 'ACTIVE')->where('topic_id',$topicid);
                        if(!empty($search)) {
                            $topic_qry->where('topic_title ', 'like', '%'.$search.'%');
                        }
                
                        $topics = $topic_qry->orderby('topic_title', 'asc')->get();

                        if($topics->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Topics list', 'data' => $topics]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Topics']);
                        }
                   
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    public function getHomework(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id','hw_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $hwid = ((isset($input) && isset($input['hw_id']))) ? $input['hw_id'] : 0; 
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
               
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    //if($userid > 0) {  

                        $hw_qry = Homeworks::where('status', 'ACTIVE')->where('id',$hwid);
                        if(!empty($search)) {
                            $hw_qry->where('hw_title ', 'like', '%'.$search.'%');
                        }
                
                        $homework = $hw_qry->orderby('hw_title', 'asc')->get();

                        if($homework->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Home Work list', 'data' => $homework]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Home Work']);
                        }
                   
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    //Subject List Api
    public function getSubjectList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user_details = DB::table('students')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  
                        $subjects = '';
                        if(!empty($user_details)) {
                            $mapped_subjects = $user_details->mapped_subjects;
                            if(!empty($mapped_subjects)) {
                                $mapped_subjects = explode(',', $mapped_subjects);
                                $mapped_subjects = array_unique($mapped_subjects);
                                if(count($mapped_subjects)>0) {
                                    $subjects = DB::table('subjects')->whereIn('id', $mapped_subjects)
                                        ->where('status', 'ACTIVE')
                                        ->orderby('position', 'asc')->get();

                                    if($subjects->isNotEmpty()) {
                                        return response()->json(['status' => 1, 'message' => 'Subjects Details', 'data' => $subjects]); 
                                    }
                                }
                            } 
                        } 
                        return response()->json(['status' => 0, 'message' => 'Subjects Details', 'data' => []]);  

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
 
    }

    //Book List Api
    public function getBookList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $subjectid = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $chapterid = ((isset($input) && isset($input['chapter_id']))) ? $input['chapter_id'] : 0;  
                $topicid = ((isset($input) && isset($input['topic_id']))) ? $input['topic_id'] : 0;  
                $termid = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user_details = DB::table('students')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  
                        $subjects = '';
                        if(!empty($user_details)) {
                            $class_id = $user_details->class_id;
                            $section_id = $user_details->section_id;
                            $mapped_subjects = $user_details->mapped_subjects;
                            if(!empty($mapped_subjects)) {
                                $mapped_subjects = explode(',', $mapped_subjects);
                                $mapped_subjects = array_unique($mapped_subjects); 
                            } 
                        } 

                        $books_qry = Topics::where('status', 'ACTIVE')->where('subject_id', $subjectid);
                        if(!empty($class_id)) {
                            $books_qry->where('class_id', $class_id);
                        }
                        if(!empty($chapterid)) {
                            $books_qry->where('chapter_id', $chapterid);
                        }
                        if(!empty($topicid)) {
                            $books_qry->where('topic_id', $topicid);
                        }
                        if(!empty($termid)) {
                            $books_qry->where('term_id', $termid);
                        }

                        $bookslist = $books_qry->orderby('position', 'asc')->get();

                        if($bookslist->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Books list', 'data' => $bookslist]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Books Work']);
                        }

                        return response()->json(['status' => 1, 'message' => 'Subjects Details', 'data' => $subjects]);  

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

      //Books Api
    public function getBook(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id','subject_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $subjectid = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $topicid = ((isset($input) && isset($input['topic_id']))) ? $input['topic_id'] : 0;  
                $termid = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
               
                $status = 1;   $message = '';
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    $user_details = DB::table('students')
                    ->leftjoin('sections', 'sections.id', 'students.section_id')
                    ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                    ->where('students.user_id', $userid)->first();  
               
                if(!empty($user_details)) {
                    $class_id = $user_details->class_id; 
                } 

                       
                        // $books_qry = Subjects::with('topics')->where('status', 'ACTIVE')->where('id', $subjectid);
                        $books_qry = ChapterTopics::where('status', 'ACTIVE')->where('subject_id', $subjectid)->where('class_id',$class_id);
                        if($termid > 0) {
                            $books_qry->where('term_id', $termid);
                        }
                        // if(!empty($topicid)) {

                        //     $books_qry = ChapterTopics::query()
                        //     ->with(['topics' => function ($query) use($topicid) {
                        //         $query->where('id', $topicid);
                        //     }])->where('id', $subjectid);
                        // }

                        $bookslist = $books_qry->orderby('position', 'asc')->get();

                        if($bookslist->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Books list', 'data' => $bookslist]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Books List']);
                        }
                   
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  

    }

    //Test List Api
    public function getTestList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0; 
                $date = ((isset($input) && isset($input['date']))) ? $input['date'] : date('Y-m-d');  
                $is_self_test = ((isset($input) && isset($input['is_self_test']))) ? $input['is_self_test'] : 0;
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user_details = DB::table('students')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  
                       
                        if(!empty($user_details)) {
                            $class_id = $user_details->class_id; 
                        } 
                        TestItems::$student_id = $userid;
                        TestItems::$random = 1;
                        $testqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                            ->leftjoin('classes', 'classes.id', 'tests.class_id')
                            ->leftjoin('subjects', 'subjects.id', 'tests.subject_id') 
                            ->where('tests.status', 'ACTIVE')
                            ->select('tests.*', 'classes.class_name', 'subjects.subject_name', 
                                'terms.term_name');
                        if($is_self_test == 0){
                            $testqry->where('tests.is_self_test', 0);
                        } else  if($is_self_test == 1){
                            $testqry->where('tests.created_by', $userid);
                        } else {
                            // all
                        } 
                    
                        if(!empty($date)){
                            $testqry->whereRaw("'".$date."' BETWEEN from_date and to_date");
                            //$testqry->whereDate('tests.from_date', $date);
                        }

                        if($class_id>0) {
                            $testqry->where('tests.class_id', $class_id);
                        } 
                        if($subject_id>0) {
                            $testqry->where('tests.subject_id', $subject_id);
                        } 
                        if($term_id>0) {
                            $testqry->where('tests.term_id', $term_id);
                        } 

                        $testslist = $testqry->orderby('tests.id', 'desc')->get();

                        if($testslist->isNotEmpty()) {

                            foreach($testslist as $sk => $st) {
                                $exists = DB::table('student_tests')->where('user_id', $userid)
                                    ->where('test_id', $st->id)->first();
                                if(!empty($exists)) {
                                    $testslist[$sk]->is_attended = 1;
                                }   else {
                                    $testslist[$sk]->is_attended = 0;
                                }

                                $excount = DB::table('student_tests')->where('user_id', $userid)
                                    ->where('test_id', $st->id)->select('id')->get();
                                if($excount->isNotEmpty()) {
                                    $testslist[$sk]->times_attended = count($excount);
                                }   else {
                                    $testslist[$sk]->times_attended = 0;
                                } 
                            } 

                            return response()->json(['status' => 1, 'message' => 'Tests list', 'data' => $testslist]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Tests']);
                        } 

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Test List Api
    public function getTestDetails(Request $request)     {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $test_id = ((isset($input) && isset($input['test_id']))) ? $input['test_id'] : 0;  
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {   
                        TestItems::$random = 1;
                        $testqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                            ->leftjoin('classes', 'classes.id', 'tests.class_id')
                            ->leftjoin('subjects', 'subjects.id', 'tests.subject_id') 
                            ->where('tests.status', 'ACTIVE')
                            ->where('tests.id', $test_id)
                            ->select('tests.*', 'classes.class_name', 'subjects.subject_name', 
                                'terms.term_name'); 

                        $testdetails = $testqry->orderby('tests.id', 'desc')->get();
                        //echo "<pre>"; print_r($testdetails->toArray()); exit;
                        if($testdetails->isNotEmpty()) {
                            //$encoded = json_encode( utf8ize( $responseForJS ) );
                            /*$data = ['status' => 1, 'message' => 'Tests list', 'data' => $testdetails[0]];
                            json_encode($data, JSON_INVALID_UTF8_IGNORE);, JSON_UNESCAPED_UNICODE*/
                            return response()->json(['status' => 1, 'message' => 'Tests list', 'data' => $testdetails[0]]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Tests']);
                        } 

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        /*}   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }*/  
    }

    public function submitTestDetails(Request $request)     {
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'test_id', 'test_answers', 'duration'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $test_id = ((isset($input) && isset($input['test_id']))) ? $input['test_id'] : 0;
                $test_answers = ((isset($input) && isset($input['test_answers']))) ? $input['test_answers'] : '';
                $duration = ((isset($input) && isset($input['duration']))) ? $input['duration'] : '';
              $api_token = $request->header('x-api-key');
              $mes = User::checkTokenExpiry($userid, $api_token);
              $status = $mes['status'];

              $message = $mes['message'];

                if($status != 1) {

                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    if($userid > 0 && $test_id > 0) {

                        $term_id = $class_id = $subject_id = 0;

                        $testdetails = DB::table('tests')->where('id', $test_id)->first();
                        if(!empty($testdetails)) {
                            $term_id = $testdetails->term_id;
                            $class_id = $testdetails->class_id;
                            $subject_id = $testdetails->subject_id;


                        }

                        $no_of_questions = DB::table('test_items')->where('test_id', $test_id)->count('id');

                         $testmark = DB::table('test_items')->where('test_id', $test_id)->sum('mark');

                       $data = ["user_id"=>$userid, "term_id"=>$term_id, "class_id"=>$class_id, "subject_id"=>$subject_id,"test_id"=>$test_id, "test_date"=>date('Y-m-d H:i:s'), "duration" =>$duration ,"test_mark" => $testmark,"total_questions" => $no_of_questions];


                //         $ex = DB::table("student_tests")->where('user_id', $userid)->where('test_id', $test_id)->first();
                //    if(!empty($ex)) {
                //             $data['updated_by'] = $userid;
                //             $data['updated_at'] = date('Y-m-d H:i:s');

                //      DB::table("student_tests")->where('user_id', $userid)->where('test_id', $test_id)->update($data);

                //             $student_test_id = $ex->id;

                //         }   else {
                            $data['created_by'] = $userid;
                            $data['created_at'] = date('Y-m-d H:i:s');

                            $student_test_id = DB::table("student_tests")->insertGetId($data);


                        // }

                        if(is_array($test_answers) && count($test_answers)>0) {

                            foreach($test_answers as $ans) {

                                
                                $get_answer = DB::table("question_bank_items")->where('id', $ans['question_bank_item_id'])->first();
                                $test_answers = $get_answer->answer;
                                $question_type_id = $get_answer->question_type_id;

                                $ans_answer = $ans['answer'];

                                $test_answers = trim(strtolower($test_answers));
                                $ans['answer'] = trim(strtolower($ans['answer']));

                                // faster solution
                                $test_answers = str_replace("\xc2\xa0", ' ', $test_answers);

                                // more flexible solution
                                $test_answers = preg_replace('/\xc2\xa0/', ' ', $test_answers);

                                $act_ans = str_replace(' ','',$test_answers);
                                $reg_ans = str_replace(' ','',$ans['answer']);
                                $test_ans = trim(strtolower($act_ans));
                                $new_ans = trim(strtolower($reg_ans));

                                $test_answers = trim($test_answers);
                                $short_ans1= strtolower($test_answers);
                                $short_ans2 = strtolower($ans['answer']);
                                  
                                if($question_type_id != 17){
                                    if($test_ans == $new_ans){
                                   
                                    //   if($test_answers == $ans['answer']){
                                        $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                            ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                        if(!empty($get_mark)){
                                            $test_mark = $get_mark->mark;
                                        }
                                    }   else {
                                        $test_mark = '0.00';
                                    }

                                }
                                if($question_type_id == 17){
                                    $re = '/\s+/m'; 
                                    $subst = " ";

                                    $test_ans = preg_replace($re, $subst, $test_ans);
                                    $new_ans = preg_replace($re, $subst, $new_ans);

                                    $a = explode(',', $test_ans);
                                    $b = explode(',', $new_ans);
                                    $total_per = count($a);
                                    $test_mark = 0;                         
                                    if (count( $a ) == count( $b ) && !array_diff( $a, $b )) { //echo "p1";
                                        $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                            ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                        if(!empty($get_mark)){
                                            $test_mark = $get_mark->mark;
                                        }
                                    }   else {//echo "p2"; echo "<pre>"; print_r($a);  print_r($b); 
                                        $diff = count(array_diff($a, $b));
                                        $mat = count(array_intersect($a, $b));

                                        $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                            ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                        $total = 0;
                                        if(!empty($get_mark)){
                                            $total = $get_mark->mark;
                                        }

                                        $remain_count = $total_per - $diff;
                                        $remain_count = $mat;
                                     
                                        $test_mark1 = ($remain_count /  $total_per) * $total;
                                        $test_mark = round($test_mark1);

                                    }
                                 
                                }

                                if($question_type_id == 14 || $question_type_id == 15){

                                    $short_ans1 = str_replace(',','',$short_ans1);
                                    $short_ans2 = str_replace(',','',$short_ans2);

                                    $re = '/\s+/m'; 
                                    $subst = " ";

                                    $short_ans1 = preg_replace($re, $subst, $short_ans1);
                                    $short_ans2 = preg_replace($re, $subst, $short_ans2);

                                    $a = explode(' ', $short_ans1);
                                    $b = explode(' ', $short_ans2);
                                    
                                    $total_per = count($a);
                                    $test_mark = 0;                         
                                    if (count( $a ) == count( $b ) && !array_diff( $a, $b )) {
                                          $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                          ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                          if(!empty($get_mark)){
                                           $test_mark = $get_mark->mark;
                                          }
                                    } else{
                                      $diff = count(array_diff($a, $b));
                                      $mat = count(array_intersect($a, $b));

                                      $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                      ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                      $total = 0;
                                      if(!empty($get_mark)){
                                       $total = $get_mark->mark;
                                      }

                                       $remain_count = $total_per - $diff;
                                       $remain_count = $mat;

                                       $test_mark2 = ($remain_count /  $total_per) * $total;
                                       $test_mark = round($test_mark2);

                                    }
                               
                                }

                                if($question_type_id == 18){

                                    $short_ans1 = str_replace(',','',$short_ans1);
                                    $short_ans2 = str_replace(',','',$short_ans2);

                                    $re = '/\s+/m'; 
                                    $subst = " ";

                                    $short_ans1 = preg_replace($re, $subst, $short_ans1);
                                    $short_ans2 = preg_replace($re, $subst, $short_ans2);

                                    $a = explode(' ', $short_ans1);
                                    $b = explode(' ', $short_ans2);
                                    
                                    $total_per = count($a);
                                    $test_mark = 0;   
                                    if (count( $a ) == count( $b ) &&  ($a == $b)) {  // echo "pp";
                                          $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                          ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                          if(!empty($get_mark)){
                                           $test_mark = $get_mark->mark;
                                          }  
                                    } else{ //  echo "qq"; echo "<pre>"; print_r($a); print_r($b);

                                        $j = 0;  
                                        if($a == $b) { } else { 
                                          for($i=0; $i<count($b); $i++) { 
                                            if($a[$i] == $b[$i]) {
                                                $j = $j+1;
                                            } else {
                                                break; 
                                            }
                                          }
                                        } 

                                        if($j > 0) {
                                            $crct_oount = count($a)-$j;
                                        }   else {
                                            $crct_oount = 0;
                                        } 

                                        $get_mark = DB::table("test_items")->where('test_id', $test_id)
                                            ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();
                                        $total = 0;
                                        if(!empty($get_mark)){
                                            $total = $get_mark->mark;
                                        } 
                                       // echo $j ."/".  $total_per ." * ". $total;
                                       $test_mark2 = ($j /  $total_per) * $total;
                                       $test_mark = round($test_mark2,2);
                                        
                                    }
                                   
                                }
                            

                                $ex = DB::table("student_test_answers")->where('student_id', $userid)
                                    ->where('student_test_id', $student_test_id)
                                    ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();

                                $answer = ["student_id"=>$userid, "student_test_id"=>$student_test_id,
                                    "question_bank_item_id"=>$ans['question_bank_item_id'],
                                    "answer"=>$ans_answer,'mark'=>$test_mark];

                                //echo "<pre>"; print_r($answer);

                                if(!empty($ex)) {
                                    $answer['updated_by'] = $userid;
                                    $answer['updated_at'] = date('Y-m-d H:i:s');

                                    DB::table("student_test_answers")->where('student_id', $userid)
                                        ->where('student_test_id', $student_test_id)
                                        ->where('question_bank_item_id', $ans['question_bank_item_id'])
                                        ->update($answer);
                                }   else {
                                    $answer['created_by'] = $userid;
                                    $answer['created_at'] = date('Y-m-d H:i:s');

                                    DB::table("student_test_answers")->insert($answer);
                                }
                            }


                            $student_mark = DB::table('student_test_answers')->where('student_test_id', $student_test_id)->sum('mark');

                            $student_attempt = DB::table('student_test_answers')->where('student_test_id', $student_test_id)->count('id');

                            $data = ["student_mark" => $student_mark,"attempeted_question" => $student_attempt];

                            $update_mark = DB::table('student_tests')->where('id','=',$student_test_id)->update( $data);


                        }

                        if($student_test_id) {
                            return response()->json(['status' => 1, 'message' => 'Test submitted successfully']);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Unable to update']);
                        }

                    }
                    else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }
             else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }
    }


    // Submit Test details of the user Api
    public function submitTestDetailsold(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'test_id', 'test_answers', 'duration'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $test_id = ((isset($input) && isset($input['test_id']))) ? $input['test_id'] : 0;  
                $test_answers = ((isset($input) && isset($input['test_answers']))) ? $input['test_answers'] : '';  
                $duration = ((isset($input) && isset($input['duration']))) ? $input['duration'] : '';  
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $test_id > 0) {   
                        
                        $term_id = $class_id = $subject_id = 0;

                        $testdetails = DB::table('tests')->where('id', $test_id)->first();
                        if(!empty($testdetails)) {
                            $term_id = $testdetails->term_id;
                            $class_id = $testdetails->class_id;
                            $subject_id = $testdetails->subject_id;
                        } 
       
                        $data = ["user_id"=>$userid, "term_id"=>$term_id, "class_id"=>$class_id, "subject_id"=>$subject_id, 
                        "test_id"=>$test_id, "test_date"=>date('Y-m-d H:i:s'), "duration" =>$duration ];

                        $ex = DB::table("student_tests")->where('user_id', $userid)->where('test_id', $test_id)->first();
                        if(!empty($ex)) {
                            $data['updated_by'] = $userid;
                            $data['updated_at'] = date('Y-m-d H:i:s');

                            DB::table("student_tests")->where('user_id', $userid)->where('test_id', $test_id)->update($data);

                            $student_test_id = $ex->id;
                        }   else {
                            $data['created_by'] = $userid;
                            $data['created_at'] = date('Y-m-d H:i:s');

                            $student_test_id = DB::table("student_tests")->insertGetId($data);
                        }

                        if(is_array($test_answers) && count($test_answers)>0) {
                            foreach($test_answers as $ans) {
                                $ex = DB::table("student_test_answers")->where('student_id', $userid)
                                    ->where('student_test_id', $student_test_id)
                                    ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();

                                $answer = ["student_id"=>$userid, "student_test_id"=>$student_test_id, 
                                    "question_bank_item_id"=>$ans['question_bank_item_id'], 
                                    "answer"=>$ans['answer']];
                                if(!empty($ex)) {
                                    $answer['updated_by'] = $userid;
                                    $answer['updated_at'] = date('Y-m-d H:i:s');

                                    DB::table("student_test_answers")->where('student_id', $userid)
                                        ->where('student_test_id', $student_test_id)
                                        ->where('question_bank_item_id', $ans['question_bank_item_id'])
                                        ->update($answer);
                                }   else {
                                    $answer['created_by'] = $userid;
                                    $answer['created_at'] = date('Y-m-d H:i:s');

                                    DB::table("student_test_answers")->insert($answer);
                                }
                            }
                        }  

                        if($student_test_id) {
                            return response()->json(['status' => 1, 'message' => 'Test submitted successfully']);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Unable to update']);
                        } 

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }


    public function newStudentTestList(Request $request){
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $test_id = ((isset($input) && isset($input['test_id']))) ? $input['test_id'] : 0;
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;
                $is_self_test = ((isset($input) && isset($input['is_self_test']))) ? $input['is_self_test'] : 0;

                $api_token = $request->header('x-api-key');
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];

                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    if($userid > 0) {
                        $testqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                        ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                        ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                        ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                        ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                        ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                        ->where('student_tests.user_id', $userid)
                        ->where('tests.is_self_test',$is_self_test)
                        ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                            'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');

                        if($test_id > 0)   {
                            $testqry->where('student_tests.test_id', $test_id)->groupby('student_tests.test_id');
                        }

                        if($subject_id > 0)   {
                            $testqry->where('student_tests.subject_id', $subject_id)->groupby('student_tests.test_id');
                        }

                      $testdetails = $testqry->get();

                  if(count($testdetails) > 0){
                    foreach($testdetails as $k => $value){
                        $new_test_id = $value->test_id;

                        $get_student = DB::table('student_tests')->where('user_id', $userid)->where('test_id', $new_test_id)->select('id','total_questions','attempeted_question', 'student_mark', 'test_mark')->orderby('id', 'desc')->get();

                          $testdetails[$k]->pre_attempts = $get_student;
                    }

                  }
                   return response()->json(['status' => 1, 'message' => 'Tests list', 'data' => $testdetails]);


                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }

   }

    //Test list for students Api
    public function getStudentsTestList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $test_id = ((isset($input) && isset($input['test_id']))) ? $input['test_id'] : 0; 
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
  
                $api_token = $request->header('x-api-key'); 
              

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {   

                    if($test_id > 0) {
                        $testqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                            ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                            ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                            ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                            ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                            ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                            ->where('student_tests.user_id', $userid)
                            ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name', 
                                'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');  

                            //$testqry->where('student_tests.test_id', $test_id);
                            $testqry->where('student_tests.id', $test_id);
                        }   else {
                            $testqry = DB::table('student_tests')->leftjoin('users', 'users.id', 'student_tests.user_id')
                            ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                            ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                            ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                            ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                            ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                            ->where('student_tests.user_id', $userid)
                            ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name', 
                                'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name'); 
                        }

                        if($subject_id > 0) {
                            $testqry->where('student_tests.subject_id', $subject_id);
                        }

                        $testdetails = $testqry->orderby('student_tests.id', 'desc')->get();

                        if($testdetails->isNotEmpty()) {
                            if($test_id > 0) {
                                return response()->json(['status' => 1, 'message' => 'Tests list', 'data' => $testdetails[0]]);
                            }   else {
                                return response()->json(['status' => 1, 'message' => 'Tests list', 'data' => $testdetails]);
                            }
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Tests']);
                        } 

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Terms List Api
    public function getTermsList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user_details = DB::table('students')
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  
                        $subjects = '';
                        if(!empty($user_details)) {
                            $class_id = $user_details->class_id;
                            if($class_id > 0) {
                                $terms = Terms::whereRAW(' FIND_IN_SET('.$class_id.', class_ids) ')
                                    ->select("term_name", "id")->get();

                                if($terms->isNotEmpty()) {
                                    return response()->json(['status' => 1, 'message' => 'Terms list', 'data' => $terms]);
                                }   else {
                                    return response()->json(['status' => 0, 'message' => 'No Terms list']);
                                }
                            }   else {
                                return response()->json([ 'status' => 0,  'message' => 'Class details not updated']);
                            } 
                        }  

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Qbs List Api for User
    public function getUserQbList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;   

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 
               
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 

                    $user_details = DB::table('students')
                        ->leftjoin('sections', 'sections.id', 'students.section_id')
                        ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                        ->where('students.user_id', $userid)->first();  
                    $subjects = ''; $data = [];
                    if(!empty($user_details)) {
                        Subjects::$class_id = $user_details->class_id;
                        $mapped_subjects = $user_details->mapped_subjects;
                        if(!empty($mapped_subjects)) {
                            $mapped_subjects = explode(',', $mapped_subjects);
                            $mapped_subjects = array_unique($mapped_subjects);
                            if(count($mapped_subjects)>0) {
                                $subjects = Subjects::with('chapters')->whereIn('id', $mapped_subjects)->orderby('position', 'asc')->get();

                                $terms = Terms::whereRAW(' FIND_IN_SET('.$user_details->class_id.', class_ids) ')
                                    ->where('status', 'ACTIVE')
                                    ->select("term_name", "id")->get();

                                $data = ['subjects' => $subjects, 'terms' => $terms];

                                return response()->json(['status' => 1, 'message' => 'Chapters list', 'data' => $data]);
                            } else {
                                return response()->json(['status' => 0, 'message' => 'No Subjects']);
                            }
                        } else {
                            return response()->json(['status' => 0, 'message' => 'No Subjects']);
                        } 
                    }else {
                        return response()->json(['status' => 0, 'message' => 'Invalid inputs']);
                    }  
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Qbs List Api
    public function getQbList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'class_id', 'subject_id', 'term_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
                $chapter_id = ((isset($input) && isset($input['chapter_id']))) ? $input['chapter_id'] : 0;  

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 
               
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 

                    $user_details = DB::table('students')
                        ->select('students.class_id', 'students.section_id')
                        ->where('students.user_id', $userid)->first();   
                    if(!empty($user_details)) {
                        $class_id = $user_details->class_id;
                    }

                    //if($userid > 0 && $class_id > 0 && $subject_id > 0 && $term_id > 0) { DB::table('question_banks') 
                    if($userid > 0 && $class_id > 0) {  

                        $qbqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                            ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                            ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                            ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                            ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername', 
                                'terms.term_name');

                        $qbqry->where('question_banks.class_id', $class_id); 
                        if($subject_id > 0) {
                            $qbqry->where('question_banks.subject_id', $subject_id); 
                        }
                        if($term_id > 0) {
                            $qbqry->where('question_banks.term_id', $term_id);  
                        }
                        if($chapter_id > 0) {
                            $qbqry->where('question_banks.chapter_id', $chapter_id);  
                        } // ->skip($page_no)->take($limit)
                        $qblist = $qbqry->orderby('question_banks.id', 'Desc')->get(); 

                        if($qblist->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Qb list', 'data' => $qblist]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'No Qb list']);
                        } 

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Qbs Summary Api
    public function getQbSummary(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'qb_ids'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $qb_ids = ((isset($input) && isset($input['qb_ids']))) ? $input['qb_ids'] : '';
                if(empty($qb_ids) || !is_array($qb_ids)) {
                    return response()->json([ 'status' => 0, 'message' => 'Please select the Question Banks for test']);
                }

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && is_array($qb_ids) && count($qb_ids)>0) {  

                        $qb = DB::table('question_bank_items')->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
                            ->leftjoin('terms', 'terms.id', 'question_banks.term_id')
                            ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                            ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                            ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                            ->select('classes.class_name', 'subjects.subject_name', 'chapters.chaptername', 
                                'terms.term_name', DB::RAW('count(question_bank_items.id) as cnt'), 'question_type_id', 'question_type')
                            ->whereIn('question_banks.id', $qb_ids)
                            ->groupby('question_bank_items.question_type', 'question_bank_items.question_bank_id')
                            ->get();

                        if($qb->isNotEmpty()) {
                            $qb = $qb->toArray(); 
                            return response()->json([ 'status' => 1, 'message' => 'QB Summary', 'data' => $qb]);
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'No Questions available in these chapters']);
                        } 
                        
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //generate Self Test Api
    public function generateSelfTest(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'test_name', 'qb_test_summary', 'class_id', 'subject_id', 'term_id', 'chapter_ids'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
                $test_name = ((isset($input) && isset($input['test_name']))) ? $input['test_name'] : '';  
                $chapter_ids = ((isset($input) && isset($input['chapter_ids']))) ? $input['chapter_ids'] : [];  
                $qb_test_summary = ((isset($input) && isset($input['qb_test_summary']))) ? $input['qb_test_summary'] : '';
                if(empty($qb_test_summary) || !is_array($qb_test_summary)) {
                    return response()->json([ 'status' => 0, 'message' => 'Please select test format']);
                }

                $api_token = $request->header('x-api-key'); 
              
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if(!is_array($chapter_ids)) {
                        return response()->json([ 'status' => 0, 'message' => 'Please select the Chapters']);
                    }
                    if(count($chapter_ids) >0) {} else {
                        return response()->json([ 'status' => 0, 'message' => 'Please select the Chapters']);
                    }
                    if($userid > 0 && is_array($qb_test_summary) && count($qb_test_summary)>0) {  

                        $user_details = DB::table('students')
                            ->select('students.class_id', 'students.section_id', 'students.school_id')
                            ->where('students.user_id', $userid)->first();   
                        if(!empty($user_details)) {
                            $class_id = $user_details->class_id;
                            $school_id = $user_details->school_id;
                        }

                        $qbids = DB::table('question_banks')
                            //->where(['class_id'=>$class_id, 'subject_id'=>$subject_id, 'term_id'=>$term_id])
                         ->whereIn('chapter_id', $chapter_ids)->select('id')->get();
                         $to_date = date('Y-m-d', strtotime('+10 days'));

                         $test_mark = 0;

                         $test_id = DB::table('tests')->insertGetId([
                            'term_id' =>$term_id,
                            'school_id' =>$school_id,
                            'class_id' =>$class_id,
                            'subject_id' =>$subject_id, 
                            'test_name' =>$test_name,
                            'from_date' => date('Y-m-d'),
                            'to_date' => $to_date,
                            'status' =>'ACTIVE',
                            'is_self_test' =>1,
                            'created_at' =>date('Y-m-d H:i:s'),
                            'created_by' =>$userid,
                        ]);
       
                        $qb_ids = [];
                        if($qbids->isNotEmpty()) {
                            foreach($qbids as $qk => $qv)   {
                                $qb_ids[] = $qv->id;
                            }
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'No Questions available in these chapters']);
                        }

                        $questions = [];
                        foreach($qb_test_summary as $qb_type) {
                            QuestionBankItems::$noofquestions = $qb_type['noofquestions'];
                            QuestionBankItems::$qb_ids = $qb_ids;
                               $qb_items = QuestionBankItems::with('questiontype_settings')
                                ->where('question_type', $qb_type['question_type'])
                                ->whereIn('question_bank_id', $qb_ids)
                                ->select('question_type_id', 'question_type', 'question_bank_id','id') 
                                //->groupby('question_bank_id')
                                ->groupby('question_type')
                                ->get();

                            if($qb_items->isNotEmpty()) {
                                $qb_items = $qb_items->toArray();
                                $arr['markperquestion'] = $qb_type['markperquestion'];
                                $arr['questions'] = $qb_items;
                                $questions[] = $arr; 
                            }
                            //echo "<pre>";print_r($qb_items); // exit;
                            foreach($qb_items as $items) {
                              
                                
                                $question_item_id = $items['id'];
                                $question_type_id = $items['question_type_id'];
                                $question_typ = $items['question_type'];
                                foreach($items['qb_items'] as $item){

                                    $test_mark += $qb_type['markperquestion'];

                                    DB::table("test_items")->insert([
                                    'test_id' => $test_id,
                                    'question_bank_item_id' => $item->id,
                                    'mark'=>$qb_type['markperquestion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_by' => $userid
                                    ]);
                                }
                            

                            }
                        }


                        DB::table('tests')->where('id', $test_id)->update(['test_mark'=>$test_mark, 'test_time' => 30]);
                     
                        if(count($questions)>0) {
                            return response()->json([ 'status' => 1, 'message' => 'Test created successfully', 'data' =>$questions]);
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'Unable to create the Test. Please provide valid Input']);    
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    // Submit Self Test details of the user Api
    public function submitSelfTest(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'class_id', 'subject_id', 'term_id', 'test_name', 'test_answers', 'duration'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;
                $test_name = ((isset($input) && isset($input['test_name']))) ? $input['test_name'] : '';  
                $test_answers = ((isset($input) && isset($input['test_answers']))) ? $input['test_answers'] : ''; 
                $duration = ((isset($input) && isset($input['duration']))) ? $input['duration'] : '';  
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $class_id > 0 && $subject_id > 0 && $term_id > 0) {   
                        
                        if(empty($test_name)) {
                            $test_name = date('ymdhis');
                        } 

                        $test_id = DB::table('tests')->insertGetId([
                            'term_id' =>$term_id,
                            'class_id' =>$class_id,
                            'subject_id' =>$subject_id,
                            'test_name' =>$test_name,
                            'status' =>'ACTIVE',
                            'is_self_test' =>1,
                            'created_at' =>date('Y-m-d H:i:s'),
                            'created_by' =>$userid,
                        ]); 
       
                        $data = ["user_id"=>$userid, "term_id"=>$term_id, "class_id"=>$class_id, "subject_id"=>$subject_id, 
                        "test_id"=>$test_id, "test_date"=>date('Y-m-d H:i:s') ];

                        $ex = DB::table("student_tests")->where('user_id', $userid)->where('test_id', $test_id)->first();
                        if(!empty($ex)) {
                            $data['updated_by'] = $userid;
                            $data['updated_at'] = date('Y-m-d H:i:s');

                            DB::table("student_tests")->where('user_id', $userid)->where('test_id', $test_id)->update($data);

                            $student_test_id = $ex->id;
                        }   else {
                            $data['created_by'] = $userid;
                            $data['created_at'] = date('Y-m-d H:i:s');

                            $student_test_id = DB::table("student_tests")->insertGetId($data);
                        }

                        if(is_array($test_answers) && count($test_answers)>0) {
                            foreach($test_answers as $ans) {

                                $extestitem = DB::table("test_items")->where('test_id', $test_id) 
                                    ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();

                                if(empty($extestitem)) {
                                    DB::table("test_items")->insert([
                                        'test_id' => $test_id,
                                        'question_bank_item_id' => $ans['question_bank_item_id'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => $userid
                                    ]);
                                } 

                                $ex = DB::table("student_test_answers")->where('student_id', $userid)
                                    ->where('student_test_id', $student_test_id)
                                    ->where('question_bank_item_id', $ans['question_bank_item_id'])->first();

                                $answer = ["student_id"=>$userid, "student_test_id"=>$student_test_id, 
                                    "question_bank_item_id"=>$ans['question_bank_item_id'], 
                                    "answer"=>$ans['answer']];
                                if(!empty($ex)) {
                                    $answer['updated_by'] = $userid;
                                    $answer['updated_at'] = date('Y-m-d H:i:s');

                                    DB::table("student_test_answers")->where('student_id', $userid)
                                        ->where('student_test_id', $student_test_id)
                                        ->where('question_bank_item_id', $ans['question_bank_item_id'])
                                        ->update($answer);
                                }   else {
                                    $answer['created_by'] = $userid;
                                    $answer['created_at'] = date('Y-m-d H:i:s');

                                    DB::table("student_test_answers")->insert($answer);
                                }
                            }
                        }  

                        if($student_test_id) {
                            return response()->json(['status' => 1, 'message' => 'Test submitted successfully']);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Unable to update']);
                        } 

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Exams List Api
    public function getExamsList(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;   
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0; 
                $exam_id = ((isset($input) && isset($input['exam_id']))) ? $input['exam_id'] : 0;  

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students')
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();   
                        if(!empty($user_details)) {
                            $class_id = $user_details->class_id;
                            $section_id = $user_details->section_id;
                            
                            if($class_id > 0) { 

                                if($exam_id > 0) {
                                    MarksEntry::$exam_id = $exam_id;
                                    MarksEntry::$class_id = $class_id;
                                    MarksEntry::$section_id = $section_id;
                                    $examqry = MarksEntry::with('marksentryitems') 
                                        ->leftjoin('classes', 'classes.id', 'marks_entry.class_id') 
                                        ->leftjoin('exams', 'exams.id', 'marks_entry.exam_id') 
                                        ->where('class_id', $class_id)->where('user_id', $userid)
                                        ->where('exams.schedule_status', 'SCHEDULED')->where('exams.publish_status', 'PUBLISHED')
                                        ->select('marks_entry.*', 'classes.class_name', 'exams.rank_on_off', 'exams.grade_on_off', 
                                            'exams.rank_settings', 'exams.grade_settings'); 
                                    $examqry->where('marks_entry.exam_id', $exam_id);
                                    $examlist = $examqry->orderby('marks_entry.id', 'Desc')->get(); 

                                    if($examlist->isNotEmpty()) {
                                        foreach($examlist as $ek=>$exams) {
                                            if($exams->pass_type == 'Fail') {
                                                $examlist[$ek]->grade = '';
                                                $examlist[$ek]->rank = 0;
                                            }
                                        }
                                    }
                                }   else {
                                    $examqry = DB::table('exams')
                                        ->leftjoin('exam_sessions', 'exam_sessions.exam_id', 'exams.id')
                                        ->leftjoin('examinations', 'examinations.id', 'exams.examination_id')
                                        //->whereRaw('FIND_IN_SET('.$class_id.', class_ids)')
                                        ->where('exam_sessions.class_id', $class_id)
                                        ->whereIn('exam_sessions.section_id', [0,$section_id])
                                        ->where('exams.schedule_status', 'SCHEDULED')->where('exams.publish_status', 'PUBLISHED')
                                        ->select('exams.*', DB::RAW('COALESCE(examinations.exam_name,exams.exam_name) as exam_name'));
                                        $examlist = $examqry->groupby('exams.id')->orderby('exams.id', 'Desc')->get(); 
                                        //->skip($page_no)->take($limit)
                                }

                                if($examlist->isNotEmpty()) {
                                    return response()->json(['status' => 1, 'message' => 'Exam list', 'data' => $examlist]);
                                }   else {
                                    return response()->json(['status' => 0, 'message' => 'No Exam list']);
                                } 
                            } else {
                                return response()->json([ 'status' => 0, 'message' => 'Invalid Class']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                        }

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //Exams List Api
    public function getExamTimetable(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;   
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0; 
                $exam_id = ((isset($input) && isset($input['exam_id']))) ? $input['exam_id'] : 0;  

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  
                        $user_details = DB::table('students')
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();   
                        if(!empty($user_details)) {
                            $class_id = $user_details->class_id;
                            $section_id = $user_details->section_id;
                            
                            if($class_id > 0) { 
 
                                $examqry = DB::table('exams')
                                    ->leftjoin('exam_sessions', 'exam_sessions.exam_id', 'exams.id')
                                    ->leftjoin('examinations', 'examinations.id', 'exams.examination_id')
                                    //->whereRaw('FIND_IN_SET('.$class_id.', class_ids)')
                                    ->where('exam_sessions.class_id', $class_id)->where('exam_sessions.status', 'ACTIVE')
                                    ->whereDate('exams.exam_enddate', '>=', date('Y-m-d'))
                                    ->select('exams.id', 'exams.school_id', 'exam_startdate', 'exam_enddate', 'exams.exam_name',
                                    DB::RAW('COALESCE(examinations.exam_name,exams.exam_name) as exam_name'));
                                $examlist = $examqry->groupby('exams.id')->orderby('exams.id', 'Desc')->get(); 
                                    //->skip($page_no)->take($limit)
                               

                                if($examlist->isNotEmpty()) {
                                    foreach($examlist as $ek => $ev) {
                                        $exam_sessions_structure = [];
                                        $exam_sessions = DB::table('exam_sessions')->leftjoin('subjects', 'subjects.id', 'exam_sessions.subject_id')
                                            ->where('exam_id', $ev->id)->where('exam_sessions.subject_id', '>', 0)
                                            ->where('exam_sessions.class_id', $class_id)
                                            ->whereIn('exam_sessions.section_id', [0, $section_id]) 
                                            ->where('exam_sessions.status', 'ACTIVE')->select('exam_sessions.*','subjects.subject_name')
                                            ->get();
                                        if($exam_sessions->isNotEmpty()) {
                                            foreach($exam_sessions as $k => $v) {
                                                $exam_sessions_structure[] = ['date' => $v->exam_date, 'subject_name' => $v->subject_name, 'session' => $v->session, 'syllabus' =>  $v->syllabus]; 

                                                if($v->is_practical  == 1) {
                                                    $exam_sessions_structure[] = ['date' => $v->practical_date, 'subject_name' => $v->subject_name.' - Practical', 'session' => $v->psession, 'syllabus' =>  $v->syllabus]; 
                                                } 
                                               
                                            }
                                            $examlist[$ek]->timetable = $exam_sessions_structure;
                                        } else {
                                            unset($examlist[$ek]);
                                        }
                                    }

                                    if(count($examlist)>0) {
                                        $new_examlist = [];
                                        foreach($examlist as $list) {
                                            $new_examlist[] = $list;
                                        }
                                        return response()->json(['status' => 1, 'message' => 'Exam list', 'data' => $new_examlist]);
                                    }   else {
                                        return response()->json(['status' => 0, 'message' => 'No Exam list']);
                                    }
                                    
                                }   else {
                                    return response()->json(['status' => 0, 'message' => 'No Exam list']);
                                } 
                            }
                        }

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    public function getnewStudentResult(Request $request){
        
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token','test_id'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $test_id = ((isset($input) && isset($input['test_id']))) ? $input['test_id'] : 0;
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;
                $is_self_test = ((isset($input) && isset($input['is_self_test']))) ? $input['is_self_test'] : 0;

                $api_token = $request->header('x-api-key');
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];

                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    if($userid > 0) {
                        $testqry = DB::table('student_tests')->leftjoin('users', 'users.id', 'student_tests.user_id')
                        ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                        ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                        ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                        ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                        ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                        ->where('student_tests.user_id', $userid)
                        ->where('tests.is_self_test',$is_self_test)
                        ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                            'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');

                        if($test_id > 0)   {
                            $testqry->where('student_tests.test_id', $test_id)->groupby('student_tests.test_id');
                        }

                        if($subject_id > 0)   {
                            $testqry->where('student_tests.subject_id', $subject_id)->groupby('student_tests.test_id');
                        }

                      $testdetails = $testqry->get();

                  if(count($testdetails) > 0){
                    foreach($testdetails as $k => $value){
                        $new_test_id = $value->test_id;
                        TestItems::$student_id = $userid;
                        TestItems::$student_test_id = $value->id;
                        $get_student = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                            ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                            ->where('test_id', $new_test_id)
                            ->where('user_id', $userid)
                            ->select('student_tests.id','total_questions','attempeted_question', 'student_mark', 'test_mark','test_id', 'duration', 'subjects.subject_name', 'users.name as student_name', 'student_tests.user_id', 'student_tests.created_at')->orderby('id', 'desc')->get();
                       

                        $testdetails[$k]->pre_attempts = $get_student;
                    }

                  }
                   return response()->json(['status' => 1, 'message' => 'Tests list', 'data' => $testdetails]);


                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }


    }

    //get Scholar Batches Api
    public function getScholarBatches(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0) {  

                        $user_batches = StudentAcademics::where('student_class_mappings.status', 'ACTIVE') 
                            ->select('student_class_mappings.academic_year')
                            ->where('student_class_mappings.user_id', $userid)
                            ->orderby('student_class_mappings.academic_year', 'asc')->get();   

                        if($user_batches->isNotEmpty()) {
                            return response()->json(['status' => 1, 'message' => 'Batches list', 'data' => $user_batches]);
                        }   else {
                            return response()->json(['status' => 0, 'message' => 'Batches Work']);
                        }  

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //get Scholar Fees Payments Api
    public function getScholarFeesPayments_5624(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'batch'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : ''; 
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $batch > 0) {  

                        $get_class_id = Student::where('user_id',$userid)->select('id','school_id','user_id','class_id','section_id','admission_no')->first();

                        if(!empty($get_class_id)) {
                            $class_id = $get_class_id->class_id; 
                            $school_id = $get_class_id->school_id; 
                            $studentId = $userid;

                            $scholar_fees_total = $scholar_fees_concession = $scholar_fees_paid = $scholar_fees_balance = 0; 

                            $student = FeeStructureList::with(['feeItems.feeItem'])
                                                ->where('school_id', $school_id)
                                                ->where('batch', $batch)
                                                ->whereRaw("FIND_IN_SET(?, class_list)", [$class_id])
                                                ->select('id','school_id','batch','fee_category_id','fee_type','class_list')
                                                ->get(); 


                            // Fetch paid records for the student
                            $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->get();
                            //echo "<pre>"; print_r($student->toArray()); 
                            // Map paid records by fee_structure_item_id
                            $paid_records_map = [];
                            foreach ($get_paid_records as $record) {
                                if (!isset($paid_records_map[$record->fee_structure_item_id])) {
                                    $paid_records_map[$record->fee_structure_item_id] = [
                                        'total_paid' => 0,
                                        'payment_status' => $record->payment_status
                                    ];
                                }
                                $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
                            }
                            //print_r($paid_records_map); exit;
                            // Loop through each fee structure item and determine the payment status and balance amount
                            foreach ($student as $feeStructure) {
                                foreach ($feeStructure->feeItems as $feeItem) {
                                    $fee_item_id = $feeItem->fee_structure_id; // $feeItem->feeItem->id;
                                    $fee_amount = $feeItem->amount;
                                    $fee_status_flag = 0; $total_paid = 0;// Default flag for not paid
                                    $balance_amount = $fee_amount; // Default balance amount to the full fee amount

                                    if (isset($paid_records_map[$fee_item_id])) {
                                        $total_paid = $paid_records_map[$fee_item_id]['total_paid'];

                                        $balance_amount = max($fee_amount - $total_paid, 0);

                                        if ($balance_amount == 0) {
                                            $fee_status_flag = 1; // Fully paid
                                        } elseif ($balance_amount < $fee_amount) {
                                            $fee_status_flag = 2; // Partially paid
                                        }
                                    }

                                    // Ensure balance_amount is not null and attach the flag and balance to the fee item
                                    $feeItem->payment_status_flag = $fee_status_flag;
                                    $feeItem->balance_amount = $balance_amount;
                                    $feeItem->paid_amount = $total_paid;
                                }
                            }

                            return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data' => $student, 
                                    'total_amount' => 0, 'concession_amount' => 0, 
                                    'paid_amount' => 0, 'balance_amount' => 0]);

                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid class details']);
                        }
                        

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //get Scholar Fees Payments Api
    public function getScholarFeesPayments_6624(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'batch'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : ''; 
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $batch > 0) { 

                        $studentId = $userid;
                        $scholar_fees_total = $scholar_fees_concession = $scholar_fees_paid = $scholar_fees_balance = 0; 

                        // Fetch student details
                        $get_class_id = Student::where('user_id', $studentId)
                            ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                            ->first();
                        $class_id = $get_class_id->class_id;
                        $section_id = $get_class_id->section_id;
                        $school_id = $get_class_id->school_id; 
                        $gender = DB::table('users')->where('id', $studentId)->value('gender');

                        FeeStructureList::$gender = $gender;

                        // Retrieve fee structures
                        $feeStructures = FeeStructureList::with(['feeItems.feeItem'])
                            ->where('school_id', $school_id)
                            ->where('batch', $batch)
                            ->get();

                        // Fetch paid records for the student
                        $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->get();

                        // Map paid records by fee_structure_item_id
                        $paid_records_map = [];
                        foreach ($get_paid_records as $record) {
                            if (!isset($paid_records_map[$record->fee_structure_item_id])) {
                                $paid_records_map[$record->fee_structure_item_id] = [
                                    'total_paid' => 0,
                                    'payment_status' => $record->payment_status,
                                    'total_concession' => 0,
                                ];
                            }
                            $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
                            $paid_records_map[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;
                        }

                        // Process fee structures based on fee_post_type
                        $studentFeeStructures = [];
                        foreach ($feeStructures as $feeStructure) {
                            $fee_post_type = $feeStructure->fee_post_type;
                            $class_list = explode(',', $feeStructure->class_list);

                            $appliesToStudent = false;
                            switch ($fee_post_type) {


                                case 1: // Class
                                    $appliesToStudent = in_array($class_id, $class_list);
                                    break;
                                case 2: // Section
                                    $appliesToStudent = in_array($section_id, $class_list);
                                    break;
                                case 3: // All
                                    $appliesToStudent = true;
                                    break;
                                case 4: // Group
                                    $communicationGroups = CommunicationGroup::all();
                                    foreach ($communicationGroups as $group) {
                                        $members = explode(',', $group->members);

                                     //   dd($members);
                                        if (in_array($studentId, $members)) {
                                            $appliesToStudent = in_array($group->id, $class_list);
                                            if ($appliesToStudent) {
                                                break;
                                            }
                                        }
                                    }
                                    break;
                            }

                            $item_fees_total = $item_fees_concession = $item_fees_paid = $item_fees_balance = 0; 
                            if ($appliesToStudent) {
                                foreach ($feeStructure->feeItems as $feeItem) {

                                    $scholar_fees_total += $feeItem->amount;

                                    $due_date = $feeItem->due_date;

                                    $fee_item_id = $feeItem->id;
                                    $fee_amount = $feeItem->amount;
                                    $fee_status_flag = 0;
                                    $total_paid = 0; $total_concession = 0;
                                    $balance_amount = $fee_amount;

                                    if (isset($paid_records_map[$fee_item_id])) {
                                        $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
                                        $balance_amount = max($fee_amount - $total_paid, 0);
                                        $total_concession = $paid_records_map[$fee_item_id]['total_concession'];

                                        $scholar_fees_paid += $total_paid;
                                        $scholar_fees_balance += $balance_amount;
                                        $scholar_fees_concession += $total_concession;

                                        $balance_amount = $balance_amount - $total_concession;

                                        if ($balance_amount == 0) {
                                            $fee_status_flag = 1; // Fully paid
                                        } elseif ($balance_amount < $fee_amount) {
                                            if(strtotime($due_date) == strtotime(date('Y-m-d'))) {
                                                $fee_status_flag = 3; // On Due
                                            }   else if(strtotime($due_date) < strtotime(date('Y-m-d'))) {
                                                $fee_status_flag = 4; // Over Due
                                            }   else {
                                                $fee_status_flag = 2; // Partially paid
                                            }  
                                        }
                                    }

                                    $feeItem->payment_status_flag = $fee_status_flag;
                                    $feeItem->balance_amount = $balance_amount;
                                    $feeItem->paid_amount = $total_paid;
                                    $feeItem->concession_amount = $total_concession;

                                    $item_fees_total +=  $fee_amount;
                                    $item_fees_concession += $total_concession;
                                    $item_fees_paid += $total_paid;
                                    $item_fees_balance += $balance_amount;
                                }
                                $studentFeeStructures[] = $feeStructure;
                            }

                            $feeStructure->item_fees_total = $item_fees_total;
                            $feeStructure->item_fees_concession = $item_fees_concession;
                            $feeStructure->item_fees_paid = $item_fees_paid;
                            $feeStructure->item_fees_balance = $item_fees_balance;

                        } 
                        //$scholar_fees_balance = $scholar_fees_balance - $scholar_fees_concession;
                        $scholar_fees_balance = $scholar_fees_total - ($scholar_fees_paid + $scholar_fees_concession); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data' => $feeStructures, 
                                    'total_amount' => $scholar_fees_total, 'concession_amount' => $scholar_fees_concession, 
                                    'paid_amount' => $scholar_fees_paid, 'balance_amount' => $scholar_fees_balance]);

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    //get Scholar Fees Payments Api
    public function getScholarFeesPayments_19624(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'batch'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : ''; 
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $batch > 0) { 
                        $overdue = $due = $upcomings = [];
                        /*$overdue = $this->getScholarFeesInfo($userid, $batch, 1);
                        $due = $this->getScholarFeesInfo($userid, $batch, 2);
                        $upcomings = $this->getScholarFeesInfo($userid, $batch, 3);*/

                        $total = $this->getScholarFeesTotalInfo($userid, $batch);
                        
                        /*return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data' => $feeStructures, 
                                    'total_amount' => $scholar_fees_total, 'concession_amount' => $scholar_fees_concession, 
                                    'paid_amount' => $scholar_fees_paid, 'balance_amount' => $scholar_fees_balance]);*/

                        $data = ['overdue' => $overdue, 'due' => $due, 'upcomings' => $upcomings];

                        return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data'=>$data, 'total' => $total]);

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }


    public function getScholarFeesInfo($userid, $batch, $mode) { 

        //$mode = 1 - Overdue; 2 - Due; 3 - Upcomings; 4 - consolidated Total

        $studentFeeStructures = [];

        $studentId = $userid;
        // Fetch student details
        $get_class_id = Student::where('user_id', $studentId)
            ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
            ->first();
            //echo "<pre>"; print_r($get_class_id); exit;
        if(!empty($get_class_id)) { 
            $class_id = $get_class_id->class_id;
            $section_id = $get_class_id->section_id;
            $school_id = $get_class_id->school_id; 
            $student_user_id=$get_class_id->user_id;

            $gender = DB::table('users')->where('id', $studentId)->value('gender');

            FeeStructureList::$student_id = $studentId;
            // Retrieve fee structures
            $feeitems = FeeStructureList::leftjoin('fee_structure_items', 'fee_structure_items.fee_structure_id', 'fee_structure_lists.id')
                ->leftjoin('fee_items', 'fee_structure_items.fee_item_id', 'fee_structure_items.fee_item_id')
                ->where('fee_structure_items.cancel_status','0')
                ->where('fee_structure_lists.school_id', $school_id)
                ->where('fee_structure_lists.batch', $batch);

            if($mode == 1) {
                $feeitems->where('fee_structure_items.due_date', '<', date('Y-m-d'));
            }  else if($mode == 2) {
                $feeitems->where('fee_structure_items.due_date', '=', date('Y-m-d'));
            }  else if($mode == 3) {
                $feeitems->where('fee_structure_items.due_date', '>', date('Y-m-d'));
            }   

            $feeitems = $feeitems->select('fee_structure_lists.*', 'fee_structure_items.id as item_id', 
                'fee_structure_items.fee_item_id', 'fee_structure_items.gender',
                'fee_structure_items.amount', 'fee_structure_items.due_date', 'fee_items.item_name')
                ->get();

            // Fetch paid records for the student
            $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->where('cancel_status','0')->get();

            // Map paid records by fee_structure_item_id
            $paid_records_map = []; $paiditems = [];
            foreach ($get_paid_records as $record) {
                if (!isset($paid_records_map[$record->fee_structure_item_id])) {
                    $paid_records_map[$record->fee_structure_item_id] = [
                        'amount_to_pay' => 0,
                        'total_paid' => 0,
                        'payment_status' => $record->payment_status,
                        'total_concession' => 0,
                        'total_waiver' => 0,
                    ];
                    $paiditems[] = $record->fee_structure_item_id;
                }
                /*$paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
                $paid_records_map[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;*/

                $paid_records_map[$record->fee_structure_item_id]['amount_to_pay'] += $record->amount_to_pay;
                $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
                if($record->is_concession == 1) {
                    $paid_records_map[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;
                }
                if($record->is_waiver == 1) {
                    $paid_records_map[$record->fee_structure_item_id]['total_waiver'] += $record->concession_amount;
                }
            }

            // Process fee structures based on fee_post_type
            $studentFeeStructures = [];

            foreach ($feeitems as $feeStructure) {
                $fee_post_type = $feeStructure->fee_post_type;
                $class_list = explode(',', $feeStructure->class_list);

                $appliesToStudent = false;
                switch ($fee_post_type) {


                    case 1: // Class
                        $appliesToStudent = in_array($class_id, $class_list);
                        break;
                    case 2: // Section
                        $appliesToStudent = in_array($section_id, $class_list);
                        break;
                    case 3: // All
                        $appliesToStudent = true;
                        break;
                    case 4: // Group
                        $communicationGroups = CommunicationGroup::where('school_id',Auth::User()->id)->get();
                        foreach ($communicationGroups as $group) {
                            $members = explode(',', $group->members);

                         //   dd($members);
                            if (in_array($studentId, $members)) {
                                $appliesToStudent = in_array($group->id, $class_list);
                                if ($appliesToStudent) {
                                    break;
                                }
                            }
                        }
                        break;
                    case 5: // Specific
                        $appliesToStudent = in_array($student_user_id, $class_list);
                        break;
                }
 
                if ($appliesToStudent) { 
                     

                        $due_date = $feeStructure->due_date; 
                        $fee_item_id = $feeStructure->fee_item_id;
                        $fee_amount = $feeStructure->amount;
                        $fee_status_flag = 0;  $due_days = 0;
                        $total_paid = 0; $total_concession = 0; $total_waiver = 0;
                        $balance_amount = $fee_amount;

                        if (isset($paid_records_map[$fee_item_id])) {
                            if($paid_records_map[$fee_item_id]['amount_to_pay'] > 0)  {
                                $scholar_fees_total += $paid_records_map[$fee_item_id]['amount_to_pay'];
                                $fee_amount = $paid_records_map[$fee_item_id]['amount_to_pay'];
                            }   else {
                                //$scholar_fees_total += $feeItem->amount;
                                $fee_amount = $feeItem->amount;
                            }

                            $balance_amount = $fee_amount;

                            $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
                            $balance_amount = max($fee_amount - $total_paid, 0);
                            $total_concession = $paid_records_map[$fee_item_id]['total_concession']; 
                            $total_waiver = $paid_records_map[$fee_item_id]['total_waiver'];

                            $balance_amount = $balance_amount - $total_concession - $total_waiver; 
                            
                        }

                        if ($balance_amount == 0) {
                            $fee_status_flag = 1; // Fully paid
                        } elseif ($balance_amount <= $fee_amount) {
                            if(strtotime($due_date) == strtotime(date('Y-m-d'))) {
                                $fee_status_flag = 3; // On Due
                            } else if(strtotime($due_date) < strtotime(date('Y-m-d'))) {
                                $fee_status_flag = 4; // Over Due
                            }else if(strtotime($due_date) > strtotime(date('Y-m-d'))) {
                                $fee_status_flag = 5; // PENDING
                            }
                               else {
                                $fee_status_flag = 2; // Partially paid
                            }
                        }

                        $feeStructure->due_days = CommonController::getDueDays($due_date);
                        $feeStructure->amount = $fee_amount;
                        $feeStructure->payment_status_flag = $fee_status_flag;
                        $feeStructure->balance_amount = $balance_amount;
                        $feeStructure->paid_amount = $total_paid;
                        $feeStructure->concession_amount = $total_concession;
                        $feeStructure->waiver_amount = $total_waiver; 
 
                    if($balance_amount >0) {
                        $studentFeeStructures[] = $feeStructure;
                    }
                } 

            } 

            //echo "<pre>"; print_r($studentFeeStructures); // print_r( $feeitems->toArray()); print_r( $paid_records_map);
        } 
        return $studentFeeStructures;
    }

    //get Scholar Fees Total Api
    public function getScholarFeesTotalInfo($userid, $batch)     { 
        if($userid > 0 && $batch > 0) { 

            $studentId = $student_user_id = $userid;
            $scholar_fees_total = $scholar_fees_concession = $scholar_fees_paid = $scholar_fees_balance = 0; 

            // Fetch student details
            $get_class_id = Student::where('user_id', $studentId)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            $class_id = $get_class_id->class_id;
            $section_id = $get_class_id->section_id;
            $school_id = $get_class_id->school_id; 
            $gender = DB::table('users')->where('id', $studentId)->value('gender');

            FeeStructureList::$student_id = $studentId;

            // Retrieve fee structures
            $feeStructures = FeeStructureList::with(['feeItems.feeItem'])
                ->where('school_id', $school_id)->where('cancel_status','0')->where('fee_type','1')
                ->where('batch', $batch)
                ->get();

            // Fetch paid records for the student
            $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->where('cancel_status','0')->get();

            // Map paid records by fee_structure_item_id
            $paid_records_map = [];
            foreach ($get_paid_records as $record) {
                if (!isset($paid_records_map[$record->fee_structure_item_id])) {
                    $paid_records_map[$record->fee_structure_item_id] = [
                        'total_paid' => 0,
                        'payment_status' => $record->payment_status,
                        'total_concession' => 0,
                        'total_waiver' => 0,
                    ];
                }
                $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
                if($record->is_concession == 1) {
                    $paid_records_map[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;
                }
                if($record->is_waiver == 1) {
                    $paid_records_map[$record->fee_structure_item_id]['total_waiver'] += $record->concession_amount;
                }
            }

            // Process fee structures based on fee_post_type
            $studentFeeStructures = []; 
                //echo "<pre>"; print_r($feeStructures->toArray()); exit;
            foreach ($feeStructures as $feeStructure) {
                $fee_post_type = $feeStructure->fee_post_type;
                $class_list = explode(',', $feeStructure->class_list);

                $appliesToStudent = false;
                switch ($fee_post_type) {


                    case 1: // Class
                        $appliesToStudent = in_array($class_id, $class_list);
                        break;
                    case 2: // Section
                        $appliesToStudent = in_array($section_id, $class_list);
                        break;
                    case 3: // All
                        $appliesToStudent = true;
                        break;
                    case 4: // Group
                        $communicationGroups = CommunicationGroup::all();
                        foreach ($communicationGroups as $group) {
                            $members = explode(',', $group->members);

                         //   dd($members);
                            if (in_array($studentId, $members)) {
                                $appliesToStudent = in_array($group->id, $class_list);
                                if ($appliesToStudent) {
                                    break;
                                }
                            }
                        }
                        break;
                    case 5: // Specific
                        $appliesToStudent = in_array($student_user_id, $class_list);
                        break;
                }

                $item_fees_total = $item_fees_concession = $item_fees_waiver = $item_fees_paid = $item_fees_balance = 0; 
                if ($appliesToStudent) {
                    foreach ($feeStructure->feeItems as $feeItem) {

                        $scholar_fees_total += $feeItem->amount;

                        $due_date = $feeItem->due_date;

                        $fee_item_id = $feeItem->id;
                        $fee_amount = $feeItem->amount;
                        $fee_status_flag = 0; $due_days = 0;
                        $total_paid = 0; $total_concession = 0; $total_waiver = 0;
                        $balance_amount = $fee_amount;

                        if (isset($paid_records_map[$fee_item_id])) {
                            $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
                            $balance_amount = max($fee_amount - $total_paid, 0);
                            $total_concession = $paid_records_map[$fee_item_id]['total_concession'];
                            $total_waiver = $paid_records_map[$fee_item_id]['total_waiver'];

                            $scholar_fees_paid += $total_paid;
                            $scholar_fees_balance += $balance_amount;
                            $scholar_fees_concession += $total_concession + $total_waiver;

                            $balance_amount = $balance_amount - $total_concession - $total_waiver;

                            if ($balance_amount == 0) {
                                $fee_status_flag = 1; // Fully paid
                            } elseif ($balance_amount <= $fee_amount) {
                                if(strtotime($due_date) == strtotime(date('Y-m-d'))) {
                                    $fee_status_flag = 3; // On Due
                                } else if(strtotime($due_date) < strtotime(date('Y-m-d'))) {
                                    $fee_status_flag = 4; // Over Due
                                }else if(strtotime($due_date) > strtotime(date('Y-m-d'))) {
                                    $fee_status_flag = 5; // PENDING
                                }
                                   else {
                                    $fee_status_flag = 2; // Partially paid
                                }
                            }
                        }

                        $feeItem->due_days = CommonController::getDueDays($due_date);
                        $feeItem->payment_status_flag = $fee_status_flag;
                        $feeItem->balance_amount = $balance_amount;
                        $feeItem->paid_amount = $total_paid;
                        $feeItem->concession_amount = $total_concession;
                        $feeItem->waiver_amount = $total_waiver;

                        $item_fees_total +=  $fee_amount;
                        $item_fees_concession += $total_concession;
                        $item_fees_waiver +=  $total_waiver;
                        $item_fees_paid += $total_paid;
                        $item_fees_balance += $balance_amount;
                    }
                    $studentFeeStructures[] = $feeStructure;
                }

                $feeStructure->item_fees_total = $item_fees_total;
                $feeStructure->item_fees_concession = $item_fees_concession;
                $feeStructure->item_fees_waiver = $item_fees_waiver;
                $feeStructure->item_fees_paid = $item_fees_paid;
                $feeStructure->item_fees_balance = $item_fees_balance;

            } 
            //$scholar_fees_balance = $scholar_fees_balance - $scholar_fees_concession;
            $scholar_fees_balance = $scholar_fees_total - ($scholar_fees_paid + $scholar_fees_concession); 
            
            return [   'total_amount' => $scholar_fees_total, 'concession_amount' => $scholar_fees_concession, 
                        'paid_amount' => $scholar_fees_paid, 'balance_amount' => $scholar_fees_balance];

        }
    }


    public function getContactsList(Request $request) { 
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'message' => $message]);
                }   else {
                    if($userid > 0) {  
                        $school_id = DB::table('users')->where('id', $userid)->value('school_college_id'); 
                        /*$list = ContactsList::where('status', 'YES')->where('school_id', $school_id)->select('contact_for')->groupby('contact_for')->get();*/

                        $list = ContactsList::leftjoin('contacts_for', 'contacts_for.id', 'school_contacts_list.contact_for')
                            ->where('school_contacts_list.status', 'YES')->where('school_contacts_list.school_id', $school_id)->select('school_contacts_list.contact_for', 'contacts_for.name')
                            ->groupby('contact_for')->get();

                        if (sizeof($list) > 0) {
                            return response()->json(['status' => 1, 'data' => $list, 'message' => 'Contacts']);
                        } else {
                            return response()->json(['status' => 0, 'message' => 'Contacts Not Available']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid Inputs']);
                    }
                }
            } else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            } 
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }
    }

    public function getBanksList(Request $request) { 
        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token'];

            $error = $this->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'message' => $message]);
                }   else {
                    if($userid > 0) {  
                        $school_id = DB::table('users')->where('id', $userid)->value('school_college_id');  

                        $list = SchoolBankList::where('status', 'ACTIVE')->where('school_id', $school_id)
                            ->select('id', 'bank_name', 'account_holder_name', 'account_no', 'branch_name','ifsc_code', 
                                'qr_code_image','upi_id')->orderby('position', 'asc')
                            ->get();

                        if ($list->isNotEmpty()) {
                            return response()->json(['status' => 1, 'data' => $list, 'message' => 'Banks']);
                        } else {
                            return response()->json(['status' => 0, 'message' => 'Banks Not Available']);
                        }
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid Inputs']);
                    }
                }
            } else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            } 
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }
    }

    //get Scholar Fees Transactions Api
    public function getScholarFeesTransactions(Request $request)     {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'batch'];

            $error = $this->checkParams($input, $requiredParams, $request);

            $error = '';

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : ''; 
  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
 
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else { 
                    if($userid > 0 && $batch > 0) {  
                        $school_id = DB::table('users')->where('id', $userid)->value('school_college_id'); 
                        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)
                            ->where('fees_payment_details.batch', $batch)
                            ->where('fees_payment_details.student_id', $userid)
                            ->where('fees_payment_details.is_concession', 0)
                            ->where('fees_payment_details.is_waiver', 0)
                            ->where('fees_payment_details.cancel_status', 0) 
                            ->orderby('fees_payment_details.id','asc')
                            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                                'creator.name as creator_name')->get();

                        return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data'=>$fee_summary_list]);

                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }  
    }

    public function getScholarFeesPayments(Request $request)     {
        try {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, TRUE);
            $requiredParams = ['user_id', 'api_token', 'batch'];
            $error = $this->checkParams($input, $requiredParams, $request);
            $error = '';
            if(empty($error)) {
                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $api_token = $request->header('x-api-key');
                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                }   else {
                    if($userid > 0 && $batch > 0) {
                        $overdue = $due = $upcomings = [];
                        /*$overdue = $this->getScholarFeesInfo($userid, $batch, 1);
                        $due = $this->getScholarFeesInfo($userid, $batch, 2);
                        $upcomings = $this->getScholarFeesInfo($userid, $batch, 3);*/
                        $total = $this->getScholarFeesTotalInfo($userid, $batch);
                        /*return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data' => $feeStructures,
                                    'total_amount' => $scholar_fees_total, 'concession_amount' => $scholar_fees_concession,
                                    'paid_amount' => $scholar_fees_paid, 'balance_amount' => $scholar_fees_balance]);*/
                        $data = $this->getScholarFeeRecord($userid, $batch);
                  //      $data = ['overdue' => $overdue, 'due' => $due, 'upcomings' => $upcomings];
                        return response()->json([ 'status' => 1, 'message' => 'Fees details', 'data'=>$data, 'total' => $total]);
                    }   else {
                        return response()->json([ 'status' => 0, 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }
    }

    public function getScholarFeeRecord($userid, $batch) {  
        if ($userid > 0 && $batch > 0) {
            $studentId = $userid;
            // Fetch student details
            $studentDetails = Student::where('user_id', $studentId)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            $class_id = $studentDetails->class_id;
            $section_id = $studentDetails->section_id;
            $school_id = $studentDetails->school_id;
            FeeStructureList::$student_id = $studentId;
            // Retrieve fee structures
            $feeStructures = FeeStructureList::with(['feeItems.feeItem'])
                ->where('school_id', $school_id)->where('cancel_status', '0')->where('fee_type','1')
                ->where('batch', $batch)
                ->get();
            //echo "<pre>"; print_r($feeStructures); exit;
            // Fetch paid records for the student
            $paidRecords = FeesPaymentDetail::where('student_id', $studentId)->where('cancel_status','0')->get();
            // Map paid records by fee_structure_item_id
            $paidRecordsMap = [];
            foreach ($paidRecords as $record) {
                if (!isset($paidRecordsMap[$record->fee_structure_item_id])) {
                    $paidRecordsMap[$record->fee_structure_item_id] = [
                        'total_paid' => 0,
                        'total_concession' => 0,
                        'total_waiver' => 0,
                    ];
                }
                $paidRecordsMap[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
                if ($record->is_concession == 1) {
                    $paidRecordsMap[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;
                }
                if ($record->is_waiver == 1) {
                    $paidRecordsMap[$record->fee_structure_item_id]['total_waiver'] += $record->concession_amount;
                }
            }
            // Initialize arrays for categorizing fees
            $overdueFees = [];
            $dueFees = [];
            $pendingFees = [];
            // Process fee structures based on fee_post_type
            foreach ($feeStructures as $feeStructure) {
                $fee_post_type = $feeStructure->fee_post_type;
                $class_list = explode(',', $feeStructure->class_list);
                $appliesToStudent = false;
                switch ($fee_post_type) {
                    case 1: // Class
                        $appliesToStudent = in_array($class_id, $class_list);
                        break;
                    case 2: // Section
                        $appliesToStudent = in_array($section_id, $class_list);
                        break;
                    case 3: // All
                        $appliesToStudent = true;
                        break;
                    case 4: // Group
                        $communicationGroups = CommunicationGroup::all();
                        foreach ($communicationGroups as $group) {
                            $members = explode(',', $group->members);
                            if (in_array($studentId, $members)) {
                                $appliesToStudent = in_array($group->id, $class_list);
                                if ($appliesToStudent) {
                                    break;
                                }
                            }
                        }
                        break;
                    case 5: // Specific
                        $appliesToStudent = in_array($studentId, $class_list);
                        break;
                }
                if ($appliesToStudent) {
                    foreach ($feeStructure->feeItems as $feeItem) {
                        $due_date = $feeItem->due_date;
                        $fee_item_id = $feeItem->id;
                        $fee_amount = $feeItem->amount;
                        $balance_amount = $fee_amount;
                        $total_concession = 0; $due_days = 0;
                        $total_waiver = 0; $total_paid = 0;
                        if (isset($paidRecordsMap[$fee_item_id])) {
                            $total_paid = $paidRecordsMap[$fee_item_id]['total_paid'];
                            $total_concession = $paidRecordsMap[$fee_item_id]['total_concession'];
                            $total_waiver = $paidRecordsMap[$fee_item_id]['total_waiver'];
                            $balance_amount = max($fee_amount - $total_paid - $total_concession - $total_waiver, 0);
                            if ($balance_amount == 0) {
                                continue; // Fully paid, skip
                            } else {
                                $feeItem->is_due_date = date('d M Y', strtotime($due_date));
                                $feeItem->balance_amount = $balance_amount;
                                $feeItem->concession_amount = $total_concession;
                                $feeItem->waiver_amount = $total_waiver;
                                $feeItem->total_paid = $total_paid;
                                $feeItem->due_days = CommonController::getDueDays($due_date);
                                if (strtotime($due_date) == strtotime(date('Y-m-d'))) {
                                    $dueFees[] = $feeItem;
                                } elseif (strtotime($due_date) < strtotime(date('Y-m-d'))) {
                                    $overdueFees[] = $feeItem;
                                } else {
                                    $pendingFees[] = $feeItem;
                                }
                            }
                        } else {
                            // No payment records, full balance is due
                            $feeItem->is_due_date = date('d M Y', strtotime($due_date));
                            $feeItem->balance_amount = $balance_amount;
                            $feeItem->concession_amount = $total_concession;
                            $feeItem->waiver_amount = $total_waiver;
                            $feeItem->total_paid = $total_paid;
                            $feeItem->due_days = CommonController::getDueDays($due_date);
                            if (strtotime($due_date) == strtotime(date('Y-m-d'))) {
                                $dueFees[] = $feeItem;
                            } elseif (strtotime($due_date) < strtotime(date('Y-m-d'))) {
                                $overdueFees[] = $feeItem;
                            } else {
                                $pendingFees[] = $feeItem;
                            }
                        }
                    }
                }
            }
            return [
                'overdue_fees' => $overdueFees,
                'due_fees' => $dueFees,
                'pending_fees' => $pendingFees,
            ];
        }
    }
}