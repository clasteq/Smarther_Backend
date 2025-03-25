<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;
use App\Models\User;
use App\Models\Classes; 
use App\Models\Sections;
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
use App\Models\BackgroundTheme;
use App\Models\Category; 
use App\Models\CommunicationPost;
use App\Models\CommunicationGroup; 
use App\Models\CommunicationSms;
use App\Models\CommunicationPostStaff;
use App\Models\PostHomeworks;
use App\Models\PreAdmissionStudent;
use App\Models\Periodtiming;
use App\Models\Holidays;
use App\Models\DltTemplate;
use App\Models\WaiverCategory;
use App\Models\OASections;
use App\Models\AttendanceApproval;
use App\Models\UserRoles;
use App\Models\Teacher;
use App\Models\RoleClasses;
use App\Models\Module;
use App\Models\RoleModuleMapping;
use App\Models\ContactsFor;
use App\Models\ClassTeacher;
use App\Models\SubjectMapping;
use App\Models\Gallery;
use App\Models\Departments;
use App\Models\Survey;
use App\Models\UserRemarks;
use App\Models\Examinations;
use App\Models\OAFeesSections;
use App\Models\FeesReceiptDetail;
use App\Models\TeachersDailyAttendance;
use App\Models\Teacherleave;
use App\Models\StudentsDailyAttendance;

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

class ApiAdminController extends Controller
{   
	
    public $accepted_formats = ['jpeg', 'jpg', 'png', 'JPG', 'JPEG', 'PNG'];
    public $accepted_formats_audio = ['mp3', 'mp4', 'MP3', 'MP4'];
    public $accepted_formats_qbt = ['mp3', 'mp4', 'jpeg', 'jpg', 'png', 'doc', 'docx', 'pdf', 'MP3', 'MP4', 'JPEG', 'JPG', 'PNG', 'DOC', 'DOCX', 'PDF'];
    public $school;

    public function __construct()    { 
        $site_on_off = CommonController::getSiteStatus();
        if($site_on_off != "ON") {
            echo json_encode(['status' => 3, 'data' => null, 'message' => "Under Maintenance"]);
            exit;
        }
    }

    public function postSchoolLogin(Request $request)
    {

        try {
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['email','password', 'fcm_token', 'device_id', 'device_type'];  //, 'school_id'

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {
                $email = $input['email'];   

                $password = $input['password'];    

                $fcmtoken = $input['fcm_token'];      

                $device_id = $input['device_id'];

                $device_type = $input['device_type'];  

                //$school_id = $input['school_id']; 

                /*if($school_id > 0)   {} else {
                    return response()->json(['status' => 0, 'message' => 'Invalid School']);
                }*/

                $user_type = 'SCHOOL';

                if (Auth::attempt(['email' => $email, 'password' => $password, 'user_type' => $user_type, 'status' => 'ACTIVE', 'delete_status' => 0])) { //, 'id' =>$school_id
                    $user = User::where('email', $email)->where('user_type', $user_type)->where('status', 'ACTIVE')
                        ->where('delete_status', 0)->first();
                        //->where('id', $school_id)
                    if(empty($user)) {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                    } 
                } else if (Auth::attempt(['mobile' => $email, 'password' => $password, 'user_type' => $user_type, 'status' => 'ACTIVE', 'delete_status' => 0])) { //, 'id' =>$school_id
                    $user = User::where('mobile', $email)->where('user_type', $user_type)->where('status', 'ACTIVE')
                        ->where('delete_status', 0)->first();
                        //->where('id', $school_id)
                    if(empty($user)) {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                    } 
                } else if(Auth::attempt(['email' => $email, 'password' => $password, 'status' => 'ACTIVE', 'delete_status' => 0, 
                        'user_type' => function ($query) {
                        $query->whereNotIn('user_type',  ['SUPER_ADMIN', 'GUESTUSER', 'STUDENT', 'SCHOOL']);
                    }])) {
                    $user = User::where('email', $email)->where('status', 'ACTIVE')->where('delete_status', 0)
                        //->where('school_college_id', $school_id)
                        ->whereNotIn('user_type', ['SUPER_ADMIN', 'GUESTUSER', 'STUDENT', 'SCHOOL'])->first();

                    if(empty($user)) {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                    }

                } else if(Auth::attempt(['mobile' => $email, 'password' => $password, 'school_college_id' => $school_id, 'status' => 'ACTIVE', 'delete_status' => 0, 'user_type' => function ($query) {
                        $query->whereNotIn('user_type',  ['SUPER_ADMIN', 'GUESTUSER', 'STUDENT', 'SCHOOL']);
                    }])) {
                    $user = User::where('mobile', $email)->where('status', 'ACTIVE')->where('delete_status', 0)
                        //->where('school_college_id', $school_id)
                        ->whereNotIn('user_type', ['SUPER_ADMIN', 'GUESTUSER', 'STUDENT', 'SCHOOL'])->first();
         
                    if(empty($user)) {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
                    }

                } else { 
                    return response()->json(['status' => 0, 'message' => 'Invalid Login Credential']);

                }
                    $user->fcm_id = $fcmtoken; 
                    $date = date('Y-m-d H:i:s'); 
                    $def_expiry_after =  CommonController::getDefExpiry();
                    $user->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                    $user->api_token = User::random_strings(30);
                    $user->last_login_date = date('Y-m-d H:i:s'); 
                    $user->last_app_opened_date = date('Y-m-d H:i:s'); 
                    $user->user_source_from = $device_type; 

                    $user->save();

                    $current_session_id = Session::getId();
                    $device_id = $request->ip();

                    $current = DB::table('users_loginstatus')->where('session_id', $current_session_id)->get();
                    if ($current->isNotEmpty()) {
                    } else {
                        DB::table('users_loginstatus')->insert(['user_id' => $user->id,
                            'session_id' => $current_session_id,
                            'check_in' => date('Y-m-d H:i:s'),
                            'device_id' => $device_id,
                            'api_token_expiry' => $user->api_token_expiry,
                            'status' => 'LOGIN',
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    $user = CommonController::getUserDetails($user->id);  
                    return response()->json(['status' => 1, 'message' => 'Login Success', 'data' => $user]);

                
            }    else {
                return response()->json([ 'status' => 0, 'message' => $error]);
            }
        }   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        }

    }

    public function getHomeContents(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id'];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $limit = 5;
                        $content = (new AdminController())->getHomeContents($school_id, $limit, $school_id);

                        /* Push notification Topics name */

                        $topics = [];  
                        $user = DB::table('users')->where('id', $userid)->select('id', 'mobile', 'school_college_id', 'user_type')->first();
                        if(!empty($user)) { 
                            $top_arr = [];
                            
                            if($user->user_type == 'SCHOOL') {
                                $topics[]['topic_name'] = CommonController::$topic_school_Admin.$user->school_college_id;
                                $top_arr[] = CommonController::$topic_school_Admin.$user->school_college_id;  
                            } else {
                                $topics[]['topic_name'] = CommonController::$topic_school_staffs.$user->school_college_id;
                                $top_arr[] = CommonController::$topic_school_staffs.$user->school_college_id;
                            }

                            $topics[]['topic_name'] = CommonController::$topic_staffs.$user->id; 
                            $top_arr[] = CommonController::$topic_staffs.$user->id;  
                        
                            $cgroups = DB::table('communication_groups')->where('school_id', $user->school_college_id)
                                ->where('status', 'ACTIVE')
                                ->whereRaw('FIND_IN_SET('.$user->id.', staff_members)')
                                ->select('id')->get(); 
                            if($cgroups->isNotEmpty()) {
                                foreach($cgroups as $cg) {
                                    $topics[]['topic_name'] = CommonController::$topic_group_staffs.$cg->id; 
                                    $top_arr[] = CommonController::$topic_group_staffs.$cg->id; 
                                }
                            }

                            $department_id = DB::table('teachers')->where('user_id', $user->id)->value('department_id'); 
                            if($department_id > 0) {  
                                $topics[]['topic_name'] = CommonController::$topic_department_staffs.$department_id; 
                                $top_arr[] = CommonController::$topic_department_staffs.$department_id;  
                            } 

                            if(count($top_arr)>0) {
                                $top_arr = array_unique($top_arr);
                                $top_arr = array_filter($top_arr);
                                $top_str = implode(',', $top_arr);
                                DB::table('users')->where('id', $user->id)->update(['topics_subscribed' => $top_str]); 
                            } 
                        } 
                        $content['topics'] = $topics;
                        /* Push notification Topics name */

                        return response()->json([ 'status' => 1, 'message' => 'Home Contents', 'data'=>$content]);
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

    public function getGeneralSettings(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id'];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                        if(!empty($settings)) { 
                            return response()->json([ 'status' => 1, 'message' => 'General Settings', 'data'=>$settings]);
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

    public function postGeneralSettings(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = [ 'user_id', 'api_token', 'school_id', "acadamic_year", "display_academic_year", 
                "academic_start_date", "academic_end_date", "helpcontact", "admin_email", "contact_address",  
                "update_holidays" ]; // "facebook_link", "twitter_link", "instagram_link", "skype_link", "youtube_link", 

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $data['acadamic_year'] = ((isset($input) && isset($input['acadamic_year']))) ? $input['acadamic_year'] : '';  
                        $data['display_academic_year'] = ((isset($input) && isset($input['display_academic_year']))) ? $input['display_academic_year'] : '';  
                        $data['academic_start_date'] = ((isset($input) && isset($input['academic_start_date']))) ? $input['academic_start_date'] : '';  
                        $data['academic_end_date'] = ((isset($input) && isset($input['academic_end_date']))) ? $input['academic_end_date'] : '';  
                        $data['helpcontact'] = ((isset($input) && isset($input['helpcontact']))) ? $input['helpcontact'] : '';  
                        $data['admin_email'] = ((isset($input) && isset($input['admin_email']))) ? $input['admin_email'] : '';  
                        $data['contact_address'] = ((isset($input) && isset($input['contact_address']))) ? $input['contact_address'] : '';  
                        $data['update_holidays'] = ((isset($input) && isset($input['update_holidays']))) ? $input['update_holidays'] : 0;  
                        $data['facebook_link'] = ((isset($input) && isset($input['facebook_link']))) ? $input['facebook_link'] : '';  
                        $data['twitter_link'] = ((isset($input) && isset($input['twitter_link']))) ? $input['twitter_link'] : '';  
                        $data['instagram_link'] = ((isset($input) && isset($input['instagram_link']))) ? $input['instagram_link'] : '';  
                        $data['skype_link'] = ((isset($input) && isset($input['skype_link']))) ? $input['skype_link'] : '';  
                        $data['youtube_link'] = ((isset($input) && isset($input['youtube_link']))) ? $input['youtube_link'] : ''; 

                        (new AdminController())->saveGeneralSettings($school_id, $data); 

                        $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                        if(!empty($settings)) {  
                            return response()->json([ 'status' => 1, 'message' => 'General Settings', 'data'=>$settings]);
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

    public function getContent(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'content'];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $content = ((isset($input) && isset($input['content']))) ? $input['content'] : '';  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && !empty($content)) { 

                        if($content == 'about') {
                            $name = 'about';
                        } elseif($content == 'terms') {
                            $name = 'terms_conditions';
                        } elseif($content == 'policy') {
                            $name = 'privacy_policy';
                        } else {
                            $name = 'about';
                        }

                        $content = DB::table('admin_settings')->where('school_id', $school_id)->value($name);
                        if(!empty($content)) { 
                            return response()->json([ 'status' => 1, 'message' => 'Content', 'data'=>$content]);
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

    public function postContent(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'content_name', 'content'];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $content = ((isset($input) && isset($input['content']))) ? $input['content'] : '';  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && !empty($content)) { 

                        if($content == 'about') {
                            $name = 'about';
                        } elseif($content == 'terms') {
                            $name = 'terms_conditions';
                        } elseif($content == 'policy') {
                            $name = 'privacy_policy';
                        } else {
                            $name = 'about';
                        }

                        DB::table('admin_settings')->where('school_id', $school_id)->update([$name=>$content]);
                        $content = DB::table('admin_settings')->where('school_id', $school_id)->value($name);
                        if(!empty($content)) { 
                            return response()->json([ 'status' => 1, 'message' => 'Content saved successfully', 'data'=>$content]);
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

    // Contacts For
    public function getContactsFor(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $contacts_for = DB::table('contacts_for')->where('id', '>', 0)->where('school_id', $school_id) 
                            ->orderby('position', 'asc')->get();  
                        if($contacts_for->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Contacts For', 'data'=>$contacts_for]);
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

    public function postContactsFor(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'contacts_for_name', 'contacts_for_status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $contacts_for_name = ((isset($input) && isset($input['contacts_for_name']))) ? $input['contacts_for_name'] : ''; 
                $contacts_for_status = ((isset($input) && isset($input['contacts_for_status']))) ? $input['contacts_for_status'] : 'ACTIVE';  
                $position = ((isset($input) && isset($input['position']))) ? $input['position'] : 99;  

                $contacts_for_id = ((isset($input) && isset($input['contacts_for_id']))) ? $input['contacts_for_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($contacts_for_id > 0) {
                            $exists = DB::table('contacts_for')->where('name', $contacts_for_name)->where('school_id', $school_id)
                                ->whereNotIn('id', [$contacts_for_id])->first();
                        } else {
                            $exists = DB::table('contacts_for')->where('name', $contacts_for_name)->where('school_id', $school_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Name Already Exists'], 201);
                        }

                        if ($contacts_for_id > 0) {
                            $class = ContactsFor::find($contacts_for_id);
                        } else {
                            $class = new ContactsFor();
                        }
                        $class->school_id = $school_id;
                        $class->name = $contacts_for_name;
                        $class->position = $position;
                        $class->status = $contacts_for_status;

                        $class->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Contacts For Details saved successfully' ]);
                         
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

    // Contacts List
    public function getContactsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $contacts_list = ContactsList::where('id', '>', 0)->where('school_id', $school_id) 
                            ->orderby('id', 'desc')->get();  
                        if($contacts_list->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Contacts List', 'data'=>$contacts_list]);
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

    public function postContactsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'contact_for', 'contact_name', 'contact_mobile', 'contact_email' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $contact_for = ((isset($input) && isset($input['contact_for']))) ? $input['contact_for'] : ''; 
                $contact_name = ((isset($input) && isset($input['contact_name']))) ? $input['contact_name'] : '';  
                $contact_mobile = ((isset($input) && isset($input['contact_mobile']))) ? $input['contact_mobile'] : ''; 
                $contact_email = ((isset($input) && isset($input['contact_email']))) ? $input['contact_email'] : '';
                $contact_info = ((isset($input) && isset($input['contact_info']))) ? $input['contact_info'] : ''; 
                $contacts_status = ((isset($input) && isset($input['contacts_status']))) ? $input['contacts_status'] : 'ACTIVE'; 

                $contacts_id = ((isset($input) && isset($input['contacts_id']))) ? $input['contacts_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        if ($contacts_id > 0) {
                            $class = ContactsList::find($contacts_id);
                        } else {
                            $class = new ContactsList();
                        }
                        $class->school_id = $school_id;
                        $class->contact_for = $contact_for;
                        $class->contact_name = $contact_name;
                        $class->contact_mobile = $contact_mobile;
                        $class->contact_email = $contact_email;
                        $class->contact_info = $contact_info;
                        $class->status = $contacts_status;

                        $class->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Contacts Details saved successfully' ]);
                         
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

    // Classes
    public function getClasses(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $classes = DB::table('classes')->where('id', '>', 0)->where('school_id', $school_id);
                        if(!empty($status_id)) {
                            $classes->where('status', $status_id);
                        }

                        $classes = $classes->orderby('position', 'asc')->get();  
                        if($classes->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Classes', 'data'=>$classes]);
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

    public function postClasses(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_name', 'class_status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_name = ((isset($input) && isset($input['class_name']))) ? $input['class_name'] : ''; 
                $class_status = ((isset($input) && isset($input['class_status']))) ? $input['class_status'] : 'ACTIVE';  
                $position = ((isset($input) && isset($input['position']))) ? $input['position'] : 99;  

                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($class_id > 0) {
                            $exists = DB::table('classes')->where('class_name', $class_name)->where('school_id', $school_id)
                                ->whereNotIn('id', [$class_id])->first();
                        } else {
                            $exists = DB::table('classes')->where('class_name', $class_name)->where('school_id', $school_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Class Name Already Exists'], 201);
                        }

                        if ($class_id > 0) {
                            $class = Classes::find($class_id);
                        } else {
                            $class = new Classes();
                        }
                        $class->school_id = $school_id;
                        $class->class_name = $class_name;
                        $class->position = $position;
                        $class->status = $class_status;

                        $class->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Class details saved successfully' ]);
                         
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

    // Sections
    public function getSections(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $sections = DB::table('sections')->leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.id', '>', 0)->where('sections.school_id', $school_id)
                            ->where('classes.status','=','ACTIVE');
                        if($class_id > 0) {
                            $sections->where('sections.class_id', $class_id);
                        }
                        $sections = $sections->select('sections.*', 'classes.class_name')
                            ->skip($page_no)->take($limit)->get();  
                        if($sections->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Sections', 'data'=>$sections]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Sections']);
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

    /* Function: postSections
    Save into  table
     */
    public function postSections(Request $request) {

        try {   
            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_id', 'section_name', 'section_status' ];
 
            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_name = ((isset($input) && isset($input['section_name']))) ? $input['section_name'] : ''; 
                $section_status = ((isset($input) && isset($input['section_status']))) ? $input['section_status'] : 'ACTIVE';  
                $position = ((isset($input) && isset($input['position']))) ? $input['position'] : 99;  

                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($section_id > 0) {
                            $exists = DB::table('sections')->where('section_name', $section_name)->where('school_id', $school_id)
                                ->where('class_id', $class_id)->whereNotIn('id', [$section_id])->first();
                        } else {
                            $exists = DB::table('sections')->where('section_name', $section_name)->where('school_id', $school_id)
                                ->where('class_id', $class_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Section Name Already Exists'], 201);
                        }

                        if ($section_id > 0) {
                            $class = Sections::find($section_id);
                        } else {
                            $class = new Sections();
                        }
                        $class->school_id = $school_id;
                        $class->class_id = $class_id;
                        $class->section_name = $section_name;
                        $class->position = $position;
                        $class->status = $section_status;

                        $class->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Section details saved successfully' ]);
                         
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

    // Subjects
    public function getSubjects(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $subject_status = ((isset($input) && isset($input['status']))) ? $input['status'] : '';   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $subjects = Subjects::where('subjects.id', '>', 0)->where('subjects.school_id', $school_id);
                        if(!empty($subject_status)) {
                            $subjects->where('status', $subject_status);
                        }
                        $subjects = $subjects->select('subjects.*')
                            ->skip($page_no)->take($limit)->get();  
                        if($subjects->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Subjects', 'data'=>$subjects]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Subjects']);
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

    /* Function: postSubjects
    Save into  table
     */
    public function postSubjects(Request $request) {

        try {   
            /*$inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE);*/

            $input = $request->all();

            $requiredParams = ['user_id', 'api_token', 'school_id', 'subject_name', 'subject_status', 'short_name' ];
 
            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true); 

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $subject_name = ((isset($input) && isset($input['subject_name']))) ? $input['subject_name'] : '';  
                $short_name = ((isset($input) && isset($input['short_name']))) ? $input['short_name'] : ''; 
                $subject_status = ((isset($input) && isset($input['subject_status']))) ? $input['subject_status'] : 'ACTIVE';  
                $position = ((isset($input) && isset($input['position']))) ? $input['position'] : 99;  
                $subject_colorcode = ((isset($input) && isset($input['subject_colorcode']))) ? $input['subject_colorcode'] : '';

                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($subject_id > 0) {
                            $exists = DB::table('subjects')->where('subject_name', $subject_name)->where('school_id','=',$school_id)->whereNotIn('id', [$subject_id])->first();
                        } else {
                            $exists = DB::table('subjects')->where('subject_name', $subject_name)->where('school_id','=',$school_id)->first();
                        } 

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Subject Name Already Exists'], 201);
                        }

                        if ($subject_id > 0) {
                            $exists = DB::table('subjects')->where('short_name', $short_name)->where('school_id','=',$school_id)->whereNotIn('id', [$subject_id])->first();
                        } else {
                            $exists = DB::table('subjects')->where('short_name', $short_name)->where('school_id','=',$school_id)->first();
                        } 

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Short Name Already Exists'], 201);
                        }

                        if ($subject_id > 0) {
                            $subject = Subjects::find($subject_id);
                        } else {
                            $subject = new Subjects();
                        }
                        $subject->school_id = $school_id;
                        $subject->subject_name = $subject_name;
                        $subject->short_name = $short_name;
                        $subject->subject_colorcode = $subject_colorcode;
                        $subject->position = $position;
                        $subject->status = $subject_status; 

                        $image = $request->file('subject_image');

                        if (!empty($image)) {
                            $ext = $image->getClientOriginalExtension();
                            $ext = strtolower($ext);
                            if (!in_array($ext, (new AdminController())->accepted_formats)) {
                                return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                            }

                            $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                            $destinationPath = public_path('/uploads/subjects/');

                            $image->move($destinationPath, $countryimg);

                            $subject->subject_image = $countryimg;

                        }

                        $subject->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Subject details saved successfully' ]);
                         
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

    // Section Subject Mappings 
    public function getSectionSubjectMappings(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $sections = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                            ->where('sections.status','=','ACTIVE')
                            ->select('sections.*', 'classes.class_name');
                        if($subject_id > 0) {
                            $sections->whereRAW(' FIND_IN_SET('.$subject_id.', mapped_subjects) ');
                        } 
                        $sections = $sections->skip($page_no)->take($limit)->get();  
                        if($sections->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Section Subject Mappings ', 'data'=>$sections]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Section Subject Mappings ']);
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

    /* Function: postSectionSubjectMappings
    Save into  table
     */
    public function postSectionSubjectMappings(Request $request) {

        try {   
            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'section_id', 'mapped_subjects' ];
 
            $error = (new ApiController())->checkParams($input, $requiredParams, $request); 

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $mapped_subjects = ((isset($input) && isset($input['mapped_subjects']))) ? $input['mapped_subjects'] : '';  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        
                        if ($section_id > 0) {
                            $section = Sections::find($section_id);
                            $section->mapped_subjects = $mapped_subjects;
                            $section->save();

                            return response()->json([ 'status' => 1, 'message' => 'Subject mapping saved successfully' ]);
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

    // Background Themes
    public function getBgthemes(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $bgthemes = BackgroundTheme::where('id', '>', 0)->where('school_id', $school_id) 
                            ->orderby('position', 'asc')->get();  
                        if($bgthemes->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'BG Themes', 'data'=>$bgthemes]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No BG Themes']);
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

    public function postBgthemes(Request $request) {
        try {    

            $input = $request->all(); 

            $bg_id = ((isset($input) && isset($input['bg_id']))) ? $input['bg_id'] : 0;  

            if($bg_id > 0) {
                $requiredParams = ['user_id', 'api_token', 'school_id', 'bg_name', 'bg_status' ];
            }   else {
                $requiredParams = ['user_id', 'api_token', 'school_id', 'bg_name', 'bg_image', 'bg_status' ];
            } 

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $bg_name = ((isset($input) && isset($input['bg_name']))) ? $input['bg_name'] : '';  
                $position = ((isset($input) && isset($input['position']))) ? $input['position'] : 99;  
                $bg_status = ((isset($input) && isset($input['bg_status']))) ? $input['bg_status'] : 'ACTIVE'; 

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($bg_id > 0) {
                            $exists = DB::table('background_themes')->where('name', $bg_name)->where('school_id', $school_id)
                                ->whereNotIn('id', [$bg_id])->first();
                        } else {
                            $exists = DB::table('background_themes')->where('name', $bg_name)->where('school_id', $school_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'BG Name Already Exists'], 201);
                        }

                        if ($bg_id > 0) {
                            $class = BackgroundTheme::find($bg_id);
                        } else {
                            $class = new BackgroundTheme();
                        }
                        $class->school_id = $school_id;
                        $class->name = $bg_name;
                        $class->position = $position;
                        $class->status = $bg_status;

                        $image = $request->file('bg_image');

                        if (!empty($image)) {
                            $ext = $image->getClientOriginalExtension();
                            $ext = strtolower($ext);
                            if (!in_array($ext, (new AdminController())->accepted_formats)) {
                                return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                            }

                            $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                            $destinationPath = public_path('/uploads/background_themes/');

                            $image->move($destinationPath, $countryimg);

                            $class->image = $countryimg;

                        }

                        $class->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'BG Theme details saved successfully' ]);
                         
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

    // Communication Category
    public function getCategories(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $categories = DB::table('categories')->where('id', '>', 0)->where('school_id', $school_id) 
                            ->orderby('position', 'asc')->get();  
                        if($categories->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Categories', 'data'=>$categories]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Categories']);
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

    public function postCategories(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'name',  'cat_status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $name = ((isset($input) && isset($input['name']))) ? $input['name'] : '';  
                $position = ((isset($input) && isset($input['position']))) ? $input['position'] : 99;  
                $cat_status = ((isset($input) && isset($input['cat_status']))) ? $input['cat_status'] : 'ACTIVE'; 
                $background_theme_id   = ((isset($input) && isset($input['background_theme_id']))) ? $input['background_theme_id'] : 0; 
                $text_color   = ((isset($input) && isset($input['text_color']))) ? $input['text_color'] : ''; 

                $cat_id = ((isset($input) && isset($input['cat_id']))) ? $input['cat_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($cat_id > 0) {
                            $exists = DB::table('categories')->where('name', $name)->where('school_id', $school_id)
                                ->whereNotIn('id', [$cat_id])->first();
                        } else {
                            $exists = DB::table('categories')->where('name', $name)->where('school_id', $school_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Categories Already Exists'], 201);
                        }

                        if ($cat_id > 0) {
                            $class = Category::find($cat_id);
                        } else {
                            $class = new Category();
                        }
                        $class->school_id = $school_id;
                        $class->name = $name;
                        $class->background_theme_id = $background_theme_id;
                        $class->text_color = $text_color;
                        $class->position = $position;
                        $class->status = $cat_status; 

                        $class->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Categories details saved successfully' ]);
                         
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

    // Communication Groups
    public function getGroups(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $groups = CommunicationGroup::where('id', '>', 0)->where('school_id', $school_id)
                            ->skip($page_no)->take($limit)->get();  
                        if($groups->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Groups', 'data'=>$groups]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Groups']);
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

    public function postGroups(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'group_name',  'student_id', 'group_status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $group_name = ((isset($input) && isset($input['group_name']))) ? $input['group_name'] : '';  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : '';  
                $group_status = ((isset($input) && isset($input['group_status']))) ? $input['group_status'] : 'ACTIVE';  

                $group_id = ((isset($input) && isset($input['group_id']))) ? $input['group_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if ($group_id > 0) {
                            $exists = DB::table('communication_groups')->where('group_name', $group_name)->where('school_id', $school_id)->whereNotIn('id', [$group_id])->first();
                        } else {
                            $exists = DB::table('communication_groups')->where('group_name', $group_name)->where('school_id', $school_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Group Name Already Exists'], 201);
                        }

                        if ($group_id > 0) {
                            $cat = CommunicationGroup::find($group_id);
                            $cat->updated_at = date('Y-m-d H:i:s');
                        } else {
                            $cat = new CommunicationGroup;
                            $cat->created_at = date('Y-m-d H:i:s');
                        }
                        $cat->school_id = $school_id; 
                        $cat->group_name = $group_name; 
                        $cat->members = $student_id;
                        $cat->status = $group_status; 

                        $cat->save(); 
                        
                        return response()->json([ 'status' => 1, 'message' => 'Communication Group details saved successfully' ]);
                         
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
    
    public function getCommnSelects(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $get_batches = (new AdminController())->getBatches(); 
                        $get_category=Category::where('status','ACTIVE')->where('school_id', $school_id)->select('id','name')->orderBy('position',"ASC")->get(); 
                        $get_background=BackgroundTheme::where('status','ACTIVE')->where('school_id', $school_id)->select('id','name','theme','image')->get(); 
                        $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id', $school_id)
                            ->select('id','group_name')->get();
                        $acadamic_year = date('Y');

                        $data = []; 
                        $data['batches'] = $get_batches;
                        $data['category'] = $get_category;
                        $data['background'] = $get_background;
                        $data['groups'] = $get_groups;
                        $data['acadamic_year'] = $acadamic_year;

                        return response()->json([ 'status' => 1, 'message' => 'Lists', 'data' => $data ]);
                         
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

    public function getCommnCCStaffs(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $get_staff = Teacher::leftjoin('users', 'users.id', 'teachers.user_id')
                            ->where('users.status','ACTIVE')->where('users.delete_status','0')
                            ->where('users.user_type','TEACHER')->where('users.school_college_id', $school_id)
                            ->select('users.id', 'users.name', 'users.mobile', 'users.email')->get(); 

                        if($get_staff->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Staffs List', 'data' => $get_staff ]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Staffs List']);
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

    public function getCommnStaffSelects(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id', $school_id)
                            ->whereRAW('(staff_members != "" OR staff_members != NULL)') 
                            ->select('id','group_name')->get(); 

                        $teacher_role = UserRoles::where('school_id', $school_id)->where('user_role', 'TEACHER')->select('id','user_role'); 
                        $get_roles= UserRoles::where('status','ACTIVE')->where('school_id', $school_id)
                            ->select('id','user_role')
                            ->union($teacher_role)
                            ->get();

                        $get_departments = Departments::where('status','ACTIVE')->where('school_id', $school_id)
                            ->select('id','department_name')->get();

                        $acadamic_year = date('Y');
                        $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                        if(!empty($settings)) {
                            $acadamic_year = trim($settings->acadamic_year);
                        }
                        if(empty($acadamic_year)) {  $acadamic_year = date('Y'); }

                        $data = [];  
                        $data['departments'] = $get_departments;
                        $data['roles'] = $get_roles;
                        $data['groups'] = $get_groups;
                        $data['acadamic_year'] = $acadamic_year;

                        return response()->json([ 'status' => 1, 'message' => 'Lists', 'data' => $data ]);
                         
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

    // Communication Classes Sections
    public function getCommnClassSections(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : '';  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $classids = (new AdminRoleController())->getApiSchoolRoleClasses($userid);

                        $classes = Classes::where('status', 'ACTIVE')->where('school_id', $school_id);
                        if(count($classids)>0) {
                            $classes->whereIn('id', $classids);
                        }
                        if(!empty(trim($search))) { 
                            $classes->whereRaw(' ( class_name like "%'.$search.'%" ) '); 
                        }
                        $classes = $classes->orderby('position', 'Asc')->get();  

                        $get_sections=Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.status','ACTIVE')->where('sections.school_id', $school_id)
                            ->where('classes.status','ACTIVE')
                            ->select('sections.*');
                        if(count($classids)>0) {
                            $get_sections->whereIn('class_id', $classids);
                        }
                        if(!empty(trim($search))) { 
                            $get_sections->whereRaw(' ( class_name like "%'.$search.'%"  or section_name like "%'.$search.'%") '); 
                        }
                        $get_sections = $get_sections->get(); 

                        $data = [];
                        $data['classes'] = $classes;
                        $data['sections'] = $get_sections;

                        if($data) { 
                            return response()->json([ 'status' => 1, 'message' => 'Class Sections', 'data'=>$data]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Class Sections']);
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
 
    // Communication Classes Section Scholars
    public function getCommnClassSectionScholars(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : ''; 
                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  
                        $classids = (new AdminRoleController())->getApiSchoolRoleClasses($userid);

                        $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                            ->leftjoin('student_class_mappings', 'students.user_id', 'student_class_mappings.user_id')
                            ->leftjoin('classes', 'classes.id', 'students.class_id')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->where('users.status','ACTIVE')->where('users.delete_status',0)
                            ->where('student_class_mappings.academic_year', $batch)
                            ->where('users.user_type','STUDENT')->where('users.school_college_id', $school_id)
                            ->where('student_class_mappings.school_id', $school_id);
                        if(count($classids)>0) {
                            $get_student->whereIn('students.class_id', $classids);
                        }
                        if($class_id > 0) {
                            $get_student->where('students.class_id', $class_id);
                        }
                        if($section_id > 0) {
                            $get_student->where('students.section_id', $section_id);
                        }

                        if(!empty(trim($search))) { 
                            $get_student->whereRaw(' ( name like "%'.$search.'%"  or section_name like "%'.$search.'%" or class_name like "%'.$search.'%" ) '); 
                        }

                        $get_student = $get_student->select('students.*')->skip($page_no)->take($limit)->get(); 

                        if($get_student->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Students', 'data' => $get_student]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Students']); 
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

    // Communication SMS Templates
    public function getCommnSMSTemplates(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $get_template = DltTemplate::where('status','ACTIVE')
                            ->select('id','name','content','type', 'no_of_variables')->get();  
                        if($get_template->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'SMS Templates', 'data' => $get_template ]);
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'NO SMS Templates']);
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

    // POST Communication
    public function postCommunicationPost(Request $request) {
        try {    

            //$inputJSON = file_get_contents('php://input');   

            $input = $request->all(); //json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'title',  'message', 'title_push', 'message_push', 'category', 
                'batch', 'post_type' ];   //, 'bg_color', 'req_ack', 'schedule_date', 'youtube_link'

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    

                $schedule_date = ((isset($input) && isset($input['schedule_date']))) ? $input['schedule_date'] : ''; 

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;   
                        if(empty(trim($schedule_date))) {
                            $inarr["schedule_date"] = date('Y-m-d H:i:s');
                        }
                        $inarr = array_merge($inarr, $input);  //  echo "<pre>"; print_r($inarr); print_r($input); exit;
                        $ret = (new AdminController())->createPost($request, $inarr); 
  
                        return $ret;
                         
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

    // Update Post Communication
    public function updateCommunicationPost(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'post_id',  'title', 'message', 
                'title_push', 'message_push',  'category', 'bg_color', 'req_ack', 'youtube_link', 'schedule_date' ];   

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $post_id = ((isset($input) && isset($input['post_id']))) ? $input['post_id'] : 0; 

                $title = ((isset($input) && isset($input['title']))) ? $input['title'] : '';
                $message = ((isset($input) && isset($input['message']))) ? $input['message'] : '';
                $title_push = ((isset($input) && isset($input['title_push']))) ? $input['title_push'] : '';
                $message_push = ((isset($input) && isset($input['message_push']))) ? $input['message_push'] : '';
                $category = ((isset($input) && isset($input['category']))) ? $input['category'] : 0;
                $bg_color = ((isset($input) && isset($input['bg_color']))) ? $input['bg_color'] : 0;
                $req_ack = ((isset($input) && isset($input['req_ack']))) ? $input['req_ack'] : 0;
                $youtube_link = ((isset($input) && isset($input['youtube_link']))) ? $input['youtube_link'] : '';
                $schedule_date = ((isset($input) && isset($input['schedule_date']))) ? $input['schedule_date'] : date('Y-m-d H:i:s');
                
                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $post_id > 0) {  
                        $post_new = DB::table('communication_posts')->where('id', $post_id)->where('posted_by', $school_id)->first();

                        if(!empty($post_new)) { 

                            $schedule_date = $post_new->notify_datetime; 

                            if(!empty($schedule_date)) { 
                                if(strtotime($schedule_date) < strtotime(date('Y-m-d H:i:s'))) {
                                    $schedule_date = date('Y-m-d H:i:s');
                                }
                            }
                            $data = [
                                'title' => $title,
                                'message' => $message,
                                'title_push' => $title_push,
                                'message_push' => $message_push,
                                'category_id' => $category,
                                'background_id' => $bg_color,
                                'request_acknowledge' => $req_ack,
                                'youtube_link' => $youtube_link,
                                'notify_datetime' => $schedule_date,
                                'updated_by' => $userid,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]; 

                            DB::table('communication_posts')->where('id', $post_id)->where('posted_by', $school_id)
                                ->update($data);

                            return  response()->json(['status'=>1,'message'=>'Post updated Successfully']);

                        } else {
                            return response()->json(['status'=>0,'message'=>'Invalid Post']);
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

    // SMS Communication
    public function postCommunicationSms(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'template_id',  'category', 'batch', 
                'post_type', 'smart_sms',  'send_type', 'final_content', 'vars' ];   

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $schedule_date = ((isset($input) && isset($input['schedule_date']))) ? $input['schedule_date'] : ''; 
                
                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;  
                        $input =  $request->all(); 
                        if(empty(trim($schedule_date))) {
                            $inarr["schedule_date"] = date('Y-m-d H:i:s');
                        }

                        $inarr = array_merge($inarr, $input);  
                        $ret = (new AdminController())->createPostSms($request, $inarr); 
  
                        return $ret;
                         
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

    // POST Communication Post
    public function deleteCommunicationPost(Request $request) {
        try {    

            //$inputJSON = file_get_contents('php://input');   

            $input = $request->all(); //json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'post_id' ];    

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $post_id = ((isset($input) && isset($input['post_id']))) ? $input['post_id'] : 0;     
                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $post_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;    
  
                        DB::table('communication_posts')->where('id', $post_id)
                            ->update(['status' => 'DELETED', 'delete_status'=>1, 'updated_by'=>$userid, 'updated_at'=>date('Y-m-d H:i:s')]);

                        return response()->json([ 'status' => 1, 'message' => 'Post deleted successfully']); 
                         
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

    // Delete Communication Staff
    public function deleteCommunicationPostStaff(Request $request) {
        try {    

            $input = $request->all(); //json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'post_id' ];    

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $post_id = ((isset($input) && isset($input['post_id']))) ? $input['post_id'] : 0;     
                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $post_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;    
  
                        DB::table('communication_posts_staff')->where('id', $post_id)
                            ->update(['status' => 'INACTIVE', 'delete_status'=>1, 'updated_by'=>$userid, 'updated_at'=>date('Y-m-d H:i:s')]);

                        return response()->json([ 'status' => 1, 'message' => 'Post deleted successfully']); 
                         
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

    // Delete Communication Staff
    public function postCommunicationPostStaff(Request $request) {
        try {    

            //$inputJSON = file_get_contents('php://input');   

            $input = $request->all(); //json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'title',  'message', 'title_push', 'message_push', 'category', 
                'batch', 'post_type' ];   //, 'bg_color', 'req_ack', 'schedule_date', 'youtube_link'

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    

                $schedule_date = ((isset($input) && isset($input['schedule_date']))) ? $input['schedule_date'] : ''; 

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;   
                        if(empty(trim($schedule_date))) {
                            $inarr["schedule_date"] = date('Y-m-d H:i:s');
                        }
                        $inarr = array_merge($inarr, $input);  //  echo "<pre>"; print_r($inarr); print_r($input); exit;
                        $ret = (new AdminController())->createPostStaff($request, $inarr); 
  
                        return $ret;
                         
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

    // Homework Communication
    public function postCommunicationHws(Request $request) {
        //try {    

            //$inputJSON = file_get_contents('php://input');   

            $input = $request->all(); //json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_id',  'section_id', 'sms_alert', 
                'subject_id', 'hw_description',  'hw_date', 'hw_submission_date', 'approve_status' ];    // hw_attachment, //dt_attachment

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $homework_id = ((isset($input) && isset($input['homework_id']))) ? $input['homework_id'] : 0;    

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;  
                        $input =  $request->all(); 
                        $inarr["id"] = $homework_id;
                        $inarr = array_merge($inarr, $input);  

                        if($homework_id > 0) {
                            $ret = (new AdminController())->updatePostHWs($request, $inarr); 
                        }   else {
                            $ret = (new AdminController())->createPostHWs($request, $inarr); 
                        } 
  
                        return $ret;
                         
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

    // Communication Posts List
    public function getCommnPostList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $category_id = ((isset($input) && isset($input['category_id']))) ? $input['category_id'] : 0; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $from_date = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';
                $to_date = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';
                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $posts = CommunicationPost::where('delete_status', 0)
                            ->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id)
                            ->orderby('id', 'desc');  

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('communication_posts.notify_datetime >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                            $posts->whereRaw('communication_posts.notify_datetime <= ?', [$to_date]); 
                        }
                        if($category_id > 0) { 
                            $posts->where('communication_posts.category_id', $category_id); 
                        } 
                        if(!empty(trim($search))) { 
                            $posts->whereRaw(' ( title like "%'.$search.'%" or message like "%'.$search.'%" ) '); 
                        }

                        $posts = $posts->skip($page_no)->take($limit)->get(); 

                        if($posts->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Communication Posts', 'data' => $posts]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Communication Posts']); 
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

    // Communication Posts List
    public function getCommnPostStaffList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $category_id = ((isset($input) && isset($input['category_id']))) ? $input['category_id'] : 0; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $from_date = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';
                $to_date = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';
                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $posts = CommunicationPostStaff::where('delete_status', 0)
                            ->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id)
                            ->orderby('id', 'desc');  

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('communication_posts_staff.notify_datetime >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                            $posts->whereRaw('communication_posts_staff.notify_datetime <= ?', [$to_date]); 
                        }
                        if($category_id > 0) { 
                            $posts->where('communication_posts_staff.category_id', $category_id); 
                        } 
                        if(!empty(trim($search))) { 
                            $posts->whereRaw(' ( title like "%'.$search.'%" or message like "%'.$search.'%" ) '); 
                        }

                        $posts = $posts->skip($page_no)->take($limit)->get(); 

                        if($posts->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Communication Staff Posts', 'data' => $posts]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Communication Staff Posts']); 
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

    // Communication Post Status List
    public function getCommnPostStatusList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'post_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $post_id = ((isset($input) && isset($input['post_id']))) ? $input['post_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   

                        $post  = DB::table('communication_posts')->where('id', $post_id)->first();

                        $users_qry = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                                ->leftjoin('classes', 'students.class_id', 'classes.id')
                                ->leftjoin('sections', 'students.section_id', 'sections.id') 
                                ->where('users.user_type', 'STUDENT')->where('users.school_college_id', $school_id)
                                ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)   
                                ->select('users.id', 'users.name', 'users.mobile',  'users.fcm_id',  'users.is_app_installed',
                                        'classes.class_name', 'sections.section_name'); 

                        $is_receivers = '';
                        if(!empty($post)) {
                            $post_type = $post->post_type;
                            $receiver_end = $post->receiver_end;
                            if($post_type == 1) { // section ids
                                $section_ids = $post->receiver_end;
                                if(!empty($section_ids)) {
                                    $section_ids = explode(',', $section_ids);
                                    $section_ids = array_unique($section_ids);
                                    $section_ids = array_filter($section_ids);
                                    if(count($section_ids) > 0) {
                                        $users_qry->whereIn('students.section_id', $section_ids); 
                                    }
                                }
                            }   else if($post_type == 2) { // user ids
                                $user_ids = $post->receiver_end;
                                if(!empty($user_ids)) {
                                    $user_ids = explode(',', $user_ids);
                                    $user_ids = array_unique($user_ids);
                                    $user_ids = array_filter($user_ids);
                                    if(count($user_ids) > 0) {
                                        $users_qry->whereIn('students.user_id', $user_ids); 
                                    }
                                }
                            }   else if($post_type == 3) { // all user ids 

                            }   else if($post_type == 4) { // group ids 
                                $group_ids = $post->receiver_end;
                                if(!empty($group_ids)) {
                                    $user_ids = [];
                                    $group_ids = explode(',', $group_ids);
                                    $group_ids = array_unique($group_ids);
                                    $group_ids = array_filter($group_ids);
                                    if(count($group_ids) > 0) {
                                        $groups = DB::table('communication_groups')->where('status', 'ACTIVE')->whereIn('id', $group_ids)->get();
                                        if($groups->isNotEmpty()) {
                                            foreach($groups as $grp) {   
                                                $uids = $grp->members;
                                                if(!empty($uids)) {
                                                    $uids = explode(',', $uids);
                                                    $uids = array_unique($uids);
                                                    $uids = array_filter($uids);
                                                    $user_ids = array_merge($user_ids, $uids);
                                                }
                                            }
                                        }
                                    }
                                    if(count($user_ids) > 0) {
                                        $users_qry->whereIn('students.user_id', $user_ids); 
                                    }
                                }
                            }  
                        }  

                        $users = $users_qry->orderBy('students.user_id', 'desc')->skip($page_no)->take($limit)->get(); 

                        if($users->isNotEmpty()) {
                            foreach($users as $uk=>$usr) {
                                $notify = DB::table('notifications')->where('type_no', 4)->where('post_id', $post_id)
                                    ->where('user_id', $usr->id)->first();
                                $users[$uk]->notify = $notify;
                            }
                            return response()->json([ 'status' => 1, 'message' => 'Communication Post Status', 'data' => $users]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Communication Post Status']); 
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


    // Communication Sms List
    public function getCommnSMSList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $category_id = ((isset($input) && isset($input['category_id']))) ? $input['category_id'] : 0; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $from_date = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';
                $to_date = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';
                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $posts = CommunicationSms::where('delete_status', 0)
                            ->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id)
                            ->orderby('id', 'desc');  

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('communication_sms.notify_datetime >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                            $posts->whereRaw('communication_sms.notify_datetime <= ?', [$to_date]); 
                        }
                        if($category_id > 0) { 
                            $posts->where('communication_sms.category_id', $category_id); 
                        } 
                        if(!empty(trim($search))) { 
                            $posts->whereRaw(' ( content like "%'.$search.'%" ) '); 
                        }

                        $posts = $posts->skip($page_no)->take($limit)->get(); 

                        if($posts->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Communication Sms', 'data' => $posts]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Communication Sms']); 
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

    // Communication HWs List
    public function getCommnHWSList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $approval_status_id = ((isset($input) && isset($input['approval_status_id']))) ? $input['approval_status_id'] : ''; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $from_date = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';
                $to_date = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';
                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $is_attachment = ((isset($input) && isset($input['is_attachment']))) ? $input['is_attachment'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $posts = PostHomeworks::whereIn('status', ["ACTIVE"])->where('school_id', $school_id);  

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('homeworks.hw_date >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime($to_date));
                            $posts->whereRaw('homeworks.hw_date <= ?', [$to_date]); 
                        } 

                        if(!empty(trim($approval_status_id))) { 
                            $posts->where('homeworks.approve_status', $approval_status_id); 
                        } 

                        if($class_id > 0) {
                            $posts->where('homeworks.class_id', $class_id); 
                        }

                        if($section_id > 0) {
                            $posts->where('homeworks.section_id', $section_id); 
                        }

                        if($subject_id > 0) {
                            $posts->where('homeworks.subject_id', $subject_id); 
                        } 

                        if($is_attachment == 1) {
                            $posts->where('homeworks.hw_attachment', '!=', ''); 
                        } else if($is_attachment == 2) {
                            $posts->whereRaw('( homeworks.hw_attachment = "" or homeworks.hw_attachment is NULL )'); 
                        }

                        $posts = $posts->groupby('ref_no')->orderby('id', 'desc')
                            ->skip($page_no)->take($limit)->get(); 

                        if($posts->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Homeworks', 'data' => $posts]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Homeworks']); 
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

    // Communication Survey List
    public function getCommnSurveyList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $from_date = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';
                $to_date = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';

                $survey_id = ((isset($input) && isset($input['survey_id']))) ? $input['survey_id'] : 0;

                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $posts = Survey::where('status','!=', 'DELETED')->where('school_id', $school_id)
                            ->orderby('id', 'desc');  

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('survey.expiry_date >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                            $posts->whereRaw('survey.expiry_date <= ?', [$to_date]); 
                        } 
                        if(!empty(trim($search))) { 
                            $posts->whereRaw(' ( survey_question like "%'.$search.'%" or survey_option1 like "%'.$search.'%" or survey_option2 like "%'.$search.'%" ) '); 
                        }

                        if($survey_id > 0) {
                            $posts = $posts->where('id', $survey_id)->get(); 

                            if($posts->isNotEmpty()) {
                                return response()->json([ 'status' => 1, 'message' => 'Survey Details', 'data' => $posts[0]]); 
                            } else {
                                return response()->json([ 'status' => 0, 'message' => 'No Survey Details']); 
                            }
                        }   else {
                            $posts = $posts->skip($page_no)->take($limit)->get(); 

                            if($posts->isNotEmpty()) {
                                return response()->json([ 'status' => 1, 'message' => 'Survey List', 'data' => $posts]); 
                            } else {
                                return response()->json([ 'status' => 0, 'message' => 'No Survey List']); 
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

    // POST Communication Survey
    public function postCommunicationSurvey(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'survey_question',  'survey_option1', 'survey_option2', 'expiry_date' ];    

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $survey_id  = ((isset($input) && isset($input['survey_id']))) ? $input['survey_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        $inarr = [];    
                        $inarr["user_id"] = $userid;
                        $inarr["x-api-key"] = $api_token;  
                        $inarr["survey_id"] = $survey_id;   
                        $inarr = array_merge($inarr, $input);  // echo "<pre>"; print_r($inarr); print_r($input); exit;
                        $ret = (new AdminController())->createSurvey($request, $inarr); 
  
                        return $ret;
                         
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

    // Communication Survey Delete
    public function deleteCommunicationSurvey(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'survey_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $survey_id = ((isset($input) && isset($input['survey_id']))) ? $input['survey_id'] : 0;    

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $survey_id > 0) {   

                        $get_data = Survey::where('id', $survey_id)->where('school_id', $school_id)->get();
                        if ($get_data->isNotEmpty()) { 
                            Survey::where('id', $survey_id)->update(['status'=>'DELETED', 'updated_at'=>date('Y-m-d H:i:s')]);
                                return response()->json(['status' => 1, 'message' => 'Survey Deleted Successfully']); 
                        } else {
                             return response()->json(['status' => 0, 'data' => [], 'message' => 'No Survey']);
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
    
    public function updateCommunicationSurvey(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'survey_question',  'survey_option1', 'survey_option2', 'expiry_date', "survey_id" ];    

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $survey_id  = ((isset($input) && isset($input['survey_id']))) ? $input['survey_id'] : 0;  

                $survey_question  = ((isset($input) && isset($input['survey_question']))) ? $input['survey_question'] : '';  
                $survey_option1  = ((isset($input) && isset($input['survey_option1']))) ? $input['survey_option1'] : '';    
                $survey_option2  = ((isset($input) && isset($input['survey_option2']))) ? $input['survey_option2'] : '';   
                $survey_option3  = ((isset($input) && isset($input['survey_option3']))) ? $input['survey_option3'] : '';    
                $survey_option4  = ((isset($input) && isset($input['survey_option4']))) ? $input['survey_option4'] : '';  
                $expiry_date  = ((isset($input) && isset($input['expiry_date']))) ? $input['expiry_date'] : '';    

                if(empty($expiry_date)) {
                    $expiry_date = date('Y-m-d');
                }

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   

                        if ($survey_id > 0) {
                            $survey = Survey::find($survey_id);
                            $survey->updated_at = date('Y-m-d H:i:s');
                            $survey->updated_by = $userid;

                            $survey->school_id = $school_id; 
                            $survey->survey_question = $survey_question; 
                            $survey->survey_option1 = $survey_option1;
                            $survey->survey_option2 = $survey_option2;
                            $survey->survey_option3 = $survey_option3;
                            $survey->survey_option4 = $survey_option4;
                            $survey->expiry_date = $expiry_date; 

                            $user_type = DB::table('users')->where('id', $userid)->value('user_type');
                            if($user_type == "SCHOOL" ) {
                                $survey->status='ACTIVE';
                            }   else {
                                $survey->status='PENDING';
                            } 

                            $survey->save(); 

                            return response()->json(['status'=>1,'message'=>'Survey Saved Successfully']);
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid Survey']);
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

    // Communication Remarks / Rewards List
    public function getCommnRemarkRewardsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';
                $mindate = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';
                $maxdate = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';

                $type = ((isset($input) && isset($input['type']))) ? $input['type'] : "REMARK"; // / REWARD
                if(empty($type)) {
                    $type = "REMARK";
                }
                if(empty($batch)) { 
                    $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                    if(!empty($settings)) {
                        $acadamic_year = $batch = trim($settings->acadamic_year);
                    } 
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $remarksqry = UserRemarks::leftjoin('users', 'user_remarks.user_id', 'users.id') 
                            ->leftjoin('students', 'user_remarks.user_id', 'students.user_id') 
                            ->leftjoin('classes', 'students.class_id', 'classes.id') 
                            ->leftjoin('sections', 'students.section_id', 'sections.id') 
                            ->where('user_remarks.school_id', $school_id)->where('user_remarks.remark_type', $type)
                            ->where('user_remarks.status', 'ACTIVE')->select('users.name','user_remarks.*', 
                                    'classes.class_name', 'sections.section_name'); 
 

                        if (!empty($search)) { 
                            $remarksqry->whereRaw('( users.name like "%'.$search . '%" OR users.mobile like "%'.$search . '%" OR users.email like "%'.$search . '%" OR users.name_code like "%'.$search . '%"  OR user_remarks.remark_description like "%'.$search . '%" OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" ) '); 
                        } 

                        if($class_id > 0){
                            $remarksqry->where('students.class_id',$class_id);  
                        }
                        if($section_id > 0){
                            $remarksqry->where('students.section_id',$section_id);  
                        }
                        if($student_id > 0) {
                            $remarksqry->where('students.user_id',$student_id);  
                        }
                        if(!empty(trim($mindate))) {
                            $mindate = date('Y-m-d', strtotime($mindate));
                            $remarksqry->where('user_remarks.created_at', '>=', $mindate); 
                
                        }
                        if(!empty(trim($maxdate))) {
                            $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                            $remarksqry->where('user_remarks.created_at', '<=', $maxdate);  
                        }

                        if(!empty($status)){
                            $remarksqry->where('user_remarks.status',$status); 
                        } 
                        
                        $orderby = 'user_remarks.id'; 
                        $dir = 'DESC'; 

                        $remarks = $remarksqry->skip($page_no)->take($limit)->orderby($orderby, $dir)->get();

                        if($remarks->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Remarks List', 'data' => $remarks]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Remarks List']); 
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

    // Scholars List
    public function getScholarsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';
                $is_app_installed = ((isset($input) && isset($input['is_app_installed']))) ? $input['is_app_installed'] : ''; 

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $users_qry = User::leftjoin('countries', 'countries.id', 'users.country')
                            ->leftjoin('states', 'states.id', 'users.state_id')
                            ->leftjoin('districts', 'districts.id', 'users.city_id')
                            ->leftjoin('students', 'students.user_id', 'users.id')
                            ->leftjoin('classes', 'classes.id', 'students.class_id')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->where('user_type', 'STUDENT')->whereNuLL('alumni_status')
                            ->where('users.delete_status',0)
                            ->where('students.delete_status',0)
                            ->where('users.school_college_id', $school_id)
                            ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                                'students.roll_no', 'students.admission_no', 'students.class_id', 'students.section_id', 
                                'students.father_name', 'students.address', 'classes.class_name', 'sections.section_name');
 
                        if(!empty($status_id)){
                            $users_qry->where('users.status',$status_id); 
                        }
                        if(!empty($is_app_installed)){
                            if($is_app_installed == 'yes') { $is_app_installed = 1; }
                            else { $is_app_installed = 0; }
                            $users_qry->where('users.is_app_installed',$is_app_installed); 
                        }
                        if($section_id>0){
                            $users_qry->where('students.section_id',$section_id); 
                        }
                        if($class_id>0){
                            $users_qry->where('students.class_id',$class_id); 
                        }
                        if(!empty(trim($search))) { 
                            $users_qry->whereRaw(' ( users.name like "%'.$search.'%" or users.mobile like "%'.$search.'%" ) '); 
                        }

                        $users = $users_qry->orderBy('users.id', 'desc')->skip($page_no)->take($limit)->get(); 

                        if($users->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Scholars List', 'data' => $users]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Scholars List']); 
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

    // Scholars Add
    public function postScholarsadd(Request $request) {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'name', 'password', 'admission_no', 'class_id', 'section_id', 
                'mobile', 'gender', 'dob', 'status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        $status = ((isset($input) && isset($input['status']))) ? $input['status'] : '';   

                        $request->request->add($input);
                           
                        $ret = (new AdminController())->postStudent($request); 

                        return $ret;
                    }   else {
                        return response()->json([ 'status' => "FAILED", 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => "FAILED", 'message' => $error]);
            }
        /*}   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } */

    }

    // Scholars additional
    public function postScholarsadditional(Request $request) {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $id = ((isset($input) && isset($input['id']))) ? $input['id'] : 0; 

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $id > 0) {   
                        
                        $request->request->add($input);
                           
                        $ret = (new AdminController())->postStudentAdditional($request); 

                        return $ret;
                    }   else {
                        return response()->json([ 'status' => "FAILED", 'message' => 'Invalid inputs']);
                    }
                }
            }    else {
                return response()->json([ 'status' => "FAILED", 'message' => $error]);
            }
        /*}   catch(\Throwable $th) {
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);
        } */

    }

    // Pre Admission Scholars List
    public function getPreScholarsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : ''; 

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $users_qry = PreAdmissionStudent::leftjoin('countries', 'countries.id', 'pre_admission_students.country_id')
                            ->leftjoin('states', 'states.id', 'pre_admission_students.state_id')
                            ->leftjoin('districts', 'districts.id', 'pre_admission_students.city_id') 
                            ->leftjoin('classes', 'classes.id', 'pre_admission_students.class_id')
                            ->leftjoin('sections', 'sections.id', 'pre_admission_students.section_id') 
                            ->where('pre_admission_students.delete_status',0) 
                            ->select('pre_admission_students.*', 'countries.name as country_name', 'states.state_name', 
                                'districts.district_name', 'classes.class_name', 'sections.section_name');
 
                        if(!empty($status_id)){
                            $users_qry->where('pre_admission_students.status',$status_id); 
                        } 
                        if($section_id>0){
                            $users_qry->where('pre_admission_students.section_id',$section_id); 
                        }
                        if($class_id>0){
                            $users_qry->where('pre_admission_students.class_id',$class_id); 
                        }
                        if(!empty(trim($search))) { 
                            $users_qry->whereRaw(' ( pre_admission_students.name like "%'.$search.'%" or pre_admission_students.mobile like "%'.$search.'%" ) '); 
                        }

                        $users = $users_qry->orderBy('pre_admission_students.id', 'desc')->skip($page_no)->take($limit)->get(); 

                        if($users->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Pre Admission Scholars List', 'data' => $users]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Pre Admission Scholars List']); 
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

    // Alumni Scholars List
    public function getAlumniScholarsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : ''; 
                $is_app_installed = ((isset($input) && isset($input['is_app_installed']))) ? $input['is_app_installed'] : ''; 
                $alumni_status = ((isset($input) && isset($input['alumni_status']))) ? $input['alumni_status'] : ''; 

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $users_qry = User::leftjoin('student_alumnis', 'student_alumnis.user_id', 'users.id')
                            ->leftjoin('classes', 'classes.id', 'student_alumnis.class_id')
                            ->leftjoin('sections', 'sections.id', 'student_alumnis.section_id')
                            ->leftjoin('students', 'users.id', 'students.user_id')
                            ->where('user_type', 'STUDENT')->whereNotNULL('users.alumni_status')
                            ->where('users.delete_status',0)
                            ->whereRAW('student_alumnis.id = (SELECT MAX(id) FROM student_alumnis WHERE student_alumnis.user_id = users.id) ')
                            ->select('users.*', 'student_alumnis.class_id', 'student_alumnis.section_id', 'students.father_name', 
                                 'classes.class_name', 'sections.section_name', 'student_alumnis.academic_year');
 
                        if(!empty($status_id)){
                            $users_qry->where('users.status',$status_id); 
                        } 
                        if(!empty($alumni_status)){
                            $users_qry->where('student_alumnis.alumni_status',$alumni_status); 
                        } 
                        if(!empty($is_app_installed)){
                            if($is_app_installed == 'yes') { $is_app_installed = 1; }
                            else { $is_app_installed = 0; }
                            $users_qry->where('users.is_app_installed',$is_app_installed); 
                        }
                        if($section_id>0){
                            $users_qry->where('students.section_id',$section_id); 
                        }
                        if($class_id>0){
                            $users_qry->where('students.class_id',$class_id); 
                        }
                        if(!empty(trim($search))) { 
                            $users_qry->whereRaw(' ( users.name like "%'.$search.'%" or users.mobile like "%'.$search.'%" ) '); 
                        }

                        $users = $users_qry->orderBy('users.id', 'desc')->skip($page_no)->take($limit)->get(); 

                        if($users->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Alumni Scholars List', 'data' => $users]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Alumni Scholars List']); 
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

    // Scholar Details
    public function getScholarsDetails(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'scholar_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $scholar_id = ((isset($input) && isset($input['scholar_id']))) ? $input['scholar_id'] : 0;   
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';   

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $scholar_id>0) {  
                        $user = User::with('userdetails')->where('id', $scholar_id)
                            ->select('id', 'school_college_id', 'reg_no', 'user_type',  'state_id', 'city_id', 'country',
                                'admission_no','name', 'last_name', 'email', 'gender', 'dob', 'country_code', 
                                'mobile','code_mobile',  'mobile1','codemobile1', 'emergency_contact_no', 'last_login_date', 
                                'last_app_opened_date', 'user_source_from', 'api_token', 'api_token_expiry', 'is_password_changed',
                                'notification_status', 'joined_date', 'profile_image', 'status', 'alumni_status')->first(); 

                        if(!empty($user)) {
                            if(empty($batch)) {
                                $batch = DB::table('admin_settings')->where('school_id', $school_id)->value('acadamic_year');
                            }
                            if($user->user_type == 'SCHOOL') {
                                $user->is_school_college_id = $user->id;
                            } else {
                                $user->is_school_college_id = $user->school_college_id;
                            }

                            $user_remarks = UserRemarks::where('user_id', $scholar_id)->where('remark_type', 'REMARK')
                                ->where('status', 'ACTIVE')->orderby('id', 'desc')->get();
                            if($user_remarks->isNotEmpty()) {} else { $user_remarks = []; }

                            $user_rewards = UserRemarks::where('user_id', $scholar_id)->where('remark_type', 'REWARD')
                                ->where('status', 'ACTIVE')->orderby('id', 'desc')->get();
                            if($user_rewards->isNotEmpty()) {} else { $user_rewards = []; }

                            $user->user_remarks = $user_remarks;
                            $user->user_rewards = $user_rewards;

                            $user->fee_details = '';
                            $user->feedata = '';

                            $request->request->add(['school_id' => $school_id, 'student_id' => $scholar_id, 'batch' => $batch ]);
                           
                            $ret = (new AdminController())->filterFeeCollections($request);
 
                            if(!empty($ret)) { 
                                $return  = $ret->getOriginalContent(); 
                                if(isset($return['student']) && !empty($return['student'])) {
                                    $fee_details = $return['student'];  
                                    $user->fee_details = $fee_details;
                                } 
                                if(isset($return['feedata']) && !empty($return['feedata'])) {
                                    $feedata = $return['feedata'];  
                                    $user->feedata = $feedata;
                                }  
                            } 

                            $user->exam_details = []; $class_id =  $section_id = 0;

                            /*  EXAMS */
                            $user_details_get = DB::table('students')
                                ->leftjoin('sections', 'sections.id', 'students.section_id')
                                ->select('sections.mapped_subjects', 'students.class_id', 'students.section_id')
                                ->where('students.user_id', $scholar_id)->first();  
                           
                            if(!empty($user_details_get)) {
                                $class_id = $user_details_get->class_id; 
                                $section_id = $user_details_get->section_id; 
                            } 
                            if($class_id > 0 && $section_id > 0) {
                                $exams = DB::table('exams')
                                    ->leftjoin('examinations', 'examinations.id', 'exams.examination_id')
                                    ->leftjoin('exam_sessions', 'exams.id', 'exam_sessions.exam_id')
                                    ->leftjoin('classes', 'classes.id', 'exam_sessions.class_id')
                                    ->where('examinations.batch', $batch)
                                    ->where('exams.class_ids', $class_id)->where('exams.examination_id', '>',0)
                                    ->whereIn('exams.section_ids', [0, $section_id]);
                                
                                $exams = $exams->where('exam_sessions.status', 'ACTIVE')
                                    ->select("examinations.exam_name", "exams.exam_name as exname", "exams.id", "exam_startdate", 
                                        DB::RAW(' DATE_FORMAT(exam_startdate, "%Y-%m") as monthyear'), 'class_id', 'section_id')
                                    ->groupby('exams.id')->orderby('exams.id', 'asc')->get();

                                if($exams->isNotEmpty()) {
                                    foreach($exams as $ek => $ev) {

                                        $exam_id = $ev->id;
                                        MarksEntry::$exam_id = $exam_id;
                                        MarksEntry::$class_id = $class_id;

                                        User::$monthyear = $ev->monthyear;
                                        User::$class_id = $class_id;
                                        User::$section_id = $section_id;
                                        User::$exam_id = $exam_id;

                                        $section_id1 = DB::table('exams')->where('id', $exam_id)->value('section_ids');

                                        MarksEntry::$section_id = $section_id;
                                        $students = User::with('marksentry')
                                            ->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                                            ->leftjoin('students', 'students.user_id', 'users.id')
                                            //->whereRaw( "'".$monthyear."' BETWEEN from_month and to_month ")
                                            ->where('student_class_mappings.class_id', $class_id);
                 
                                        $students->where('student_class_mappings.section_id', $section_id);  
                                        $students->where('student_class_mappings.user_id',$scholar_id); 

                                        $students = $students->where('student_class_mappings.status', 'ACTIVE')
                                            ->where('user_type', 'STUDENT')
                                            ->where('student_class_mappings.user_id', '>', 0)
                                            ->select('users.id', 'name', 'email', 'mobile', 'students.admission_no')
                                            ->orderby('name')->get();
                                        if($students->isNotEmpty()) {
                                            $exams[$ek]->exam_result = $students->toArray();
                                        }   else {
                                            $exams[$ek]->exam_result = [];
                                        }
                                    }  
                                    $exams = $exams->toArray();

                                    $user->exam_details = $exams; 
                                }
                            }
                            /*  EXAMS */

                            return response()->json([ 'status' => 1, 'message' => 'Scholar Details', 'data' => $user]);
                            //echo "<pre>"; print_r($ret); exit;
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

    // Staffs List
    public function getStaffsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : ''; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $users_qry = User::with('teachers')->leftjoin('countries', 'countries.id', 'users.country')
                            ->leftjoin('states', 'states.id', 'users.state_id')
                            ->leftjoin('districts', 'districts.id', 'users.city_id')
                            ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                            ->leftjoin('classes', 'classes.id', 'teachers.class_tutor')
                            ->leftjoin('sections', 'sections.id', 'teachers.section_id')
                            ->where('user_type', 'TEACHER')->where('school_college_id', $school_id)
                            ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                                'teachers.emp_no', 'teachers.date_of_joining', 'teachers.qualification', 'teachers.exp', 
                                'teachers.post_details', 'teachers.subject_id', 'teachers.class_id', 'teachers.class_tutor',  
                                'teachers.section_id', 'teachers.father_name', 'teachers.address', 'classes.class_name', 
                                'sections.section_name');
 
                        if(!empty($status_id)){
                            $users_qry->where('users.status',$status_id); 
                        } 
                        if($section_id>0){
                            $users_qry->where('teachers.section_id',$section_id); 
                        }
                        if($class_id>0){
                            $users_qry->where('teachers.class_id',$class_id); 
                        }
                        if($subject_id>0){
                            $users_qry->whereRAW(' FIND_IN_SET('.$subject_id.', teachers.subject_id) ');
                        }
                        if(!empty(trim($search))) { 
                            $users_qry->whereRaw(' ( users.name like "%'.$search.'%" or users.mobile like "%'.$search.'%" ) '); 
                        }

                        $users = $users_qry->orderBy('users.id', 'desc')->skip($page_no)->take($limit)->get(); 

                        if($users->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Staffs List', 'data' => $users]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Staffs List']); 
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

    // Staffs List
    public function getClassTutorsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : ''; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   

                        $sections = Sections::leftjoin('classes', 'sections.class_id', 'classes.id')
                            ->where('classes.school_id',$school_id)->where('classes.school_id',$school_id)
                            ->where('classes.status','=','ACTIVE')->where('sections.status','=','ACTIVE')
                            ->select('sections.*', 'classes.class_name')->orderby('classes.position', 'Asc')
                            ->orderby('sections.position', 'Asc')->get();

                        if($sections->isNotEmpty()) {
                            foreach($sections as $sk=>$sv) {
                                $teacher_id = DB::table('class_teachers')->leftjoin('users', 'class_teachers.teacher_id', 'users.id')
                                    ->where('class_id', $sv->class_id)
                                    ->where('section_id', $sv->id)->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                                    ->where('users.school_college_id',$school_id)->value('teacher_id');
                                $sections[$sk]->teacher_id = $teacher_id;
                            }
                        }

                        $teacher = DB::table('teachers')->leftjoin('users','users.id','teachers.user_id')
                            ->where('users.user_type','TEACHER')->where('users.status','ACTIVE')->where('users.delete_status',0)
                            ->where('users.school_college_id',$school_id)
                            ->orderby('users.name', 'asc')->get();  

                        if($sections->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Staffs List', 'data' => $sections, 'teachers' => $teacher]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Staffs List']); 
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

    // Update Class teacher List
    public function postClassTutor(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'teacher_id', 'section_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $teacher_id = ((isset($input) && isset($input['teacher_id']))) ? $input['teacher_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        /*if($teacher_id > 0)  {} else {
                            return response()->json([ 'status' => 0,  'message' => 'Please select the Teacher']);
                        }*/

                        if($section_id > 0)  {} else {
                            return response()->json([ 'status' => 0, 'message' => 'Please select the Section']);
                        }

                        if($teacher_id > 0) {
                             $teacher_chk = DB::table('class_teachers')->where('teacher_id', $teacher_id)
                                ->where('status', 'ACTIVE')->first(); 
                             if(!empty($teacher_chk)) {
                                if($teacher_chk->section_id != $section_id) {
                                    return response()->json(['status' => 0, 'message' => 'Class Already Assigned for this Teacher']);
                                }
                             } 
                             $status = 'ACTIVE';
                         } else {
                            $teacher_id = 0;
                            $status = 'INACTIVE';
                         } 

                         $teachers = ClassTeacher::where('section_id', $section_id)->first();
                         if(empty($teachers)) {
                             $teachers = new ClassTeacher();
                         } else {
                             $teachers = ClassTeacher::find($teachers->id);
                         }

                         $teachers->teacher_id = $teacher_id;
                         $teachers->class_id = DB::table('sections')->where('id', $section_id)->value('class_id');
                         $teachers->section_id = $section_id;
                         $teachers->status = $status;
                         $teachers->save();

                        return response()->json(['status' => 1, 'message' => 'Class Teachers Saved Successfully']);

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

    // subject staffs List
    public function getSubjectStaffsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : ''; 
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : ''; 
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0; 
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   

                        $users_qry = DB::table('users')->where('user_type', 'TEACHER')->where('users.status', 'ACTIVE')
                            ->where('users.school_college_id', $school_id)
                            ->select('users.id', 'name', 'mobile');   

                        if($class_id > 0 || $section_id > 0 || $subject_id > 0){
                            $users_qry->leftjoin('subject_mapping', 'users.id', 'subject_mapping.teacher_id');
                            if($class_id > 0){
                                $users_qry->where('subject_mapping.class_id',$class_id);
                            }
                            if($section_id > 0){
                                $users_qry->where('subject_mapping.section_id',$section_id);
                            }
                            $users_qry->where('subject_mapping.status', 'ACTIVE')
                                ->groupby('subject_mapping.teacher_id');
 
                            if($subject_id > 0){
                                $users_qry->where('subject_mapping.subject_id',$subject_id); 
                            }
                        } 

                        $users = $users_qry->orderBy('users.name', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($users->isNotEmpty()) {
                            foreach($users as $uk=>$user) {
                                $users[$uk]->handling_classes = SubjectMapping::getHandlingClasses($user->id);
                            }

                            return response()->json([ 'status' => 1, 'message' => 'Subject Teachers List', 'data' => $users]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Subject Teachers Mapped']); 
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

    // Class Timings
    public function getClassTimings(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $period = Periodtiming::leftjoin('classes', 'classes.id', 'period_timings.class_id')
                            ->where('period_timings.id','!=',1)->where('classes.status','ACTIVE')
                            ->where('classes.school_id', $school_id)->where('period_timings.id','!=',1)
                            ->select('period_timings.*');

                        if($class_id > 0) {
                            $period->where('class_id','=',$class_id); 
                        }
                        $period = $period->orderby('classes.position','asc')->get();
                        if($period->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Class Timings', 'data'=>$period]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Class Timings']);
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

    // save Class Timings
    public function postClassTimings(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_id',  'period_1', 'period_2',  'period_3', 'period_4',
                'period_5', 'period_6',  'period_7', 'period_8' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $period_1 = ((isset($input) && isset($input['period_1']))) ? $input['period_1'] : '';  
                $period_2 = ((isset($input) && isset($input['period_2']))) ? $input['period_2'] : '';  
                $period_3 = ((isset($input) && isset($input['period_3']))) ? $input['period_3'] : '';  
                $period_4 = ((isset($input) && isset($input['period_4']))) ? $input['period_4'] : '';  
                $period_5 = ((isset($input) && isset($input['period_5']))) ? $input['period_5'] : '';  
                $period_6 = ((isset($input) && isset($input['period_6']))) ? $input['period_6'] : '';  
                $period_7 = ((isset($input) && isset($input['period_7']))) ? $input['period_7'] : '';  
                $period_8 = ((isset($input) && isset($input['period_8']))) ? $input['period_8'] : '';  

                $period_id = ((isset($input) && isset($input['period_id']))) ? $input['period_id'] : 0;  
 

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        if(!empty($period_1)) {
                            $newPeriod_1 = date('h:i A', strtotime($period_1));
                        }else{
                            $newPeriod_1 = "00:00";
                        }

                        if(!empty($period_2)) {
                            $newPeriod_2 = date('h:i A', strtotime($period_2));
                        }else{
                            $newPeriod_2 = "00:00";
                        }

                        if(!empty($period_3)) {
                            $newPeriod_3 = date('h:i A', strtotime($period_3));
                        }else{
                            $newPeriod_3 ="00:00";
                        }

                        if(!empty($period_4)) {
                            $newPeriod_4 = date('h:i A', strtotime($period_4));
                        }else{
                            $newPeriod_4 = "00:00";
                        }

                        if(!empty($period_5)) {
                            $newPeriod_5 = date('h:i A', strtotime($period_5));
                        }else{
                            $newPeriod_5 = "00:00";
                        }

                        if(!empty($period_6)) {
                            $newPeriod_6 = date('h:i A', strtotime($period_6));
                        }else{
                            $newPeriod_6 = "00:00";
                        }

                        if(!empty($period_7)) {
                            $newPeriod_7 = date('h:i A', strtotime($period_7));
                        }else{
                            $newPeriod_7 = "00:00";
                        }

                        if(!empty($period_8)) {
                            $newPeriod_8 = date('h:i A', strtotime($period_8));
                        }else{
                            $newPeriod_8 = "00:00";
                        } 

                        if ($period_id > 0) {
                            $exists = DB::table('period_timings')->where('class_id', $class_id)->whereNotIn('id', [$period_id])->first();
                        } else {
                            $exists = DB::table('period_timings')->where('class_id', $class_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 0, 'message' => 'Class Timings Alreay Created for the Selected Class'], 201);
                        } 
                          
                       if ($period_id > 0) {
                            $period = Periodtiming::find($period_id);
                        } 
                        else {
                            $period = new Periodtiming;
                        } 

                        $period->school_id = $school_id;   
                        
                        $period->class_id = $class_id;
                        $period->period_1 = $newPeriod_1;
                        $period->period_2 = $newPeriod_2;
                        $period->period_3 = $newPeriod_3;
                        $period->period_4 = $newPeriod_4;
                        $period->period_5 = $newPeriod_5;
                        $period->period_6 = $newPeriod_6;
                        $period->period_7 = $newPeriod_7;
                        $period->period_8 = $newPeriod_8;
                        $period->save();
                    
                        return response()->json([ 'status' => 1, 'message' => 'Class Timings details saved successfully' ]);
                         
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

    // Circulars
    public function getCirculars(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';  
                $approval_status_id = ((isset($input) && isset($input['approval_status_id']))) ? $input['approval_status_id'] : '';  
                $teacher_id = ((isset($input) && isset($input['teacher_id']))) ? $input['teacher_id'] : 0; 
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
                $search = isset($input['search']) ? $input['search'] : '';

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $sectionsqry = Circulars::where('circular.id', '>', 0)->where('school_id', $school_id)
                            ->select('circular.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day'));  

                        if($status_id != ''){
                            $sectionsqry->where('status','=',$status_id);  
                        } 

                        if($approval_status_id != ''){
                            $sectionsqry->where('approve_status','=',$approval_status_id); 
                        }

                        if($class_id > 0){
                            $sectionsqry->whereRAW(' FIND_IN_SET('.$class_id.', class_ids) '); 
                        }

                        if($teacher_id  > 0){
                            $sectionsqry->where('created_by','=',$teacher_id); 
                        }

                        if(!empty(trim($mindate))) {
                            $mindate = date('Y-m-d', strtotime($mindate));
                            $sectionsqry->where('circular.circular_date', '>=', $mindate); 
                
                        }
                        if(!empty(trim($maxdate))) {
                            $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                            $sectionsqry->where('circular.circular_date', '<=', $maxdate); 
                        }
                        if(!empty(trim($search))) { 
                            $sectionsqry->whereRaw(' ( circular_title like "%'.$search.'%"  or circular_message like "%'.$search.'%") '); 
                        }

                        $circulars = $sectionsqry->orderby('circular.id','desc')->skip($page_no)->take($limit)->get();
                        if($circulars->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Circulars', 'data'=>$circulars]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Circulars']);
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

    // save Circulars
    public function postCirculars(Request $request) {
        try {    

            /*$inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); */

            $input = $request->all();

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_ids',  'circular_title', 'circular_message',  
                'circular_date', 'status_id', 'approve_status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_ids = ((isset($input) && isset($input['class_ids']))) ? $input['class_ids'] : '';  
                $circular_title = ((isset($input) && isset($input['circular_title']))) ? $input['circular_title'] : '';  
                $circular_message = ((isset($input) && isset($input['circular_message']))) ? $input['circular_message'] : '';  
                $circular_date = ((isset($input) && isset($input['circular_date']))) ? $input['circular_date'] : '';  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : 'ACTIVE';  
                $approve_status = ((isset($input) && isset($input['approve_status']))) ? $input['approve_status'] : '';  

                $circular_id = ((isset($input) && isset($input['circular_id']))) ? $input['circular_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        if ($circular_id > 0) {
                            $circular = Circulars::find($circular_id);
                            $circular->updated_at = date('Y-m-d H:i:s');
                            $circular->updated_by = $school_id;
                        } else {
                            $circular = new Circulars();
                            $circular->created_by = $school_id;
                            $circular->created_at = date('Y-m-d H:i:s');
                        }

                        if (is_array($class_ids) && count($class_ids) > 0) {
                            $class_ids = implode(',', $class_ids);
                        } else {
                            return response()->json([ 'status' => 0,  'message' => "Please select the classes " ]);
                        }

                        $circular->school_id = $school_id;

                        $circular->class_ids = $class_ids;
                        $circular->circular_title = $circular_title;
                        $circular->circular_message = $circular_message;
                        $circular->circular_date = $circular_date;

                        $image = $request->file('circular_image');
                        if (!empty($image)) {
                            $ext = $image->getClientOriginalExtension();
                            if (!in_array($ext, $this->accepted_formats)) {
                                return response()->json(['status' => 0, 'message' => 'Image File Format Wrong.Please upload png,jpeg,jpg']);
                            }

                            $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                            $destinationPath = public_path('/uploads/circulars');

                            $image->move($destinationPath, $countryimg);

                            $circular->circular_image = $countryimg;

                        }
                        $image = $request->file('circular_attachments');
                        if (!empty($image)) {
                            $ext = $image->getClientOriginalExtension();
                            if (!in_array($ext, $this->accepted_formats_audio)) {
                                return response()->json(['status' => 0, 'message' => 'Attachment File Format Wrong.Please upload mp3,mp4']);
                            }

                            $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                            $destinationPath = public_path('/uploads/circulars');

                            $image->move($destinationPath, $countryimg);

                            $circular->circular_attachments = $countryimg;

                        } 
                        $circular->status = $status;
                        $circular->approve_status = $approve_status;

                        $circular->save();
                    
                        return response()->json([ 'status' => 1, 'message' => 'Circulars details saved successfully' ]);
                         
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

    // Events
    public function getEvents(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';  
                $approval_status_id = ((isset($input) && isset($input['approval_status_id']))) ? $input['approval_status_id'] : '';  
                $teacher_id = ((isset($input) && isset($input['teacher_id']))) ? $input['teacher_id'] : 0; 
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
                $search = isset($input['search']) ? $input['search'] : '';

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $sectionsqry = Events::where('events.id', '>', 0)->where('school_id', $school_id)
                            ->select('events.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day'));  

                        if($status_id != ''){
                            $sectionsqry->where('status','=',$status_id);  
                        } 

                        if($approval_status_id != ''){
                            $sectionsqry->where('approve_status','=',$approval_status_id); 
                        }

                        if($class_id > 0){
                            $sectionsqry->whereRAW(' FIND_IN_SET('.$class_id.', class_ids) '); 
                        }

                        if($teacher_id  > 0){
                            $sectionsqry->where('created_by','=',$teacher_id); 
                        }

                        if(!empty(trim($mindate))) {
                            $mindate = date('Y-m-d', strtotime($mindate));
                            $sectionsqry->where('events.circular_date', '>=', $mindate); 
                
                        }
                        if(!empty(trim($maxdate))) {
                            $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                            $sectionsqry->where('events.circular_date', '<=', $maxdate); 
                        }
                        if(!empty(trim($search))) { 
                            $sectionsqry->whereRaw(' ( circular_title like "%'.$search.'%"  or circular_message like "%'.$search.'%") '); 
                        }

                        $circulars = $sectionsqry->orderby('events.id','desc')->skip($page_no)->take($limit)->get();
                        if($circulars->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Events', 'data'=>$circulars]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Events']);
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

    // save Events
    public function postEvents(Request $request) {
        try {    

            /*$inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); */

            $input = $request->all();

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_ids',  'circular_title', 'circular_message',  
                'circular_date', 'status_id', 'approve_status'  ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_ids = ((isset($input) && isset($input['class_ids']))) ? $input['class_ids'] : '';  
                $circular_title = ((isset($input) && isset($input['circular_title']))) ? $input['circular_title'] : '';  
                $circular_message = ((isset($input) && isset($input['circular_message']))) ? $input['circular_message'] : '';  
                $circular_date = ((isset($input) && isset($input['circular_date']))) ? $input['circular_date'] : '';  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : 'ACTIVE';  
                $approve_status = ((isset($input) && isset($input['approve_status']))) ? $input['approve_status'] : '';  
                $youtube_link = ((isset($input) && isset($input['youtube_link']))) ? $input['youtube_link'] : '';  

                $circular_id = ((isset($input) && isset($input['circular_id']))) ? $input['circular_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        if ($circular_id > 0) {
                            $circular = Events::find($circular_id);
                            $circular->updated_at = date('Y-m-d H:i:s');
                            $circular->updated_by = $school_id;
                        } else {
                            $circular = new Events();
                            $circular->created_by = $school_id;
                            $circular->created_at = date('Y-m-d H:i:s');
                        }

                        if (is_array($class_ids) && count($class_ids) > 0) {
                            $class_ids = implode(',', $class_ids);
                        } else {
                            return response()->json([ 'status' => 0,  'message' => "Please select the classes " ]);
                        }

                        $circular->school_id = $school_id;

                        $circular->class_ids = $class_ids;
                        $circular->circular_title = $circular_title;
                        $circular->circular_message = $circular_message;
                        $circular->circular_date = $circular_date;
                        $circular->youtube_link = $youtube_link;

                        $images = $request->file('circular_image',[]);
           
                        if (!empty($images)) {
                            $arr = []; $str =  '';
                            if(!empty($circular->circular_image)) {
                                $sarr = explode(';', $circular->circular_image);
                            }   else {
                                $sarr = [];
                            }

                            $total_count = count($images)+count($sarr);
                            if($total_count <= 10){
                            foreach($images as $image) {
                         
                                $accepted_formats = ['jpeg', 'jpg', 'png'];
                                $ext = $image->getClientOriginalExtension();
                                if (!in_array($ext, $accepted_formats)) {
                                    return response()->json(['status' => 0, 'message' => 'File Format Wrong.Please upload Jepg, jpg, Png Format Files']);
                                }
                         
                                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                                $destinationPath = public_path('/uploads/circulars');
                              
                                $image->move($destinationPath, $countryimg);

                                $arr[] = $countryimg;
                            }
                          
                            }
                            else{
                                return response()->json(['status' => 0, 'message' => 'Only 10 images are Allows to Upload']);
                            }
                            if(count($arr)>0) {
                                $arr = array_merge($sarr, $arr);
                                $str = implode(';', $arr);
                            }
                            $circular->circular_image = $str;
                     
                        }

                        $images = $request->file('circular_attachments');
                        if (!empty($images)) {
                            $arr = []; $str =  '';
                            if(!empty($circular->circular_attachments)) {
                                $sarr = explode(';', $circular->circular_attachments);
                            }   else {
                                $sarr = [];
                            }
                            foreach($images as $image) {
                                $ext = $image->getClientOriginalExtension();
                                if (!in_array($ext, $this->accepted_formats_audio) && !in_array($ext, $this->accepted_formats_audio)) {
                                    return response()->json(['status' => 0, 'message' => 'File Format Wrong.Please upload mp3,mp4']);
                                }

                                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                                $destinationPath = public_path('/uploads/circulars');

                                $image->move($destinationPath, $countryimg);

                                $arr[] = $countryimg;
                            }
                            if(count($arr)>0) {
                                $arr = array_merge($sarr, $arr);
                                $str = implode(';', $arr);
                            }
                            $circular->circular_attachments = $str;
                        }

                        $circular->status = $status;
                        $circular->approve_status = $approve_status;

                        $circular->save();
                    
                        return response()->json([ 'status' => 1, 'message' => 'Event details saved successfully' ]);
                         
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

    // Holidays
    public function getHolidays(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0; 
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   
                $search = isset($input['search']) ? $input['search'] : '';

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $sectionsqry = Holidays::where('holidays.id', '>', 0)->where('school_college_id', $school_id);  
  
                        if(!empty(trim($search))) { 
                            $sectionsqry->whereRaw(' ( holiday_date like "%'.$search.'%" ) '); 
                        }

                        $holidays = $sectionsqry->orderby('holidays.holiday_date','desc')->skip($page_no)->take($limit)->get();
                        if($holidays->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Holidays', 'data'=>$holidays]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Holidays']);
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

    // save Holidays
    public function postHolidays(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'holiday_date',  'holiday_description' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $holiday_date = ((isset($input) && isset($input['holiday_date']))) ? $input['holiday_date'] : '';  
                $holiday_description = ((isset($input) && isset($input['holiday_description']))) ? $input['holiday_description'] : '';   
                if(!empty(trim($holiday_date))) {
                    $holiday_date = date('Y-m-d', strtotime($holiday_date));
                }   else {
                    return response()->json(['status' => 0, 'message' => 'Please select the Valid Holiday Date'] );
                }

                $holiday_id = ((isset($input) && isset($input['holiday_id']))) ? $input['holiday_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  
                          
                        if ($holiday_id > 0) {
                            $holiday = Holidays::find($holiday_id);
                        } else {
                            $holiday = new Holidays();
                        }

                        $holiday->school_college_id = $school_id;
                        $holiday->holiday_date = $holiday_date;
                        $holiday->holiday_description = $holiday_description;

                        $holiday->save();
                    
                        return response()->json([ 'status' => 1, 'message' => 'Holiday saved successfully' ]);
                         
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

    // Delete Holidays
    public function postDeleteHolidays(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'holiday_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $holiday_id = ((isset($input) && isset($input['holiday_id']))) ? $input['holiday_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $holiday_id > 0) {  
                          
                        Holidays::where('id', $holiday_id)->delete();
                    
                        return response()->json([ 'status' => 1, 'message' => 'Holiday deleted successfully' ]);
                         
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

    // Timetable
    public function getTimetable(Request $request) {
        try {    

            $inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); 

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_id', 'section_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;   
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        if($class_id > 0) {} else {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select the Class']);
                        } 
                        if($section_id > 0) {} else {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select the Section']);
                        }
                          
                        if (($class_id > 0) && ($section_id > 0)) {
                            $sections = Sections::where("id", $section_id)->where("class_id", $class_id)->get();
                            if($sections->isNotEmpty()) {
                                foreach ($sections as $section) {
                                    $map_subjects = $section->mapped_subjects;
                                }

                                $periods = Periodtiming::where('class_id',$class_id)->where('school_id', $school_id);  
                                $periods = $periods->select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first();

                                if(!empty($periods)){
                                    $periods = $periods->toArray();
                                          
                                    $timetable1 = DB::table('timetables')->where('class_id', $class_id)->where('section_id', $section_id)->get();
                                    if($timetable1->isNotEmpty()) {
                                        foreach($timetable1 as $times) {
                                            for($i=1; $i<=8; $i++) {
                                                $period = 'period_'.$i;
                                                $timetable[$times->day_id]['period_'.$i] = $times->$period;
                                            }
                                        }
                                    }   else {
                                        $timetable = [];
                                    } 
                                } else{
                                    return response()->json(['status' => 0, 'data' => [], 'message' => 'Please Assign Periods For Selected Class']);
                                }

                                $idsArr = explode(',', $map_subjects);
                                $subjects = DB::table('subjects')->whereIn('id', $idsArr)->orderby('position', 'asc')->get(); 
                                $class = Classes::select('*')->get();
                                $days = DB::table('days')->select('*')->get();

                                $row1 = [];
                                $row1['day'] = 'Days';
                                if(!empty($periods) ) {
                                    foreach ($periods as $pk=>$periodtiming) {
                                        if($periodtiming != '00:00' && $periodtiming != '') {
                                            $row1['subject'][$pk] = $periodtiming;
                                        }
                                    }
                                } 
                                
                                $data = [];
                                $data[] = $row1;

                                if(!empty($days)) {
                                    foreach ($days as $dk=>$day) {
                                        $row[$dk] = [];
                                        $row[$dk]['day'] = $day->day_name;
                                        foreach ($periods as $key => $periodtiming) {
                                            if($key != 'is_class_name') {
                                                $row[$dk]['subject'][$key] = '';
                                            }
                                            if($periodtiming != '00:00' &&  $periodtiming != '') {
                                                if (!empty($subjects)) {
                                                    foreach ($subjects as $subject) {
                                                        if(isset($timetable[$day->id]) && isset($timetable[$day->id][$key])) {
                                                            if($timetable[$day->id][$key] == $subject->id) {
                                                                $row[$dk]['subject'][$key] = $subject->subject_name;
                                                            } 
                                                        }
                                                    }
                                                } 
                                            }
                                        }
                                        $data[] = $row[$dk];
                                    }
                                } 
 
                                return response()->json(['status' => 1, 'data' => $data, 'message' => 'Timetable']);
                            }  else {
                                return response()->json(['status' => 0, 'data' => [], 'message' => 'Not a valid section']);
                            }
                        }   else {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select Class and Section']);
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

    // School Banks List
    public function getSchoolBanksList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $bank_qry = SchoolBankList::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $bank_qry->where('status',$status_id); 
                        }  

                        $banks = $bank_qry->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($banks->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Banks List', 'data' => $banks]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Banks List']); 
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

    // Waiver Category List
    public function getWaiverCategoryList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $wavier_category = WaiverCategory::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $wavier_category->where('status',$status_id); 
                        }  

                        $wavier_category = $wavier_category->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($wavier_category->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Waiver Category List', 'data' => $wavier_category]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Waiver Category List']); 
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

    // Concession Category List
    public function getConcessionCategoryList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $concession_category = ConcessionCategory::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $concession_category->where('status',$status_id); 
                        }  

                        $concession_category = $concession_category->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($concession_category->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Concession Category List', 'data' => $concession_category]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Concession Category List']); 
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

    // Fee Cancel Reasons List
    public function getFeeCancelReasonsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $cancel_reason = FeeCancelReason::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $cancel_reason->where('status',$status_id); 
                        }  

                        $cancel_reason = $cancel_reason->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($cancel_reason->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Cancel Reasons List', 'data' => $cancel_reason]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Cancel Reasons List']); 
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

    // Fee Payment Modes List
    public function getFeePaymentModesList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $payment_mode = PaymentMode::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $payment_mode->where('status',$status_id); 
                        }  

                        $payment_mode = $payment_mode->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($payment_mode->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Payment Modes List', 'data' => $payment_mode]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Payment Modes List']); 
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

    // Fee Terms List
    public function getFeeTermsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $fee_term = FeeTerm::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $fee_term->where('status',$status_id); 
                        }  

                        $fee_term = $fee_term->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($fee_term->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Term List', 'data' => $fee_term]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Term List']); 
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

    // Fee Receipt Heads List
    public function getFeeReceiptHeadsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $receipt_head = ReceiptHead::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $receipt_head->where('status',$status_id); 
                        }  

                        $receipt_head = $receipt_head->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($receipt_head->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Receipt Heads List', 'data' => $receipt_head]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Receipt Heads List']); 
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

    // Fee Accounts List
    public function getFeeAccountsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $accounts = Account::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $accounts->where('status',$status_id); 
                        }  

                        $accounts = $accounts->orderBy('position', 'ASC')->skip($page_no)->take($limit)->get(); 

                        if($accounts->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Accounts List', 'data' => $accounts]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Accounts List']); 
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

    // Fee Category List
    public function getFeeCategoryList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $feecategory = FeeCategory::leftjoin('accounts', 'accounts.id', 'fee_categories.account_id')
                            ->where('fee_categories.school_id', $school_id);
 
                        if(!empty($status_id)){
                            $feecategory->where('fee_categories.status',$status_id); 
                        }  

                        $feecategory = $feecategory->orderBy('fee_categories.position', 'ASC')
                            ->select('fee_categories.*', 'accounts.account_name')->skip($page_no)->take($limit)->get(); 

                        if($feecategory->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Category List', 'data' => $feecategory]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Category List']); 
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

    // Fee Items List
    public function getFeeItemsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';     
                $category_id = ((isset($input) && isset($input['category_id']))) ? $input['category_id'] : 0;   
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $feeitems = FeeItems::where('school_id', $school_id);
 
                        if(!empty($status_id)){
                            $feeitems->where('status',$status_id); 
                        }  

                        if($category_id > 0){
                            $feeitems->where('category_id',$category_id); 
                        }  

                        $feeitems = $feeitems->orderBy('id', 'deSC')->skip($page_no)->take($limit)->get(); 

                        if($feeitems->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Items List', 'data' => $feeitems]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Items List']); 
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

    // Fee Structures List
    public function getFeeStructuresList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;      

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $feestructure = FeeStructureItem::leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id') 
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                            ->leftjoin('fee_terms', 'fee_terms.id', 'fee_structure_items.fee_term_id') 
                            ->where('fee_structure_items.school_id', $school_id)
                            ->select('fee_categories.name', 'fee_structure_items.*', 'fee_structure_lists.batch',
                                'fee_structure_lists.fee_category_id', 'fee_structure_lists.fee_type', 
                                'fee_structure_lists.fee_post_type', 'fee_structure_lists.class_list',
                                'fee_items.item_code', 'fee_items.item_name', 'fee_terms.name as term_name');
  

                        $feestructure = $feestructure->orderBy('fee_structure_items.id', 'DESC')
                            ->skip($page_no)->take($limit)->get(); 

                        if($feestructure->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Fee Structures List', 'data' => $feestructure]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Fee Structures List']); 
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

    // Class Sections List
    public function getClassSections(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $sections = Sections::where('class_id', $class_id)->where('status','=','ACTIVE')
                            ->orderby('position', 'asc')->select('section_name', 'id')->get();  

                        if($sections->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Sections List', 'data' => $sections]); 
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Sections List']); 
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

    // Class Section Subjects List
    public function getClassSecSubjects(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'section_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;     

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   
                        $mapped_subjects = DB::table('sections')->where('id', $section_id)->value('mapped_subjects');
                        if(!empty($mapped_subjects)) {
                            $mapped_subjects = explode(',', $mapped_subjects);
                            $subjects = Subjects::whereIn("id", $mapped_subjects)->where('status','ACTIVE')
                                ->select("subject_name", "id")->orderby('position', 'asc')->get();

                            if($subjects->isNotEmpty()) {
                                return response()->json([ 'status' => 1, 'message' => 'Subjects List', 'data' => $subjects]); 
                            } else {
                                return response()->json([ 'status' => 0, 'message' => 'No Subjects List']); 
                            }
                        }  else {
                            return response()->json([ 'status' => 0, 'message' => 'No Subjects List']); 
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

    // Attendance
    public function getOAStudentAttendance(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'cdate' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $cdate = ((isset($input) && isset($input['cdate']))) ? $input['cdate'] : '';   
                if(empty($cdate)) {
                    $cdate = date('Y-m-d');
                }

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {    

                        //OASections::$acadamic_year = date('Y');
                        OASections::$cdate = $cdate;
                        $sectionsqry = OASections::leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                            ->where('sections.status','=','ACTIVE')
                            ->where('classes.school_id', $school_id)
                            ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');  

                        if($class_id>0){
                            $sectionsqry->where('class_id',$class_id); 
                        }
                        if($section_id>0){
                            $sectionsqry->where('sections.id',$section_id); 
                        }
 
                        $orderby = 'classes.id';   $dir = 'DESC'; 

                        $sections = $sectionsqry->orderby($orderby, $dir)->get();   

                        $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                        if(!empty($settings)) {
                            $acadamic_year = trim($settings->acadamic_year);
                        }

                        $overall = OASections::getOverallAttribute($acadamic_year, $school_id);

                        if($sections->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Overall Attendance', 'data' => $sections, 
                                    'overall' => $overall ]);
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'No Sections']);
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

    public function getStudentLeaveReports(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'cdate', 'page_no' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;   
                $date = ((isset($input) && isset($input['cdate']))) ? $input['cdate'] : '';   
                if(empty($date)) {
                    $date = date('Y-m-d');
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   

                        $acadamic_year = $request->get('acadamic_year',date('Y')); 

                        $users_qry = AttendanceApproval::leftjoin('classes','classes.id','attendance_approval.class_id')
                        ->leftjoin('sections','sections.id','attendance_approval.section_id')
                        ->leftjoin('users','users.id','attendance_approval.user_id')
                        ->where(function($query) {
                            $query->where('attendance_approval.fn_status', 2)
                                  ->orWhere('attendance_approval.an_status', 2);
                        })
                        ->where('attendance_approval.admin_status', 1)
                        ->select('attendance_approval.*','classes.class_name','sections.section_name','users.name','users.mobile', 'users.mobile1');  

                        if(!empty($date)){
                            $users_qry->where('date',$date); 
                        }
                         
                        $users_qry->where('users.school_college_id', $school_id); 
                         

                        if($student_id > 0){
                            $users_qry->where('attendance_approval.user_id',$student_id);  
                        }
                        if($class_id > 0){
                            $users_qry->where('attendance_approval.class_id',$class_id);  
                        }
                        if($section_id > 0){
                            $users_qry->where('attendance_approval.section_id',$section_id);  
                        } 
                        
                        $orderby = 'attendance_approval.id';   $dir = 'DESC';  
                        $sections = $users_qry->skip($page_no)->take($limit)->orderby($orderby, $dir)->get();  

                        if($sections->isNotEmpty()) {
                            return response()->json([ 'status' => 0, 'message' => 'Absent Report', 'data' => $sections]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Absence']);
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
    
    public function getStudentattendanceReports(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'monthyear', 'class_id', 'section_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $monthyear = ((isset($input) && isset($input['monthyear']))) ? $input['monthyear'] : '';   
                if(empty($monthyear)) {
                    $monthyear = date('Y-m');
                }
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {   

                        if($class_id > 0) {} else { $class_id = 0; }
                        if($section_id > 0) {} else { $section_id = 0; }

                        if($class_id == 0) {
                            return response()->json(['status' => 0, 'message' => 'Please select the Class']);
                        }
                        if($section_id == 0) {
                            return response()->json(['status' => 0, 'message' => 'Please select the Section']);
                        }

                        $total_attendance_detail = [];

                        User::$monthyear = $monthyear;
                        User::$class_id = $class_id;
                        User::$section_id = $section_id;

                        $academic_year = date('Y');
                        $final_year = date('Y') + 1;
                        $cur_month = date('m');
                        $from_month =  $academic_year.'-06';
                        $to_month = $final_year.'-04';
                        $check_month =  $academic_year.'-'.$cur_month;
                        $userids = []; $students = '';

                        $orderdate = explode('-', $monthyear);
                        $year = $orderdate[0];
                        $month   = $orderdate[1];
                        $fin_month = $year.'-'.$month;
                        
                        $users = DB::select("select student_class_mappings.*, `users`.`id`, `name`, `email`, `mobile`, `students`.`class_id`,`profile_image`, `students`.`section_id`, `students`.`admission_no` from `student_class_mappings` left join `users` on `student_class_mappings`.`user_id` = `users`.`id` left join `students` on `students`.`user_id` = `users`.`id` where `user_type` = 'STUDENT' and '".$fin_month."' BETWEEN from_month and to_month and `student_class_mappings`.`class_id` = '".$class_id."' and `student_class_mappings`.`section_id` = '".$section_id."' and users.status ='ACTIVE' and users.delete_status = 0 and users.school_college_id = '".$school_id."'");
                        $userids = []; $students = '';
                        if(!empty($users)) {
                            
                            foreach($users as $user) {
                                $userids[] = $user->user_id;
                            }
                            $userids = array_unique($userids);
                            list($year, $month) = explode('-', $monthyear);
             
                            $students = User::with('dailyattendance')
                                ->leftjoin('students', 'students.user_id', 'users.id')
                                ->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                                ->where('user_type', 'STUDENT')->where('users.school_college_id',$school_id)
                                ->where('users.status','ACTIVE')->where('users.delete_status',0)
                                ->whereIn('users.id', $userids)
                                // ->where('academic_year', $year)
                                ->whereRaw("'".$fin_month."' BETWEEN from_month and to_month")
                                /* ->where('student_class_mappings.class_id', $class_id)
                                ->where('student_class_mappings.section_id', $section_id)*/
                                ->select('users.id', 'name', 'email', 'mobile', 'students.class_id', 'students.section_id', 'students.admission_no','users.profile_image')
                                ->orderby('users.name','asc')
                                ->get();
                                // echo $monthyear;
                            list($year, $month) = explode('-', $monthyear);
                            $sundays = CommonController::getSundays($year, $month); 
                            $saturdays = CommonController::getSaturdays($year, $month); 
                            // echo "<pre>";print_r($saturdays);
                            // exit;
                            
                            
                            foreach($students as $k=>$v){
                                $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                                    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                                $students[$k]->holidays_list = $holidays;
                                $students[$k]->total_attendance_detail = CommonController::getStudentAttendance($year, $month, $v->id); 
                            }

                            if($students->isNotEmpty()) {

                                $total_working_days = CommonController::getTotalWorkingDays($monthyear); 
                                $lastdate = date('t', strtotime($monthyear));
                                $students = $students->toArray();

                                $data = [  'lastdate'=>$lastdate,'saturdays'=>$saturdays,'sundays'=>$sundays,
                                        'total_working_days'=>$total_working_days 
                                    ]; 

                                return response()->json(['status' => 1, 'data' => $students, 'message' => 'Students attendance Detail', 'total' => $data ]);

                            }   else {
                                return response()->json(['status' => 0,  'message' => 'No Students attendance Detail']);
                            }
                        }  else {
                            return response()->json(['status' => 0,  'message' => 'No Students attendance Detail']);
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
    
    public function getStudentsLeaveList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'page_no' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;   
                $mindate = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';   
                $maxdate = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';    
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   
                $limit = CommonController::$page_limit;

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {    

                        $users_qry = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->select('leaves.*','classes.class_name','sections.section_name','users.name');  
 
                        $users_qry->where('users.school_college_id', $school_id); 

                         
                        if(!empty(trim($mindate))) {
                            $mindate = date('Y-m-d', strtotime($mindate));
                            $users_qry->whereRaw('leaves.leave_date >= ?', [$mindate]); 
                        }
                        if(!empty(trim($maxdate))) {
                            $maxdate = date('Y-m-d', strtotime($maxdate));
                            $users_qry->whereRaw('leaves.leave_date <= ?', [$maxdate]); 
                        }
                        if($student_id > 0){
                            $users_qry->where('leaves.student_id',$student_id); 
                        }
                        if($class_id > 0){
                            $users_qry->where('leaves.class_id',$class_id); 
                        }
                        if($section_id > 0){
                            $users_qry->where('leaves.section_id',$section_id); 
                        } 

                        $orderby = 'leaves.id'; $dir = 'DESC'; 
             
                        $users = $users_qry->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();

                        if($users->isNotEmpty()) {
                            return response()->json([ 'status' => 1, 'message' => 'Leaves', 'data'=> $users]); 
                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'No Leaves']);
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

    // Student Daily Attendance
    public function getStudentDailyAttendance(Request $request) {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'cdate', 'class_id', 'section_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $cdate = ((isset($input) && isset($input['cdate']))) ? $input['cdate'] : '';   
                if(empty($cdate)) {
                    $cdate = date('Y-m-d');
                } else {
                    $cdate = date('Y-m-d', strtotime($cdate));
                }
                $monthyear = date('Y-m', strtotime($cdate));
                $lastdate = date('t', strtotime($monthyear));

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {    

                        $orderdate = explode('-', $cdate);
                        $year = $orderdate[0];
                        $month   = $orderdate[1];
                        $day  = $orderdate[2];
                        $day = $day * 1;          
                        if($class_id > 0) {} else { $class_id = 0; }
                        if($section_id > 0) {} else { $section_id = 0; }

                        if($class_id == 0) {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select the Class']);
                        }
                        if($section_id == 0) {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select the Section']);
                        }

                        User::$monthyear = $monthyear;
                        User::$class_id = $class_id;
                        User::$section_id = $section_id;
                        $new_date = $cdate;
                        $academic_year = date('Y');
                        $final_year = date('Y') + 1;
                        $cur_month = date('m');
                        $from_month =  $academic_year.'-06';
                        $to_month = $final_year.'-04';
                        $check_month =  $academic_year.'-'.$cur_month;
                        $userids = []; $students = '';

                        $orderdate = explode('-', $monthyear);
                        $year = $orderdate[0];
                        $month   = $orderdate[1];
                        $fin_month = $year.'-'.$month;
                        
                        $users = User::leftjoin('students', 'students.user_id', 'users.id')
                        //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                        ->where('user_type', 'STUDENT')->where('students.delete_status', 0)
                        ->where('users.status','ACTIVE')
                        // ->whereRaw("'".$check_month."' BETWEEN from_month and to_month")
                        ->where('students.class_id', $class_id)
                        ->where('students.section_id', $section_id)
                        ->select('students.*','users.id', 'name', 'email', 'mobile','profile_image', 'students.class_id', 'students.section_id', 'students.admission_no')
                        ->orderby('name')
                        ->get();


                        // $users = DB::select("select student_class_mappings.*, `users`.`id`, `name`, `email`, `mobile`, `students`.`class_id`, `students`.`section_id`, `students`.`admission_no` from `student_class_mappings` left join `users` on `student_class_mappings`.`user_id` = `users`.`id` left join `students` on `students`.`user_id` = `users`.`id` where `users `.`user_type` = 'STUDENT' and `student_class_mappings`.`class_id` = '".$class_id."' and `student_class_mappings`.`section_id` = '".$section_id."'");

                        $appstatus = 0;
                        $app = DB::table('attendance_approval')->where('class_id', $class_id)
                                ->where('section_id', $section_id)->where('date', $cdate)->where('admin_status', 1)->get();
                        if($app->isNotEmpty()) {
                            $appstatus = 1;
                        }
                        if(!empty($users)) {
                            foreach($users as $user) {
                                $userids[] = $user->user_id;
                            }
                            $userids = array_unique($userids);
                            list($year, $month) = explode('-', $monthyear);

                             $students = User::with('dailyattendance')
                                ->leftjoin('students', 'students.user_id', 'users.id')
                                //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                                ->where('user_type', 'STUDENT')->where('students.delete_status', 0)
                                ->where('users.status','ACTIVE')
                                ->whereIn('users.id', $userids)
                               //->whereRaw("'".$fin_month."' BETWEEN from_month and to_month")
                                /* ->where('student_class_mappings.class_id', $class_id)
                                ->where('student_class_mappings.section_id', $section_id)*/
                                 ->where('students.class_id', $class_id)
                                ->where('students.section_id', $section_id)
                                ->select('users.id', 'name', 'email', 'mobile','profile_image', 'students.class_id', 'students.section_id', 'students.admission_no')
                                ->orderby('users.name', 'asc')
                                ->get();



                                $total_boys = User::with('dailyattendance')
                                ->leftjoin('students', 'students.user_id', 'users.id')
                                //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                                ->where('user_type', 'STUDENT')->where('students.delete_status', 0)
                                ->where('users.status','ACTIVE')
                                ->where('users.gender','MALE')
                                ->whereIn('users.id', $userids)
                               //->whereRaw("'".$fin_month."' BETWEEN from_month and to_month")
                                 ->where('students.class_id', $class_id)
                                ->where('students.section_id', $section_id)
                                ->select('users.id', 'name', 'email', 'mobile','profile_image', 'students.class_id', 'students.section_id', 'students.admission_no')
                                ->get()->count();

                                $total_girls = User::with('dailyattendance')
                                ->leftjoin('students', 'students.user_id', 'users.id')
                                //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                                ->where('user_type', 'STUDENT')->where('students.delete_status', 0)
                                ->where('users.status','ACTIVE')
                                ->where('users.gender','FEMALE')
                                ->whereIn('users.id', $userids)
                               //->whereRaw("'".$fin_month."' BETWEEN from_month and to_month")
                                 ->where('students.class_id', $class_id)
                                ->where('students.section_id', $section_id)
                                ->select('users.id', 'name', 'email', 'mobile','profile_image', 'students.class_id', 'students.section_id', 'students.admission_no')
                                ->get()->count();

                                $date = 'day_'.$day;
                                $fn_chk = StudentsDailyAttendance::whereIn('user_id', $userids)->where($date,1)->where('monthyear', $monthyear)->select('id')->get()->count();
                                $date2 = 'day_'.$day.'_an';
                                $an_chk = StudentsDailyAttendance::whereIn('user_id', $userids)->where($date2,1)->where('monthyear', $monthyear)->select('id')->get()->count();
                               
                               list($year, $month) = explode('-', $monthyear);
                               $sundays = CommonController::getSundays($year, $month); 
                               $saturdays = CommonController::getSaturdays($year, $month); 
                               $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')->where('school_college_id', $school_id)
                                    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                                    if($holidays->isNotEmpty()){
                                        $holidays = $holidays->toArray();
                                    }

                            // Morning Session
                            $total_boys_present_fn = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',1)
                                //->where('attendance_approval.an_status',1)
                                ->where('users.gender','MALE')
                                        ->where('students.delete_status', 0)
                                        ->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)->get()->count();

                            $total_boys_absent_fn = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',2)
                                //->where('attendance_approval.an_status',2)
                                ->where('users.gender','MALE')->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();

                            $total_girls_present_fn = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',1)
                                //->where('attendance_approval.an_status',1)
                                ->where('users.gender','FEMALE')->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();

                            $total_girls_absent_fn = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',2)
                                //->where('attendance_approval.an_status',2)
                                ->where('users.gender','FEMALE')->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();


                            // Afternoon Session
                            $total_boys_present_an = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)//->where('attendance_approval.fn_status',1)
                                ->where('attendance_approval.an_status',1)->where('users.gender','MALE')
                                        ->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();

                            $total_boys_absent_an = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)//->where('attendance_approval.fn_status',2)
                                ->where('attendance_approval.an_status',2)->where('users.gender','MALE')->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();

                            $total_girls_present_an = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)//->where('attendance_approval.fn_status',1)
                                ->where('attendance_approval.an_status',1)->where('users.gender','FEMALE')->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();

                            $total_girls_absent_an = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->leftjoin('students', 'students.user_id', 'users.id')->where('attendance_approval.date',$new_date)//->where('attendance_approval.fn_status',2)
                                ->where('attendance_approval.an_status',2)->where('users.gender','FEMALE')->where('students.class_id', $class_id)
                                        ->where('students.section_id', $section_id)
                                        ->where('students.delete_status', 0)->get()->count();

                            if($students->isNotEmpty()) {
                                $students = $students->toArray();

                                foreach($students as $sk=>$stud) {
                                    if(isset($stud['dailyattendance']) && is_array($stud['dailyattendance']) && count($stud['dailyattendance'])>0) {
                                        $students[$sk]['dayfn'] = $stud['dailyattendance'][$date];
                                        $students[$sk]['dayan'] = $stud['dailyattendance'][$date2];
                                    }
                                    //echo "<pre>";print_r($date);print_r($date2);print_r($stud['dailyattendance']);exit;
                                }
                                //

                                $data = ['monthyear'=>$monthyear, 'class_id'=>$class_id, 'section_id'=>$section_id, 
                                'students'=>$students, 'lastdate'=>$lastdate, 'fn_chk'=>$fn_chk, 'an_chk'=>$an_chk,
                                'new_date'=>$new_date, 'sundays'=>$sundays, 'saturdays'=>$saturdays, 'holidays'=>$holidays,
                                'total_boys'=>$total_boys, 'total_girls'=>$total_girls, 
                                'total_boys_present_fn'=>$total_boys_present_fn,
                                'total_boys_absent_fn'=>$total_boys_absent_fn,
                                'total_girls_present_fn'=>$total_girls_present_fn,
                                'total_girls_absent_fn'=>$total_girls_absent_fn,
                                'total_boys_present_an'=>$total_boys_present_an,
                                'total_boys_absent_an'=>$total_boys_absent_an,
                                'total_girls_present_an'=>$total_girls_present_an,
                                'total_girls_absent_an'=>$total_girls_absent_an,
                                'appstatus'=>$appstatus
                                ]; 

                                return response()->json(['status' => 1, 'data' => $data, 'message' => 'Students attendance Detail']);

                            }   else {
                                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Students attendance Detail']);
                            }

                            return response()->json(['status' => 0, 'data' => [], 'message' => 'No Students attendance Detail']);
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

    // Save Student Daily Attendance
    public function saveStudentDailyAttendance(Request $request) {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'cdate', 'class_id', 'section_id', 'att_chk'  ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;     
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $cdate = ((isset($input) && isset($input['cdate']))) ? $input['cdate'] : '';   
                $att_chk = ((isset($input) && isset($input['att_chk']))) ? $input['att_chk'] : '';   
                $present_students = ((isset($input) && isset($input['present_students']))) ? $input['present_students'] : '';  
                $absent_students = ((isset($input) && isset($input['absent_students']))) ? $input['absent_students'] : '';  

                if($att_chk > 0) {} else {
                    return response()->json(['status' => 0, 'message' => 'Please select the mode of Attendance']);
                }

                if(empty($present_students) && empty($absent_students)) { 
                    return response()->json(['status' => 0, 'message' => 'Please select the Scholars']);
                } 

                if(empty($cdate)) {
                    $cdate = date('Y-m-d');
                } else {
                    $cdate = date('Y-m-d', strtotime($cdate));
                } 

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {    
                        $attendance_type = [];
                        if(!empty($present_students)) {
                            $present_students_arr = explode(',', $present_students);
                            if(count($present_students_arr)>0) {
                                foreach($present_students_arr as $stud) {
                                    $attendance_type[$stud] = 'p';
                                } 
                            }
                        }
                        if(!empty($absent_students)) {
                            $absent_students_arr = explode(',', $absent_students);
                            if(count($absent_students_arr)>0) {
                                foreach($absent_students_arr as $stud) {
                                    $attendance_type[$stud] = 'a';
                                } 
                            }
                        }

                        $request->request->add(['new_date' => $cdate, 'tclass_id' => $class_id, 'tsection_id' => $section_id,
                            'student_id' => [], 'att_chk' => $att_chk, 'attendance_type' => $attendance_type ]);
                        //echo "<pre>"; print_r($request->all()); exit;
                        $ret = (new AdminController())->postDailyAttendancePage($request);
                        return $ret;
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

    // UserRoles
    public function getUserRoles(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $role_status = ((isset($input) && isset($input['status']))) ? $input['status'] : '';  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $roles = UserRoles::where('school_id', $school_id);
                        if(!empty($role_status)) {
                            $roles->where('status', $role_status);
                        }
                        $roles = $roles->orderby('id', 'desc')->skip($page_no)->take($limit)->get();  
                        if($roles->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Roles', 'data'=>$roles]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Roles']);
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

    public function postUserRoles(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'user_role', 'status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $user_role = ((isset($input) && isset($input['user_role']))) ? $input['user_role'] : '';  
                $role_status = ((isset($input) && isset($input['status']))) ? $input['status'] : 'ACTIVE';  

                $role_id = ((isset($input) && isset($input['role_id']))) ? $input['role_id'] : 0;   

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if(empty($user_role)) {
                            return response()->json([ 'status' => 0, 'data' => null, 'message' => 'Please enter the User role']);
                        }

                        $exroles = ['USER', 'SUPER_ADMIN', 'GUESTUSER', 'SCHOOL', 'STUDENT', 'TEACHER'];
                        if(!empty($user_role)) { 
                            $rolename = strtoupper($user_role);
                            if (in_array($rolename, $exroles)) {
                                return response()->json([ 'status' => "FAILED", 'message' => "Role Name Already Exists."
                                ]);
                            } 
                        }

                        if ($role_id > 0) {
                            $exroles = UserRoles::where('id', '!=', $role_id)->where('school_id', $school_id)
                                ->whereRAW('LOWER(user_role) = "'.strtolower($user_role).'"')->first();
                        }   else {
                            $exroles = UserRoles::whereRAW('LOWER(user_role) = "'.strtolower($user_role).'"')
                                ->where('school_id', $school_id)->first();
                        }

                        if(!empty($exroles)) {
                            return response()->json([
                                'status' => "FAILED",
                                'message' => "Role Name Already Exists."
                            ]);
                        }

                        if ($role_id > 0) {
                            $role = UserRoles::find($role_id);
                            $role->updated_at = date('Y-m-d H:i:s');
                        } else {
                            $role = new UserRoles();
                            $role->created_at = date('Y-m-d H:i:s');

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

                            $role->ref_code = CommonController::$code_prefix.'UR'.$append;
                        }

                        $role->school_id = $school_id;
                        $role->user_role = $user_role;
                        $role->status = $role_status; 

                        $role->save();
                        return response()->json([
                            'status' => 1, 'message' => 'User Role Saved Successfully'
                        ]);
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

    // Role Admin Users 
    public function getRoleUsers(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $roleusers = DB::table('users')->leftjoin('userroles', 'userroles.ref_code', 'users.user_type')
                            ->whereNotIn('user_type', ['USER', 'SUPER_ADMIN', 'GUESTUSER', 'SCHOOL', 'STUDENT', 'TEACHER'])
                            ->where('users.school_college_id', $school_id)
                            ->select('users.*', 'userroles.user_role')
                            ->orderby('users.id', 'desc')->skip($page_no)->take($limit)->get();  
                        if($roleusers->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Role Users', 'data'=>$roleusers]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Role Users']);
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

    public function postRoleUsers(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'user_role', 'name', 'mobile', 'password', 'gender',
                'dob', 'emp_no', 'date_of_joining', 'father_name', 'address', 'status' ];  

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $user_role = ((isset($input) && isset($input['user_role']))) ? $input['user_role'] : '';  
                $name = ((isset($input) && isset($input['name']))) ? $input['name'] : '';  
                $mobile = ((isset($input) && isset($input['mobile']))) ? $input['mobile'] : '';  
                $password = ((isset($input) && isset($input['password']))) ? $input['password'] : '';  
                $gender = ((isset($input) && isset($input['gender']))) ? $input['gender'] : '';  
                $dob = ((isset($input) && isset($input['dob']))) ? $input['dob'] : '';  
                $emp_no = ((isset($input) && isset($input['emp_no']))) ? $input['emp_no'] : '';  
                $date_of_joining = ((isset($input) && isset($input['date_of_joining']))) ? $input['date_of_joining'] : '';  
                $father_name = ((isset($input) && isset($input['father_name']))) ? $input['father_name'] : '';  
                $address = ((isset($input) && isset($input['address']))) ? $input['address'] : '';  
                $user_status = ((isset($input) && isset($input['status']))) ? $input['status'] : '';  

                $lastname = ((isset($input) && isset($input['lastname']))) ? $input['lastname'] : '';  
                $email = ((isset($input) && isset($input['email']))) ? $input['email'] : '';  
                $country_id = ((isset($input) && isset($input['country_id']))) ? $input['country_id'] : 0;  
                $state_id = ((isset($input) && isset($input['state_id']))) ? $input['state_id'] : 0;  
                $city_id = ((isset($input) && isset($input['city_id']))) ? $input['city_id'] : 0;  
                $qualification = ((isset($input) && isset($input['qualification']))) ? $input['qualification'] : '';  
                $exp = ((isset($input) && isset($input['exp']))) ? $input['exp'] : '';  
                $post_details = ((isset($input) && isset($input['post_details']))) ? $input['post_details'] : '';  

                $role_user_id = ((isset($input) && isset($input['role_user_id']))) ? $input['role_user_id'] : 0;   
                $subjectId = 0; $classId = $class_tutor = $section_id = 0;
                if($country_id == ''){   $country_id = 0;   }
                if($state_id == ''){  $state_id = 0;  }
                if($city_id == ''){  $city_id = 0;  } 
                if($class_tutor == ''){  $class_tutor = 0;  }
                if($section_id == ''){  $section_id = 0; } 

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if (!empty($mobile)) {
                            if ($role_user_id > 0) {
                                $exists = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)->whereNotIn('id', [$role_user_id])->first();
                            } else {
                                $exists = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)->first();
                            }
                        }

                        if ($role_user_id > 0) {
                            $emp_no_chk = DB::table('teachers')->where('emp_no', $emp_no)->where('school_id', $school_id)->whereNotIn('user_id', [$role_user_id])->first();
                        } else {
                            $emp_no_chk = DB::table('teachers')->where('emp_no', $emp_no)->where('school_id', $school_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 'FAILED', 'message' => 'Mobile Already Exists'], 201);
                        }

                        if (!empty($emp_no_chk)) {
                            return response()->json(['status' => 'FAILED', 'message' => 'Employee Number Already Exists'], 201);
                        }

                        $date = date('Y-m-d H:i:s');
                        if ($role_user_id > 0) {
                            $users = User::find($role_user_id);
                            $users->updated_at = date('Y-m-d H:i:s');
                            $users->updated_by = $userid;
                        } else {
                            if(empty($password)) {
                                return response()->json([
                                    'status' => "FAILED",
                                    'message' => "Please Enter the Password"
                                ]);
                            }
                            $users = new User();

                            $lastjobid = DB::table('users')->where('created_at', 'like', date('Y-m-d') . '%')->orderby('id', 'desc')->count();
                            $lastjobid = $lastjobid + 1;
                            $append = str_pad($lastjobid, 6, "0", STR_PAD_LEFT);
                            $reg_no = date('ymd') . $append;

                            $users->reg_no = $reg_no;
                            $def_expiry_after =  CommonController::getDefExpiry();
                            $users->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                            $users->api_token = User::random_strings(30);
                            $users->last_login_date = date('Y-m-d H:i:s');
                            $users->last_app_opened_date = date('Y-m-d H:i:s');
                            $users->user_source_from = 'ADMIN';
                            $users->joined_date = $date;
                            $users->created_at = $date;
                            $users->created_by = $userid;  
                        }

                        if(!empty($password)) {
                            $users->passcode = $password;
                            $password = Hash::make($password);
                            $users->password = $password;
                        }
                        $users->school_college_id = $school_id;
                        $users->user_type = $user_role;
                        $users->name = $name;
                        $users->email = $email;
                        $users->mobile = $mobile;
                        $users->last_name = $lastname;
                        $users->gender = $gender;
                        $users->dob = $dob;
                        $country_code = DB::table('countries')->where('id', $country_id)->value('phonecode');
                        $users->country = $country_id;
                        $users->country_code = $country_code;
                        $users->code_mobile = $country_code.$mobile;
                        $users->state_id = $state_id;
                        $users->city_id = $city_id;
                        $users->status = $user_status; 

                        $users->save();

                        $userId = $users->id;

                        if ($role_user_id > 0) {
                            $teachers = Teacher::where('user_id', $role_user_id)->first();
                            if(empty($teachers)) {
                                $teachers = new Teacher;
                            }
                        } else {
                            $teachers = new Teacher;
                        }
                        $teachers->school_id = $school_id;
                        $teachers->user_id = $userId;
                        $teachers->emp_no = $emp_no;
                        $teachers->date_of_joining = $date_of_joining;
                        $teachers->qualification = $qualification;
                        $teachers->exp = $exp;
                        $teachers->post_details = $post_details;
                        $teachers->subject_id = $subjectId;
                        $teachers->class_id = $classId;
                        $teachers->class_tutor = $class_tutor;
                        $teachers->section_id = $section_id;
                        $teachers->father_name = $father_name;
                        $teachers->address = $address;
                        $teachers->status = $user_status;
                        $teachers->save();

                        return response()->json([  'status' => 1, 'message' => 'User Saved Successfully'  ]);


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

    public function postRoleUsersProfileImage(Request $request) {
        try {   
            /*$inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);*/

            $input = $request->all();

            $requiredParams = ['user_id', 'api_token', 'school_id', 'role_user_id', 'profile_image'  ];  

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   

                $role_user_id = ((isset($input) && isset($input['role_user_id']))) ? $input['role_user_id'] : 0;  
                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $role_user_id > 0) {  

                        if ($role_user_id > 0) {
                            $users = User::find($role_user_id);
                            if($users) {
                                $users->updated_at = date('Y-m-d H:i:s');
                                $users->updated_by = $userid;
                                $image = $request->file('profile_image');
                                if (!empty($image)) {

                                    $ext = $image->getClientOriginalExtension();
                                    $ext = strtolower($ext);

                                    if (!in_array($ext, $this->accepted_formats)) {
                                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                                    }

                                    $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                                    $destinationPath = public_path('/uploads/userdocs/');

                                    $image->move($destinationPath, $countryimg);

                                    $users->profile_image = $countryimg;

                                } else {
                                    return response()->json([ 'status' => 0, 'message' => 'Please Upload the Image']);
                                }

                                $users->save();

                                return response()->json([  'status' => 1, 'message' => 'User Profile Image updated Successfully'  ]);

                            }   else {
                                return response()->json([ 'status' => 0, 'message' => 'Invalid User']);
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

    // User Role Class Mapping
    public function getRoleClassMappingList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $roleid = ((isset($input) && isset($input['roleid']))) ? $input['roleid'] : 0;  
                $classid = ((isset($input) && isset($input['classid']))) ? $input['classid'] : 0;  
                $mapping_status = ((isset($input) && isset($input['status']))) ? $input['status'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $roleclassmappings = RoleClasses::leftjoin('userroles', 'userroles.id', 'role_classes.role_id')
                            ->where('userroles.school_id', $school_id)
                            ->select('role_classes.*', 'userroles.user_role');

                        if($roleid > 0) {
                            $roleclassmappings->where('role_classes.role_id',$roleid); 
                        }

                        if($classid > 0) {
                            $roleclassmappings->whereRAW(' FIND_IN_SET('.$classid.', role_classes.class_ids) '); 
                        }

                        if(!empty($mapping_status)){
                            $roleclassmappings->where('userroles.status',$mapping_status); 
                        }

                        $roleclassmappings = $roleclassmappings->orderby('userroles.id', 'desc')
                            ->skip($page_no)->take($limit)->get();  
                        if($roleclassmappings->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Role Class Mappings', 'data'=>$roleclassmappings]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Role Class Mappings']);
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

    public function postRoleClassMapping(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'role_id', 'class_ids', 'status'  ];  

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;   

                $role_id = ((isset($input) && isset($input['role_id']))) ? $input['role_id'] : 0;
                $class_ids = ((isset($input) && isset($input['class_ids']))) ? $input['class_ids'] : '';
                $mapping_status = ((isset($input) && isset($input['status']))) ? $input['status'] : 0;

                $mapping_id = ((isset($input) && isset($input['mapping_id']))) ? $input['mapping_id'] : 0;

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) {  

                        if ($mapping_id > 0) {
                            $exists = DB::table('role_classes')->where('role_id', $role_id)->whereNotIn('id', [$mapping_id])->first();
                        } else {
                            $exists = DB::table('role_classes')->where('role_id', $role_id)->first();
                        }

                        if (!empty($exists)) {
                            return response()->json(['status' => 'FAILED', 'message' => 'Classes Already Mapped'], 201);
                        }

                        if (is_array($class_ids) && count($class_ids) > 0) {
                            $class_ids = implode(',', $class_ids);
                        } else {
                            return response()->json([

                                'status' => 0,
                                'message' => "Please select the classes ",
                            ]);
                        }

                        if ($mapping_id > 0) {
                            $role_classes = RoleClasses::find($mapping_id);
                        } else {
                            $role_classes = new RoleClasses;
                        } 

                        $role_classes->role_id = $role_id;
                        $role_classes->class_ids = $class_ids; 
                        $role_classes->status = $status;

                        $role_classes->save();
                        return response()->json([ 'status' => 1, 'message' => 'Mapping updated Successfully']);

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

    // User Role Module Mapping
    public function getRoleModuleMappingList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'role_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $role_id = ((isset($input) && isset($input['role_id']))) ? $input['role_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $role = UserRoles::find($role_id);
                        if(!empty($role)) {

                            $index = 0;
                            $update_id = $role_id;
                            //$treeview  = $this->module_tree($index, $update_id); 
                            $treeview  = $this->module_tree_api($index, $update_id); 

                            return response()->json([ 'status' => 1, 'message' => 'Role Modume Mapping', 'data' => $treeview]);

                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid Role']);
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


    public function module_tree($index, $update_id) {
        $module = Module::where("parent_module_fk", $index)->get(); 
        $treeview = [];

        foreach ($module as $mc) {
            $id = $mc['id'];
            $name = $mc['module_name'];
            $menu_item = $mc['menu_item'];
            $parent = $mc['parent_module_fk'];
            $url = $mc['url'];

            $module_add = $mc['module_add'];
            $module_edit = $mc['module_edit'];
            $module_delete = $mc['module_delete'];
            $module_view = $mc['module_view'];
            $module_list = $mc['module_list'];
            $module_status_update = $mc['module_status_update'];
            $module_aadhar_status_update=$mc['module_aadhar_status_update'];

            //$treeview['SysroleModule']['module_fk']['id'][] = $id;

            $class = "";
            $type = 0;
            if ($url != "" && $url != "#") {
                $type = 1;
                $class = "";
            }
            $checked = "";
            $achecked = "";
            $echecked = "";
            $dchecked = "";
            $vchecked = "";
            $apchecked = "";
            $lchecked='';
            $apaschecked='';
            if (! empty($update_id)) {

                // $qry = "select `ra_add`,`ra_edit`,`ra_delete`,`ra_view` from role_access where ra_role_fk=$update_id and ra_module_fk=$id ";
                // $counts_select = mysql_query ( $qry );
                // $counts = mysql_fetch_array ( $counts_select );

                $role_access = RoleModuleMapping::where("ra_role_fk", $update_id)->where("ra_module_fk", $id)->first();
                

                if (!empty($role_access)) {
                    $checked = "checked";
                    if ($role_access->ra_add == 1) {
                        $achecked = "checked";
                    }
                    if ($role_access->ra_edit == 1) {
                        $echecked = "checked";
                    }
                    if ($role_access->ra_delete == 1) {
                        $dchecked = "checked";
                    }
                    if ($role_access->ra_view == 1) {
                        $vchecked = "checked";
                    }
                    if ($role_access->ra_list == 1) {
                        $lchecked = "checked";
                    }
                    if ($role_access->ra_status_update == 1) {
                        $apchecked = "checked";
                    }
                    if ($role_access->ra_aadhar_status_update == 1) {
                        $apaschecked = "checked";
                    }
                    
                }
            }
            $treeview_menu  = []; 
            //$treeview['SysroleModule']['module_fk']['treeview-menu'] = [];
            //$treeview_menu['SysroleModule']  = []; 


            $inner = $this->module_tree($id, $update_id);

            $treeview_menu[] = ['id' => $id, 'checked' => $checked, 'parent' => $parent, 
                'name' => $name, 'module_add' => $module_add, 'achecked' => $achecked, 
                'module_edit' => $module_edit, 'echecked' => $echecked,
                'module_delete' => $module_delete, 'dchecked' => $dchecked,
                'module_view' => $module_view, 'vchecked' => $vchecked,
                'module_list' => $module_list, 'lchecked' => $lchecked,
                'module_status_update' => $module_status_update, 'apchecked' => $apchecked, 'inner' => $inner
            ];  
            $treeview[] = ['id' => $id, 'treeview-menu' => $treeview_menu];
             
        } 

        return $treeview;
    } 

    public function module_tree_api($index, $update_id) {
        $module = Module::where("parent_module_fk", $index)->get(); 
        $treeview = [];

        foreach ($module as $mc) {
            $id = $mc['id'];
            $name = $mc['module_name'];
            $menu_item = $mc['menu_item'];
            $parent = $mc['parent_module_fk'];
            $url = $mc['url'];

            $module_add = $mc['module_add'];
            $module_edit = $mc['module_edit'];
            $module_delete = $mc['module_delete'];
            $module_view = $mc['module_view'];
            $module_list = $mc['module_list'];
            $module_status_update = $mc['module_status_update'];
            $module_aadhar_status_update=$mc['module_aadhar_status_update'];

            //$treeview['SysroleModule']['module_fk']['id'][] = $id;

            $class = "";
            $type = 0;
            if ($url != "" && $url != "#") {
                $type = 1;
                $class = "";
            }
            $checked = "";
            $achecked = "";
            $echecked = "";
            $dchecked = "";
            $vchecked = "";
            $apchecked = "";
            $lchecked='';
            $apaschecked='';
            if (! empty($update_id)) {

                // $qry = "select `ra_add`,`ra_edit`,`ra_delete`,`ra_view` from role_access where ra_role_fk=$update_id and ra_module_fk=$id ";
                // $counts_select = mysql_query ( $qry );
                // $counts = mysql_fetch_array ( $counts_select );

                $role_access = RoleModuleMapping::where("ra_role_fk", $update_id)->where("ra_module_fk", $id)->first();
                

                if (!empty($role_access)) {
                    $checked = "checked";
                    if ($role_access->ra_add == 1) {
                        $achecked = "checked";
                    }
                    if ($role_access->ra_edit == 1) {
                        $echecked = "checked";
                    }
                    if ($role_access->ra_delete == 1) {
                        $dchecked = "checked";
                    }
                    if ($role_access->ra_view == 1) {
                        $vchecked = "checked";
                    }
                    if ($role_access->ra_list == 1) {
                        $lchecked = "checked";
                    }
                    if ($role_access->ra_status_update == 1) {
                        $apchecked = "checked";
                    }
                    if ($role_access->ra_aadhar_status_update == 1) {
                        $apaschecked = "checked";
                    }
                    
                }
            }
            $treeview_menu  = []; 
            //$treeview['SysroleModule']['module_fk']['treeview_menu'] = [];
            //$treeview_menu['SysroleModule']  = []; 


            $inner = $this->module_tree_api($id, $update_id);

            $treeview_menu[] = ['id' => $id, 'checked' => $checked, 'parent' => $parent, 
                'name' => $name, 'module_add' => $module_add, 'achecked' => $achecked, 
                'module_edit' => $module_edit, 'echecked' => $echecked,
                'module_delete' => $module_delete, 'dchecked' => $dchecked,
                'module_view' => $module_view, 'vchecked' => $vchecked,
                'module_list' => $module_list, 'lchecked' => $lchecked,
                'module_status_update' => $module_status_update, 'apchecked' => $apchecked, 'inner' => $inner
            ];  
            $treeview[] = ['id' => $id, 'treeview_menu' => $treeview_menu];
             
        } 

        return $treeview;
    } 

    public function postRoleModuleMapping(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'role_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;    
                $role_id = ((isset($input) && isset($input['role_id']))) ? $input['role_id'] : 0;   

                $api_token = $request->header('x-api-key'); 

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $role_id > 0) { 
                        
                        $role_fk = $role_id;

                        $sysrolemodule = $request['SysroleModule']['id'];

                        $select_val1 = array();
                        $role_access = RoleModuleMapping::where('ra_role_fk', $role_fk)->get();
                        foreach ($role_access as $role_access) {
                            $select_val1[] = $role_access['ra_module_fk'];
                        }

                        foreach ($sysrolemodule as $pos => $val) {
                            if (isset($request['SysroleModule_add' . $val])) {
                                $add = 1;
                            } else {
                                $add = 0;
                            }

                            if (isset($request['SysroleModule_edit' . $val])) {
                                $edit = 1;
                            } else {
                                $edit = 0;
                            }

                            if (isset($request['SysroleModule_view' . $val])) {
                                $view = 1;
                            } else {
                                $view = 0;
                            }

                            if (isset($request['SysroleModule_delete' . $val])) {
                                $delete = 1;
                            } else {
                                $delete = 0;
                            }

                            if (isset($request['SysroleModule_list' . $val])) {
                                $list = 1;
                            } else {
                                $list = 0;
                            }

                            if (isset($request['SysroleModule_statusupdate' . $val])) {
                                $status_update = 1;
                            } else {
                                $status_update = 0;
                            }

                            if (isset($request['SysroleModule_aadharstatusupdate' . $val])) {
                                $aadhar_status_update = 1;
                            } else {
                                $aadhar_status_update = 0;
                            }

                            $role_access_pa = RoleModuleMapping::where('ra_role_fk', $role_fk)->where('ra_module_fk', $val)->first();
                            $id = '';
                            if (! empty($role_access_pa)) {
                                $id = $role_access_pa->id;
                            }
                            // $select=mysqli_query($conn,"select ra_pk from role_access where ra_role_fk=$role and ra_module_fk=$val");

                            if ($id > 0) {
                                $rolemodule = RoleModuleMapping::find($id);
                                $rolemodule->modified_by = $userid;
                            } else {
                                $rolemodule = new RoleModuleMapping();
                                $rolemodule->created_at = date('Y-m-d');
                                $rolemodule->created_by = $userid;
                            }
                            $rolemodule->ra_role_fk = $role_fk;
                            $rolemodule->ra_module_fk = $val;
                            $rolemodule->ra_add = $add;
                            $rolemodule->ra_edit = $edit;
                            $rolemodule->ra_delete = $delete;
                            $rolemodule->ra_view = $view;
                            $rolemodule->ra_list = $list;
                            $rolemodule->ra_status_update = $status_update;
                            $rolemodule->ra_aadhar_status_update = $aadhar_status_update;
                            $rolemodule->save();

                            $select_val[] = $val;

                            // if(mysqli_num_rows($select)>0){
                            // $result=mysqli_fetch_array($select);
                            // $select_val[]=$val;
                            // mysqli_query($conn,"update role_access set `add`=$add, `edit`=$edit, `delete`=$delete, `view`=$view where ra_role_fk=$role and ra_module_fk=$val ");
                            // }else {
                            // mysqli_query("delete from role_access where ra_role_fk=$id and ra_role_fk=$val");
                            // $sql="INSERT INTO `role_access` (`ra_pk`, `ra_role_fk`, `ra_module_fk`, `ra_add`, `ra_edit`, `ra_delete`, `ra_view`,`created_by_user_fk`,`created_on`,`modified_by_user_fk`,`modified_on`) VALUES (NULL, '$role', '$val', '$add', '$edit', '$delete', '$view', '$session_user_fk','$current_time','$session_user_fk','$current_time');";
                            // mysqli_query($conn,$sql);

                            // }
                        } 

                        $delete_mo = array_diff($select_val1, $select_val);   
                        foreach ($delete_mo as $delete_mo) {
                            DB::table('role_access')->where('ra_role_fk', $role_fk)
                                ->where('ra_module_fk', $delete_mo)
                                ->delete();
                        }

                        return response()->json([ 'status' => 1,  'message' => 'Role Saved Successfully' ]);

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



    // User Role Module Mapping
    public function getStaffModuleMappingList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id'  ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $role_id = ((isset($input) && isset($input['role_id']))) ? $input['role_id'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $role = UserRoles::find($role_id);
                        if(!empty($role)) {

                            $index = 0;
                            $update_id = $role_id;
                            //$treeview  = $this->module_tree($index, $update_id); 
                            $treeview  = $this->module_tree_api($index, $update_id); 

                            return response()->json([ 'status' => 1, 'message' => 'Role Modume Mapping', 'data' => $treeview]);

                        }   else {
                            return response()->json([ 'status' => 0, 'message' => 'Invalid Role']);
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

    // Chapters
    public function getChaptersList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
                $search = isset($input['search']) ? $input['search'] : '';

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $chaptersqry = Chapters::where('chapters.id', '>', 0)->where('school_id', $school_id)
                            ->select('chapters.*');  

                        if($status_id != ''){
                            $chaptersqry->where('status','=',$status_id);  
                        }  

                        if($class_id > 0){
                            $chaptersqry->whereRAW(' FIND_IN_SET('.$class_id.', class_id) '); 
                        }

                        if($subject_id  > 0){
                            $chaptersqry->where('subject_id', $subject_id); 
                        }

                        if($term_id  > 0){
                            $chaptersqry->where('term_id', $term_id); 
                        }
 
                        if(!empty(trim($search))) { 
                            $chaptersqry->whereRaw(' ( chaptername like "%'.$search.'%" ) '); 
                        }

                        $chapters = $chaptersqry->orderby('chapters.id','desc')->skip($page_no)->take($limit)->get();
                        if($chapters->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Chapters', 'data'=>$chapters]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Chapters']);
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

    // Chapter Topics
    public function getChapterTopicsList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
                $chapter_id = ((isset($input) && isset($input['chapter_id']))) ? $input['chapter_id'] : 0;  
                $search = isset($input['search']) ? $input['search'] : '';

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $chaptersqry = ChapterTopics::where('chapter_topics.id', '>', 0)->where('school_id', $school_id)
                            ->select('chapter_topics.*');  

                        if($status_id != ''){
                            $chaptersqry->where('status','=',$status_id);  
                        }  

                        if($class_id > 0){
                            $chaptersqry->whereRAW(' FIND_IN_SET('.$class_id.', class_id) '); 
                        }

                        if($subject_id  > 0){
                            $chaptersqry->where('subject_id', $subject_id); 
                        } 

                        if($chapter_id  > 0){
                            $chaptersqry->where('chapter_id', $chapter_id); 
                        }

                        if($term_id  > 0){
                            $chaptersqry->where('term_id', $term_id); 
                        }
 
                        if(!empty(trim($search))) { 
                            $chaptersqry->whereRaw(' ( chapter_topic_name like "%'.$search.'%" ) '); 
                        }

                        $chapters = $chaptersqry->orderby('chapter_topics.id','desc')->skip($page_no)->take($limit)->get();
                        if($chapters->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Chapter Topics', 'data'=>$chapters]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Chapter Topics']);
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

    // Books
    public function getBooksList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $status_id = ((isset($input) && isset($input['status_id']))) ? $input['status_id'] : '';  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  
                $term_id = ((isset($input) && isset($input['term_id']))) ? $input['term_id'] : 0;  
                $chapter_id = ((isset($input) && isset($input['chapter_id']))) ? $input['chapter_id'] : 0;  
                $search = isset($input['search']) ? $input['search'] : '';

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $chaptersqry = Topics::where('topics.id', '>', 0)->where('school_id', $school_id)
                            ->select('topics.*');  

                        if($status_id != ''){
                            $chaptersqry->where('status','=',$status_id);  
                        }  

                        if($class_id > 0){
                            $chaptersqry->whereRAW(' FIND_IN_SET('.$class_id.', class_id) '); 
                        }

                        if($subject_id  > 0){
                            $chaptersqry->where('subject_id', $subject_id); 
                        }

                        if($term_id  > 0){
                            $chaptersqry->where('term_id', $term_id); 
                        }
 
                        if(!empty(trim($search))) { 
                            $chaptersqry->whereRaw(' ( topic_title like "%'.$search.'%" ) '); 
                        }

                        $chapters = $chaptersqry->orderby('topics.position','asc')->skip($page_no)->take($limit)->get();
                        if($chapters->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Books', 'data'=>$chapters]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Books']);
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

    // Gallery
    public function getGallery(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $gallery = Gallery::where('school_id', $school_id)->select('gallery.*')
                            ->where('status', '!=', 'DELETED')
                            ->orderby('gallery.id', 'desc')->skip($page_no)->take($limit)->get();  
                        if($gallery->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Gallery', 'data'=>$gallery]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Gallery']);
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

    // save Gallery
    public function postGallery(Request $request) {
        try {    

            /*$inputJSON = file_get_contents('php://input');   

            $input = json_decode($inputJSON, TRUE); */

            $input = $request->all();

            $requiredParams = ['user_id', 'api_token', 'school_id', 'gallery_title',  'gallery_image', 'gallery_status' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request, true);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $gallery_title = ((isset($input) && isset($input['gallery_title']))) ? $input['gallery_title'] : '';  
                $gallery_status = ((isset($input) && isset($input['gallery_status']))) ? $input['gallery_status'] : 'ACTIVE';   

                $gallery_id = ((isset($input) && isset($input['gallery_id']))) ? $input['gallery_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        if ($gallery_id > 0) {
                            $gallery = Gallery::find($gallery_id);
                            $gallery->updated_at = date('Y-m-d H:i:s');
                            $gallery->updated_by = $school_id;
                        } else {
                            $gallery = new Gallery();
                            $gallery->created_by = $school_id;
                            $gallery->created_at = date('Y-m-d H:i:s');
                        } 

                        $gallery->school_id = $school_id; 
                        $gallery->gallery_title = $gallery_title;
                        $gallery->status = $gallery_status; 

                        $images = $request->file('gallery_image',[]);
           
                        if (!empty($images)) {
                            $arr = []; $str =  '';
                            if(!empty($gallery->gallery_image)) {
                                $sarr = explode(';', $gallery->gallery_image);
                            }   else {
                                $sarr = [];
                            } 
                        
                            foreach($images as $image) {
                         
                                $accepted_formats = ['jpeg', 'jpg', 'png'];
                                $ext = $image->getClientOriginalExtension();
                                $ext = strtolower($ext);
                                if (!in_array($ext, $accepted_formats)) {
                                    return response()->json(['status' => 0, 'message' => 'File Format Wrong.Please upload Jepg, jpg, Png Format Files']);
                                }
                         
                                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                                $destinationPath = public_path('/uploads/gallery/'.$school_id.'/');
                              
                                $image->move($destinationPath, $countryimg);

                                $arr[] = $countryimg;
                            }  

                            if(count($arr)>0) {
                                $arr = array_merge($sarr, $arr);
                                $str = implode(';', $arr);
                            }
                            $gallery->gallery_image = $str;
                     
                        }  

                        $gallery->save();
                    
                        return response()->json([ 'status' => 1, 'message' => 'Gallery saved successfully' ]);
                         
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

    // Examinations
    public function getExaminations(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $examsqry = Examinations::leftjoin('terms', 'terms.id', 'examinations.term_id')
                            ->leftjoin('exams', 'exams.examination_id', 'examinations.id')
                            ->select( 'examinations.id as examination_id', 'examinations.school_id', 'examinations.exam_name', 
                                'exams.schedule_status', 'examinations.status', 'exams.id as exam_id', 'exams.monthyear', 
                                'exams.class_ids', 'exams.section_ids', 'exams.exam_startdate', 'exams.exam_enddate', 'exams.rank_on_off', 
                                'exams.grade_on_off', 'exams.result_in', 'exams.rank_settings', 'exams.grade_settings', 'exams.rank_type', 
                                'exams.rankincludefailures', 'exams.publish_status', 'terms.term_name'  );  
 
                        if($status != '' || $status != 0){
                            $examsqry->where('examinations.status',$status); 
                        } 

                        $examsqry->where('examinations.school_id', $school_id); 

                        if($class_id > 0) {
                            $examsqry->where('exams.class_ids',$class_id); 
                        }

                        if($section_id > 0) {
                            $examsqry->whereIn('exams.section_ids', [0, $section_id]); 
                        } 

                        $orderby = 'examinations.id'; 
                        $dir = 'ASC'; 

                        $exams = $examsqry->skip($page_no)->take($limit)->orderby($orderby, $dir)->groupby('examinations.id')->get();
                        if($exams->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Examinations', 'data'=>$exams]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Examinations']);
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

    public function getExaminationSettings(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $schedule_status = ((isset($input) && isset($input['schedule_status']))) ? $input['schedule_status'] : ''; 
                $publish_status = ((isset($input) && isset($input['publish_status']))) ? $input['publish_status'] : '';  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $examsqry = Examinations::leftjoin('terms', 'terms.id', 'examinations.term_id')
                            ->leftjoin('exams', 'exams.examination_id', 'examinations.id')
                            ->select( 'examinations.id as examination_id', 'examinations.school_id', 'examinations.exam_name', 
                                'exams.schedule_status', 'examinations.status', 'exams.id as exam_id', 'exams.monthyear', 
                                'exams.class_ids', 'exams.section_ids', 'exams.exam_startdate', 'exams.exam_enddate', 'exams.rank_on_off', 
                                'exams.grade_on_off', 'exams.result_in', 'exams.rank_settings', 'exams.grade_settings', 'exams.rank_type', 
                                'exams.rankincludefailures', 'exams.publish_status', 'terms.term_name'  );   

                        if($status != '' || $status != 0){
                            $examsqry->where('examinations.status',$status); 
                        }

                        if(!empty(trim($schedule_status))){
                            $examsqry->where('exams.schedule_status',$schedule_status); 
                        }

                        if(!empty(trim($publish_status))){
                            $examsqry->where('exams.publish_status',$publish_status); 
                        } 
                            
                        $examsqry->where('examinations.school_id', $school_id); 

                        if($class_id > 0) {
                            $examsqry->where('exams.class_ids',$class_id); 
                        }

                        if($section_id > 0) {
                            $examsqry->where('exams.section_ids',$section_id); 
                        }
  
                        $orderby = 'examinations.id'; 
                        $dir = 'ASC'; 

                        $exams = $examsqry->skip($page_no)->take($limit)->orderby($orderby, $dir)->get(); 
                        if($exams->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Examination Settings', 'data'=>$exams]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Examination Settings']);
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

    public function getExamTerms(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $schedule_status = ((isset($input) && isset($input['schedule_status']))) ? $input['schedule_status'] : ''; 
                $publish_status = ((isset($input) && isset($input['publish_status']))) ? $input['publish_status'] : '';  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $examsqry = Terms::select('terms.*');   

                        if($status != '' || $status != 0){
                            $examsqry->where('terms.status',$status); 
                        } 
                        $examsqry->where('terms.school_id', $school_id); 

                        if($class_id > 0) {
                            $examsqry->whereRAW(' FIND_IN_SET('.$class_id.', class_ids) '); 
                        } 
  
                        $orderby = 'terms.id'; 
                        $dir = 'ASC'; 

                        $exams = $examsqry->skip($page_no)->take($limit)->orderby($orderby, $dir)->get(); 
                        if($exams->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Terms', 'data'=>$exams]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Terms']);
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

    public function getExamResults(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'class_id', 'section_id', 'exam_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0; 
                $exam_id = ((isset($input) && isset($input['exam_id']))) ? $input['exam_id'] : 0;  
                $subject_id = ((isset($input) && isset($input['subject_id']))) ? $input['subject_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0 && $exam_id > 0) {  

                        $monthyear = DB::table('exams')->where('id', $exam_id)->value('monthyear');
                        if(empty($monthyear)) {
                            $monthyear = date('Y-m');
                        }
                        User::$monthyear = $monthyear;
                        User::$class_id = $class_id;
                        User::$section_id = $section_id;
                        User::$exam_id = $exam_id;
                        if($subject_id  >0){
                            User::$subject_id = $subject_id;
                            MarksEntry::$subject_id = $subject_id;
                        }
                        // User::$student_id = $student_id;
                        /*if($subject_id  != '' || $subject_id  != ''){
                            MarksEntry::$subject_id = $subject_id;
                        }*/
                       
                        MarksEntry::$exam_id = $exam_id;
                        MarksEntry::$class_id = $class_id; 

                        $section_id1 = DB::table('exams')->where('id', $exam_id)->value('section_ids');

                        MarksEntry::$section_id = $section_id1;
                        $students = User::with('marksentry')
                            ->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                            ->leftjoin('students', 'students.user_id', 'users.id')
                            ->whereRaw( "'".$monthyear."' BETWEEN from_month and to_month ")
                            ->where('student_class_mappings.class_id', $class_id);

                        if($section_id > 0) {
                            $students->where('student_class_mappings.section_id', $section_id);
                        }

                        if($student_id > 0) {
                            $students->where('student_class_mappings.user_id',$student_id);
                        }

                        $students = $students->where('student_class_mappings.status', 'ACTIVE')
                            ->where('user_type', 'STUDENT')
                            ->where('student_class_mappings.user_id', '>', 0)
                            ->select('users.id', 'name', 'email', 'mobile', 'students.admission_no')
                            ->orderby('name')->get();
             
                         
                            /*foreach($students as $k => $v){

                                $get_sub = Sections::where('class_id',$class_id)->where('id',$section_id)->first();
                                $students[$k]->subject = $get_sub;

                                if($get_sub->isNotEmpty()) {
                                    foreach($get_sub as $sk=>$sub) {
                                        $get_sub[$sk]->is_subject_name = $sub->subject_name;
                                        $get_sub[$sk]->is_subject_id = $sub->subject_id;
                                    }
                                }
                                $students[$k]->subject = $get_sub; 

                            }*/
                           
                        if($students->isNotEmpty()) {

                            $subjects = DB::table('exams')->leftjoin('exam_sessions', 'exam_sessions.exam_id', 'exams.id')
                                    ->leftjoin('subjects', 'subjects.id', 'exam_sessions.subject_id')
                                    ->where('exams.id',$exam_id)->where('subjects.id','>',0)->where('exam_sessions.status','ACTIVE');
                            if($subject_id  >0){
                                $subjects->where('exam_sessions.subject_id',$subject_id);
                            }
                            if($class_id  >0){
                                $subjects->where('exam_sessions.class_id',$class_id);
                            }
                            $section_id = DB::table('exams')->where('id', $exam_id)->value('section_ids');
                            if($section_id  >0){
                                $subjects->where('exam_sessions.section_id',$section_id);
                            }
                            $subjects = $subjects->select('exam_sessions.subject_id as is_subject_id', 'subjects.subject_name as is_subject_name')
                                    ->get();
                            $students = $students->toArray();
                            foreach($students as $k => $v){
                                $marks = [];
                                if(isset($v['marksentry']) && !empty($v['marksentry'])) {
                                    if(isset($v['marksentry']['marksentryitems']) && !empty($v['marksentry']['marksentryitems'])) {
                                        foreach($v['marksentry']['marksentryitems'] as $k1 => $v1){
                                            $marks[$v1['subject_id']] = $v1;
                                        }
                                    }
                                }
                                $students[$k]['marks'] = $marks;
                            }

                            $c = []; $total_marks = ''; $rank = '-';  $grade = '-';//echo "<pre>"; print_r($students); exit;
                            if(!empty($subjects)) {
                                foreach($subjects as $sk => $sv) {
                                  $c[$sv->is_subject_id] = $sv->is_subject_name;
                                }
                            }

                            $table = [];
                            if(!empty($students) && count($students)>0) {
                                $thead = [];
                                $thead['id'] = 0;
                                $thead['name'] = "Name";
                                $thead['admission_no'] = "Admission No";
                                foreach( $c as $key=>$value) {
                                    $thead['subject_name'][] = ["id"=>"s".$key, "value"=>$value, "subject_id"=> $key];
                                }
                                if(isset($in_subject_id) && ($in_subject_id > 0)) {} 
                                else {
                                    $thead['total'] = "Total";
                                } 
                                $thead['rank'] = "Rank";
                                $thead['grade'] = "Grade";

                                $tbody = [];
                                foreach($students as $sk => $student)  {
                                    $tbody[$sk]['id'] = $student['id'];
                                    $tbody[$sk]['name'] = $student['name'];
                                    $tbody[$sk]['admission_no'] = $student['admission_no'];
                                    foreach( $c as $key=>$value){
                                        $total_marks = 0;  
                                        $marks = $remarks = $grade = $checked = ''; $is_absent = $rank = 0; 

                                        if (isset($student['marks']) && isset($student['marks'][$key]) && !empty($student['marks'][$key])) {
                                            $total_marks = $student['marks'][$key]['marks']; 
                                            $remarks = $student['marks'][$key]['remarks'];
                                            $grade = $student['marks'][$key]['grade'];

                                            $is_absent = $student['marks'][$key]['is_absent'];
                                            $checked = ($is_absent == 1) ? 'checked' : '';
                                            $marks = ($is_absent == 1) ? 'A' : $student['marks'][$key]['marks']; 
                                            $rank = ($is_absent == 1) ? '' : $student['marks'][$key]['rank'];
                                            if($rank > 0) {} else { $rank = 0; }
                                            if(!empty($grade)) {} else { $grade = '-'; }
                                        } 
                                        if(isset($in_subject_id) && ($in_subject_id > 0)) {} else {
                                            if (isset($student['marksentry']) && isset($student['marksentry']['rank']) && !empty($student['marksentry']['marks'])) {
                                                $rank = $student['marksentry']['rank'];
                                                $grade = $student['marksentry']['grade'];
                                                $total_marks = $student['marksentry']['marks'];
                                            }
                                        }


                                        if($rank > 0) {} else { $rank = '-'; }
                                        if(!empty($grade)) {} else { $grade = '-'; }
                                        if($total_marks > 0) {} else { $total_marks = ''; }
                                       
                                        $tbody[$sk]['subject_name'][] = ["id"=>"s".$key, "value"=>$marks, "subject_id"=> $key, "is_absent"=>$is_absent, "remarks"=> $remarks]; 

                                    }  

                                    if(isset($in_subject_id) && ($in_subject_id > 0)) {} else { 
                                        $tbody[$sk]['total_marks'] = $total_marks;
                                    } 

                                    $tbody[$sk]['rank'] = $rank;
                                    $tbody[$sk]['grade'] = $grade;  
                                    
                                }
                            } 
                             
                            $table['head'] =  $thead; 
                            $table['tbody'] =  $tbody;  

                            if(count($table)>0) { 
                                return response()->json([ 'status' => 1, 'message' => 'Exam Result', 'data'=>$table]);
                            } else {
                                return response()->json([ 'status' => 0, 'message' => 'No Exam Result']);
                            }
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Students']);
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

    public function saveMarkEntry(Request $request) {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = [ 'user_id', 'api_token', 'school_id', 'class_id', "section_id", "exam_id", 
                "mark"  ]; 

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  

                $api_token = $request->header('x-api-key');
                $page_no = 0;  $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                        $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                        $exam_id = ((isset($input) && isset($input['exam_id']))) ? $input['exam_id'] : 0;   
                        $mark = ((isset($input) && isset($input['mark']))) ? $input['mark'] : [];     

                        $error = '';

                        $subjects = DB::table('exams')->leftjoin('exam_sessions', 'exam_sessions.exam_id', 'exams.id')
                                ->leftjoin('subjects', 'subjects.id', 'exam_sessions.subject_id')
                                ->where('exams.id',$exam_id)->where('exam_sessions.class_id',$class_id)
                                ->where('subjects.id','>',0)->where('exam_sessions.status','ACTIVE')
                                ->whereIn('exam_sessions.section_id',[0, $section_id]);
                        /*if($subject_id  >0){
                            $subjects->where('exam_sessions.subject_id',$subject_id);
                        }*/ 
                        $subjects = $subjects->select('exam_sessions.subject_id as is_subject_id', 'subjects.subject_name as is_subject_name', 'exam_sessions.is_practical', 'exam_sessions.theory_mark', 'exam_sessions.practical_mark')
                                ->get();

                        $max = [];
                        if($subjects->isNotEmpty()) {
                            foreach($subjects as $sk=>$sv) {
                                $max[$sv->is_subject_id]['theory_mark'] = $sv->theory_mark;
                                $max[$sv->is_subject_id]['practical_mark'] = $sv->practical_mark;
                                $max[$sv->is_subject_id]['is_practical'] = $sv->is_practical;
                            }
                        }

                        $monthyear = DB::table('exams')->where('id', $exam_id)->value('monthyear');

                        //echo "<pre>"; print_r($mark); print_r($max); exit;

                        if(!empty($mark) && count($mark)>0) {
                            foreach($mark as $student) {
                                $student_id = $student['student_id'];
                                $theory_marks = $mrk = $student['mrk'];
                                $practical_marks = $pmrk = isset($student['pmrk']) ? $student['pmrk'] : 0;
                                $remarks = $remark = isset($student['remark']) ? $student['remark'] : '';
                                $subject = $s_id = $student['s_id'];
                                $is_absent = $student['is_ab'];

                                $marks = $mrk  + $pmrk;
                                $grade = '';

                                if($marks > 0) {
                                    $gr = DB::table('grades')->where('school_id', '<=', $school_id)
                                        ->where('mark', '<=', $marks)->orderby('mark', 'desc')->first();
                                    if(!empty($gr)) {
                                        $grade = $gr->grade;
                                    }
                                }

                                $tm = (isset($max[$s_id]) && isset($max[$s_id]['theory_mark'])) ? $max[$s_id]['theory_mark'] : 0;
                                $pm = (isset($max[$s_id]) && isset($max[$s_id]['practical_mark'])) ? $max[$s_id]['practical_mark'] : 0;
                                $total_marks = $tm + $pm;

                                $is_practical = (isset($max[$s_id]) && isset($max[$s_id]['is_practical'])) ? $max[$s_id]['is_practical'] : 0;
                                $theory_mark = (isset($max[$s_id]) && isset($max[$s_id]['theory_mark'])) ? $max[$s_id]['theory_mark'] : 0;
                                $practical_mark = (isset($max[$s_id]) && isset($max[$s_id]['practical_mark'])) ? $max[$s_id]['practical_mark'] : 0;
                                //echo $marks ."%%". $total_marks.";";
                                if($marks <= $total_marks) {
                                }   else {
                                    $error =  '  Entered mark should not be greater than Total Marks';
                                }

                                if($marks > 0 && $is_practical == 1) {
                                    //echo $theory_marks ."==". $theory_mark.";";
                                    if($theory_marks <= $theory_mark) {
                                    }   else {
                                        $sname = DB::table('users')->where('id', $student_id)->value('name');
                                        $error =  '  Entered Theory mark should not be greater than Total Theory Marks'.' '. $sname;
                                    }
                                    //echo $practical_marks ."==". $practical_mark.";";
                                    if($practical_marks <= $practical_mark) {
                                    }   else {
                                        $sname = DB::table('users')->where('id', $student_id)->value('name');
                                        $error =  '  Entered Practical mark should not be greater than Total Practical Marks'.' '. $sname;
                                    }
                                } 

                                $data = ['subject_id'=>$s_id, 'total_marks'=>$total_marks, 'is_absent' => $is_absent,
                                        'marks'=>$marks, 'theory_marks'=>$theory_marks, 'practical_marks'=>$practical_marks, 
                                        'remarks'=>$remarks, 'grade'=>$grade];

                                $ex = DB::table('marks_entry')->where('user_id', $student_id)
                                    ->where(['class_id'=>$class_id, 'section_id'=>$section_id, 'exam_id'=>$exam_id,
                                            'monthyear'=>$monthyear])->first();

                                if(!empty($ex)) {
                                    $data['updated_at'] = date('Y-m-d H:i:s');
                                    $data['updated_by'] = $userid;

                                    $mark_entry_id = $ex->id;

                                }   else {
                                    $data['created_at'] = date('Y-m-d H:i:s');
                                    $data['created_by'] = $userid;

                                    $mark_entry_id = DB::table('marks_entry')->insertGetId([
                                        'user_id'=>$student_id,  'monthyear'=>$monthyear,
                                        'class_id'=>$class_id, 'section_id'=>$section_id,
                                        'exam_id'=>$exam_id,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'created_by'=>$userid
                                    ]);
                                }

                                $exentry = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)
                                    ->where('subject_id', $subject)->first();

                                if(empty($error)) {
                                    if(!empty($exentry)) { 
                                        DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->where('subject_id', $subject)->update($data);
                                    }   else { 
                                        $data['mark_entry_id'] = $mark_entry_id;
                                        DB::table('marks_entry_items')->insert($data);
                                    }
                                }


                                $total_marks = DB::table('marks_entry_items')
                                    ->leftjoin('exam_sessions', 'exam_sessions.subject_id', 'marks_entry_items.subject_id')
                                    ->where('exam_sessions.status', 'ACTIVE')->where('exam_sessions.exam_id', $exam_id) 
                                    ->where('exam_sessions.class_id', $class_id)//->where('exam_sessions.section_id', $section_id) 
                                    ->where('mark_entry_id', $mark_entry_id)->sum('total_marks');
                                $marks = DB::table('marks_entry_items')
                                    ->leftjoin('exam_sessions', 'exam_sessions.subject_id', 'marks_entry_items.subject_id')
                                    ->where('exam_sessions.status', 'ACTIVE')->where('exam_sessions.exam_id', $exam_id) 
                                    ->where('exam_sessions.class_id', $class_id)//->where('exam_sessions.section_id', $section_id) 
                                    ->where('mark_entry_id', $mark_entry_id)->sum('marks');
                                $cnt = DB::table('marks_entry_items')
                                    ->leftjoin('exam_sessions', 'exam_sessions.subject_id', 'marks_entry_items.subject_id')
                                    ->where('exam_sessions.status', 'ACTIVE')->where('exam_sessions.exam_id', $exam_id) 
                                    ->where('exam_sessions.class_id', $class_id)//->where('exam_sessions.section_id', $section_id) 
                                    ->where('mark_entry_id', $mark_entry_id)
                                    ->count('marks_entry_items.id');

                                $remarks = $grade = $pass_type = '';
                                if($cnt > 0) {
                                    $avg = ($marks / $total_marks) * 100;

                                    $grade = '';
                                    $gr = DB::table('grades')->where('school_id', $school_id)->where('mark', '<=', $avg)->orderby('mark', 'desc')->first();
                                    if(!empty($gr)) {
                                        $grade = $gr->grade;
                                        $remarks = $gr->remark;
                                        if($avg<30) {
                                            $pass_type = 'Fail';
                                        }   else {
                                            $pass_type = 'Pass';
                                        }
                                    }
                                    if(empty($grade)) {
                                        if($avg>90) {
                                            $remarks = 'Out Standing'; $grade = 'O'; $pass_type = 'Pass';
                                        } elseif($avg>75) {
                                            $remarks = 'Distinction'; $grade = 'D'; $pass_type = 'Pass';
                                        }  elseif($avg>60) {
                                            $remarks = 'Very Good'; $grade = 'A+'; $pass_type = 'Pass';
                                        }  elseif($avg>45) {
                                            $remarks = 'Good'; $grade = 'A'; $pass_type = 'Pass';
                                        }  elseif($avg>30) {
                                            $remarks = 'OK'; $grade = 'B'; $pass_type = 'Pass';
                                        }   else {
                                            $remarks = 'Poor'; $grade = 'C'; $pass_type = 'Fail';
                                        }
                                    }
                                }

                                DB::table('marks_entry')->where('id', $mark_entry_id)
                                    ->update(['total_marks'=>$total_marks, 'marks' => $marks, 'remarks'=>$remarks,
                                              'grade'=>$grade, 'pass_type'=>$pass_type,
                                              'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>$userid
                                    ]); 
                            }
                        }

                         
                        if(!empty($error)) {
                            return response()->json(['status' => 0, 'message' => $error]);
                        }

                        (new AdminController())->updateRank($school_id, $class_id, $section_id, $exam_id);
 
                        return response()->json([ 'status' => 1, 'message' => 'Saved Successfully' ]);
                        
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

    public function getFetchExaminations(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;  

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $batch = DB::table('admin_settings')->where('school_id', $school_id)->value('acadamic_year');
                        $exams = DB::table('exams')
                            ->leftjoin('examinations', 'examinations.id', 'exams.examination_id')
                            ->leftjoin('exam_sessions', 'exams.id', 'exam_sessions.exam_id')
                            ->leftjoin('classes', 'classes.id', 'exam_sessions.class_id')
                            ->where('examinations.batch', $batch)
                            ->where('exams.class_ids', $class_id)->where('exams.examination_id', '>',0);
                        if($section_id > 0) {
                            $exams->whereIn('exams.section_ids', [0, $section_id]);
                        }
                        $exams = $exams->where('exam_sessions.status', 'ACTIVE')
                            ->select("examinations.exam_name", "exams.exam_name as exname", "exams.id", "exam_startdate", DB::RAW(' DATE_FORMAT(exam_startdate, "%Y-%m") as monthyear'), 'class_id', 'section_id')
                            ->groupby('exams.id')->orderby('exams.id', 'asc')->get();

                        if($exams->isNotEmpty()) {
                            $class_names  = '';
                            $class_ids = DB::table('classes') 
                                ->where('id', $class_id)->select('class_name')->groupby('id')->orderby('classes.position', 'asc')->get();
                               
                            if($class_ids->isNotEmpty()) {
                                foreach( $class_ids as $ids){
                                    $val[] = $ids->class_name;        
                                }
                                $class_names  = implode(',', $val);
                            }   else {
                                $class_names  = '';
                            }

                            foreach($exams as $ke=>$kv) { 

                                $section_names  = '';
                                if($kv->section_id > 0) {
                                    $section_names = DB::table('sections')->where('id', $kv->section_id)->value('section_name');
                                } else {
                                    $section_names  = 'All';
                                }
                 
                                $exams[$ke]->class_name = $class_names;
                                $exams[$ke]->section_name = $section_names;
                            }
                        }
                        if($exams->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Examinations', 'data'=>$exams]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Examinations']);
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

    public function getFeeCollectionReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;  
                $fee_category = ((isset($input) && isset($input['fee_category']))) ? $input['fee_category'] : 0;  
                $fee_item_id = ((isset($input) && isset($input['fee_item_id']))) ? $input['fee_item_id'] : 0;  
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $overall_fee_collected = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->where('fees_payment_details.is_concession', '0')->where('fees_payment_details.is_waiver', '0')
                            ->where('fees_payment_details.cancel_status', '0')->whereNuLL('users.alumni_status'); 

                            $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->where('fees_payment_details.is_concession', '0')->where('fees_payment_details.is_waiver', '0')
                            ->where('fees_payment_details.cancel_status', '0')->whereNuLL('users.alumni_status')
                            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                                'creator.name as creator_name'); 

                            if (!empty($search)) { 
                                $fee_summary_list->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                $overall_fee_collected->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")');
                                
                            }

                            if($batch > 0){
                                $fee_summary_list->where('fees_payment_details.batch',$batch); 
                                $overall_fee_collected->where('fees_payment_details.batch',$batch);
                            }
                            if($class_id > 0){
                                $fee_summary_list->where('fees_payment_details.class_id',$class_id); 
                                $overall_fee_collected->where('fees_payment_details.class_id',$class_id);
                            }
                            if($section_id > 0){
                                $fee_summary_list->where('fees_payment_details.section_id',$section_id); 
                                $overall_fee_collected->where('fees_payment_details.section_id',$section_id);
                            }
                            if($student_id > 0){
                                $fee_summary_list->where('fees_payment_details.student_id',$student_id); 
                                $overall_fee_collected->where('fees_payment_details.student_id',$student_id);
                            }
                            if($fee_category > 0){
                                $fee_summary_list->where('fee_categories.id',$fee_category); 
                                $overall_fee_collected->where('fee_categories.id',$fee_category);
                            }
                            if($fee_item_id > 0){
                                $fee_summary_list->where('fee_items.id',$fee_item_id); 
                                $overall_fee_collected->where('fee_items.id',$fee_item_id);
                            }
                            if(!empty(trim($mindate))) {
                                $mindate = date('Y-m-d', strtotime($mindate));
                                $fee_summary_list->where('fees_payment_details.created_at', '>=', $mindate); 
                                $overall_fee_collected->where('fees_payment_details.created_at', '>=', $mindate);
                    
                            }
                            if(!empty(trim($maxdate))) {
                                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                                $fee_summary_list->where('fees_payment_details.created_at', '<=', $maxdate); 
                                $overall_fee_collected->where('fees_payment_details.created_at', '<=', $maxdate);
                            }

 
                            $orderby = 'fees_payment_details.id';
                             
                            $dir = 'DESC';
                            

                            $overall_fee_collected = $overall_fee_collected->sum('fees_payment_details.amount_paid');

                            $fee_collection = $fee_summary_list->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();


                        if($fee_collection->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$fee_collection, 'overall_fee_collected'=>$overall_fee_collected]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeePendingReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;   
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $overall_fee_pending = DB::table('student_class_mappings') 
                        ->leftjoin('users', 'users.id', 'student_class_mappings.user_id') 
                        ->leftjoin('classes', 'classes.id', 'student_class_mappings.class_id')
                        ->leftjoin('sections', 'sections.id', 'student_class_mappings.section_id')
                        ->where('student_class_mappings.school_id', $school_id)->where('users.school_college_id', $school_id)
                        ->where('users.delete_status',0)->where('users.status','ACTIVE')//->whereNuLL('users.alumni_status')
                        ->where('student_class_mappings.balance_fees', '>', 0)
                        ->orderby('student_class_mappings.id','desc'); 

                        $fee_pending = DB::table('student_class_mappings') 
                        ->leftjoin('users', 'users.id', 'student_class_mappings.user_id') 
                        ->leftjoin('classes', 'classes.id', 'student_class_mappings.class_id')
                        ->leftjoin('sections', 'sections.id', 'student_class_mappings.section_id')
                        ->where('student_class_mappings.school_id', $school_id)->where('users.school_college_id', $school_id)
                        ->where('users.delete_status',0)->where('users.status','ACTIVE')//->whereNuLL('users.alumni_status')
                        ->where('student_class_mappings.balance_fees', '>', 0)
                        ->select('student_class_mappings.*', 'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name' ); 
 

                        if (!empty($search)) { 
                            $fee_pending->whereRaw('( users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%")'); 
                            $overall_fee_pending->whereRaw('( users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%")'); 
                             
                        }                     

                        if($batch > 0){
                            $fee_pending->where('student_class_mappings.academic_year',$batch); 
                            $overall_fee_pending->where('student_class_mappings.academic_year',$batch); 
                        }
                        if($class_id > 0){
                            $fee_pending->where('student_class_mappings.class_id',$class_id); 
                            $overall_fee_pending->where('student_class_mappings.class_id',$class_id); 
                        }
                        if($section_id > 0){
                            $fee_pending->where('student_class_mappings.section_id',$section_id); 
                            $overall_fee_pending->where('student_class_mappings.section_id',$section_id); 
                        }
                        if($student_id > 0){
                            $fee_pending->where('student_class_mappings.user_id',$student_id); 
                            $overall_fee_pending->where('student_class_mappings.user_id',$student_id); 
                        } 

 
                        $orderby = 'student_class_mappings.balance_fees'; 
                        $dir = 'DESC'; 

                        $overall_fee_pending = $overall_fee_pending->sum('student_class_mappings.balance_fees'); 

                        $fee_pending = $fee_pending->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();  


                        if($fee_pending->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$fee_pending, 'overall_fee_pending'=>$overall_fee_pending]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeeWaiverReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;  
                $fee_category = ((isset($input) && isset($input['fee_category']))) ? $input['fee_category'] : 0;  
                $fee_item_id = ((isset($input) && isset($input['fee_item_id']))) ? $input['fee_item_id'] : 0;  
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : ''; 
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $overall_fee_concession = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->whereRAW(' (fees_payment_details.is_concession = 0 AND fees_payment_details.is_waiver = 1) ') 
                            ->where('fees_payment_details.cancel_status', '0')
                            //->whereNuLL('users.alumni_status')
                            ->orderby('fees_payment_details.id','desc'); 

                            $cancelled_fee_concession = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->whereRAW(' (fees_payment_details.is_concession = 0 AND fees_payment_details.is_waiver = 1) ') 
                            ->where('fees_payment_details.cancel_status', '2')
                            //->whereNuLL('users.alumni_status')
                            ->orderby('fees_payment_details.id','desc'); 

                            $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->whereRAW(' (fees_payment_details.is_concession = 0 AND fees_payment_details.is_waiver = 1) ') 
                            //->where('fees_payment_details.cancel_status', '0')
                            //->whereNuLL('users.alumni_status') 
                            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                                'creator.name as creator_name');  

                            if (!empty($search)) { 
                                    $fee_summary_list->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                    $overall_fee_concession->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                    $cancelled_fee_concession->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                 
                            }             

                            if($batch > 0){
                                $fee_summary_list->where('fees_payment_details.batch',$batch); 
                                $overall_fee_concession->where('fees_payment_details.batch',$batch);
                                $cancelled_fee_concession->where('fees_payment_details.batch',$batch);
                            }
                            if($class_id > 0){
                                $fee_summary_list->where('fees_payment_details.class_id',$class_id); 
                                $overall_fee_concession->where('fees_payment_details.class_id',$class_id);
                                $cancelled_fee_concession->where('fees_payment_details.class_id',$class_id);
                            }
                            if($section_id > 0){
                                $fee_summary_list->where('fees_payment_details.section_id',$section_id); 
                                $overall_fee_concession->where('fees_payment_details.section_id',$section_id);
                                $cancelled_fee_concession->where('fees_payment_details.section_id',$section_id);
                            }
                            if($student_id > 0){
                                $fee_summary_list->where('fees_payment_details.student_id',$student_id); 
                                $overall_fee_concession->where('fees_payment_details.student_id',$student_id);
                                $cancelled_fee_concession->where('fees_payment_details.student_id',$student_id);
                            }
                            if($fee_category > 0){
                                $fee_summary_list->where('fee_categories.id',$fee_category); 
                                $overall_fee_concession->where('fee_categories.id',$fee_category);
                                $cancelled_fee_concession->where('fee_categories.id',$fee_category);
                            }
                            if($fee_item_id > 0){
                                $fee_summary_list->where('fee_items.id',$fee_item_id); 
                                $overall_fee_concession->where('fee_items.id',$fee_item_id);
                                $cancelled_fee_concession->where('fee_items.id',$fee_item_id);
                            }
                            if(!empty(trim($mindate))) {
                                $mindate = date('Y-m-d', strtotime($mindate));
                                $fee_summary_list->where('fees_payment_details.created_at', '>=', $mindate); 
                                $overall_fee_concession->where('fees_payment_details.created_at', '>=', $mindate);
                                $cancelled_fee_concession->where('fees_payment_details.created_at', '>=', $mindate);
                    
                            }
                            if(!empty(trim($maxdate))) {
                                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                                $fee_summary_list->where('fees_payment_details.created_at', '<=', $maxdate); 
                                $overall_fee_concession->where('fees_payment_details.created_at', '<=', $maxdate);
                                $cancelled_fee_concession->where('fees_payment_details.created_at', '<=', $maxdate);
                            }

 
                            $orderby = 'fees_payment_details.id'; 
                            $dir = 'DESC'; 

                            $overall_fee_concession = $overall_fee_concession->sum('fees_payment_details.concession_amount');
                            $cancelled_fee_concession  = $cancelled_fee_concession->sum('fees_payment_details.concession_amount');

                            $total_concession = $overall_fee_concession - $cancelled_fee_concession;

                            $fee_collection = $fee_summary_list->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get(); 


                        if($fee_collection->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$fee_collection, 'total_concession'=>$total_concession]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeeConcessionReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;  
                $fee_category = ((isset($input) && isset($input['fee_category']))) ? $input['fee_category'] : 0;  
                $fee_item_id = ((isset($input) && isset($input['fee_item_id']))) ? $input['fee_item_id'] : 0;  
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : ''; 
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $overall_fee_concession = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->whereRAW(' (fees_payment_details.is_concession = 1 AND fees_payment_details.is_waiver = 0) ') 
                            ->where('fees_payment_details.cancel_status', '0')
                            //->whereNuLL('users.alumni_status')
                            ->orderby('fees_payment_details.id','desc'); 

                            $cancelled_fee_concession = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->whereRAW(' (fees_payment_details.is_concession = 1 AND fees_payment_details.is_waiver = 0) ') 
                            ->where('fees_payment_details.cancel_status', '2')
                            //->whereNuLL('users.alumni_status')
                            ->orderby('fees_payment_details.id','desc'); 

                            $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
                            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
                            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
                            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
                            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
                            ->where('fees_payment_details.school_id', $school_id)->where('users.school_college_id', $school_id)
                            ->where('users.delete_status',0)->where('users.status','ACTIVE')
                            ->whereRAW(' (fees_payment_details.is_concession = 1 AND fees_payment_details.is_waiver = 0) ') 
                            //->where('fees_payment_details.cancel_status', '0')
                            //->whereNuLL('users.alumni_status') 
                            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                                'creator.name as creator_name');  

                            if (!empty($search)) { 
                                    $fee_summary_list->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                    $overall_fee_concession->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                    $cancelled_fee_concession->whereRaw('( fee_categories.name like "%'.$search . '%" OR fee_items.item_name like "%'.$search . '%" OR fee_items.item_code like "%'.$search . '%" OR users.name like "%'.$search . '%" OR users.admission_no like "%'.$search . '%"  OR classes.class_name like "%'.$search . '%" OR sections.section_name like "%'.$search . '%" OR creator.name like "%'.$search . '%")'); 
                                 
                            }             

                            if($batch > 0){
                                $fee_summary_list->where('fees_payment_details.batch',$batch); 
                                $overall_fee_concession->where('fees_payment_details.batch',$batch);
                                $cancelled_fee_concession->where('fees_payment_details.batch',$batch);
                            }
                            if($class_id > 0){
                                $fee_summary_list->where('fees_payment_details.class_id',$class_id); 
                                $overall_fee_concession->where('fees_payment_details.class_id',$class_id);
                                $cancelled_fee_concession->where('fees_payment_details.class_id',$class_id);
                            }
                            if($section_id > 0){
                                $fee_summary_list->where('fees_payment_details.section_id',$section_id); 
                                $overall_fee_concession->where('fees_payment_details.section_id',$section_id);
                                $cancelled_fee_concession->where('fees_payment_details.section_id',$section_id);
                            }
                            if($student_id > 0){
                                $fee_summary_list->where('fees_payment_details.student_id',$student_id); 
                                $overall_fee_concession->where('fees_payment_details.student_id',$student_id);
                                $cancelled_fee_concession->where('fees_payment_details.student_id',$student_id);
                            }
                            if($fee_category > 0){
                                $fee_summary_list->where('fee_categories.id',$fee_category); 
                                $overall_fee_concession->where('fee_categories.id',$fee_category);
                                $cancelled_fee_concession->where('fee_categories.id',$fee_category);
                            }
                            if($fee_item_id > 0){
                                $fee_summary_list->where('fee_items.id',$fee_item_id); 
                                $overall_fee_concession->where('fee_items.id',$fee_item_id);
                                $cancelled_fee_concession->where('fee_items.id',$fee_item_id);
                            }
                            if(!empty(trim($mindate))) {
                                $mindate = date('Y-m-d', strtotime($mindate));
                                $fee_summary_list->where('fees_payment_details.created_at', '>=', $mindate); 
                                $overall_fee_concession->where('fees_payment_details.created_at', '>=', $mindate);
                                $cancelled_fee_concession->where('fees_payment_details.created_at', '>=', $mindate);
                    
                            }
                            if(!empty(trim($maxdate))) {
                                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                                $fee_summary_list->where('fees_payment_details.created_at', '<=', $maxdate); 
                                $overall_fee_concession->where('fees_payment_details.created_at', '<=', $maxdate);
                                $cancelled_fee_concession->where('fees_payment_details.created_at', '<=', $maxdate);
                            }

 
                            $orderby = 'fees_payment_details.id'; 
                            $dir = 'DESC'; 

                            $overall_fee_concession = $overall_fee_concession->sum('fees_payment_details.concession_amount');
                            $cancelled_fee_concession  = $cancelled_fee_concession->sum('fees_payment_details.concession_amount');

                            $total_concession = $overall_fee_concession - $cancelled_fee_concession;

                            $fee_collection = $fee_summary_list->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get(); 


                        if($fee_collection->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$fee_collection, 'total_concession'=>$total_concession]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeeOverallReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;   
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        $acadamic_year = ''; 
                        $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                        if(!empty($settings)) {
                            $acadamic_year = trim($settings->acadamic_year);
                        }   

                        OAFeesSections::$acadamic_year = $acadamic_year; 
                        $sectionsqry = OAFeesSections::leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                            ->where('sections.status','=','ACTIVE')
                            ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');
                        $filteredqry = OAFeesSections::leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                            ->where('sections.status','=','ACTIVE')
                            ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');
 
                        $sectionsqry->where('classes.school_id', $school_id);
                        $filteredqry->where('classes.school_id', $school_id); 

                        if($class_id>0){
                            $sectionsqry->where('class_id',$class_id);
                            $filteredqry->where('class_id',$class_id);
                        }
                        if($section_id>0){
                            $sectionsqry->where('sections.id',$section_id);
                            $filteredqry->where('sections.id',$section_id);
                        }

                        if (!empty($order)) {
                            $orderby = $columns[$order]['name'];
                        } else {
                            $orderby = 'classes.position';
                        }
                        if (empty($dir)) {
                            $dir = 'ASC';
                        } 

                        $sections = $sectionsqry->orderby($orderby, $dir)->get();


                        if($sections->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$sections]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeeReceiptsCancelledReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;  
                $fee_category = ((isset($input) && isset($input['fee_category']))) ? $input['fee_category'] : 0;  
                $fee_item_id = ((isset($input) && isset($input['fee_item_id']))) ? $input['fee_item_id'] : 0;  
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if(empty($batch)) { 
                            $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                            if(!empty($settings)) {
                                $batch = trim($settings->acadamic_year);
                            } 
                        }


                        $feesummary_qry = FeesReceiptDetail::leftjoin('accounts', 'accounts.id', 'fees_receipt_details.account_id')
                            ->leftjoin('receipt_heads', 'receipt_heads.id', 'accounts.recepit_id')
                            ->leftjoin('payment_modes', 'payment_modes.id', 'fees_receipt_details.payment_mode')
                            ->leftjoin('fee_cancel_reasons', 'fee_cancel_reasons.id', 'fees_receipt_details.cancel_reason')
                            ->leftjoin('users as cancellor', 'cancellor.id', 'fees_receipt_details.canceled_by') 
                            ->leftjoin('users as creator', 'creator.id', 'fees_receipt_details.posted_by') 
                            ->leftjoin('users as scholar', 'scholar.id', 'fees_receipt_details.student_id')
                            ->leftjoin('students', 'students.user_id', 'fees_receipt_details.student_id')
                            ->leftjoin('classes', 'classes.id', 'students.class_id')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->where('fees_receipt_details.school_id', $school_id)
                            ->where('fees_receipt_details.batch', $batch)->where('fees_receipt_details.cancel_status', 1) 
                            ->select('fees_receipt_details.*', 'creator.name as creator_name', 'classes.class_name', 
                                'sections.section_name', 'scholar.name',  'scholar.admission_no', 'payment_modes.name as payment_name',
                                'receipt_heads.name as receipt_head_name', 'accounts.account_name', 
                                'fee_cancel_reasons.cancel_reason as fee_cancel_reason', 'cancellor.name as cancellor_name'
                            );   

                            if (!empty($search)) {
                                if (isset($search) && !empty($search)) {
                                    $feesummary_qry->whereRaw('( accounts.account_name like "%'.$search . '%" OR payment_modes.name like "%'.$search . '%" OR scholar.name like "%'.$search . '%" OR scholar.mobile like "%'.$search . '%" OR scholar.admission_no like "%'.$search . '%" )'); 
                                }
                            }  

                            if(!empty(trim($mindate))) {
                                $mindate = date('Y-m-d', strtotime($mindate));
                                $feesummary_qry->where('fees_receipt_details.created_at', '>=', $mindate); 
                    
                            }
                            if(!empty(trim($maxdate))) {
                                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                                $feesummary_qry->where('fees_receipt_details.created_at', '<=', $maxdate); 
                            }


                            if($student_id>0) {
                                $feesummary_qry->where('fees_receipt_details.student_id', $student_id); 
                            }

                            if($class_id>0) {
                                $feesummary_qry->where('students.class_id', $class_id); 
                            }

                            if($section_id>0) {
                                $feesummary_qry->where('students.section_id', $section_id); 
                            } 
                  
                            $orderby = 'fees_receipt_details.created_at'; 
                            $dir = 'DESC'; 

                            $feesummary = $feesummary_qry->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();


                        if($feesummary->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$feesummary ]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeeReceiptsReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $batch = ((isset($input) && isset($input['batch']))) ? $input['batch'] : '';  
                $class_id = ((isset($input) && isset($input['class_id']))) ? $input['class_id'] : 0;  
                $section_id = ((isset($input) && isset($input['section_id']))) ? $input['section_id'] : 0;  
                $student_id = ((isset($input) && isset($input['student_id']))) ? $input['student_id'] : 0;  
                $fee_category = ((isset($input) && isset($input['fee_category']))) ? $input['fee_category'] : 0;  
                $fee_item_id = ((isset($input) && isset($input['fee_item_id']))) ? $input['fee_item_id'] : 0;  
                $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
                $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if(empty($batch)) { 
                            $settings = DB::table('admin_settings')->where('school_id', $school_id)->orderby('id', 'asc')->first();
                            if(!empty($settings)) {
                                $batch = trim($settings->acadamic_year);
                            } 
                        }


                        $feesummary_qry = FeesReceiptDetail::leftjoin('accounts', 'accounts.id', 'fees_receipt_details.account_id')
                            ->leftjoin('receipt_heads', 'receipt_heads.id', 'accounts.recepit_id')
                            ->leftjoin('payment_modes', 'payment_modes.id', 'fees_receipt_details.payment_mode')
                            ->leftjoin('fee_cancel_reasons', 'fee_cancel_reasons.id', 'fees_receipt_details.cancel_reason')
                            ->leftjoin('users as cancellor', 'cancellor.id', 'fees_receipt_details.canceled_by') 
                            ->leftjoin('users as creator', 'creator.id', 'fees_receipt_details.posted_by') 
                            ->leftjoin('users as scholar', 'scholar.id', 'fees_receipt_details.student_id')
                            ->leftjoin('students', 'students.user_id', 'fees_receipt_details.student_id')
                            ->leftjoin('classes', 'classes.id', 'students.class_id')
                            ->leftjoin('sections', 'sections.id', 'students.section_id')
                            ->where('fees_receipt_details.school_id', $school_id)
                            ->where('fees_receipt_details.batch', $batch)->where('fees_receipt_details.cancel_status', 0) 
                            ->select('fees_receipt_details.*', 'creator.name as creator_name', 'classes.class_name', 
                                'sections.section_name', 'scholar.name',  'scholar.admission_no', 'payment_modes.name as payment_name',
                                'receipt_heads.name as receipt_head_name', 'accounts.account_name', 
                                'fee_cancel_reasons.cancel_reason as fee_cancel_reason', 'cancellor.name as cancellor_name'
                            );   

                            if (!empty($search)) {
                                if (isset($search) && !empty($search)) {
                                    $feesummary_qry->whereRaw('( accounts.account_name like "%'.$search . '%" OR payment_modes.name like "%'.$search . '%" OR scholar.name like "%'.$search . '%" OR scholar.mobile like "%'.$search . '%" OR scholar.admission_no like "%'.$search . '%" )'); 
                                }
                            }  

                            if(!empty(trim($mindate))) {
                                $mindate = date('Y-m-d', strtotime($mindate));
                                $feesummary_qry->where('fees_receipt_details.created_at', '>=', $mindate); 
                    
                            }
                            if(!empty(trim($maxdate))) {
                                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                                $feesummary_qry->where('fees_receipt_details.created_at', '<=', $maxdate); 
                            }


                            if($student_id>0) {
                                $feesummary_qry->where('fees_receipt_details.student_id', $student_id); 
                            }

                            if($class_id>0) {
                                $feesummary_qry->where('students.class_id', $class_id); 
                            }

                            if($section_id>0) {
                                $feesummary_qry->where('students.section_id', $section_id); 
                            } 
                  
                            $orderby = 'fees_receipt_details.created_at'; 
                            $dir = 'DESC'; 

                            $feesummary = $feesummary_qry->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();


                        if($feesummary->isNotEmpty()) { 
                            return response()->json([ 'status' => 1, 'message' => 'Fee Collection', 'data'=>$feesummary ]);
                        } else {
                            return response()->json([ 'status' => 0, 'message' => 'No Collection']);
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

    public function getFeeSummaryReport(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $fee_filter = ((isset($input) && isset($input['fee_filter']))) ? $input['fee_filter'] : '';  
                $fee_type = ((isset($input) && isset($input['fee_type']))) ? $input['fee_type'] : '';  
                $dateFilter = ((isset($input) && isset($input['dateFilter']))) ? $input['dateFilter'] : 0;  
                $search = ((isset($input) && isset($input['search']))) ? $input['search'] : '';  
                $order = ((isset($input) && isset($input['order']))) ? $input['order'] : 0;   

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        if(!empty($fee_type)){ 
                            $types = [$fee_type];
                        } else {
                            $types = ['COLLECTED','CONCESSION','WAIVER'];
                        }
                        $final_array = ''; $recordsTotal = $total_amount = 0; 

                        if(empty($fee_filter)) {
                            $fee_filter = 'ACCOUNT';
                        }

                        if(empty($dateFilter)) {
                            $dateFilter = date('M d, Y') .' - '. date('M d, Y');
                        }

                        if(!empty($dateFilter)) {
                            $dates  = explode(' - ', $dateFilter);
                            $start = date('Y-m-d', strtotime($dates[0]));
                            $end = date('Y-m-d', strtotime($dates[1])); 
                        }

                        if(!empty($fee_filter)){

                            switch($fee_filter) {
                                case 'ACCOUNT' :   

                                    if(empty($fee_type)){  

                                        $collected_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('accounts.id')
                                            ->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $collected_list->whereRaw('( accounts.account_name like "%'.$search . '%"  )'); 
                                            }
                                        } 

                                        $collected_list->select('accounts.account_name', DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $collected_list = $collected_list->orderby($orderby, $dir)->get(); 

                                        
                                        $concession_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',1)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('accounts.id')
                                            ->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $concession_list->whereRaw('( accounts.account_name like "%'.$search . '%"  )'); 
                                            }
                                        } 

                                        $concession_list->select('accounts.account_name', DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $concession_list = $concession_list->orderby($orderby, $dir)->get(); 

                                        
                                        $waiver_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',1)->groupby('accounts.id')
                                            ->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $waiver_list->whereRaw('( accounts.account_name like "%'.$search . '%"  )'); 
                                            }
                                        } 

                                        $waiver_list->select('accounts.account_name', DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $waiver_list = $waiver_list->orderby($orderby, $dir)->orderby('accounts.account_name', 'asc')->get(); 

                                        /*echo "<pre>"; print_r($collected_list->toArray());
                                        echo "<pre>"; print_r($concession_list->toArray());
                                        echo "<pre>"; print_r($waiver_list->toArray());*/


                                        $final_array = [];

                                        if($collected_list->isNotEmpty()) {
                                            $collected_array = $collected_list->toArray();
                                            $final_array = array_merge($final_array, $collected_array);
                                        }
                                        if($concession_list->isNotEmpty()) {
                                            $concession_array = $concession_list->toArray();
                                            $final_array = array_merge($final_array, $concession_array);
                                        }
                                        if($waiver_list->isNotEmpty()) {
                                            $waiver_array = $waiver_list->toArray();
                                            $final_array = array_merge($final_array, $waiver_array);
                                        }
                                        //echo "<pre>"; print_r($final_array); 
             

                                    } else {

                                        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->groupby('accounts.id'); 

                                        if($fee_type == 'COLLECTED') { 
                                            $fee_summary_list->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('accounts.id')
                                                ->select('accounts.account_name', DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount')); 
                                        } elseif($fee_type == 'CONCESSION') { 
                                            $fee_summary_list->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',1)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('accounts.id')
                                                ->select('accounts.account_name', DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } elseif($fee_type == 'WAIVER') { 
                                            $fee_summary_list->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',1)->groupby('accounts.id')
                                                ->select('accounts.account_name', DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } 

                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $fee_summary_list->whereRaw('( accounts.account_name like "%'.$search. '%"  )'); 
                                            }
                                        }  

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        }  

                                        $fee_summary_list = $fee_summary_list->orderby($orderby, $dir)->get();
             
                                        $final_array = [];

                                        if($fee_summary_list->isNotEmpty()) {
                                            $fee_summary_list = $fee_summary_list->toArray();
                                            $final_array = array_merge($final_array, $fee_summary_list);
                                        } 
                                    } 
                                     
                                break;

                                case 'CATEGORY' :   

                                    if(empty($fee_type)){  

                                        $collected_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fee_categories.id')
                                            ->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $collected_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $collected_list->select(DB::RAW(' CONCAT(accounts.account_name, " - ", fee_categories.name) as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $collected_list = $collected_list->orderby($orderby, $dir)->get(); 

                                        
                                        $concession_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',1)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fee_categories.id')
                                            ->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $concession_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $concession_list->select(DB::RAW(' CONCAT(accounts.account_name, " - ", fee_categories.name) as account_name'), DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $concession_list = $concession_list->orderby($orderby, $dir)->get(); 

                                        
                                        $waiver_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',1)->groupby('fee_categories.id')
                                            ->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $waiver_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $waiver_list->select(DB::RAW(' CONCAT(accounts.account_name, " - ", fee_categories.name) as account_name'), DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $waiver_list = $waiver_list->orderby($orderby, $dir)->get(); 

                                        /*echo "<pre>"; print_r($collected_list->toArray());
                                        echo "<pre>"; print_r($concession_list->toArray());
                                        echo "<pre>"; print_r($waiver_list->toArray());*/


                                        $final_array = [];

                                        if($collected_list->isNotEmpty()) {
                                            $collected_array = $collected_list->toArray();
                                            $final_array = array_merge($final_array, $collected_array);
                                        }
                                        if($concession_list->isNotEmpty()) {
                                            $concession_array = $concession_list->toArray();
                                            $final_array = array_merge($final_array, $concession_array);
                                        }
                                        if($waiver_list->isNotEmpty()) {
                                            $waiver_array = $waiver_list->toArray();
                                            $final_array = array_merge($final_array, $waiver_array);
                                        }
                                        //echo "<pre>"; print_r($final_array); 
             

                                    } else {

                                        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE'); 

                                        if($fee_type == 'COLLECTED') { 
                                            $fee_summary_list->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fee_categories.id')
                                                ->select(DB::RAW(' CONCAT(accounts.account_name, " - ", fee_categories.name) as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount')); 
                                        } elseif($fee_type == 'CONCESSION') { 
                                            $fee_summary_list->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',1)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fee_categories.id')
                                                ->select(DB::RAW(' CONCAT(accounts.account_name, " - ", fee_categories.name) as account_name'), DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } elseif($fee_type == 'WAIVER') { 
                                            $fee_summary_list->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',1)->groupby('fee_categories.id')
                                                ->select(DB::RAW(' CONCAT(accounts.account_name, " - ", fee_categories.name) as account_name'), DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } 

                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $fee_summary_list->whereRaw('( accounts.account_name like "%'.$search . '%"   OR fee_categories.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        }  

                                        $fee_summary_list = $fee_summary_list->orderby($orderby, $dir)->get();
             
                                        $final_array = [];

                                        if($fee_summary_list->isNotEmpty()) {
                                            $fee_summary_list = $fee_summary_list->toArray();
                                            $final_array = array_merge($final_array, $fee_summary_list);
                                        } 
                                    } 
                                     
                                break;

                                case 'ITEM' :   

                                    if(empty($fee_type)){  

                                        $collected_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id') 
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_item_id')
                                            ->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $collected_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $collected_list->select(DB::RAW(' CONCAT(fee_categories.name, " - ", fee_items.item_name) as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $collected_list = $collected_list->orderby($orderby, $dir)->get(); 

                                        
                                        $concession_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',1)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_item_id')
                                            ->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $concession_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $concession_list->select(DB::RAW(' CONCAT(fee_categories.name, " - ", fee_items.item_name) as account_name'), DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $concession_list = $concession_list->orderby($orderby, $dir)->get(); 

                                        
                                        $waiver_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',1)->groupby('fee_structure_items.fee_item_id')
                                            ->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $waiver_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $waiver_list->select(DB::RAW(' CONCAT(fee_categories.name, " - ", fee_items.item_name) as account_name'), DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $waiver_list = $waiver_list->orderby($orderby, $dir)->get(); 

                                        /*echo "<pre>"; print_r($collected_list->toArray());
                                        echo "<pre>"; print_r($concession_list->toArray());
                                        echo "<pre>"; print_r($waiver_list->toArray());*/


                                        $final_array = [];

                                        if($collected_list->isNotEmpty()) {
                                            $collected_array = $collected_list->toArray();
                                            $final_array = array_merge($final_array, $collected_array);
                                        }
                                        if($concession_list->isNotEmpty()) {
                                            $concession_array = $concession_list->toArray();
                                            $final_array = array_merge($final_array, $concession_array);
                                        }
                                        if($waiver_list->isNotEmpty()) {
                                            $waiver_array = $waiver_list->toArray();
                                            $final_array = array_merge($final_array, $waiver_array);
                                        }
                                        //echo "<pre>"; print_r($final_array); 
             

                                    } else {

                                        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE'); 

                                        if($fee_type == 'COLLECTED') { 
                                            $fee_summary_list->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_item_id')
                                                ->select(DB::RAW(' CONCAT(fee_categories.name, " - ", fee_items.item_name) as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount')); 
                                        } elseif($fee_type == 'CONCESSION') { 
                                            $fee_summary_list->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',1)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_item_id')
                                                ->select(DB::RAW(' CONCAT(fee_categories.name, " - ", fee_items.item_name) as account_name'), DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } elseif($fee_type == 'WAIVER') { 
                                            $fee_summary_list->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',1)->groupby('fee_structure_items.fee_item_id')
                                                ->select(DB::RAW(' CONCAT(fee_categories.name, " - ", fee_items.item_name) as account_name'), DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        }  

                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $fee_summary_list->whereRaw('( accounts.account_name like "%'.$search . '%"   OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%" )'); 
                                            }
                                        }  

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        }  

                                        $fee_summary_list = $fee_summary_list->orderby($orderby, $dir)->get();
             
                                        $final_array = [];

                                        if($fee_summary_list->isNotEmpty()) {
                                            $fee_summary_list = $fee_summary_list->toArray();
                                            $final_array = array_merge($final_array, $fee_summary_list);
                                        } 
                                    } 
                                     
                                break;

                                case 'TERM' :   

                                    if(empty($fee_type)){  

                                        $collected_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id') 
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_terms', 'fee_terms.id', 'fee_structure_items.fee_term_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_term_id')
                                            ->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $collected_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%"  OR fee_terms.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $collected_list->select(DB::RAW(' fee_terms.name as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $collected_list = $collected_list->orderby($orderby, $dir)->get(); 

                                        
                                        $concession_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_terms', 'fee_terms.id', 'fee_structure_items.fee_term_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',1)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_term_id')
                                            ->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $concession_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%"  OR fee_terms.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $concession_list->select(DB::RAW(' fee_terms.name as account_name'), DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $concession_list = $concession_list->orderby($orderby, $dir)->get(); 

                                        
                                        $waiver_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_terms', 'fee_terms.id', 'fee_structure_items.fee_term_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',1)->groupby('fee_structure_items.fee_term_id')
                                            ->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $waiver_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%"  OR fee_terms.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $waiver_list->select(DB::RAW(' fee_terms.name as account_name'), DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $waiver_list = $waiver_list->orderby($orderby, $dir)->get(); 

                                        /*echo "<pre>"; print_r($collected_list->toArray());
                                        echo "<pre>"; print_r($concession_list->toArray());
                                        echo "<pre>"; print_r($waiver_list->toArray());*/


                                        $final_array = [];

                                        if($collected_list->isNotEmpty()) {
                                            $collected_array = $collected_list->toArray();
                                            $final_array = array_merge($final_array, $collected_array);
                                        }
                                        if($concession_list->isNotEmpty()) {
                                            $concession_array = $concession_list->toArray();
                                            $final_array = array_merge($final_array, $concession_array);
                                        }
                                        if($waiver_list->isNotEmpty()) {
                                            $waiver_array = $waiver_list->toArray();
                                            $final_array = array_merge($final_array, $waiver_array);
                                        }
                                        //echo "<pre>"; print_r($final_array); 
             

                                    } else {

                                        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('fee_terms', 'fee_terms.id', 'fee_structure_items.fee_term_id') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE'); 

                                        if($fee_type == 'COLLECTED') { 
                                            $fee_summary_list->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_term_id')
                                                ->select(DB::RAW(' fee_terms.name as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount')); 
                                        } elseif($fee_type == 'CONCESSION') { 
                                            $fee_summary_list->whereRaw(' concession_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',1)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fee_structure_items.fee_term_id')
                                                ->select(DB::RAW(' fee_terms.name as account_name'), DB::RAW('"Concession" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } elseif($fee_type == 'WAIVER') { 
                                            $fee_summary_list->whereRaw(' is_waiver_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',1)->groupby('fee_structure_items.fee_term_id')
                                                ->select(DB::RAW(' fee_terms.name as account_name'), DB::RAW('"Waiver" as fee_type'), DB::RAW(' sum(concession_amount) as total_amount')); 
                                        } 

                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $fee_summary_list->whereRaw('( accounts.account_name like "%'.$search . '%"   OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%"  OR fee_terms.name like "%'.$search . '%" )'); 
                                            }
                                        }  

                                        if($order == 0) {
                                            $orderby = 'accounts.account_name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'accounts.account_name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        }  

                                        $fee_summary_list = $fee_summary_list->orderby($orderby, $dir)->get();
             
                                        $final_array = [];

                                        if($fee_summary_list->isNotEmpty()) {
                                            $fee_summary_list = $fee_summary_list->toArray();
                                            $final_array = array_merge($final_array, $fee_summary_list);
                                        } 
                                    } 
                                     
                                break;

                                case 'PAYMENTMODE' :   

                                    if(empty($fee_type)){  

                                        $collected_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id') 
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('payment_modes', 'payment_modes.id', 'fees_payment_details.payment_mode') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE')->where('fees_payment_details.is_concession',0)
                                            ->where('fees_payment_details.is_waiver',0)->groupby('fees_payment_details.payment_mode')
                                            ->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ');


                                        if (!empty($search)) {
                                            if (isset($search) && !empty($search)) {
                                                $collected_list->whereRaw('( accounts.account_name like "%'.$search . '%"  OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%"  OR payment_modes.name like "%'.$search . '%"  OR payment_modes.name like "%'.$search . '%" )'); 
                                            }
                                        } 

                                        $collected_list = $collected_list->select(DB::RAW(' payment_modes.name as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount'));

                                        if($order == 0) {
                                            $orderby = 'payment_modes.name';
                                        } elseif($order == 2) {
                                            $orderby = 'total_amount';
                                        } else {
                                            $orderby = 'payment_modes.name';
                                        }

                                        if (empty($dir)) {
                                            $dir = 'ASC';
                                        } 

                                        $collected_list = $collected_list->orderby($orderby, $dir)->get(); 

                                        
                                        $concession_list = '';  // // not applicable

                                        
                                        $waiver_list = ''; // not applicable

                                        /*echo "<pre>"; print_r($collected_list->toArray());
                                        echo "<pre>"; print_r($concession_list->toArray());
                                        echo "<pre>"; print_r($waiver_list->toArray());*/


                                        $final_array = [];

                                        if($collected_list->isNotEmpty()) {
                                            $collected_array = $collected_list->toArray();
                                            $final_array = array_merge($final_array, $collected_array);
                                        } 
                                        //echo "<pre>"; print_r($final_array); 
             

                                    } else {
             
                                        $final_array = [];

                                        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
                                            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                                            ->leftjoin('payment_modes', 'payment_modes.id', 'fees_payment_details.payment_mode') 
                                            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                                            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
                                            ->leftjoin('accounts', 'accounts.id', 'fee_categories.account_id') 
                                            ->where('fees_payment_details.school_id', $school_id) 
                                            ->where('fees_payment_details.cancel_status',0)->where('fee_structure_items.cancel_status',0)
                                            ->where('fee_structure_lists.cancel_status',0)->where('fee_categories.status','ACTIVE')
                                            ->where('accounts.status','ACTIVE'); 

                                        if($fee_type == 'COLLECTED') { 
                                            $fee_summary_list->whereRaw(' paid_date BETWEEN "'.$start.'" AND "'.$end.'" ')
                                                ->where('fees_payment_details.is_concession',0)
                                                ->where('fees_payment_details.is_waiver',0)->groupby('fees_payment_details.payment_mode')
                                                ->select(DB::RAW(' payment_modes.name as account_name'), DB::RAW('"Collected" as fee_type'), DB::RAW(' sum(amount_paid) as total_amount')); 

                                            if (!empty($search)) {
                                                if (isset($search) && !empty($search)) {
                                                    $fee_summary_list->whereRaw('( accounts.account_name like "%'.$search . '%"   OR fee_categories.name like "%'.$search . '%"  OR fee_items.item_name like "%'.$search . '%"  OR payment_modes.name like "%'.$search . '%" )'); 
                                                }
                                            } 

                                            if($order == 0) {
                                                $orderby = 'accounts.account_name';
                                            } elseif($order == 2) {
                                                $orderby = 'total_amount';
                                            } else {
                                                $orderby = 'accounts.account_name';
                                            }

                                            if (empty($dir)) {
                                                $dir = 'ASC';
                                            }  

                                            $fee_summary_list = $fee_summary_list->orderby($orderby,$dir)->get();
                                            if($fee_summary_list->isNotEmpty()) {
                                                $fee_summary_list = $fee_summary_list->toArray();
                                                $final_array = array_merge($final_array, $fee_summary_list);
                                            } 
                                        } elseif($fee_type == 'CONCESSION') { // not applicable
                                        } elseif($fee_type == 'WAIVER') {  // not applicable
                                        }  

                                        
                                    } 
                                     
                                break;
                            }

                        } 

                        if (!empty($final_array)) {
                            foreach ($final_array as $post) {
                                $data[] = $post;
                                $total_amount += $post['total_amount'];
                            }
                            $recordsTotal = count($final_array);
                        } 
  
                        return response()->json([ 'status' => 1, 'message' => 'Fee Report', 'data' => $final_array, 'total' => $total_amount]);
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

    public function getStaffDailyAttendance(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $monthyear = ((isset($input) && isset($input['monthyear']))) ? $input['monthyear'] : date('Y-m');  
                $new_date = ((isset($input) && isset($input['date']))) ? $input['date'] : date('Y-m-d');   
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 
                        if(empty($monthyear)) {
                            $monthyear = date('Y-m');
                        }

                        if(empty($date)) {
                            $date = date('Y-m-d');
                        }

                        $lastdate = date('t', strtotime($monthyear));
                        User::$monthyear = $monthyear;

                        $teachers = User::with('teacherdailyattendance')->where('users.school_college_id', $school_id)
                            ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                            //->where('user_type', 'TEACHER')
                            ->whereNotIn('user_type',  ['SUPER_ADMIN', 'GUESTUSER', 'STUDENT', 'SCHOOL'])
                            ->select('users.id', 'name', 'email', 'mobile', 'teachers.emp_no','users.profile_image')
                            ->orderby('users.name', 'ASC')
                            ->get();
                        $userids = array();
                        foreach($teachers as $k=>$v){
                        array_push($userids,$v->id);
                        }

                        list($year, $month) = explode('-', $monthyear);

                        $sundays = CommonController::getSundays($year, $month); 
                        $saturdays = ''; //CommonController::getSaturdays($year, $month);  

                        $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                           ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                           ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                           // $students[$k]->holidays_list = $holidays;
                           if($holidays->isNotEmpty()){
                               $holidays = $holidays->toArray();
                           }

                        $orderdate = explode('-', $new_date);
                        $year = $orderdate[0];
                        $month   = $orderdate[1];
                        $day  = $orderdate[2];
                        $day = $day * 1;

                  
                        $date = 'day_'.$day;
                        $fn_chk = TeachersDailyAttendance::whereIn('user_id', $userids)->where($date,1)->where('monthyear', $monthyear)->select('id')->get()->count();
                        $date2 = 'day_'.$day.'_an';
                        $an_chk = TeachersDailyAttendance::whereIn('user_id', $userids)->where($date2,1)->where('monthyear', $monthyear)->select('id')->get()->count();

                        if($teachers->isNotEmpty()) {
                            $teachers = $teachers->toArray();
                            //echo "<pre>"; print_r($teachers);exit;
                            /*$html = view('admin.loadteachers_dailyattendance')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate])->with('fn_chk',$fn_chk)->with('an_chk',$an_chk)->with('new_date',$new_date)->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays)->render();*/

                            return response()->json(['status' => 1, 'data' => $teachers, 'message' => 'Teacher attendance Detail']);

                        }   else {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'No Teacher attendance Detail']);
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

    public function getStaffLeaveList(Request $request) {
        try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $teacher_id = ((isset($input) && isset($input['teacher_id']))) ? $input['teacher_id'] : 0;  
                $mindate = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';   
                $maxdate = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';    
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $users_qry = Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->select('teacher_leave.*','users.name')->where('users.school_college_id', $school_id); 

                         if($teacher_id != '' || $teacher_id != 0){
                            $users_qry->where('user_id',$teacher_id); 
                         }

                         if(!empty(trim($mindate))) {
                            $mindate = date('Y-m-d', strtotime($mindate));
                            $users_qry->whereRaw('from_date >= ?', [$mindate]); 
                
                        }
                        if(!empty(trim($maxdate))) {
                            $maxdate = date('Y-m-d', strtotime($maxdate));
                            $users_qry->whereRaw('from_date <= ?', [$maxdate]); 
                        } 

                        $orderby = 'id';  $dir = 'DESC'; 

                        $teachers = $users_qry->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();

                        if($teachers->isNotEmpty()) { 

                            return response()->json(['status' => 1, 'data' => $teachers, 'message' => 'Teacher Leave List']);

                        }   else {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'No Teacher Leave List']);
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

    public function getStaffAttendanceReport(Request $request) {
        //try {   
            $inputJSON = file_get_contents('php://input');

            $input = json_decode($inputJSON, TRUE);

            $requiredParams = ['user_id', 'api_token', 'school_id', 'monthyear' ];

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

            if(empty($error)) {

                $userid = ((isset($input) && isset($input['user_id']))) ? $input['user_id'] : 0;  
                $school_id = ((isset($input) && isset($input['school_id']))) ? $input['school_id'] : 0;  
                $monthyear = ((isset($input) && isset($input['monthyear']))) ? $input['monthyear'] : '';  
                 

                $mindate = ((isset($input) && isset($input['from_date']))) ? $input['from_date'] : '';   
                $maxdate = ((isset($input) && isset($input['to_date']))) ? $input['to_date'] : '';    
                $search = isset($input['search']) ? $input['search'] : '';

                $page_no = ((isset($input) && isset($input['page_no']))) ? $input['page_no'] : 0;   

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $users_qry = Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->select('teacher_leave.*','users.name')->where('users.school_college_id', $school_id); 

                         if($teacher_id != '' || $teacher_id != 0){
                            $users_qry->where('user_id',$teacher_id); 
                         }

                         if(!empty(trim($mindate))) {
                            $mindate = date('Y-m-d', strtotime($mindate));
                            $users_qry->whereRaw('from_date >= ?', [$mindate]); 
                
                        }
                        if(!empty(trim($maxdate))) {
                            $maxdate = date('Y-m-d', strtotime($maxdate));
                            $users_qry->whereRaw('from_date <= ?', [$maxdate]); 
                        } 

                        $orderby = 'id';  $dir = 'DESC'; 

                        $teachers = $users_qry->orderBy($orderby, $dir)->offset($page_no)->limit($limit)->get();

                        if($teachers->isNotEmpty()) { 

                            return response()->json(['status' => 1, 'data' => $teachers, 'message' => 'Teacher Leave List']);

                        }   else {
                            return response()->json(['status' => 0, 'data' => [], 'message' => 'No Teacher Leave List']);
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
}