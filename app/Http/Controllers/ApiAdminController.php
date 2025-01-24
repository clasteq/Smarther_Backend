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

            $requiredParams = ['email','password', 'fcm_token', 'device_id', 'device_type', 'school_id']; 

            $error = (new ApiController())->checkParams($input, $requiredParams, $request);

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

                $user_type = 'SCHOOL';

                if (Auth::attempt(['email' => $email, 'password' => $password, 'user_type' => $user_type, 'id' =>$school_id])) {
                    $user = User::where('email', $email)->where('user_type', $user_type)->where('status', 'ACTIVE')
                        ->where('id', $school_id)->first();
                    if(empty($user)) {
                        return response()->json(['status' => 0, 'message' => 'Invalid Login']);
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

                } else {

                    return response()->json(['status' => 0, 'message' => 'Invalid Login Credential']);

                }
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
                        $content = (new AdminController())->getHomeContents($school_id, $limit);
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

                $api_token = $request->header('x-api-key');
                $limit = CommonController::$page_limit;

                $mes = User::checkTokenExpiry($userid, $api_token);
                $status = $mes['status'];   $message = $mes['message'];
                if($status != 1) {
                    return response()->json([ 'status' => $status, 'data' => null, 'message' => $message]);
                } 
                else {
                    if($userid > 0 && $school_id > 0) { 

                        $classes = DB::table('classes')->where('id', '>', 0)->where('school_id', $school_id) 
                            ->orderby('position', 'asc')->get();  
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
                if(empty($batch))   {
                    $acadamic_year = $batch = date('Y');

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
                        $ret = (new AdminController())->createPostHWs($request, $inarr); 
  
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
                if(empty($batch))   {
                    $acadamic_year = $batch = date('Y'); 
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
                if(empty($batch))   {
                    $acadamic_year = $batch = date('Y'); 
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
                if(empty($batch))   {
                    $acadamic_year = $batch = date('Y'); 
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
                            $treeview  = $this->module_tree($index, $update_id); 

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

}