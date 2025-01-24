<?php
namespace App\Http\Controllers;
 
use App\Models\Banner;
use App\Models\BackgroundTheme;
use App\Models\Category;
use App\Models\Chapters;
use App\Models\ChapterTopics;
use App\Models\Circulars;
use App\Models\Classes;
use App\Models\ClassesMaster;
use App\Models\Countries;
use App\Models\CommunicationPost;
use App\Models\CommunicationGroup;
use App\Models\Districts;
use App\Models\Grades;
use App\Models\Events;
use App\Models\Faq;
use App\Models\Holidays;
use App\Models\Homeworks;
use App\Http\Controllers\CommonController;
use App\Models\Periodtiming;
use App\Models\Sections;
use App\Models\OASections;
use App\Models\ClassTeacher;
use App\Models\SubjectMapping;
use App\Models\Slot;
use App\Models\Sports;
use App\Models\States;
use App\Models\Student;
use App\Models\Subjects;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\Topics;
use App\Models\Leaves;
use App\Models\Teacherleave;
use App\Models\User;
use App\Models\StudentAcademics;
use App\Models\Exams;
use App\Models\Terms;
use App\Models\MarksEntry;
use App\Models\MarksEntryItems;
use App\Models\QuestionBanks;
use App\Models\QuestionBankItems;
use App\Models\QuestionTypes;
use App\Models\QuestionTypeSettings;
use App\Models\Tests;
use App\Models\TestItems;
use App\Models\StudentTests;
use App\Models\StudentTestAnswers;
use App\Models\StudentsDailyAttendance;
use App\Models\TeachersDailyAttendance;
use App\Models\AttendanceApproval;
use App\Models\TestPapers;

use App\Models\DltTemplate;
use App\Models\CommunicationSms;

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
use App\Models\FeesReceiptDetail;

use App\Models\ContactsList;
use App\Models\ContactsFor;

use Auth;
use DB;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;
use Validator;
use View;
use PDF;
use File;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public $accepted_formats = ['jpeg', 'jpg', 'png'];
    public $accepted_formats_audio = ['mp3', 'mp4'];
    public $accepted_formats_qbt = ['mp3', 'mp4', 'jpeg', 'jpg', 'png', 'doc', 'docx', 'pdf'];
    public $school;

    public function __construct()    { 
        $ourl = config("constants.APP_URL"); 
        $url = $ourl; //URL('/'); 
        $curr = url()->full();
        $url = str_replace('/', '', $url);
        $curr = str_replace('/', '', $curr);
        $re = '/'.$url.'(.*)admin/'; 
        $str = $curr;
        $this->school =  '';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        //print_r($matches); exit;
        if(is_array($matches) && count($matches)>0) {
            $this->school = $matches[0][1];
        }  else {
            $re = '/'.$url.'(.*)page/'; 
            $str = $curr;
            $this->school =  '';
            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
            //print_r($matches); exit;
            if(is_array($matches) && count($matches)>0) {
                $this->school = $matches[0][1];
            }
        }



        //echo $school; exit;
    }

    public function deleteAccountUrl(){ 
        return view('users.delete_account'); 
    }

    public function termsconditions()    {   
        if(!empty($this->school)) { 
            $school_id = DB::table('users')->where('user_type', 'SCHOOL')->where('slug_name', $this->school)->value('id');
            if($school_id > 0) {
                $terms_conditions = DB::table('admin_settings')->where('school_id', $school_id)->value('terms_conditions'); 
                if(empty($terms_conditions)) {
                    $terms_conditions = DB::table('admin_settings')->where('id', 1)->value('terms_conditions'); 
                }
            }   else {
                $terms_conditions = DB::table('admin_settings')->where('id', 1)->value('terms_conditions'); 
            }
        }   else {
            $terms_conditions = DB::table('admin_settings')->where('id', 1)->value('terms_conditions');
        }
        return view('users.pagecontent')->with('content', $terms_conditions);
    }

    public function aboutus()    { 
        if(!empty($this->school)) { 
            $school_id = DB::table('users')->where('user_type', 'SCHOOL')->where('slug_name', $this->school)->value('id');
            if($school_id > 0) {
                $about = DB::table('admin_settings')->where('school_id', $school_id)->value('about'); 
                if(empty($about)) {
                    $about = DB::table('admin_settings')->where('id', 1)->value('about'); 
                }
            }   else {
                $about = DB::table('admin_settings')->where('id', 1)->value('about'); 
            }
        }   else {
            $about = DB::table('admin_settings')->where('id', 1)->value('about');
        } 
        return view('users.pagecontent')->with('content', $about);
    }

    public function policy()    { 
        if(!empty($this->school)) { 
            $school_id = DB::table('users')->where('user_type', 'SCHOOL')->where('slug_name', $this->school)->value('id');
            if($school_id > 0) {
                $privacy_policy = DB::table('admin_settings')->where('school_id', $school_id)->value('privacy_policy'); 
                if(empty($privacy_policy)) {
                    $privacy_policy = DB::table('admin_settings')->where('id', 1)->value('privacy_policy'); 
                }
            }   else {
                $privacy_policy = DB::table('admin_settings')->where('id', 1)->value('privacy_policy'); 
            }
        }   else {
            $privacy_policy = DB::table('admin_settings')->where('id', 1)->value('privacy_policy');
        }
   
        return view('users.pagecontent')->with('content', $privacy_policy);
    }

    public function index()
    {
        if (Auth::check()) {
            return redirect('/admin/home');
        } else {
            return view('admin.login');
        }
    }

    /* Function: postLogin
    Login Functionality
    Params: email , password
    return: JSON */

    public function postLogin(Request $request)
    {

        $userEmail = $request->input('email');

        $password = $request->input('password');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            $msg = $validator->errors()->all();

            return response()->json([

                'status' => "FAILED",
                'message' => $msg,

            ]);
        }

        if($this->school == 'clastechowner') {
            $user_type = 'SUPER_ADMIN';
        } else {
            $user_type = 'SCHOOL';
        }

        if (Auth::attempt(['email' => $userEmail, 'password' => $password, 'user_type' => $user_type, 'slug_name' => $this->school])) {

            $userStatus = User::where('email', $userEmail)->where('user_type', $user_type)->where('slug_name', $this->school)->first();

            $userStatus->last_login_date = date('Y-m-d');

            $userStatus->save();

            $current_session_id = Session::getId();
            $device_id = $request->ip();

            $current = DB::table('users_loginstatus')->where('session_id', $current_session_id)->get();
            if ($current->isNotEmpty()) {
            } else {
                DB::table('users_loginstatus')->insert(['user_id' => $userStatus->id,
                    'session_id' => $current_session_id,
                    'check_in' => date('Y-m-d H:i:s'),
                    'device_id' => $device_id,
                    'api_token_expiry' => $userStatus->api_token_expiry,
                    'status' => 'LOGIN',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            return response()->json(['status' => 'SUCCESS', 'message' => 'Please wait redirecting...']);

        } else {

            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);

        }

    }

    public function checkSetAdminCountry(Request $request)
    {
        $session_country = $request->session_country;
        if ($session_country > 0) {
            $country_code = Countries::where('id', $session_country)->value('phonecode');
            Session::put('session_country', $session_country);
            Session::put('session_country_code', $country_code);
        } else {
            Session::put('session_country', '');
            Session::put('session_country_code', '');
        }
        list($currency, $mrp_symbol) = CommonController::getAdminCurrency();

        Session::put('currency', $currency);
        Session::put('mrp_symbol', $mrp_symbol);

        return response()->json([

            'status' => "SUCCESS",
            'message' => 'Country set successfully',

        ]);
    }

    /* Function: homePage
    Loading Admin Home page */
    public function homePage()
    {
        if (Auth::check()) {
            $session_country_code = Session::get('session_country_code');
            $user_type = Auth::User()->user_type;

            if ($user_type == "SUPER_ADMIN" || $user_type == "SCHOOL") { // Super Admin
                $students_count = User::where('user_type', 'STUDENT')->where('delete_status',0)
                    ->where('users.school_college_id', Auth::User()->id)->where('status', 'ACTIVE')->count();
                $teachers_count = User::where('user_type', 'TEACHER')
                    ->where('users.school_college_id', Auth::User()->id)->where('status', 'ACTIVE')->count();

                $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
                $overall_fee_collected = FeesPaymentDetail::where('batch', $batch)
                                        ->where('school_id', Auth::User()->id)
                                        ->where('is_concession', '0')
                                        ->where('cancel_status', '0')
                                        ->sum('amount_paid');

                $startMonth = Carbon::today()->month;
                $endMonth = $startMonth + 1;
                $student_birthdays = DB::table('users')
                    ->leftjoin('students', 'students.user_id', 'users.id')
                    ->leftjoin('classes', 'students.class_id', 'classes.id')
                    ->leftjoin('sections', 'students.section_id', 'sections.id')
                    ->where('users.school_college_id', Auth::User()->id)->where('users.delete_status',0) 
                    ->whereIn(DB::raw('MONTH(users.dob)'), [$startMonth, $endMonth])
                    ->where('user_type', 'STUDENT') 
                    ->select('users.id', 'name', 'mobile', 'users.dob', 'users.profile_image', 
                        'students.class_id', 'students.section_id', 'classes.class_name', 'sections.section_name')
                    ->get();

                if($student_birthdays->isNotEmpty()) {
                    foreach($student_birthdays as $sk=>$sb) {
                        $student_birthdays[$sk]->is_profile_image = User::getUserProfileImageAttribute($sb->id);
                    } 
                }

                $staff_birthdays = DB::table('users')
                    ->leftjoin('teachers', 'teachers.user_id', 'users.id') 
                    ->where('users.school_college_id', Auth::User()->id)->where('users.delete_status',0)
                    ->whereIn(DB::raw('MONTH(users.dob)'), [$startMonth, $endMonth])->where('user_type', 'TEACHER') 
                    ->select('users.id', 'name', 'mobile', 'users.dob', 'users.profile_image', 
                        'teachers.department_name', 'teachers.designation')
                    ->get();

                if($staff_birthdays->isNotEmpty()) {
                    foreach($staff_birthdays as $sk=>$sb) {
                        $staff_birthdays[$sk]->is_profile_image = User::getUserProfileImageAttribute($sb->id);
                    } 
                } 

                $posts_arr = [];  $postsms_arr = [];
                $posts = CommunicationPost::where('delete_status', 0)->where('posted_by', Auth::User()->id)
                    ->orderby('id', 'desc')->skip(0)->take(1)->get();
                if($posts->isNotEmpty()) {
                    $posts_arr = $posts->toArray();
                }

                $postsms = CommunicationSms::where('delete_status', 0)->where('posted_by', Auth::User()->id)
                    ->orderby('id', 'desc')->skip(0)->take(1)->get();
                if($postsms->isNotEmpty()) {
                    $postsms_arr = $postsms->toArray();
                }

                return view::make('admin.home')->with([
                    'students_count' => $students_count,
                    'teachers_count' => $teachers_count,
                    'overall_fee_collected' => $overall_fee_collected,
                    'student_birthdays' => $student_birthdays,
                    'staff_birthdays' => $staff_birthdays,
                    'posts_arr' => $posts_arr,
                    'postsms_arr' => $postsms_arr
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: logout
    Logout the session and redirects to login page */
    public function logout(Request $request)
    {
        $current_session_id = Session::getId();
        $device_id = $request->ip();
        $current = DB::table('users_loginstatus')->where('session_id', $current_session_id)->get();
        if ($current->isNotEmpty()) {
            DB::table('users_loginstatus')->update([
                'check_out' => date('Y-m-d H:i:s'),
                'status' => 'LOGOUT',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            DB::table('users_loginstatus')->insert(['user_id' => Auth::User()->id,
                'session_id' => $current_session_id,
                'check_in' => date('Y-m-d H:i:s'),
                'device_id' => $device_id,
                'api_token_expiry' => Auth::User()->api_token_expiry,
                'status' => 'LOGIN',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if (Auth::check()) {
            Auth::logout();
            return redirect('/admin');
        } else {
            return redirect('/admin');
        }
    }

    // Settings Admin
    public function settings()
    {
        if (Auth::check()) {
            $settings = DB::table('admin_settings');
            if(Auth::User()->user_type == 'SCHOOL') {
                $settings->where('school_id', Auth::User()->id); 
            }
            $settings = $settings->orderby('id', 'asc')->first();

            return view('admin.settings')->with('settings', $settings);
        } else {
            return redirect('/admin/login');
        }
    }

    public function saveSettings(Request $request)
    {
        if (Auth::check()) {
            /*$site_on_off = $request->site_on_off;
            $def_pagination_limit = $request->def_pagination_limit;
            $def_expiry_after = $request->def_expiry_after;*/
            $acadamic_year = $request->acadamic_year;
            $display_academic_year = $request->display_academic_year;
            $academic_start_date = $request->academic_start_date;
            $academic_end_date = $request->academic_end_date;
            $helpcontact = $request->helpcontact;
            $admin_email = $request->admin_email;
            $contact_address = $request->contact_address;
            $facebook_link = $request->facebook_link;
            $twitter_link = $request->twitter_link;
            $instagram_link = $request->instagram_link;
            $skype_link = $request->skype_link;
            $youtube_link = $request->youtube_link;

            $update_holidays = $request->get('update_holidays', 0);
            if($update_holidays == 1) {} else { $update_holidays = 0; }

            if(strtotime($academic_start_date) > 0) {
                $academic_start_date = date('Y-m-d', strtotime($academic_start_date));
            }

            if(strtotime($academic_end_date) > 0) {
                $academic_end_date = date('Y-m-d', strtotime($academic_end_date));
            }

            if(strtotime($academic_start_date) > strtotime($academic_end_date)) {
                return response()->json([

                    'status' => "FAILED",
                    'message' => "Academic Start date must be lesser than Academic End date",
                ]);
            }

            $ex = DB::table('admin_settings')->where('school_id', Auth::User()->id)->first();
            if(empty($ex)) {
                 DB::table('admin_settings')->insert([
                    /*'site_on_off' => $site_on_off,
                    'def_pagination_limit' => $def_pagination_limit,
                    'def_expiry_after' => $def_expiry_after,*/
                    'school_id' => Auth::User()->id,
                    'helpcontact' => $helpcontact,
                    'admin_email' => $admin_email,
                    'contact_address' => $contact_address,
                    'facebook_link' => $facebook_link,
                    'twitter_link' => $twitter_link,
                    'instagram_link' => $instagram_link,
                    'skype_link' => $skype_link,
                    'youtube_link' => $youtube_link,
                    'acadamic_year' => $acadamic_year,
                    'display_academic_year' => $display_academic_year,
                    'academic_start_date' => $academic_start_date,
                    'academic_end_date' => $academic_end_date,
                ]);
            }   else {
                DB::table('admin_settings')->where('school_id', Auth::User()->id)->update([
                    /*'site_on_off' => $site_on_off,
                    'def_pagination_limit' => $def_pagination_limit,
                    'def_expiry_after' => $def_expiry_after,*/
                    'school_id' => Auth::User()->id,
                    'helpcontact' => $helpcontact,
                    'admin_email' => $admin_email,
                    'contact_address' => $contact_address,
                    'facebook_link' => $facebook_link,
                    'twitter_link' => $twitter_link,
                    'instagram_link' => $instagram_link,
                    'skype_link' => $skype_link,
                    'youtube_link' => $youtube_link,
                    'acadamic_year' => $acadamic_year,
                    'display_academic_year' => $display_academic_year,
                    'academic_start_date' => $academic_start_date,
                    'academic_end_date' => $academic_end_date,
                ]);
            } 
            

            $year_start_end = DB::table('year_start_end')->where('school_id', Auth::User()->id)
                ->where('academic_year', $acadamic_year)->get();
            if($year_start_end->isNotEmpty()) {
                DB::table('year_start_end')->where('academic_year', $acadamic_year)
                    ->update(['school_id' => Auth::User()->id,
                              'academic_start_date'=>$academic_start_date, 
                              'academic_end_date'=>$academic_end_date,
                              'created_at'=>date('Y-m-d H:i:s'),
                            ]);
            }   else {
                DB::table('year_start_end')
                    ->insert(['school_id' => Auth::User()->id,
                              'academic_year'=>$acadamic_year,
                              'academic_start_date'=>$academic_start_date, 
                              'academic_end_date'=>$academic_end_date,
                              'created_at'=>date('Y-m-d H:i:s'),
                            ]);
            }

            if($update_holidays == 1 && strtotime($academic_start_date) > 0 && strtotime($academic_end_date) > 0) {
                $academic_start_date = date('Y-m-d', strtotime($academic_start_date)); 
                $academic_end_date = date('Y-m-d', strtotime($academic_end_date));

                $sundays = $saturdays = [];
                $months = CommonController::getMonthsInRange($academic_start_date, $academic_end_date);
                if(!empty($months)) {
                    foreach($months as $mn) {
                        $y = date('Y', strtotime($mn));
                        $m = date('m', strtotime($mn));
                        $sundays[] = CommonController::getSundays($y,$m);
                        $saturdays[] = CommonController::getSaturdays($y,$m);
                    }

                    if(count($sundays) > 0) {
                        foreach($sundays as $suns) {
                            if(is_array($suns)) {
                                foreach($suns as $sun) { 
                                    if(strtotime($sun) > 0 && (strtotime($sun) >= strtotime($academic_start_date)) && (strtotime($sun) <= strtotime($academic_end_date))) {
                                        $ex = DB::table('holidays')->where('school_college_id', Auth::User()->id)
                                        ->where('holiday_date', $sun)->first();
                                        if(empty($ex)) {
                                            DB::table('holidays')->insert(['school_college_id'=> Auth::User()->id,'holiday_date'=>$sun, 'holiday_description'=>'Sunday', 'created_at' => date('Y-m-d H:i:s')]);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if(count($saturdays) > 0) {
                        foreach($saturdays as $suns) {
                            if(is_array($suns)) {
                                foreach($suns as $sun) { 
                                    if(strtotime($sun) > 0 && (strtotime($sun) >= strtotime($academic_start_date)) && (strtotime($sun) <= strtotime($academic_end_date))) {
                                        $ex = DB::table('holidays')
                                            ->where('school_college_id', Auth::User()->id)
                                            ->where('holiday_date', $sun)->first();
                                        if(empty($ex)) {
                                            DB::table('holidays')->insert(['school_college_id'=> Auth::User()->id,'holiday_date'=>$sun, 'holiday_description'=>'Saturday', 'created_at' => date('Y-m-d H:i:s')]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                 
                }
            }

            return response()->json([

                'status' => "SUCCESS",
                'message' => "Saved Successfully",
            ]);
        } else {
            return response()->json([

                'status' => "FAILED",
                'message' => "Session Logged Out. Please Login Again",
            ]);
        }
    }

    // About Setclasses
    public function about()
    {
        if (Auth::check()) {
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->orderby('id', 'asc')->first();
            $about = $settings->about;
            return view('admin.about')->with('about', $about);
        } else {
            return redirect('/admin/login');
        }
    }

    public function saveAbout(Request $request)
    {
        if (Auth::check()) {
            $about = $request->about;

            $ex = DB::table('admin_settings')->where('school_id', Auth::User()->id)->first();
            if(!empty($ex)) {
                DB::table('admin_settings')->where('school_id', Auth::User()->id)->update([
                    'about' => $about,
                ]);
            }   else {
                DB::table('admin_settings')->insert([
                    'about' => $about, 'school_id' => Auth::User()->id,
                ]);
            }
            

            return response()->json([

                'status' => "SUCCESS",
                'message' => "About Info Saved Successfully",
            ]);
        } else {
            return response()->json([

                'status' => "FAILED",
                'message' => "Session Logged Out. Please Login Again",
            ]);
        }
    }

    // Privacy Policy Setclasses
    public function privacypolicy()
    {
        if (Auth::check()) {
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->orderby('id', 'asc')->first();
            $privacy_policy = $settings->privacy_policy;
            return view('admin.privacypolicy')->with('privacy_policy', $privacy_policy);
        } else {
            return redirect('/admin/login');
        }
    }

    public function savePrivacypolicy(Request $request)
    {
        if (Auth::check()) {
            $privacy_policy = $request->privacy_policy;
            $ex = DB::table('admin_settings')->where('school_id', Auth::User()->id)->first();
            if(!empty($ex)) {
                DB::table('admin_settings')->where('school_id', Auth::User()->id)->update([
                    'privacy_policy' => $privacy_policy,
                ]);
            }   else {
                DB::table('admin_settings')->insert([
                    'privacy_policy' => $privacy_policy, 'school_id' => Auth::User()->id,
                ]);
            } 

            return response()->json([

                'status' => "SUCCESS",
                'message' => "Policy Info Saved Successfully",
            ]);
        } else {
            return response()->json([

                'status' => "FAILED",
                'message' => "Session Logged Out. Please Login Again",
            ]);
        }
    }

    // Terms and Conditions
    public function termscond()
    {
        if (Auth::check()) {
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->orderby('id', 'asc')->first();
            $terms = '';
            if (!empty($settings)) {
                $terms = $settings->terms_conditions;
            }
            return view('admin.terms')->with('terms', $terms);
        } else {
            return redirect('/admin/login');
        }
    }

    public function saveTermsCond(Request $request)
    {
        if (Auth::check()) {
            $terms = $request->terms;
            $ex = DB::table('admin_settings')->where('school_id', Auth::User()->id)->first();
            if(!empty($ex)) {
                DB::table('admin_settings')->where('school_id', Auth::User()->id)->update([
                    'terms_conditions' => $terms,
                ]);
            }   else {
                DB::table('admin_settings')->insert([
                    'terms_conditions' => $terms, 'school_id' => Auth::User()->id,
                ]);
            }  

            return response()->json([

                'status' => "SUCCESS",
                'message' => "Terms and Conditions Saved Successfully",
            ]);
        } else {
            return response()->json([

                'status' => "FAILED",
                'message' => "Session Logged Out. Please Login Again",
            ]);
        }
    }

    // FAQs
    /*
     * Function: viewFAQ
     */
    public function viewFAQ()
    {
        if (Auth::check()) {
            return view('admin.faq');
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getFAQ
     * Datatable Load
     */
    public function getFAQ(Request $request)
    {
        if (Auth::check()) {
            $status = $request->get('status','');
            $faq_qry = Faq::where('id','>','0')->where('school_id', Auth::User()->id);
            if(!empty($status)){
                $faq_qry->where('status',$status);
            }
            $faq = $faq_qry->get();
            return Datatables::of($faq)->make(true);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: postFAQ
     * Save into hby_faq table
     */
    public function postFAQ(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $faq_type = $request->faq_type;
            $question = $request->question;
            $answer = $request->answer;
            $position = $request->position;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'faq_type' => 'required',
                'question' => 'required',
                'answer' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs",
                ]);
            }

            if ($id > 0) {
                $faq = Faq::find($id);
                $faq->updated_at = date('Y-m-d H:i:s');
            } else {
                $faq = new Faq();
                $faq->created_at = date('Y-m-d H:i:s');
            }
            $faq->school_id = Auth::User()->id;
            $faq->faq_type = $faq_type;
            $faq->question = $question;
            $faq->answer = $answer;
            $faq->position = $position;
            $faq->status = $status;

            $faq->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Faq Saved Successfully',
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editFAQ(Request $request)
    {
        if (Auth::check()) {
            $faq = Faq::where('id', $request->code)->get();
            if ($faq->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $faq[0],
                    'message' => 'Faq Detail',
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Faq Detail',
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Countries
    /* Function: viewCountries
     */
    public function viewCountries()
    {   
        if (Auth::check()) {
            return view('admin.countries');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getCountries
    Datatable Load
     */
    public function getCountries(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');

            $countriesqry = Countries::where('id', '>', 0);
            $filteredqry = Countries::where('id', '>', 0);

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $countriesqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $countriesqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(!empty($status)){
                $countriesqry->where('status',$status);
                $filteredqry->where('status',$status);
            }
            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }




            $countries = $countriesqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Countries::orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($countries)) {
                foreach ($countries as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postCountries
    Save into em_countries table
     */
    public function postCountries(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $code = $request->code;
            $phonecode = $request->phonecode;
            $name = $request->name;
            //$alias_name = $request->alias_name;
            $currency_symbol = $request->currency_symbol;
            $currency = $request->currency;
            //$alias_currency = $request->alias_currency;
            //$is_register = $request->is_register;
            $position = $request->position;
            $status = $request->status;
            $image = $request->file('country_flag');

            $validator = Validator::make($request->all(), [
                'code' => 'required',
                'code' => 'unique:countries,code,' . $id,
                'phonecode' => 'required',
                'phonecode' => 'unique:countries,phonecode,' . $id,
                'name' => 'required',
                'name' => 'unique:countries,name,' . $id,
                //'alias_name' => 'required',
                'currency_symbol' => 'required',
                'currency' => 'required',
                //'alias_currency' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('countries')->where('name', $name)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('countries')->where('name', $name)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Country Already Exists'], 201);
            }

            if ($id > 0) {
                $country = Countries::find($id);
            } else {
                $country = new Countries;
            }

            if (!empty($image)) {

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/image/countries');

                $image->move($destinationPath, $countryimg);

                $country->country_flag = $countryimg;

            }

            $country->code = $code;
            $country->phonecode = $phonecode;
            $country->name = $name;
            //$country->alias_name = $alias_name;
            $country->currency_symbol = $currency_symbol;
            $country->currency = $currency;
            //$country->is_register = $is_register;
            $country->position = $position;
            $country->status = $status;

            $country->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Country Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editCountries(Request $request)
    {
        if (Auth::check()) {
            $country = Countries::where('id', $request->code)->get();
            if ($country->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $country[0], 'message' => 'Country Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Country Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    // States
    /*
     * Function: viewStates
     */
    public function viewStates()
    {
        if (Auth::check()) {
            $countries = Countries::all()->where('status','=','ACTIVE');
            return view('admin.states')->with('countries', $countries);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getStates
     * Datatable Load
     */
    public function getStates(Request $request)
    {
        if (Auth::check()) {
            /*$states = States::leftjoin('countries', 'countries.id', 'states.country_id')
            ->select('states.*', 'countries.name as country_name')->get();*/
            //return Datatables::of($states)->make(true);
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('statestatus', '');

            $users_qry = States::leftjoin('countries', 'countries.id', 'states.country_id');
            $filtered_qry = States::leftjoin('countries', 'countries.id', 'states.country_id');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'states.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($status)) {
                $users_qry->where('states.status', $status);
                $filtered_qry->where('states.status', $status);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'states.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }


            $totalDataqry = States::orderby('states.id', 'asc');
            $totalData = $totalDataqry->select('states.id')->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->select('states.id')->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $users = $users_qry->select('states.*', 'countries.name as country_name')
                ->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: postStates
     * Save into dx_states table
     */
    public function postStates(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $state_name = $request->state_name;
            $status = $request->status;
            $country_id = $request->country_id;

            $validator = Validator::make($request->all(), [
                'country_id' => 'required',
                'state_name' => 'required',
                'state_name' => 'unique:states,state_name,' . $id,
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $state = States::find($id);
            } else {
                $state = new States();
            }
            $state->country_id = $country_id;
            $state->state_name = $state_name;
            $state->status = $status;
            $state->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'State Saved Successfully',
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editStates(Request $request)
    {
        if (Auth::check()) {
            $state = States::where('id', $request->code)->get();
            if ($state->isNotEmpty()) {

                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $state[0],
                    'message' => 'State Detail',
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No State Detail',
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    // Districts
    /*
     * Function: viewDistricts
     */
    public function viewDistricts()
    {
        if (Auth::check()) {
            $states = States::where('status', 'ACTIVE')->get();
            return view('admin.districts')->with('states', $states);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getDistricts
     * Datatable Load
     */
    public function getDistricts(Request $request)
    {
        if (Auth::check()) {
            /*$districts = Districts::all();
            return Datatables::of($districts)->make(true);*/
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('status', '');

            $users_qry = Districts::leftjoin('states', 'states.id', 'districts.state_id');
            $filtered_qry = Districts::leftjoin('states', 'states.id', 'districts.state_id');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'districts.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($status)) {
                $users_qry->where('districts.status', $status);
                $filtered_qry->where('districts.status', $status);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'districts.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $totalDataqry = Districts::orderby('districts.id', 'asc');
            $totalData = $totalDataqry->select('districts.id')->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->select('districts.id')->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $users = $users_qry->select('districts.*', 'states.state_name as state_name')
                ->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: postDistricts
     * Save into dx_districts table
     */
    public function postDistricts(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $state_id = $request->state_id;
            $district_name = $request->district_name;
            $status = $request->status;
            $validator = Validator::make($request->all(), [
                'state_id' => 'required',
                'district_name' => 'required',
                'district_name' => 'unique:districts,district_name,' . $id,
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $district = Districts::find($id);
            } else {
                $district = new Districts();
            }
            $district->country_id = DB::table('states')->where('id', $state_id)->value('country_id');
            $district->state_id = $state_id;
            $district->district_name = $district_name;
            $district->status = $status;

            $district->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'District Saved Successfully',
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editDistricts(Request $request)
    {
        if (Auth::check()) {
            $district = Districts::where('id', $request->code)->get();
            if ($district->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $district[0],
                    'message' => 'Districts Detail',
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Districts Detail',
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Grades
    /* Function: viewGrades
     */
    public function viewGrades()
    {
        if (Auth::check()) {
            return view('admin.grades');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getGrades
    Datatable Load
     */
    public function getGrades(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $gradesqry = Grades::where('id', '>', 0)->where('school_id', Auth::User()->id);
            $filteredqry = Grades::where('id', '>', 0)->where('school_id', Auth::User()->id);
            $status = $request->get('status','');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $classesqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $classesqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(!empty($status)){
                $gradesqry->where('status',$status);
                $filteredqry->where('status',$status);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $grades = $gradesqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Grades::orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($grades)) {
                foreach ($grades as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postGrades
    Save into em_countries table
     */
    public function postGrades(Request $request)
    {
        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $id = $request->id;
            $mark = $request->mark;
            $grade = $request->grade;
            $remark = $request->remark;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'mark' => 'required',
                'grade' => 'required',
                'remark' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('grades')->where('grade', $grade)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('grades')->where('grade', $grade)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 0, 'message' => 'Grade Already Exists'], 201);
            }

            if ($id > 0) {
                $grades = Grades::find($id);
            } else {
                $grades = new Grades();
            }
            $grades->school_id = $school_id;
            $grades->mark = $mark;
            $grades->grade = $grade;
            $grades->remark = $remark;
            $grades->status = $status;

            $grades->save();
            return response()->json(['status' => 1, 'message' => 'Grade Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editGrades(Request $request)
    {
        if (Auth::check()) {
            $grade = Grades::where('id', $request->code)->get();
            if ($grade->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $grade[0], 'message' => 'Grade Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Grade Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }


    //viewMasterClasses
    public function viewMasterClasses()
    {
        if (Auth::check()) {

            return view('admin.classmaster');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getMasterClasses(Request $request)
    {

        if (Auth::check()) {
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = ClassesMaster::where('status','=',$status)->get();
           }else{
            $mclass = ClassesMaster::all();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postMasterClasses(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_name = $request->class_name; 
            $position = $request->position;
            $status = $request->status; 

            $validator = Validator::make($request->all(), [
                'class_name' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($id > 0) {
                $exists = DB::table('class_master')->where('class_name', $class_name)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('class_master')->where('class_name', $class_name)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Class Name Already Exists.",
                ]);
            }


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $mclass = ClassesMaster::find($id);
                $mclass->updated_at = date('Y-m-d H:i:s');
            } else {
                $mclass = new ClassesMaster;
                $mclass->created_at = date('Y-m-d H:i:s');
            }

            $mclass->class_name = $class_name; 
            $mclass->position = $position;
            $mclass->status = $status; 

            $mclass->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Class Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editMasterClasses(Request $request)
    {

        if (Auth::check()) {
            $mclass = ClassesMaster::where('id', $request->code)->get();

            if ($mclass->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $mclass[0], 'message' => 'Classes Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Classes Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Classes
    /* Function: viewClasses
     */
    public function viewClasses()
    {
        if (Auth::check()) {
            return view('admin.schoolclasses');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getSchoolClasses
    Datatable Load
     */
    public function getSchoolClasses(Request $request) {
        if (Auth::check()) {
            $school_id = Auth::User()->id; 

            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $classesqry = Classes::leftjoin('class_master', 'class_master.id', 'classes.class_name')
                ->where('school_id', $school_id)->select('class_master.class_name')
                ->where('class_master.status', 'ACTIVE')
                ->where('classes.status', 'ACTIVE');
            $filteredqry = Classes::leftjoin('class_master', 'class_master.id', 'classes.class_name')
                ->where('school_id', $school_id)->select('class_master.class_name')
                ->where('class_master.status', 'ACTIVE')
                ->where('classes.status', 'ACTIVE');
            $status = $request->get('status','');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $classesqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $classesqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(!empty($status)){
                $classesqry->where('classes.status',$status);
                $filteredqry->where('classes.status',$status);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'class_master.position';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $classes = $classesqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Classes::where('school_id', $school_id)->orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($classes)) {
                foreach ($classes as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    public function updateClasses() {
        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $classes = ClassesMaster::where('status', 'ACTIVE')->orderby('position', 'asc')->get();
            $schoolclasses = Classes::where('status', 'ACTIVE')->where('school_id', $school_id)
                ->orderby('position', 'asc')->get();
            $sclassarr = [];
            if($schoolclasses->isNotEmpty()) {
                foreach($schoolclasses as $cla) {
                    $sclassarr[] = $cla->class_name;
                }

            }
            return view('admin.classesadd')->with('classes', $classes)->with('sclassarr', $sclassarr);
        } else {
            return redirect('/admin/login');
        }
    } 
    
    /* Function: postUpdateClasses
    Save into em_countries table
     */
    public function postUpdateClasses(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();  
            $class = $request->class; 
            $position = $request->position; 
            $school_id = Auth::User()->id;

            $validator = Validator::make($request->all(), [
                'class' => 'required'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([ 
                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if(!empty($class) && is_array($class)) {
                DB::table('classes')->where('school_id', $school_id)->update(['status'=>'INACTIVE']);
                foreach($class as $k=>$v) {
                    $exists = DB::table('classes')->where('school_id', $school_id)->where('class_name', $k)->first();
                    if(!empty($exists)) {
                        DB::table('classes')->where('school_id', $school_id)->where('class_name', $k)
                        ->update(['status'=>'ACTIVE', 'position'=>$position[$k], 'updated_at'=>date('Y-m-d H:i:s')]);
                    }   else {
                        DB::table('classes')->insert(['school_id'=>$school_id, 'class_name'=>$k, 'status'=>'ACTIVE', 'position'=>$position[$k], 'created_at'=>date('Y-m-d H:i:s')]);
                    } 
                }
            }   else {
                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check the classes",
                ]);
            } 
 
            return response()->json(['status' => 'SUCCESS', 'message' => 'Class details updated Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getClasses
    Datatable Load
     */
    public function getClasses(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $classesqry = Classes::where('id', '>', 0)->where('school_id', Auth::User()->id);
            $filteredqry = Classes::where('id', '>', 0)->where('school_id', Auth::User()->id);
            $status = $request->get('status','');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $classesqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $classesqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(!empty($status)){
                $classesqry->where('status',$status);
                $filteredqry->where('status',$status);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $classes = $classesqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Classes::orderby('id', 'asc')->where('school_id', Auth::User()->id);
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($classes)) {
                foreach ($classes as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postCountries
    Save into em_countries table
     */
    public function postClasses(Request $request)
    {
        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $id = $request->id;
            $class_name = $request->class_name;
            $position = $request->position;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'class_name' => 'required',
                //'class_name' => 'unique:classes,class_name,' . $id,
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('classes')->where('class_name', $class_name)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('classes')->where('class_name', $class_name)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 0, 'message' => 'Class Name Already Exists'], 201);
            }

            if ($id > 0) {
                $class = Classes::find($id);
            } else {
                $class = new Classes();
            }
            $class->school_id = $school_id;
            $class->class_name = $class_name;
            $class->position = $position;
            $class->status = $status;

            $class->save();
            return response()->json(['status' => 1, 'message' => 'Class Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editClasses(Request $request)
    {
        if (Auth::check()) {
            $class = Classes::where('id', $request->code)->get();
            if ($class->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $class[0], 'message' => 'Class Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Class Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Sections
    /* Function: viewSections
     */
    public function viewSections()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get(); 
            return view('admin.sections')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getSections
    Datatable Load
     */
    public function getSections(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $subject = $request->get('subject', '0');
            $status = $request->get('status','0');
            $sectionsqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->select('sections.*', 'classes.class_name');
                if($subject != 0) {
                    $sectionsqry->where('mapped_subjects', 'like', '%'.$subject.'%');
                }
                if($status != 0 || $status != '') {
                    $sectionsqry->where('sections.status', $status);
                }

            $filteredqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->select('sections.*', 'classes.class_name');
                if($subject != 0) {
                    $filteredqry->where('mapped_subjects', 'like', '%'.$subject.'%');
                }
                if($status != 0 || $status != '') {
                    $filteredqry->where('sections.status', $status);
                }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'sections.status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('sections.school_id', Auth::User()->id);
                $filteredqry->where('sections.school_id', Auth::User()->id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'sections.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $sections = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('sections.id')->count();

            $totalDataqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')->orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('sections.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postSections
    Save into em_countries table
     */
    public function postSections(Request $request)
    {
        // return $request;
        if (Auth::check()) {
            $id = $request->id;
            $class_id = $request->class_id;
            //$mapped_subjects = implode(',', $request->mapped_subjects);
            $section_name = $request->section_name;
            $position = $request->position;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                //'mapped_subjects' => 'required',
                'section_name' => 'required',
                //'section_name' => 'unique:school_class_Sections,section_name,'.$id,
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('sections')->where('section_name', $section_name)->where('class_id', $class_id)
                    ->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('sections')->where('section_name', $section_name)->where('class_id', $class_id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 0, 'message' => 'Section Name Already Exists'], 201);
            }

            if ($id > 0) {
                $class = Sections::find($id);
            } else {
                $class = new Sections();
            }
            $class->school_id = Auth::User()->id;
            $class->class_id = $class_id;
            //$class->mapped_subjects = $mapped_subjects;
            $class->section_name = $section_name;
            $class->position = $position;
            $class->status = $status;

            $class->save();
            return response()->json(['status' => 1, 'message' => 'Section Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editSections(Request $request)
    {
        if (Auth::check()) {
            $class = Sections::where('id', $request->code)->get();
            if ($class->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $class[0], 'message' => 'Section Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Section Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Section Subject Mappings
    /* Function: viewSectionSubjects
     */
    public function viewSectionSubjects()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $subject = Subjects::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $subject->where('school_id', Auth::User()->id); 
            }
            $subject = $subject->orderby('position','asc')->get();
            return view('admin.section_subjects')->with('classes', $classes)->with('subject', $subject);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getSectionSubjects
    Datatable Load
     */
    public function getSectionSubjects(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $subject = $request->get('subject', '0');
            $status = $request->get('status','0');
            $sectionsqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->select('sections.*', 'classes.class_name');
                if($subject != 0) {
                    $sectionsqry->where('mapped_subjects', 'like', '%'.$subject.'%');
                }
                if($status != 0 || $status != '') {
                    $sectionsqry->where('sections.status', $status);
                }

            $filteredqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->select('sections.*', 'classes.class_name');
                if($subject != 0) {
                    $filteredqry->where('mapped_subjects', 'like', '%'.$subject.'%');
                }
                if($status != 0 || $status != '') {
                    $filteredqry->where('sections.status', $status);
                }


            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('sections.school_id', Auth::User()->id);
                $filteredqry->where('sections.school_id', Auth::User()->id); 
            } 

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'sections.status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'sections.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $sections = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('sections.id')->count();

            $totalDataqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('sections.school_id', Auth::User()->id); 
            } 
            $totalDataqry = $totalDataqry->orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postSectionSubjects
    Save into em_countries table
     */
    public function postSectionSubjects(Request $request)
    {
        // return $request;
        if (Auth::check()) {
            $id = $request->id;
            $mapped_subjects = implode(',', $request->mapped_subjects);
            /*$class_id = $request->class_id;
            $section_name = $request->section_name;
            $position = $request->position;
            $status = $request->status;*/

            $validator = Validator::make($request->all(), [
                'mapped_subjects' => 'required',
                /*'class_id' => 'required',
                'section_name' => 'required',
                //'section_name' => 'unique:school_class_Sections,section_name,'.$id,
                'position' => 'required',
                'status' => 'required',*/
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            /*if ($id > 0) {
                $exists = DB::table('sections')->where('section_name', $section_name)->where('class_id', $class_id)
                    ->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('sections')->where('section_name', $section_name)->where('class_id', $class_id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 0, 'message' => 'Section Name Already Exists'], 201);
            }*/

            if ($id > 0) {
                $class = Sections::find($id);
            } else {
                $class = new Sections();
            }
            $class->mapped_subjects = $mapped_subjects;
            /*$class->class_id = $class_id;
            $class->section_name = $section_name;
            $class->position = $position;
            $class->status = $status;*/

            $class->save();
            return response()->json(['status' => 1, 'message' => 'Section Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editSectionSubjects(Request $request)
    {
        if (Auth::check()) {
            $class = Sections::where('id', $request->code)->get();
            if ($class->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $class[0], 'message' => 'Section Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Section Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Circulars
    /* Function: viewCirculars
     */
    public function viewCirculars()
    {
        if (Auth::check()) {
            $classes = Classes::where('status','ACTIVE')->orderby('position','asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $teacher = User::leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('users.user_type', 'TEACHER')
                ->where('users.status', 'ACTIVE')
                ->select('users.id', 'users.name', 'users.mobile');
            if(Auth::User()->user_type == 'SCHOOL') {
                $teacher->where('school_college_id', Auth::User()->id); 
            }
            $teacher = $teacher->orderby('name', 'asc')->get();
            return view('admin.circulars')->with('classes', $classes)->with('teacher', $teacher);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getCirculars
    Datatable Load
     */
    public function getCirculars(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status',0);
            $cls_id = $request->get('cls_id',0);
            $approval_status_id = $request->get('approval_status_id','0');
            $teacher_id = $request->get('teacher_id',0);
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';


            $sectionsqry = Circulars::where('circular.id', '>', 0) 
                ->select('circular.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day')); 

            $filteredqry = Circulars::where('circular.id', '>', 0)
                ->select('circular.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day'));

            if($status != ''){
                $sectionsqry->where('status','=',$status);
                $filteredqry->where('status','=',$status);

            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('school_id', Auth::User()->id); 
                $filteredqry->where('school_id', Auth::User()->id); 
            }

            if($approval_status_id != ''){
                $sectionsqry->where('approve_status','=',$approval_status_id);
                $filteredqry->where('approve_status','=',$approval_status_id);
            }

            if($cls_id != ''){
                $sectionsqry->whereRAW(' FIND_IN_SET('.$request->cls_id.', class_ids) ');
                $filteredqry->whereRAW(' FIND_IN_SET('.$request->cls_id.', class_ids) ');
            }

            if($teacher_id != ''){
                $sectionsqry->where('created_by','=',$teacher_id);
                $filteredqry->where('created_by','=',$teacher_id);
            }

            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $sectionsqry->where('circular.circular_date', '>=', $mindate);
                $filteredqry->where('circular.circular_date', '>=', $mindate);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                $sectionsqry->where('circular.circular_date', '<=', $maxdate);
                $filteredqry->where('circular.circular_date', '<=', $maxdate);
            }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'circular.status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'circular.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $circulars = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('circular.id')->count();

            $totalDataqry = Circulars::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($circulars)) {
                foreach ($circulars as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postCirculars
    Save into em_countries table
     */
    public function postCirculars(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_ids = $request->class_ids;
            $circular_title = $request->circular_title;
            $circular_message = $request->circular_message;
            $circular_date = $request->circular_date;
            $circular_date = date('Y-m-d', strtotime($circular_date));
            $status = $request->status;
            $approve_status = $request->approve_status;

            $validator = Validator::make($request->all(), [
                'class_ids' => 'required',
                'circular_title' => 'required',
                'circular_message' => 'required',
                'circular_date' => 'required',
                'status' => 'required',
                'approve_status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }
            if ($id > 0) {
                $circular = Circulars::find($id);
                $circular->updated_at = date('Y-m-d H:i:s');
                $circular->updated_by = Auth::User()->id;
            } else {
                $circular = new Circulars();
                $circular->created_by = Auth::User()->id;
                $circular->created_at = date('Y-m-d H:i:s');
            }

            if (is_array($class_ids) && count($class_ids) > 0) {
                $class_ids = implode(',', $class_ids);
            } else {
                return response()->json([

                    'status' => 0,
                    'message' => "Please select the classes ",
                ]);
            }

            $circular->school_id = Auth::User()->id;

            $circular->class_ids = $class_ids;
            $circular->circular_title = $circular_title;
            $circular->circular_message = $circular_message;
            $circular->circular_date = $circular_date;

            $image = $request->file('circular_image');
            if (!empty($image)) {
                $ext = $image->getClientOriginalExtension();
                if (!in_array($ext, $this->accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
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
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload mp3,mp4']);
                }

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/circulars');

                $image->move($destinationPath, $countryimg);

                $circular->circular_attachments = $countryimg;

            }
            /*$images = $request->file('circular_attachments');
            if (!empty($images)) {
                $arr = []; $str =  '';
                if(!empty($circular->circular_attachments)) {
                    $sarr = explode(';', $circular->circular_attachments);
                }
                foreach($images as $image) {
                    $ext = $image->getClientOriginalExtension();
                    if (!in_array($ext, $this->accepted_formats_audio)) {
                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload mp3,mp4']);
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
            }*/
            $circular->status = $status;
            $circular->approve_status = $approve_status;

            $circular->save();
            return response()->json(['status' => "SUCCESS", 'message' => 'Circular Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editCirculars(Request $request)
    {
        if (Auth::check()) {
            $circular = Circulars::where('id', $request->code)->get();
            if ($circular->isNotEmpty()) {
                return response()->json(['status' => "SUCCESS", 'data' => $circular[0], 'message' => 'Circular Detail']);
            } else {
                return response()->json(['status' => "FAILED", 'data' => [], 'message' => 'No Circular Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Events
    /* Function: viewEvents
     */
    public function viewEvents()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','ACTIVE')->orderby('position','asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $teacher = User::leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('users.user_type', 'TEACHER')
                ->where('users.status', 'ACTIVE')
                ->select('users.id', 'users.name', 'users.mobile')->orderby('name', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $teacher->where('school_college_id', Auth::User()->id); 
            }
            $teacher = $teacher->get();
            return view('admin.events')->with('classes', $classes)->with('teacher', $teacher);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getEvents
    Datatable Load
     */
    public function getEvents(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','0');
            $cls_id = $request->get('cls_id','');
            $approval_status_id = $request->get('approval_status_id','0');
            $teacher_id = $request->get('teacher_id',0);
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $sectionsqry = Events::where('events.id', '>', 0)
                ->select('events.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day')); 

            $filteredqry = Events::where('events.id', '>', 0)
                ->select('events.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day')); 

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'events.status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('events.school_id', Auth::User()->id);
                $filteredqry->where('events.school_id', Auth::User()->id);
            }
            
            if($status != '' || $status != 0){
                $sectionsqry->where('status',$status);
                $filteredqry->where('status',$status);
            }

            if($cls_id != ''){
                $sectionsqry->whereRAW(' FIND_IN_SET('.$request->cls_id.', class_ids) ');
                $filteredqry->whereRAW(' FIND_IN_SET('.$request->cls_id.', class_ids) ');
            }

             if($approval_status_id != ''){
                $sectionsqry->where('approve_status','=',$approval_status_id);
                $filteredqry->where('approve_status','=',$approval_status_id);
            } 

            if($teacher_id != ''){
                $sectionsqry->where('created_by','=',$teacher_id);
                $filteredqry->where('created_by','=',$teacher_id);
            }

            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $sectionsqry->where('events.circular_date', '>=', $mindate);
                $filteredqry->where('events.circular_date', '>=', $mindate);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                $sectionsqry->where('events.circular_date', '<=', $maxdate);
                $filteredqry->where('events.circular_date', '<=', $maxdate);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'events.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $circulars = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('events.id')->count();

            $totalDataqry = Events::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('events.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($circulars)) {
                foreach ($circulars as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postEvents
    Save into em_countries table
     */
    public function postEvents(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_ids = $request->class_ids;
            $circular_title = $request->circular_title;
            $circular_message = $request->circular_message;
            $circular_date = $request->circular_date;
            $circular_date = date('Y-m-d', strtotime($circular_date));
            $status = $request->status;
            $approve_status = $request->approve_status;
            $youtube_link  = $request->youtube_link;

            $validator = Validator::make($request->all(), [
                'class_ids' => 'required',
                'circular_title' => 'required',
                'circular_message' => 'required',
                'circular_date' => 'required',
                'status' => 'required',
                'approve_status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }
            if ($id > 0) {
                $circular = Events::find($id);
                $circular->updated_at = date('Y-m-d H:i:s');
                $circular->updated_by = Auth::User()->id;
            } else {
                $circular = new Events();
                $circular->created_at = date('Y-m-d H:i:s');
                $circular->created_by = Auth::User()->id;
            }

            if (is_array($class_ids) && count($class_ids) > 0) {
                $class_ids = implode(',', $class_ids);
            } else {
                return response()->json([

                    'status' => 0,
                    'message' => "Please select the classes ",
                ]);
            }
            $circular->school_id = Auth::User()->id;
            $circular->class_ids = $class_ids;
            $circular->circular_title = $circular_title;
            $circular->circular_message = $circular_message;
            $circular->circular_date = $circular_date;   
            $circular->youtube_link = $youtube_link;
            
            $images = $request->file('circular_image',[]);
          
        //    echo "size". $size = $request->file('circular_image')->getSize();
        //    exit;
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
            return response()->json(['status' => 1, 'message' => 'Event Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function deleteEventAttachment(Request $request)   {
        if (Auth::check()) {
            $input = $request->all();
            $eventid = (isset($input['eventid'])) ? $input['eventid'] : 0;
            $imgid = (isset($input['imgid'])) ? $input['imgid'] : '';
            if($eventid > 0 && !empty($imgid)) {
                $event = Events::find($eventid);
                $arr = explode('/', $imgid);
                $imgname = array_pop($arr);

                if(!empty($event->circular_attachments)) {
                    $imgs = explode(';',$event->circular_attachments);
                    if(count($imgs)>0) {
                        $pos = array_search($imgname, $imgs);
                        if ($pos !== false) {
                            unset($imgs[$pos]);
                            if(count($imgs)>0) {
                                $imgs = implode(';', $imgs);
                                $event->circular_attachments = $imgs;
                            } else {
                                $event->circular_attachments = '';
                            }
                            $event->save();
                            return response()->json(['status' => 1, 'data' => [], 'message' => 'Deleted Successfully']);
                        }
                    }
                }
            }
            return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select the valid image to delete']);
        } else {
            return response()->json(['status' => 0, 'data' => [], 'message' => 'Session Logged Out']);
        }
    }


    public function deleteEventCircular(Request $request)   {
        if (Auth::check()) {
            $input = $request->all();
            $eventid = (isset($input['eventid'])) ? $input['eventid'] : 0;
            $imgid = (isset($input['imgid'])) ? $input['imgid'] : '';
          
            if($eventid > 0 && !empty($imgid)) {
                $event = Events::find($eventid);
                $arr = explode('/', $imgid);
                $imgname = array_pop($arr);

                if(!empty($event->circular_image)) {
                    $imgs = explode(';',$event->circular_image);
                    if(count($imgs)>0) {
                        $pos = array_search($imgname, $imgs);
                        if ($pos !== false) {
                            unset($imgs[$pos]);
                            if(count($imgs)>0) {
                                $imgs = implode(';', $imgs);
                                $event->circular_image = $imgs;
                            } else {
                                $event->circular_image = '';
                            }
                            $event->save();
                            return response()->json(['status' => 1, 'data' => [], 'message' => 'Deleted Successfully']);
                        }
                    }
                }
                return response()->json(['status' => 1, 'data' => [], 'message' => 'Image Deleted Successfully']);
            }
            else{
                return response()->json(['status' => 0, 'data' => [], 'message' => 'Please select the valid image to delete']);
            }
           
        } else {
            return response()->json(['status' => 0, 'data' => [], 'message' => 'Session Logged Out']);
        }
    }


    public function loadGallery(Request $request)   {
        // if (Auth::check()) {
       

        if (Auth::check()) {
            $circular = Events::where('id', $request->id)->first();
            if (!empty($circular)) {
         
               return response()->json(['status' => 1, 'data' => $circular, 'message' => 'Event Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Event Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function editEvents(Request $request)
    {
        if (Auth::check()) {
            $circular = Events::where('id', $request->code)->get();
            if ($circular->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $circular[0], 'message' => 'Event Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Event Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Holidays
    /* Function: viewHolidays
     */
    public function viewHolidays()
    {
        if (Auth::check()) {
            return view('admin.holidays');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getHolidays
    Datatable Load
     */
    public function getHolidays(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $sectionsqry = Holidays::where('holidays.id', '>', 0)
                ->select('holidays.*');
            $filteredqry = Holidays::where('holidays.id', '>', 0)
                ->select('holidays.*');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'holidays.status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'holidays.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('holidays.school_college_id', Auth::User()->id);
                $filteredqry->where('holidays.school_college_id', Auth::User()->id);
            } 

            $circulars = $sectionsqry->skip($start)->take($length)->orderby($orderby, 'desc')->get();
            $filters = $filteredqry->select('holidays.id')->count();

            $totalDataqry = Holidays::orderby('id', 'desc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('holidays.school_college_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($circulars)) {
                foreach ($circulars as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postHolidays
    Save into em_countries table
     */
    public function postHolidays(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $holiday_date = $request->holiday_date;
            $holiday_description = $request->holiday_description;
            $holiday_date = date('Y-m-d', strtotime($holiday_date));

            $validator = Validator::make($request->all(), [
                'holiday_date' => 'required',
                'holiday_description' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }
            if ($id > 0) {
                $holiday = Holidays::find($id);
            } else {
                $holiday = new Holidays();
            }

            $holiday->school_college_id = Auth::User()->id;
            $holiday->holiday_date = $holiday_date;
            $holiday->holiday_description = $holiday_description;

            $holiday->save();
            return response()->json(['status' => 1, 'message' => 'Holiday Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editHolidays(Request $request)
    {
        if (Auth::check()) {
            $holiday = Holidays::where('id', $request->code)->get();
            if ($holiday->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $holiday[0], 'message' => 'Holiday Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Holiday Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function deleteHolidays(Request $request)
    {
        if (Auth::check()) {
            $holiday = Holidays::where('id', $request->code)->get();
            if ($holiday->isNotEmpty()) {
                Holidays::where('id', $request->code)->delete();
                return response()->json(['status' => 1, 'data' => null, 'message' => 'Holiday Deleted Successfully']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Holiday Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Change Holidays
    /* Function: viewChangeHolidays
     */
    public function viewChangeHolidays()
    {
        if (Auth::check()) {
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->select('academic_start_date', 'academic_end_date')->first();
            $academic_start_date = $academic_end_date = ''; $months = [];
            if(!empty($settings)) { 
                $academic_start_date = $settings->academic_start_date;
                $academic_end_date = $settings->academic_end_date;
                $months = CommonController::getMonthsInRange($academic_start_date, $academic_end_date);
            } 
            
            return view('admin.changeholidays')->with([
                'academic_start_date' => $academic_start_date, 
                'academic_end_date' => $academic_end_date, 'months' => $months
            ]);
        } else {
            return redirect('/admin/login');
        }
    } 

    public function loadChangeHolidays(Request $request)
    {
        if (Auth::check()) {
            $yrmonth = $request->code;
            if(empty($yrmonth)) {
                return response()->json(['status' => 0, 'message' => 'Please select the Month']);
            } 
            $startdate = date('Y-m-d', strtotime('first day of '.$yrmonth));
            $enddate = date('Y-m-d', strtotime('last day of '.$yrmonth));
            $holiday = Holidays::where('holiday_date', '>=', $startdate)->where('holiday_date', '<=', $enddate)
                ->where('status', 1)->orderby('holiday_date', 'asc');
            $wday = Holidays::where('holiday_date', '>=', $startdate)->where('holiday_date', '<=', $enddate)
                ->where('status', 0)->orderby('holiday_date', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $holiday->whereIn('holidays.school_college_id', [Auth::User()->id]); 
                $wday->whereIn('holidays.school_college_id', [Auth::User()->id]); 
            }
            $holiday = $holiday->get();
            $wday = $wday->get();
            $working_days = CommonController::getDatesFromRange($startdate, $enddate);
            $hdays = []; $hdays1 = []; $wdays = []; $working_days1 = [];

            if ($wday->isNotEmpty()) {
                foreach($wday as $wd) {
                    $wdays[$wd->holiday_date] = $wd->holiday_description;
                } 
            } 

            if ($holiday->isNotEmpty()) {
                foreach($holiday as $hd) {
                    $hdays[] = $hd->holiday_date;
                    if(!empty($hd->holiday_description)) {
                        $hdays1[] = $hd->holiday_description;
                    }
                } // echo "<pre>"; print_r($wdays); print_r($hdays1); exit;
                foreach($working_days as $wk=>$wd) {

                    if(in_array($wd, $wdays)) {  
                        unset($hdays1[$wk]);
                    } 

                    if(in_array($wd, $hdays)) {  
                        unset($working_days[$wk]);
                    }

                    $working_days1[$wd]['holiday_date'] = $wd;
                    $working_days1[$wd]['holiday_description'] = '';
                    if(isset($wdays[$wd])) {
                        $working_days1[$wd]['holiday_description'] = $wdays[$wd];
                    } 
                }

                //echo "<pre>"; print_r($working_days1); exit;
                $content = view('admin.holidays_list')->with([ 'holiday' => $holiday, 'working_days' => $working_days, 'working_days1' => $working_days1 ])->render();

                return response()->json(['status' => 1, 'data' => $content, 'message' => 'Holiday Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Holiday Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }  

    public function saveChangeHolidays(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $holiday_type = $input['holiday_type'];
            $holiday_date = $input['holiday_date'];
            $holiday_description = $input['holiday_description'];
            //echo "<pre>"; print_r($holiday_type); print_r($holiday_description); exit;
            if(is_array($holiday_type)) {
                foreach($holiday_type as $hk => $hv) {
                    if($hv == 'h') {
                        $ex = DB::table('holidays')->where('holiday_date', $hk)
                            ->where('holidays.school_college_id', Auth::User()->id)->first();
                        if(empty($ex)) {
                            DB::table('holidays')->insert(['school_college_id' => Auth::User()->id, 'holiday_date'=>$hk, 'holiday_description'=>$holiday_description[$hk], 
                                'status'=> 1, 'created_at' => date('Y-m-d H:i:s')]);
                        } else {
                            DB::table('holidays')->where('holiday_date', $hk)->where('holidays.school_college_id', Auth::User()->id)
                                ->update(['school_college_id' => Auth::User()->id, 
                                'holiday_date'=>$hk, 
                                'holiday_description'=>$holiday_description[$hk], 
                                'status'=> 1, 'updated_at' => date('Y-m-d H:i:s')]);
                        }
                    } else {
                        if(!empty($holiday_description[$hk])) {
                            $ex = DB::table('holidays')->where('holiday_date', $hk)
                                ->where('holidays.school_college_id', Auth::User()->id)->first();
                            if(empty($ex)) {
                                DB::table('holidays')->insert(['school_college_id' => Auth::User()->id, 'holiday_date'=>$hk, 'holiday_description'=>$holiday_description[$hk], 
                                    'status'=> 0, 'created_at' => date('Y-m-d H:i:s')]);
                            } else {
                                DB::table('holidays')->where('holiday_date', $hk)
                                    ->where('holidays.school_college_id', Auth::User()->id)
                                    ->update(['school_college_id' => Auth::User()->id, 
                                    'holiday_date'=>$hk, 
                                    'holiday_description'=>$holiday_description[$hk], 
                                    'status'=> 0, 'updated_at' => date('Y-m-d H:i:s')]);
                            }
                        }
                    }
                }
            } 

            return response()->json(['status' => 1, 'message' => 'Holiday Details saved successfully']);
            
        } else {
            return redirect('/admin/login');
        }
    }

    //Banner

    public function viewBanners()
    {
        if (Auth::check()) {
            return view('admin.banner');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getBanners(Request $request)
    {

        $banners = Banner::orderby('id', 'DESC')->get();

        return Datatables::of($banners)->make(true);

    }

    public function postBanners(Request $request)
    {if (Auth::check()) {

        $id = $request->id;

        $name = $request->name;

        $status = $request->status;

        $short_desc = $request->short_desc;

        $long_desc = $request->long_desc;

        $position = $request->position;

        $type = $request->type;

        $is_link = $request->is_link;

        $link_level = $request->link_level;

        $category_id = $request->category_id;

        $product_id = $request->product_id;

        $link_level = $request->link_level;

        $image = $request->file('image');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {

            $msg = $validator->errors()->all();

            return response()->json([

                'status' => "FAILED",
                'message' => "Please check your all inputs",
            ]);
        }

        /*if($is_link == 'YES') {
        if(empty($link_level) || empty($link_id)) {
        return response()->json([

        'status' => "FAILED",
        'message' => "Please check your all inputs"
        ]);
        }
        }*/

        if ($id > 0) {
            $banner = Banner::find($id);
            $banner->updated_by = Auth::User()->id;
            $banner->updated_at = date('Y-m-d H:i:s');
        } else {
            $banner = new Banner;
            $banner->created_by = Auth::User()->id;
            $banner->created_at = date('Y-m-d H:i:s');
        }

        if (!empty($image)) {

            $bannerimg = rand() . time() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('/uploads/banners');

            $image->move($destinationPath, $bannerimg);

            $banner->banner_image = $bannerimg;

        }

        $banner->name = $name;

        $banner->short_desc = $short_desc;

        $banner->long_desc = $long_desc;

        $banner->status = $status;

        $banner->type = $type;

        $banner->position = $position;

        $banner->is_link = $is_link;

        /*$category_id = 0;

        if($is_link == 'NO') {
        $link_level = 0;
        $link_id = 0;
        $category_id = 0;
        }   else if($is_link == 'YES') {
        $category_id = $link_id;
        if($link_lev el == 1) {
        $category_id = DB::table('categories')->where('id', $link_id)->value('id');
        }   else if($link_level == 2)   {
        $category_id = DB::table('categories')->where('id', $link_id)->value('id');
        }
        }*/

        /*if($link_level == 0 || $link_level == 2) {
        $banner->type = 'MENU_BANNER';
        }   else {
        $banner->type = 'TOP_BANNER';
        }  */

        $banner->category_id = $category_id;

        $banner->link_level = $link_level;

        $banner->link_id = $product_id;

        $banner->save();

        return response()->json(['status' => 'SUCCESS', 'message' => 'Banner has been saved'], 201);

    } else {
        return redirect('/admin/login');
    }
    }

    public function editBanners(Request $request)
    {
        if (Auth::check()) {
            $banner = Banner::where('id', $request->code)->get();
            if ($banner->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $banner[0], 'message' => 'Banner Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Banner Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Days
    /* Function: viewDays
     */
    public function viewDays()
    {
        if (Auth::check()) {
            return view('admin.days');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getDays
    Datatable Load
     */
    public function getDays(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $sectionsqry = DB::table('days')->where('id', '>', 0)
                ->select('day_name');
            $filteredqry = DB::table('days')->where('id', '>', 0)
                ->select('day_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'days.position';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $sections = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('days.id')->count();

            $totalDataqry = Sections::orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    //Period Timings
    public function viewPeriodTiming()
    {
        if (Auth::check()) {
            $classes = Classes::where('id','>',0)->where('status','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $periods = Periodtiming::all();

            return view('admin.periodtiming')->with('periods', $periods)->with('classes',$classes);
        } else {
            return redirect('/admin/login');
        }
    }

    
    public function getPeriods(Request $request)
    {

        if (Auth::check()) {  

            $class_id = $request->get('class_id',0);


            if($class_id != ''){
             $period = Periodtiming::leftjoin('classes', 'classes.id', 'period_timings.class_id')
                ->where('class_id','=',$class_id)->where('period_timings.id','!=',1)
                ->where('classes.status','ACTIVE');
                if(Auth::User()->user_type == 'SCHOOL') {
                    $period->where('period_timings.school_id', Auth::User()->id); 
                }
                $period = $period->select('period_timings.*')
                ->orderby('updated_at','desc')->get();

            }else{
                $period = Periodtiming::leftjoin('classes', 'classes.id', 'period_timings.class_id')
                ->where('period_timings.id','!=',1)
                ->where('classes.status','ACTIVE');
                if(Auth::User()->user_type == 'SCHOOL') {
                    $period->where('classes.school_id', Auth::User()->id); 
                }
                $period = $period->select('period_timings.*')
                ->orderby('updated_at','desc')->where('period_timings.id','!=',1)->get();
            }
            return Datatables::of($period)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function addPeriods(Request $request)
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
           return view('admin.addperiod_timings')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    public function postPeriodTiming(Request $request)
    {

        $id = $request->id;
        $class_id = $request->class_id;
        $period_1 = $request->period_1;
        $period_2 = $request->period_2;
        $period_3 = $request->period_3;
        $period_4 = $request->period_4;
        $period_5 = $request->period_5;
        $period_6 = $request->period_6;
        $period_7 = $request->period_7;
        $period_8 = $request->period_8;

        $validator = Validator::make($request->all(), [
            'class_id' => 'required',
         ]);

       if ($validator->fails()) {

            $msg = $validator->errors()->all();

            return response()->json([

                'status' => "FAILED",
                'message' => "Please Select the Class",
            ]);
        }
      if($period_1){
        $newPeriod_1 = date('h:i A', strtotime($period_1));
      }else{
       $newPeriod_1 = "00:00";
      }

      if($period_2){
        $newPeriod_2 = date('h:i A', strtotime($period_2));
      }else{
        $newPeriod_2 = "00:00";
      }

      if($period_3){
        $newPeriod_3 = date('h:i A', strtotime($period_3));
      }else{
        $newPeriod_3 ="00:00";
      }

      if($period_4){
        $newPeriod_4 = date('h:i A', strtotime($period_4));
      }else{
        $newPeriod_4 = "00:00";
      }

      if($period_5){
        $newPeriod_5 = date('h:i A', strtotime($period_5));
      }else{
        $newPeriod_5 = "00:00";
      }

      if($period_6){
        $newPeriod_6 = date('h:i A', strtotime($period_6));
      }else{
        $newPeriod_6 = "00:00";
      }

      if($period_7){
        $newPeriod_7 = date('h:i A', strtotime($period_7));
      }else{
        $newPeriod_7 = "00:00";
      }

      if($period_8){
        $newPeriod_8 = date('h:i A', strtotime($period_8));
      }else{
        $newPeriod_8 = "00:00";
      }


      if ($id > 0) {
        $exists = DB::table('period_timings')->where('class_id', $class_id)->whereNotIn('id', [$id])->first();
    } else {
        $exists = DB::table('period_timings')->where('class_id', $class_id)->first();
    }

    if (!empty($exists)) {
        return response()->json(['status' => 'FAILED', 'message' => 'Period Alreay Created for the Selected Class'], 201);
    }

      
       if ($id > 0) {
            $period = Periodtiming::find($id);
        } 
        else {
            $period = new Periodtiming;
        }
        if(Auth::User()->user_type == 'SCHOOL') {
            $period->school_id = Auth::User()->id;   
        } 
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
        return response()->json(['status' => 'SUCCESS', 'message' => 'Period Saved Successfully']);

        

        // $periodtiming = Periodtiming::where($id);
        // $periodtiming->period_1 = $newPeriod_1;
        // $periodtiming->period_2 = $newPeriod_2;
        // $periodtiming->period_3 = $newPeriod_3;
        // $periodtiming->period_4 = $newPeriod_4;
        // $periodtiming->period_5 = $newPeriod_5;
        // $periodtiming->period_6 = $newPeriod_6;
        // $periodtiming->period_7 = $newPeriod_7;
        // $periodtiming->period_8 = $newPeriod_8;
        // $periodtiming->updated_at = date('Y-m-d H:i:s');
        // $periodtiming->update();

        // return redirect()->back();

    }



    public function getPeriodTiming(Request $request)
    {
         
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE')->orderby('position','asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $period = [];
            $period = Periodtiming::where('id', $request->get('id'))->get();
             if($period->isNotEmpty()) {
                $period = $period->toArray();
                $period = $period[0];
            }   else {
                $period = [];
            }
        // }
        // echo "<pre>"; print_r($period); exit;
        return view('admin.editperiod_timings')->with('classes',$classes)->with(['period'=>$period]);
            // if ($period->isNotEmpty()) {
            //   return view('admin.editperiod_timings')->with('period',$period);
            // } else {
            //     return response()->json(['status' => "FAILED", 'data' => [], 'message' => 'No Circular Detail']);
            // }
        } else {
            return redirect('/admin/login');
        }
    }


    
  

    public function fetchClasses(Request $request){
         $getclass_id = Classes::where('id',$request->class_id)->where('status','ACTIVE')->select('class_name','position')->first();
         if(!empty($getclass_id)) {
            $class_name = $getclass_id->class_name;
            $position = $getclass_id->position;
            $classes = DB::table('classes')->where('position', '>', $position);
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $data['classes'] = $classes->where('status','=','ACTIVE')->get();
       
            return response()->json($data);
        }

    }


    public function fetchClasseSubject(Request $request){
        $data['subjects'] = []; $subs = [];
        $mapped_subjects = DB::table('sections')->where('class_id', $request->class_id)->where('status', 'ACTIVE')
            ->orderby('position','asc')->get();
        if($mapped_subjects->isNotEmpty()) {
            foreach($mapped_subjects as $sec) {
                $mapsubs = $sec->mapped_subjects;
                $mapsubs = explode(',', $mapsubs);
                $subs = array_merge($subs, $mapsubs);
            }
            if(count($subs)>0) {
                $subjs = Subjects::whereIn("id", $subs)->where('status', 'ACTIVE');
                if(Auth::User()->user_type == 'SCHOOL') {
                    $subjs->where('school_id', Auth::User()->id); 
                }
                $subjs = $subjs->select("subject_name", "id")->orderby('position','asc')->get();
                $data['subjects'] = $subjs;
            }
        }

    return response()->json($data);
   }


    public function fetchSection(Request $request)
    {

        $data['section'] = Sections::where('class_id', $request->class_id)->where('status','=','ACTIVE')
            ->orderby('position', 'asc')
            ->get(["section_name", "id"]);

        return response()->json($data);
    }

    public function fetchSubjectSection(Request $request)
    {

        $data['sections'] = Sections::where('class_id', $request->class_id)
            ->whereRAW(' FIND_IN_SET('.$request->subject_id.', mapped_subjects) ')->where('status','ACTIVE')
            ->orderby('position', 'asc')
            ->get(["section_name", "id"]);

        return response()->json($data);
    }

    public function fetchStudent(Request $request)
    {
        if($request->section_id) {        
            $data['student'] = Student::leftjoin('users','users.id','students.user_id')
                ->where('students.class_id', $request->class_id)
                ->where('students.section_id',$request->section_id)
                ->where('users.status','=','ACTIVE')
                ->get(["users.name", "users.id"]);
        }
        else{
            $data['student'] = Student::leftjoin('users','users.id','students.user_id')
                ->where('students.class_id', $request->class_id)
                ->where('users.status','=','ACTIVE')
                ->get(["users.name", "users.id"]);
        }

        return response()->json($data);
    }

    public function fetchTest(Request $request)
    {

       $data['tests'] = DB::table('tests')->where('class_id', $request->class_id)->where('is_self_test',0)->where('subject_id', $request->subject_id)->where('status','=','ACTIVE')
            ->get(["test_name", "id", "from_date", "to_date"]);

        return response()->json($data);
    }

    public function fetchExams(Request $request)
    {
        /*$data['exams'] = Exams::whereRAW(' FIND_IN_SET('.$request->class_id.', class_ids) ')
            ->select("exam_name", "id", "exam_startdate", DB::RAW(' DATE_FORMAT(exam_startdate, "%Y-%m") as monthyear'))->where('status','ACTIVE')->get();*/

        $data['exams'] = DB::table('exams')
            ->leftjoin('exam_sessions', 'exams.id', 'exam_sessions.exam_id')
            ->leftjoin('classes', 'classes.id', 'exam_sessions.class_id')
            ->where('class_id', $request->class_id)->where('exam_sessions.status', 'ACTIVE')
            ->select("exam_name", "exams.id", "exam_startdate", DB::RAW(' DATE_FORMAT(exam_startdate, "%Y-%m") as monthyear'))
            ->groupby('exams.id')->orderby('exams.id', 'asc')->get();

        return response()->json($data);
    }

    public function fetchTerms(Request $request)
    {
        $data['terms'] = Terms::whereRAW(' FIND_IN_SET('.$request->class_id.', class_ids) ')->where('status','ACTIVE')->select("term_name", "id")->get();

        return response()->json($data);
    }

    public function fetchStudentClass(Request $request){
        // echo $request->student_id;
        $data = Student::leftjoin('users','users.id','students.user_id')->where('students.user_id', $request->student_id)->where('users.status','=','ACTIVE')
        ->get(["students.class_id", "students.section_id"]);
        return response()->json($data);
        
    }

    public function fetchSubject(Request $request)
    {
        $data['subjects'] = []; $subs = [];
        $section_id = $request->section_id;
        $isclass = $request->isclass;
        if($isclass == 1) {
            $mapped_subjects = DB::table('sections')->where('class_id', $section_id)
                ->where('status', 'ACTIVE')->orderby('position', 'asc')->get();
            if($mapped_subjects->isNotEmpty()) {
                foreach($mapped_subjects as $sec) {
                    $mapsubs = $sec->mapped_subjects;
                    $mapsubs = explode(',', $mapsubs);
                    $subs = array_merge($subs, $mapsubs);
                }
                if(count($subs)>0) {
                    $data['subjects'] = Subjects::whereIn("id", $subs)->where('status','ACTIVE')
                        ->select("subject_name", "id")->orderby('position', 'asc')->get();
                }
            }

        } else {
            $mapped_subjects = DB::table('sections')->where('id', $section_id)->value('mapped_subjects');
            if(!empty($mapped_subjects)) {
                $mapped_subjects = explode(',', $mapped_subjects);
                $data['subjects'] = Subjects::whereIn("id", $mapped_subjects)->where('status','ACTIVE')
                    ->select("subject_name", "id")->orderby('position', 'asc')->get();
            }
        }
        return response()->json($data);
    }

    /*public function fetchSubject(Request $request)
    {
        $data['subjects'] = []; $subs = [];
        $section_id = $request->section_id;
        $isclass = $request->isclass;
        if($isclass == 1) {
            $mapped_subjects = DB::table('sections')->where('class_id', $section_id)
                ->where('status', 'ACTIVE')->orderby('position', 'asc')->get();
            if($mapped_subjects->isNotEmpty()) {
                foreach($mapped_subjects as $sec) {
                    $mapsubs = $sec->mapped_subjects;
                    $mapsubs = explode(',', $mapsubs);
                    $subs = array_merge($subs, $mapsubs);
                }
                if(count($subs)>0) {
                    $data['subjects'] = Subjects::whereIn("id", $subs)->where('status','ACTIVE')
                        ->select("subject_name", "id")->orderby('position', 'asc')->get();
                }
            }

        } else {
            $mapped_subjects = DB::table('sections')->where('id', $section_id)->value('mapped_subjects');
            if(!empty($mapped_subjects)) {
                $mapped_subjects = explode(',', $mapped_subjects);
                $data['subjects'] = Subjects::whereIn("id", $mapped_subjects)->where('status','ACTIVE')
                    ->select("subject_name", "id")->orderby('position', 'asc')->get();
            }
        }
        return response()->json($data);
    }*/

    public function fetchExamSubject(Request $request)
    {
        $data['subject'] = []; $subs = [];
        $exam_id = $request->exam_id; 
        $class_id = $request->class_id; 
        $subjects = DB::table('exams')->leftjoin('exam_sessions', 'exam_sessions.exam_id', 'exams.id')
                        ->leftjoin('subjects', 'subjects.id', 'exam_sessions.subject_id')
                        ->where('exams.id',$exam_id)->where('subjects.id','>',0); 
        if($class_id > 0) {
            $subjects->where('class_id',$class_id);
        }
        $subjects = $subjects->select('exam_sessions.subject_id as id', 'subjects.subject_name as name')
                ->groupby('exam_sessions.subject_id')->get();
        $data['subject'] = $subjects;
        return response()->json($data);
    }

    //Time tables
    public function viewTimetable(Request $request) {
        if (Auth::check()) {

            $class = $periods = $days = $subjects = '';
            $class = Classes::select('*')->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $class->where('school_id', Auth::User()->id); 
            }
            $class = $class->get();
            // $class = Section::select('*')->get()->where('status','=','ACTIVE');
           /* $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first()->toArray();


            $days = DB::table('days')->select('*')->get();

            $subjects = Subjects::all();*/

            return view('admin.timetable')->with('class', $class)->with('periods', $periods)->with('days', $days)->with('subjects', $subjects);

        } else {
            return redirect('/admin/login');
        }
    }

    //Attendance Management
    /* Function: loadTimetable
     */
    public function loadTimetable(Request $request)   {
        if(Auth::check()){
            $class_id = $request->get('class_id', '');
            $section_id = $request->get('section_id', '');
            $map_subjects = '';    $timetable = [];
            if($class_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Class']);
            }
            if($section_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Section']);
            }
            if (($class_id > 0) && ($section_id > 0)) {
                $sections = Sections::where("id", $section_id)->where("class_id", $class_id)->get();
                if($sections->isNotEmpty()) {
                    foreach ($sections as $section) {
                        $map_subjects = $section->mapped_subjects;
                    }

                    $periods = Periodtiming::where('class_id',$class_id);
                    if(Auth::User()->user_type == 'SCHOOL') {
                        $periods->where('school_id', Auth::User()->id); 
                    }
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

                    // echo "<pre>";print_r($timetable);
                    // exit;
                }
                else{
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please Assign Periods For Selected Class']);
                }
                    $idsArr = explode(',', $map_subjects);
                    $subjects = DB::table('subjects')->whereIn('id', $idsArr)->orderby('position', 'asc')->get();
                    // $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first()->toArray();
                    $class = Classes::select('*')->get();
                    $days = DB::table('days')->select('*')->get();
                    $html = view('admin.loadtimetable')->with('class', $class)->with('periods', $periods)->with('days', $days)->with('subjects', $subjects)->with('class_id', $class_id)->with('section_id', $section_id)->with('timetable', $timetable)->render();
                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Timetable']);
                }  else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Not a valid section']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
            }
            return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
        }else{
            return redirect('/login');
        }
    }

    public function saveTimetable(Request $request)
    {
        $class_id = $request->tclass_id;
        $section_id = $request->tsection_id;
        $subject_id = $request->subject_id;

        if($class_id > 0 && $section_id > 0) {
            if(count($subject_id)>0) {
                foreach($subject_id as $day_id=>$subjects) {
                    $data = [];
                    if(count($subjects)>0) {
                        foreach($subjects as $key => $subid) {
                            $data[$key] = $subid;
                        }
                    }
                 

                    $chk = DB::table('timetables')->where(['class_id'=>$class_id, 'section_id'=>$section_id, 'day_id'=>$day_id])->first();

                    if(!empty($chk)) {
                        $id = $chk->id;
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $timetable = Timetable::where('id', $id)->update($data);
                    }   else {
                        $data['class_id'] = $class_id;
                        $data['section_id'] = $section_id;
                        $data['day_id'] = $day_id;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        Timetable::insert($data);
                    }
                }
                return response()->json(['status' => 'SUCCESS', 'message' => 'Timetable saved successfully']);
            }   else {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid timetable']);
            }
        }   else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Class and Section']);
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Timetables has been saved'], 201);

    }

    // Load Levels

    public function loadLevels(Request $request)
    {
        $link_level = $request->link_level;
        $link_id = $request->link_id;
        $levels = '';
        if ($link_level == 1) {
            $levels = DB::table('categories')->select('id', 'name')->where('parent_id', 0)->where('status', 'ACTIVE')->get();
            $leveloption = '<option value="">Select Sub Category</option>';
        } else if ($link_level == 2) {
            $levels = DB::table('categories')->select('id', 'name')->where('parent_id', '!=', 0)->where('status', 'ACTIVE')->get();
            $leveloption = '<option value="">Select Service</option>';
        } else {
            $leveloption = '<option value="">Select</option>';
        }

        if (!empty($levels) && $levels->isNotEmpty()) {
            foreach ($levels as $key => $value) {
                if ($link_id == $value->id) {
                    $selected = ' selected ';
                } else {
                    $selected = '';
                }
                $leveloption .= '<option value="' . $value->id . '" ' . $selected . '>' . $value->name . '</option>';
            }
        }
        return response()->json(['status' => 'SUCCESS', 'message' => 'Levels List', 'data' => $leveloption]);
    }

    //Sports

    public function viewSports()
    {
        if (Auth::check()) {
            return view('admin.sports');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getSports(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('statestatus', '');

            $users_qry = Sports::where('id', '>', 0);
            $filtered_qry = Sports::where('id', '>', 0);

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'sports.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'sports.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $totalData = $users_qry->select('sports.id')->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->select('sports.id')->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $sports = $users_qry->select('sports.*')
                ->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $data = [];
            if (!empty($sports)) {
                $sports = $sports->toArray();
                foreach ($sports as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postSports(Request $request)
    {

        if (Auth::check()) {

            $id = $request->id;

            $name = $request->name;

            $status = $request->status;

            $short_desc = $request->short_desc;

            $long_desc = $request->long_desc;

            $position = $request->position;

            $image = $request->file('image');

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs",
                ]);
            }

            if ($id > 0) {
                $banner = Sports::find($id);
                $banner->updated_by = Auth::User()->id;
                $banner->updated_at = date('Y-m-d H:i:s');
            } else {
                $banner = new Sports;
                $banner->created_by = Auth::User()->id;
                $banner->created_at = date('Y-m-d H:i:s');
            }

            if (!empty($image)) {

                $bannerimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/sports');

                $image->move($destinationPath, $bannerimg);

                $banner->sport_image = $bannerimg;

            }

            $banner->name = $name;

            $banner->short_desc = $short_desc;

            $banner->long_desc = $long_desc;

            $banner->status = $status;

            $banner->position = $position;

            $banner->save();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Sports has been saved'], 201);

        } else {
            return redirect('/admin/login');
        }
    }

    public function editSports(Request $request)
    {
        if (Auth::check()) {
            $sports = Sports::where('id', $request->code)->get();
            if ($sports->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $sports[0], 'message' => 'Sports Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Sports Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Import Students

    public function viewImportStudents()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('school_id', Auth::User()->id)->where('status', 'ACTIVE')->get();
            $sections = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.school_id', Auth::User()->id)
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')->where('sections.status','=','ACTIVE')
                ->select('sections.*', 'classes.class_name')->get();
            return view('admin.import_students')->with(['classes' => $classes, 'sections' => $sections]);

        } else {
            return redirect('/admin/login');
        }
    }

    //Import Teachers

    public function viewImportTeachers()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('school_id', Auth::User()->id)->where('status', 'ACTIVE')->get();
            $sections = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.school_id', Auth::User()->id)
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')->where('sections.status','=','ACTIVE')
                ->select('sections.*', 'classes.class_name')->get();
            return view('admin.import_teachers')->with(['classes' => $classes, 'sections' => $sections]);

        } else {
            return redirect('/admin/login');
        }
    }

    //Students

    public function viewStudents()
    {
        if (Auth::check()) {
           
            return view('admin.students');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getStudents(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $status = $request->get('status_id', '');
            $section = $request->get('section_id', '');
            $class_id = $request->get('class_id', '');

            $users_qry = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('students', 'students.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'students.class_id')
                ->leftjoin('sections', 'sections.id', 'students.section_id')
                ->where('user_type', 'STUDENT')
                ->where('users.delete_status',0)
                ->where('students.delete_status',0)
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'students.roll_no', 'students.admission_no', 'students.class_id', 'students.section_id', 'students.father_name',
                'students.address', 'classes.class_name', 'sections.section_name');
            $filtered_qry = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('students', 'students.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'students.class_id')
                ->leftjoin('sections', 'sections.id', 'students.section_id')
                ->where('user_type', 'STUDENT')
                ->where('users.delete_status',0)
                ->where('students.delete_status',0)
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'students.roll_no', 'students.admission_no', 'students.class_id', 'students.section_id', 'students.father_name',
                'students.address', 'classes.class_name', 'sections.section_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

            if(!empty($status)){
                $users_qry->where('users.status',$status);
                $filtered_qry->where('users.status',$status);
            }
            if(!empty($section)){
                $users_qry->where('students.section_id',$section);
                $filtered_qry->where('students.section_id',$section);
            }
            if(!empty($class_id)){
                $users_qry->where('students.class_id',$class_id);
                $filtered_qry->where('students.class_id',$class_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'users.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $totalData = User::leftjoin('countries', 'countries.id', 'users.country')
            ->leftjoin('states', 'states.id', 'users.state_id')
            ->leftjoin('districts', 'districts.id', 'users.city_id')
            ->leftjoin('students', 'students.user_id', 'users.id')
            ->leftjoin('classes', 'classes.id', 'students.class_id')
            ->leftjoin('sections', 'sections.id', 'students.section_id')
            ->where('users.delete_status',0)
            ->where('students.delete_status',0)
            ->where('user_type', 'STUDENT');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalData->where('users.school_college_id', Auth::User()->id); 
            }
            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    //Players

    public function viewPlayers()
    {
        if (Auth::check()) {
            return view('admin.players');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getPlayers(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('statestatus', '');

            $users_qry = User::where('user_type', 'USER')->where('is_player', 1);
            $filtered_qry = User::where('user_type', 'USER')->where('is_player', 1);

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'users.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $totalData = $users_qry->select('users.id')->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->select('users.id')->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $users = $users_qry->select('users.*')
                ->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    //Academies

    public function viewAcademies()
    {
        if (Auth::check()) {
            return view('admin.academies');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getAcademies(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('statestatus', '');

            $users_qry = User::where('user_type', 'ACADEMY');
            $filtered_qry = User::where('user_type', 'ACADEMY');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'users.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $totalData = $users_qry->select('users.id')->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->select('users.id')->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $users = $users_qry->select('users.*')
                ->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    //Schools

    public function viewSchools()
    {
        if (Auth::check()) {
            return view('admin.schools');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getSchools(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('statestatus', '');

            $users_qry = User::where('user_type', 'SCHOOL');
            $filtered_qry = User::where('user_type', 'SCHOOL');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'users.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $totalData = $users_qry->select('users.id')->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->select('users.id')->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $users = $users_qry->select('users.*')
                ->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    public function fetchStates(Request $request)
    {

        $data['states'] = States::where("country_id", $request->country_id)->where('status','ACTIVE')
            ->get(["state_name", "id", "country_id"]);

        return response()->json($data);
    }

    public function fetchDistricts(Request $request)
    {

        $data['districts'] = Districts::where("state_id", $request->state_id)->where('status','ACTIVE')
            ->get(["district_name", "id"]);

        return response()->json($data);
    }

    public function fetchQuestions(Request $request)
    {
        $question_id = $request->questions;
        $question_bank_id = $request->qbid;
        $data['questions'] = QuestionBankItems::where("question_type_id", $request->questions)->where('question_bank_id',$question_bank_id)
            ->get(["question_type_id", "id"]);

        return response()->json($data);
    }

    public function fetchQuestionType(Request $request)
    {
        $question_id = $request->questions;
        $question_bank_id = $request->qbid;
        $data['questions'] = QuestionBankItems::where("id", $request->questions)->where('question_bank_id',$question_bank_id)->get(["question_type_id", "id"]);

        return response()->json($data);
    }

    public function fetchAttendance(Request $request)
    {
        $class_id = $request->class_id;
        $section_id = $request->section_id;
       
        $data['student'] = Student::leftjoin('users','users.id','students.user_id')->where('students.class_id', $request->class_id)->where('students.section_id',$request->section_id)->where('users.status','=','ACTIVE')
            ->get(["users.name", "users.id"]);
        return response()->json($data);
    }

    public function fetchTeachers(Request $request)
    {
        $data['teachers'] = Teacher::leftjoin('users','users.id','teachers.user_id')->where('users.status','=','ACTIVE')->get(["users.name", "users.id"]);
        return response()->json($data);
    }
    //Student

    public function viewStudent()
    {
        if (Auth::check()) {
            $countries = Countries::select('id', 'name')->where('status','=','ACTIVE')->get();
            $classes = Classes::where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            return view('admin.student')->with('countries', $countries)->with('classes',$classes);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getStudent(Request $request)
    {

        if (Auth::check()) {

                $students = Student::query()
                ->with(['users' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'email', 'gender', 'dob', 'country', 'state_id', 'city_id', 'profile_image', 'mobile');
                }])
                ->get();

                return Datatables::of($students)->make(true);
        } else {
            return redirect('/admin/login');
        }
    }

    public function postStudent(Request $request)
    {

        if (Auth::check()) {

            $id = $request->id;
            $name = $request->name;
            $lastname = $request->lastname;
            $gender = $request->gender;
            $email = $request->email;
            $password = $request->password;
            $mobile = $request->mobile;
            $dob = $request->dob;
            $joined_date = $request->joined_date;
            $country = $request->country;
            $state_id = $request->state_id;
            $city_id = $request->city_id;
            $image = $request->file('profile_image');

            $roll_no = $request->roll_no;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $admission_no = $request->admission_no;
            $father_name = $request->father_name;
            $address = $request->address;
            $status = $request->status;
            $mobile1 = $request->mobile1;
            $emergency_contact_no = $request->emergency_contact_no;
            // $mobile = $request

            if($country == ''){
                $country = 0;
            }
            if($state_id == ''){
                $state_id = 0;
            }
            if($city_id == ''){
                $city_id = 0;
            }
            if($roll_no == ''){
                $roll_no = 0;
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mobile' => 'required',
                // 'roll_no' => 'required',
                'admission_no' => 'required',
                'joined_date'  => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if(substr( $mobile, 0, 1 ) === "0") {
                return response()->json(['status' => 0, 'message' => 'Invalid mobile']);
            }

            if((strlen($mobile)<8) || (strlen($mobile)>10)) {
                return response()->json(['status' => 0, 'message' => 'Invalid mobile']);
            }
                  

            if(!empty($email)){
                if ($id > 0) {
                    $exists = DB::table('users')->where('email', $email)->whereNotIn('id', [$id])->first();
                } else {
                    $exists = DB::table('users')->where('email', $email)->first();
                }
    
            }
           
            if(!empty($roll_no)){
                if ($id > 0) {
                    $roll_chk = DB::table('students')->where('roll_no', $roll_no)->whereNotIn('user_id', [$id])->where('class_id',$class_id)->where('section_id',$section_id)->first();
    
                } else {
                    $roll_chk = DB::table('students')->where('roll_no', $roll_no)->where('class_id',$class_id)->where('section_id',$section_id)->first();
                }
            }
          


            if(!empty($admission_no)){
                if ($id > 0) {
                    $admission_no_chk = DB::table('students')->where('admission_no', $admission_no)
                    ->where('school_id', Auth::User()->id)->whereNotIn('user_id', [$id])->first();
                } else {
                    $admission_no_chk = DB::table('students')->where('admission_no', $admission_no)
                    ->where('school_id', Auth::User()->id)->first();
                }
            }
         

            // if ($id > 0) {
            //     $admission_no_chk = DB::table('students')->where('admission_no', $admission_no)->whereNotIn('user_id', [$id])->first();
            // } else {
            //     $radmission_no_chk = DB::table('students')->where('admission_no', $admission_no)->first();
            // }


            if (!empty($exists)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Email Already Exists'], 201);
            }


            if (!empty($roll_chk)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Roll Number Already Exists'], 201);
            }
            if (!empty($admission_no_chk)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Admission Number Already Exists'], 201);
            }

            $date = date('Y-m-d H:i:s');
            if ($id > 0) {
                $users = User::find($id);
                $users->updated_at = $date;
                $users->updated_by = Auth::User()->id;
            } else {
                $users = new User;

                $def_expiry_after =  CommonController::getDefExpiry();
                $users->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                $users->api_token = User::random_strings(30);
                $users->last_login_date = date('Y-m-d H:i:s');
                $users->last_app_opened_date = date('Y-m-d H:i:s');
                $users->user_source_from = 'ADMIN';
                //$users->joined_date = $date;
                $users->created_at = $date;
                $users->created_by = Auth::User()->id;
            }

            $users->user_type = "STUDENT";

            $lastjobid = DB::table('users')
                ->where('created_at', 'like', date('Y-m-d') . '%')
                ->orderby('id', 'desc')->count();
            $lastjobid = $lastjobid + 1;
            $append = str_pad($lastjobid, 6, "0", STR_PAD_LEFT);
            $reg_no = date('ymd') . $append;

            $users->reg_no = $reg_no;
            $users->name = $name;
            $users->last_name = $lastname;
            $users->gender = $gender;
            $users->dob = $dob;
            $users->joined_date = $joined_date;
            $users->email = $email;
            $users->status = $status;

            if(!empty($password)) {
                $users->password = Hash::make($password);
            }

            $users->school_college_id = Auth::User()->id;

            $users->passcode = $password;
            $country_code = DB::table('countries')->where('id', $country)->value('phonecode');
            $users->mobile = $mobile;
            $users->mobile1 = $mobile1;
            $users->country = $country;
            $users->country_code = $country_code;
            $users->code_mobile = $country_code.$mobile;
            $users->codemobile1 = $country_code.$mobile1;
            $users->emergency_contact_no = $emergency_contact_no;
            $users->state_id = $state_id;
            $users->city_id = $city_id;
            $users->admission_no = $admission_no;
            $phone_code = $users->code_mobile;


            // if ($id > 0) {
            //     $phone_err = DB::table('users')->where('code_mobile',  $users->code_mobile)->whereNotIn('id', [$id])->first();
            // } else {
            //     $phone_err = DB::table('users')->where('code_mobile',  $users->code_mobile)->first();
            // }
            // if (!empty($phone_err)) {
            //     return response()->json(['status' => 'FAILED', 'message' => 'Mobile Number Already Exists'], 201);
            // }

            // exit;

            if (!empty($image)) {

                $ext = $image->getClientOriginalExtension();
                if (!in_array($ext, $this->accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                }

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/userdocs/');

                $image->move($destinationPath, $countryimg);

                $users->profile_image = $countryimg;

            }
            $users->save();

            $userId = $users->id;

            if ($id > 0) {
                $students = Student::where('user_id', $id)->first();
                if(empty($students)) {
                    $students = new Student;
                }
            } else {
                $students = new Student;
            }
            $students->school_id = Auth::User()->id;
            $students->user_id = $userId;
            $students->roll_no = $roll_no;
            $students->class_id = $class_id;
            $students->section_id = $section_id;
            $students->admission_no = $admission_no;
            $students->father_name = $father_name;
            $students->address = $address;
            $students->status = $status;
            $students->save();


            if ($id > 0) {
                $academics = StudentAcademics::where('user_id', $id)->first();
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
            $acadamic_year = $settings->acadamic_year;
            $from_month = $acadamic_year.'-'.'06';
            $to_year = $acadamic_year + 1;
            $to_month = $to_year.'-'.'04';
            $academics->user_id = $userId;
            $academics->academic_year = $acadamic_year;
            $academics->from_month = $from_month;
            $academics->to_month = $to_month;
            $academics->class_id = $class_id;
            $academics->section_id = $section_id;
            $academics->status = $status;

            $academics->save();

            return response()->json(['status' => 'SUCCESS', 'message' => 'Student Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editStudent(Request $request)
    {

        if (Auth::check()) {

                /*$students = Student::query()
                ->with(['users' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'email', 'gender', 'dob', 'country', 'state_id', 'city_id', 'profile_image', 'mobile');
                }])
                ->where('user_id', $request->id)
                ->get();*/

            $students = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('students', 'students.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'students.class_id')
                ->leftjoin('sections', 'sections.id', 'students.section_id')
                ->where('user_type', 'STUDENT')
                ->where('users.id', $request->id)
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'students.roll_no', 'students.admission_no', 'students.class_id', 'students.section_id', 'students.father_name',
                'students.address', 'classes.class_name', 'sections.section_name')->get();

            if ($students->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $students[0], 'message' => 'Student Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Student Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }


    public function deleteStudent(Request $request)
    {
        if (Auth::check()) {
            $chk_cnt = StudentAcademics::where('user_id',$request->id)->get()->count();
            if($chk_cnt == 0){
            $students = User::leftjoin('countries', 'countries.id', 'users.country')
            ->leftjoin('students', 'students.user_id', 'users.id')
            ->where('user_type', 'STUDENT')
            ->where('users.id', $request->id)->get();
            if ($students->isNotEmpty()) {
                User::where('id', $request->id)->update(['delete_status'=>1]);
                student::where('user_id', $request->id)->update(['delete_status'=>1]);
                return response()->json(['status' => 'SUCCESS', 'data' => null, 'message' => 'Student Deleted Successfully..!'],201);
            } else {
                return response()->json(['status' => 'FAILED', 'message' => 'No Student Details'], 201);
               
            }
        }
        else{
            return response()->json(['status' => 'FAILED', 'message' => 'The Student was Already Mapped For Class So Unable to delete the Student..!'], 201);
        

        }
        } else {
            return redirect('/admin/login');
        }
    }

    public function viewStudentAcademics(Request $request) {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE')->orderby('position', 'Asc')->get();
            $students  = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                ->where('users.status', 'ACTIVE')->where('user_type', 'STUDENT')
                ->select('users.id', 'name', 'last_name', 'students.admission_no')->orderby('students.admission_no', 'Asc')->get();
            return view('admin.studentsacademics')->with('classes', $classes)->with('students', $students);

            $students = DB::table('users')->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                ->where('user_type', 'STUDENT')
                ->select('users.id', 'users.name', 'users.email', 'student_class_mappings.*')->get();

            if ($students->isNotEmpty()) {
                $html = view('admin.student_academics')->with('students', $students)->render();
                return response()->json(['status' => 'SUCCESS', 'data' => $students, 'message' => 'Student Academics Detail',
                    'data' => $html
                ]);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Student Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Teachers

    public function viewTeachers()
    {
        if (Auth::check()) {
            $countries = Countries::select('id', 'name')->where('status','=','ACTIVE')->get();
            $classes = Classes::where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get(); 
            $subjects = Subjects::all();
            return view('admin.teachers')->with('countries', $countries)->with('classes', $classes)->with('subjects', $subjects);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getTeachers(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('statestatus', '');
            $subject = $request->get('subject', '0');
            $status = $request->get('status_id','');
            $section = $request->get('section_id','');
            $class_id = $request->get('class_id','');
            $users_qry = User::with('teachers')->leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'teachers.class_tutor')
                ->leftjoin('sections', 'sections.id', 'teachers.section_id')
                ->where('user_type', 'TEACHER')
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'teachers.emp_no', 'teachers.date_of_joining', 'teachers.qualification', 'teachers.exp', 'teachers.post_details',
                'teachers.subject_id', 'teachers.class_id', 'teachers.class_tutor',  'teachers.section_id', 'teachers.father_name',
                'teachers.address', 'classes.class_name', 'sections.section_name');

                if($subject != 0){
                    $users_qry->where('teachers.subject_id','like','%'.$subject.'%');

                }
            $filtered_qry = User::with('teachers')->leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'teachers.class_tutor')
                ->leftjoin('sections', 'sections.id', 'teachers.section_id')
                ->where('user_type', 'TEACHER')
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'teachers.emp_no', 'teachers.date_of_joining', 'teachers.qualification', 'teachers.exp', 'teachers.post_details',
                'teachers.subject_id', 'teachers.class_id', 'teachers.class_tutor',  'teachers.section_id', 'teachers.father_name',
                'teachers.address', 'classes.class_name', 'sections.section_name');
                if($subject != 0){
                    $filtered_qry->where('teachers.subject_id','like','%'.$subject.'%');

                }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

            if(!empty($status)){
                $users_qry->where('users.status',$status);
                $filtered_qry->where('users.status',$status);
            }
            if(!empty($section)){
                $users_qry->where('teachers.section_id',$section);
                $filtered_qry->where('teachers.section_id',$section);
            }
            
            if(!empty($class_id)){
                $users_qry->where('teachers.class_id',$class_id);
                $filtered_qry->where('teachers.class_id',$class_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'users.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $totalData = User::with('teachers')->leftjoin('countries', 'countries.id', 'users.country')
            ->leftjoin('states', 'states.id', 'users.state_id')
            ->leftjoin('districts', 'districts.id', 'users.city_id')
            ->leftjoin('teachers', 'teachers.user_id', 'users.id')
            ->leftjoin('classes', 'classes.id', 'teachers.class_tutor')
            ->leftjoin('sections', 'sections.id', 'teachers.section_id')
            ->where('user_type', 'TEACHER');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalData->where('users.school_college_id', Auth::User()->id); 
            }
            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

        /*if (Auth::check()) {

            $teachers = Teacher::query()
                ->with(['users' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'email', 'gender', 'dob', 'country', 'state_id', 'city_id', 'profile_image', 'mobile');
                }])
                ->get();

            return Datatables::of($teachers)->make(true);

        } else {
            return redirect('/admin/login');
        }*/

    }

    public function postTeachers(Request $request)
    {

        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name;
            $lastname = $request->lastname;
            $gender = $request->gender;
            $email = $request->email;
            $mobile = $request->mobile;
            $dob = $request->dob;
            $country = $request->country;
            $state_id = $request->state_id;
            $city_id = $request->city_id;
            $image = $request->file('profile_image');

            $emp_no = $request->emp_no;
            $date_of_joining = $request->date_of_joining;
            $qualification = $request->qualification;
            $exp = $request->exp;
            $post_details = $request->post_details;
            $subject = $request->subject_id;
            $class = $request->class_id;
            $class_tutor = $request->class_tutor;
            $section_id = $request->section_id;
            $father_name = $request->father_name;

            $subjectId = 0;
            $classId = 0;

            $address = $request->address;
            $password = $request->password;

            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'emp_no' => 'required',
                'date_of_joining' => 'required',
                'mobile' => 'required',
               
            ]);

            if($country == ''){
                $country = 0;
            }
            if($state_id == ''){
                $state_id = 0;
            }
            if($city_id == ''){
                $city_id = 0;
            }
            if($post_details == ''){
                $post_details = 0;
            }
            if($class_tutor == ''){
                $class_tutor = 0;
            }
            if($section_id == ''){
                $section_id = 0;
            }
            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('users')->where('email', $email)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('users')->where('email', $email)->first();
            }

            if ($id > 0) {
                $emp_no_chk = DB::table('teachers')->where('emp_no', $emp_no)->where('school_id', Auth::User()->id)->whereNotIn('user_id', [$id])->first();
            } else {
                $emp_no_chk = DB::table('teachers')->where('emp_no', $emp_no)->where('school_id', Auth::User()->id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Email Already Exists'], 201);
            }

            if (!empty($emp_no_chk)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Employee Number Already Exists'], 201);
            }

            $date = date('Y-m-d H:i:s');
            if ($id > 0) {
                $users = User::find($id);
                $users->updated_at = $date;
                $users->updated_by = Auth::User()->id;
            } else {
                $users = new User;

                $def_expiry_after =  CommonController::getDefExpiry();
                $users->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                $users->api_token = User::random_strings(30);
                $users->last_login_date = date('Y-m-d H:i:s');
                $users->last_app_opened_date = date('Y-m-d H:i:s');
                $users->user_source_from = 'ADMIN';
                $users->joined_date = $date;
                $users->created_at = $date;
                $users->created_by = Auth::User()->id;
            }

            if(!empty($password)) {
                $users->password = Hash::make($password);
            }

            $users->user_type = "TEACHER";

            $lastjobid = DB::table('users')
                ->where('created_at', 'like', date('Y-m-d') . '%')
                ->orderby('id', 'desc')->count();
            $lastjobid = $lastjobid + 1;
            $append = str_pad($lastjobid, 6, "0", STR_PAD_LEFT);
            $reg_no = date('ymd') . $append;

            $users->school_college_id = Auth::User()->id;
            $users->reg_no = $reg_no;
            $users->name = $name;
            $users->last_name = $lastname;
            $users->gender = $gender;
            $users->dob = $dob;
            $users->email = $email;
            $country_code = DB::table('countries')->where('id', $country)->value('phonecode');
            $users->mobile = $mobile;
            $users->country = $country;
            $users->country_code = $country_code;
            $users->code_mobile = $country_code.$mobile;
            $users->state_id = $state_id;
            $users->city_id = $city_id;
            $users->status = $status;
            $users->passcode = $password;
            // if ($id > 0) {
            //     $mobile_chk = DB::table('users')->where('code_mobile', $users->code_mobile)->whereNotIn('id', [$id])->first();
            // } else {
            //     $mobile_chk = DB::table('users')->where('code_mobile', $users->code_mobile)->first();
            // }


            // if (!empty($mobile_chk)) {
            //     return response()->json(['status' => 'FAILED', 'message' => 'Mobile Number Already Exists'], 201);
            // }

            if (!empty($image)) {

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/userdocs/');

                $image->move($destinationPath, $countryimg);

                $users->profile_image = $countryimg;

            }
            $users->save();

            $userId = $users->id;

            if ($id > 0) {
                $teachers = Teacher::where('user_id', $id)->first();
                if(empty($teachers)) {
                    $teachers = new Teacher;
                }
            } else {
                $teachers = new Teacher;
            }
            $teachers->school_id = Auth::User()->id;
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
            $teachers->status = $status;
            $teachers->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Teachers Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editTeachers(Request $request)
    {

        if (Auth::check()) {

            /*$teachers = Teacher::query()
                ->with(['users' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'email', 'gender', 'dob', 'country', 'state_id', 'city_id', 'profile_image', 'mobile');
                }])
                ->where('user_id', $request->id)
                ->get();
            */
            
            $teachers =     User::with('teachers')->leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'teachers.class_tutor')
                ->leftjoin('sections', 'sections.id', 'teachers.section_id')
                ->where('user_type', 'TEACHER')
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'teachers.emp_no', 'teachers.date_of_joining', 'teachers.qualification', 'teachers.exp', 'teachers.post_details',
                'teachers.subject_id', 'teachers.class_id', 'teachers.class_tutor',  'teachers.section_id', 'teachers.father_name',
                'teachers.address')->where('user_id', $request->id)->get();
                //   echo "<pre>"; print_r($teachers); exit;

            if ($teachers->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $teachers[0], 'message' => 'Teachers Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Teachers Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    // Class Teacher
      public function viewClassTeachers()
     {
         if (Auth::check()) {
             $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
             $subjects = Subjects::all();
             $teacher = DB::table('teachers')->leftjoin('users','users.id','teachers.user_id')
                ->where('users.user_type','TEACHER')->where('users.school_college_id', Auth::User()->id)
                ->where('users.status','ACTIVE')->orderby('users.name', 'asc')->get();
             return view('admin.class_teachers')->with('classes', $classes)->with('subjects', $subjects)->with('teacher',$teacher);
         } else {
             return redirect('/admin/login');
         }
     }

     public function getClassTeachers(Request $request)
     {

         if (Auth::check()) {
             $limit = $request->get('length', '10');
             $start = $request->get('start', '0');
             $dir = $request->input('order.0.dir');
             $columns = $request->get('columns');
             $order = $request->input('order.0.column');

             $input = $request->all();
             $status = $request->get('status', '');
             $subject = $request->get('subject', '0');
             $users_qry = ClassTeacher::leftjoin('users','users.id','class_teachers.teacher_id')
                 ->leftjoin('classes', 'classes.id', 'class_teachers.class_id')
                 ->leftjoin('sections', 'sections.id', 'class_teachers.section_id')
                 ->where('user_type', 'TEACHER')
                 ->select('class_teachers.*','users.name','classes.class_name', 'sections.section_name');

            $filtered_qry = ClassTeacher::leftjoin('users','users.id','class_teachers.teacher_id')
            ->leftjoin('classes', 'classes.id', 'class_teachers.class_id')
            ->leftjoin('sections', 'sections.id', 'class_teachers.section_id')
            ->where('user_type', 'TEACHER')
            ->select('class_teachers.*','users.name','classes.class_name', 'sections.section_name');

             if (count($columns) > 0) {
                 foreach ($columns as $key => $value) {
                     if (!empty($value['search']['value']) && !empty($value['name'])) {
                         if ($value['name'] == 'users.status') {
                             $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                             $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                         } else {
                             $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                             $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                         }
                     }
                 }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }


            if(!empty($status)){
                $users_qry->where('class_teachers.status',$status);
                $filtered_qry->where('class_teachers.status',$status);
            }

             if (!empty($order)) {
                 $orderby = $columns[$order]['name'];
             } else {
                 $orderby = 'class_teachers.id';
             }
             if (empty($dir)) {
                 $dir = 'DESC';
             }

             $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
             $totalData = $users_qry->select('class_teachers.id')->get();

             if (!empty($totalData)) {
                 $totalData = count($totalData);
             }
             $totalfiltered = $totalData;
             $filtered = $filtered_qry->get()->toArray();
             if (!empty($filtered)) {
                 $totalfiltered = count($filtered);
             }


             $data = [];
             if (!empty($users)) {
                 $users = $users->toArray();
                 foreach ($users as $post) {
                     $nestedData = [];
                     foreach ($post as $k => $v) {
                         $nestedData[$k] = $v;
                     }
                     $data[] = $nestedData;
                 }
             }

             $json_data = array(
                 "draw" => intval($request->input('draw')),
                 "recordsTotal" => intval($totalData),
                 "data" => $data,
                 "recordsFiltered" => intval($totalfiltered),
             );

             echo json_encode($json_data);
         } else {
             return redirect('/admin/login');
         }

     }

     public function postClassTeachers(Request $request)
     {

         if (Auth::check()) {
             $id = $request->id;
             $teacher_id = $request->teacher_id;
             $section_id = $request->section_id;
             $class_id = $request->class_id;
             $status = $request->status;

             $validator = Validator::make($request->all(), [
                 'teacher_id' => 'required',
                 'section_id' => 'required',
                 'class_id' => 'required',
                ]);

             if ($validator->fails()) {

                 $msg = $validator->errors()->all();

                 return response()->json([

                     'status' => "FAILED",
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
          if ($id > 0) {
                 $teacher_chk = DB::table('class_teachers')->where('teacher_id', $teacher_id)
                    ->where('status', 'ACTIVE')->whereNotIn('id', [$id])->first();

             } else {
                 $teacher_chk = DB::table('class_teachers')->where('teacher_id', $teacher_id)->where('status', 'ACTIVE')->first();

             }

             if($id > 0){
                $class_count = DB::table('class_teachers')->where('class_id', $class_id)->where('section_id', $section_id)->whereNotIn('id', [$id])->where('status', 'ACTIVE')->first();

             }else{
                $class_count = DB::table('class_teachers')->where('class_id', $class_id)->where('section_id', $section_id)->where('status', 'ACTIVE')->first();
             }
             
             if (!empty($teacher_chk)) {
                 return response()->json(['status' => 'FAILED', 'message' => 'Class Already Assigned for this Teacher'], 201);
             }
             if (!empty($class_count)) {
                return response()->json(['status' => 'FAILED', 'message' => 'This Class and Section was Already Assigned for Another Teacher'], 201);
            }
             if ($id > 0) {
                 $teachers = ClassTeacher::where('id', $id)->first();
                 if(empty($teachers)) {
                     $teachers = new ClassTeacher;
                 }
             } else {
                 $teachers = new ClassTeacher;
             }

             $teachers->teacher_id = $teacher_id;
             $teachers->class_id = $class_id;
             $teachers->section_id = $section_id;
             $teachers->status = $status;
             $teachers->save();
             return response()->json(['status' => 'SUCCESS', 'message' => 'Class Teachers Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }

     public function editClassTeachers(Request $request)
     {

         if (Auth::check()) {

           $teachers =  ClassTeacher::leftjoin('users','users.id','class_teachers.teacher_id')
           ->leftjoin('classes', 'classes.id', 'class_teachers.class_id')
           ->leftjoin('sections', 'sections.id', 'class_teachers.section_id')
           ->where('user_type', 'TEACHER')
           ->select('class_teachers.*','users.name','classes.class_name', 'sections.section_name')->where('class_teachers.id', $request->id)->get();

             if ($teachers->isNotEmpty()) {
                 return response()->json(['status' => 'SUCCESS', 'data' => $teachers[0], 'message' => 'Teachers Detail']);
             } else {
                 return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Teachers Detail']);
             }
         } else {
             return redirect('/admin/login');
         }
     }

     // Teachers Subject Mapping

     public function viewMappingSubject()
     {
         if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get(); 
             $subjects = Subjects::all();
             $teacher = DB::table('teachers')->leftjoin('users','users.id','teachers.user_id')->where('users.user_type','TEACHER')->where('users.status','=','ACTIVE')->where('users.school_college_id', Auth::User()->id)->orderby('users.name', 'asc')->get();
             return view('admin.subject_mapping')->with('classes', $classes)->with('subjects', $subjects)->with('teacher',$teacher);
         } else {
             return redirect('/admin/login');
         }
     }
     public function addSubjectMapping(Request $request)
     {
         if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get(); 
            $subjects = Subjects::all();
            $teacher = DB::table('teachers')->leftjoin('users','users.id','teachers.user_id')->where('users.user_type','TEACHER')->where('users.status','=','ACTIVE')->where('users.school_college_id', Auth::User()->id)->orderby('users.name', 'asc')->get();

            return view('admin.addsubject_mapping')->with('classes', $classes)->with('subjects', $subjects)->with('teacher',$teacher);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function cloneMappedSubject(Request $request) {
        if(Auth::check()){
            $html = ''; $i = 2;
            $qtype = $request->get('code', 0);
            $i = $request->get('i', 1);
            $i++;
            if($qtype > 0) {
                $teacher = DB::table('teachers')->leftjoin('users','users.id','teachers.user_id')->where('users.user_type','TEACHER')->where('users.status','=','ACTIVE')->get();
                $classes = Classes::all()->where('status','=','ACTIVE');
                if($teacher->isNotEmpty()) {
                    // echo $i;
                    // $classes = $classes->toArray();
                //   echo "<pre>";  print_r($question_types);
                //       
                $i = 1; 
                foreach($classes as $class){
                   
                   if($class->id){
                            $html = view('admin.loadmappedsubjects')->with('classes', $classes)->with('i', $i)->render();
                            return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Clone Detail']);
                        }   else {
                            $html = view('admin.loadmappedsubjects')->with('classes', $classes)->with('i', $i)->render();
                            return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Clone Detail']);
                        }
                        $i++;
                    }
                   
                }
            }
            // else{
            return response()->json(['status' => 'FAILED', 'data' => $html, 'message' => 'No Clone Detail']);
                    // }
        }   else {
            return response()->json(['status' => 'FAILED', 'data' => $html, 'message' => 'No Clone Detail']);
        }
    }

     public function getMappingSubject(Request $request)
     {

         if (Auth::check()) {
             $limit = $request->get('length', '10');
             $start = $request->get('start', '0');
             $dir = $request->input('order.0.dir');
             $columns = $request->get('columns');
             $order = $request->input('order.0.column');

             $input = $request->all();
             $status = $request->get('status', '');
             $class_id = $request->get('class_id', 0);
             $section_id = $request->get('section_id', 0);
             $subject_id = $request->get('subject_id', 0);

             $subject = $request->get('subject', '0');
             $users_qry = SubjectMapping::leftjoin('users','users.id','subject_mapping.teacher_id')
                 ->leftjoin('classes', 'classes.id', 'subject_mapping.class_id')
                 ->leftjoin('sections', 'sections.id', 'subject_mapping.section_id')
                 ->leftjoin('subjects', 'subjects.id', 'subject_mapping.subject_id')
                 ->where('subject_mapping.status', 'ACTIVE')
                 ->where('user_type', 'TEACHER')
                 ->groupby('subject_mapping.teacher_id')
                 ->select('subject_mapping.*','users.name','subjects.subject_name','classes.class_name', 'sections.section_name');

            $filtered_qry = SubjectMapping::leftjoin('users','users.id','subject_mapping.teacher_id')
            ->leftjoin('classes', 'classes.id', 'subject_mapping.class_id')
            ->leftjoin('sections', 'sections.id', 'subject_mapping.section_id')
            ->leftjoin('subjects', 'subjects.id', 'subject_mapping.subject_id')
            ->where('user_type', 'TEACHER')
            ->where('subject_mapping.status', 'ACTIVE')
            ->groupby('subject_mapping.teacher_id')
            ->select('subject_mapping.*','users.name','subjects.subject_name','classes.class_name', 'sections.section_name');

             if (count($columns) > 0) {
                 foreach ($columns as $key => $value) {
                     if (!empty($value['search']['value']) && !empty($value['name'])) {
                         if ($value['name'] == 'users.status') {
                             $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                             $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                         } else if($value['name'] == 'handling_classes') {
                             $users_qry->whereRaw('( subjects.subject_name like "%'.$value['search']['value'].'%" or classes.class_name like "%'.$value['search']['value'].'%" or sections.section_name like "%'.$value['search']['value'].'%" ) ');
                             $filtered_qry->whereRaw('( subjects.subject_name like "%'.$value['search']['value'].'%" or classes.class_name like "%'.$value['search']['value'].'%" or sections.section_name like "%'.$value['search']['value'].'%" ) ');
                         } else {
                             $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                             $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                         }
                     }
                 }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

            if(!empty($status)){
                $users_qry->where('subject_mapping.status',$status);
                $filtered_qry->where('subject_mapping.status',$status);
            }

            if($class_id > 0){
                $users_qry->where('subject_mapping.class_id',$class_id);
                $filtered_qry->where('subject_mapping.class_id',$class_id);
            }
            if($section_id > 0){
                $users_qry->where('subject_mapping.section_id',$section_id);
                $filtered_qry->where('subject_mapping.section_id',$section_id);
            }
            if($subject_id > 0){
                $users_qry->where('subject_mapping.subject_id',$subject_id);
                $filtered_qry->where('subject_mapping.subject_id',$subject_id);
            }

             if (!empty($order)) {
                 $orderby = $columns[$order]['name'];
             } else {
                 $orderby = 'subject_mapping.id';
             }
             if (empty($dir)) {
                 $dir = 'DESC';
             }

             $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
             $totalData = $users_qry->select('subject_mapping.id')->get();

             if (!empty($totalData)) {
                 $totalData = count($totalData);
             }
             $totalfiltered = $totalData;
             $filtered = $filtered_qry->get()->toArray();
             if (!empty($filtered)) {
                 $totalfiltered = count($filtered);
             }


             $data = [];
             if (!empty($users)) {
                 $users = $users->toArray();
                 foreach ($users as $post) {
                     $nestedData = [];
                     foreach ($post as $k => $v) {
                         $nestedData[$k] = $v;
                     }
                     $data[] = $nestedData;
                 }
             }

             $json_data = array(
                 "draw" => intval($request->input('draw')),
                 "recordsTotal" => intval($totalData),
                 "data" => $data,
                 "recordsFiltered" => intval($totalfiltered),
             );

             echo json_encode($json_data);
         } else {
             return redirect('/admin/login');
         }

     }

     public function postMappingSubject(Request $request)
     {

         if (Auth::check()) {
             $id = $request->id;
             $teacher_id = $request->teacher_id;
             $section_id = $request->section_id;
             $class_id = $request->class_id;
             $subject_id = $request->subject_id;
             $status = "ACTIVE";

             $validator = Validator::make($request->all(), [
                 'teacher_id' => 'required',
                 'section_id' => 'required',
                 'subject_id' => 'required',
                 'class_id' => 'required',
                ]);

             if ($validator->fails()) {

                 $msg = $validator->errors()->all();

                 return response()->json([

                     'status' => "FAILED",
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
          if ($id > 0) {
                 $teacher_chk = DB::table('subject_mapping')->leftjoin('users', 'users.id', 'teacher_id')
                    ->where('class_id', $class_id)->where('section_id', $section_id)->where('subject_id', $subject_id)
                    ->where('subject_mapping.status','ACTIVE')->where('users.status','ACTIVE')->whereNotIn('id', [$id])->first();

             } else {
                 $teacher_chk = DB::table('subject_mapping')->leftjoin('users', 'users.id', 'teacher_id')
                 ->where('class_id', $class_id)->where('section_id', $section_id)->where('subject_id', $subject_id)
                 ->where('subject_mapping.status','ACTIVE')->where('users.status','ACTIVE')->first();

             }

           if (!empty($teacher_chk)) {
                 return response()->json(['status' => 'FAILED', 'message' => 'This Subject Already Mapped for the Selected Teacher'], 201);
             }
            if ($id > 0) {
                 $teachers = SubjectMapping::where('id', $id)->first();
                 if(empty($teachers)) {
                     $teachers = new SubjectMapping;
                 }
             } else {
                 $teachers = new SubjectMapping;
             }

             $teachers->teacher_id = $teacher_id;
             $teachers->subject_id = $subject_id;
             $teachers->class_id = $class_id;
             $teachers->section_id = $section_id;
             $teachers->status = $status;
             $teachers->save();
             return response()->json(['status' => 'SUCCESS', 'message' => 'Subject Mapped Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }

     public function editMappingSubject(Request $request)
     {
        if (Auth::check()) {
            $classes = Classes::all()->where('status','=','ACTIVE');
            $subjects = Subjects::all();
            $teacher = DB::table('teachers')->leftjoin('users','users.id','teachers.user_id')->where('users.user_type','TEACHER')->where('users.id',$request->teacher_id)->where('users.status','=','ACTIVE')->get();

            return view('admin.editsubjectmapping')->with('classes', $classes)->with('subjects', $subjects)->with('teacher',$teacher);
         } else {
             return redirect('/admin/login');
         }
     }

     public function loadMappedSubjects(Request $request){
        if(Auth::check()){
            $teacher_id = $request->teacher_id;
            $subjects = SubjectMapping::where('teacher_id',$teacher_id)->where('status','ACTIVE')->get();
                //   if($subjects->isNotEmpty()){
                    $subjects->toArray();
                    $html = view('admin.loadmappedsubjects')->with('subjects',$subjects)->render();
                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Mapped Subject List']);
                // }  else {
                //     return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Not a valid section']);
                // }
          
            // return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
        }else{
            return redirect('/login');
        }
     }

     public function deleteMappedSubject(Request $request){
        if(Auth::check()){
            $teacher_id = $request->teacher_id;
            $id = $request->id;
            $subjects = SubjectMapping::where('teacher_id',$teacher_id)->where('status','ACTIVE')->where('id',$id)->update(['status'=>'INACTIVE']);
              return response()->json(['status' => 'SUCCESS', 'data' => [], 'message' => 'Deleted Successfully..!']);
              
        }else{
            return redirect('/login');
        }
     }
     // Promotions

     public function viewStudentPromotions(Request $request) {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            $students  = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                ->where('users.status', 'ACTIVE')->where('user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->id)
                ->select('users.id', 'name', 'last_name', 'students.admission_no')->orderby('students.admission_no', 'Asc')->get();
            return view('admin.student_promotions')->with('classes', $classes);

      
        } else {
            return redirect('/admin/login');
        }
    }


    public function loadStudentPromotions(Request $request)   {
        if(Auth::check()){
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $subject = $request->get('subject', '0');
            $status = $request->get('status','0');
               $class_id = $request->get('class_id','');
               $section_id = $request->get('section_id','');

               
 
                $students_qry =  User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('students', 'students.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'students.class_id')
                ->leftjoin('sections', 'sections.id', 'students.section_id')
                ->where('user_type', 'STUDENT')
                ->where('students.class_id',$class_id)
                ->where('students.section_id',$section_id)
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'students.roll_no', 'students.admission_no', 'students.class_id', 'students.section_id', 'students.father_name',
                'students.address', 'classes.class_name', 'sections.section_name');

                $filteredqry =  User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('students', 'students.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'students.class_id')
                ->leftjoin('sections', 'sections.id', 'students.section_id')
                ->where('user_type', 'STUDENT')
                ->where('students.class_id',$class_id)
                ->where('students.section_id',$section_id)
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'students.roll_no', 'students.admission_no', 'students.class_id', 'students.section_id', 'students.father_name',
                'students.address', 'classes.class_name', 'sections.section_name');


                if (count($columns) > 0) {
                    foreach ($columns as $key => $value) {
                        if (!empty($value['name']) && !empty($value['search']['value'])) {
                            if ($value['name'] == 'sections.status') {
                                $students_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            } else {
                                $students_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            }
                        }
                    }
                }
    
                if (!empty($order)) {
                    $orderby = $columns[$order]['name'];
                } else {
                    $orderby = 'users.id';
                }
                if (empty($dir)) {
                    $dir = 'ASC';
                }
    
                $students = $students_qry->skip($start)->take($length)->orderby($orderby, $dir)->get();
                $totalData = $students_qry->select('users.id')->count();
                if (!empty($students)) {
                    $totalData = count($students);
                }
                $totalfiltered = $totalData;
                $filtered = $filteredqry->get()->toArray();
                if (!empty($filtered)) {
                    $totalfiltered = count($filtered);
                }

                $data = [];
            if (!empty($students)) {
                $students = $students->toArray();
                foreach ($students as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        }else{
            return redirect('/login');
        }
    }

    public function updateStudentPromotions(Request $request){
        $err = '';  $qb = [];
        if (Auth::check()) {
            $input = $request->all();
            $qbid = $request->get('qbid', []);
            $class_id = $request->get('class_id','');
            $section_id = $request->get('section_id','');
            $from_class_id = $request->get('from_class_id','');
            $from_section_id = $request->get('from_section_id','');
            // echo "<pre>";print_r($qbid);
            // exit;
            // if(is_array($qbid) && count($qbid)>0) {
            $qb = Student::leftjoin('users','users.id','students.user_id')->where('students.class_id', $from_class_id)->where('students.section_id',$from_section_id)->where('users.status','=','ACTIVE')->get();
            $academic_year = date('Y');
            $final_year = date('Y') + 1;
            $cur_month = date('m');
            $from_month =  $academic_year.'-06';
            $to_month = $final_year.'-04';
            $check_month =  $academic_year.'-'.$cur_month;
           
            foreach($qbid as $k=> $v){

                $user_id = $v;
           
                $check_acadamic =  DB::table('student_class_mappings')->whereRaw("'".$check_month."' BETWEEN from_month and to_month")->where('user_id', $user_id)->orderby('id','desc')->first();
                if(empty($check_acadamic)){
                 $class_mapping = DB::table('student_class_mappings')->where('academic_year', $academic_year)
                        ->where('user_id', $user_id)->orderby('id','desc')->first();
                 
                  $status = 'ACTIVE';
                if (!empty($class_mapping)) {
                    $id = $class_mapping->id;
                    $academics = StudentAcademics::find($id);
                    $academics->updated_at = date('Y-m-d H:i:s');
                    $academics->updated_by = Auth::User()->id;
                    $academics->school_id = Auth::User()->id;
                    $academics->user_id = $user_id;
                    $academics->academic_year = $academic_year;
                    $academics->from_month = $from_month;
                    $academics->to_month = $to_month;
                    $academics->class_id = $class_id;
                    $academics->section_id = $section_id;
                    $academics->status = $status;
                    $academics->save();
                    
                 
                } else {
                    $academics = new StudentAcademics();
                    $academics->created_at = date('Y-m-d H:i:s');
                    $academics->created_by = Auth::User()->id;
                    $academics->school_id = Auth::User()->id;
                    $academics->user_id = $user_id;
                    $academics->academic_year = $academic_year;
                    $academics->from_month = $from_month;
                    $academics->to_month = $to_month;
                    $academics->class_id = $class_id;
                    $academics->section_id = $section_id;
                    $academics->status = $status;
                    $academics->save();
                }
                DB::table('students')
                ->where('user_id', $user_id)
                ->update(['class_id'=>$class_id, 'section_id' => $section_id,'updated_at'=>date('Y-m-d H:i:s')]);
            }
            else{
                return response()->json(['status' => 'FAILED', 'message' => 'Student`s Acadamic Year Was Not Disclosed']);
            }
            }
            return response()->json(['status' => 'SUCCESS', 'message' => 'Class Promoted Successfully']);
       
       
        } else {
            return redirect('/admin/login');
        }
    }
    //Slot
    public function viewSLot()
    {
        if (Auth::check()) {
            return view('admin.slot');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getSlot(Request $request)
    {

        if (Auth::check()) {

            $status = $request->get('status',0);


            if($status != ''){
             $slot = Slot::all()->where('status','=',$status);

            }else{
                $slot = Slot::all();
            }
            return Datatables::of($slot)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postSlot(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $slot_name = $request->slot_name;
            $from_time = $request->from_time;
            $to_time = $request->to_time;
            $position = $request->position;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'slot_name' => 'required',
                'from_time' => 'required',
                'to_time' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $slot = Slot::find($id);
            } else {
                $slot = new Slot;
            }

            $slot->slot_name = $slot_name;
            $slot->from_time = $from_time;
            $slot->to_time = $to_time;
            $slot->position = $position;
            $slot->status = $status;

            $slot->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Slot Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editSlot(Request $request)
    {

        if (Auth::check()) {
            $slot = Slot::where('id', $request->id)->get();

            if ($slot->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $slot[0], 'message' => 'Slot Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Slot Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Subjects
    public function viewSubjects()
    {
        if (Auth::check()) {

            return view('admin.subjects');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getSubjects(Request $request)
    {

        if (Auth::check()) {
            $status = $request->get('status',0);
           if($status != ''){
            $subjects = Subjects::where('status','=',$status)->where('school_id','=',Auth::User()->id)->get();
           }else{
            $subjects = Subjects::all();
           }


            return Datatables::of($subjects)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postSubjects(Request $request)
    {
        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $id = $request->id;
            $subject_name = $request->subject_name;
            $subject_colorcode = $request->subject_colorcode;
            $position = $request->position;
            $status = $request->status;
            $image = $request->file('subject_image');

            $validator = Validator::make($request->all(), [
                'subject_name' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($id > 0) {
                $exists = DB::table('subjects')->where('subject_name', $subject_name)->where('school_id','=',Auth::User()->id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('subjects')->where('subject_name', $subject_name)->where('school_id','=',Auth::User()->id)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Subject Name Already Exists.",
                ]);
            }


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $subject = Subjects::find($id);
            } else {
                $subject = new Subjects;
            }
            $subject->school_id = $school_id;
            $subject->subject_name = $subject_name;
            $subject->subject_colorcode = $subject_colorcode;
            $subject->position = $position;
            $subject->status = $status;

            if (!empty($image)) {

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/subjects/');

                $image->move($destinationPath, $countryimg);

                $subject->subject_image = $countryimg;

            }

            $subject->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Subject Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editSubjects(Request $request)
    {

        if (Auth::check()) {
            $subjects = Subjects::where('id', $request->id)->get();

            if ($subjects->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $subjects[0], 'message' => 'Subjects Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Subjects Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Home Work
    public function viewHomework()
    {
        if (Auth::check()) {

            $classes = Classes::where('status','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $subjects = Subjects::where('status','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $subjects->where('school_id', Auth::User()->id); 
            }
            $subjects = $subjects->orderby('position','asc')->get();
             $tests = Tests::all();
            $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8');
            if(Auth::User()->user_type == 'SCHOOL') {
                $periods->where('school_id', Auth::User()->id); 
            }
            $periods = $periods->first()->toArray();
            return view('admin.homework')->with('classes', $classes)->with('subjects', $subjects)->with('periods', $periods)->with('tests',$tests);
        } else {
            return redirect('/admin/login');
        }
    }



    public function getHomework(Request $request)
    {

        if (Auth::check()) {

            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column'); 

            $status = $request->get('status','');
            $test_id = $request->get('test_id','0');
            $classid = $request->get('classid','0');
            $sectionid = $request->get('sectionid','0');
            $subjectid = $request->get('subjectid','0');

            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $hwqry = Homeworks::leftjoin('classes', 'classes.id', 'homeworks.class_id')
                ->leftjoin('sections', 'sections.id', 'homeworks.section_id')
                ->leftjoin('subjects', 'subjects.id', 'homeworks.subject_id')
                ->select('homeworks.*');
            $filteredqry = Homeworks::leftjoin('classes', 'classes.id', 'homeworks.class_id')
                ->leftjoin('sections', 'sections.id', 'homeworks.section_id')
                ->leftjoin('subjects', 'subjects.id', 'homeworks.subject_id')
                ->select('homeworks.*');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'homeworks.status') {
                            $hwqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $hwqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $hwqry->where('homeworks.school_id', Auth::User()->id);
                $filteredqry->where('homeworks.school_id', Auth::User()->id);
            }

            if(!empty($status)){
                $hwqry->where('homeworks.status',$status);
                $filteredqry->where('homeworks.status',$status);
            }

            if($test_id != '' || $test_id != 0){ 
                $hwqry->where('homeworks.test_id','like','%'.$test_id.'%');
                $filteredqry->where('homeworks.test_id','like','%'.$test_id.'%'); 
            }

            if($classid > 0){ 
                $hwqry->where('homeworks.class_id',$classid);
                $filteredqry->where('homeworks.class_id',$classid);
            }

            if($sectionid > 0){ 
                $hwqry->where('homeworks.section_id',$sectionid);
                $filteredqry->where('homeworks.section_id',$sectionid);
            }

            if($subjectid > 0){ 
                $hwqry->where('homeworks.subject_id',$subjectid);
                $filteredqry->where('homeworks.subject_id',$subjectid);
            } 

            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $hwqry->where('homeworks.hw_date', '>=', $mindate);
                $filteredqry->where('homeworks.hw_date', '>=', $mindate);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                $hwqry->where('homeworks.hw_date', '<=', $maxdate);
                $filteredqry->where('homeworks.hw_date', '<=', $maxdate);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            } 


            $hws = $hwqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Homeworks::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($hws)) {
                foreach ($hws as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data); 
        } else {
            return redirect('/admin/login');
        }

    }

    public function postHomework(Request $request)
    {

        if (Auth::check()) {
            $id = $request->id;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $subject_id = $request->subject_id;
            // $period = $request->period;
            $hw_title = $request->hw_title;
            $hw_description = $request->hw_description;
            $hw_date = $request->hw_date;
            $hw_submission_date = $request->hw_submission_date;
            $position = $request->position;
            $status = $request->status;
            $test_id = $request->test_id;
            if(!empty($test_id)){
                $test = implode(',', $test_id);
            }else{
                $test = '';
            } 
         

            $is_hw_attachment = $request->is_hw_attachment;
            $is_dt_attachment = $request->is_dt_attachment;

            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'section_id' => 'required',
                'subject_id' => 'required',
                // 'period' => 'required',
                'hw_title' => 'required',
                'hw_description' => 'required',
                'hw_date' => 'required',
                'hw_submission_date' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs",
                ]);
            }
         
            if(date('Y-m-d',strtotime($hw_submission_date)) > date('Y-m-d',strtotime($hw_date))){

            if ($id > 0) {
                $homework = Homeworks::find($id);
                $homework->updated_at = date('Y-m-d H:i:s');
            } else {
                $homework = new Homeworks();
                $homework->created_at = date('Y-m-d H:i:s');
            }
            $homework->school_id = Auth::User()->id;
            $homework->class_id = $class_id;
            $homework->section_id = $section_id;
            $homework->subject_id = $subject_id;
            // $homework->period = $period;
            $homework->test_id = $test;
            $homework->hw_title = $hw_title;
            $homework->hw_description = $hw_description;
            $homework->hw_date = $hw_date;
            $homework->hw_submission_date = $hw_submission_date;
            $homework->position = $position;
            $homework->status = $status;

            $homeworkfile = $request->file('hw_attachment');
            if (!empty($homeworkfile)) {

                $ext = $homeworkfile->getClientOriginalExtension();
                if (!in_array($ext, $this->accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                }

                $topicimg = rand() . time() . '.' . $homeworkfile->getClientOriginalExtension();

                $destinationPath = public_path('/image/homework');

                $homeworkfile->move($destinationPath, $topicimg);

                $homework->hw_attachment = $topicimg;
            }

            $dailytask = $request->file('dt_attachment');
            if (!empty($dailytask)) {

                $ext = $dailytask->getClientOriginalExtension();
                if (!in_array($ext, $this->accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                }

                $topicimg = rand() . time() . '.' . $dailytask->getClientOriginalExtension();

                $destinationPath = public_path('/image/dailytask');

                $dailytask->move($destinationPath, $topicimg);

                $homework->dt_attachment = $topicimg;
            }

            $homework->save();

            if(!empty($test_id) && count($test_id)>0) {
                foreach($test_id as $tid) {
                    $test_to_date = DB::table('tests')->where('id', $tid)->value('to_date');
                    $hw_submission_date = date('Y-m-d', strtotime($hw_submission_date));
                    if(strtotime($test_to_date) < strtotime($hw_submission_date)) {
                        DB::table('tests')->where('id', $tid)->update(['to_date'=>$hw_submission_date, 
                        'updated_by'=>Auth::User()->id, 'updated_at'=>date('Y-m-d H:i:s')]);
                    }
                }
            }


            $subject_name = DB::table('subjects')->where('id', $subject_id)->value('subject_name');

            $students  = DB::table('students')->leftjoin('users', 'users.id', 'students.user_id')
                ->where('students.class_id', $class_id)->where('students.section_id', $section_id)
                ->where('users.status', 'ACTIVE')->where('users.user_type', 'STUDENT')
                ->select('users.id')->groupby('users.id')->get();

            if($students->isNotEmpty()) {
                foreach($students as $stud) {
                    $type_no = 2;
                    $title = $hw_title;
                    $message = 'Homework given in '.$subject_name;
                    $fcmMsg = array("fcm" => array("notification" => array(
                        "title" => $title,
                        "body" => $message,
                        "type" => $type_no,
                      )));

                    CommonController::push_notification($stud->id, $type_no, $homework->id, $fcmMsg);
                }
            } 
                            

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Home Work  Saved Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Homework Submission Date must be greater than a Homework assigned Date..!',
            ]);
        }
        } else {
            return redirect('/admin/login');
        }
    }

    public function editHomework(Request $request)
    {

        if (Auth::check()) {
            $homework = Homeworks::where('id', $request->id)->get();

            if ($homework->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $homework[0], 'message' => 'Home Work Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Home Work Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Chapters
    public function viewChapters()
    {
        if (Auth::check()) {
            $classes = Classes::where('status','ACTIVE')->orderby('position','asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $subjects = Subjects::all(); 
            return view('admin.chapters')->with('classes', $classes)->with('subjects', $subjects);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getChapters(Request $request)
    {

        if (Auth::check()) {

            /*$status = $request->get('status','0');
            $class_id = $request->get('class_id','0');
            $subject_id = $request->get('subject_id','0');
            $term_id = $request->get('term_id','0');
            /*if($status == ''){
                $chapters = Chapters::all();
            }else{
                $chapters = Chapters::all()->where('status','=',$status);
            }* /
            $chapters = Chapters::where('id', '>', 0);
            if(!empty($status)){
                $chapters->where('status','=',$status);
            }   

            if($class_id>0){
                $chapters->where('class_id','=',$class_id);
            }
            if($subject_id>0){
                $chapters->where('subject_id','=',$subject_id);
            }
            if($term_id>0){
                $chapters->where('term_id','=',$term_id);
            }
            $chapters = $chapters->all();
            return Datatables::of($chapters)->make(true);*/

            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id','0');
            $subject_id = $request->get('subject_id','0');
            $term_id = $request->get('term_id','0');
            
            $chaptersqry = Chapters::where('id', '>', 0);
            $filteredqry = Chapters::where('id', '>', 0);

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $chaptersqry->where('school_id', Auth::User()->id);
                $filteredqry->where('school_id', Auth::User()->id);
            }

            if(!empty($status)){
                $chaptersqry->where('status',$status);
                $filteredqry->where('status',$status);
            }
            if($class_id>0){
                $chaptersqry->where('class_id','=',$class_id);
                $filteredqry->where('class_id',$status);
            }
            if($subject_id>0){
                $chaptersqry->where('subject_id','=',$subject_id);
                $filteredqry->where('subject_id',$subject_id);
            }
            if($term_id>0){
                $chaptersqry->where('term_id','=',$term_id);
                $filteredqry->where('term_id',$term_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            } 


            $chapters = $chaptersqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Chapters::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($chapters)) {
                foreach ($chapters as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postChapters(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_id = $request->class_id;
            $subject_id = $request->subject_id;
            $chaptername = $request->chaptername;  
            $position = $request->position;
            $term_id = $request->term_id;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'subject_id' => 'required',
                'chaptername' => 'required',
                'position' => 'required',
                'status' => 'required',
                'term_id' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs",
                ]);
            }

            if ($id > 0) {
                $exchapter = Chapters::where('id', '!=', $id)->where('class_id',$class_id)->where('subject_id', $subject_id)
                    ->where('term_id', $term_id)
                    ->whereRAW('LOWER(chaptername) = "' . strtolower($chaptername) . '"')->first();

                if (!empty($exchapter)) {
                    return response()->json([
                        'status' => "FAILED",
                        'message' => "Chapter Name Already Exists.",
                    ]);
                }
            } else {
                if(is_array($chaptername) && count($chaptername)>0) {
                    $chaptername = array_unique($chaptername);
                    $chaptername = array_filter($chaptername);

                    foreach($chaptername as $chp){
                        $exchapter = Chapters::whereRAW('LOWER(chaptername) = "' . strtolower($chp) . '"')
                            ->where('class_id',$class_id)->where('subject_id', $subject_id)
                            ->where('term_id', $term_id)->first();

                        if (!empty($exchapter)) {
                            return response()->json([
                                'status' => "FAILED",
                                'message' => "Chapter Name ".$chp." Already Exists.",
                            ]);
                        }
                    }
                    
                }   else {
                    return response()->json([
                        'status' => "FAILED",
                        'message' => "Please enter Chapter Name.",
                    ]);
                }
                
            }

            if ($id > 0) {
                $chapter = Chapters::find($id);
                $chapter->updated_at = date('Y-m-d H:i:s'); 
                $chapter->school_id = Auth::User()->id;
                $chapter->class_id = $class_id;
                $chapter->subject_id = $subject_id; 
                $chapter->term_id = $term_id; 
                $chapter->chaptername = $chaptername; 
                $chapter->position = $position;
                $chapter->status = $status;

                $chapter->save();

            } else {
                if(is_array($chaptername) && count($chaptername)>0){
                    foreach($chaptername as $ck=>$chp){
                        $chapter = new Chapters();
                        $chapter->created_at = date('Y-m-d H:i:s');

                        // Last Order id
                        $lastorderid = DB::table('chapters')
                            ->orderby('id', 'desc')->select('id')->limit(1)->get();

                        if ($lastorderid->isNotEmpty()) {
                            $lastorderid = $lastorderid[0]->id;
                            $lastorderid = $lastorderid + 1;
                        } else {
                            $lastorderid = 1;
                        }

                        $append = str_pad($lastorderid, 3, "0", STR_PAD_LEFT);

                        $chapter->ref_code = CommonController::$code_prefix . 'CH' . $append; 
                        $chapter->school_id = Auth::User()->id;
                        $chapter->class_id = $class_id;
                        $chapter->subject_id = $subject_id; 
                        $chapter->term_id = $term_id; 
                        $chapter->chaptername = $chp; 
                        $chapter->position = $position[$ck];
                        $chapter->status = $status;

                        $chapter->save();
                    }
                }
            }
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Chapter Saved Successfully',
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editChapters(Request $request)
    {
        if (Auth::check()) {
            $chapter = Chapters::where('id', $request->code)->get();
            if ($chapter->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $chapter[0],
                    'message' => 'Chapter Detail',
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Chapter Detail',
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function fetchChapters(Request $request)
    {
        $class_id = $request->get('class_id', 0);
        $term_id = $request->get('term_id', 0);
        $chapter = Chapters::where("subject_id", $request->subject_id)->where('status','ACTIVE');
        if($class_id > 0) {
            $chapter->where("class_id", $class_id);
        }
        if($term_id > 0) {
            $chapter->where("term_id", $term_id);
        }
        $data['chapter'] = $chapter->get(["chaptername", "id"]);
        return response()->json($data);
    }

    public function fetchChaptersTopics(Request $request)
    {

        $data['chapter'] = ChapterTopics::where("chapter_id", $request->chapter_id)->where('status','ACTIVE')
            ->get(["chapter_topic_name", "id"]);

        return response()->json($data);
    }

    public function viewChaptersTopics()
    {
        if (Auth::check()) {
            $classes = Classes::where('status','ACTIVE')->orderby('position','asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $subjects = Subjects::where('status','ACTIVE')->orderby('position','asc')->get();
            return view('admin.chapterstopics')->with('classes', $classes)->with('subjects', $subjects);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getChapterTopics(Request $request)
    {
        if (Auth::check()) {

            /*$status = $request->get('status','');

            $chapter_topicsqry = ChapterTopics::leftjoin('subjects', 'subjects.id', 'chapter_topics.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'chapter_topics.chapter_id')
                ->select('chapter_topics.*', 'subjects.subject_name', 'chapters.chaptername');
                if($status != ''){
                    $chapter_topicsqry->where('chapter_topics.status',$status);
                }
                $chapter_topics = $chapter_topicsqry->get();
            return Datatables::of($chapter_topics)->make(true);*/

            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id','0');
            $subject_id = $request->get('subject_id','0');
            $term_id = $request->get('term_id','0');
            
            $chaptersqry = ChapterTopics::leftjoin('chapters', 'chapters.id', 'chapter_topics.chapter_id')->where('chapter_topics.id', '>', 0)->select('chapter_topics.*', 'chapters.chaptername');
            $filteredqry = ChapterTopics::leftjoin('chapters', 'chapters.id', 'chapter_topics.chapter_id')->where('chapter_topics.id', '>', 0)->select('chapter_topics.*', 'chapters.chaptername');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $chaptersqry->where('chapter_topics.school_id', Auth::User()->id);
                $filteredqry->where('chapter_topics.school_id', Auth::User()->id);
            }

            if(!empty($status)){
                $chaptersqry->where('chapter_topics.status',$status);
                $filteredqry->where('chapter_topics.status',$status);
            }
            if($class_id>0){
                $chaptersqry->where('chapter_topics.class_id','=',$class_id);
                $filteredqry->where('chapter_topics.class_id',$status);
            }
            if($subject_id>0){
                $chaptersqry->where('chapter_topics.subject_id','=',$subject_id);
                $filteredqry->where('chapter_topics.subject_id',$subject_id);
            }
            if($term_id>0){
                $chaptersqry->where('chapter_topics.term_id','=',$term_id);
                $filteredqry->where('chapter_topics.term_id',$term_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'chapter_topics.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }




            $chapters = $chaptersqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('chapter_topics.id')->count();

            $totalDataqry = ChapterTopics::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('chapter_topics.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($chapters)) {
                foreach ($chapters as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    public function postChapterTopics(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_id = $request->class_id;
            $subject_id = $request->subject_id;
            $chapter_id = $request->chapter_id;
            $term_id = $request->term_id;
            $chapter_topic_name = $request->chapter_topic_name;
            $position = $request->position;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'subject_id' => 'required',
                'chapter_id' => 'required',
                'term_id' => 'required',
                'chapter_topic_name' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs".implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exchapter = ChapterTopics::where('id', '!=', $id)->where('chapter_id', $chapter_id)
                    ->where('term_id', $term_id)
                    ->whereRAW('LOWER(chapter_topic_name) = "' . strtolower($chapter_topic_name) . '"')->first();
            } else {
                $exchapter = ChapterTopics::whereRAW('LOWER(chapter_topic_name) = "' . strtolower($chapter_topic_name) . '"')
                    ->where('term_id', $term_id)
                    ->where('chapter_id', $chapter_id)->first();
            }

            if (!empty($exchapter)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Topic Name Already Exists.",
                ]);
            }

            if ($id > 0) {
                $chapter = ChapterTopics::find($id);
                $chapter->updated_at = date('Y-m-d H:i:s');
            } else {
                $chapter = new ChapterTopics();
                $chapter->created_at = date('Y-m-d H:i:s');

                // Last Order id
                $lastorderid = DB::table('chapter_topics')
                    ->orderby('id', 'desc')->select('id')->limit(1)->get();

                if ($lastorderid->isNotEmpty()) {
                    $lastorderid = $lastorderid[0]->id;
                    $lastorderid = $lastorderid + 1;
                } else {
                    $lastorderid = 1;
                }

                $append = str_pad($lastorderid, 3, "0", STR_PAD_LEFT);

                $chapter->ref_code = CommonController::$code_prefix . 'CHT' . $append;
            }
            $chapter->school_id = Auth::User()->id;
            $chapter->class_id = $class_id;
            $chapter->subject_id = $subject_id;
            $chapter->chapter_id = $chapter_id;
            $chapter->term_id = $term_id;
            $chapter->chapter_topic_name = $chapter_topic_name;
            $chapter->position = $position;
            $chapter->status = $status;
            $chapter->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Topic Saved Successfully',
            ]);
        } else {
            return redirect('/admin/login');
        }

    }

    public function editChapterTopics(Request $request)
    {
        if (Auth::check()) {
            $chapter = ChapterTopics::where('id', $request->code)->get();
            if ($chapter->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $chapter[0],
                    'message' => 'Topic Detail',
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Topic Detail',
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function viewSingleChapter()
    {

        if (Auth::check()) {
            $classes = Classes::where('status','ACTIVE')->orderby('position','asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $subjects = Subjects::all();
            return view('admin.topics')->with('classes', $classes)->with('subjects', $subjects);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getTopics(Request $request)
    {
        if (Auth::check()) {

            /*$status = $request->get('status','');
            $topics_qry = Topics::leftjoin('subjects', 'subjects.id', 'topics.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'topics.chapter_id')
                ->leftjoin('chapter_topics', 'chapter_topics.id', 'topics.topic_id')
                ->where('topics.id', '>', 0);
                if($status != '' || $status != 0){
                    $topics_qry->where('topics.status',$status);
                }

            $topics = $topics_qry->select('topics.*', 'subjects.subject_name', 'chapters.chaptername', 'chapter_topics.chapter_topic_name')->get();

            return Datatables::of($topics)->make(true);*/

            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id','0');
            $subject_id = $request->get('subject_id','0');
            $term_id = $request->get('term_id','0');
            
            $chaptersqry = Topics::leftjoin('chapters', 'chapters.id', 'topics.chapter_id')
                ->leftjoin('chapter_topics', 'chapter_topics.id', 'topics.topic_id')
                ->where('topics.id', '>', 0)
                ->select('topics.*', 'chapters.chaptername', 'chapter_topics.chapter_topic_name');
            $filteredqry = Topics::leftjoin('chapters', 'chapters.id', 'topics.chapter_id')
                ->leftjoin('chapter_topics', 'chapter_topics.id', 'topics.topic_id')
                ->where('topics.id', '>', 0)
                ->select('topics.*', 'chapters.chaptername', 'chapter_topics.chapter_topic_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $chaptersqry->where('topics.school_id', Auth::User()->id);
                $filteredqry->where('topics.school_id', Auth::User()->id);
            }

            if(!empty($status)){
                $chaptersqry->where('topics.status',$status);
                $filteredqry->where('topics.status',$status);
            }
            if($class_id>0){
                $chaptersqry->where('topics.class_id','=',$class_id);
                $filteredqry->where('topics.class_id',$status);
            }
            if($subject_id>0){
                $chaptersqry->where('topics.subject_id','=',$subject_id);
                $filteredqry->where('topics.subject_id',$subject_id);
            }
            if($term_id>0){
                $chaptersqry->where('topics.term_id','=',$term_id);
                $filteredqry->where('topics.term_id',$term_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'topics.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }




            $chapters = $chaptersqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Topics::orderby('topics.id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('topics.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($chapters)) {
                foreach ($chapters as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: postTopics
     * Save into Chapters table
     */
    public function postTopics(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_id = $request->class_id;
            $subject_id = $request->subject_id;
            $chapter_id = $request->chapter_id;
            $term_id = $request->term_id;
            $topic_id = $request->topic_id;
            $topic_title = $request->topic_title;
            $topic_type = $request->topic_type;

            $video_link = $request->video_link;
            $position = $request->position;
            $status = $request->status;
            $is_recommended = $request->get('is_recommended', 0);

            $is_topic_file = $request->is_topic_file;

            if($chapter_id > 0) {} else { $chapter_id = 0; }
            if($topic_id > 0) {} else { $topic_id = 0; }

            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'term_id' => 'required',
                // 'topic_type' => 'required',
                'topic_title' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs",
                ]);
            }

            // if ($topic_type == 'DOC') {
            //     $image = $request->file('topic_file');
            //     if (empty($image) && empty($is_topic_file)) {
            //         return response()->json([
            //             'status' => "FAILED",
            //             'message' => "Please Upload the File",
            //         ]);
            //     }
            // } else if ($topic_type == 'VIDEO') {
            //     $image = $request->file('topic_file');

            //     if (empty($image) && empty($video_link) && empty($is_topic_file)) {
            //         return response()->json([
            //             'status' => "FAILED",
            //             'message' => "Please Upload the File or Enter the Youtube Video Token",
            //         ]);
            //     }
            // }

            if ($id > 0) { 
                $extopic = Topics::where('id', '!=', $id)->where('class_id', $class_id)
                    ->where('term_id', $term_id)
                    ->whereRAW('LOWER(topic_title) = "' . strtolower($topic_title) . '"')->where('subject_id', $subject_id)->first();
            } else {
                $extopic = Topics::whereRAW('LOWER(topic_title) = "' . strtolower($topic_title) . '"')
                    ->where('term_id', $term_id)
                    ->where('class_id', $class_id)->where('subject_id', $subject_id)->first();
            }

            if (!empty($extopic)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Topic Name Already Exists.",
                ]);
            }

            if ($id > 0) {
                $topics = Topics::find($id);
                $topics->updated_at = date('Y-m-d H:i:s');
            } else {
                $topics = new Topics();
                $topics->created_at = date('Y-m-d H:i:s');

                // Last Order id
                $lastorderid = DB::table('topics')
                    ->orderby('id', 'desc')->select('id')->limit(1)->get();

                if ($lastorderid->isNotEmpty()) {
                    $lastorderid = $lastorderid[0]->id;
                    $lastorderid = $lastorderid + 1;
                } else {
                    $lastorderid = 1;
                }

                $append = str_pad($lastorderid, 3, "0", STR_PAD_LEFT);

                $topics->ref_code = CommonController::$code_prefix . 'TP' . $append;
            }

            $topics->is_free = "Yes";

            $topics->school_id = Auth::User()->id;
            $topics->class_id = $class_id;
            $topics->subject_id = $subject_id;
            $topics->term_id = $term_id;
            $topics->chapter_id = $chapter_id;
            $topics->topic_id = $topic_id;
            $topics->topic_title = $topic_title;
            $topics->topic_type = "PDF";
            $topics->position = $position;

            if (!empty($video_link)) {
                $video_link = "https://www.youtube.com/embed/" . $video_link . "?&autoplay=1";
            }

            $topics->video_link = $video_link;
            $topics->status = $status;
            $topics->is_recommended = $is_recommended;

            $image = $request->file('topic_file');
            if (!empty($image)) {
                $ext = $image->getClientOriginalExtension();
                $accepted_formats = ['pdf'];

              if(!in_array($ext,$accepted_formats)){
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please Upload Pdf Formats Only..!']);
                }
                
                $topicimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/image/topics');

                $image->move($destinationPath, $topicimg);

                $topics->topic_file = $topicimg;
            }

            $topics->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Topics Saved Successfully',
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editTopics(Request $request)
    {
        if (Auth::check()) {
            $topics = Topics::where('id', $request->code)->get();
            if ($topics->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $topics[0],
                    'message' => 'Topics Detail',
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Topics Detail',
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }


     //Students leave
     public function viewStudentLeave()
     {
         if (Auth::check()) {
            // $student = Student::leftjoin('users','users.id','students.user_id')->get('students.user_id','users.name as name');
            $student = User::leftjoin('students', 'students.user_id', 'users.id')
            ->where('users.user_type', 'STUDENT')
            ->where('users.status','ACTIVE')
            ->select('users.*')->get();
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
             $teacher = Teacher::where('user_id', Auth::user()->id)->select('*')->first();
             return view('admin.studentleave')->with('teacher',$teacher)->with('student',$student)->with('class',$classes);

         } else {
             return redirect('/admin/login');
         }
     }

     public function getStudentLeave(Request $request)
     {

         if (Auth::check()) {
            $student_id = $request->get('student_id','');
            $class_id = $request->get('class_id', '');
            $section_id = $request->get('section_id','');
            $input = $request->all();
            $start = $input['start'];
            $limit = $request->get('length', '10');
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

             $users_qry = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->select('leaves.*','classes.class_name','sections.section_name','users.name');
             $filtered_qry = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->select('leaves.*','classes.class_name','sections.section_name','users.name');
 
             if (count($columns) > 0) {
                 foreach ($columns as $key => $value) {
                   
                     if (!empty($value['search']['value']) && !empty($value['name'])) {
                         if ($value['name'] == 'status') {
                           
                             $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                             $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                         } else {
                          
                             $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                             $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                         }
                     }
                 }
             }

             if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

             
             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('leaves.leave_date >= ?', [$mindate]);
                $filtered_qry->whereRaw('leaves.leave_date >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime($maxdate));
                $users_qry->whereRaw('leaves.leave_date <= ?', [$maxdate]);
                $filtered_qry->whereRaw('leaves.leave_date <= ?', [$maxdate]);
            }
             if($student_id != '' || $student_id != 0){
                $users_qry->where('leaves.student_id',$student_id);
                $filtered_qry->where('leaves.student_id',$student_id);
             }
             if($class_id != '' || $class_id != 0){
                $users_qry->where('leaves.class_id',$class_id);
                $filtered_qry->where('leaves.class_id',$class_id);
             }
             if($section_id != '' || $section_id != 0){
                $users_qry->where('leaves.section_id',$section_id);
                $filtered_qry->where('leaves.section_id',$section_id);
             }
 
             if (!empty($order)) {
                 $orderby = $columns[$order]['name'];
             } else {
                 $orderby = 'id';
             }
             if (empty($dir)) {
                 $dir = 'DESC';
             }
 
             $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
             $totalData = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->select('leaves.*','classes.class_name','sections.section_name','users.name');
             if(Auth::User()->user_type == 'SCHOOL') {
                $totalData->where('users.school_college_id', Auth::User()->id); 
             }

             $totalData = $totalData->get();
           
             if (!empty($totalData)) {
                 $totalData = count($totalData);
             }
             $totalfiltered = $totalData;
             $filtered = $filtered_qry->get()->toArray();
             if (!empty($filtered)) {
                 $totalfiltered = count($filtered);
             }
 
             $data = [];
             if (!empty($users)) {
                 $users = $users->toArray();
                 foreach ($users as $post) {
                     $nestedData = [];
                     foreach ($post as $k => $v) {
                         $nestedData[$k] = $v;
                     }
                     $data[] = $nestedData;
                 }
             }
 
             $json_data = array(
                 "draw" => intval($request->input('draw')),
                 "recordsTotal" => intval($totalData),
                 "data" => $data,
                 "recordsFiltered" => intval($totalfiltered),
             );
 
             echo json_encode($json_data);

         } else {
             return redirect('/admin/login');
         }

     }


     public function editStudentLeave(Request $request)
     {
         if (Auth::check()) {
            $id = $request->get('id');
            // $student = Student::leftjoin('users','users.id','students.user_id')->get('students.user_id','users.name as name');
            $leave = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->where('leaves.id',$id)->select('leaves.*','classes.class_name','sections.section_name','users.name')->first();
            // echo "<pre>";print_r($leave);
                return view('admin.editstudentleave')->with('qb',$leave);
             } else {
             return redirect('/admin/login');
         }
     }

     public function updateStudentLeave(Request $request)
     {
        if (Auth::check()) {


            $input = $request->all();
            $leave_id = $request->get('leave_id', 0);
            $status = $request->get('status_id', '');
              
            $edit_leave = DB::table('leaves')->where('id', $leave_id)->update(['status'=>$status]);

            if($status == 'APPROVED') {
                $studentlv = DB::table('leaves')->where('id', $leave_id)->first();
                if(!empty($studentlv)) {
                    $student_id = $studentlv->student_id;
                    $duration = $studentlv->leave_type;
                    $class_id = $studentlv->class_id;
                    $section_id = $studentlv->section_id;
                    $from = $to = ''; $sessiona = [];

                    if($student_id > 0) {

                        if($duration == 'HALF MORNING') {
                            $from = date('Y-m-d', strtotime($studentlv->leave_date)); $to = ''; $sessiona = ['fn'];
                        } else if($duration == 'HALF AFTERNOON') {
                            $from = date('Y-m-d', strtotime($studentlv->leave_date)); $to = ''; $sessiona = ['an'];
                        } else if($duration == 'FULL DAY') {
                            $from = date('Y-m-d', strtotime($studentlv->leave_date)); $to = ''; $sessiona = ['fn', 'an'];
                        } else if($duration == 'MORE THAN ONE DAY') {
                            $from = date('Y-m-d', strtotime($studentlv->leave_date)); 
                            $to = date('Y-m-d', strtotime('+1 day '.$studentlv->leave_end_date)); $sessiona = ['fn', 'an'];
                        } 

                        $days = [];
                        if(!empty($to)) {
                            $period = new \DatePeriod(
                                 new \DateTime($from),
                                 new \DateInterval('P1D'),
                                 new \DateTime($to)
                            );
                            foreach ($period as $key => $value) {
                                $dayname = $value->format('l'); 
                                $dayname = strtolower($dayname);
                                if($dayname != "sunday") {
                                    $days[] = $value->format('Y-m-d');      
                                }
                            }
                        }   else {
                            $days[] = $from;
                        }

                        $mode = 2;
                        if(count($days)>0) {
                            foreach($days as $day) {
                                $orderdate = explode('-', $day);
                                $year = $orderdate[0];
                                $month   = $orderdate[1];
                                $day  = $orderdate[2];
                                $day = $day * 1;
                                $monthyear = $year.'-'.$month; 

                                $ex = DB::table('studentsdaily_attendance')->where('user_id', $student_id)
                                    ->where('monthyear', $monthyear)->where('class_id', $class_id)
                                    ->where('section_id', $section_id)->first();

                                foreach($sessiona as $session) {
                        
                                    if($session == "fn"){
                                        if(!empty($ex)) {
                                            $date = 'day_'.$day;
                                            DB::table('studentsdaily_attendance')->where('user_id', $student_id)
                                                ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                                                ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                                        }   else {
                                            $date = 'day_'.$day;
                                            DB::table('studentsdaily_attendance')->insert([
                                                'user_id'=>$student_id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                                'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                                'created_by'=>Auth::User()->id
                                            ]);
                                        }
                                    }else if($session == "an"){

                                        if(!empty($ex)) {
                                            $date = 'day_'.$day.'_an';
                                            DB::table('studentsdaily_attendance')->where('user_id', $student_id)
                                                ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                                                ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                                        }   else {
                                            $date = 'day_'.$day.'_an';
                                            DB::table('studentsdaily_attendance')->insert([
                                                'user_id'=>$student_id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                                'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                                'created_by'=>Auth::User()->id
                                            ]);
                                        } 
                                    } 
                                }
                            }
                        } 
                    }
                }
            } 
         
            return response()->json(['status' => 'SUCCESS', 'message' => 'Leave Status Updated Successfully']);
         
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
     }


     public function getExcelStudentLeave(Request $request)
     {

         if (Auth::check()) {
            $student_id = $request->get('student_id','');
            $class_id = $request->get('class_id', '');
            $section_id = $request->get('section_id','');


            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

             $users_qry = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->select('leaves.*','classes.class_name','sections.section_name','users.name');
            
             if (count($columns) > 0) {
                 foreach ($columns as $key => $value) {
                     if (!empty($value['search']['value']) && !empty($value['name'])) {
                         if ($value['name'] == 'status') {
                             $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                         } else {
                             $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                           
                         }
                     }
                 }
             }

             
             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('leaves.leave_date >= ?', [$mindate]);
              
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime($maxdate));
                $users_qry->whereRaw('leaves.leave_date <= ?', [$maxdate]);
          }
             if($student_id != '' || $student_id != 0){
                $users_qry->where('leaves.student_id',$student_id);
               
             }
             if($class_id != '' || $class_id != 0){
                $users_qry->where('leaves.class_id',$class_id);
               
             }
             if($section_id != '' || $section_id != 0){
                $users_qry->where('leaves.section_id',$section_id);
               
             }
 
             if (!empty($order)) {
                 $orderby = $columns[$order]['name'];
             } else {
                 $orderby = 'leaves.id';
             }
             if (empty($dir)) {
                 $dir = 'DESC';
             }
 
             $users = $users_qry->orderBy($orderby, $dir)->get();
          
             $teacher_leave_excel = [];

      if (! empty($users)) {
          $i = 1;
          foreach ($users as $rev) {
           $teacher_leave_excel[] = [
                  "S.No" => $i,
                  "Student Name" => $rev->is_student_name,
                  "Class Name" => $rev->is_class_name,
                  "Section Name" => $rev->is_section_name,
                  "Leave Date" => $rev->leave_date,
                  "Leave End Date" => $rev->leave_end_date,
                  "Leave Start Time" => $rev->leave_starttime,
                  "Leave End Time" => $rev->leave_endtime,
                  "Leave Type" => $rev->leave_type,
                  "Leave Reason" => $rev->leave_reason,
                  "Status" => $rev->status,
              ];

              $i++;
          }
      }

      header("Content-Type: text/plain");
      $flag = false;
      foreach ($teacher_leave_excel as $row) {
          if (! $flag) {
              // display field/column names as first row
              echo implode("\t", array_keys($row)) . "\r\n";
              $flag = true;
          }
          echo implode("\t", array_values($row)) . "\r\n";
      }
      exit();

         } else {
             return redirect('/admin/login');
         }

     }
     //teachers leave
     public function viewTeacherLeave()
     {
         if (Auth::check()) {
            $teacher = User::leftjoin('teachers', 'teachers.user_id', 'users.id')
            ->where('users.user_type', 'TEACHER')->where('users.school_college_id', Auth::User()->id)
            ->where('users.status', 'ACTIVE')
            ->select('users.*')->get();
             return view('admin.teacherleave')->with('teacher',$teacher);

         } else {
             return redirect('/admin/login');
         }
     }

     public function getTeacherLeave(Request $request)
     {

         if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];
            $limit = $request->get('length', '10');
            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');

             $teacher_id = $request->get('teacher_id','');
             $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
             $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
             $users_qry = Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->select('teacher_leave.*','users.name');
             $filtered_qry=Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->select('teacher_leave.*','users.name');
           
             if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

             if($teacher_id != '' || $teacher_id != 0){
                $users_qry->where('user_id',$teacher_id);
                $filtered_qry->where('user_id',$teacher_id);
             }

             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('from_date >= ?', [$mindate]);
                $filtered_qry->whereRaw('from_date >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime($maxdate));
                $users_qry->whereRaw('from_date <= ?', [$maxdate]);
                $filtered_qry->whereRaw('from_date <= ?', [$maxdate]);
            }

            
             
              if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $totalData =Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')
                ->where('users.school_college_id', Auth::User()->id)->select('teacher_leave.*','users.name')->get();
          
            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }

            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
            //  return Datatables::of($teacherleave)->make(true);
         } else {
             return redirect('/admin/login');
         }

     }

     public function editTeacherLeave($id)
     {
         if (Auth::check()) {
          $leave = Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->where('teacher_leave.id',$id)->select('teacher_leave.*','users.name')->first();
            // echo "<pre>";print_r($leave);
                return view('admin.editteacherleave')->with('qb',$leave);
             } else {
             return redirect('/admin/login');
         }
     }

     public function updateTeacherLeave(Request $request)
     {
        if (Auth::check()) {


            $input = $request->all();
            $leave_id = $request->get('leave_id', 0);
            $status = $request->get('status_id', '');
              
            $edit_leave = DB::table('teacher_leave')->where('id', $leave_id)->update(['status'=>$status]);
         
            return response()->json(['status' => 'SUCCESS', 'message' => 'Teacher Leave Status Updated Successfully']);
         
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
     }

     public function getExcelTeacherLeave(Request $request)
     {
        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
             $teacher_id = $request->get('teacher_id','');
             $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
             $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
             $users_qry =  Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->select('teacher_leave.*','users.name');
           if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                     } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                     }
                    }
                }
            }

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

            
             
             if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            // $leads = $users_qry->orderby($orderby, $dir)->get();
            $teacher_leave = $users_qry->get();

               $teacher_leave_excel = [];

        if (! empty($teacher_leave)) {
            $i = 1;
            foreach ($teacher_leave as $rev) {
             $teacher_leave_excel[] = [
                    "S.No" => $i,
                    "Teacher Name" => $rev->is_teacher_name,
                    "Title" => $rev->title,
                    "Duration" => $rev->duration,
                    "From Date" => $rev->is_from_date,
                    "Leave Type" => $rev->leave_type,
                    "Description" => $rev->description,
                    "Status" => $rev->status,
                ];

                $i++;
            }
        }

        header("Content-Type: text/plain");
        $flag = false;
        foreach ($teacher_leave_excel as $row) {
            if (! $flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            echo implode("\t", array_values($row)) . "\r\n";
        }
        exit();

         } else {
             return redirect('/admin/login');
         }

    }

    public function viewOAStudentAttendanceApproval(Request $request)   {
        if(Auth::check()){
            $monthyear = $class_id = $section_id = '';
            $lastdate = date('t', strtotime(date('Y-m')));
            $classes = Classes::where('status', 'ACTIVE')->orderby('position', 'Asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->get();
            $students  = '';
            $cdate = date('Y-m-d');     $monthyear = date('Y-m'); 
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;


            $oa_students = DB::table('student_class_mappings')
                ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->where('student_class_mappings.academic_year', $acadamic_year) 
                ->where('users.delete_status',0)->where('users.status', 'ACTIVE')
                ->where('users.school_college_id', Auth::User()->id)
                ->select('users.id')->count();

            $oa_boys = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year) 
            ->where('users.status', 'ACTIVE')->where('users.gender', 'MALE')->where('users.delete_status',0)
            ->where('users.school_college_id', Auth::User()->id)
            ->select('users.id')->count();  

            $oa_girls = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year) 
            ->where('users.status', 'ACTIVE')->where('users.gender', 'FEMALE')->where('users.delete_status',0)
            ->where('users.school_college_id', Auth::User()->id)
            ->select('users.id')->count(); 
 
            $cday = 'day_'.date('j');   $cday_an = 'day_'.date('j').'_an';

            $att_bp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday, 1)->where('user_type', 'STUDENT')
                ->where('users.school_college_id', Auth::User()->id)
                ->where('users.delete_status',0)->where('gender', 'MALE') 
                ->select('users.id')->count();
            $att_bp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday_an, 1)->where('user_type', 'STUDENT')
                ->where('users.school_college_id', Auth::User()->id)
                ->where('users.delete_status',0)->where('gender', 'MALE') 
                ->select('users.id')->count();

            $att_ba_fn = $oa_boys - $att_bp_fn;
            $att_ba_an = $oa_boys - $att_bp_an;

            $att_gp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday, 1)->where('user_type', 'STUDENT')
                ->where('users.school_college_id', Auth::User()->id)
                ->where('users.delete_status',0)->where('gender', 'FEMALE') 
                ->select('users.id')->count();
            $att_gp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday_an, 1)->where('user_type', 'STUDENT')
                ->where('users.school_college_id', Auth::User()->id)
                ->where('users.delete_status',0)->where('gender', 'FEMALE') 
                ->select('users.id')->count();

            $att_ga_fn = $oa_girls - $att_gp_fn;
            $att_ga_an = $oa_girls - $att_gp_an; 

            $att_oap_fn = $att_bp_fn + $att_gp_fn;
            $att_oap_an = $att_bp_an + $att_gp_an;
            $att_oaa_fn = $att_ba_fn + $att_ga_fn;
            $att_oaa_an = $att_ba_an + $att_ga_an;

            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();

            return view('admin.students_attendance_oa_approval')->with(['monthyear'=>$monthyear, 'cdate'=>$cdate,
                'oa_students'=>$oa_students, 'oa_boys'=>$oa_boys, 'oa_girls'=>$oa_girls, 
                'att_bp_fn'=>$att_bp_fn, 'att_bp_an' => $att_bp_an, 
                'att_ba_fn'=>$att_ba_fn, 'att_ba_an' => $att_ba_an, 
                'att_gp_fn' =>$att_gp_fn, 'att_gp_an'=>$att_gp_an, 
                'att_ga_fn' =>$att_ga_fn, 'att_ga_an'=>$att_ga_an, 
                'att_oap_fn' =>$att_oap_fn, 'att_oap_an'=>$att_oap_an, 
                'att_oaa_fn' =>$att_oaa_fn, 'att_oaa_an'=>$att_oaa_an, 
                'classes'=>$classes, 'cdate'=>$cdate
            ]);
        }else{
            return redirect('/login');
        }
    }

    public function loadOAStudentAttendanceApproval(Request $request) {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = -1; // $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');

            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_dropdown',0);
            $cdate = $request->get('cdate',date('Y-m-d'));  

            //OASections::$acadamic_year = date('Y');
            OASections::$cdate = $cdate;
            $sectionsqry = OASections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('classes.school_id', Auth::User()->id)
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');
            $filteredqry = OASections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('classes.school_id', Auth::User()->id)
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) { 
                        $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                    }
                }
            }

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
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            } 

            $sections = $sectionsqry->orderby($orderby, $dir)->get();//->skip($start)->take($length)
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = DB::table('sections')->leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.id');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $overall = OASections::getOverallAttribute();
            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
                "overall" => $overall
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    public function oaAttendanceApproval(Request $request) {
        if(Auth::check()){
            $attdate = $request->get('attdate', '');
            if(strtotime($attdate)>0) {}
            else {
                return response()->json([ 
                    'status' => "FAILED",
                    'message' => "Please select the Date need to approve Attendance",
                ]);
            }
            $attdate = date('Y-m-d', strtotime($attdate));
            $att_section = $request->get('att_section', []);
            if(empty($att_section)) {
                return response()->json([ 
                    'status' => "FAILED",
                    'message' => "Please select the Sections need to approve Attendance",
                ]);
            }
            if(is_array($att_section) && count($att_section)>0) {
                foreach($att_section as $class_id => $section_arr) {
                    if(is_array($section_arr) && count($section_arr)>0) {
                        foreach($section_arr as $section_id => $arr) {
                            $ex = DB::table('attendance_approval_class_section')->where('class_id', $class_id)
                                ->where('section_id', $section_id)->where('date', $attdate)->first();
                            if(!empty($ex)) {
                                DB::table('attendance_approval_class_section')->where('class_id', $class_id)
                                ->where('section_id', $section_id)->where('date', $attdate)
                                ->update(['admin_status'=>1, 'updated_by'=>Auth::User()->id, 
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            }   else {
                                DB::table('attendance_approval_class_section')
                                    ->insert(['date'=>$attdate, 'class_id'=>$class_id, 
                                        'section_id'=>$section_id, 'admin_status'=>1, 
                                        'created_by'=>Auth::User()->id, 
                                        'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                }
                return response()->json([ 
                    'status' => "SUCCESS",
                    'message' => "Attendance Approved Successfully",
                ]);
            }   else {
                return response()->json([ 
                    'status' => "FAILED",
                    'message' => "Please select the Sections need to approve Attendance",
                ]);
            }
            echo "<pre>"; print_r($att_section); exit;
        }else{
            return redirect('/login');
        }
    }

    public function viewOAStudentAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $class_id = $section_id = '';
            $lastdate = date('t', strtotime(date('Y-m')));
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            $students  = '';
            $cdate = date('Y-m-d');     $monthyear = date('Y-m'); 
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->id)->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;


            $oa_students = DB::table('student_class_mappings')
                ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->where('student_class_mappings.academic_year', $acadamic_year) 
                ->where('users.delete_status',0)->where('users.status', 'ACTIVE')
                ->where('users.school_college_id', Auth::User()->id)
                ->select('users.id')->count();

            $oa_boys = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year) 
            ->where('users.status', 'ACTIVE')->where('users.gender', 'MALE')->where('users.delete_status',0)
            ->where('users.school_college_id', Auth::User()->id)
            ->select('users.id')->count();  

            $oa_girls = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year) 
            ->where('users.status', 'ACTIVE')->where('users.gender', 'FEMALE')->where('users.delete_status',0)
            ->where('users.school_college_id', Auth::User()->id)
            ->select('users.id')->count(); 
 
            $cday = 'day_'.date('j');   $cday_an = 'day_'.date('j').'_an';

            $att_bp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday, 1)->where('user_type', 'STUDENT')
                ->where('users.delete_status',0)->where('gender', 'MALE') 
                ->where('users.school_college_id', Auth::User()->id)
                ->select('users.id')->count();
            $att_bp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday_an, 1)->where('user_type', 'STUDENT')
                ->where('users.delete_status',0)->where('gender', 'MALE') 
                ->where('users.school_college_id', Auth::User()->id)
                ->select('users.id')->count();

            $att_ba_fn = $oa_boys - $att_bp_fn;
            $att_ba_an = $oa_boys - $att_bp_an;

            $att_gp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday, 1)->where('user_type', 'STUDENT')
                ->where('users.delete_status',0)->where('gender', 'FEMALE') 
                ->where('users.school_college_id', Auth::User()->id)
                ->select('users.id')->count();
            $att_gp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
                ->where('monthyear', $monthyear)->where($cday_an, 1)->where('user_type', 'STUDENT')
                ->where('users.delete_status',0)->where('gender', 'FEMALE') 
                ->where('users.school_college_id', Auth::User()->id)
                ->select('users.id')->count();

            $att_ga_fn = $oa_girls - $att_gp_fn;
            $att_ga_an = $oa_girls - $att_gp_an; 

            $att_oap_fn = $att_bp_fn + $att_gp_fn;
            $att_oap_an = $att_bp_an + $att_gp_an;
            $att_oaa_fn = $att_ba_fn + $att_ga_fn;
            $att_oaa_an = $att_ba_an + $att_ga_an;

            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();

            return view('admin.students_attendance_oa')->with(['monthyear'=>$monthyear, 'cdate'=>$cdate,
                'oa_students'=>$oa_students, 'oa_boys'=>$oa_boys, 'oa_girls'=>$oa_girls, 
                'att_bp_fn'=>$att_bp_fn, 'att_bp_an' => $att_bp_an, 
                'att_ba_fn'=>$att_ba_fn, 'att_ba_an' => $att_ba_an, 
                'att_gp_fn' =>$att_gp_fn, 'att_gp_an'=>$att_gp_an, 
                'att_ga_fn' =>$att_ga_fn, 'att_ga_an'=>$att_ga_an, 
                'att_oap_fn' =>$att_oap_fn, 'att_oap_an'=>$att_oap_an, 
                'att_oaa_fn' =>$att_oaa_fn, 'att_oaa_an'=>$att_oaa_an, 
                'classes'=>$classes, 'cdate'=>$cdate
            ]);
        }else{
            return redirect('/login');
        }
    }

    public function loadOAStudentAttendance(Request $request) {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = -1; // $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');

            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_dropdown',0);
            $cdate = $request->get('cdate',date('Y-m-d'));  

            //OASections::$acadamic_year = date('Y');
            OASections::$cdate = $cdate;
            $sectionsqry = OASections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');
            $filteredqry = OASections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) { 
                        $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('classes.school_id', Auth::User()->id);
                $filteredqry->where('classes.school_id', Auth::User()->id);
            }

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
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            } 

            $sections = $sectionsqry->orderby($orderby, $dir)->get();//->skip($start)->take($length)
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = DB::table('sections')->leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.id');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $overall = OASections::getOverallAttribute();
            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
                "overall" => $overall
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }


    //Attendance Management
    /* Function: viewStudentAttendance
     */
    public function viewStudentAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $class_id = $section_id = '';
            $lastdate = date('t', strtotime(date('Y-m')));
            $classes = Classes::where('status', 'ACTIVE')->orderby('position', 'Asc')->get();
            $students  = '';
            $new_date = date('Y-m-d');
            // $date = 'day_'.$day;
            // $attendance_chk = '';
            // $date2 = 'day_'.$day.'_an';
            // $attendance_chk= '';
           
             /*DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                ->where('users.status', 'ACTIVE')->where('user_type', 'STUDENT')
                ->select('users.id', 'name', 'last_name', 'admission_no')->orderby('admission_no', 'Asc')->get();
            */
        return view('admin.students_attendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate, 'students'=>$students,'new_date' => $new_date,'attendance_chk' =>0,'attendance_chk2'=>0]);
        }else{
            return redirect('/login');
        }
    }

    //Attendance Management
    /* Function: loadStudentAttendance
     */
    public function loadStudentAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the Year']);
            }
            $lastdate = date('t', strtotime($monthyear));
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);
            
            if($class_id > 0) {} else { $class_id = 0; }
            if($section_id > 0) {} else { $section_id = 0; }

            if($class_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Class']);
            }
            if($section_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Section']);
            }

            User::$monthyear = $monthyear;
            User::$class_id = $class_id;
            User::$section_id = $section_id;

            $users = DB::select("select student_class_mappings.*, `users`.`id`, `name`, `email`, `mobile`, `students`.`class_id`, `students`.`section_id`, `students`.`admission_no` from `student_class_mappings` left join `users` on `student_class_mappings`.`user_id` = `users`.`id` left join `students` on `students`.`user_id` = `users`.`id` where `user_type` = 'STUDENT' and '".$monthyear."' BETWEEN from_month and to_month and `student_class_mappings`.`class_id` = '".$class_id."' and `student_class_mappings`.`section_id` = '".$section_id."'");
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
                    ->where('user_type', 'STUDENT')
                    ->where('users.status','ACTIVE')
                    ->whereIn('users.id', $userids)
                    ->where('academic_year', $year)
                //    ->whereRaw("'".$monthyear."' BETWEEN from_month and to_month")
                    /* ->where('student_class_mappings.class_id', $class_id)
                    ->where('student_class_mappings.section_id', $section_id)*/
                    ->select('users.id', 'name', 'email', 'mobile', 'students.class_id', 'students.section_id', 'students.admission_no')
                    ->get();
// echo $monthyear;
foreach($students as $k=>$v){
    list($year, $month) = explode('-', $monthyear);
 $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
    $students[$k]->holidays_list = $holidays;
}
                if($students->isNotEmpty()) {
                    $students = $students->toArray();
                //   echo "<pre>";print_r($students);exit;
                    $html = view('admin.loadstudentsattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                    'section_id'=>$section_id, 'students'=>$students, 'lastdate'=>$lastdate])->render();

                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students attendance Detail']);

                }   else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
            }


            return view('admin.studentsattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }


    public function viewEditStudentsAttendance($encid,$monthyear,$class_id,$section_id, Request $request)   {
        if(Auth::check()){
            $student_id = 0;
           $obj = json_decode(base64_decode($encid));
            if(!empty($obj)) {
             $student_id = $obj->id;
            }

         if($student_id > 0) {
                $pl = DB::table('users')->leftjoin('students','students.user_id','users.id')->where('users.id', $student_id)
                ->select('users.id', 'users.name', 'users.email', 'users.mobile','students.class_id','students.section_id')->get();
                if($pl->isNotEmpty()) {
                
                    if(empty($monthyear)) {
                        $monthyear = date('Y-m');
                    }
                    $lastdate = date('t', strtotime($monthyear));
                    User::$monthyear = $monthyear;
                    User::$class_id = $class_id;
                    User::$section_id = $section_id;
                    $players = User::with('attendance')->leftjoin('students','students.user_id','users.id')
                        ->where('users.id', $student_id)
                       ->select('users.id', 'users.name', 'users.email', 'users.mobile','students.class_id','students.section_id')->get();
                       
                       foreach($players as $k=>$v){
                        list($year, $month) = explode('-', $monthyear);
                     $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                        ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                        ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                        $players[$k]->holidays_list = $holidays;
                    }
                    if($players->isNotEmpty()) {
                        $players = $players->toArray();
                   
                    }
                    return view('admin.updateattendance')->with(['players'=>$players, 'monthyear'=>$monthyear,
                        'lastdate'=>$lastdate])->with('class_id',$class_id)->with('section_id',$section_id);
                }  else {
                    return view('admin.updateattendance')->with(['error'=>1]);
                }
            }   else {
                return view('admin.updateattendance')->with(['error'=>1]);
            }
        }else{
            return redirect('/login');
        }
    }

    /* Function: getStudentAcademics
    Datatable Load
     */
    public function getStudentAcademics(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status_id','');

            $sectionsqry = StudentAcademics::leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->leftjoin('students', 'students.user_id', 'student_class_mappings.user_id')
                ->leftjoin('classes', 'classes.id', 'student_class_mappings.class_id')
                ->leftjoin('sections', 'sections.id', 'student_class_mappings.section_id')
                ->select('student_class_mappings.*', 'users.name', 'users.last_name', 'students.admission_no', 'classes.class_name', 'sections.section_name');
            $filteredqry = StudentAcademics::leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->leftjoin('students', 'students.user_id', 'student_class_mappings.user_id')
                ->leftjoin('classes', 'classes.id', 'student_class_mappings.class_id')
                ->leftjoin('sections', 'sections.id', 'student_class_mappings.section_id')
                ->select('student_class_mappings.*', 'users.name', 'users.last_name', 'students.admission_no', 'classes.class_name', 'sections.section_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'student_class_mappings.status') {
                            $sectionsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(!empty($status)){
                $sectionsqry->where('student_class_mappings.status',$status);
                $filteredqry->where('student_class_mappings.status',$status);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'students.admission_no';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $circulars = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->count();

            $totalDataqry = StudentAcademics::orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($circulars)) {
                foreach ($circulars as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

     /* Function: updateStudentAttendance
        Save into users table for the Students class n section
     */
    public function updateStudentAttendance(Request $request)
    {
        if(Auth::check()){
           $student_id = $request->student_id;
            $mode = $request->mode;
            $day = $request->day;
            $monthyear = $request->monthyear;
            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_id',0);

            // $validator = Validator::make($request->all(), [
            //     'student_id' => 'required',
            //     'mode' => 'required',
            //     'day' => 'required',
            //     'monthyear' => 'required',
            //     'class_id' => 'required',
            //     'section_id' => 'required',
            // ]);

            // if ($validator->fails()) {

            //     $msg = $validator->errors()->all();

            //     return response()->json([
            //         'status' => 0,
            //         'message' => implode(', ', $msg)
            //     ]);
            // }

            if($student_id > 0) {

                $ex = DB::table('students_attendance')->where('user_id', $student_id)
                    ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();

                if(!empty($ex)) {
                    $date = 'day_'.$day;
                    DB::table('students_attendance')->where('user_id', $student_id)
                        ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day;
                    DB::table('students_attendance')->insert([
                        'user_id'=>$student_id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
            }

            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }

    }

    public function viewStudentDailyAttendance(Request $request)   {
        if(Auth::check()){

            $monthyear = $class_id = $section_id = '';
            $students  = '';

            $date = $request->get('date', '');
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);

            if(empty(trim($date))) {
                $lastdate = date('t', strtotime(date('Y-m')));
                $new_date = date('Y-m-d');
                $monthyear = date('Y-m');
            } else {
                $lastdate = date('t', strtotime(date('Y-m', strtotime($date))));  
                $new_date = date('Y-m-d', strtotime($date));
                $monthyear = date('Y-m', strtotime($date));
            }
            list($year, $month) = explode('-', $monthyear);
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            $sundays = CommonController::getSundays($year, $month); 
            $saturdays = CommonController::getSaturdays($year, $month); 
            $holidays = DB::table('holidays')->where('school_college_id', Auth::User()->id)->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
            
            if($holidays->isNotEmpty()){
                $holidays = $holidays->toArray();
            } $appstatus = '';
            return view('admin.students_dailyattendance')->with(['date'=>$date, 'monthyear'=>$monthyear, 'class_id'=>$class_id,'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate, 'students'=>$students,'new_date' => $new_date,'attendance_chk' =>0,'attendance_chk2'=>0])->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays)->with('total_boys','')->with('total_girls','')->with('total_boys_present','')->with('total_boys_absent','')->with('total_girls_present','')->with('total_girls_absent','')->with('appstatus', $appstatus);
        }else{
            return redirect('/login');
        }
    }

    public function updateAttendanceLeave($leave_date, $userid, $leave_type, $status) {  
        list($year, $month, $date) = explode('-', $leave_date);
        $sundays = CommonController::getSundays($year, $month); 
        $saturdays = CommonController::getSaturdays($year, $month); 
        $holidays = DB::table('holidays')->whereRAW('holiday_date = "'.$leave_date.'" ')
            ->where('school_college_id', Auth::User()->id)->get();
        $day = $date * 1;
        $new_leave_date = $year.'-'.$month.'-'.$day;
        $leave_end_date = $new_leave_date;
        //if($holidays->isEmpty()){  
            //if(!in_array($new_leave_date,$saturdays)){
               // if(!in_array($new_leave_date,$sundays)){   

                    $user_details = DB::table('students') 
                        ->select('students.class_id', 'students.section_id')
                        ->where('students.user_id', $userid)->first();  

                    if(!empty($user_details)) { 
                        $class_id = $user_details->class_id;
                        $section_id = $user_details->section_id;
                        $student_id = $userid;

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
                        $leave_reason = 'Leave updated by Teacher';

                        $ex = DB::table('leaves')->where(['student_id' => $student_id,
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'leave_date' => $new_leave_date])
                            ->whereIn('leave_end_date', [$leave_end_date, '0000-00-00'])->first();

                        if(!empty($ex)) {  
                            $id = DB::table('leaves')->where(['student_id' => $student_id,
                                'class_id' => $class_id,
                                'section_id' => $section_id,
                                'leave_date' => $new_leave_date])
                            ->whereIn('leave_end_date', [$leave_end_date, '0000-00-00'])
                            ->update([ 
                                'leave_starttime' => $leave_starttime,
                                'leave_endtime' => $leave_endtime,
                                'leave_type' => $leave_type,
                                'leave_reason' => $leave_reason,
                                'leave_attachment'  => $leave_attachment,
                                'status' => $status,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_by' => $userid,
                                'updated_by' => $userid
                            ]);
                        }   else {  
                            if($status != 'CANCELLED'){
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
                                    'status' => $status,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_by' => $userid,
                                    'updated_by' => $userid
                                ]);
                            }
                        } 
                        
                    //}

                //}
            //}
        }
    }


    public function postDailyAttendance(Request $request)
    {
        $monthyear = date('Y-m');
        $class_id = $request->tclass_id;
        $new_date = $request->new_date;
        $orderdate = explode('-', $new_date);
        $year = $orderdate[0];
        $month   = $orderdate[1];
        $day  = $orderdate[2];
        $day = $day * 1;
        $section_id = $request->tsection_id;
        $fn_section = $request->fn_section;
        $an_section = $request->an_section;
        $student_id = $request->student_id; 
        $students = Student::leftjoin('users','users.id','students.user_id')->where('students.class_id', $class_id)
            ->where('students.section_id',$section_id)->where('users.status','=','ACTIVE')->get(); 

        foreach($students as $key=>$value) {
            $date = 'day_'.$day;
            
            $fn_status = $an_status = 0; $gender = $value->gender;

            $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)
                ->where('user_id',$value->id)->where('date',$new_date)->first();
            if(!empty($ex)) {
                $fn_status = $ex->fn_status;
                $an_status = $ex->an_status;
                $exid = $ex->id;
            }   else {
                $fn_status = $an_status = $exid = 0; 
            } 

            if(isset($fn_section)){
                
                if(in_array($value->id,$fn_section)){
                
                    $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first(); 
                       
                 
                    if(!empty($ex)) {
                        $mode = 1;
                        $date = 'day_'.$day;
                        DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                            ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                            ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]); 

                    }   else {
                        $mode = 1;
                       $date = 'day_'.$day;
                        DB::table('studentsdaily_attendance')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]); 
                    }

                    $mode = 1;
                    $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->first();
                    if(!empty($ex)) {
                        DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)
                            ->update(['fn_status'=>$mode,'admin_status'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                        ]);
                    }   else {
                        DB::table('attendance_approval')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'date'=>$new_date, 'fn_status'=>$mode,'admin_status'=>1,'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]);
                    } 
                    $this->updateAttendanceLeave($new_date, $value->id, 'HALF MORNING','CANCELLED'); 


                }   else{
                    $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first(); 
                  
                    if(!empty($ex)) {
                        $mode = 2;
                        $date = 'day_'.$day;
                        DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                             ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                             ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]); 

                    }   else {
                        $mode = 2;
                         $date = 'day_'.$day;
                         DB::table('studentsdaily_attendance')->insert([
                             'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                             'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                             'created_by'=>Auth::User()->id
                         ]); 

                    }

                    $mode = 2;
                    $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->first();
                    if(!empty($ex)) {
                        DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)
                            ->update(['fn_status'=>$mode,'admin_status'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                        ]);
                    }   else {
                        DB::table('attendance_approval')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'date'=>$new_date, 'fn_status'=>$mode,'admin_status'=>1,'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]);
                    }

                    $this->updateAttendanceLeave($new_date, $value->id, 'HALF MORNING','APPROVED'); 
                }
            
            }     else{
                $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first(); 
              
                if(!empty($ex)) {
                    $mode = 2;
                    $date = 'day_'.$day;
                    DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                         ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                         ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]); 

                }   else {
                    $mode = 2;
                     $date = 'day_'.$day;
                     DB::table('studentsdaily_attendance')->insert([
                         'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                         'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                         'created_by'=>Auth::User()->id
                     ]); 

                }

                $mode = 2;
                $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->first();
                if(!empty($ex)) {
                    DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)
                        ->update(['fn_status'=>$mode,'admin_status'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                    ]);
                }   else {
                    DB::table('attendance_approval')->insert([
                        'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'date'=>$new_date, 'fn_status'=>$mode,'admin_status'=>1,'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }

                $this->updateAttendanceLeave($new_date, $value->id, 'HALF MORNING','APPROVED'); 
            }       

            if(isset($an_section)){

                if(in_array($value->id,$an_section)){
                    $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();

                  
                    if(!empty($ex)) {
                        $mode = 1;
                        $date = 'day_'.$day.'_an';
                        DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                            ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                            ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]); 

                    }   else {
                        $mode = 1;
                        $date = 'day_'.$day.'_an';
                        DB::table('studentsdaily_attendance')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]); 

                    }

                    $mode = 1;
                    $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->first();
                    if(!empty($ex)) {
                        DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)
                            ->update(['an_status'=>$mode,'admin_status'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                        ]);
                    }   else {
                        DB::table('attendance_approval')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'date'=>$new_date, 'an_status'=>$mode,'admin_status'=>1,'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]);
                    }

                    $this->updateAttendanceLeave($new_date, $value->id, 'HALF AFTERNOON','CANCELLED'); 
                }
                else{
                    $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();
                   if(!empty($ex)) {
                        $mode = 2;
                        $date = 'day_'.$day.'_an';
                        DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                            ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                            ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]); 

                    }   else {
                        $mode = 2;
                        $date = 'day_'.$day.'_an';
                        DB::table('studentsdaily_attendance')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]); 

                    }

                    $mode = 2;
                    $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->first();
                    if(!empty($ex)) {
                        DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)
                            ->update(['an_status'=>$mode,'admin_status'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                        ]);
                    }   else {
                        DB::table('attendance_approval')->insert([
                            'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                            'date'=>$new_date, 'an_status'=>$mode,'admin_status'=>1,'created_at'=>date('Y-m-d H:i:s'),
                            'created_by'=>Auth::User()->id
                        ]);
                    }
                    $this->updateAttendanceLeave($new_date, $value->id, 'HALF AFTERNOON','APPROVED');
                }
            }  else{
                $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();
               if(!empty($ex)) {
                    $mode = 2;
                    $date = 'day_'.$day.'_an';
                    DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                        ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]); 

                }   else {
                    $mode = 2;
                    $date = 'day_'.$day.'_an';
                    DB::table('studentsdaily_attendance')->insert([
                        'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]); 

                }

                $mode = 2;
                $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->first();
                if(!empty($ex)) {
                    DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)
                        ->update(['an_status'=>$mode,'admin_status'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                    ]);
                }   else {
                    DB::table('attendance_approval')->insert([
                        'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'date'=>$new_date, 'an_status'=>$mode,'admin_status'=>1,'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
                $this->updateAttendanceLeave($new_date, $value->id, 'HALF AFTERNOON','APPROVED');
            } 

            $ex = DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)
                ->where('user_id',$value->id)->where('date',$new_date)->first();
            if(!empty($ex)) {
                $fn_status_after = $ex->fn_status;
                $an_status_after = $ex->an_status;
                $exid_after = $ex->id;

                if($exid == $exid_after) {
                    if(($fn_status == $fn_status_after) && ($an_status == $an_status_after)) {
                        // nothing to do
                    } else {
                        $ward = 'Ward';
                        if($gender == 'MALE') {
                            $ward = 'Son';
                        }   else {
                            $ward = 'Daughter';
                        }
                        $msg = '';
                        if($fn_status_after == 1 && $an_status_after == 1) {
                            $msg = 'Your '.$ward.' is Present Today';
                        } elseif($fn_status_after == 2 && $an_status_after == 2) {
                            $msg = 'Your '.$ward.' is Absent Today';
                        } elseif($fn_status_after == 2) {
                            $msg = 'Your '.$ward.' is Absent for Morning Half-day';
                        } elseif($an_status_after == 2) {
                            $msg = 'Your '.$ward.' is Absent for Aftertoon Half-day';
                        }

                        $type_no = 1;
                        $title = 'Attendance';
                        $message = $msg;
                        $fcmMsg = array("fcm" => array("notification" => array(
                            "title" => $title,
                            "body" => $message,
                            "type" => $type_no,
                          )));

                        CommonController::push_notification($value->id, $type_no, $exid_after, $fcmMsg);
                    }
                }
            }   else {
                $fn_status = $an_status = $exid = 0; 
            }      
                        
        }


        $total_boys_present = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',1)->where('attendance_approval.an_status',1)->where('users.gender','MALE')->get()->count();

        $total_boys_absent = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',2)->where('attendance_approval.an_status',2)->where('users.gender','MALE')->get()->count();

        $total_girls_present = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',1)->where('attendance_approval.an_status',1)->where('users.gender','FEMALE')->get()->count();

        $total_girls_absent = User::leftjoin('attendance_approval','attendance_approval.user_id','users.id')->where('attendance_approval.date',$new_date)->where('attendance_approval.fn_status',2)->where('attendance_approval.an_status',2)->where('users.gender','FEMALE')->get()->count();

       
        return response()->json(['status' => 'SUCCESS', 'message' => 'Attendance saved successfully','total_boys_present'=> $total_boys_present,'total_boys_absent'=>$total_boys_absent,'total_girls_present' => $total_girls_present,'total_girls_absent'=>$total_girls_absent]);
     

    }



    public function updateStudentDailyAttendance(Request $request)
    {
        if(Auth::check()){
           $student_id = $request->student_id;
            $mode = $request->mode;
            $day = $request->day;
            $monthyear = $request->monthyear;
            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_id',0);

       
            if($student_id > 0) {

                $ex = DB::table('students_attendance')->where('user_id', $student_id)
                    ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();

                if(!empty($ex)) {
                    $date = 'day_'.$day;
                    DB::table('students_attendance')->where('user_id', $student_id)
                        ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day;
                    DB::table('students_attendance')->insert([
                        'user_id'=>$student_id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
            }

            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }

    }


    


    public function loadStudentDailyAttendance(Request $request)   {
        if(Auth::check()){   
            $school_id = Auth::User()->id;
            $monthyear = $request->get('monthyear', '');
            $lastdate = date('t', strtotime($monthyear));
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);
            $new_date = $request->get('date',''); 
             $orderdate = explode('-', $new_date);
             $year = $orderdate[0];
             $month   = $orderdate[1];
             $day  = $orderdate[2];
              $day = $day * 1;          
            if($class_id > 0) {} else { $class_id = 0; }
            if($section_id > 0) {} else { $section_id = 0; }

            if($class_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Class']);
            }
            if($section_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Section']);
            }

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
                    ->where('section_id', $section_id)->where('date', $new_date)->where('admin_status', 1)->get();
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
                    //echo "<pre>";print_r($students);exit;
                    $html = view('admin.loadstudentsdailyattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                    'section_id'=>$section_id, 'students'=>$students, 'lastdate'=>$lastdate])->with('fn_chk',$fn_chk)->with('an_chk',$an_chk)->with('new_date',$new_date)->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays)->with('total_boys',$total_boys)
                        ->with('total_girls',$total_girls)

                        ->with('total_boys_present_fn',$total_boys_present_fn)
                        ->with('total_boys_absent_fn',$total_boys_absent_fn)
                        ->with('total_girls_present_fn',$total_girls_present_fn)
                        ->with('total_girls_absent_fn',$total_girls_absent_fn)
                        ->with('total_boys_present_an',$total_boys_present_an)
                        ->with('total_boys_absent_an',$total_boys_absent_an)
                        ->with('total_girls_present_an',$total_girls_present_an)
                        ->with('total_girls_absent_an',$total_girls_absent_an)
                        ->with('appstatus',$appstatus)
                        ->render();

                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students attendance Detail']);

                }   else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
            }


            return view('admin.studentsdailyattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }

    /* Function: postStudentAcademics
    Save into   table
     */
    public function postStudentAcademics(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $user_id = $request->user_id;
            $academic_year = $request->academic_year;
            $from_month = $request->from_month;
            $to_month = $request->to_month;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'academic_year' => 'required',
                'from_month' => 'required',
                'to_month' => 'required',
                'class_id' => 'required',
                'section_id' => 'required',
               
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 'FAILED',
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('student_class_mappings')->where('academic_year', $academic_year)
                    ->where('user_id', $user_id)->whereNotIn('id', [$id])->first();
                    
            } else {
                $exists = DB::table('student_class_mappings')->where('academic_year', $academic_year)
                    ->where('user_id', $user_id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Details Already Exists'], 201);
            }

            if ($id > 0) {
                $academics = StudentAcademics::find($id);
                $academics->updated_at = date('Y-m-d H:i:s');
                $academics->updated_by = Auth::User()->id;
            } else {
                $academics = new StudentAcademics();
                $academics->created_at = date('Y-m-d H:i:s');
                $academics->created_by = Auth::User()->id;
            }
            $academics->user_id = $user_id;
            $academics->academic_year = $academic_year;
            $academics->from_month = $from_month;
            $academics->to_month = $to_month;
            $academics->class_id = $class_id;
            $academics->section_id = $section_id;
            $academics->status = $status;

            $academics->save();
            return response()->json(['status' => "SUCCESS", 'message' => 'Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editStudentAcademics(Request $request)
    {
        if (Auth::check()) {
            $academics = StudentAcademics::where('id', $request->id)->get();
            if ($academics->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $academics[0], 'message' => 'Academics Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Academics Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Teacher Attendance Management
    /* Function: viewTeacherAttendance
     */
    public function viewTeacherAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                $monthyear = date('Y-m');
            }
            $lastdate = date('t', strtotime($monthyear));
            User::$monthyear = $monthyear;
            $teachers = User::with('teacherattendance')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('teachers.user_id', '>', 0)
                ->where('users.status', 'ACTIVE')
                ->select('users.id', 'name', 'email', 'mobile', 'emp_no')->get();
            if($teachers->isNotEmpty()) {
                $teachers = $teachers->toArray();
                //echo "<pre>"; print_r($teachers);exit;
            }   else {
                $teachers = [];
            }
            return view('admin.teachersattendance')->with(['teachers'=>$teachers, 'monthyear'=>$monthyear
                , 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }


    public function viewEditTeachersAttendance($encid,$monthyear, Request $request)   {
        if(Auth::check()){
            $teacher_id = 0;
           $obj = json_decode(base64_decode($encid));
            if(!empty($obj)) {
             $teacher_id = $obj->id;
            }
           if($teacher_id > 0) {
                $pl = DB::table('users')->where('id', $teacher_id)->where('status', 'ACTIVE')
                    ->select('id', 'name', 'email', 'mobile')->get();
                if($pl->isNotEmpty()) {
                
                    if(empty($monthyear)) {
                        $monthyear = date('Y-m');
                    }
                    $lastdate = date('t', strtotime($monthyear));
                    User::$monthyear = $monthyear;
                    $players = User::with('teacherattendance')
                        ->where('id', $teacher_id)
                       ->select('id', 'name', 'email', 'mobile')->get();

                       foreach($players as $k=>$v){
                        list($year, $month) = explode('-', $monthyear);
                     $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                        ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                        ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                        $players[$k]->holidays_list = $holidays;
                    }
                    if($players->isNotEmpty()) {
                        $players = $players->toArray();
                        //echo "<pre>"; print_r($players);exit;
                    }
                    return view('admin.updateteacherattendance')->with(['players'=>$players, 'monthyear'=>$monthyear,
                        'lastdate'=>$lastdate]);
                }  else {
                    return view('admin.updateteacherattendance')->with(['error'=>1]);
                }
            }   else {
                return view('admin.updateteacherattendance')->with(['error'=>1]);
            }
        }else{
            return redirect('/login');
        }
    }

    /* Function: updateTeacherAttendance
     */
    public function updateTeacherAttendance(Request $request)
    {
        if(Auth::check()){
            $teacherid = $request->teacherid;
            $mode = $request->mode;
            $day = $request->day;
            $monthyear = $request->monthyear;

            // $validator = Validator::make($request->all(), [
            //     'teacherid' => 'required',
            //     'mode' => 'required',
            //     'day' => 'required',
            //     'monthyear' => 'required',
            // ]);

            // if ($validator->fails()) {

            //     $msg = $validator->errors()->all();

            //     return response()->json([
            //         'status' => 0,
            //         'message' => implode(', ', $msg)
            //     ]);
            // }

            if($teacherid > 0) {

                $ex = DB::table('teachers_attendance')->where('user_id', $teacherid)
                    ->where('monthyear', $monthyear)->first();

                if(!empty($ex)) {
                    $date = 'day_'.$day;
                    DB::table('teachers_attendance')->where('user_id', $teacherid)
                        ->where('monthyear', $monthyear)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day;
                    DB::table('teachers_attendance')->insert([
                        'user_id'=>$teacherid,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
            }

            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }
    }



    //Attendance Management
    /* Function: loadTeacherAttendance
     */
    public function loadTeacherAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the Year']);
            }
            $lastdate = date('t', strtotime($monthyear));
            User::$monthyear = $monthyear;

            $teachers = User::with('teacherattendance')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('user_type', 'TEACHER')
                ->select('users.id', 'name', 'email', 'mobile', 'teachers.emp_no')
                ->get();
                foreach($teachers as $k=>$v){
                    list($year, $month) = explode('-', $monthyear);
                 $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                    $teachers[$k]->holidays_list = $holidays;
                }
            if($teachers->isNotEmpty()) {
                $teachers = $teachers->toArray();
                //echo "<pre>"; print_r($teachers);exit;
                $html = view('admin.loadteachersattendance')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate])->render();

                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Teacher attendance Detail']);

            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Teacher attendance Detail']);
            }

            return view('admin.teachersattendance')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }



       //Teacher Daily Attendance Management
    /* Function: viewTeacherAttendance
     */
    public function viewTeacherDailyAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                $monthyear = date('Y-m');
            }
            $lastdate = date('t', strtotime($monthyear));
            $new_date = date('Y-m-d');
            User::$monthyear = $monthyear;
            $teachers = User::with('teacherdailyattendance')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('teachers.user_id', '>', 0)
                ->where('users.status', 'ACTIVE')->where('users.school_college_id', Auth::User()->id)
                ->select('users.id', 'name', 'email', 'mobile', 'emp_no','users.profile_image')->get();
                $userids = array();
                foreach($teachers as $k=>$v){

                    list($year, $month) = explode('-', $monthyear);
                 $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')->where('holidays.school_college_id', Auth::User()->id)
                    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                    $teachers[$k]->holidays_list = $holidays;
                    array_push($userids,$v->id);
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
               list($year, $month) = explode('-', $monthyear);
               $sundays = CommonController::getSundays($year, $month); 
               $saturdays = CommonController::getSaturdays($year, $month); 
               $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                  ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                  ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                  // $students[$k]->holidays_list = $holidays;
                  if($holidays->isNotEmpty()){
                      $holidays = $holidays->toArray();
                  }
            if($teachers->isNotEmpty()) {
                $teachers = $teachers->toArray();
                // echo "<pre>"; print_r($teachers);exit;
            }   else {
                $teachers = [];
            }
            return view('admin.teachersdaily_attendance')->with(['teachers'=>$teachers, 'monthyear'=>$monthyear
                , 'lastdate'=>$lastdate,'fn_chk'=>$fn_chk,'an_chk'=>$an_chk])->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays)->with('new_date',$new_date);
        }else{
            return redirect('/login');
        }
    }

    public function loadTeacherDailyAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            $new_date = $request->get('date','');
            if(empty($monthyear)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the Year']);
            }
            $lastdate = date('t', strtotime($monthyear));
            User::$monthyear = $monthyear;

            $teachers = User::with('teacherdailyattendance')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('user_type', 'TEACHER')
                ->select('users.id', 'name', 'email', 'mobile', 'teachers.emp_no','users.profile_image')
                ->get();
                $userids = array();
                foreach($teachers as $k=>$v){
                array_push($userids,$v->id);
                }

                list($year, $month) = explode('-', $monthyear);

                $sundays = CommonController::getSundays($year, $month); 
                $saturdays = CommonController::getSaturdays($year, $month); 
                // echo"<pre>".print_r($sundays);
                // echo "<pre>".print_r($saturdays);
                // exit;

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
                $html = view('admin.loadteachers_dailyattendance')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate])->with('fn_chk',$fn_chk)->with('an_chk',$an_chk)->with('new_date',$new_date)->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays)->render();

                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Teacher attendance Detail']);

            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Teacher attendance Detail']);
            }

            return view('admin.teachersattendance')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }


    public function postTeachersDailyAttendance(Request $request)
    {
        $monthyear = date('Y-m');
        // $class_id = $request->tclass_id;
        $new_date = $request->new_date;
        $orderdate = explode('-', $new_date);
         $year = $orderdate[0];
         $month   = $orderdate[1];
         $day  = $orderdate[2];
         $day = $day * 1;
        // $section_id = $request->tsection_id;
        $fn_section = $request->fn_section;
        $an_section = $request->an_section;
         $teacher = Teacher::leftjoin('users','users.id','teachers.user_id')->where('users.status','=','ACTIVE')->get();
                foreach($teacher as $key=>$value) {
               $date = 'day_'.$day;
             
               if(isset($fn_section)){
                
                  if(in_array($value->id,$fn_section)){
                
                    $ex = DB::table('teachersdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->first();

                        $mode = 1;
                        //  exit;
                            if(!empty($ex)) {
                                // echo "if";
                                // exit;
                                $date = 'day_'.$day;
                                DB::table('teachersdaily_attendance')->where('user_id', $value->id)
                                    ->where('monthyear', $monthyear)->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                            }   else {
                               $date = 'day_'.$day;
                                DB::table('teachersdaily_attendance')->insert([
                                    'user_id'=>$value->id,'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),'created_by'=>Auth::User()->id
                                ]);
                            }
                        }
                        else{
                            $ex = DB::table('teachersdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->first();

                          $mode = 2;
                         //    exit;
                             if(!empty($ex)) {
                                 $date = 'day_'.$day;
                                 DB::table('teachersdaily_attendance')->where('user_id', $value->id)
                                     ->where('monthyear', $monthyear)->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                             }   else {
                                
                                 $date = 'day_'.$day;
                                 DB::table('teachersdaily_attendance')->insert([
                                     'user_id'=>$value->id,'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),'created_by'=>Auth::User()->id
                                 ]);
                             }
                         }
                        }
                 
                       

                    if(isset($an_section)){

                            $ex = DB::table('teachersdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->first();

                            if(in_array($value->id,$an_section)){
                            $mode = 1;
                            if(!empty($ex)) {
                                $date = 'day_'.$day.'_an';
                                DB::table('teachersdaily_attendance')->where('user_id', $value->id)
                                    ->where('monthyear', $monthyear)->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                            }   else {
                                $date = 'day_'.$day.'_an';
                                DB::table('teachersdaily_attendance')->insert([                  'user_id'=>$value->id,'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),'created_by'=>Auth::User()->id
                                ]);
                            }
                        }
                        else{
                            $ex = DB::table('teachersdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->first();

                            
                            $mode = 2;
                            if(!empty($ex)) {
                                $date = 'day_'.$day.'_an';
                                DB::table('teachersdaily_attendance')->where('user_id', $value->id)
                                    ->where('monthyear', $monthyear)->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                            }   else {
                                $date = 'day_'.$day.'_an';
                                DB::table('teachersdaily_attendance')->insert([
                                    'user_id'=>$value->id,'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),'created_by'=>Auth::User()->id
                                ]);
                            }
                        }
                    }
                       
                        
                }
               
                return response()->json(['status' => 'SUCCESS', 'message' => 'Attendance saved successfully']);
            
        // }   else {
        //     return response()->json(['status' => 'FAILED', 'message' => 'Invalid Class and Section']);
        // }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Timetables has been saved'], 201);

    }




    public function viewHomeworkTestList($id)
    {
        if (Auth::check()) {
            $qb = [];
            if($id> 0) {
                $qb = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                    ->leftjoin('classes', 'classes.id', 'tests.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                    //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                        'terms.term_name')
                    ->where('tests.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.previewtest')->with(['qb'=>$qb]);
        } else {
            return redirect('/admin/login');
        }
    }



    //Exams
    /* Function: viewExams
     */
    public function viewExams()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            return view('admin.exams')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    public function loadExams(Request $request)   {
        if(Auth::check()){
            $id = $request->get('id', 0);
            $start_date = $request->get('start_date', '');
            $end_date = $request->get('end_date', '');
            $monthyear = $request->get('monthyear','');
            $exam_name = $request->get('exam_name','');
            if(empty($exam_name)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the valid exam name']);
            }
            if(empty($start_date)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the valid Start date']);
            }
            if(empty($end_date)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the valid End date']);
            }
            if(strtotime($start_date) > strtotime($end_date)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'End date must be greater than Start date']);
            }
            $map_subjects = '';    $timetable = [];
              if (($start_date != '') && ($end_date != '')) {
                $classes = Classes::where('status','ACTIVE');
                if(Auth::User()->user_type == 'SCHOOL') {
                    $classes->where('school_id', Auth::User()->id); 
                }
                $classes = $classes->orderby('position','asc')->get();
                
                // exit;   
                $class_ids = array();
                 $subjects=[];
                if($classes->isNotEmpty()){
                    $classes->toArray();
                    foreach ($classes as $k=>$class) {
                        $class_id = $class->id;
                        $mapped_subjects = Sections::where('class_id', $class_id)->where('status', 'ACTIVE')->first();
                        $classes[$k]->subjects = $mapped_subjects;
                        if(!empty($mapped_subjects)) {
                            // foreach($mapped_subjects as $sec) {
                                $mapsubs = $mapped_subjects->mapped_subjects;
                                $mapsubs = explode(',', $mapsubs);
                                $subjects[] = DB::table('subjects')->whereIn("id", $mapsubs)->where('status','ACTIVE')
                                ->select("subject_name", "id")->get();
                                 $classes[$k]->subjects = $subjects;
                                //  array_push($class_ids, $subjects);
                            // }
                        } 

                                                   
                    }
                   

                      $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first();
                        $startTimeStamp = strtotime($start_date);
                         $endTimeStamp = strtotime($end_date);
                         $timeDiff = abs($endTimeStamp - $startTimeStamp);
                         $numberDays = $timeDiff/86400;  // 86400 seconds in one day
                         $numberDays = intval($numberDays) + 1;
                        
                         $date = $start_date;
                         $array = array();
  
                         // Use strtotime function
                         $Variable1 = strtotime($start_date);
                         $Variable2 = strtotime($end_date);
                        //  $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
                       $timetable1 = DB::table('exams')->where('monthyear',$monthyear)->where('exam_name',$exam_name)->get();
                    if($timetable1->isNotEmpty()) {
                        $exam_id = array();
                        foreach($timetable1 as $times) {

                            for ($currentDate = $Variable1; $currentDate <= $Variable2;$currentDate += (86400)) {
                                $date = date('Y-m-d', $currentDate);
                                // $array[] = $Store;
                                $orderdate = explode('-', $date);
                                $year = $orderdate[0];
                                $month   = $orderdate[1];
                                $day  = $orderdate[2];
                                $day = $day * 1;
                                $new_date = 'day_'.$day;
                                $timetable[$times->class_ids][$date] = $times->$new_date;
                            }
                            // while (strtotime($date) <= strtotime($end_date)) { // Compare start date is less than 
                            //     $date = date ("Y-m-d", strtotime("+1 day", strtotime($date))); // increment date by 1 day
                            //     $orderdate = explode('-', $date);
                            //     $year = $orderdate[0];
                            //     $month   = $orderdate[1];
                            //     $day  = $orderdate[2];
                            //     $day = $day * 1;
                            //     $new_date = 'day_'.$day;
                            //     $timetable[$times->class_ids][$date] = $times->$new_date;
                              
                            //  }
                          
                        }
                    }   else {
                        $timetable = [];
                    }
              

                    // if
                    // explode($exam_id)

                    
                    // echo "<pre>";print_r($timetable);
                    // exit;
                    $datesArray = [];

                    $startingDate = strtotime($start_date);
                    $endingDate = strtotime($end_date);
                   
                    for ($currentDate = $startingDate; $currentDate <= $endingDate; $currentDate += (86400)) {
                        $date = date('Y-m-d', $currentDate);
                        $datesArray[] = $date;
                    }
              
                    $class = Classes::select('*')->get();
                    $days = DB::table('days')->select('*')->get();


                    $exam = '';$examsarr = [];
                    $exams = Exams::where('id', $id)->get(); 
                    if ($exams->isNotEmpty()) { 
                        $examsarr = $exams->toArray();
                        $exam = $exams[0];
                        $examsarr = $examsarr[0];
                    }


                    $html = view('admin.loadexamlist')->with('exam_name',$exam_name)->with('monthyear',$monthyear)->with('start_date',$start_date)->with('end_date',$end_date)->with('class', $class)->with('periods', $datesArray)->with('classes', $classes)->with('subjects', $subjects)->with('class_id', $class_id)->with('timetable', $timetable)->with('examsarr', $examsarr)->with('exam_id', $id)->render();
                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Exam Time Table']);
                }  else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Not a valid section']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
            }
            return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
        }else{
            return redirect('/login');
        }
    }


    public function viewloadExams(Request $request)   {
        if(Auth::check()){
            $examid = $request->get('id', '');
            $start_date = $request->get('start_date', '');
            $end_date = $request->get('end_date', '');
            $monthyear = $request->get('monthyear','');
            $exam_name = $request->get('exam_name','');
            $map_subjects = '';    $timetable = [];
              if (($start_date != '') && ($end_date != '')) {
                $classes = Classes::where('status','ACTIVE');
                if(Auth::User()->user_type == 'SCHOOL') {
                    $classes->where('school_id', Auth::User()->id); 
                }
                $classes = $classes->orderby('position','asc')->get();
                
                // exit;   
                $class_ids = array();
                 $subjects=[];
                if($classes->isNotEmpty()){
                    $classes->toArray();
                    foreach ($classes as $k=>$class) {
                    $class_id = $class->id;
                    $mapped_subjects = Sections::where('class_id', $class_id)->where('status', 'ACTIVE')->first();
                    $classes[$k]->subjects = $mapped_subjects;
                    if(!empty($mapped_subjects)) {
                        // foreach($mapped_subjects as $sec) {
                            $mapsubs = $mapped_subjects->mapped_subjects;
                            $mapsubs = explode(',', $mapsubs);
                            $subjects[] = DB::table('subjects')->whereIn("id", $mapsubs)->where('status','ACTIVE')
                            ->select("subject_name", "id")->get();
                             $classes[$k]->subjects = $subjects;
                            //  array_push($class_ids, $subjects);
                        // }
                    }
                    

                                                   
                    }
                   

                      $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->where('school_id', Auth::User()->id)->first();
                        $startTimeStamp = strtotime($start_date);
                         $endTimeStamp = strtotime($end_date);
                         $timeDiff = abs($endTimeStamp - $startTimeStamp);
                         $numberDays = $timeDiff/86400;  // 86400 seconds in one day
                         $numberDays = intval($numberDays) + 1;
                        
                         $date = $start_date;
                         $array = array();
  
                         // Use strtotime function
                         $Variable1 = strtotime($start_date);
                         $Variable2 = strtotime($end_date);
                        //  $date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
                       $timetable1 = DB::table('exams')->where('school_id', Auth::User()->id)->where('monthyear',$monthyear)->where('exam_name',$exam_name)->get();
                    if($timetable1->isNotEmpty()) {
                        $exam_id = array();
                        foreach($timetable1 as $times) {

                            for ($currentDate = $Variable1; $currentDate <= $Variable2;$currentDate += (86400)) {
                                $date = date('Y-m-d', $currentDate);
                                // $array[] = $Store;
                                $orderdate = explode('-', $date);
                                $year = $orderdate[0];
                                $month   = $orderdate[1];
                                $day  = $orderdate[2];
                                $day = $day * 1;
                                $new_date = 'day_'.$day;
                                $timetable[$times->class_ids][$date] = $times->$new_date;
                            }
                            // while (strtotime($date) <= strtotime($end_date)) { // Compare start date is less than 
                            //     $date = date ("Y-m-d", strtotime("+1 day", strtotime($date))); // increment date by 1 day
                            //     $orderdate = explode('-', $date);
                            //     $year = $orderdate[0];
                            //     $month   = $orderdate[1];
                            //     $day  = $orderdate[2];
                            //     $day = $day * 1;
                            //     $new_date = 'day_'.$day;
                            //     $timetable[$times->class_ids][$date] = $times->$new_date;
                              
                            //  }
                          
                        }
                    }   else {
                        $timetable = [];
                    }
              

                    // if
                    // explode($exam_id)

                    
                    // echo "<pre>";print_r($timetable);
                    // exit;
                    $datesArray = [];

                    $startingDate = strtotime($start_date);
                    $endingDate = strtotime($end_date);
                   
                    for ($currentDate = $startingDate; $currentDate <= $endingDate; $currentDate += (86400)) {
                        $date = date('Y-m-d', $currentDate);
                        $datesArray[] = $date;
                    }
              
                    $class = Classes::select('*')->get();
                    $days = DB::table('days')->select('*')->get();

                    $examsarr = [];
                    $exams = Exams::where('id', $examid)->get(); 
                    if ($exams->isNotEmpty()) { 
                        $examsarr = $exams->toArray();
                        $exam = $exams[0];
                        $examsarr = $examsarr[0];
                    }
                    //echo "<pre>"; print_r($examsarr); exit;
                    $html = view('admin.previewloadexamlist')->with('exam_name',$exam_name)
                        ->with('monthyear',$monthyear)->with('start_date',$start_date)
                        ->with('end_date',$end_date)->with('class', $class)->with('periods', $datesArray)
                        ->with('classes', $classes)->with('subjects', $subjects)->with('class_id', $class_id)
                        ->with('timetable', $timetable)->with('examsarr', $examsarr)
                        ->with('exam_id', $examid)->render();
                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Exam Time Table']);
                }  else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Not a valid section']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
            }
            return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Invalid inputs']);
        }else{
            return redirect('/login');
        }
    }

    /* Function: getExams
    Datatable Load
     */
    public function getExams(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id',0);

            $examsqry = Exams::leftjoin('exam_sessions', 'exams.id', 'exam_sessions.exam_id')
                ->select('exams.*')->groupby('exam_id');//->groupby('exam_name');
            $filteredqry = Exams::leftjoin('exam_sessions', 'exams.id', 'exam_sessions.exam_id')
                ->select('exams.*')->groupby('exam_id');//->groupby('exam_name');


            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'exams.status') {
                            $examsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $examsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }
            if($status != '' || $status != 0){
                $examsqry->where('exams.status',$status);
                $filteredqry->where('exams.status',$status);
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $examsqry->where('exams.school_id', Auth::User()->id);
                $filteredqry->where('exams.school_id', Auth::User()->id);
            }

            if($class_id > 0) {
                $examsqry->where('exam_sessions.class_id',$class_id);
                $filteredqry->where('exam_sessions.class_id',$class_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'exams.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $exams = $examsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('exams.id')->get();

            $totalDataqry = Exams::leftjoin('exam_sessions', 'exams.id', 'exam_sessions.exam_id')
                ->groupby('exam_id');
            $totalData = $totalDataqry->select('exams.id')->get();

            if($totalData->isNotEmpty()) {
                $totalData = count($totalData);
            }

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = count($filters);
            }

            $data = [];
            if (!empty($exams)) {
                foreach ($exams as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postExams
    Save into em_countries table
     */

     public function addExams(Request $request)
     {
         if (Auth::check()) {
             $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            
            // return view('admin.add_exam')->with('classes', $classes);
            return view('admin.add_exam')->with('start_date','')->with('end_date','')->with('class', '')->with('periods', '')->with('classes', '')->with('subjects', '')->with('class_id', '')
                ->with('timetable', '')->with('exam_id', '');
         } else {
             return redirect('/admin/login');
         }
     }


     public function postExams(Request $request){
        if (Auth::check()) {
            $input = $request->all();
            $id = $request->get('id', 0);
            $from_date = $request->from_date;
            $last_date = $request->last_date;
            $examname = $request->examname;
            $month_year = $request->month_year;
            $session = $request->session;
            $syllabus = $request->syllabus;

            $class_id = $request->tclass_id;
            $section_id = $request->tsection_id;
            $subject_id = $request->subject_id;
           
            if(empty($session)) { $session = []; } 
            if(empty($subject_id)) { $subject_id = []; } 
            if(empty($syllabus)) { $syllabus = []; } 

            //echo "<pre>"; print_r($input); exit;
            if(count($session) > 0) { 
                $data['monthyear'] = $month_year;
                $data['class_ids'] = 0;
                $data['exam_name'] = $examname;
                $data['exam_startdate'] = $from_date;
                $data['exam_enddate'] = $last_date;
                $data['school_id'] = Auth::User()->id;
                if($id > 0) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $exam_id = $id; 
                    Exams::where('id', $id)->update($data); 
                }   else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $exam_id = Exams::insertGetId($data); 
                }
                
                DB::table('exam_sessions')->where('exam_id', $exam_id)->update(['status'=>'INACTIVE']);
                DB::table('events')->where('exam_id', $exam_id)->update(['status'=>'INACTIVE']);
                foreach($session as $class_id => $sess) {
                    $data = [];
                    $data['exam_id'] = $exam_id;
                    $data['class_id'] = $class_id;
                    $subid = 0;

                    if(is_array($sess)) {
                        foreach($sess as $dt => $val) {
                            if(isset($subject_id[$class_id]) && isset($subject_id[$class_id][$dt])) {
                                $data['subject_id'] = $subject_id[$class_id][$dt];
                                $data['exam_date'] = $dt;
                                $data['session'] = $val;
                                $data['status'] = 'ACTIVE';
                                $data['syllabus'] = $syllabus[$class_id][$dt];

                                if($id > 0) {
                                    $ex = DB::table('exam_sessions')->where('exam_id', $exam_id)
                                        ->where('class_id', $class_id)
                                        ->where('subject_id', $subject_id[$class_id][$dt])
                                        ->where('exam_date', $dt)->first();
                                    if(!empty($ex)) {

                                        $data['updated_by'] = Auth::User()->id; 
                                        $data['updated_at'] = date('Y-m-d H:i:s'); 

                                        DB::table('exam_sessions')->where('exam_id', $exam_id)
                                        ->where('class_id', $class_id)
                                        ->where('subject_id', $subject_id[$class_id][$dt])
                                        ->where('exam_date', $dt)
                                        ->update($data);
                                    }   else {
                                        $data['created_by'] = Auth::User()->id; 
                                        $data['created_at'] = date('Y-m-d H:i:s'); 
                                        DB::table('exam_sessions')->insert($data); 
                                    }
                                }   else {
                                    $data['created_by'] = Auth::User()->id; 
                                    $data['created_at'] = date('Y-m-d H:i:s'); 
                                    DB::table('exam_sessions')->insert($data); 
                                } 

                                /* Update into Event */
                                $ex = DB::table('exam_sessions')->where('exam_id', $exam_id)
                                        ->where('class_id', $class_id)
                                        ->where('subject_id', $subject_id[$class_id][$dt])
                                        ->where('exam_date', $dt)->first();
                                if(!empty($ex)) {
                                    $ex1 =  DB::table('events')->where('exam_id', $exam_id)
                                        ->where('exam_session_id', $ex->id)->first();
                                    if(!empty($ex1)) {
                                        DB::table('events')->where('exam_id', $exam_id)
                                            ->where('exam_session_id', $ex1->id)
                                            ->update(['status'=>$ex1->status]);
                                    }   else {
                                        $subject_name = DB::table('subjects')->where('id', $subject_id[$class_id][$dt])
                                            ->value('subject_name');

                                        /*$circular_title = $examname.' Exam '.$subject_name.' is to be held on '.date('d M, Y', strtotime($dt)). '';*/

                                        $circular_title = $examname.' Exam '.$subject_name;
                                        if(!empty($syllabus[$class_id][$dt])) {
                                            $circular_title.= ' - Syllabus '.$syllabus[$class_id][$dt];
                                        }

                                        $circular_message = $circular_title; 

                                        DB::table('events')->insert([
                                            'class_ids' => $class_id,
                                            'exam_id' => $exam_id,
                                            'exam_session_id' => $ex->id,
                                            'circular_title' => $circular_title,
                                            'circular_message' => $circular_message,
                                            'circular_date' => $dt,
                                            'status' => 'ACTIVE',
                                            'approve_status' => 'APPROVED',
                                            'created_by' => Auth::User()->id,
                                            'created_at' => date('Y-m-d H:i:s')
                                        ]);
                                    }

                                    
                                }
                                /* Update into Event */

                            }
                        }
                    } 
                }
            } 
 
            return response()->json(['status' => 'SUCCESS', 'message' => 'Exams has been saved'], 201);
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Session '], 201);
        }
     }
     
     public function postExamsold20423(Request $request){
        $input = $request->all();
        $class_id = $request->tclass_id;
        $section_id = $request->tsection_id;
        $subject_id = $request->subject_id;
       
        $start_date = $request->from_date;
        $last_date = $request->last_date;
        $session = $request->session;
        $exam_name = $request->examname;
        $monthyear = $request->month_year;

        
        $session_chk = '';
        
        if(count($subject_id)>0) {
          foreach($subject_id as $class_id=>$subjects) {
          if(count($subjects)>0) {
              foreach($subjects as $key => $subid) {
                // if(isset($session[$class_id][$key])){
                    if(isset($session[$class_id][$key])){
                        $session_chk = $session[$class_id][$key];
                    }
                           
             
                    if($session_chk == "fn") {
                    $orderdate = explode('-', $key);
                    $year = $orderdate[0];
                    $month   = $orderdate[1];
                    $day  = $orderdate[2];
                    $day = $day * 1;
                    $day = 'day_'.$day;
                    $data[$day] = $subid.'_fn';
                  }
                      elseif($session_chk == "an"){
                        $orderdate = explode('-', $key);
                        $year = $orderdate[0];
                        $month   = $orderdate[1];
                        $day  = $orderdate[2];
                        $day = $day * 1;
                        $day = 'day_'.$day;
                        $data[$day] = $subid.'_an';
                      }
                    }
                    // }
                    }
                    
                
                    $data['exam_startdate'] = $start_date;
                    $data['exam_enddate'] = $last_date;
                    $chk = DB::table('exams')->where(['class_ids'=>$class_id])->where('monthyear',$monthyear)->where('exam_name',$exam_name)->first();

                    if(!empty($chk)) {
                        $id = $chk->id;
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        $timetable = Exams::where('id', $id)->where('class_ids',$class_id)->update($data);
                       
                    }   else {
                        $data['class_ids'] = $class_id;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $data['exam_name'] = $exam_name;
                        $data['monthyear'] = $monthyear;
                        $timetable = Exams::insert($data);
                     
                    }

                      

                }
                return response()->json(['status' => 'SUCCESS', 'message' => 'Exams saved successfully']);
            }   else {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid Exams']);
            }
        // }   else {
        //     return response()->json(['status' => 'FAILED', 'message' => 'Invalid Class and Section']);
        // }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Exams has been saved'], 201);
     }


    // public function postExams(Request $request)
    // {
    //     if (Auth::check()) {
    //         $id = $request->id;
    //         $class_ids = $request->class_ids;
    //         $monthyear = $request->monthyear;
    //         $exam_name = $request->exam_name;
    //         $exam_startdate = $request->exam_startdate;
    //         $exam_enddate = $request->exam_enddate;

    //         $status = $request->status;

    //         $validator = Validator::make($request->all(), [
    //             'class_ids' => 'required',
    //             'monthyear' => 'required',
    //             'exam_name' => 'required',
    //             'exam_startdate' => 'required',
    //             'exam_enddate' => 'required',
    //             'status' => 'required',
    //         ]);

    //         if ($validator->fails()) {

    //             $msg = $validator->errors()->all();

    //             return response()->json([

    //                 'status' => 0,
    //                 'message' => "Please check your all inputs " . implode(', ', $msg),
    //             ]);
    //         }
    //         $exam_startdate = date('Y-m-d', strtotime($exam_startdate));
    //         $exam_enddate = date('Y-m-d', strtotime($exam_enddate));
    //         if ($id > 0) {
    //             $exams = Exams::find($id);
    //         } else {
    //             $exams = new Exams();
    //         }

    //         if (is_array($class_ids) && count($class_ids) > 0) {
    //             $class_ids = implode(',', $class_ids);
    //         } else {
    //             return response()->json([

    //                 'status' => 0,
    //                 'message' => "Please select the classes ",
    //             ]);
    //         }
    //         $exams->class_ids = $class_ids;
    //         $exams->monthyear = $monthyear;
    //         $exams->exam_name = $exam_name;
    //         $exams->exam_startdate = $exam_startdate;
    //         $exams->exam_enddate = $exam_enddate;
    //         $exams->status = $status;

    //         $exams->save();
    //         return response()->json(['status' => 1, 'message' => 'Exam Saved Successfully']);
    //     } else {
    //         return redirect('/admin/login');
    //     }
    // }

    public function editExamsold20423($monthyear,$exam_name)
    {
        if (Auth::check()) {
            $exams = Exams::where('monthyear', $monthyear)->where('exam_name',$exam_name)->get();
            $exam = Exams::where('monthyear', $monthyear)->where('exam_name',$exam_name)->groupby('exam_name')->first();
            if ($exams->isNotEmpty()) {
                // echo "<pre>";print_r($exams);
                $classes = Classes::where('status', 'ACTIVE')->orderby('position','asc')->get();
              return view('admin.editexam')->with('start_date','')->with('end_date','')->with('class', '')->with('periods', '')->with('classes', '')->with('subjects', '')->with('class_id', '')->with('timetable', '')->with(['exams'=>$exams])->with('exam',$exam);
                // return view('admin.editexam')->with(['exams'=>$exams])->with('exam',$exam);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Exam Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function editExams(Request $request)
    {
        if (Auth::check()) {
            $exam = '';$examsarr = [];
            $exam_id = $request->get('id');
            $exams = Exams::where('id', $exam_id)->get(); 
            if ($exams->isNotEmpty()) { 
                $examsarr = $exams->toArray();
                $exam = $exams[0];
                $examsarr = $examsarr[0];
                $classes = Classes::where('status', 'ACTIVE')->orderby('position','asc')->get();

               // echo "<pre>"; print_r($examsarr);  exit;
              return view('admin.editexam')->with('start_date','')->with('end_date','')
                ->with('class', '')->with('periods', '')->with('classes', '')
                ->with('subjects', '')->with('class_id', '')->with('exam_id', $exam_id)
                ->with('timetable', '')->with(['exams'=>$exams])->with('exam',$exam)->with('examsarr',$examsarr);
                // return view('admin.editexam')->with(['exams'=>$exams])->with('exam',$exam);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Exam Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    
    public function previewExams(Request $request)
    {
        if (Auth::check()) {
            $exam_id = $request->get('id');
            $exams = Exams::where('id', $exam_id)->get();  
            if ($exams->isNotEmpty()) { 
                $examsarr = $exams->toArray();
                $exam = $exams[0];
                $examsarr = $examsarr[0];
                $classes = Classes::where('status', 'ACTIVE');
                if(Auth::User()->user_type == 'SCHOOL') {
                    $classes->where('school_id', Auth::User()->id); 
                }
                $classes = $classes->orderby('position','asc')->get();
                return view('admin.previewexam')->with('start_date','')->with('end_date','')
                    ->with('class', '')->with('periods', '')->with('classes', '')->with('subjects', '')
                    ->with('class_id', '')->with('timetable', '')->with(['exams'=>$exams])
                    ->with('exam',$exam)->with('examsarr',$examsarr)->with('exam_id', $exam_id); 
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Exam Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function previewExamsold20423($monthyear,$exam_name)
    {
        if (Auth::check()) {
            $exams = Exams::where('monthyear', $monthyear)->where('exam_name',$exam_name)->get();
            $exam = Exams::where('monthyear', $monthyear)->where('exam_name',$exam_name)->groupby('exam_name')->first();
            if ($exams->isNotEmpty()) {
                // echo "<pre>";print_r($exams);
                $classes = Classes::where('status', 'ACTIVE')->orderby('position','asc')->get();
              return view('admin.previewexam')->with('start_date','')->with('end_date','')->with('class', '')->with('periods', '')->with('classes', '')->with('subjects', '')->with('class_id', '')->with('timetable', '')->with(['exams'=>$exams])->with('exam',$exam);
                // return view('admin.editexam')->with(['exams'=>$exams])->with('exam',$exam);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Exam Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }


    //Terms
    /* Function: viewTerms
     */
    public function viewTerms()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            return view('admin.termsacademics')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getTerms
    Datatable Load
     */
    public function getTerms(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status',0);
            $cls_id = $request->get('cls_id','');

            $termsqry = Terms::select('terms.*');
            if($status != ''){
            $termsqry->where('status','=',$status);
            }
            $filteredqry = Terms::select('terms.*');
            if($status != ''){
            $filteredqry->where('status','=',$status);
            }

            if($cls_id != ''){
                $termsqry->whereRAW(' FIND_IN_SET('.$request->cls_id.', class_ids) ');
                $filteredqry->whereRAW(' FIND_IN_SET('.$request->cls_id.', class_ids) ');
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $termsqry->where('terms.school_id', Auth::User()->id);
                $filteredqry->where('terms.school_id', Auth::User()->id);
            }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'terms.status') {
                            $termsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $termsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'terms.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('terms.id')->count();

            $totalDataqry = Terms::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('terms.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($terms)) {
                foreach ($terms as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: postTerms
    Save into em_countries table
     */
    public function postTerms(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $class_ids = $request->class_ids;
            $term_name = $request->term_name;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'class_ids' => 'required',
                'term_name' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('terms')->where('term_name', $term_name)->where('school_id', Auth::User()->id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('terms')->where('term_name', $term_name)->where('school_id', Auth::User()->id)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 0, 'message' => 'Term Already Exists'], 201);
            }


            if ($id > 0) {
                $terms = Terms::find($id);
            } else {
                $terms = new Terms();
            }

            if (is_array($class_ids) && count($class_ids) > 0) {
                $class_ids = implode(',', $class_ids);
            } else {
                return response()->json([

                    'status' => 0,
                    'message' => "Please select the classes ",
                ]);
            }
            $terms->school_id = Auth::User()->id;
            $terms->class_ids = $class_ids;
            $terms->term_name = $term_name;
            $terms->status = $status;

            $terms->save();
            return response()->json(['status' => 1, 'message' => 'Term Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editTerms(Request $request)
    {
        if (Auth::check()) {
            $terms = Terms::where('id', $request->code)->get();
            if ($terms->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $terms[0], 'message' => 'Term Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Term Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //Marks Entry Management
    /* Function: viewMarksEntry
     */
    public function viewMarksEntry(Request $request)   {
        if(Auth::check()){
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            $monthyear = $request->get('monthyear', '');
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);
            $exam_id = $request->get('exam_id', 0);
            $subject_id = $request->get('subject_id', 0);
            if(empty($monthyear)) {
                $monthyear = date('Y-m');
            }
            $students = [];

            return view('admin.marksentry')->with(['students'=>$students,
                'monthyear'=>$monthyear, 'classes'=>$classes, 'class_id'=>$class_id]);
        }else{
            return redirect('/login');
        }
    }

    /* Function: updateMarksEntry
     */
    public function updateMarksEntry(Request $request)
    {
        if(Auth::check()){
            $monthyear = $request->monthyear;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $exam_id = $request->exam_id;
            $subject_id = $request->subject_id;
            $subject = $request->subject;
            $total_marks = $request->total_marks;
            $marks = $request->marks;
            $remarks = $request->remarks;
            //$grade = $request->grade;
            $student_id = $request->student_id;
            $validator = Validator::make($request->all(), [
                'monthyear' => 'required',
                'class_id' => 'required',
                'section_id' => 'required',
                'exam_id' => 'required',
                // 'subject_id' => 'required',
                'total_marks' => 'required',
                'marks' => 'required',
                //'remarks' => 'required',
                //'grade' => 'required',
                'student_id' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([
                    'status' => 0,
                    'message' => implode(', ', $msg)
                ]);
            }

            if($student_id > 0) {

                $ex = DB::table('marks_entry')->where('user_id', $student_id)
                    ->where(['class_id'=>$class_id, 'section_id'=>$section_id, 'exam_id'=>$exam_id,
                            'monthyear'=>$monthyear])->first();
                //'subject_id'=>$subject_id
                $grade = '';
                $gr = DB::table('grades')->where('mark', '<=', $marks)->orderby('mark', 'desc')->first();
                if(!empty($gr)) {
                    $grade = $gr->grade;
                }
                $data = ['subject_id'=>$subject, 'total_marks'=>$total_marks,
                        'marks'=>$marks, 'remarks'=>$remarks, 'grade'=>$grade];
                if(!empty($ex)) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $data['updated_by'] = Auth::User()->id;

                    $mark_entry_id = $ex->id;

                }   else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['created_by'] = Auth::User()->id;

                    $mark_entry_id = DB::table('marks_entry')->insertGetId([
                        'user_id'=>$student_id,  'monthyear'=>$monthyear,
                        'class_id'=>$class_id, 'section_id'=>$section_id,
                        'exam_id'=>$exam_id,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
                
                // if($subject_id != ''){
                    $exentry = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)
                    ->where('subject_id', $subject)->first();
                // }
                // else{
                //     $exentry = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->first();
                // }
                

                if(!empty($exentry)) {
                    DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->where('subject_id', $subject)->update($data);
                }   else {
                    $data['mark_entry_id'] = $mark_entry_id;
                    DB::table('marks_entry_items')->insert($data);
                }

                $total_marks = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->sum('total_marks');
                $marks = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->sum('marks');
                $cnt = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->count('id');

                $remarks = $grade = $pass_type = '';
                if($cnt > 0) {
                    $avg = $marks / $total_marks;

                    $grade = '';
                    $gr = DB::table('grades')->where('mark', '<=', $marks)->orderby('mark', 'desc')->first();
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
                              'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id
                    ]);

                return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
            }

            return response()->json(['status' => 0, 'message' => 'Invalid inputs']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }
    }

    /* Function: updateAllMarksEntry
     */
    public function updateAllMarksEntry(Request $request)
    {
        if(Auth::check()){

            $input = $request->all();   

            $monthyear = $request->monthyear;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $exam_id = $request->exam_id;

            $total_marks1 = $request->total_marks;
            $marks1 = $request->marks;
            $remarks1 = $request->remarks;
            //$grade = $request->grade;
            //echo "<pre>"; print_r($total_marks); print_r($marks);  print_r($remarks);
            $validator = Validator::make($request->all(), [
                'monthyear' => 'required',
                'class_id' => 'required',
                'section_id' => 'required',
                'exam_id' => 'required',
                // 'subject_id' => 'required',
                'total_marks' => 'required',
                'marks' => 'required',
                //'remarks' => 'required',
                //'grade' => 'required',
                //'student_id' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([
                    'status' => 0,
                    'message' => implode(', ', $msg)
                ]);
            } 
            $log = DB::enableQueryLog();   $error = '';
            if(!empty($total_marks1) && count($total_marks1)>0) {
                foreach($total_marks1 as $student_id => $subs) {
                    if(!empty($subs) && count($subs)>0) {
                        foreach($subs as $subject => $total_mark) {
                            if($student_id > 0) {

                                $total_marks = $total_marks1[$student_id][$subject];
                                $marks = $marks1[$student_id][$subject];
                                $remarks = $remarks1[$student_id][$subject]; 

                                $ex = DB::table('marks_entry')->where('user_id', $student_id)
                                    ->where(['class_id'=>$class_id, 'section_id'=>$section_id, 'exam_id'=>$exam_id,
                                            'monthyear'=>$monthyear])->first();
                                //'subject_id'=>$subject_id
                                $grade = ''; 
                                if($marks > 0) {
                                    $gr = DB::table('grades')->where('mark', '<=', $marks)->orderby('mark', 'desc')->first();
                                    if(!empty($gr)) {
                                        $grade = $gr->grade;
                                    }
                                }

                                if($marks <= $total_marks) {
                                }   else {
                                    $error =  '  Entered mark should not be greater than Total Marks';
                                }

                                $data = ['subject_id'=>$subject, 'total_marks'=>$total_marks,
                                        'marks'=>$marks, 'remarks'=>$remarks, 'grade'=>$grade];
                                if(!empty($ex)) {
                                    $data['updated_at'] = date('Y-m-d H:i:s');
                                    $data['updated_by'] = Auth::User()->id;

                                    $mark_entry_id = $ex->id;

                                }   else {
                                    $data['created_at'] = date('Y-m-d H:i:s');
                                    $data['created_by'] = Auth::User()->id;

                                    $mark_entry_id = DB::table('marks_entry')->insertGetId([
                                        'user_id'=>$student_id,  'monthyear'=>$monthyear,
                                        'class_id'=>$class_id, 'section_id'=>$section_id,
                                        'exam_id'=>$exam_id,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        'created_by'=>Auth::User()->id
                                    ]);
                                }
                                
                                // if($subject_id != ''){
                                    $exentry = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)
                                    ->where('subject_id', $subject)->first();
                                // }
                                // else{
                                //     $exentry = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->first();
                                // }
                                 
                                if(empty($error)) {
                                    if(!empty($exentry)) { 
                                        DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->where('subject_id', $subject)->update($data);
                                    }   else { 
                                        $data['mark_entry_id'] = $mark_entry_id;
                                        DB::table('marks_entry_items')->insert($data);
                                    }
                                }

                                $total_marks = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->sum('total_marks');
                                $marks = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->sum('marks');
                                $cnt = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)->count('id');

                                $remarks = $grade = $pass_type = '';
                                if($cnt > 0) {
                                    $avg = $marks / $total_marks;

                                    $grade = '';
                                    $gr = DB::table('grades')->where('mark', '<=', $marks)->orderby('mark', 'desc')->first();
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
                                              'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id
                                    ]); 
                            }
                        }
                    }
                }
            } //$queries = DB::getQueryLog();  echo "<pre>"; print_r($queries);
            if(!empty($error)) {
                return response()->json(['status' => 0, 'message' => 'Entered mark should not be greater than Total Marks']);
            }
            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);

            return response()->json(['status' => 0, 'message' => 'Invalid inputs']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }
    }

    //MarksEntry Management
    /* Function: loadMarksEntry
     */
    public function loadMarksEntry(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the Year']);
            }
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);
            $exam_id = $request->get('exam_id', 0);
            $subject_id = $request->get('subject_id', 0);
            $student_id = $request->get('student_id',0);
            $total_marks = $request->get('total_marks',0);
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
                ->select('users.id', 'name', 'email', 'mobile', 'students.admission_no')->get();
 
             
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
                        ->where('exams.id',$exam_id)->where('subjects.id','>',0);
                if($subject_id  >0){
                    $subjects->where('exam_sessions.subject_id',$subject_id);
                }
                if($class_id  >0){
                    $subjects->where('exam_sessions.class_id',$class_id);
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
                // echo "<pre>"; print_r($subjects);exit;
               
                $html = view('admin.loadmarksentry')->with(['monthyear'=>$monthyear, 'students'=>$students, 'subjects'=>$subjects, 'totalmarks'=>$total_marks])->render();

                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students Detail']);

            }   else {
                $students = [];
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students Detail']);
            }
        }else{
            return redirect('/login');
        }
    }

    //Question Banks
    /* Function: viewQuestionbank
     */
    public function viewQuestionbank()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $subject = Subjects::where('id', '>', 0)->orderby('position','asc')->get();
            return view('admin.questionbank')->with(['class'=>$classes, 'subject'=>$subject]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getQuestionbank
    Datatable Load
     */
    public function getQuestionbank(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $class_id = (isset($input['class_id'])) ? $input['class_id'] : 0 ;
            $subject_id = (isset($input['subject_id'])) ? $input['subject_id'] : 0;
            $term_id = (isset($input['term_id'])) ? $input['term_id'] : 0;

            $termsqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->where('question_banks.deleted_status',0)
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername','terms.term_name');
            $filteredqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->where('question_banks.deleted_status',0)
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername','terms.term_name');

            if($class_id>0) {
                $termsqry->where('question_banks.class_id', $class_id);
                $filteredqry->where('question_banks.class_id', $class_id);
            }
            if($subject_id>0) {
                $termsqry->where('question_banks.subject_id', $subject_id);
                $filteredqry->where('question_banks.subject_id', $subject_id);
            }
            if($term_id>0) {
                $termsqry->where('question_banks.term_id', $term_id);
                $filteredqry->where('question_banks.term_id', $term_id);
            }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'question_banks.status') {
                            $termsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $termsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $termsqry->where('question_banks.school_id', Auth::User()->id);
                $filteredqry->where('question_banks.school_id', Auth::User()->id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'question_banks.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('question_banks.id')->count();

            $totalDataqry = QuestionBanks::where('deleted_status',0)->orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('question_banks.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($terms)) {
                foreach ($terms as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    public function checkChapterQb(Request $request) {
        if (Auth::check()) {
            $class_id = $request->get('class_id',0);  
            $subject_id = $request->get('subject_id',0);
            $chapter_id = $request->get('chapter_id',0);
            $qb_id = $request->get('qb_id',0);

            if ($qb_id > 0) {
                $exists = DB::table('question_banks')->where('class_id', $class_id)
                    ->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)
                    ->where('status', 'ACTIVE')->where('deleted_status', 0)
                    ->whereNotIn('id', [$qb_id])->first();
            } else {
                $exists = DB::table('question_banks')->where('class_id', $class_id)
                    ->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)
                    ->where('status', 'ACTIVE')->where('deleted_status', 0)
                    ->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Question bank for this chapter Already Exists'], 201);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function addQuestionbank(Request $request)
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $question_types = QuestionTypes::with('questiontype_settings')
                ->where('status','ACTIVE')->orderby('position', 'Asc')->get();
            if($question_types->isNotEmpty()) {
                $question_types = $question_types->toArray();
            }
            //echo "<pre>"; print_r($question_types); exit;
            return view('admin.addquestionbank')->with('classes', $classes)->with('question_types', $question_types);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editQuestionbank(Request $request)
    {
        if (Auth::check()) {
            $id = $request->get('id');
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $question_types = QuestionTypes::with('questiontype_settings')
                ->where('status','ACTIVE')->orderby('position', 'Asc')->get();
            if($question_types->isNotEmpty()) {
                $question_types = $question_types->toArray();
            }
            QuestionBankItems::$admin = 1;
            if($id> 0) {
                $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->where('question_banks.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }

            //echo "<pre>"; print_r($question_types); exit;
            return view('admin.editquestionbank')->with('classes', $classes)
                ->with('question_types', $question_types)->with('qb', $qb)->with('id', $id);
        } else {
            return redirect('/admin/login');
        }
    }


    public function deleteQuestionBank(Request $request)
    {
        if (Auth::check()) {
            $id = $request->get('id',0);
        if($id> 0) {
                $qb = DB::table('question_banks')->where('id',$id)->update(['deleted_status' => 1,'updated_by' => Auth::user()->id,'updated_at' => date('Y-m-d H:i:s')]);
          if($qb) {
         $qb = DB::table('question_bank_items')->where('question_bank_id',$id)->update(['deleted_status' => 1,'updated_by' => Auth::user()->id,'updated_at' => date('Y-m-d H:i:s')]);
               
          }
              return response()->json(['status' => 1, 'data' => null, 'message' => 'Question Bank Deleted Successfully']);
            } else {
              return response()->json(['status' => 0, 'data' => [], 'message' => 'No Question Bank Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function deleteIndividualQuestionBank(Request $request){
        if (Auth::check()) {
          $item_id = $request->get('item_id','');
          $qb = DB::table('question_bank_items')->where('id',$item_id)->update(['deleted_status'=> 1, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
         if($qb){
            return response()->json(['status' => 'SUCCESS', 'data' => null, 'message' => 'Question Deleted Successfully']);
         }
         else{
            return response()->json(['status' => 'FAILED', 'data' => null, 'message' => 'Unable to Delete the Question']);
         }
           
           
        } else {
            return redirect('/admin/login');
        }
    }

    public function cloneQuestiontype(Request $request) {
        if(Auth::check()){
            $html = ''; $i = 2;
            $qtype = $request->get('code', 0);
            $i = $request->get('i', 1);
            $i++;
            if($qtype > 0) {
                $question_types = QuestionTypes::with('questiontype_settings')
                    ->where('status','ACTIVE')->where('id',$qtype)->get();
                if($question_types->isNotEmpty()) {
                    $question_types = $question_types->toArray();
                //   echo "<pre>";  print_r($question_types);
                //     exit;
                    foreach($question_types as $qtype) {
                        if(isset($qtype['questiontype_settings'])) {
                            $html = view('admin.loadquestiontype')->with('qtype', $qtype)->with('i', $i)->render();
                            return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Clone Detail']);
                        }   else {
                            $html = view('admin.loadquestiontype')->with('qtype', $qtype)->with('i', $i)->render();
                            return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Clone Detail']);
                        }
                    }
                }
            }
            return response()->json(['status' => 'FAILED', 'data' => $html, 'message' => 'No Clone Detail']);
        }   else {
            return response()->json(['status' => 'FAILED', 'data' => $html, 'message' => 'No Clone Detail']);
        }
    }

    /* Function: postQuestionbank
      */
      public function postQuestionbank(Request $request)
      {
          if (Auth::check()) {
              $id = $request->id;
              $class_id = $request->class_id;
              $subject_id = $request->subject_id;
              $chapter_id = $request->chapter_id;
              $term_id = $request->term_id;
              $qb_name = $request->qb_name;
              $notes = $request->file('notes');
              $qb_notes = $request->get('notes_file'); 
          //     if(!empty($notes)){
          //     $notes = time().'.'.$request->notes; 
          //    $request->notes->move(public_path('/image/notes'), $notes);
          //     }
          //     else{
          //       $notes = $qb_notes;
          //     }
            
  
              if (!empty($notes)) {
  
                  $notes = rand() . time() . '.' . $notes->getClientOriginalExtension();
  
                  $request->notes->move(public_path('/image/notes'), $notes);
  
                  }
                  else{
                      $notes = $qb_notes;
                    }
  
              $validator = Validator::make($request->all(), [
                  'class_id' => 'required',
                  'subject_id' => 'required',
                  'chapter_id' => 'required',
                  'term_id' => 'required',
                  'qb_name' => 'required',
              ]);
  
              if ($validator->fails()) {
  
                  $msg = $validator->errors()->all();
  
                  return response()->json([
  
                      'status' => "FAILED",
                      'message' => "Please check inputs " . implode(', ', $msg),
                  ]);
              }

                

              $input = $request->all(); 
                // echo "<pre>"; print_r($input); exit;
              $question_bank_id = $request->get('question_bank_id', 0);

                if ($question_bank_id > 0) {
                    $exists = DB::table('question_banks')->where('class_id', $class_id)
                        ->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)
                        ->where('status', 'ACTIVE')->where('deleted_status', 0)
                        ->whereNotIn('id', [$question_bank_id])->first();
                } else {
                    $exists = DB::table('question_banks')->where('class_id', $class_id)
                        ->where('subject_id', $subject_id)->where('chapter_id', $chapter_id)
                        ->where('status', 'ACTIVE')->where('deleted_status', 0)
                        ->first();
                }

                if (!empty($exists)) { 
                    return response()->json(['status' => 'FAILED', 'message' => 'Question bank for this chapter Already Exists'], 201);
                }

              $qb_item_id = $request->get('qb_item_id', []);
              $oqb_item_id = $request->get('oqb_item_id', []);
              $question = $request->get('question', []);
              $choose_question = $request->file('choose_question', []);
              $choose_question1 = $request->get('choose_question1', []);
              $answer = $request->get('answer', []);
              $display_answer = $request->get('display_answer', []);
              $option_1 = $request->get('option_1', []);
              $option_2 = $request->get('option_2', []);
              $option_3 = $request->get('option_3', []);
              $option_4 = $request->get('option_4', []);
              $choose_1 = $request->file('choose_1', []);
              $choose_2 = $request->file('choose_2', []);
              $choose_3 = $request->file('choose_3', []);
              $choose_4 = $request->file('choose_4', []); 
              $hint_file = $request->file('hint_file', []);

              
              $oquestion_type = $request->get('oquestion_type', []); 
              $oquestion = $request->get('oquestion', []);
              $oanswer = $request->get('oanswer', []);
              $ohint_file = $request->file('ohint_file', []);  

              $accepted_formats = ['jpeg', 'jpg', 'png'];

                if(is_array($question) && count($question)> 0) { 
                    foreach($question as $qtype=>$qtn) {                  
                        if(is_array($qtn) && count($qtn)> 0) {
                            foreach($qtn as $kq=>$quest) {


                if(is_array($hint_file) && count($hint_file)> 0) {
                    if(isset($hint_file[$qtype]) && isset($hint_file[$qtype][$kq]) && !empty($hint_file[$qtype][$kq])) {  

                      $up = $hint_file[$qtype][$kq];
                      $ext = $up->getClientOriginalExtension();
                      if (!in_array($ext, $accepted_formats)) {
                          return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg in Hint file']);
                      }
                    }
                }

                if(isset($choose_question[$qtype]) && isset($choose_question[$qtype][$kq]) && !empty($choose_question[$qtype][$kq])) {
                    $ext = $choose_question[$qtype][$kq]->getClientOriginalExtension();
                    $countryimg = rand() . time() . '.' . $choose_question[$qtype][$kq]->getClientOriginalExtension();
                    if(!in_array($ext,$accepted_formats)){
                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..! in Choose']);
                    }
                }

                if(isset($choose_1[$qtype]) && isset($choose_1[$qtype][$kq]) && !empty($choose_1[$qtype][$kq])) {
                    $ext = $choose_1[$qtype][$kq]->getClientOriginalExtension();
                    $countryimg = rand() . time() . '.' . $choose_1[$qtype][$kq]->getClientOriginalExtension();
                    if(!in_array($ext,$accepted_formats)){
                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..! in Choose']);
                    }
                }

                if(isset($choose_2[$qtype]) && isset($choose_2[$qtype][$kq]) && !empty($choose_2[$qtype][$kq])) {
                    $ext = $choose_2[$qtype][$kq]->getClientOriginalExtension();
                    $countryimg = rand() . time() . '.' . $choose_2[$qtype][$kq]->getClientOriginalExtension();
                    if(!in_array($ext,$accepted_formats)){
                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..! in Choose']);
                    }
                }

                if(isset($choose_3[$qtype]) && isset($choose_3[$qtype][$kq]) && !empty($choose_3[$qtype][$kq])) {
                    $ext = $choose_3[$qtype][$kq]->getClientOriginalExtension();
                    $countryimg = rand() . time() . '.' . $choose_3[$qtype][$kq]->getClientOriginalExtension();
                    if(!in_array($ext,$accepted_formats)){
                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..! in Choose']);
                    }
                }

                if(isset($choose_4[$qtype]) && isset($choose_4[$qtype][$kq]) && !empty($choose_4[$qtype][$kq])) {
                    $ext = $choose_4[$qtype][$kq]->getClientOriginalExtension();
                    $countryimg = rand() . time() . '.' . $choose_4[$qtype][$kq]->getClientOriginalExtension();
                    if(!in_array($ext,$accepted_formats)){
                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..! in Choose']);
                    }
                }

                if(is_array($ohint_file) && count($ohint_file)> 0) {
                    if(isset($ohint_file[$qtype]) && isset($ohint_file[$qtype][$kq]) && !empty($ohint_file[$qtype][$kq])) {  

                      $up = $ohint_file[$qtype][$kq];
                      $ext = $up->getClientOriginalExtension();
                      if (!in_array($ext, $accepted_formats)) {
                          return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg ']);
                      }
                    }
                }

                            }
                        }
                    }
                }


              $question_file = $request->file('question_file');  //echo "<pre>"; print_r($question_file); exit;
  
              $qb_data = ['term_id' => $term_id, 'class_id' => $class_id, 'subject_id' => $subject_id, 'chapter_id' => $chapter_id,'qb_name'=>$qb_name,'notes' => $notes,'status' => 'ACTIVE'];
              if($question_bank_id > 0) {
                  $qb_data['updated_at'] = date('Y-m-d H:i:s');
                  $qb_data['updated_by'] = Auth::User()->id;
                  $qb_data['school_id'] = Auth::User()->id;
                  DB::table('question_banks')->where('id', $question_bank_id)->update($qb_data);
              }   else {
                  $qb_data['created_at'] = date('Y-m-d H:i:s');
                  $qb_data['created_by'] = Auth::User()->id;
                  $qb_data['school_id'] = Auth::User()->id;  
                  $question_bank_id = DB::table('question_banks')->insertGetId($qb_data);
              }

              if(is_array($question) && count($question)> 0) {
                // echo "image";
              foreach($question as $qtype=>$qtn) {                  
                      if(is_array($qtn) && count($qtn)> 0) {
                          foreach($qtn as $kq=>$quest) {
                            // echo "choose";
                              if(!empty($quest)){
                                  $row = [];
                                  $row['question_type'] = $qtype;

                                    if(is_array($hint_file) && count($hint_file)> 0) {
                                        if(isset($hint_file[$qtype]) && isset($hint_file[$qtype][$kq]) && !empty($hint_file[$qtype][$kq])) {  

                                          $up = $hint_file[$qtype][$kq];
                                          $ext = $up->getClientOriginalExtension();
                                          if (!in_array($ext, $accepted_formats)) {
                                              return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg ']);
                                          }
      
                                          $topicimg = rand() . time() . '.' . $up->getClientOriginalExtension();
      
                                          $destinationPath = public_path('/image/qb');
      
                                          $up->move($destinationPath, $topicimg);
      
                                          $row['hint_file'] = $topicimg;  
                                        }
                                    }          


                                if(isset($answer[$qtype]) && isset($answer[$qtype][$kq]) && !empty($answer[$qtype][$kq])) {
                                      $row['answer'] = $answer[$qtype][$kq];
                                  }   else {
                                      $arr = [8,9,10,11];
                                      // Missing letters, jumbled words, jumbled letters, Dictation
                                      if(in_array($qtype, $arr)) {
                                          $row['answer'] = $quest;
                                      }   else {
                                          $row['answer'] = '';
                                      }
                                  }
                               
                            
                               if(isset($question[$qtype]) && isset($question[$qtype][$kq]) && !empty($question[$qtype][$kq])) {
                                    $row['question'] = $question[$qtype][$kq];
                                }   else {
                                    $row['question'] = '';
                                }

                                  if(isset($option_1[$qtype]) && isset($option_1[$qtype][$kq]) && !empty($option_1[$qtype][$kq])) {
                                      $row['option_1'] = $option_1[$qtype][$kq];
                                  }   else {
                                      $row['option_1'] = '';
                                  }
                                  if(isset($option_2[$qtype]) && isset($option_2[$qtype][$kq]) && !empty($option_2[$qtype][$kq])) {
                                      $row['option_2'] = $option_2[$qtype][$kq];
                                  }   else {
                                      $row['option_2'] = '';
                                  }
                                  if(isset($option_3[$qtype]) && isset($option_3[$qtype][$kq]) && !empty($option_3[$qtype][$kq])) {
                                      $row['option_3'] = $option_3[$qtype][$kq];
                                  }   else {
                                      $row['option_3'] = '';
                                  }
                                  if(isset($option_4[$qtype]) && isset($option_4[$qtype][$kq]) && !empty($option_4[$qtype][$kq])) {
                                      $row['option_4'] = $option_4[$qtype][$kq];
                                  }   else {
                                      $row['option_4'] = '';
                                  }
                          
                             
                                  if(isset($display_answer[$qtype]) && isset($display_answer[$qtype][$kq]) && !empty($display_answer[$qtype][$kq])) {
                                      // $row['display_answer'] = $display_answer[$qtype][$kq];
                                      $row['display_answer'] = $answer[$qtype][$kq];
                                  }   else {
                                      $row['display_answer'] = '';
                                  }
                                  
                                  $row['question_bank_id'] = $question_bank_id;
                                  $row['question_type_id'] = $qtype;
                                  $row['question_type'] = DB::table('question_types')->where('id', $qtype)->value('question_type');
  
                                  if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
                                      $row['updated_by'] = Auth::User()->id;
                                      $row['updated_at'] = date('Y-m-d H:i:s');
  
                                      DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
                                  }   else {
                                      $row['created_by'] = Auth::User()->id;
                                      $row['created_at'] = date('Y-m-d H:i:s');
  
                                      DB::table('question_bank_items')->insert($row);
                                  }
                              }
                          }
                      }
                  }
              } 
          if(is_array($choose_question) && count($choose_question)> 0) { 
            foreach($choose_question as $qtype=>$qtn) {
             if(is_array($qtn) && count($qtn)> 0) {
                        foreach($qtn as $kq=>$quest) {
                            if(!empty($quest))  {
 
                                $row = [];
                                $row['question_type'] = $qtype;
                                
                               if(isset($answer[$qtype]) && isset($answer[$qtype][$kq]) && !empty($answer[$qtype][$kq])) {
                                    $row['answer'] = $answer[$qtype][$kq];
                                }   else {
                                    $arr = [8,9,10,11];
                                    // Missing letters, jumbled words, jumbled letters, Dictation
                                    if(in_array($qtype, $arr)) {
                                        $row['answer'] = $quest;
                                    }   else {
                                        $row['answer'] = '';
                                    }
                                }
                             
                        
                              if(isset($choose_question[$qtype]) && isset($choose_question[$qtype][$kq]) && !empty($choose_question[$qtype][$kq])) {
                                $ext = $choose_question[$qtype][$kq]->getClientOriginalExtension();
                                   $countryimg = rand() . time() . '.' . $choose_question[$qtype][$kq]->getClientOriginalExtension();
                                   if(!in_array($ext,$accepted_formats)){
                                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..!']);
                                   }
                                   
                    
                                    $destinationPath = public_path('/image/questionbank');
                            
                                    $choose_question[$qtype][$kq]->move($destinationPath, $countryimg);
                            
                                    $row['question'] = $countryimg;
                                }   else {
                                    $row['question'] = '';
                                }

                                if(isset($choose_1[$qtype]) && isset($choose_1[$qtype][$kq]) && !empty($choose_1[$qtype][$kq])) {
                                $ext = $choose_1[$qtype][$kq]->getClientOriginalExtension();
                                $countryimg = rand() . time() . '.' . $choose_1[$qtype][$kq]->getClientOriginalExtension();

                                if(!in_array($ext,$accepted_formats)){
                                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..!']);
                                   }
                        
                                $destinationPath = public_path('/image/questionbank');
                        
                                $choose_1[$qtype][$kq]->move($destinationPath, $countryimg);
                        
                                  $row['option_1']= $countryimg;
                                 }  
                                if(isset($choose_2[$qtype]) && isset($choose_2[$qtype][$kq]) && !empty($choose_2[$qtype][$kq])) {
                                    $ext = $choose_2[$qtype][$kq]->getClientOriginalExtension();
                                    if(!in_array($ext,$accepted_formats)){
                                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..!']);
                                       }
                               $countryimg = rand() . time() . '.' . $choose_2[$qtype][$kq]->getClientOriginalExtension();
                        
                               $destinationPath = public_path('/image/questionbank');
                       
                               $choose_2[$qtype][$kq]->move($destinationPath, $countryimg);
                       
                                $row['option_2']= $countryimg;
                                } 
                                if(isset($choose_3[$qtype]) && isset($choose_3[$qtype][$kq]) && !empty($choose_3[$qtype][$kq])) {

                                    $ext = $choose_3[$qtype][$kq]->getClientOriginalExtension();

                                    if(!in_array($ext,$accepted_formats)){
                                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..!']);
                                       }

                                    $countryimg = rand() . time() . '.' . $choose_3[$qtype][$kq]->getClientOriginalExtension();
                        
                                    $destinationPath = public_path('/image/questionbank');
                            
                                    $choose_3[$qtype][$kq]->move($destinationPath, $countryimg);
                            
                                    $row['option_3']= $countryimg;
                                }   
                                if(isset($choose_4[$qtype]) && isset($choose_4[$qtype][$kq]) && !empty($choose_4[$qtype][$kq])) {
                                    $ext = $choose_4[$qtype][$kq]->getClientOriginalExtension();

                                    if(!in_array($ext,$accepted_formats)){
                                        return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg Formats Only..!']);
                                       }


                                    $countryimg = rand() . time() . '.' . $choose_4[$qtype][$kq]->getClientOriginalExtension();
                        
                                    $destinationPath = public_path('/image/questionbank');
                            
                                    $choose_4[$qtype][$kq]->move($destinationPath, $countryimg);
                                    $row['option_4']= $countryimg;
                                } 
                               if(isset($display_answer[$qtype]) && isset($display_answer[$qtype][$kq]) && !empty($display_answer[$qtype][$kq])) {
                                    // $row['display_answer'] = $display_answer[$qtype][$kq];
                                    $row['display_answer'] = $answer[$qtype][$kq];
                                }   else {
                                    $row['display_answer'] = '';
                                }

                                if(is_array($hint_file) && count($hint_file)> 0) {
                                    if(isset($hint_file[$qtype]) && isset($hint_file[$qtype][$kq]) && !empty($hint_file[$qtype][$kq])) {  

                                      $up = $hint_file[$qtype][$kq];
                                      $ext = $up->getClientOriginalExtension();
                                      if (!in_array($ext, $accepted_formats)) {
                                          return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg ']);
                                      }
  
                                     $topicimg = rand() . time() . '.' . $up->getClientOriginalExtension();
  
                                      $destinationPath = public_path('/image/qb');
  
                                      $up->move($destinationPath, $topicimg);
  
                                      $row['hint_file'] = $topicimg;  
                                    }
                                }   

                                
                                $row['question_bank_id'] = $question_bank_id;
                                $row['question_type_id'] = $qtype;
                                $row['question_type'] = DB::table('question_types')->where('id', $qtype)->value('question_type');
                           if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
                                    $row['updated_by'] = Auth::User()->id;
                                    $row['updated_at'] = date('Y-m-d H:i:s');

                                    DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
                                }   else {
                                    $row['created_by'] = Auth::User()->id;
                                    $row['created_at'] = date('Y-m-d H:i:s');

                                    DB::table('question_bank_items')->insert($row);
                                }
                            }
                        }
                    }
                }
            }
            // print_r($choose_question1);
            // exit;

            if(is_array($choose_question1) && count($choose_question1)> 0) {
                foreach($choose_question1 as $qtype=>$qtn) {
                   if(is_array($qtn) && count($qtn)> 0) {
                              foreach($qtn as $kq=>$quest) {
                                  if(!empty($quest))  {
       
                                      $row = [];
                                      $row['question_type'] = $qtype;
                                    //   echo "qtype".$qtype;
                                    //   exit;
                                      
                                     if(isset($answer[$qtype]) && isset($answer[$qtype][$kq]) && !empty($answer[$qtype][$kq])) {
                                          $row['answer'] = $answer[$qtype][$kq];
                                      }   else {
                                          $arr = [8,9,10,11];
                                          // Missing letters, jumbled words, jumbled letters, Dictation
                                          if(in_array($qtype, $arr)) {
                                              $row['answer'] = $quest;
                                          }   else {
                                              $row['answer'] = '';
                                          }
                                      }

                                        if(is_array($hint_file) && count($hint_file)> 0) {
                                            if(isset($hint_file[$qtype]) && isset($hint_file[$qtype][$kq]) && !empty($hint_file[$qtype][$kq])) {  

                                              $up = $hint_file[$qtype][$kq];
                                              $ext = $up->getClientOriginalExtension();
                                              if (!in_array($ext, $accepted_formats)) {
                                                  return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg ']);
                                              }
          
                                             $topicimg = rand() . time() . '.' . $up->getClientOriginalExtension();
          
                                              $destinationPath = public_path('/image/qb');
          
                                              $up->move($destinationPath, $topicimg);
          
                                              $row['hint_file'] = $topicimg;  
                                            }
                                        }   

                                      $row['question_bank_id'] = $question_bank_id;
                                      $row['question_type_id'] = $qtype;
                                      $row['question_type'] = DB::table('question_types')->where('id', $qtype)->value('question_type');
      
                                    if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
                                          $row['updated_by'] = Auth::User()->id;
                                          $row['updated_at'] = date('Y-m-d H:i:s');
                                            //echo "<pre>"; print_r($row);
                                          DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
                                      }   else {
                                          $row['created_by'] = Auth::User()->id;
                                          $row['created_at'] = date('Y-m-d H:i:s');
                                            //echo "<pre>"; print_r($row);
                                          DB::table('question_bank_items')->insert($row);
                                      }
                                  }
                              }
                          }
                      }
                  }
      

              if(is_array($question_file) && count($question_file)> 0) {
                  foreach($question_file as $qtype=>$qtn) {
                      if(is_array($qtn) && count($qtn)> 0) {
                          foreach($qtn as $kq=>$quest) {
                              if(!empty($quest)) {
                                  if (!empty($quest)) {
                                      $ext = $quest->getClientOriginalExtension();
                                      if (!in_array($ext, $this->accepted_formats_qbt)) {
                                          return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg,doc,docx,mp3,mp4,pdf']);
                                      }
  
                                      $topicimg = rand() . time() . '.' . $quest->getClientOriginalExtension();
  
                                      $destinationPath = public_path('/image/qb');
  
                                      $quest->move($destinationPath, $topicimg);
  
                                      $row = [];
                                      if(isset($oquestion[$qtype]) && isset($oquestion[$qtype][$kq]) && !empty($oquestion[$qtype][$kq])) {
                                          $row['question'] = $oquestion[$qtype][$kq];
                                      }   else {
                                          $row['question'] = '';
                                      }
                                      $row['question_type'] = $qtype;
                                      $row['question_file'] = $topicimg;
                                      $row['answer'] = !empty($answer[$qtype][$kq]) ? $answer[$qtype][$kq] : '';
  
                                      $row['question_bank_id'] = $question_bank_id;
                                      $row['question_type_id'] = $qtype;
                                      $row['question_type'] = DB::table('question_types')->where('id', $qtype)->value('question_type');
  
                                      if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
                                          $row['updated_by'] = Auth::User()->id;
                                          $row['updated_at'] = date('Y-m-d H:i:s');
  
                                          DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
                                      }   else {
                                          $row['created_by'] = Auth::User()->id;
                                          $row['created_at'] = date('Y-m-d H:i:s');
  
                                          DB::table('question_bank_items')->insert($row);
                                      }
                                  }
                              }
                          }
                      }
                  }
              } 
  
              if(is_array($oquestion_type) && count($oquestion_type)> 0) {
                  foreach($oquestion_type as $qtype=>$qtn) {
                      if(is_array($qtn) && count($qtn)> 0) {
                          foreach($qtn as $kq=>$quest) {
                           if(!empty($quest)) {
                                  $row = [];
                                  $row['question_type'] = $quest;
                                if(isset($oquestion[$qtype]) && isset($oquestion[$qtype][$kq]) && !empty($oquestion[$qtype][$kq])) {
                                      $row['question'] = $oquestion[$qtype][$kq];
                                  }   else {
                                      $row['question'] = '';
                                  }
                                  if(isset($oanswer[$qtype]) && isset($oanswer[$qtype][$kq]) && !empty($oanswer[$qtype][$kq])) {
                                      $row['answer'] = $oanswer[$qtype][$kq];
                                  }   else {
                                      $row['answer'] = '';
                                  }

                                    if(is_array($ohint_file) && count($ohint_file)> 0) {
                                        if(isset($ohint_file[$qtype]) && isset($ohint_file[$qtype][$kq]) && !empty($ohint_file[$qtype][$kq])) {  

                                          $up = $ohint_file[$qtype][$kq];
                                          $ext = $up->getClientOriginalExtension();
                                          if (!in_array($ext, $accepted_formats)) {
                                              return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg ']);
                                          }
      
                                         $topicimg = rand() . time() . '.' . $up->getClientOriginalExtension();
      
                                          $destinationPath = public_path('/image/qb');
      
                                          $up->move($destinationPath, $topicimg);
      
                                          $row['hint_file'] = $topicimg;  
                                        }
                                    }    
  
                                  $row['question_bank_id'] = $question_bank_id;
                                  $row['question_type_id'] = 12;

                                  if(isset($oqb_item_id[$qtype]) && isset($oqb_item_id[$qtype][$kq]) && ($oqb_item_id[$qtype][$kq]>0)){
                                      $row['updated_by'] = Auth::User()->id;
                                      $row['updated_at'] = date('Y-m-d H:i:s');
  
                                      DB::table('question_bank_items')->where('id', $oqb_item_id[$qtype][$kq])->update($row);
                                  }   else {
                                      $row['created_by'] = Auth::User()->id;
                                      $row['created_at'] = date('Y-m-d H:i:s');
  
                                      DB::table('question_bank_items')->insert($row);
                                  }
                              }
                          }
                      }
                  }
              }
              
            //   echo "<pre>"; print_r($input);
              return response()->json(['status' => "SUCCESS", 'message' => 'Question Bank Saved Successfully']);
          } else {
              return redirect('/admin/login');
          }
      }

    // Preview Question Banks
    /* Function: previewQuestionbank
     */
    public function previewQuestionbank(Request $request)
    {
        if (Auth::check()) {
            $qb = [];  $id = $request->get('id');
            QuestionBankItems::$admin = 1;
            if($id> 0) {
                $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->where('question_banks.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.previewquestionbank')->with(['qb'=>$qb]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: exportQuestionbank
      */
    public function exportQuestionbank(Request $request)
    {
        if (Auth::check()) {
            $checkedqb = $request->checkedqb;

            $validator = Validator::make($request->all(), [
                'checkedqb' => 'required',
            ]);

            if ($validator->fails()) {
                $msg = $validator->errors()->all();
                return response()->json([

                    'status' => "FAILED",
                    'message' =>  implode(', ', $msg),
                ]);
            }
            $input = $request->all();
            $checkedqb = $input['checkedqb'];
            if(is_array($checkedqb) && count($checkedqb) >0) {
                $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $checkedqb)->where('question_banks.deleted_status',0)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();

                    $exparray = [];
                    $exparray[] = ['qbid', 'class_name', 'subject_name', 'chapter_name', 'term_name', 'question_type_id',
                        'question_type', 'question', 'answer', 'option_1', 'option_2', 'option_3', 'option_4', 'question_file'];

                    foreach($qb as $arr) {
                        $data = [$arr['id'], $arr['class_name'], $arr['subject_name'], $arr['chaptername'],
                            $arr['term_name']];
                        if(is_array($arr['questionbank_items']) && count($arr['questionbank_items'])>0) {
                            foreach($arr['questionbank_items'] as $item) {

                                if(is_array($item['qb_items']) && count($item['qb_items'])>0) {
                                    foreach($item['qb_items'] as $qbitem) {

                                        $data1 = [$item['question_type_id'], $item['question_type'], $qbitem->question,
                                            $qbitem->answer, $qbitem->option_1, $qbitem->option_2, $qbitem->option_3,
                                            $qbitem->option_4, $qbitem->question_file ];

                                        $exparray[] = array_merge($data, $data1);

                                    }
                                }
                            }

                        }
                    }
                    header("Content-Disposition: attachment; filename=\"qb.xlsx\"");
                    header("Content-Type: application/vnd.ms-excel;");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    $out = fopen("php://output", 'w');
                    foreach ($exparray as $data)
                    {
                        fputcsv($out, $data,"\t");
                    }
                    fclose($out);
                    exit;
                    // echo "<pre>"; print_r($exparray);   exit;
                }

            }
        }
    }

    // Test Attempted
    /* Function: viewTestAttempted
     */
    public function viewTestAttempted($tid)
    {
        if (Auth::check()) { 
            TestItems::$admin = 1;
            $tests = DB::table('tests')
                ->leftjoin('terms', 'terms.id', 'tests.term_id')
                ->leftjoin('classes', 'classes.id', 'tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'tests.subject_id') 
                ->select('classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'tests.test_name' 
                )->where('tests.id', $tid)
                ->first();
            return view('admin.testattempted')->with('test_id',$tid)->with('tests',$tests);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getTestAttempted
    Datatable Load
     */
    public function getTestAttempted(Request $request)
    {    
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $test_id = $request->get('test_id',''); 
            $student_id = $request->get('student_id',''); 

            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
           
            $users_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->where('tests.id', $test_id)
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'users.mobile', 'users.mobile1',
                     'students.admission_no', 'tests.test_name', DB::RAW('count(student_tests.id) as test_attempted'));
            $filtered_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->where('tests.id', $test_id)
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name', DB::RAW('count(student_tests.id) as test_attempted'));

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'tests.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if($student_id != '' || $student_id != 0){
                $users_qry->where('student_tests.user_id',$student_id);
                $filtered_qry->where('student_tests.user_id',$student_id);
             }

             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
                $filtered_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '.$maxdate));
                $users_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
                $filtered_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
            } 
            

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'student_tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $studenttest = $users_qry->groupby('student_tests.user_id')->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filtered_qry->groupby('student_tests.user_id')->select('student_tests.id')->count();

            // $totalDataqry = Tests::orderby('id', 'asc');
            $totalDataqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
            ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
            ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
            ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
            ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
            ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
            ->where('tests.id', $test_id)
            ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');
            $totalData = $totalDataqry->select('student_tests.id')->groupby('student_tests.user_id')->get()->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($studenttest)) {
                foreach ($studenttest as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    //Test list
    /* Function: viewTestlist
     */
    public function viewTestlist()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $subject = Subjects::where('id', '>', 0)->orderby('position','asc')->get();
            return view('admin.testlist')->with('class',$classes)->with('subject',$subject)->with('class_id','')->with('subject_id','');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getTestlist
    Datatable Load
     */
    public function getTestlist(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $class_id = $request->get('class_id','');
            $subject_id = $request->get('subject_id','');
            $manual_auto = $request->get('manual_auto',''); 

            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
           
            $termsqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                ->leftjoin('classes', 'classes.id', 'tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->leftjoin('student_tests', 'student_tests.test_id', 'tests.id')
                ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', DB::RAW('count(student_tests.id) as test_attempted'));
            $filteredqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                ->leftjoin('classes', 'classes.id', 'tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->leftjoin('student_tests', 'student_tests.test_id', 'tests.id')
                ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', DB::RAW('count(student_tests.id) as test_attempted'));

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'tests.status') {
                            $termsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $termsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $termsqry->where('tests.school_id', Auth::User()->id);
                $filteredqry->where('tests.school_id', Auth::User()->id);
            }

            if(!empty($class_id)){
                $termsqry->where('tests.class_id',$class_id);
                $filteredqry->where('tests.class_id',$class_id);
            }
            if(!empty($subject_id)){
                $termsqry->where('tests.subject_id',$subject_id);
                $filteredqry->where('tests.subject_id',$subject_id);
            }
            if(!empty($manual_auto)){
                $termsqry->where('manual_auto',$manual_auto);
                $filteredqry->where('manual_auto',$manual_auto);
            } 

            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $termsqry->whereRaw('tests.created_at >= ?', [$mindate]);
                $filteredqry->whereRaw('tests.created_at >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day'. $maxdate));
                $termsqry->whereRaw('tests.created_at <= ?', [$maxdate]);
                $filteredqry->whereRaw('tests.created_at <= ?', [$maxdate]);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->groupby('tests.id')->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->groupby('tests.id')->select('tests.id')->count();

            $totalDataqry = Tests::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('tests.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($terms)) {
                foreach ($terms as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    public function addTest(Request $request)
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            return view('admin.addtest')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editTest(Request $request){
        $err = $items = '';  $qb = []; $qb_ids = []; $items = [];  $id = $request->get('id');
        if (Auth::check()) {
            $test = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                ->leftjoin('classes', 'classes.id', 'tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name')
                ->where('tests.id', $id)->get();

            if($test->isNotEmpty()) {
                $test = $test->toArray();
                $test = $test[0];

                $qbid = $test['qb_ids'];
                if(!empty($qbid)) {
                    $qbid = explode(',', $qbid);
                }
            }   else {
                $test = [];
                $qbid = [];
            } 


            if(is_array($qbid) && count($qbid)>0) { 
            } else {
                $qbidsqry = DB::table('test_items')
                    ->leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
                    ->where('test_id', $id)
                    ->select(DB::RAW('DISTINCT(question_bank_id) as question_bank_id'))
                    ->get();

                if($qbidsqry->isNotEmpty()) {
                    foreach($qbidsqry as $qq)
                    $qbid[] = $qq->question_bank_id;
                }
            } 
            
            //echo "<pre>"; print_r($qbid); exit;
            if(is_array($qbid) && count($qbid)>0) {

                $qb = DB::table('question_banks')->leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->get();

                if($qb->isNotEmpty()) { 
                    $qb = $qb->toArray();
                    foreach($qb as $qbs) {
                        $qb_ids[] = $qbs->id;
                    }
                    
                    /*$items = QuestionBankItems::with('questiontype_settings')
                        ->where('deleted_status',0)->whereIn('question_bank_id', $qb_ids)
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')->groupby('question_bank_id')->get();
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                    }*/


                    $items = DB::table('question_bank_items') 
                        ->where('deleted_status',0) 
                        ->whereIn('question_bank_id', $qb_ids)
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->get(); 
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                        foreach($items as $ik=>$item) {
                            $qb_items = DB::table('question_bank_items')
                                ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
                                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                                ->where('question_type_id', $item->question_type_id)
                                ->where('question_bank_items.deleted_status',0)
                                ->where('question_type', $item->question_type)
                                ->whereIn('question_bank_id', $qb_ids)
                                ->select('question_bank_items.*', 'question_banks.chapter_id', 'question_banks.qb_name', 'chapters.chaptername')
                                ->get();
                            if($qb_items->isNotEmpty()) {
                                $items[$ik]->qb_items1 = $qb_items->toArray();
                            }   else {
                                $items[$ik]->qb_items1 = [];
                            }
                        }
                        
                        foreach($items as $ik=>$item) {//echo "<pre>";  print_r($item); exit;
                            foreach($items[$ik]->qb_items1 as $qbik=>$qbiv) {

                                $test_mark = DB::table('test_items')->where('test_id', $id)
                                    ->where('question_bank_item_id', $qbiv->id)
                                    ->where('status', 'ACTIVE')->value('mark');

                                  //$items[$ik]->qb_items['question_bank_id']['question_bank_name'] = $qbiv->qb_name;
                                  $items[$ik]->qb_items[$qbiv->question_bank_id][] = ['qb_items'=>$qbiv, 'qb_name'=>$qbiv->qb_name, 'test_mark'=>$test_mark]; 
                                /*if(!empty($qbiv) && count($qbiv)>0) {
                                    $items[$ik]->$qbiv['question_bank_id']  = $qbiv->qb_name;
                                    $items[$ik]->$qbiv->question_bank_id->qb_items = $qbiv;
                                }   else {
                                    $items[$ik]->qb_items = $qbiv;
                                }*/
                            }
                        }

                    } 

                }   else {
                    $qb = [];
                    $items = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test'; 
            }
            //echo "<pre>"; print_r($items); exit; // print_r($qb); 
            return view('admin.edittest')->with(['qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'test'=>$test]);
        } else {
            return redirect('/admin/login');
        }
    }


    public function editTest_old($id){

     
        if (Auth::check()) {
       
              $qb = [];
            if($id> 0) {
                $qb = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                    ->leftjoin('classes', 'classes.id', 'tests.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                    //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                        'terms.term_name')
                    ->where('tests.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }

             
            }
         
            // echo "<pre>"; print_r($qb); exit;
            return view('admin.edittest')->with(['qb'=>$qb]);
        } else {
            return redirect('/admin/login');
        }
    }


    public function editTestList(Request $request) {
        if (Auth::check()) {


            $input = $request->all();
            $class_id = $request->get('class_id', 0);
            $subject_id = $request->get('subject_id', 0);
            $from_date = $request->get('from_date','');
            $to_date = $request->get('to_date','');
        
            $term_id = $request->get('term_id', 0);
            $chapter_id = $request->get('chapter_id', []);
            $test_id = $request->get('test_id','');
            if(is_array($chapter_id)) {
                $chapter_id = array_unique($chapter_id);
                $chapter_ids = implode(',', $chapter_id);
            }   else {
                $chapter_ids = '';
            }
            $test_name = $request->get('test_name', '');
            $marks = $request->get('marks', []);
            $test_mark = $request->get('test_mark','');
            $question_item_id = $request->get('question_item_id', []);
            $question_type = $request->get('question_type',[]);
            $test_time = $request->get('test_time',0);
            if(empty($test_time)) {
                $test_time = 0;
            }
            if($test_mark < 10){
                return response()->json(['status' => 'FAILED', 'message' => 'Minimun Mark of Test is 10']);
            }
            if($test_time > 0){} else {
                return response()->json(['status' => 'FAILED', 'message' => 'Please enter the valid Test time']);
            }

            $total_mark = 0;
            foreach($question_item_id as $q=>$v) {
                
              $total_mark += $marks[$q];
            }

            
            $total = round($total_mark);
            if($total != $test_mark){
                return response()->json(['status' => 'FAILED', 'message' => 'The Total Mark of Test  is'.' '.  $total .' but the Given Test Mark is'.' '. $test_mark .' ..!']);
            }


        //    if(count($question_type) > 0){
            if(is_array($question_item_id) && count($question_item_id)>0) {
               DB::table('tests')->where('id',$test_id)->update([
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'status'=>'ACTIVE',
                    'from_date' => $from_date,
                    'test_mark'=>$test_mark,
                    'test_time'=>$test_time,
                    'to_date' => $to_date,
                    'created_by'=>Auth::User()->id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);

                $data = ['status'=>'INACTIVE'];
                DB::table('test_items')->where('test_id', $test_id)->update($data);
                      
               
                foreach($question_item_id as $q=>$v) {
                   
                                   
                   $userStatus =DB::table('test_items')->where('test_id', $test_id)->where('question_bank_item_id',$q)->get()->count();
                   if(!empty($marks[$q])){
                    
                   if($userStatus > 0)
                   {
                    //  $mark = $marks;
                    
                    DB::table('test_items')->where('test_id', $test_id)->where('question_bank_item_id',$q)->update([
                        'mark'=>$marks[$q],
                        'status'=>'ACTIVE',
                         ]);
                      
                   }else{
                       DB::table('test_items')->insert([
                        'test_id'=>$test_id,
                        'question_bank_item_id'=>$q,
                        'mark'=>$marks[$q],
                        'status'=>'ACTIVE',
                        'created_by'=>Auth::User()->id,
                        'created_at'=>date('Y-m-d H:i:s')
                    ]);
                   }
                }
                else{

                 return response()->json(['status' => 'FAILED', 'message' => 'Please Enter the marks for Selected Questions']);
                }

                }
               
            }

            return response()->json(['status' => 'SUCCESS', 'message' => 'Test Saved Successfully']);
        //    }
        // else{
        //     return response()->json(['status' => 'FAILED', 'message' => 'Please Select Test Items']);
        // }

            
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
    }



    /* Function: getQuestionbankForTest
    Datatable Load
     */
    public function getQuestionbankForTest(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $class_id = (isset($input['class_id'])) ? $input['class_id'] : 0 ;
            $subject_id = (isset($input['subject_id'])) ? $input['subject_id'] : 0;
            $term_id = (isset($input['term_id'])) ? $input['term_id'] : 0;

            $termsqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                    'terms.term_name')
                ->where('question_banks.deleted_status',0);
            $filteredqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                    'terms.term_name')
                ->where('question_banks.deleted_status',0);

            $termsqry->where('question_banks.class_id', $class_id);
            $termsqry->where('question_banks.subject_id', $subject_id);
            $termsqry->where('question_banks.term_id', $term_id);

            $filteredqry->where('question_banks.class_id', $class_id);
            $filteredqry->where('question_banks.subject_id', $subject_id);
            $filteredqry->where('question_banks.term_id', $term_id);

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'question_banks.status') {
                            $termsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $termsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'question_banks.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('question_banks.id')->count();

            $totalDataqry = QuestionBanks::where('question_banks.deleted_status',0)->orderby('id', 'asc');
            $totalData = $filteredqry->select('question_banks.id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($terms)) {
                foreach ($terms as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }


    public function viewQbforTest(Request $request)
    {
        $err = $items = '';  $qb = []; $qb_ids = []; $items = [];
        if (Auth::check()) {
            $input = $request->all(); //echo "<pre>"; print_r($input);
            $qbid = $request->get('qbid', []);
            $section_id = $request->get('section_id', []);
            $section_names = ''; $section_ids = '';
            if(is_array($section_id) && count($section_id)>0) {
                $section_ids = implode(',', $section_id);
                $sec = DB::table('sections')->whereIn('id', $section_id)->select('section_name')->get();
                if($sec->isNotEmpty()) {
                    foreach($sec as $sn) {
                        $section_names .= $sn->section_name.', ';
                    }
                }
            }
            if(is_array($qbid) && count($qbid)>0) {

                $qb = DB::table('question_banks')->leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->get();

                if($qb->isNotEmpty()) { 
                    $qb = $qb->toArray();
                    foreach($qb as $qbs) {
                        $qb_ids[] = $qbs->id;
                    }
                    
                    /*$items = QuestionBankItems::with('questiontype_settings')
                        ->where('deleted_status',0)->whereIn('question_bank_id', $qb_ids)
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')->groupby('question_bank_id')->get();
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                    }*/


                    $items = DB::table('question_bank_items') 
                        ->where('deleted_status',0) 
                        ->whereIn('question_bank_id', $qb_ids)
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->get(); 
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                        foreach($items as $ik=>$item) {
                            $qb_items = DB::table('question_bank_items')
                                ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
                                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                                ->where('question_type_id', $item->question_type_id)
                                ->where('question_bank_items.deleted_status',0)
                                ->where('question_type', $item->question_type)
                                ->whereIn('question_bank_id', $qb_ids)
                                ->select('question_bank_items.*', 'question_banks.chapter_id', 'question_banks.qb_name', 'chapters.chaptername')
                                ->get();
                            if($qb_items->isNotEmpty()) {
                                $items[$ik]->qb_items1 = $qb_items->toArray();
                            }   else {
                                $items[$ik]->qb_items1 = [];
                            }
                        }
                        
                        foreach($items as $ik=>$item) {//echo "<pre>";  print_r($item); exit;
                            foreach($items[$ik]->qb_items1 as $qbik=>$qbiv) {
                                  //$items[$ik]->qb_items['question_bank_id']['question_bank_name'] = $qbiv->qb_name;
                                  $items[$ik]->qb_items[$qbiv->question_bank_id][] = ['qb_items'=>$qbiv, 'qb_name'=>$qbiv->qb_name]; 
                                /*if(!empty($qbiv) && count($qbiv)>0) {
                                    $items[$ik]->$qbiv['question_bank_id']  = $qbiv->qb_name;
                                    $items[$ik]->$qbiv->question_bank_id->qb_items = $qbiv;
                                }   else {
                                    $items[$ik]->qb_items = $qbiv;
                                }*/
                            }
                        }

                    } 

                }   else {
                    $qb = [];
                    $items = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>";  print_r($qb);exit; print_r($items);  , 'section_names'=>$section_names
            return view('admin.viewqbfrtest')->with(['qbank'=>$qb, 'section_ids'=>$section_ids, 'qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'section_names'=>$section_names]); 
        } else {
            return redirect('/admin/login');
        }
    }


    public function viewQbforTest_bfrcombine(Request $request)
    {
        $err = '';  $qb = [];
        if (Auth::check()) {
            $input = $request->all();
            $qbid = $request->get('qbid', []);
            if(is_array($qbid) && count($qbid)>0) {

                $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->where('question_banks.deleted_status',0)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                }   else {
                    $qb = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.viewqbfrtest')->with(['qbank'=>$qb, 'err'=>$err]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function saveQbTest(Request $request) {
        if (Auth::check()) {


            $input = $request->all();
            $from_date = $request->get('from_date','');
            $to_date = $request->get('to_date','');
            $class_id = $request->get('class_id', 0);
            $subject_id = $request->get('subject_id', 0);
            $section_id = $request->get('section_id', '');
            $term_id = $request->get('term_id', 0);
            $chapter_id = $request->get('chapter_id', []);
            $test_mark = $request->get('test_mark','');
            $test_time = $request->get('test_time',0);

            $qb_ids = $request->get('qb_ids','');
            $chapter_ids = $request->get('chapter_ids','');

            if(empty($test_time)) {
                $test_time = 0;
            }

            if(!empty($qb_ids)) {
                $qb_ids1 = explode(',', $qb_ids);
                $qb_ids1 = array_unique($qb_ids1);
                $qb_ids1 = array_filter($qb_ids1);
                $qb_ids = implode(',', $qb_ids1);
            }

            if(!empty($chapter_ids)) {
                $chapter_ids1 = explode(',', $chapter_ids);
                $chapter_ids1 = array_unique($chapter_ids1);
                $chapter_ids1 = array_filter($chapter_ids1);
                $chapter_ids = implode(',', $chapter_ids1);
            }

            if(is_array($chapter_id)) {
                $chapter_id = array_unique($chapter_id);
                //$chapter_ids = implode(',', $chapter_id);
            }   else {
                //$chapter_ids = '';
            }
            $test_name = $request->get('test_name', '');
            $marks = $request->get('marks', []); 

            $question_item_id = $request->get('question_item_id', []);
            $question_type = $request->get('question_type',[]);

            if(count($question_item_id) > 0){ }
            else {
                return response()->json(['status' => 'FAILED', 'message' => 'Please select the Questions']);
            }

            if($test_mark < 10){
                return response()->json(['status' => 'FAILED', 'message' => 'Minimun Mark of Test is 10']);
            }
            $total_mark = 0;
            foreach($question_item_id as $q=>$v) {
                if(!empty($marks[$q])){
                    $total_mark += $marks[$q];
                }
            }

            if($test_time > 0){ }
            else {
                return response()->json(['status' => 'FAILED', 'message' => 'Please enter the Valid Test time']);
            }
           
            $total = round($total_mark);
            if($total != $test_mark){
                return response()->json(['status' => 'FAILED', 'message' => 'The Total Mark of Test  is'.' '.  $total .' but the Given Test Mark is'.' '. $test_mark .' ..!']);
            }
            
            // if(count($question_type)>0){
            
            if(is_array($question_item_id) && count($question_item_id)>0) {
                $test_id = DB::table('tests')->insertGetId([
                    'school_id'=>Auth::User()->id,
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'section_ids'=>$section_id,
                    'chapter_ids'=>$chapter_ids,
                    'qb_ids'=>$qb_ids,
                    'test_name'=>$test_name,
                    'test_mark'=>$test_mark,
                    'test_time'=>$test_time,
                    'status'=>'ACTIVE',
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'created_by'=>Auth::User()->id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);

                
                foreach($question_item_id as $q=>$v) {
                   if(!empty($marks[$q])){
                        DB::table('test_items')->insert([
                            'test_id'=>$test_id,
                            'question_bank_item_id'=>$q,
                            'mark'=>$marks[$q],
                            'created_by'=>Auth::User()->id,
                            'created_at'=>date('Y-m-d H:i:s')
                        ]);
                   
                    } else{
                        return response()->json(['status' => 'FAILED', 'message' => 'Please Enter the marks for Selected Questions']);
                    } 
                }

                $subject_name = DB::table('subjects')->where('id', $subject_id)->value('subject_name');

                $students  = DB::table('students')->leftjoin('users', 'users.id', 'students.user_id')
                    ->where('students.class_id', $class_id);
                if(!empty($section_id)) {
                    $sids = explode(',', $section_id);
                    $sids = array_unique($sids);
                    $sids = array_filter($sids);
                    if(count($sids)>0) {
                        $students->whereIn('students.section_id', $sids);
                    }
                }
                    
                $students = $students->where('users.status', 'ACTIVE')->where('users.user_type', 'STUDENT')
                    ->select('users.id')->groupby('users.id')->get();

                if($students->isNotEmpty()) {
                    foreach($students as $stud) {
                        $type_no = 3;
                        $title = $test_name;
                        $message = 'Test given in '.$subject_name;
                        $fcmMsg = array("fcm" => array("notification" => array(
                            "title" => $title,
                            "body" => $message,
                            "type" => $type_no,
                          )));

                        CommonController::push_notification($stud->id, $type_no, $test_id, $fcmMsg);
                    }
                } 
            }

           
           
            return response()->json(['status' => 'SUCCESS', 'message' => 'Test Saved Successfully']);
            
        // }
        // else{
        //      return response()->json(['status' => 'FAILED', 'message' => 'Please Select Test Items']);
           
        // }
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
    }


    public function saveQbAutoTestPapers(Request $request) {
        if (Auth::check()) {


            $input = $request->all();
            $from_date = $request->get('from_date','');
            $to_date = $request->get('to_date','');
            $class_id = $request->get('class_id', 0);
            $subject_id = $request->get('subject_id', 0);
            $term_id = $request->get('term_id', 0);
            $chapter_id = $request->get('chapter_id', []);
            if(is_array($chapter_id)) {
                $chapter_id = array_unique($chapter_id);
                $chapter_ids = implode(',', $chapter_id);
            }   else {
                $chapter_ids = '';
            }
            $test_name = $request->get('test_name', '');
            $noofquest = $request->get('noofquest',[]);
            $marksperquest = $request->get('marksperquest',[]);
            $tot_question = $request->get('total_question',[]);
            $test_mark = $request->get('test_mark','');
            $total = $request->get('total_mark',[]);
            $qb_id = $request->get('qb_id',[]);
            $no_of_papers = $request->get('no_of_papers',0);
           
            if($no_of_papers < 1){
                return response()->json(['status' => 'FAILED', 'message' => 'Please enter the Number of Question Papers needed']);
            }
            if($test_mark < 10){
                return response()->json(['status' => 'FAILED', 'message' => 'Minimun Mark of Test is 10']);
            }
            $total_mark = 0;
            foreach($total as $tot =>$val) {
              $total_mark += $total[$tot];
             }
            

             $total = round($total_mark);
            // echo $total ."==".$test_mark;
             if($total != $test_mark){
             return response()->json(['status' => 'FAILED', 'message' => 'The Total Mark of Test  is'.' '.  $total .' but the Given Test Mark is'.' '. $test_mark .' ..!']);
             }
          
            // $noofquest = $request->get('noofquest',[]);
            //   echo "question".  $question_item_id = $request->get('question_item_id', []);

            
            if(is_array($noofquest) && count($noofquest)>0) {
                $test_id = DB::table('test_papers')->insertGetId([
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'test_mark'=>$test_mark,
                    'status'=>'ACTIVE',
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'no_of_papers' => $no_of_papers,
                    'created_by'=>Auth::User()->id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);

                for($no = 1; $no <= $no_of_papers; $no++)  {
                
                    foreach($noofquest as $q=>$v) {


                        //   if(!empty($noofquest[$q]) || $noofquest[$q] != 0 ){
                      
                        if(!empty($marksperquest[$q]) || $marksperquest[$q] != 0){                    
                            $limit = $v;
                            $explode_ques = explode('_',$q);
                            $question_type_id = $explode_ques[0];
                            $question_bank_id = $explode_ques[1];
                            $question_type = $explode_ques[2];
                            $mark_per_ques = $marksperquest[$q];
                            if($limit != 0){

                                $get_question = DB::table('question_bank_items')->select('question_bank_items.*')
                                    ->whereIn('question_bank_id',$qb_id)->where('question_type_id',$question_type_id)->where('question_type',$question_type)->orderBy(DB::raw('RAND()'))->limit($limit)->get()->toArray();

                           
                                foreach ($get_question as $data) {
                                    $datas    = get_object_vars($data);
                                    $id = $datas['id'];
                                    DB::table('test_items_papers')->insert([
                                        'test_id'=>$test_id,
                                        'test_no'=>$no,
                                        'question_bank_item_id'=>$id,
                                        'mark'=>$marksperquest[$q],
                                        'created_by'=>Auth::User()->id,
                                        'created_at'=>date('Y-m-d H:i:s')
                                    ]);  
                                } 
                            }
                        }  
                    }
                }
            }
              
            return response()->json(['status' => 'SUCCESS', 'message' => 'Test Saved Successfully']);
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
    }

    public function saveQbAutoTest(Request $request) {
        if (Auth::check()) {


            $input = $request->all();
            $from_date = $request->get('from_date','');
            $to_date = $request->get('to_date','');
            $class_id = $request->get('class_id', 0);
            $subject_id = $request->get('subject_id', 0);
            $term_id = $request->get('term_id', 0);
            $chapter_id = $request->get('chapter_id', []);
            if(is_array($chapter_id)) {
                $chapter_id = array_unique($chapter_id);
                $chapter_ids = implode(',', $chapter_id);
            }   else {
                $chapter_ids = '';
            }
            $test_name = $request->get('test_name', '');
            $noofquest = $request->get('noofquest',[]);
            $marksperquest = $request->get('marksperquest',[]);
            $tot_question = $request->get('total_question',[]);
            $test_mark = $request->get('test_mark','');
            $total = $request->get('total_mark',[]);
            $qb_id = $request->get('qb_id',[]);
           
           
            if($test_mark < 10){
                return response()->json(['status' => 'FAILED', 'message' => 'Minimun Mark of Test is 10']);
            }
            $total_mark = 0;
            foreach($total as $tot =>$val) {
              $total_mark += $total[$tot];
             }
            

             $total = round($total_mark);
          // echo $total ."==".$test_mark;
             if($total != $test_mark){
             return response()->json(['status' => 'FAILED', 'message' => 'The Total Mark of Test  is'.' '.  $total .' but the Given Test Mark is'.' '. $test_mark .' ..!']);
             }
          
            // $noofquest = $request->get('noofquest',[]);
           //   echo "question".  $question_item_id = $request->get('question_item_id', []);

            if(is_array($noofquest) && count($noofquest)>0) {
                $test_id = DB::table('tests')->insertGetId([
                    'school_id'=>Auth::User()->id,
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'test_mark'=>$test_mark,
                    'status'=>'ACTIVE',
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'manual_auto' => 2,
                    'created_by'=>Auth::User()->id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
                
                foreach($noofquest as $q=>$v) {


                //   if(!empty($noofquest[$q]) || $noofquest[$q] != 0 ){
                  
                if(!empty($marksperquest[$q]) || $marksperquest[$q] != 0){                    
                    $limit = $v;
                    $explode_ques = explode('_',$q);
                    $question_type_id = $explode_ques[0];
                    $question_bank_id = $explode_ques[1];
                    $question_type = $explode_ques[2];
                    $mark_per_ques = $marksperquest[$q];
                    if($limit != 0){

                    $get_question = DB::table('question_bank_items')->select('question_bank_items.*')
                        ->whereIn('question_bank_id',$qb_id)->where('question_type_id',$question_type_id)->where('question_type',$question_type)->orderBy(DB::raw('RAND()'))->limit($limit)->get()->toArray();

               
                    foreach ($get_question as $data) {
                        $datas    = get_object_vars($data);
                        $id = $datas['id'];
                        DB::table('test_items')->insert([
                            'test_id'=>$test_id,
                            'question_bank_item_id'=>$id,
                            'mark'=>$marksperquest[$q],
                            'created_by'=>Auth::User()->id,
                            'created_at'=>date('Y-m-d H:i:s')
                        ]);  
                    } 
                }
                }
              

                    }
               }
              
         return response()->json(['status' => 'SUCCESS', 'message' => 'Test Saved Successfully']);
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
    }

    //Test list
    /* Function: viewTestlistPapers
     */
    public function viewTestlistPapers()
    {
        if (Auth::check()) {
            $classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            $subject = Subjects::where('id', '>', 0)->orderby('position','asc')->get();
            return view('admin.testlistpapers')->with('class',$classes)->with('subject',$subject)->with('class_id','')->with('subject_id','');
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getTestlistPapers
    Datatable Load
     */
    public function getTestlistPapers(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $class_id = $request->get('class_id','');
            $subject_id = $request->get('subject_id','');

            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
           
            $termsqry = TestPapers::leftjoin('terms', 'terms.id', 'test_papers.term_id')
                ->leftjoin('classes', 'classes.id', 'test_papers.class_id')
                ->leftjoin('subjects', 'subjects.id', 'test_papers.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->select('test_papers.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name');
            $filteredqry = TestPapers::leftjoin('terms', 'terms.id', 'test_papers.term_id')
                ->leftjoin('classes', 'classes.id', 'test_papers.class_id')
                ->leftjoin('subjects', 'subjects.id', 'test_papers.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->select('test_papers.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'test_papers.status') {
                            $termsqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $termsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $termsqry->where('classes.school_id', Auth::User()->id);
                $filteredqry->where('classes.school_id', Auth::User()->id);
            }

            if(!empty($class_id)){
                $termsqry->where('class_id',$class_id);
                $filteredqry->where('class_id',$class_id);
            }
            if(!empty($subject_id)){
                $termsqry->where('subject_id',$subject_id);
                $filteredqry->where('subject_id',$subject_id);
            }

            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $termsqry->whereRaw('test_papers.created_at >= ?', [$mindate]);
                $filteredqry->whereRaw('test_papers.created_at >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day'. $maxdate));
                $termsqry->whereRaw('test_papers.created_at <= ?', [$maxdate]);
                $filteredqry->whereRaw('test_papers.created_at <= ?', [$maxdate]);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'test_papers.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('test_papers.id')->count();

            $totalDataqry = Tests::orderby('id', 'asc');
            if(Auth::User()->user_type == 'SCHOOL') {
                $totalDataqry->where('tests.school_id', Auth::User()->id); 
            }
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($terms)) {
                foreach ($terms as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    // Preview Test
    /* Function: previewTest
     */
    public function previewTest(Request $request)
    {
        if (Auth::check()) {
            $qb = []; $id = $request->get('id');
            if($id> 0) {
                $qb = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                    ->leftjoin('classes', 'classes.id', 'tests.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                    //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                        'terms.term_name')
                    ->where('tests.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.previewtest')->with(['qb'=>$qb]);
        } else {
            return redirect('/admin/login');
        }
    }


    // Preview Test
    /* Function: previewTestPapers
     */
    public function previewTestPapers(Request $request)
    {
        if (Auth::check()) {
            $qb = [];  $id = $request->get('id');
            if($id> 0) {
                $qb = TestPapers::leftjoin('terms', 'terms.id', 'test_papers.term_id')
                    ->leftjoin('classes', 'classes.id', 'test_papers.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'test_papers.subject_id')
                    //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('test_papers.*', 'classes.class_name', 'subjects.subject_name',
                        'terms.term_name')
                    ->where('test_papers.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.previewtestpapers')->with(['qb'=>$qb]);
        } else {
            return redirect('/admin/login');
        }
    }

    //Students Test list
    /* Function: viewStudentsTestlist
     */
    public function viewStudentsTestlist()
    {
        if (Auth::check()) {
            $student = User::leftjoin('students', 'students.user_id', 'users.id')
            ->where('users.user_type', 'STUDENT')
            ->select('users.*')->get();
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            return view('admin.studentstestlist')->with('student',$student)->with('class',$classes);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getStudentsTestlist
    Datatable Load
     */
    public function getStudentsTestlist(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $class_id = $request->get('class_id','');
            $section_id = $request->get('section_id','');
            $student_id = $request->get('student_id','');
           $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
           $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
            $users_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');
            $filtered_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'tests.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

            if($student_id != '' || $student_id != 0){
                $users_qry->where('student_tests.user_id',$student_id);
                $filtered_qry->where('student_tests.user_id',$student_id);
             }

             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
                $filtered_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '.$maxdate));
                $users_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
                $filtered_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
            }
            
            if($class_id != '' || $class_id != 0){
                $users_qry->where('student_tests.class_id',$class_id);
                $filtered_qry->where('student_tests.class_id',$class_id);
             }
            

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'student_tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $studenttest = $users_qry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filtered_qry->select('student_tests.id')->count();

            // $totalDataqry = Tests::orderby('id', 'asc');
            $totalDataqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
            ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
            ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
            ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
            ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
            ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
            ->where('users.school_college_id', Auth::User()->id)
            ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');
            $totalData = $totalDataqry->select('student_tests.id')->get()->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($studenttest)) {
                foreach ($studenttest as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }


    public function getExcelStudentsTestlist(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $class_id = $request->get('class_id','');
            $section_id = $request->get('section_id','');
            $student_id = $request->get('student_id','');
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
            $users_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'users.mobile', 'users.mobile1',
                    'students.admission_no', 'tests.test_name');
            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'tests.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                               } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if($student_id != '' || $student_id != 0){
                $users_qry->where('student_tests.user_id',$student_id);
              
             }

             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
              
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '.$maxdate));
                $users_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
              
            }
            
            if($class_id != '' || $class_id != 0){
                $users_qry->where('student_tests.class_id',$class_id);
         
             }
            

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'student_tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $studenttest = $users_qry->orderby($orderby, $dir)->get();
            
            $teacher_leave_excel = [];

            if (!empty($studenttest)) {
                $i = 1;
                foreach ($studenttest as $rev) {
                 $teacher_leave_excel[] = [
                        "S.No" => $i,
                        "Student Name" => $rev->student_name,
                        "Admission No" => $rev->admission_no,
                        "Father Mobile" => $rev->mobile,
                        "Alternate No" => $rev->mobile1,
                        "Term" => $rev->term_name,
                        "Class" => $rev->class_name,
                        "Subject" => $rev->subject_name,
                        "Test Date" => $rev->test_date,
                        "Test Name" => $rev->test_name,
                        "Test Mark" => $rev->test_mark,
                        "Student Mark" => $rev->student_mark,
                        "Student Grade" => $rev->student_grade,
                        "Duration" => $rev->duration,
                     
                    ];
    
                    $i++;
                }
            }
    
            header("Content-Type: text/plain");
            $flag = false;
            foreach ($teacher_leave_excel as $row) {
                if (! $flag) {
                    // display field/column names as first row
                    echo implode("\t", array_keys($row)) . "\r\n";
                    $flag = true;
                }
                echo implode("\t", array_values($row)) . "\r\n";
            }
            exit();
          

        } else {
            return redirect('/admin/login');
        }
    }

    // Preview Students Test
    /* Function: previewStudentsTest
     */
    public function previewStudentsTest(Request $request)
    {
        if (Auth::check()) {
            $qb = []; $id = $request->get('id');
            if($id> 0) {
                $qb = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                        ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                        ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                        ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                        ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                        ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                        ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                            'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name')
                        ->where('student_tests.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.previewstudentstest')->with(['qb'=>$qb, 'id'=>$id]);
        } else {
            return redirect('/admin/login');
        }
    }


    public function editStudentsTest(Request $request)
    {
        if (Auth::check()) {
            $qb = []; $id = $request->get('id');
            if($id> 0) {
                TestItems::$admin = 1;
                $qb = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                        ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                        ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                        ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                        ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                        ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                        ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                            'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name')
                        ->where('student_tests.id', $id)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                    $qb = $qb[0];
                }   else {
                    $qb = [];
                }
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.editstudenttestlist')->with(['qb'=>$qb, 'id'=>$id]);
        } else {
            return redirect('/admin/login');
        }
    }

    // save Students Test
    /* Function: saveStudentsMark
     */
    public function saveStudentsMark(Request $request)
    {
        if (Auth::check()) {
            $students_test_id = $request->students_test_id;
            $mark = $request->mark;
            $grade = $request->grade;

            if($students_test_id> 0) {
                if(is_array($mark) && count($mark)>0) {
                    foreach($mark as $question_bank_item_id=>$smark) {

                       DB::table('student_test_answers')->where('student_test_id', $students_test_id)
                            ->where('question_bank_item_id', $question_bank_item_id)
                            ->update(['mark'=> $smark, 'updated_by'=>Auth::User()->id, 'updated_at'=>date('Y-m-d H:i:s')]);
                    }
                }

                $test_id = DB::table('student_tests')->where('id', $students_test_id)->value('test_id');
                $test_mark = DB::table('test_items')->where('test_id', $test_id)->sum('mark');
                $student_mark = DB::table('student_test_answers')->where('student_test_id', $students_test_id)->sum('mark');

                DB::table('student_tests')->where('id', $students_test_id)
                    ->update(['test_mark'=>$test_mark, 'student_mark'=>$student_mark, 'student_grade'=>$grade,
                              'updated_by'=>Auth::User()->id, 'updated_at'=>date('Y-m-d H:i:s')]);

                return response()->json(['status' => 'SUCCESS', 'message' => 'Marks updared successfully']);
            }   else {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid Student Test']);
            }
            //echo "<pre>"; print_r($qb); exit;
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Student Test']);
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Session Logged Out, Please Login Again']);
        }
    }

    public function addAutoTest(Request $request)
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position','asc')->get();
            return view('admin.autoaddtest')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    public function viewQbforAutoTest(Request $request)
    {
        $err = $items = '';  $qb = []; $qb_ids = []; $items = [];
        if (Auth::check()) {
            $input = $request->all();
            $qbid = $request->get('qbid', []);
            if(is_array($qbid) && count($qbid)>0) {

                $qb = DB::table('question_banks')->leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->get();

                if($qb->isNotEmpty()) { 
                    $qb = $qb->toArray();
                    foreach($qb as $qbs) {
                        $qb_ids[] = $qbs->id;
                    }
                    
                    /*$items = QuestionBankItems::with('questiontype_settings')
                        ->where('deleted_status',0)->whereIn('question_bank_id', $qb_ids)
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')->groupby('question_bank_id')->get();
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                    }*/


                    $items = DB::table('question_bank_items') 
                        ->where('deleted_status',0) 
                        ->whereIn('question_bank_id', $qb_ids)
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->get(); 
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                        foreach($items as $ik=>$item) {
                            $qb_items = DB::table('question_bank_items')
                                ->where('question_type_id', $item->question_type_id)
                                ->where('deleted_status',0)
                                ->where('question_type', $item->question_type)
                                ->whereIn('question_bank_id', $qb_ids)
                                ->get();
                            if($qb_items->isNotEmpty()) {
                                $items[$ik]->qb_items = $qb_items->toArray();
                            }   else {
                                $items[$ik]->qb_items = [];
                            }
                        }
                        
                    } 

                }   else {
                    $qb = [];
                    $items = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>";  print_r($items); exit; 
            // echo "<pre>";print_r($qb); exit;
            return view('admin.viewqbfrautotest')->with(['qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'qb_id'=> $qbid]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function addAutoTestPapers(Request $request)
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE')->orderby('position','asc')->get();
            return view('admin.autoaddtestpapers')->with('classes', $classes);
        } else {
            return redirect('/admin/login');
        }
    }

    public function viewQbforAutoTestPapers(Request $request)
    {
        $err = $items = '';  $qb = []; $qb_ids = []; $items = [];
        if (Auth::check()) {
            $input = $request->all();
            $qbid = $request->get('qbid', []);
            if(is_array($qbid) && count($qbid)>0) {

                $qb = DB::table('question_banks')->leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->get();

                if($qb->isNotEmpty()) { 
                    $qb = $qb->toArray();
                    foreach($qb as $qbs) {
                        $qb_ids[] = $qbs->id;
                    }
                    
                    /*$items = QuestionBankItems::with('questiontype_settings')
                        ->where('deleted_status',0)->whereIn('question_bank_id', $qb_ids)
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')->groupby('question_bank_id')->get();
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                    }*/


                    $items = DB::table('question_bank_items') 
                        ->where('deleted_status',0) 
                        ->whereIn('question_bank_id', $qb_ids)
                        ->orderby('question_type_id', 'asc')  
                        ->groupby('question_type_id')->groupby('question_type')
                        ->select('question_type_id', 'question_type', 'question_bank_id')
                        ->get(); 
                    if($items->isNotEmpty()) {
                        $items = $items->toArray();
                        foreach($items as $ik=>$item) {
                            $qb_items = DB::table('question_bank_items')
                                ->where('question_type_id', $item->question_type_id)
                                ->where('deleted_status',0)
                                ->where('question_type', $item->question_type)
                                ->whereIn('question_bank_id', $qb_ids)
                                ->get();
                            if($qb_items->isNotEmpty()) {
                                $items[$ik]->qb_items = $qb_items->toArray();
                            }   else {
                                $items[$ik]->qb_items = [];
                            }
                        }
                        
                    } 

                }   else {
                    $qb = [];
                    $items = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>";  print_r($items); exit; 
            // echo "<pre>";print_r($qb); exit;
            return view('admin.viewqbfrautotestpapers')->with(['qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'qb_id'=> $qbid]);
        } else {
            return redirect('/admin/login');
        }
    }


    public function viewQbforAutoTest_beforcombine(Request $request)
    {
        $err = '';  $qb = [];
        if (Auth::check()) {
            $input = $request->all();
            $qbid = $request->get('qbid', []);
            if(is_array($qbid) && count($qbid)>0) {

                $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->where('question_banks.deleted_status',0)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                }   else {
                    $qb = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('admin.viewqbfrautotest')->with(['qbank'=>$qb, 'err'=>$err]);
        } else {
            return redirect('/admin/login');
        }
    }


    function get_dir_size($directory){
        $size = 0;
        $files = glob($directory.'/*');
        foreach($files as $path){
            is_file($path) && $size += filesize($path);
            is_dir($path)  && $size += get_dir_size($path);
        }
        return $size;
    } 



    public function viewStudentAttenReport(){
        if(Auth::check()){
        $monthyear = $class_id = $section_id = '';
        $lastdate = date('t', strtotime(date('Y-m')));
        $classes = Classes::where('status', 'ACTIVE');
        if(Auth::User()->user_type == 'SCHOOL') {
            $classes->where('school_id', Auth::User()->id); 
        }
        $classes = $classes->orderby('position', 'Asc')->get();
        $students  = '';
        $new_date = date('Y-m-d');
        $monthyear = date('Y-m');
       
        list($year, $month) = explode('-', $monthyear);
        $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
        ->whereRAW('MONTH(holiday_date) = "'.$month.'" ');
        if(Auth::User()->user_type == 'SCHOOL') {
            $holidays->where('school_college_id', Auth::User()->id); 
        }
        $holidays = $holidays->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
        // $students[$k]->holidays_list = $holidays;
        if($holidays->isNotEmpty()){
            $holidays = $holidays->toArray();
        }
        // $date = 'day_'.$day;
        // $attendance_chk = '';
        // $date2 = 'day_'.$day.'_an';
        // $attendance_chk= '';
       
         /*DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
            ->where('users.status', 'ACTIVE')->where('user_type', 'STUDENT')
            ->select('users.id', 'name', 'last_name', 'admission_no')->orderby('admission_no', 'Asc')->get();
        */
    return view('admin.studentattenreport')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
            'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate, 'students'=>$students,'new_date' => $new_date,'attendance_chk' =>0,'attendance_chk2'=>0])->with('holidays',$holidays);
    }else{
        return redirect('/login');
    }
        
    }

    public function loadStudentAttendanceRep(Request $request){
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the Year']);
            }
            $lastdate = date('t', strtotime($monthyear));
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);
            
            if($class_id > 0) {} else { $class_id = 0; }
            if($section_id > 0) {} else { $section_id = 0; }

            if($class_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Class']);
            }
            if($section_id == 0) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please select the Section']);
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
            
            $users = DB::select("select student_class_mappings.*, `users`.`id`, `name`, `email`, `mobile`, `students`.`class_id`,`profile_image`, `students`.`section_id`, `students`.`admission_no` from `student_class_mappings` left join `users` on `student_class_mappings`.`user_id` = `users`.`id` left join `students` on `students`.`user_id` = `users`.`id` where `user_type` = 'STUDENT' and '".$fin_month."' BETWEEN from_month and to_month and `student_class_mappings`.`class_id` = '".$class_id."' and `student_class_mappings`.`section_id` = '".$section_id."'");
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
                    ->where('user_type', 'STUDENT')
                    ->where('users.status','ACTIVE')
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

                    $students = $students->toArray();
                    //   echo "<pre>";print_r($students);exit;
                    $html = view('admin.loadstudentsattendancerep')
                        ->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                            'section_id'=>$section_id, 'students'=>$students, 
                            'lastdate'=>$lastdate,'saturdays'=>$saturdays,'sundays'=>$sundays,
                            'total_working_days'=>$total_working_days 
                        ])
                        ->render();

                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students attendance Detail']);

                }   else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
            }


            return view('admin.studentattenreport')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }


    public function updateStudentAttendanceRep(Request $request)
    {
        if(Auth::check()){
           $student_id = $request->student_id;
            $mode = $request->mode;
            $day = $request->day;
            $monthyear = $request->monthyear;
            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_id',0);
            $session = $request->session;

            // $validator = Validator::make($request->all(), [
            //     'student_id' => 'required',
            //     'mode' => 'required',
            //     'day' => 'required',
            //     'monthyear' => 'required',
            //     'class_id' => 'required',
            //     'section_id' => 'required',
            // ]);

            // if ($validator->fails()) {

            //     $msg = $validator->errors()->all();

            //     return response()->json([
            //         'status' => 0,
            //         'message' => implode(', ', $msg)
            //     ]);
            // }

            if($student_id > 0) {

                $ex = DB::table('studentsdaily_attendance')->where('user_id', $student_id)
                    ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();
                    
                    if($session == "fn"){
                if(!empty($ex)) {
                    $date = 'day_'.$day;
                    DB::table('studentsdaily_attendance')->where('user_id', $student_id)
                        ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day;
                    DB::table('studentsdaily_attendance')->insert([
                        'user_id'=>$student_id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
            }else if($session == "an"){

                if(!empty($ex)) {
                    $date = 'day_'.$day.'_an';
                    DB::table('studentsdaily_attendance')->where('user_id', $student_id)
                        ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day.'_an';
                    DB::table('studentsdaily_attendance')->insert([
                        'user_id'=>$student_id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }

            }
            }

            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }

    }


    public function viewTeacherAttendanceRep(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                $monthyear = date('Y-m');
            }
            $lastdate = date('t', strtotime($monthyear));
            User::$monthyear = $monthyear;
            $teachers = User::with('teacherdailyattendance')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('teachers.user_id', '>', 0)
                ->where('users.status', 'ACTIVE')
                ->select('users.id', 'name', 'email', 'mobile', 'emp_no','profile_image')->get();
            if($teachers->isNotEmpty()) {
                $teachers = $teachers->toArray();
                //echo "<pre>"; print_r($teachers);exit;
            }   else {
                $teachers = [];
            }
            return view('admin.teachersattendancerep')->with(['teachers'=>$teachers, 'monthyear'=>$monthyear
                , 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }


    public function viewEditTeachersAttendanceRep($encid,$monthyear, Request $request)   {
        if(Auth::check()){
            $teacher_id = 0;
           $obj = json_decode(base64_decode($encid));
            if(!empty($obj)) {
             $teacher_id = $obj->id;
            }
           if($teacher_id > 0) {
                $pl = DB::table('users')->where('id', $teacher_id)->where('status', 'ACTIVE')
                    ->select('id', 'name', 'email', 'mobile','profile_image')->get();
                if($pl->isNotEmpty()) {
                
                    if(empty($monthyear)) {
                        $monthyear = date('Y-m');
                    }
                    $lastdate = date('t', strtotime($monthyear));
                    User::$monthyear = $monthyear;
                    $players = User::with('teacherattendance')
                        ->where('id', $teacher_id)
                       ->select('id', 'name', 'email', 'mobile')->get();

                       foreach($players as $k=>$v){
                        list($year, $month) = explode('-', $monthyear);
                     $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                        ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                        ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                        $players[$k]->holidays_list = $holidays;
                    }
                    if($players->isNotEmpty()) {
                        $players = $players->toArray();
                        //echo "<pre>"; print_r($players);exit;
                    }
                    return view('admin.updateteacherattendance')->with(['players'=>$players, 'monthyear'=>$monthyear,
                        'lastdate'=>$lastdate]);
                }  else {
                    return view('admin.updateteacherattendance')->with(['error'=>1]);
                }
            }   else {
                return view('admin.updateteacherattendance')->with(['error'=>1]);
            }
        }else{
            return redirect('/login');
        }
    }

    /* Function: updateTeacherAttendance
     */
    public function updateTeacherAttendanceRep(Request $request)
    {
        if(Auth::check()){
            $teacherid = $request->teacherid;
            $mode = $request->mode;
            $day = $request->day;
            $monthyear = $request->monthyear;
            $session = $request->session;

            // $validator = Validator::make($request->all(), [
            //     'teacherid' => 'required',
            //     'mode' => 'required',
            //     'day' => 'required',
            //     'monthyear' => 'required',
            // ]);

            // if ($validator->fails()) {

            //     $msg = $validator->errors()->all();

            //     return response()->json([
            //         'status' => 0,
            //         'message' => implode(', ', $msg)
            //     ]);
            // }

            if($teacherid > 0) {

                $ex = DB::table('teachersdaily_attendance')->where('user_id', $teacherid)
                    ->where('monthyear', $monthyear)->first();

                    if($session == "fn"){

                if(!empty($ex)) {
                    $date = 'day_'.$day;
                    DB::table('teachersdaily_attendance')->where('user_id', $teacherid)
                        ->where('monthyear', $monthyear)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day;
                    DB::table('teachersdaily_attendance')->insert([
                        'user_id'=>$teacherid,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }
            }
            else if($session == "an"){
                if(!empty($ex)) {
                    $date = 'day_'.$day.'_an';
                    DB::table('teachersdaily_attendance')->where('user_id', $teacherid)
                        ->where('monthyear', $monthyear)
                        ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                }   else {
                    $date = 'day_'.$day.'_an';
                    DB::table('teachersdaily_attendance')->insert([
                        'user_id'=>$teacherid,
                        'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                        'created_by'=>Auth::User()->id
                    ]);
                }

            }
            }

            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
        }else{
            return response()->json(['status' => 0, 'message' => 'Session Out. Please logout and login again']);
        }
    }



    //Attendance Management
    /* Function: loadTeacherAttendance
     */
    public function loadTeacherAttendanceRep(Request $request)   {
        if(Auth::check()){
            $monthyear = $request->get('monthyear', '');
            if(empty($monthyear)) {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please enter the Year']);
            }
            $lastdate = date('t', strtotime($monthyear));
            User::$monthyear = $monthyear;
            list($year, $month) = explode('-', $monthyear);
            $teachers = User::with('teacherdailyattendance')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->where('user_type', 'TEACHER')->where('users.school_college_id', Auth::User()->id)
                ->select('users.id', 'name', 'email', 'mobile','profile_image', 'teachers.emp_no')
                ->get();
                foreach($teachers as $k=>$v){
                   
                 $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')->where('holidays.school_college_id', Auth::User()->id)
                    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                    $teachers[$k]->holidays_list = $holidays;
                }
                $sundays = CommonController::getSundays($year, $month); 
                $saturdays = CommonController::getSaturdays($year, $month); 
            if($teachers->isNotEmpty()) {
                $teachers = $teachers->toArray();
                //echo "<pre>"; print_r($teachers);exit;
                $html = view('admin.loadteachersattendancerep')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate,'sundays'=>$sundays,'saturdays'=>$saturdays])->render();

                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Teacher attendance Detail']);

            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Teacher attendance Detail']);
            }

            return view('admin.teachersattendancerep')->with(['monthyear'=>$monthyear, 'teachers'=>$teachers, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }

    //Students Strength
    /* Function: viewStudentStrength
    */
    public function viewStudentStrength()
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            return view('admin.studentstrength')->with(['classes'=>$classes]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getStudentStrength
    Datatable Load
    */
    public function getStudentStrength(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_id',0);
            $acadamic_year = $request->get('acadamic_year',date('Y')); 

            Sections::$acadamic_year = $acadamic_year;
            $sectionsqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');
            $filteredqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) { 
                        $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $sectionsqry->where('classes.school_id', Auth::User()->id);
                $filteredqry->where('classes.school_id', Auth::User()->id);
            }

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
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }




            $sections = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')->where('classes.school_id', Auth::User()->id)
                ->select('sections.id');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    //Students Attendance Presence
    /* Function: viewStudentsPresence
    */
    public function viewStudentsPresence()
    {
        if (Auth::check()) {
            $classes = Classes::where('status', 'ACTIVE')->orderby('position', 'Asc')->get();
            return view('admin.studentspresence')->with(['classes'=>$classes]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getStudentsPresence
    Datatable Load
    */
    public function getStudentsPresence(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_id',0);
            $acadamic_year = $request->get('acadamic_year',date('Y')); 

            Sections::$acadamic_year = $acadamic_year;
            $sectionsqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');
            $filteredqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.class_id', 'sections.id', 'classes.class_name', 'sections.section_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) { 
                        $sectionsqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                    }
                }
            }

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
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }




            $sections = $sectionsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = Sections::leftjoin('classes', 'classes.id', 'sections.class_id')
                ->where('sections.id', '>', 0)->where('classes.status','=','ACTIVE')
                ->where('sections.status','=','ACTIVE')
                ->select('sections.id');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    //Students Attendance Presence
    /* Function: viewStudentAbsentReport
    */
    public function viewStudentAbsentReport()
    {
        if (Auth::check()) {
            $student = User::leftjoin('students', 'students.user_id', 'users.id')
            ->where('users.user_type', 'STUDENT')
            ->where('users.status','ACTIVE')
            ->select('users.*')->get();

            // $data['student'] = Student::leftjoin('users','users.id','students.user_id')->where('users.status','=','ACTIVE')
            // ->get(["users.name", "users.id"])

            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            return view('admin.studentsabsent')->with(['class'=>$classes,'student'=>$student]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getStudentsPresence
    Datatable Load
    */
    public function getStudentAbsentReport(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $class_id = $request->get('class_id',0);
            $section_id = $request->get('section_id',0);
            $user_id = $request->get('student_id',0);
            $date = $request->get('date','');

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
            $filteredqry = AttendanceApproval::leftjoin('classes','classes.id','attendance_approval.class_id')
            ->leftjoin('sections','sections.id','attendance_approval.section_id')
            ->leftjoin('users','users.id','attendance_approval.user_id')
            ->where(function($query) {
                $query->where('attendance_approval.fn_status', 2)
                      ->orWhere('attendance_approval.an_status', 2);
            })
            ->where('attendance_approval.admin_status', 1)
            ->select('attendance_approval.*','classes.class_name','sections.section_name','users.name');
          
          
            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) { 
                        $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                    }
                }
            }

            if(!empty($date)){
                $users_qry->where('date',$date);
                $filteredqry->where('date',$date);
            }
            
            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filteredqry->where('users.school_college_id', Auth::User()->id);
            }

            if($user_id != '' || $user_id != 0){
                $users_qry->where('attendance_approval.user_id',$user_id);
                $filteredqry->where('attendance_approval.user_id',$user_id);

             }
             if($class_id != '' || $class_id != 0){
                $users_qry->where('attendance_approval.class_id',$class_id);
                $filteredqry->where('attendance_approval.class_id',$class_id);

             }
             if($section_id != '' || $section_id != 0){
                $users_qry->where('attendance_approval.section_id',$section_id);
                $filteredqry->where('attendance_approval.section_id',$section_id);

             }


            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }




            $sections = $users_qry->skip($start)->take($length)->orderby($orderby, $dir)->get();

            $filters = $filteredqry->select('id')->count();

            $totalDataqry = AttendanceApproval::leftjoin('classes','classes.id','attendance_approval.class_id')
            ->leftjoin('sections','sections.id','attendance_approval.section_id')
            ->leftjoin('users','users.id','attendance_approval.user_id')->where('users.school_college_id', Auth::User()->id)
            ->where(function($query) {
                $query->where('attendance_approval.fn_status', 2)
                      ->orWhere('attendance_approval.an_status', 2);
            })
            ->where('attendance_approval.admin_status', 1);

            $totalData = $filteredqry->select('attendance_approval.id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($sections)) {
                foreach ($sections as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    public function getExcelTodayStudentLeaveReports(Request $request)
    {

        if (Auth::check()) {
           $user_id = $request->get('student_id','');
           $class_id = $request->get('class_id', '');
           $section_id = $request->get('section_id','');
           $date = $request->get('date', '');

           $limit = $request->get('length', '10');
           $start = $request->get('start', '0');
           $dir = $request->input('order.0.dir');
           $columns = $request->get('columns');
           $order = $request->input('order.0.column');

           $input = $request->all();
         
           $users_qry = AttendanceApproval::leftjoin('classes','classes.id','attendance_approval.class_id')
           ->leftjoin('sections','sections.id','attendance_approval.section_id')
           ->leftjoin('users','users.id','attendance_approval.user_id')
           ->where(function($query) {
               $query->where('attendance_approval.fn_status', 2)
                     ->orWhere('attendance_approval.an_status', 2);
           })
           ->where('attendance_approval.admin_status', 1)
           ->select('attendance_approval.*','classes.class_name','sections.section_name','users.name','users.mobile','users.mobile1'); 
         
           if (count($columns) > 0) {
               foreach ($columns as $key => $value) {
                   if (!empty($value['name']) && !empty($value['search']['value'])) { 
                       $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                   }
               }
           }

           if(!empty($date)){
               $users_qry->where('date',$date); 
           }
           
           if($user_id != '' || $user_id != 0){
               $users_qry->where('attendance_approval.user_id',$user_id);  
            }
            if($class_id != '' || $class_id != 0){
               $users_qry->where('attendance_approval.class_id',$class_id);  
            }
            if($section_id != '' || $section_id != 0){
               $users_qry->where('attendance_approval.section_id',$section_id);  
            }


           if (!empty($order)) {
               $orderby = $columns[$order]['name'];
           } else {
               $orderby = 'id';
           }
           if (empty($dir)) {
               $dir = 'DESC';
           }

            $users = $users_qry->orderBy($orderby, $dir)->get();
         
            $teacher_leave_excel = [];

     if (! empty($users)) {
         $i = 1;
         foreach ($users as $rev) {
          $teacher_leave_excel[] = [
                 "S.No" => $i,
                 "Student Name" => $rev->is_student_name,
                 "Class Name" => $rev->is_class_name,
                 "Section Name" => $rev->is_section_name,
                 "Father Mobile" => $rev->mobile,
                 "Mother Mobile" => $rev->mobile1,
                 "Date" => $rev->date,
                 "Forenoon" => $rev->is_fn_status,
                 "Afternoon" => $rev->is_an_status,
                
             ];

             $i++;
         }
     }

     header("Content-Type: text/plain");
     $flag = false;
     foreach ($teacher_leave_excel as $row) {
         if (! $flag) {
             // display field/column names as first row
             echo implode("\t", array_keys($row)) . "\r\n";
             $flag = true;
         }
         echo implode("\t", array_values($row)) . "\r\n";
     }
     exit();

        } else {
            return redirect('/admin/login');
        }

    }

    public function viewStudentTestAttempts()
    {
        if (Auth::check()) {
            $student = User::leftjoin('students', 'students.user_id', 'users.id')
            ->where('users.user_type', 'STUDENT')
            ->where('users.status','ACTIVE')
            ->select('users.*')->get();

            $classes = Classes::where('status', 'ACTIVE');
            if(Auth::User()->user_type == 'SCHOOL') {
                $classes->where('school_id', Auth::User()->id); 
            }
            $classes = $classes->orderby('position', 'Asc')->get();
            return view('admin.studenttestattempts')->with(['class'=>$classes,'student'=>$student]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getStudentsTestAttempts
    Datatable Load
     */
    public function getStudentsTestAttempts(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $class_id = $request->get('class_id','');
            $section_id = $request->get('section_id','');
            $student_id = $request->get('student_id','');
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
            $users_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name', 
                    DB::RAW(' count(student_tests.id) as attempt_count ')
                );
            $filtered_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name', 
                    DB::RAW(' count(student_tests.id) as attempt_count '));

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'tests.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('users.school_college_id', Auth::User()->id);
                $filtered_qry->where('users.school_college_id', Auth::User()->id);
            }

            if($student_id != '' || $student_id != 0){
                $users_qry->where('student_tests.user_id',$student_id);
                $filtered_qry->where('student_tests.user_id',$student_id);
             }

             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
                $filtered_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '.$maxdate));
                $users_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
                $filtered_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
            }
            
            if($class_id != '' || $class_id != 0){
                $users_qry->where('student_tests.class_id',$class_id);
                $filtered_qry->where('student_tests.class_id',$class_id);
             }
            

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'student_tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $studenttest = $users_qry->groupby('student_tests.user_id')->groupby('student_tests.test_id')->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filtered_qry->groupby('student_tests.user_id')->groupby('student_tests.test_id')->select('student_tests.id')->get();

            // $totalDataqry = Tests::orderby('id', 'asc');
            $totalDataqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
            ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
            ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
            ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
            ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
            ->leftjoin('tests', 'tests.id', 'student_tests.test_id')->where('users.school_college_id', Auth::User()->id)
            ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');
            $totalData = $totalDataqry->groupby('student_tests.user_id')->groupby('student_tests.test_id')->select('student_tests.id')->get()->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = count($filters);
            }

            $data = [];
            if (!empty($studenttest)) {
                foreach ($studenttest as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }
    }

    public function viewTestAttemptResult(Request $request)
    {
        if (Auth::check()) {
            TestItems::$admin = 1;
            $user_id = $request->get('uid'); $test_id = $request->get('tid'); 
            $attempts = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name' 
                )->where('student_tests.user_id', $user_id)->where('student_tests.test_id', $test_id)
                ->get(); 
            return view('admin.viewtestattemptresult')->with('attempts', $attempts);
        } else {
            return redirect('/admin/login');
        }
    }

 
    public function send_attendance_notification() {
        $date = date('Y-m-d');
        
        $pending = DB::table('attendance_approval_class_section')->where('sent_notification', 0)
            ->whereDate('date', $date)->skip(0)->take(5)->get(); 
 
        if($pending->isNotEmpty()) {
            foreach($pending as $pen) {     
                $class_id = $pen->class_id;
                $section_id = $pen->section_id;

                $users = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id') 
                    ->where('user_type', 'STUDENT')->where('students.delete_status', 0)
                    ->where('users.status','ACTIVE')
                    ->where('students.class_id', $pen->class_id)->where('students.section_id', $pen->section_id)
                    ->select('users.id', 'users.gender') 
                    ->get();
                //echo "<pre>"; print_r($users);  
                if($users->isNotEmpty()) {
                    foreach($users as $user) {
                        $ex = DB::table('attendance_approval')->where('class_id', $class_id)
                            ->where('section_id', $section_id)->where('user_id', $user->id)->where('date', $date)->first();
                        $fn_status = $an_status = 0; $gender = $user->gender;
                        if(!empty($ex)) {
                            $sent_notification = $ex->sent_notification;
                            if($ex->sent_notification == 0) {
                                $fn_status = $ex->fn_status;
                                $an_status = $ex->an_status;
                                $exid = $ex->id;

                                DB::table('attendance_approval')->where('class_id', $class_id)
                                ->where('section_id', $section_id)->where('user_id', $user->id)->where('date', $date)
                                ->update(['admin_status'=>1, 'sent_notification'=>1, 'updated_by'=>1, 
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }   else {
                            $fn_status = 0;
                            $an_status = 0;
                            $sent_notification = 0;
                            $exid = DB::table('attendance_approval')
                                ->insertGetId(['date'=>$date, 'class_id'=>$class_id, 
                                    'section_id'=>$section_id, 'user_id'=>$user->id,
                                    'fn_status'=>0, 'an_status'=>0, 'admin_status'=>1, 
                                    'sent_notification'=>1,
                                    'created_by'=>1, 
                                    'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                        if($sent_notification == 0) {
                            $ward = 'Ward';
                            if($gender == 'MALE') {
                                $ward = 'Son';
                            }   else {
                                $ward = 'Daughter';
                            }
                            $msg = '';
                            if($fn_status == 1 && $an_status == 1) {
                                $msg = 'Your '.$ward.' is Present Today';
                            } elseif($fn_status == 2 && $an_status == 2) {
                                $msg = 'Your '.$ward.' is Absent Today';
                            } elseif($fn_status == 2) {
                                $msg = 'Your '.$ward.' is Absent for Morning Half-day';
                            } elseif($an_status == 2) {
                                $msg = 'Your '.$ward.' is Absent for Aftertoon Half-day';
                            }

                            $type_no = 1;
                            $title = 'Attendance';
                            $message = $msg;
                            $fcmMsg = array("fcm" => array("notification" => array(
                                "title" => $title,
                                "body" => $message,
                                "type" => $type_no,
                              )));

                            CommonController::push_notification($user->id, $type_no, $exid, $fcmMsg);
                        } 
                    }

                    $users_count = count($users);
                    $stud_count = DB::table('attendance_approval')->where('class_id', $class_id)
                            ->where('section_id', $section_id)->where('sent_notification', 1)->where('date', $date)->count();
                    if($users_count == $stud_count) {
                        DB::table('attendance_approval_class_section')->where('sent_notification', 0)
                            ->whereDate('date', $date)->where('class_id', $pen->class_id)->where('section_id', $pen->section_id)
                            ->update(['sent_notification'=>1]);
                    }
                }  
 
            }
        }  
    }

    // Communication posts 
    public function viewPosts(Request $request)  {
        if (Auth::check()) { 
            $limit = 50;  $page_no = 0;  $school_id = Auth::User()->id;
            $categories = Category::where('status', 'ACTIVE')->where('school_id', $school_id)->orderby('position', 'asc')->get(); 
            $posts = CommunicationPost::where('delete_status', 0)->where('posted_by', $school_id)
            ->orderby('id', 'desc')
            ->paginate($limit, ['communication_posts.*'], 'page', $page_no);
 
            return view('admin.posts')->with('categories', $categories)->with('posts', $posts);
        } else {
            return redirect('/admin/login');
        }
    }

    public function filterThings(Request $request) {
        if (Auth::check()) { 
            $filter_page = $request->get('filter_page', 0);
            $filter_pagename = $request->get('filter_pagename', '');
            $filter_input_id = $request->get('filter_input_id', 0); 

            $from_date = $request->get('filter_from_date', '');
            $to_date = $request->get('filter_to_date', ''); 
            $search = $request->get('filter_search', ''); 
            $filter_category_id = $request->get('filter_category_id', ''); 

            $school_id = Auth::User()->id;

            $limit = 50;
            $filter_pagename = trim($filter_pagename);
            if(!empty($filter_pagename)) {
                switch ($filter_pagename) {
                    case 'communcation_posts':  
                        $page_no = $filter_page;  
                        $posts = CommunicationPost::where('delete_status', 0)->where('posted_by', $school_id);

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('communication_posts.notify_datetime >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                            $posts->whereRaw('communication_posts.notify_datetime <= ?', [$to_date]); 
                        }
                        if($filter_category_id > 0) { 
                            $posts->where('communication_posts.category_id', $filter_category_id); 
                        } 
                        if(!empty(trim($search))) { 
                            $posts->whereRaw(' ( title like "%'.$search.'%" or message like "%'.$search.'%" ) '); 
                        }

                        $posts = $posts->orderby('id', 'desc')
                            ->paginate($limit, ['communication_posts.*'], 'page', $page_no);
                        $content =  view('admin.posts_list')->with('posts',$posts)->render(); 

                        return response()->json(['status'=>1, 'message' => 'Posts list','data'=>$content]);


                    break;

                    case 'communcation_postsms':  
                        $page_no = $filter_page;  
                        $posts = CommunicationSms::where('delete_status', 0)->where('posted_by', $school_id);

                        if(!empty(trim($from_date))) {
                            $from_date = date('Y-m-d', strtotime($from_date));
                            $posts->whereRaw('communication_sms.notify_datetime >= ?', [$from_date]); 
                
                        }
                        if(!empty(trim($to_date))) {
                            $to_date = date('Y-m-d', strtotime('+1 day'.$to_date));
                            $posts->whereRaw('communication_sms.notify_datetime <= ?', [$to_date]); 
                        }
                        if($filter_category_id > 0) { 
                            $posts->where('communication_sms.category_id', $filter_category_id); 
                        } 
                        if(!empty(trim($search))) { 
                            $posts->whereRaw(' ( content like "%'.$search.'%" ) '); 
                        }

                        $posts = $posts->orderby('id', 'desc')
                            ->paginate($limit, ['communication_sms.*'], 'page', $page_no);
                        $content =  view('admin.postsms_list')->with('posts',$posts)->render(); 

                        return response()->json(['status'=>1, 'message' => 'Posts list','data'=>$content]);


                    break; 
                }
            }

        } else{
            return response()->json([ 'status' => 0, 'message' => 'Session Logged Out']); 
        }
    }

    public function addPosts()
    {
        if (Auth::check()) {

            $get_category=Category::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_background=BackgroundTheme::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','name','theme','image')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->id)->get(); 


            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->id)->get(); 

            $classes = Classes::where('status', 'ACTIVE')->where('school_id', Auth::User()->id)->orderby('position', 'Asc')->get();


            return view('admin.communicationscholar',compact('get_category','get_background','get_groups','get_student','get_sections', 'classes'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function editPosts(Request $request)
    {
        if (Auth::check()) {

            $get_category=Category::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_background=BackgroundTheme::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','name','theme','image')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->id)->get();  

            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->id)->get(); 

            $post = CommunicationPost::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
            }
            return view('admin.communicationscholaredit',compact('get_category','get_background','get_groups','get_student',
                    'get_sections', 'post'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function viewPostStatus(Request $request) {
        if (Auth::check()) {
            $post = CommunicationPost::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
                $post_receivers = CommunicationPost::getIsReceiversAttribute($request->get('id'));
            }  //echo "<pre>"; print_r($post_receivers); exit;
            return view('admin.post_status',compact('post', 'post_receivers'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function getPostStatus(Request $request) {

        if (Auth::check()) {

            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $status = $request->get('status_id', '');
            $section = $request->get('section_id', '');
            $class_id = $request->get('class_id', '');

            $post_id = $request->get('id');
            $post  = DB::table('communication_posts')->where('id', $post_id)->first();

            $users_qry = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    //->leftjoin('notifications', 'notifications.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->id)
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                    //->where('notifications.type_no', 4)->where('notifications.post_id', $post_id)   
                    ->select('users.id', 'users.name',  'users.fcm_id'); 

            $filtered_qry = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    //->leftjoin('notifications', 'notifications.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->id)
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                    //->where('notifications.type_no', 4)->where('notifications.post_id', $post_id)   
                    ->select('users.id', 'users.name',  'users.fcm_id'); 

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
                            $filtered_qry->whereIn('students.section_id', $section_ids);
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
                            $filtered_qry->whereIn('students.user_id', $user_ids);
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
                            $filtered_qry->whereIn('students.user_id', $user_ids);
                        }
                    }
                }  
            } 

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            } 

            if(!empty($status)){
                $users_qry->where('users.status',$status);
                $filtered_qry->where('users.status',$status);
            }
            if(!empty($section)){
                $users_qry->where('students.section_id',$section);
                $filtered_qry->where('students.section_id',$section);
            }
            if(!empty($class_id)){
                $users_qry->where('students.class_id',$class_id);
                $filtered_qry->where('students.class_id',$class_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'students.user_id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            if($users->isNotEmpty()) {
                foreach($users as $uk=>$usr) {
                    $notify = DB::table('notifications')->where('type_no', 4)->where('post_id', $post_id)
                        ->where('user_id', $usr->id)->first();
                    $users[$uk]->notify = $notify;
                }
            }
            $totalData = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0) 
                    ->select('users.id', 'users.fcm_id');

            if(Auth::User()->user_type == 'SCHOOL') {
                $totalData->where('users.school_college_id', Auth::User()->id); 
            }
            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    public function postCommunication(Request $request){

        if (Auth::check()) {

       // Auth::User()->id;
     //  date('Y-m-d H:i:s', strtotime($request->enq_date));
       $post_id   = $request->post_id; 

       $title=$request->title;
       $message=$request->message;
       $title_push=$request->title_push;
       $message_push=$request->message_push;
       $category=$request->category;
       $batch=$request->batch;
       $post_type=$request->post_type;
       $bg_color=$request->bg_color;
       $req_ack=$request->req_ack;
       $receiver_end=0;
       $youtube_link = $request->youtube_link;


       if ($post_type == 4) {
        // Check if 'group_post' is an array
        if (is_array($request->group_post)) {
            // Convert the array to a comma-separated string
            $receiver_end = implode(',', $request->group_post);
        } else {
            // Handle the case where 'group_post' is not an array (optional)
            $receiver_end = '';
        }
       }

       if ($post_type == 3) {

        $receiver_end =0;
       }

       if ($post_type == 2) {
        // Check if 'group_post' is an array
        if (is_array($request->student_post)) {
            // Convert the array to a comma-separated string
            $receiver_end = implode(',', $request->student_post);
        } else {
            // Handle the case where 'student_post' is not an array (optional)
            $receiver_end = '';
        }
       }

       if ($post_type == 1) {
            // Check if 'group_post' is an array
            $receiver_end = '';

            $receiver_arr = [];
            if (is_array($request->section_post)) {
                $receiver_arr = array_merge($receiver_arr, $request->section_post);
            } 
            if (is_array($request->class_post)) {
                $class_post = $request->class_post;
                $classes = implode(',', $request->class_post);
                if(is_array($class_post) && count($class_post)>0) { 
                    $get_sections = Sections::where('status','ACTIVE')->whereIn('class_id',$class_post)
                        ->where('school_id',Auth::User()->id)->select('id')->get();
                    if($get_sections->isNotEmpty()) {
                        foreach($get_sections as $sec) {
                            $receiver_arr[] = $sec->id; 
                        }
                    }
                }
            }  
            $receiver_arr = array_unique($receiver_arr); 
            $receiver_arr = array_filter($receiver_arr); 
            $receiver_end = implode(',', $receiver_arr);
       }
    
       $schedule_date = $request->has('schedule_date') ? $request->schedule_date : now();


        $validator= Validator::make($request->all(),
            [
                'title' => 'required',
                'message' => 'required',
                'title_push' => 'required',
                'message_push' => 'required',
            ],[]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if($post_id > 0) {
                $post_new= CommunicationPost::find($post_id);
            } else {
                $post_new= new CommunicationPost;
            }

            $post_new->title=$title;
            $post_new->message=$message;
            $post_new->title_push=$title_push;
            $post_new->message_push=$message_push;
            $post_new->category_id=$category;
            $post_new->batch=$batch;
            $post_new->post_type=$post_type;
            $post_new->receiver_end=$receiver_end;
            $post_new->background_id=$bg_color;
            $post_new->request_acknowledge=$req_ack;
            $post_new->notify_datetime=$schedule_date;
            $post_new->posted_by=Auth::User()->id;
            $post_new->youtube_link=$youtube_link;

            $images = $request->file('image_attachment'); $files = [];
            if(is_array($images) && count($images)>0) {
                $allowedExtensions = [ 'jpg', 'jpeg', 'png' ];
                foreach($images as $image) {
                    if (!empty($image) && $image != 'null') { 
                        $ext = $image->getClientOriginalExtension();
                        $ext = strtolower($ext);
                        if(!in_array($ext, $allowedExtensions)) {
                            return response()->json(['status' => 0, 'message' => 'File format wrong. Please upload jpeg,jpg,png']);
                        }
                    }
                }

                foreach($images as $image) {
                    if (!empty($image) && $image != 'null') { 
                        $ext = $image->getClientOriginalExtension();
                        $ext = strtolower($ext);
                        $image1_name = rand() . time() . '.' . $ext; 
                        $image->move(public_path('uploads/media/'), $image1_name); 
                        $files[] = $image1_name;
                    }
                    $post_new->image_attachment = implode(',', $files);
                }
            } 

            $image = $request->file('media_attachment'); 
            if (!empty($image) && $image != 'null') { 
                $allowedExtensions = [ 'mp3', 'wav' ];  
                $ext = $image->getClientOriginalExtension();
                $ext = strtolower($ext);
                if(!in_array($ext, $allowedExtensions)) {
                    return response()->json(['status' => 0, 'message' => 'File format wrong. Please upload mp3,wav']);
                } 
                $image1_name = rand() . time() . '.' . $ext; 
                $image->move(public_path('uploads/media/'), $image1_name);  
                $post_new->media_attachment = $image1_name; 
            } 

            $image = $request->file('video_attachment'); 
            if (!empty($image) && $image != 'null') { 
                $allowedExtensions = [ 'mp4', 'wmv' ];  
                $ext = $image->getClientOriginalExtension();
                $ext = strtolower($ext);
                if(!in_array($ext, $allowedExtensions)) {
                    return response()->json(['status' => 0, 'message' => 'File format wrong. Please upload mp4,wmv']);
                } 
                $image1_name = rand() . time() . '.' . $ext; 
                $image->move(public_path('uploads/media/'), $image1_name);  
                $post_new->video_attachment = $image1_name; 
            } 

            $images = $request->file('files_attachment'); $files = [];
            if(is_array($images) && count($images)>0) {
                $allowedExtensions = [ 'doc', 'pdf', 'xls', 'pptx', 'xlsx', 'docx' ];
                foreach($images as $image) {
                    if (!empty($image) && $image != 'null') { 
                        $ext = $image->getClientOriginalExtension();
                        $ext = strtolower($ext);
                        if(!in_array($ext, $allowedExtensions)) {
                            return response()->json(['status' => 0, 'message' => 'File format wrong. Please upload doc,pdf,xls,pptx']);
                        }
                    }
                }

                foreach($images as $image) {
                    if (!empty($image) && $image != 'null') { 
                        $ext = $image->getClientOriginalExtension();
                        $ext = strtolower($ext);
                        $image1_name = rand() . time() . '.' . $ext; 
                        $image->move(public_path('uploads/media/'), $image1_name); 
                        $files[] = $image1_name;
                    }
                    $post_new->files_attachment = implode(',', $files);
                }
            } 
            
            $post_new->save();

            return response()->json(['status'=>1,'message'=>'Post Created Successfully']);

        } else {
            return redirect('/admin/login');
        }
    }

    public function deletePosts(Request $request) {

        if (Auth::check()) {
            DB::table('communication_posts')->where('id', $request->get('post_id'))
                ->update(['delete_status'=>1, 'updated_by'=>Auth::User()->id, 'updated_at'=>date('Y-m-d H:i:s')]);

            return response()->json([ 'status' => 1, 'message' => 'Post deleted successfully']); 
        } else {
            return response()->json([ 'status' => 0, 'message' => 'Session Logged Out']); 
        }    
    } 
 

    // Communication posts SMS
    public function viewPostSms(Request $request)  {
        if (Auth::check()) { 
            $limit = 50;  
            $page_no = 0;
            $school_id = Auth::User()->id;
            $categories = Category::where('status', 'ACTIVE')->where('school_id', $school_id)->orderby('position', 'asc')->get(); 
            $posts = CommunicationSms::where('delete_status', 0)->where('posted_by', $school_id)
            ->orderby('id', 'desc')
            ->paginate($limit, ['communication_sms.*'], 'page', $page_no);

            return view('admin.postsms')->with('categories', $categories)->with('posts', $posts);
        } else {
            return redirect('/admin/login');
        }
    }

    public function addPostSms()
    {
        if (Auth::check()) {

            $get_category=Category::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_template=DltTemplate::where('status','ACTIVE')->select('id','name','content','type')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->id)->get();  


            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->id)->get(); 

            return view('admin.communication_sms_scholar',compact('get_category','get_template','get_groups','get_student','get_sections'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function postCommunicationSmsScholar(Request $request){

        if (Auth::check()) {

       // Auth::User()->id;
     //  date('Y-m-d H:i:s', strtotime($request->enq_date));

      
       $template_id=$request->template;
       $category=$request->category;
       $batch=$request->batch;
       $post_type=$request->post_type;
       $smart_sms=$request->smart_sms;
       $send_type=$request->send_type;
       $final_content=$request->final_content;
       $receiver_end=0;

       if ($post_type == 4) {
        // Check if 'group_post' is an array
        if (is_array($request->group_post)) {
            // Convert the array to a comma-separated string
            $receiver_end = implode(',', $request->group_post);
        } else {
            // Handle the case where 'group_post' is not an array (optional)
            $receiver_end = '';
        }
       }

       if ($post_type == 3) {

        $receiver_end =0;
       }

       if ($post_type == 2) {
        // Check if 'group_post' is an array
        if (is_array($request->student_post)) {
            // Convert the array to a comma-separated string
            $receiver_end = implode(',', $request->student_post);
        } else {
            // Handle the case where 'student_post' is not an array (optional)
            $receiver_end = '';
        }
       }

       if ($post_type == 1) {
        // Check if 'group_post' is an array
        if (is_array($request->section_post)) {
            // Convert the array to a comma-separated string
            $receiver_end = implode(',', $request->section_post);
        } else {
            // Handle the case where 'section_post' is not an array (optional)
            $receiver_end = '';
        }
       }
    
       $schedule_date = $request->has('schedule_date') ? $request->schedule_date : now();


        $validator= Validator::make($request->all(),
            [
                'template' => 'required',
                'category' => 'required',
                'post_type' => 'required',
               
            ],[]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            $post_new= new CommunicationSms;

            $post_new->template_id=$template_id;
            $post_new->category_id=$category;
            $post_new->batch=$batch;
            $post_new->post_type=$post_type;
            $post_new->receiver_end=$receiver_end;
            $post_new->smart_sms=$smart_sms;
            $post_new->send_type=$send_type;
            $post_new->content=$final_content;
           
            $post_new->notify_datetime=$schedule_date;
            $post_new->posted_by=Auth::User()->id;
     
            $post_new->save();

            return response()->json(['status'=>1,'message'=>'Sms Created Successfully']);

        } else {
            return redirect('/admin/login');
        }
    }

    public function editPostSms(Request $request)
    {
        if (Auth::check()) {

            $school_id = Auth::User()->id;

            $get_category=Category::where('status','ACTIVE')->where('school_id', $school_id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_template=DltTemplate::where('status','ACTIVE')->select('id','name','content','type')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->id)->get();  

            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->id)->get(); 

            $post = CommunicationSms::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
            }
            return view('admin.communicationsmsscholaredit',compact('get_category','get_template','get_groups','get_student',
                    'get_sections', 'post'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function deletePostSms(Request $request) {

        if (Auth::check()) {
            DB::table('communication_sms')->where('id', $request->get('post_id'))
                ->update(['delete_status'=>1, 'updated_by'=>Auth::User()->id, 'updated_at'=>date('Y-m-d H:i:s')]);

            return response()->json([ 'status' => 1, 'message' => 'Post SMS deleted successfully']); 
        } else {
            return response()->json([ 'status' => 0, 'message' => 'Session Logged Out']); 
        }    
    } 

    //view Categories
    public function viewCategories()
    {
        if (Auth::check()) {

            return view('admin.categories');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getCategories(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = Category::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $mclass = Category::where('school_id', $school_id)->get();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postCategories(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name; 
            $position = $request->position;
            $status = $request->status; 
            $school_id = Auth::User()->id;

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($id > 0) {
                $exists = DB::table('categories')->where('name', $name)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('categories')->where('name', $name)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Category Name Already Exists.",
                ]);
            }


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $cat = Category::find($id);
                $cat->updated_at = date('Y-m-d H:i:s');
            } else {
                $cat = new Category;
                $cat->created_at = date('Y-m-d H:i:s');
            }

            $cat->school_id = $school_id; 
            $cat->name = $name; 
            $cat->position = $position;
            $cat->status = $status; 

            $cat->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Category Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editCategories(Request $request)
    {

        if (Auth::check()) {
            $cat = Category::where('id', $request->code)->get();

            if ($cat->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $cat[0], 'message' => 'Category Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Category Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //view Background Themes
    public function viewBackgroundThemes()
    {
        if (Auth::check()) {

            return view('admin.background_themes');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getBackgroundThemes(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = BackgroundTheme::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $mclass = BackgroundTheme::where('school_id', $school_id)->get();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postBackgroundThemes(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name; 
            $position = $request->position;
            $status = $request->status; 
            $school_id = Auth::User()->id;

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($id > 0) {
                $exists = DB::table('background_themes')->where('name', $name)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('background_themes')->where('name', $name)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Theme Name Already Exists.",
                ]);
            }


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $bt = BackgroundTheme::find($id);
                $bt->updated_at = date('Y-m-d H:i:s');
            } else {
                $bt = new BackgroundTheme;
                $bt->created_at = date('Y-m-d H:i:s');
            }

            $image = $request->file('image');
            if (!empty($image)) {

                $ext = $image->getClientOriginalExtension();
                if (!in_array($ext, $this->accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                }

                $bannerimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/background_themes');

                $image->move($destinationPath, $bannerimg);

                $bt->image = $bannerimg;

            }

            $bt->school_id = $school_id; 
            $bt->name = $name; 
            $bt->position = $position;
            $bt->status = $status; 

            $bt->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Background Theme Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editBackgroundThemes(Request $request)
    {

        if (Auth::check()) {
            $bt = BackgroundTheme::where('id', $request->code)->get();

            if ($bt->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $bt[0], 'message' => 'Background Theme Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Background Theme Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }  

    //view Group
    public function viewGroup()
    {
        if (Auth::check()) {
            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->id)->get(); 
            return view('admin.group')->with('get_student',$get_student);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getGroup(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = CommunicationGroup::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $mclass = CommunicationGroup::where('school_id', $school_id)->get();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postGroup(Request $request)
    {
        if (Auth::check()) {
            $input = $request->all(); //echo "<pre>"; print_r($input); exit;
            $id = $request->id;
            $group_name = $request->group_name; 
            $student_id = $request->student_id;
            $status = $request->status; 
            $school_id = Auth::User()->id;

            $validator = Validator::make($request->all(), [
                'group_name' => 'required',
                'student_id' => 'required',
                'status' => 'required',
            ]);

            if ($id > 0) {
                $exists = DB::table('communication_groups')->where('group_name', $group_name)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('communication_groups')->where('group_name', $group_name)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Group Name Already Exists.",
                ]);
            }


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if(is_array($student_id) && count($student_id) > 0) {
                $student_id = implode(',', $student_id);
            }   else {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Please select the Scholars.",
                ]);
            }

            if ($id > 0) {
                $cat = CommunicationGroup::find($id);
                $cat->updated_at = date('Y-m-d H:i:s');
            } else {
                $cat = new CommunicationGroup;
                $cat->created_at = date('Y-m-d H:i:s');
            }

            $cat->school_id = $school_id; 
            $cat->group_name = $group_name; 
            $cat->members = $student_id;
            $cat->status = $status; 

            $cat->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Communication Group Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editGroup(Request $request)
    {

        if (Auth::check()) {
            $cat = CommunicationGroup::where('id', $request->code)->get();

            if ($cat->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $cat[0], 'message' => 'Communication Group Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Communication Group Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }  

    public function FeeTermsMaster()
    {
        if (Auth::check()) {

            return view('admin.feeterms_master');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeTerms(Request $request)
    
    {
        $status = $request->input('status');

        $fee_term = FeeTerm::where('school_id', Auth::User()->id)
            ->orderBy('position', 'ASC')
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
          
          return Datatables::of($fee_term)->make(true);
      
     }

     public function postFeeTerms(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $term_name = $request->term_name;
             $position = $request->position;
             $status = $request->status;
 
             $validator = Validator::make($request->all(), [
                 'term_name' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
             if ($id > 0) {
                 $exists = DB::table('fee_terms')->where('name', $term_name)
                     ->whereNotIn('id', [$id])->first();
             } else {
                 $exists = DB::table('fee_terms')->where('name', $term_name)->first();
             }
 
             if (!empty($exists)) {
                 return response()->json(['status' => 0, 'message' => 'Term Name Already Exists'], 201);
             }
 
             if ($id > 0) {
                 $post_mod = FeeTerm::find($id);
             } else {
                 $post_mod = new FeeTerm();
             }
             $post_mod->school_id = Auth::User()->id;
             $post_mod->name = $term_name;
             $post_mod->position = $position;
             $post_mod->status = $status;
             $post_mod->save();
             return response()->json(['status' => 1, 'message' => 'Fee Terms Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editFeeTerms(Request $request)
     {
         if (Auth::check()) {
             $get_data = FeeTerm::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Fee Terms Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Details Found']);
             }
         } else {
             return redirect('/admin/login');
         }
     }



    public function FeeItemsMaster()
    {
        if (Auth::check()) {


            $get_category=FeeCategory::where('status','ACTIVE')->orderBy('position','ASC')->get();

            return view('admin.feeitemsmaster',compact('get_category'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeItems(Request $request)
    
    {
        $status = $request->input('status');

        $fee_items = FeeItems::where('school_id', Auth::User()->id)
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
          
          return Datatables::of($fee_items)->make(true);
      
     }

     public function postFeeItems(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $category_id = $request->category_id;
             $item_name = $request->item_name;
             $item_code = $request->item_code;
             $status = $request->status;

             //School Id need to map.
 
             $validator = Validator::make($request->all(), [
                 'item_name' => 'required',
                 'item_code' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
             if ($id > 0) {
             $exists = DB::table('fee_items')->where('item_code', $item_code)->where('category_id', $category_id)
                      ->whereNotIn('id', [$id])->first();
             } else {
                  $exists = DB::table('fee_items')->where('item_code', $item_code)->where('category_id', $category_id)->first();
             }
 
             if (!empty($exists)) {
                  return response()->json(['status' => 0, 'message' => 'Fee Item Code Already Exists'], 201);
             }
 
             if ($id > 0) {
                 $post_bank = FeeItems::find($id);
             } else {
                 $post_bank = new FeeItems();
             }
             $post_bank->school_id = Auth::User()->id;
             $post_bank->item_name = $item_name;
             $post_bank->item_code = $item_code;
             $post_bank->category_id = $category_id;
             $post_bank->status = $status;
             $post_bank->save();
             return response()->json(['status' => 1, 'message' => 'Fee Item Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editFeeItems(Request $request)
     {
         if (Auth::check()) {
             $get_data = FeeItems::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Fee Item Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Details Found']);
             }
         } else {
             return redirect('/admin/login');
         }
     }



    public function BankListMaster()
    {
        if (Auth::check()) {

            return view('admin.schoolbanklist');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getBankList(Request $request)
    
    {
        $status = $request->input('status');

        $bank_master = SchoolBankList::where('school_id', Auth::User()->id)
            ->orderBy('position', 'ASC')
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
          
          return Datatables::of($bank_master)->make(true);
      
     }

     public function postBankList(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $bank_name = $request->bank_name;
             $account_number = $request->account_number;
             $ifsc_code = $request->ifsc_code;
             $branch_name = $request->branch_name;
             $position = $request->position;
             $status = $request->status;

             //School Id need to map.
 
             $validator = Validator::make($request->all(), [
                 'bank_name' => 'required',
                 'account_number' => 'required',
                 'branch_name' => 'required',
                 'ifsc_code' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
              if ($id > 0) {
                  $exists = DB::table('school_bank_lists')->where('account_no', $account_number)
                      ->whereNotIn('id', [$id])->first();
              } else {
                  $exists = DB::table('school_bank_lists')->where('account_no', $account_number)->first();
              }
 
              if (!empty($exists)) {
                  return response()->json(['status' => 0, 'message' => 'Account Number Already Exists'], 201);
              }
 
             if ($id > 0) {
                 $post_bank = SchoolBankList::find($id);
             } else {
                 $post_bank = new SchoolBankList();
             }
          
             $post_bank->bank_name = $bank_name;
             $post_bank->account_no = $account_number;
             $post_bank->branch_name = $branch_name;
             $post_bank->ifsc_code = $ifsc_code;
             $post_bank->position = $position;
             $post_bank->status = $status;
             $post_bank->save();
             return response()->json(['status' => 1, 'message' => 'Bank Master Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editBankList(Request $request)
     {
         if (Auth::check()) {
             $get_data = SchoolBankList::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Bank Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Bank Detail']);
             }
         } else {
             return redirect('/admin/login');
         }
     }



    public function ConcessionCategoryMaster()
    {
        if (Auth::check()) {

            return view('admin.concession_category');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getConcessionCategory(Request $request)
    
    {
        $status = $request->input('status');

        $concession_category = ConcessionCategory::where('school_id', Auth::User()->id)
            ->orderBy('position', 'ASC')
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
          
          return Datatables::of($concession_category)->make(true);
      
     }

     public function postConcessionCategory(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $concession_name = $request->concession_name;
             $position = $request->position;
             $status = $request->status;
 
             $validator = Validator::make($request->all(), [
                 'concession_name' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
             if ($id > 0) {
                 $exists = DB::table('concession_categories')->where('name', $concession_name)
                     ->whereNotIn('id', [$id])->first();
             } else {
                 $exists = DB::table('concession_categories')->where('name', $concession_name)->first();
             }
 
             if (!empty($exists)) {
                 return response()->json(['status' => 0, 'message' => 'Name Already Exists'], 201);
             }
 
             if ($id > 0) {
                 $post_cat = ConcessionCategory::find($id);
             } else {
                 $post_cat = new ConcessionCategory();
             }
             $post_cat->school_id = Auth::User()->id;
             $post_cat->name = $concession_name;
             $post_cat->position = $position;
             $post_cat->status = $status;
             $post_cat->save();
             return response()->json(['status' => 1, 'message' => 'Concession Name Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editConcessionCategory(Request $request)
     {
         if (Auth::check()) {
             $get_data = ConcessionCategory::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Category Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Category Detail']);
             }
         } else {
             return redirect('/admin/login');
         }
     }

    public function FeeCancelReasonMaster()
    {
        if (Auth::check()) {

            return view('admin.feecancelreason');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeCancelReason(Request $request)
    
    {
        $status = $request->input('status');

        $cancel_reason = FeeCancelReason::where('school_id', Auth::User()->id)
            ->orderBy('position', 'ASC')
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
          
          return Datatables::of($cancel_reason)->make(true);
      
     }

     public function postFeeCancelReason(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $cancel_reason = $request->cancel_reason;
             $reason_code = $request->reason_code;
             $position = $request->position;
             $status = $request->status;
 
             $validator = Validator::make($request->all(), [
                 'cancel_reason' => 'required',
                 'reason_code' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
             if ($id > 0) {
                 $exists = DB::table('fee_cancel_reasons')->where('cancel_reason', $cancel_reason)
                     ->whereNotIn('id', [$id])->first();
             } else {
                 $exists = DB::table('fee_cancel_reasons')->where('cancel_reason', $cancel_reason)->first();
             }
 
             if (!empty($exists)) {
                 return response()->json(['status' => 0, 'message' => 'Reason Already Exists'], 201);
             }
 
             if ($id > 0) {
                 $post_reason = FeeCancelReason::find($id);
             } else {
                 $post_reason = new FeeCancelReason();
             }
             $post_reason->school_id = Auth::User()->id;
             $post_reason->cancel_reason = $cancel_reason;
             $post_reason->reason_code = $reason_code;
             $post_reason->position = $position;
             $post_reason->status = $status;
             $post_reason->save();
             return response()->json(['status' => 1, 'message' => 'Reason Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editFeeCancelReason(Request $request)
     {
         if (Auth::check()) {
             $get_data = FeeCancelReason::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Reason Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Reason Detail']);
             }
         } else {
             return redirect('/admin/login');
         }
     }


    public function PaymentModeMaster()
    {
        if (Auth::check()) {

            return view('admin.paymentmodes');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getPaymentMode(Request $request)
    
    {
        $status = $request->input('status');

        $payment_mode = PaymentMode::where('school_id', Auth::User()->id)->orderBy('position', 'ASC')
                            ->when(!empty($status), function ($query) use ($status) {
                                return $query->where('status', $status);
                            })
                            ->get();
          
          return Datatables::of($payment_mode)->make(true);
      
     }

     public function postPaymentMode(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $payment_mode = $request->payment_mode;
             $position = $request->position;
             $status = $request->status;
 
             $validator = Validator::make($request->all(), [
                 'payment_mode' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
             if ($id > 0) {
                 $exists = DB::table('payment_modes')->where('name', $payment_mode)
                     ->whereNotIn('id', [$id])->first();
             } else {
                 $exists = DB::table('payment_modes')->where('name', $payment_mode)->first();
             }
 
             if (!empty($exists)) {
                 return response()->json(['status' => 0, 'message' => 'Mode of Payment Already Exists'], 201);
             }
 
             if ($id > 0) {
                 $post_mod = PaymentMode::find($id);
             } else {
                 $post_mod = new PaymentMode();
             }
             $post_mod->school_id = Auth::User()->id;
             $post_mod->name = $payment_mode;
             $post_mod->position = $position;
             $post_mod->status = $status;
             $post_mod->save();
             return response()->json(['status' => 1, 'message' => 'Mode of Payment Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editPaymentMode(Request $request)
     {
         if (Auth::check()) {
             $get_data = PaymentMode::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'MOD Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No MOD Detail']);
             }
         } else {
             return redirect('/admin/login');
         }
     }


    public function feeCategoryMaster()
    {
        if (Auth::check()) {
            $accounts = Account::where('school_id', Auth::User()->id)->where('status', 'ACTIVE')->orderby('position','asc')->get();
            return view('admin.feecategory')->with('accounts', $accounts);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeCategory(Request $request)
    
    {
        $status = $request->input('status');

        $fee_category = FeeCategory::leftjoin('accounts', 'accounts.id', 'fee_categories.account_id')
            ->where('fee_categories.school_id', Auth::User()->id)->orderBy('fee_categories.position', 'ASC')
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('fee_categories.status', $status);
            })->select('fee_categories.*', 'accounts.account_name')
            ->get();
          
          return Datatables::of($fee_category)->make(true);
      
     }

     public function postFeeCategory(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
           
             $category_name = $request->category_name;
             $position = $request->position;
             $status = $request->status;
             $account_id = $request->account_id;
             $validator = Validator::make($request->all(), [
                 'account_id' => 'required',
                 'category_name' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
 
             if ($validator->fails()) {
 
                 $msg = $validator->errors()->all();
 
                 return response()->json([
 
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
 
             if ($id > 0) {
                 $exists = DB::table('fee_categories')->where('name', $category_name)->where('account_id', $account_id)
                     ->whereNotIn('id', [$id])->first();
             } else {
                 $exists = DB::table('fee_categories')->where('name', $category_name)->where('account_id', $account_id)->first();
             }
 
             if (!empty($exists)) {
                 return response()->json(['status' => 0, 'message' => 'Category Name Already Exists'], 201);
             }
 
             if ($id > 0) {
                 $post_fee_category = FeeCategory::find($id);
             } else {
                 $post_fee_category = new FeeCategory();
             }
             $post_fee_category->school_id = Auth::User()->id;
             $post_fee_category->account_id = $account_id;
             $post_fee_category->name = $category_name;
             $post_fee_category->position = $position;
             $post_fee_category->status = $status;
             $post_fee_category->save();
             return response()->json(['status' => 1, 'message' => 'Category Saved Successfully']);
         } else {
             return redirect('/admin/login');
         }
     }
 
     public function editFeeCategory(Request $request)
     {
         if (Auth::check()) {
             $get_data = FeeCategory::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Category Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Category Detail']);
             }
         } else {
             return redirect('/admin/login');
         }
     }

    public function receiptHeadMaster()
    {
        if (Auth::check()) {

            return view('admin.receipthead_master');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getReceiptHead(Request $request)
    
    {
        $status = $request->input('status');

        $receipt_head = ReceiptHead::where('school_id', Auth::User()->id)->orderBy('position', 'ASC')
                            ->when(!empty($status), function ($query) use ($status) {
                                return $query->where('status', $status);
                            })
                            ->get();
          
          return Datatables::of($receipt_head)->make(true);
      
     }

       /* Function: postReceiptHead
    Save into em_countries table
     */
    public function postReceiptHead(Request $request)
    {
        // return $request;
        if (Auth::check()) {
            $id = $request->id;
          
            $receipt_name = $request->receipt_name;
            $starting_number = $request->starting_number;
            $no_prefix = $request->no_prefix;
            $no_suffix = $request->no_suffix;
            $padding_digit = $request->padding_digit;
            $position = $request->position;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'receipt_name' => 'required',
                'starting_number' => 'required',
                'no_prefix' => 'required',
                'no_suffix' => 'required',
                'padding_digit' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('receipt_heads')->where('name', $receipt_name)
                    ->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('receipt_heads')->where('name', $receipt_name)->first();
            }

            if (!empty($exists)) {
                return response()->json(['status' => 0, 'message' => 'Receipt Name Already Exists'], 201);
            }

            if ($id > 0) {
                $post_receipt_head = ReceiptHead::find($id);
            } else {
                $post_receipt_head = new ReceiptHead();
            }

            $post_receipt_head->school_id = Auth::User()->id;
            $post_receipt_head->name = $receipt_name;
            $post_receipt_head->starting_number = $starting_number;
            $post_receipt_head->no_prefix = $no_prefix;
            $post_receipt_head->no_suffix = $no_suffix;
            $post_receipt_head->padding_digit = $padding_digit;
            $post_receipt_head->position = $position;
            $post_receipt_head->status = $status;

            $post_receipt_head->save();
            return response()->json(['status' => 1, 'message' => 'Receipt Head Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editReceiptHead(Request $request)
    {
        if (Auth::check()) {
            $get_data = ReceiptHead::where('id', $request->code)->get();
            if ($get_data->isNotEmpty()) {
                return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Receipt Head Detail']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'message' => 'No Receipt Head Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function feeStructureListPage()
    {
        if (Auth::check()) {
           
            return view('admin.fee_structure_list');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getBatches() {
        $batches = [];
        $academic_year = DB::table('student_class_mappings')->min('academic_year'); 
        $start = $academic_year;
        $current = date('Y');
        $next = $current + 1;

        for($i=$start; $i<=$next; $i++) { 
            $plus = $i + 1;
            $display_academic_year = $i .' - '. $plus;
            $batches[] = ['academic_year' => $i, 'display_academic_year' => $display_academic_year];
        }
        return $batches;
    }

    public function feeStructureAddPage()
    {
        if (Auth::check()) {

            $get_batches = $this->getBatches(); 

            $get_classes = Classes::where('status', 'ACTIVE')->where('school_id', Auth::User()->id)->orderby('position', 'Asc')->get(); 

            $get_fee_category = FeeCategory::where('status','ACTIVE')->where('school_id', Auth::User()->id)->orderBy('position','ASC')->get();

            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->id)->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->id)->select('id','group_name')->get();
           
            return view('admin.fee_structure_add',compact('get_classes','get_sections','get_groups','get_fee_category', 'get_batches'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function filterFeeCategoryItem(Request $request){

        if($request->ajax()){

        $category_id=$request->selected_category;
        $filter_data=DB::table('fee_items')->where('category_id',$category_id)->select('item_name','id','category_id','item_code')->get();

        return response()->json(['filter_data'=>$filter_data]);
        }
    }

    public function postNewFeeStructure(Request $request){

        if (Auth::check()) {

       // Auth::User()->id;
     //  date('Y-m-d H:i:s', strtotime($request->enq_date));


       $batch=$request->batch;
       $category_id=$request->category_id;
       $fee_type=$request->fee_type;
       $fee_post_type=$request->fee_post_type;

       $class_list = '';

       switch ($fee_post_type) {
           case 1:
               if (is_array($request->class_list)) {
                   $class_list = implode(',', $request->class_list);
               }
               break;

           case 2:
               if (is_array($request->section_list)) {
                   $class_list = implode(',', $request->section_list);
               }
               break;

           case 3:
               $class_list = '0';
               break;

           case 4:
               if (is_array($request->group_list)) {
                   $class_list = implode(',', $request->group_list);
               }
               break;
       }

        $validator= Validator::make($request->all(),
            [
                'batch' => 'required',
                'category_id' => 'required',
                'fee_type' => 'required',


            ],[]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            $post_new= new FeeStructureList;


            $post_new->school_id=Auth::User()->id;
            $post_new->fee_category_id=$category_id;
            $post_new->batch=$batch;
            $post_new->fee_type=$fee_type;
            $post_new->fee_post_type=$fee_post_type;
            $post_new->class_list=$class_list;
            $post_new->posted_by=Auth::User()->id;

            $post_new->save();


            if ($post_new) {
                // Loop through each array element
                foreach ($request->fee_item as $key => $fee_item) {
                    // Create a new CashinTypeLog instance
                    $update_items_list = new FeeStructureItem;

                    // Assign values from the arrays to the corresponding properties
                    $update_items_list->school_id=Auth::User()->id;
                    $update_items_list->fee_structure_id = $post_new->id;
                    $update_items_list->fee_item_id = $fee_item;
                    $update_items_list->gender = $request->gender[$key];
                    $update_items_list->amount = $request->fee_amount[$key];
                    $update_items_list->due_date = $request->due_date[$key];

                    $update_items_list->save();
                }
            }

            return response()->json(['status'=>1,'message'=>'Fee Structure Created Successfully']);

        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeStructureLists(Request $request)  {

        /*

        $fee_structure_list = FeeStructureList::where('school_id', $school_id)->get();

          return Datatables::of($fee_structure_list)->make(true);*/

        if (Auth::check()) {
            
            $school_id=Auth::User()->id;

            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $status = $request->get('status_id', '');
            $section = $request->get('section_id', '');
            $class_id = $request->get('class_id', '');

            $users_qry = FeeStructureItem::leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id') 
                ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                ->where('fee_structure_items.school_id', $school_id)
                ->select('fee_categories.name', 'fee_structure_items.*', 'fee_structure_lists.batch',
                    'fee_structure_lists.fee_category_id', 'fee_structure_lists.fee_type', 
                    'fee_structure_lists.fee_post_type', 'fee_structure_lists.class_list',
                    'fee_items.item_code', 'fee_items.item_name');

            $filtered_qry = FeeStructureItem::leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id') 
                ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                ->where('fee_structure_items.school_id', $school_id)
                ->select('fee_categories.name', 'fee_structure_items.*', 'fee_structure_lists.batch',
                    'fee_structure_lists.fee_category_id', 'fee_structure_lists.fee_type', 
                    'fee_structure_lists.fee_post_type', 'fee_structure_lists.class_list',
                    'fee_items.item_code', 'fee_items.item_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'users.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(Auth::User()->user_type == 'SCHOOL') {
                $users_qry->where('fee_structure_items.school_id', Auth::User()->id);
                $filtered_qry->where('fee_structure_items.school_id', Auth::User()->id);
            }

            if(!empty($status)){
                $users_qry->where('users.status',$status);
                $filtered_qry->where('users.status',$status);
            }
            /*if(!empty($section)){
                $users_qry->where('students.section_id',$section);
                $filtered_qry->where('students.section_id',$section);
            }
            if(!empty($class_id)){
                $users_qry->where('students.class_id',$class_id);
                $filtered_qry->where('students.class_id',$class_id);
            }*/

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fee_structure_items.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $totalData = FeeStructureItem::leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
                ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id') 
                ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id') 
                ->where('fee_structure_items.school_id', $school_id)
                ->select('fee_structure_items.id');

            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($users)) {
                $users = $users->toArray();
                foreach ($users as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
  

    }
    public function accountmaster() {
        if (Auth::check()) {
            $recepit = ReceiptHead::where('status','ACTIVE')->where('school_id', Auth::User()->id)
                ->orderBy('position','ASC')->get();
            return view('admin.account',compact('recepit'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function acclist(Request $request)
    {
        $status = $request->input('status');
        $accounts = Account::where('school_id', Auth::User()->id)
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
        return Datatables::of($accounts)->make(true);
    }

     public function addaccount(Request $request)
     {
         // return $request;
         if (Auth::check()) {
             $id = $request->id;
             $recepit = $request->recepit_id;
             $account_name = $request->account_name;
             $position = $request->position;
             $status = $request->status;
             //School Id need to map.
             $validator = Validator::make($request->all(), [
                 'account_name' => 'required',
                 'position' => 'required',
                 'status' => 'required',
             ]);
             if ($validator->fails()) {
                 $msg = $validator->errors()->all();
                 return response()->json([
                     'status' => 0,
                     'message' => "Please check your all inputs " . implode(', ', $msg),
                 ]);
             }
            //  if ($id > 0) {
            //      $exists = DB::table('school_bank_lists')->where('name', $bank_name)
            //          ->whereNotIn('id', [$id])->first();
            //  } else {
            //      $exists = DB::table('school_bank_lists')->where('name', $bank_name)->first();
            //  }
            //  if (!empty($exists)) {
            //      return response()->json(['status' => 0, 'message' => 'Name Already Exists'], 201);
            //  }
             if ($id > 0) {
                 $account_details = Account::find($id);
             } else {
                 $account_details = new Account();
             }
             $account_details->school_id = Auth::User()->id;
             $account_details->account_name = $account_name;
             $account_details->position = $position;
             $account_details->recepit_id = $recepit;
             $account_details->status = $status;
             $account_details->save();
             return response()->json(['status' => 1, 'message' => 'Recepit saved successfully']);
         } else {
             return redirect('/admin/login');
         }
     }

     public function editaccount(Request $request)
     {
         if (Auth::check()) {
             $get_account = Account::where('id', $request->code)->first();
             if ($get_account) {
                 return response()->json(['status' => 1, 'data' => $get_account, 'message' => 'Account Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Details Found']);
             }
         } else {
             return redirect('/admin/login');
         }
     }


    public function feeCollectionPage()
    {
        if (Auth::check()) {
   
            $school_id = Auth::User()->id;
            
            $get_classes = Classes::where('status', 'ACTIVE')->where('school_id', Auth::User()->id)->orderby('position', 'Asc')->get(); 

            $get_student=Student::where('status','ACTIVE')->where('school_id',$school_id)
                            ->where('delete_status','0')
                            ->get(); 

            $get_batches = $this->getBatches(); 

            $get_payment_mode=PaymentMode::where('status','ACTIVE')->orderBy('position','ASC')->get();

            return view('admin.fee_collection',compact('get_classes','get_student', 'get_batches', 'get_payment_mode'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function searchStudentNames(Request $request) {
        $school_id = Auth::user()->id;
        $name = $request->input('name');
        $class_id = $request->input('class_id');
        $batch = $request->input('batch');

        // Fetch all relevant students from the database
        $students = Student::query()
            ->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'students.user_id')
            ->where('students.school_id', $school_id)->where('students.status','ACTIVE')->where('students.delete_status','0');

            if($batch>0) {
                $students->where('student_class_mappings.academic_year', $batch);
            }

            if($class_id>0) {
                $students->where('student_class_mappings.class_id', $class_id);
            }

            $students = $students->get();

        // Filter the students in memory based on the appended 'is_student_name' attribute
        $filteredStudents = $students->filter(function($student) use ($name) {
            return stripos($student->is_student_name, $name) !== false;
        });

        // Return the filtered students
        return response()->json(['students' => $filteredStudents->values()]);
        }


    /*public function filterFeeCollections(Request $request){

        $school_id = Auth::user()->id;
        $studentId = $request->input('student_id');
        $batch = $request->input('batch');

        $get_class_id = Student::where('user_id',$studentId)->select('id','school_id','user_id','class_id','section_id','admission_no')->first();

        $class_id = $get_class_id->class_id;
        $gender = DB::table('users')->where('id', $studentId)->value('gender');

        FeeStructureList::$gender = $gender;
        $student = FeeStructureList::with(['feeItems.feeItem'])
                            ->where('school_id', $school_id)
                            ->where('batch', $batch)
                            ->whereRaw("FIND_IN_SET(?, class_list)", [$class_id])
                            ->select('id','school_id','batch','fee_category_id','fee_type','class_list')
                            ->get();

            // // Fetch paid records for the student
            // $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->get();

            // // Map paid records by fee_structure_item_id
            // $paid_records_map = [];
            // foreach ($get_paid_records as $record) {
            //     if (!isset($paid_records_map[$record->fee_structure_item_id])) {
            //         $paid_records_map[$record->fee_structure_item_id] = [
            //             'total_paid' => 0,
            //             'payment_status' => $record->payment_status
            //         ];
            //     }
            //     $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
            //     // Update payment status to the worst status encountered
            //     if ($record->payment_status == 'PARTIAL') {
            //         $paid_records_map[$record->fee_structure_item_id]['payment_status'] = 'PARTIAL';
            //     }
            // }

            // // Loop through each fee structure item and determine the payment status and balance amount
            // foreach ($student as $feeStructure) {
            //     foreach ($feeStructure->feeItems as $feeItem) {
            //         $fee_item_id = $feeItem->feeItem->id;
            //         $fee_amount = $feeItem->amount;
            //         $fee_status_flag = 0; // Default flag for not paid
            //         $balance_amount = $fee_amount; // Default balance amount to the full fee amount

            //         if (isset($paid_records_map[$fee_item_id])) {
            //             $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
            //             $payment_status = $paid_records_map[$fee_item_id]['payment_status'];

            //             $balance_amount = max($fee_amount - $total_paid, 0);

            //             if ($payment_status == 'PAID') {
            //                 $fee_status_flag = 1;
            //             } elseif ($payment_status == 'PARTIAL') {
            //                 $fee_status_flag = 2;
            //             }
            //         }

            //         // Ensure balance_amount is not null and attach the flag and balance to the fee item
            //         $feeItem->payment_status_flag = $fee_status_flag;
            //         $feeItem->balance_amount = $balance_amount;
            //     }
            // }


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
                $fee_item_id = $feeItem->id; // $feeItem->feeItem->id;
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

        return response()->json(['student' => $student,'student_detail'=>$get_class_id]);
    }*/

    public function filterFeeCollections(Request $request) {
        $school_id = Auth::user()->id;
        $studentId = $request->input('student_id');
        $batch = $request->input('batch');

        $scholar_fees_total = $scholar_fees_concession = $scholar_fees_paid = $scholar_fees_balance = 0; 

        // Fetch student details
        $get_class_id = Student::where('user_id', $studentId)
            ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
            ->first();
        $class_id = $get_class_id->class_id;
        $section_id = $get_class_id->section_id; 

        FeeStructureList::$student_id = $studentId; 

        // Retrieve fee structures
        $feeStructures1 = FeeStructureList::with(['feeItems.feeItem'])
            ->where('school_id', $school_id)->where('fee_type',1)
            ->where('batch', $batch)->orderby('id', 'asc')->get();



        // Fetch paid records for the student
        $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->where('cancel_status', 0)->get();

        // Map paid records by fee_structure_item_id
        $paid_records_map = [];  $paiditems = [];
        foreach ($get_paid_records as $record) {
            if (!isset($paid_records_map[$record->fee_structure_item_id])) {
                $paid_records_map[$record->fee_structure_item_id] = [ 
                    'amount_to_pay' => 0,
                    'total_paid' => 0,
                    'payment_status' => $record->payment_status,
                    'total_concession' => 0,
                ];

                $paiditems[] = $record->fee_structure_item_id;
            }
            /*if($record->amount_to_pay > 0 && $record->amount_paid == 0)  {
                $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_to_pay;
            }   else {
                $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
            }*/
            $paid_records_map[$record->fee_structure_item_id]['amount_to_pay'] += $record->amount_to_pay;
            $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
            $paid_records_map[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;
        }

        if(count($paiditems)>0){
            FeeStructureList::$paiditems = $paiditems;
            $feeStructures = FeeStructureList::with(['feeItems.feeItem'])
                ->where('school_id', $school_id)->where('fee_type','!=', 1)
                ->where('batch', $batch)
                //->union($feeStructures1)
                ->orderby('id', 'asc')
                ->get();
            if($feeStructures->isNotEmpty())  {
                //$feeStructures = array_merge($feeStructures1, $feeStructures);
                $feeStructures = $feeStructures1->merge($feeStructures); 
                $feeStructures = $feeStructures->all();
            }
        }  else {
            $feeStructures = $feeStructures1;
        } 

          //echo "<pre>"; print_r($paiditems); exit;

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

            if ($appliesToStudent) {
                foreach ($feeStructure->feeItems as $fk=>$feeItem) {
                    if($feeStructure->fee_type == 1) {
                        $scholar_fees_total += $feeItem->amount;
                    }

                    $due_date = $feeItem->due_date;

                    $fee_item_id = $feeItem->id;
                    $fee_amount = $feeItem->amount;
                    $fee_status_flag = 0;
                    $total_paid = 0; $total_concession = 0;
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

                        $scholar_fees_paid += $total_paid;
                        $scholar_fees_balance += $balance_amount;
                        $scholar_fees_concession += $total_concession;

                        $balance_amount = $balance_amount - $total_concession; 
                        
                    }   

                    if ($balance_amount == 0) {
                        $fee_status_flag = 1; // Fully paid
                    } elseif ($balance_amount <= $fee_amount) {
                        if(strtotime($due_date) == strtotime(date('Y-m-d'))) {
                            $fee_status_flag = 3; // On Due
                        }   else if(strtotime($due_date) < strtotime(date('Y-m-d'))) {
                            $fee_status_flag = 4; // Over Due
                        }   else {
                            $fee_status_flag = 2; // Partially paid
                        }  
                    }  

                    $feeItem->due_days = CommonController::getDueDays($due_date);
                    $feeItem->amount = $fee_amount;
                    $feeItem->payment_status_flag = $fee_status_flag;
                    $feeItem->balance_amount = $balance_amount;
                    $feeItem->paid_amount = $total_paid;
                    $feeItem->concession_amount = $total_concession;
                }  
                //echo "<pre>"; print_r($needtoshow); exit;

                $studentFeeStructures[] = $feeStructure;
            }
        } 
        //$scholar_fees_balance = $scholar_fees_balance - $scholar_fees_concession;
        $scholar_fees_balance = $scholar_fees_total - ($scholar_fees_paid + $scholar_fees_concession);
        $feedata = ['scholar_fees_total' => $scholar_fees_total, 'scholar_fees_concession' => $scholar_fees_concession, 
                    'scholar_fees_paid' => $scholar_fees_paid, 'scholar_fees_balance' => $scholar_fees_balance 
                    ];

        return response()->json(['student' => $studentFeeStructures, 'student_detail' => $get_class_id, 
            'feedata' => $feedata ]);
    }


    public function postPayFees_bfrreceipt(Request $request){

        if (Auth::check()) {
 
            $input = $request->all(); 

            $fee_structure_item_ids = $request->fee_structure_item_id;
            $paid_date=$request->paid_date;
            $paid_amount=$request->paid_amount;
            $payment_mode=$request->payment_mode;
            $payment_remark=$request->payment_remark;
            $student_id=$request->student_id;
            $school_id=$request->school_id;  


            $class_id = DB::table('students')->where('user_id', $student_id)->value('class_id');
            $section_id = DB::table('students')->where('user_id', $student_id)->value('section_id'); 

            $validator= Validator::make($request->all(),
            [
                'fee_structure_item_id' => 'required|array',
                'fee_structure_item_id.*' => 'required',
                'paid_date' => 'required|date',
                'paid_amount' => 'required|numeric',
                'payment_mode' => 'required',

            ],[]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            } 

            // Iterate over each fee structure item ID
                foreach ($fee_structure_item_ids as $fee_structure_item_id) {

                    $get_data = FeeStructureItem::where('id',$fee_structure_item_id)->first();
                     // echo "<pre>"; print_r($get_data);exit;
                    if (!$get_data) {
                        continue; // Skip if fee structure item is not found
                    }

                    $fee_amount = $get_data->amount;

                    $fee_structure_id = $get_data->fee_structure_id;
                    $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
                    if($fee_structure_id > 0) {
                        $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
                    } 

                    $fee_amount = FeeStructureItem::getBalance($fee_structure_item_id, $batch, $student_id, Auth::User()->id);
 
                    // Calculate the amount to be paid for this fee structure item
                    $amount_to_pay = min($fee_amount, $paid_amount);

                    // Determine the payment status
                    $payment_status = $amount_to_pay >= $fee_amount ? 'PAID' : 'PARTIAL';

                    // Create a new FeesPaymentDetail instance for each fee structure item ID
                    $post_new = new FeesPaymentDetail;

                    $post_new->fee_structure_item_id = $fee_structure_item_id;

                    $post_new->student_id = $student_id;
                    $post_new->school_id = $school_id;
                    
                    $post_new->batch = $batch;
                    $post_new->class_id = $class_id;
                    $post_new->section_id = $section_id;
                    $post_new->fee_structure_id = $fee_structure_id;

                    $post_new->paid_date = $paid_date;
                    $post_new->amount_paid = $amount_to_pay;
                    $post_new->payment_mode = $payment_mode;
                    $post_new->payment_remarks = $payment_remark;
                    $post_new->posted_by = Auth::User()->id;
                    $post_new->payment_status = $payment_status;

                    // Save the new instance
                    $post_new->save();

                    // Reduce the paid amount
                    $paid_amount -= $amount_to_pay;

                    // If paid amount is zero, stop processing further
                    if ($paid_amount <= 0) {
                        break;
                    }
                }

            return response()->json(['status'=>1,'message'=>'Payment Updated Successfully']);

        } else {
            return redirect('/admin/login');
        }
    }

    public function postPayFees(Request $request)  {
        if (Auth::check()) {
            $input = $request->all();

            $fee_structure_item_ids = $request->fee_structure_item_id;
            $paid_date = $request->paid_date;
            $paid_amount = $request->paid_amount;
            $payment_mode = $request->payment_mode;
            $payment_remark = $request->payment_remark;
            $student_id = $request->student_id;
            $school_id = $request->school_id;

            $class_id = DB::table('students')->where('user_id', $student_id)->value('class_id');
            $section_id = DB::table('students')->where('user_id', $student_id)->value('section_id');

            $validator = Validator::make($request->all(), [
                'fee_structure_item_id' => 'required|array',
                'fee_structure_item_id.*' => 'required',
                'paid_date' => 'required|date',
                'paid_amount' => 'required|numeric',
                'payment_mode' => 'required',
            ], []);

            if ($validator->fails()) {
                $msg = $validator->errors()->all();
                return response()->json([
                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year');
            $total_amount_paid = 0;

            // Group fee structure items by account_id
            $fee_structure_items_by_account = [];

            foreach ($fee_structure_item_ids as $fee_structure_item_id) {
                $fee_item_id = FeeStructureItem::where('id', $fee_structure_item_id)->value('fee_item_id');
                $category_id = FeeItems::where('id', $fee_item_id)->value('category_id');
                $account_id = FeeCategory::where('id', $category_id)->value('account_id');

                if (!isset($fee_structure_items_by_account[$account_id])) {
                    $fee_structure_items_by_account[$account_id] = [];
                }
                $fee_structure_items_by_account[$account_id][] = $fee_structure_item_id;
            }

            foreach ($fee_structure_items_by_account as $account_id => $fee_structure_item_ids) {
                // Fetch the receipt head details for the account

                $get_re_id = DB::table('accounts')->where('id', $account_id)->first();
                $receipt_head = DB::table('receipt_heads')->where('id', $get_re_id->recepit_id)->first();
                if (!$receipt_head) {
                    continue; // Skip if receipt head details are not found
                }

                // Generate the receipt number based on receipt head details
                $receipt_no = $this->generateReceiptNumber($receipt_head, $account_id);

                // Create a new receipt entry for each account_id
                $new_receipt = new FeesReceiptDetail;
                $new_receipt->school_id = $school_id;
                $new_receipt->student_id = $student_id;
                $new_receipt->batch = $batch;
                $new_receipt->receipt_no = $receipt_no;
                $new_receipt->amount = 0; // This will be updated after calculating total payment
                $new_receipt->payment_mode = $payment_mode;
                $new_receipt->receipt_date = date('Y-m-d');
                $new_receipt->posted_by = Auth::User()->id;
                $new_receipt->account_id = $account_id; // Ensure you have account_id field in the receipt table
                $new_receipt->save();

                $receipt_total_amount = 0;

                foreach ($fee_structure_item_ids as $fee_structure_item_id) {
                    $get_data = FeeStructureItem::where('id', $fee_structure_item_id)->first();
                    if (!$get_data) {
                        continue; // Skip if fee structure item is not found
                    }

                    $fee_amount = $get_data->amount;
                    $fee_structure_id = $get_data->fee_structure_id;
                    if ($fee_structure_id > 0) {
                        $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
                    }

                    $fee_amount = FeeStructureItem::getBalance($fee_structure_item_id, $batch, $student_id, Auth::User()->id);
                    if($fee_amount > 0) {
                        $amount_to_pay = min($fee_amount, $paid_amount);
                    }   else {
                        $amount_to_pay = $fee_amount;
                    }
                    $payment_status = $amount_to_pay >= $fee_amount ? 'PAID' : 'PARTIAL';

                    $ex = FeesPaymentDetail::where(['fee_structure_item_id' => $fee_structure_item_id, 
                            'fee_structure_id' => $fee_structure_id, 'amount_paid' => 0, 'school_id' => $school_id,
                            'student_id' => $student_id, 'cancel_status' => 0 ])
                            ->where('amount_to_pay', '>', 0)->first();
                    if(!empty($ex)) {
                        $post_new = FeesPaymentDetail::find($ex->id);
                    }   else {
                        $post_new = new FeesPaymentDetail;
                        $post_new->fee_structure_item_id = $fee_structure_item_id;
                        $post_new->student_id = $student_id;
                        $post_new->school_id = $school_id;
                        $post_new->batch = $batch;
                        $post_new->class_id = $class_id;
                        $post_new->section_id = $section_id;
                        $post_new->fee_structure_id = $fee_structure_id;
                    } 
                    
                    $post_new->paid_date = $paid_date;
                    $post_new->amount_paid = $amount_to_pay;
                    $post_new->payment_mode = $payment_mode;
                    $post_new->payment_remarks = $payment_remark;
                    $post_new->posted_by = Auth::User()->id;
                    $post_new->payment_status = $payment_status;
                    $post_new->receipt_id = $new_receipt->id;

                    $post_new->save();

                    $receipt_total_amount += $amount_to_pay;
                    $paid_amount -= $amount_to_pay;

                    if ($paid_amount <= 0) {
                        break;
                    }
                }

                $new_receipt->amount = $receipt_total_amount;
                $new_receipt->save();

                $total_amount_paid += $receipt_total_amount;
                
                $this->generateReceiptPdf($new_receipt->id, $student_id);
            }
            return response()->json(['status' => 1, 'message' => 'Payment Updated Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function generateReceiptPdf($receiptId, $student_id) {  
        // $receiptId = 1;  $student_id = 1707;
        $school_id = Auth::User()->id; 

        // Retrieve the receipt details from the database
        $receipt = FeesReceiptDetail::with('feepayments')->where('id', $receiptId)->first();
        //echo "<pre>"; print_r($receipt->toArray()); exit;

        $pdfcontent = view('admin.fee_receipt_pdf')->with(['receipt' => $receipt])->render();
        //echo $pdfcontent;exit;  

        // Generate the PDF from the 'fee_receipt_pdf' Blade view
        $pdf = PDF::loadView('admin.fee_receipt_pdf', ['receipt' => $receipt]);

        // Define the directory path
        /*if (!file_exists(public_path('/uploads/receipt_pdf/'.$school_id.'/'.$student_id))) {
            mkdir(public_path('/uploads/receipt_pdf/'.$school_id.'/'.$student_id), 0777, true);
        } */ 

        $directoryPath = public_path('/uploads/receipt_pdf/'.$school_id.'/'.$student_id.'/');

        // Generate a unique 14-character string for the file name
        $uniqueString = $receiptId; // uniqid('', true); // Alternatively, use uniqid('', true) for a more unique string

        // Create the file path with only the unique string as the file name
        $filePath = $directoryPath . $uniqueString . '.pdf';

        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0777, true, true);
        }

        // Save the PDF to a file
        $pdf->save($filePath);

        // Update the receipt record in the database to store the PDF file path
        DB::table('fees_receipt_details')->where('id', $receiptId)->update(['receipt_pdf' => $uniqueString . '.pdf']);
        //$receipt->receipt_pdf = $uniqueString . '.pdf';
        //$receipt->save();

        // Return a response indicating success
        //return response()->json(['status' => 'success', 'message' => 'Receipt PDF generated successfully']);
    }

    private function generateReceiptNumber($receipt_head, $account_id) {
        // Fetch the next receipt number starting from the receipt head's starting number
        $last_receipt_number = DB::table('fees_receipt_details')
            ->where('account_id', $account_id)
            ->orderBy('id', 'desc')
            ->value('receipt_no');

        $next_number = $receipt_head->starting_number;
        if ($last_receipt_number) {
            // Extract the numeric part from the last receipt number
            $last_number = (int)str_replace([$receipt_head->no_prefix, $receipt_head->no_suffix], '', $last_receipt_number);
            $next_number = $last_number + 1;
        }

        // Format the receipt number with padding, prefix, and suffix
        $formatted_number = str_pad($next_number, $receipt_head->padding_digit, '0', STR_PAD_LEFT);
        return $receipt_head->no_prefix . $formatted_number . $receipt_head->no_suffix;
    }

    public function postPayFeesConcession(Request $request){

        if (Auth::check()) {
 
            $input = $request->all(); //echo "<pre>"; print_r($input); exit;
            $feebalance_amount = $request->get('feebalance_amount', 0);
            $feeconcession_student_id = $request->get('feeconcession_student_id', 0);
            $feeconcession_item_id = $request->get('feeconcession_item_id', 0);
            $concession_amount  = $request->get('concession_amount', 0);

            if($concession_amount > $feebalance_amount) {
                return response()->json(['status' => 'FAILED', 'message' => 'Concession amount must be lesser or equal to Rs. '.$feebalance_amount]);
            }

            $validator= Validator::make($request->all(),
            [
                'feeconcession_student_id' => 'required',
                'feeconcession_item_id.*' => 'required',
                'concession_amount' => 'required|numeric', 

            ],[]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 'FAILED',
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            } 

            $get_data = FeeStructureItem::where('id',$feeconcession_item_id)->first();
             // echo "<pre>"; print_r($get_data);exit;
            if (!$get_data) {
                return response()->json([ 
                    'status' => 'FAILED',
                    'message' => "Invalid Item ",
                ]);
            }

            $fee_amount = $get_data->amount;

            $fee_structure_id = $get_data->fee_structure_id;
            $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
            if($fee_structure_id > 0) {
                $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
            }
            $class_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('class_id');
            $section_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('section_id'); 

            // Calculate the amount to be paid for this fee structure item
            $amount_to_pay = $concession_amount;

            // Determine the payment status
            $payment_status = $concession_amount >= $feebalance_amount ? 'PAID' : 'PARTIAL';

            // Create a new FeesPaymentDetail instance for each fee structure item ID
            $post_new = new FeesPaymentDetail;

            $post_new->fee_structure_item_id = $feeconcession_item_id;

            $post_new->student_id = $feeconcession_student_id;
            $post_new->school_id = Auth::User()->id;
            
            $post_new->batch = $batch;
            $post_new->class_id = $class_id;
            $post_new->section_id = $section_id;
            $post_new->fee_structure_id = $fee_structure_id;

            $post_new->paid_date = null;
            $post_new->amount_paid = 0;
            $post_new->payment_mode = 0;
            $post_new->payment_remarks = '';
            $post_new->is_concession = 1;
            $post_new->concession_amount = $concession_amount;
            $post_new->concession_date = date('Y-m-d');

            $post_new->posted_by = Auth::User()->id;
            $post_new->payment_status = '';

            // Save the new instance
            $post_new->save();

            return response()->json([  'status' => 'SUCCESS', 
                 'message' => "Concession added" ,
            ]);

        } else {
            return redirect('/admin/login');
        }
    }

    public function feeSummaryPage(Request $request)
    {
        if (Auth::check()) {
   
            $school_id = Auth::User()->id;
            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0);
            $studentdetails = Student::where('user_id', $student_id)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            if(!empty($studentdetails)) {
                $studentdetails = $studentdetails->toArray();
            }
            //echo "<pre>"; print_r($studentdetails); exit;
            return view('admin.feesummary',compact('batch','student_id', 'studentdetails'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeSummaryLists(Request $request)
    {
        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0); 
            $school_id = Auth::User()->id;

            $feesummary_qry = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->where('fees_payment_details.batch', $batch)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                'creator.name as creator_name'
            ); 

            $filtered_qry = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->where('fees_payment_details.batch', $batch)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                'creator.name as creator_name'); 

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'fees_payment_details.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            } 

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fees_payment_details.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $feesummary = $feesummary_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();

            $totalData = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->where('fees_payment_details.batch', $batch)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.id');

            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($feesummary)) {
                $feesummary = $feesummary->toArray();
                foreach ($feesummary as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    public function feeConcessionsPage(Request $request)
    {
        if (Auth::check()) {
   
            $school_id = Auth::User()->id;
            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0);
            $studentdetails = Student::where('user_id', $student_id)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            if(!empty($studentdetails)) {
                $studentdetails = $studentdetails->toArray();
            }
            //echo "<pre>"; print_r($studentdetails); exit;
            return view('admin.feeconcessions',compact('batch','student_id', 'studentdetails'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeConcessionsLists(Request $request)
    {
        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0); 
            $school_id = Auth::User()->id;

            $feesummary_qry = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->where('fees_payment_details.batch', $batch)->where('fees_payment_details.is_concession', 1)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 
                'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code'); 

            $filtered_qry = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->where('fees_payment_details.batch', $batch)->where('fees_payment_details.is_concession', 1)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 
                'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code'); 

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'fees_payment_details.status') {
                            $users_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $users_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            } 

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fees_payment_details.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $feesummary = $feesummary_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();

            $totalData = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->where('fees_payment_details.batch', $batch)->where('fees_payment_details.is_concession', 1)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.id');

            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($feesummary)) {
                $feesummary = $feesummary->toArray();
                foreach ($feesummary as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getLoadFeesConcessions(Request $request)
    {
        if (Auth::check()) {

            $school_id = Auth::User()->id;
            $batch = $request->get('batch', date('Y'));
            $studentId = $request->get('student_id', 0); 

            // Fetch student details
            $get_class_id = Student::where('user_id', $studentId)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            $class_id = $get_class_id->class_id;
            $section_id = $get_class_id->section_id; 

            FeeStructureList::$student_id = $studentId;

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

                if ($appliesToStudent) {
                    foreach ($feeStructure->feeItems as $fk => $feeItem) { 

                        $fee_item_id = $feeItem->id;
                        $fee_amount = $feeItem->amount;
                        $fee_status_flag = 0;
                        $total_paid = 0; $total_concession = 0;
                        $balance_amount = $fee_amount;

                        if (isset($paid_records_map[$fee_item_id])) {
                            $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
                            $balance_amount = max($fee_amount - $total_paid, 0);
                            $total_concession = $paid_records_map[$fee_item_id]['total_concession']; 

                            $balance_amount = $balance_amount - $total_concession;

                            if ($balance_amount == 0) {
                                $fee_status_flag = 1; // Fully paid
                                unset($feeStructure->feeItems[$fk]);
                            } elseif ($balance_amount <= $fee_amount) {
                                $fee_status_flag = 2; // Partially paid
                            }
                        }

                        $feeItem->payment_status_flag = $fee_status_flag;
                        $feeItem->balance_amount = $balance_amount;
                        $feeItem->paid_amount = $total_paid;
                        $feeItem->concession_amount = $total_concession;
                    }
                    $studentFeeStructures[] = $feeStructure;
                }
            }   

            //echo "<pre>"; print_r($studentFeeStructures); exit;

            $data = view('admin.loadfeeconcessions',compact('batch','studentId', 'studentFeeStructures'))->render();

            return response()->json([  'status' => 'SUCCESS',   'message' => "Concession List", 'data' => $data ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function postSaveFeesConcessions(Request $request) {
        if (Auth::check()) {
            $input = $request->all();  
            $concessions = $request->get('concessions', []);
            $concession_amount = $request->get('concession_amount', []);
            $concession_remarks = $request->get('concession_remarks', []);
            $feeconcession_student_id = $request->get('feeconcession_student_id', 0);

            if(count($concessions) > 0) {
                foreach($concessions as $feeconcession_item_id=>$itemval) {
                    $get_data = FeeStructureItem::where('id',$feeconcession_item_id)->first(); 
                    if (!$get_data) {
                        return response()->json([ 
                            'status' => 'FAILED',
                            'message' => "Invalid Item ",
                        ]);
                    }

                    $fee_amount = $get_data->amount;

                    $fee_structure_id = $get_data->fee_structure_id;
                    $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
                    if($fee_structure_id > 0) {
                        $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
                    }
                    $class_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('class_id');
                    $section_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('section_id'); 

                    // Calculate the amount to be paid for this fee structure item
                    $amount_to_pay = $concession_amount[$feeconcession_item_id];

                    // Determine the payment status
                    $payment_status = '';

                    // Create a new FeesPaymentDetail instance for each fee structure item ID
                    $post_new = new FeesPaymentDetail;

                    $post_new->fee_structure_item_id = $feeconcession_item_id;

                    $post_new->student_id = $feeconcession_student_id;
                    $post_new->school_id = Auth::User()->id;
                    
                    $post_new->batch = $batch;
                    $post_new->class_id = $class_id;
                    $post_new->section_id = $section_id;
                    $post_new->fee_structure_id = $fee_structure_id;

                    $post_new->paid_date = null;
                    $post_new->amount_paid = 0;
                    $post_new->payment_mode = 0;
                    $post_new->payment_remarks = '';
                    $post_new->is_concession = 1;
                    $post_new->concession_amount = $concession_amount[$feeconcession_item_id];
                    $post_new->concession_date = date('Y-m-d');
                    $post_new->concession_remarks = $concession_remarks[$feeconcession_item_id];
                    $post_new->posted_by = Auth::User()->id;
                    $post_new->payment_status = '';

                    // Save the new instance
                    $post_new->save();
                }


                return response()->json([  'status' => 'SUCCESS',  'message' => "Concession added"   ]);
            // echo "<pre>"; print_r($concessions);print_r($concession_amount);print_r($concession_remarks); exit;
            } else {
                return response()->json([  'status' => 'FAILED',   'message' => "Please check the boxes need  to apply the Concessions" ]);
            }
        } else {
            return redirect('/admin/login');
        }    
    }

    public function getLoadAdditionalFeesitems(Request $request)
    {
        if (Auth::check()) {

            $school_id = Auth::User()->id;
            $batch = $request->get('batch', date('Y'));
            $studentId = $request->get('student_id', 0); 
            $fee_type = $request->get('fee_type', 0); 

            // Fetch student details
            $get_class_id = Student::where('user_id', $studentId)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            $class_id = $get_class_id->class_id;
            $section_id = $get_class_id->section_id; 

            FeeStructureList::$student_id = $studentId;

            // Retrieve fee structures
            $feeStructures = FeeStructureList::with(['feeItems.feeItem'])
                ->where('school_id', $school_id)->where('fee_type', $fee_type)
                ->where('batch', $batch)
                ->get();

            // Fetch paid records for the student
            $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)->where('cancel_status', 0)->get();

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

                if ($appliesToStudent) {
                    foreach ($feeStructure->feeItems as $fk => $feeItem) { 

                        $fee_item_id = $feeItem->id;
                        $fee_amount = $feeItem->amount;
                        $fee_status_flag = 0;
                        $total_paid = 0; $total_concession = 0;
                        $balance_amount = $fee_amount;

                        if (isset($paid_records_map[$fee_item_id])) {
                            $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
                            $balance_amount = max($fee_amount - $total_paid, 0);
                            $total_concession = $paid_records_map[$fee_item_id]['total_concession']; 

                            $balance_amount = $balance_amount - $total_concession;

                            if ($balance_amount == 0) {
                                $fee_status_flag = 1; // Fully paid
                                unset($feeStructure->feeItems[$fk]);
                            } elseif ($balance_amount <= $fee_amount) {
                                $fee_status_flag = 2; // Partially paid
                            }
                        }

                        $feeItem->payment_status_flag = $fee_status_flag;
                        $feeItem->balance_amount = $balance_amount;
                        $feeItem->paid_amount = $total_paid;
                        $feeItem->concession_amount = $total_concession;
                    }
                    $studentFeeStructures[] = $feeStructure;
                }
            }   

            //echo "<pre>"; print_r($studentFeeStructures); exit;

            $data = view('admin.loadfeeadditionals',compact('batch','studentId', 'studentFeeStructures', 'fee_type'))->render();

            return response()->json([  'status' => 'SUCCESS',   'message' => "Concession List", 'data' => $data ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function postSaveFeesAdditions(Request $request) {
        if (Auth::check()) {
            $input = $request->all();   //echo "<pre>"; print_r($input); exit;
            $additional = $request->get('additional', []);
            $concession_amount = $request->get('concession_amount', []);
            $fee_type = $request->get('fee_type', '');
            $feeconcession_student_id = $request->get('feeadd_student_id', 0);

            if(count($additional) > 0) {
                foreach($additional as $feeconcession_item_id=>$itemval) {
                    $get_data = FeeStructureItem::where('id',$feeconcession_item_id)->first(); 
                    if (!$get_data) {
                        return response()->json([ 
                            'status' => 'FAILED',
                            'message' => "Invalid Item ",
                        ]);
                    }

                    $fee_amount = $get_data->amount;

                    $fee_structure_id = $get_data->fee_structure_id;
                    $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
                    if($fee_structure_id > 0) {
                        $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
                    }
                    $class_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('class_id');
                    $section_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('section_id'); 

                    // Calculate the amount to be paid for this fee structure item
                    $amount_to_pay = $concession_amount[$feeconcession_item_id];

                    // Determine the payment status
                    $payment_status = '';

                    // Create a new FeesPaymentDetail instance for each fee structure item ID
                    $post_new = new FeesPaymentDetail;

                    $post_new->fee_structure_item_id = $feeconcession_item_id;

                    $post_new->student_id = $feeconcession_student_id;
                    $post_new->school_id = Auth::User()->id;
                    
                    $post_new->batch = $batch;
                    $post_new->class_id = $class_id;
                    $post_new->section_id = $section_id;
                    $post_new->fee_structure_id = $fee_structure_id;

                    $post_new->paid_date = null;
                    $post_new->amount_to_pay = $concession_amount[$feeconcession_item_id];
                    $post_new->amount_paid = 0;
                    $post_new->payment_mode = 0;
                    $post_new->payment_remarks = '';
                    $post_new->is_concession = 0;
                    $post_new->concession_amount = 0;
                    $post_new->concession_date = null;
                    $post_new->concession_remarks = null;
                    $post_new->posted_by = Auth::User()->id;
                    $post_new->payment_status = '';

                    // Save the new instance
                    $post_new->save();
                }


                return response()->json([  'status' => 'SUCCESS',  'message' => "Additional fees added"   ]);
            // echo "<pre>"; print_r($concessions);print_r($concession_amount);print_r($concession_remarks); exit;
            } else {
                return response()->json([  'status' => 'FAILED',   'message' => "Please check the boxes need  to apply the Additional Fees" ]);
            }
        } else {
            return redirect('/admin/login');
        }    
    } 

    public function postDeleteFeesAdditions(Request $request) {
        if (Auth::check()) {
            $input = $request->all();   
            $student_id = $input['student_id'];
            $item_id = $input['itemid'];
            $fee_structure_id = $input['fee_structure_id'];

            $get_data = FeeStructureItem::where('id',$item_id)->first(); 
            if (!$get_data) {
                return response()->json([ 
                    'status' => 'FAILED',
                    'message' => "Invalid Item ",
                ]);
            }
            $fee_structure_id = $get_data->fee_structure_id;
            $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
            if($fee_structure_id > 0) {
                $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
            }
            $class_id = DB::table('students')->where('user_id', $student_id)->value('class_id');
            $section_id = DB::table('students')->where('user_id', $student_id)->value('section_id'); 

            $ex = DB::table('fees_payment_details')->where(['student_id'=>$student_id, 'school_id'=>Auth::User()->id, 
                    'batch'=>$batch, 'class_id'=>$class_id, 'section_id'=>$section_id, 'fee_structure_id'=>$fee_structure_id, 
                    'fee_structure_item_id'=>$item_id])->first();
            if(!empty($ex)) {
                DB::table('fees_payment_details')->where('id', $ex->id)->update(['cancel_status'=>1, 
                        'cancelled_by'=>Auth::User()->id, 'cancelled_date'=>date('Y-m-d H:i:s')]);
            }

            return response()->json([  'status' => 'SUCCESS',  'message' => "Deleted Successfully" ]); 
        } else {
            return redirect('/admin/login');
        } 
    }

    public function postSaveFeesAdditions_bfrreceipt(Request $request) {
        if (Auth::check()) {
            $input = $request->all();   //echo "<pre>"; print_r($input); exit;
            $additional = $request->get('additional', []);
            $concession_amount = $request->get('concession_amount', []);
            $fee_type = $request->get('fee_type', '');
            $feeconcession_student_id = $request->get('feeadd_student_id', 0);

            if(count($additional) > 0) {
                foreach($additional as $feeconcession_item_id=>$itemval) {
                    $get_data = FeeStructureItem::where('id',$feeconcession_item_id)->first(); 
                    if (!$get_data) {
                        return response()->json([ 
                            'status' => 'FAILED',
                            'message' => "Invalid Item ",
                        ]);
                    }

                    $fee_amount = $get_data->amount;

                    $fee_structure_id = $get_data->fee_structure_id;
                    $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
                    if($fee_structure_id > 0) {
                        $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
                    }
                    $class_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('class_id');
                    $section_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('section_id'); 

                    // Calculate the amount to be paid for this fee structure item
                    $amount_to_pay = $concession_amount[$feeconcession_item_id];

                    // Determine the payment status
                    $payment_status = '';

                    // Create a new FeesPaymentDetail instance for each fee structure item ID
                    $post_new = new FeesPaymentDetail;

                    $post_new->fee_structure_item_id = $feeconcession_item_id;

                    $post_new->student_id = $feeconcession_student_id;
                    $post_new->school_id = Auth::User()->id;
                    
                    $post_new->batch = $batch;
                    $post_new->class_id = $class_id;
                    $post_new->section_id = $section_id;
                    $post_new->fee_structure_id = $fee_structure_id;

                    $post_new->paid_date = date('Y-m-d');
                    $post_new->amount_paid = $concession_amount[$feeconcession_item_id];
                    $post_new->payment_mode = 1;
                    $post_new->payment_remarks = '';
                    $post_new->is_concession = 0;
                    $post_new->concession_amount = 0;
                    $post_new->concession_date = null;
                    $post_new->concession_remarks = null;
                    $post_new->posted_by = Auth::User()->id;
                    $post_new->payment_status = 'PAID';

                    // Save the new instance
                    $post_new->save();
                }


                return response()->json([  'status' => 'SUCCESS',  'message' => "Additional fees added"   ]);
            // echo "<pre>"; print_r($concessions);print_r($concession_amount);print_r($concession_remarks); exit;
            } else {
                return response()->json([  'status' => 'FAILED',   'message' => "Please check the boxes need  to apply the Additional Fees" ]);
            }
        } else {
            return redirect('/admin/login');
        }    
    }

    public function postSaveFeesAdditions_receipt(Request $request) {
        if (Auth::check()) {
            $input = $request->all();   //echo "<pre>"; print_r($input); exit;
            $additional = $request->get('additional', []);
            $concession_amount = $request->get('concession_amount', []);
            $fee_type = $request->get('fee_type', '');
            $feeconcession_student_id = $request->get('feeadd_student_id', 0);

            if(count($additional) > 0) {
                $school_id = Auth::User()->id;
                $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year');
                $total_amount_paid = 0;

                // Group fee structure items by account_id
                $fee_structure_items_by_account = [];

                foreach ($additional as $fee_structure_item_id => $iv) {
                    $fee_item_id = FeeStructureItem::where('id', $fee_structure_item_id)->value('fee_item_id');
                    $category_id = FeeItems::where('id', $fee_item_id)->value('category_id');
                    $account_id = FeeCategory::where('id', $category_id)->value('account_id');

                    if (!isset($fee_structure_items_by_account[$account_id])) {
                        $fee_structure_items_by_account[$account_id] = [];
                    }
                    $fee_structure_items_by_account[$account_id][] = $fee_structure_item_id;
                }

                foreach ($fee_structure_items_by_account as $account_id => $fee_structure_item_ids) {
                    // Fetch the receipt head details for the account

                    $get_re_id = DB::table('accounts')->where('id', $account_id)->first();
                    $receipt_head = DB::table('receipt_heads')->where('id', $get_re_id->recepit_id)->first();
                    if (!$receipt_head) {
                        continue; // Skip if receipt head details are not found
                    }

                    // Generate the receipt number based on receipt head details
                    $receipt_no = $this->generateReceiptNumber($receipt_head, $account_id);

                    // Create a new receipt entry for each account_id
                    $new_receipt = new FeesReceiptDetail;
                    $new_receipt->school_id = $school_id;
                    $new_receipt->student_id = $feeconcession_student_id;
                    $new_receipt->batch = $batch;
                    $new_receipt->receipt_no = $receipt_no;
                    $new_receipt->amount = 0; // This will be updated after calculating total payment
                    $new_receipt->payment_mode = 1;
                    $new_receipt->receipt_date = date('Y-m-d');
                    $new_receipt->posted_by = Auth::User()->id;
                    $new_receipt->account_id = $account_id; // Ensure you have account_id field in the receipt table
                    $new_receipt->save();

                    $receipt_total_amount = 0; 

                
                    foreach($additional as $feeconcession_item_id=>$itemval) {
                        $get_data = FeeStructureItem::where('id',$feeconcession_item_id)->first(); 
                        if (!$get_data) {
                            return response()->json([ 
                                'status' => 'FAILED',
                                'message' => "Invalid Item ",
                            ]);
                        }

                        $fee_amount = $get_data->amount;

                        $fee_structure_id = $get_data->fee_structure_id;
                        $batch = DB::table('admin_settings')->where('school_id', Auth::User()->id)->value('acadamic_year'); 
                        if($fee_structure_id > 0) {
                            $batch = DB::table('fee_structure_lists')->where('id', $fee_structure_id)->value('batch');
                        }
                        $class_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('class_id');
                        $section_id = DB::table('students')->where('user_id', $feeconcession_student_id)->value('section_id'); 

                        // Calculate the amount to be paid for this fee structure item
                        $amount_to_pay = $concession_amount[$feeconcession_item_id];

                        // Determine the payment status
                        $payment_status = '';

                        // Create a new FeesPaymentDetail instance for each fee structure item ID
                        $post_new = new FeesPaymentDetail;

                        $post_new->fee_structure_item_id = $feeconcession_item_id;

                        $post_new->student_id = $feeconcession_student_id;
                        $post_new->school_id = Auth::User()->id;
                        
                        $post_new->batch = $batch;
                        $post_new->class_id = $class_id;
                        $post_new->section_id = $section_id;
                        $post_new->fee_structure_id = $fee_structure_id;

                        $post_new->paid_date = date('Y-m-d');
                        $post_new->amount_paid = $concession_amount[$feeconcession_item_id];
                        $post_new->payment_mode = 1;
                        $post_new->payment_remarks = '';
                        $post_new->is_concession = 0;
                        $post_new->concession_amount = 0;
                        $post_new->concession_date = null;
                        $post_new->concession_remarks = null;
                        $post_new->posted_by = Auth::User()->id;
                        $post_new->payment_status = 'PAID';
                        $post_new->receipt_id = $new_receipt->id;
                        // Save the new instance
                        $post_new->save();

                        $receipt_total_amount += $amount_to_pay; 
                    }

                    $new_receipt->amount = $receipt_total_amount;
                    $new_receipt->save();

                    $total_amount_paid += $receipt_total_amount;
                    
                    $this->generateReceiptPdf($new_receipt->id, $feeconcession_student_id);

                }


                return response()->json([  'status' => 'SUCCESS',  'message' => "Additional fees added"   ]);
                // echo "<pre>"; print_r($concessions);print_r($concession_amount);print_r($concession_remarks); exit;
            } else {
                return response()->json([  'status' => 'FAILED',   'message' => "Please check the boxes need  to apply the Additional Fees" ]);
            }
        } else {
            return redirect('/admin/login');
        }    
    }

    public function repfeeSummaryPage()
    {
        if (Auth::check()) {
   
            $school_id = Auth::User()->id;
            
            $get_classes = Classes::where('status', 'ACTIVE')->where('school_id', Auth::User()->id)->orderby('position', 'Asc')->get(); 

            $get_student=Student::where('status','ACTIVE')->where('school_id',$school_id)
                            ->where('delete_status','0')
                            ->get(); 

            $get_batches = $this->getBatches();  

            return view('admin.fee_summary',compact('get_classes','get_student', 'get_batches'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function getrepFeeSummaryLists(Request $request)  {

        $school_id = Auth::User()->id;

        $student_id = $request->get('student_id');
       
        $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id)
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 
                'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code')->get();
          
        return Datatables::of($fee_summary_list)->make(true);
      
    }

    public function getScholarFeeFummaryExcel(Request $request)
    {

        if (Auth::check()) {
           $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();

            $batch = $request->get('batch', '');  
            $class_id = $request->get('class_id', '');
            $section_id = $request->get('section_id', '');
            $student_id = $request->get('student_id', '');
            $fee_category = $request->get('fee_category', '');
            $fee_item_id = $request->get('fee_item_id', '');
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $school_id = Auth::User()->id;    

            $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
            ->where('fees_payment_details.school_id', $school_id)->where('fees_payment_details.student_id', $student_id) 
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                'creator.name as creator_name');
           
            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'fees_payment_details.status') {
                            $fee_summary_list->where($value['name'], 'like', $value['search']['value'] . '%'); 
                        } else {
                            $fee_summary_list->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                        }
                    }
                }
            }                 

            if($batch > 0){
                $fee_summary_list->where('fees_payment_details.batch',$batch); 
            }
            if($class_id > 0){
                $fee_summary_list->where('fees_payment_details.class_id',$class_id); 
            }
            if($section_id > 0){
                $fee_summary_list->where('fees_payment_details.section_id',$section_id); 
            }
            if($student_id > 0){
                $fee_summary_list->where('fees_payment_details.student_id',$student_id); 
            }
            if($fee_category > 0){
                $fee_summary_list->where('fee_categories.id',$fee_category); 
            }
            if($fee_item_id > 0){
                $fee_summary_list->where('fees_payment_details.fee_structure_item_id',$fee_item_id); 
            }
            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $fee_summary_list->where('fees_payment_details.created_at', '>=', $mindate); 
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                $fee_summary_list->where('fees_payment_details.created_at', '<=', $maxdate); 
            }


            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fees_payment_details.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $fee_collection = $fee_summary_list->orderBy($orderby, $dir)->get();
         
            $fee_collection_excel = [];

            if (! empty($fee_collection)) {
                $i = 1;
                foreach ($fee_collection as $rev) {
                    $fee_collection_excel[] = [
                         "Batch" => $rev->batch,
                         "Class" => $rev->class_name,
                         "Section" => $rev->section_name,
                         "Scholar" => $rev->scholar_name,
                         "Admission Number" => $rev->admission_no,
                         "Category" => $rev->name,
                         "Item" => $rev->item_name,
                         "Amount" => $rev->amount,
                         "Due Date" => $rev->due_date,
                         "Paid Amount" => $rev->amount_paid,
                         "Paid Date" => $rev->paid_date,
                         "Payment Remarks" => $rev->payment_remarks,
                         "Concession Amount" => $rev->concession_amount,
                         "Concession Date" => $rev->concession_date,
                         "Collected By" => $rev->creator_name,
                         "Collected Date" => $rev->created_at,
                    ];

                    $i++;
                }
            }

   
             header("Content-Type: text/plain");
             $flag = false;
             foreach ($fee_collection_excel as $row) {
                 if (! $flag) {
                     // display field/column names as first row
                     echo implode("\t", array_keys($row)) . "\r\n";
                     $flag = true;
                 }
                 echo implode("\t", array_values($row)) . "\r\n";
             }
             exit();

        } else {
            return redirect('/admin/login');
        }
    }


    // Fees Collection Report 

    public function viewCollectionFeesReport()
    {
        if (Auth::check()) {

            $get_batches = $this->getBatches(); 

            $get_classes = Classes::where('status', 'ACTIVE')->where('school_id', Auth::User()->id)->orderby('position', 'Asc')->get(); 

            $get_fee_category = FeeCategory::where('status','ACTIVE')->where('school_id', Auth::User()->id)->orderBy('position','ASC')->get();
           
            return view('admin.fee_report_collection')->with(['get_batches' => $get_batches, 'get_classes'=>$get_classes, 
                'get_fee_category' => $get_fee_category]);

        } else {
            return redirect('/admin/login');
        }
    }

    public function getCollectionFeesReport(Request $request)
    {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();

            $batch = $request->get('batch', '');  
            $class_id = $request->get('class_id', '');
            $section_id = $request->get('section_id', '');
            $student_id = $request->get('student_id', '');
            $fee_category = $request->get('fee_category', '');
            $fee_item_id = $request->get('fee_item_id', '');
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $school_id = Auth::User()->id;    

            $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
            ->where('fees_payment_details.school_id', $school_id)
            ->where('fees_payment_details.is_concession', 0) 
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                'creator.name as creator_name');

            $filtered_qry = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
            ->where('fees_payment_details.school_id', $school_id)
            ->where('fees_payment_details.is_concession', 0) 
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                'creator.name as creator_name');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'fees_payment_details.status') {
                            $fee_summary_list->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $fee_summary_list->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filtered_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }                 

            if($batch > 0){
                $fee_summary_list->where('fees_payment_details.batch',$batch);
                $filtered_qry->where('fees_payment_details.batch',$batch);
            }
            if($class_id > 0){
                $fee_summary_list->where('fees_payment_details.class_id',$class_id);
                $filtered_qry->where('fees_payment_details.class_id',$class_id);
            }
            if($section_id > 0){
                $fee_summary_list->where('fees_payment_details.section_id',$section_id);
                $filtered_qry->where('fees_payment_details.section_id',$section_id);
            }
            if($student_id > 0){
                $fee_summary_list->where('fees_payment_details.student_id',$student_id);
                $filtered_qry->where('fees_payment_details.student_id',$student_id);
            }
            if($fee_category > 0){
                $fee_summary_list->where('fee_categories.id',$fee_category);
                $filtered_qry->where('fee_categories.id',$fee_category);
            }
            if($fee_item_id > 0){
                $fee_summary_list->where('fees_payment_details.fee_structure_item_id',$fee_item_id);
                $filtered_qry->where('fees_payment_details.fee_structure_item_id',$fee_item_id);
            }
            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $fee_summary_list->where('fees_payment_details.created_at', '>=', $mindate);
                $filtered_qry->where('fees_payment_details.created_at', '>=', $mindate);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                $fee_summary_list->where('fees_payment_details.created_at', '<=', $maxdate);
                $filtered_qry->where('fees_payment_details.created_at', '<=', $maxdate);
            }


            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fees_payment_details.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $fee_collection = $fee_summary_list->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();

            $totalData = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->where('fees_payment_details.school_id', $school_id)
            ->where('fees_payment_details.is_concession', 0) 
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.id'); 

            $totalData = $totalData->get();

            if (!empty($totalData)) {
                $totalData = count($totalData);
            }
            $totalfiltered = $totalData;
            $filtered = $filtered_qry->get()->toArray();
            if (!empty($filtered)) {
                $totalfiltered = count($filtered);
            }


            $data = [];
            if (!empty($fee_collection)) {
                $fee_collection = $fee_collection->toArray();
                foreach ($fee_collection as $post) {
                    $nestedData = [];
                    foreach ($post as $k => $v) {
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "data" => $data,
                "recordsFiltered" => intval($totalfiltered),
            );

            echo json_encode($json_data);
        } else {
            return redirect('/admin/login');
        }

    }

    public function getCollectionFeesReportExcel(Request $request)
    {

        if (Auth::check()) {
           $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();

            $batch = $request->get('batch', '');  
            $class_id = $request->get('class_id', '');
            $section_id = $request->get('section_id', '');
            $student_id = $request->get('student_id', '');
            $fee_category = $request->get('fee_category', '');
            $fee_item_id = $request->get('fee_item_id', '');
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $school_id = Auth::User()->id;    

            $fee_summary_list = FeesPaymentDetail::leftjoin('fee_structure_items', 'fee_structure_items.id', 'fees_payment_details.fee_structure_item_id')
            ->leftjoin('fee_structure_lists', 'fee_structure_lists.id', 'fee_structure_items.fee_structure_id')
            ->leftjoin('fee_categories', 'fee_categories.id', 'fee_structure_lists.fee_category_id')
            ->leftjoin('fee_items', 'fee_items.id', 'fee_structure_items.fee_item_id')
            ->leftjoin('users', 'users.id', 'fees_payment_details.student_id')
            ->leftjoin('users as creator', 'creator.id', 'fees_payment_details.posted_by')
            ->leftjoin('classes', 'classes.id', 'fees_payment_details.class_id')
            ->leftjoin('sections', 'sections.id', 'fees_payment_details.section_id')
            ->where('fees_payment_details.school_id', $school_id) 
            ->orderby('fees_payment_details.id','asc')
            ->select('fees_payment_details.*', 'fee_structure_items.fee_item_id', 'fee_structure_items.amount', 
                'fee_structure_items.due_date', 'fee_structure_lists.batch', 'fee_structure_lists.fee_category_id', 
                'fee_structure_lists.fee_type', 'fee_categories.name', 'fee_items.item_name', 'fee_items.item_code',
                'users.name as scholar_name', 'users.admission_no', 'classes.class_name', 'sections.section_name',
                'creator.name as creator_name');
           
            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'fees_payment_details.status') {
                            $fee_summary_list->where($value['name'], 'like', $value['search']['value'] . '%'); 
                        } else {
                            $fee_summary_list->where($value['name'], 'like', '%' . $value['search']['value'] . '%'); 
                        }
                    }
                }
            }                 

            if($batch > 0){
                $fee_summary_list->where('fees_payment_details.batch',$batch); 
            }
            if($class_id > 0){
                $fee_summary_list->where('fees_payment_details.class_id',$class_id); 
            }
            if($section_id > 0){
                $fee_summary_list->where('fees_payment_details.section_id',$section_id); 
            }
            if($student_id > 0){
                $fee_summary_list->where('fees_payment_details.student_id',$student_id); 
            }
            if($fee_category > 0){
                $fee_summary_list->where('fee_categories.id',$fee_category); 
            }
            if($fee_item_id > 0){
                $fee_summary_list->where('fees_payment_details.fee_structure_item_id',$fee_item_id); 
            }
            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $fee_summary_list->where('fees_payment_details.created_at', '>=', $mindate); 
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '. $maxdate));
                $fee_summary_list->where('fees_payment_details.created_at', '<=', $maxdate); 
            }


            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fees_payment_details.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $fee_collection = $fee_summary_list->orderBy($orderby, $dir)->get();
         
            $fee_collection_excel = [];

            if (! empty($fee_collection)) {
                $i = 1;
                foreach ($fee_collection as $rev) {
                    $fee_collection_excel[] = [
                         "Batch" => $rev->batch,
                         "Class" => $rev->class_name,
                         "Section" => $rev->section_name,
                         "Scholar" => $rev->scholar_name,
                         "Admission Number" => $rev->admission_no,
                         "Category" => $rev->name,
                         "Item" => $rev->item_name,
                         "Amount" => $rev->amount,
                         "Due Date" => $rev->due_date,
                         "Paid Amount" => $rev->amount_paid,
                         "Paid Date" => $rev->paid_date,
                         "Payment Remarks" => $rev->payment_remarks,
                         "Collected By" => $rev->creator_name,
                         "Collected Date" => $rev->created_at,
                    ];

                    $i++;
                }
            }

   
             header("Content-Type: text/plain");
             $flag = false;
             foreach ($fee_collection_excel as $row) {
                 if (! $flag) {
                     // display field/column names as first row
                     echo implode("\t", array_keys($row)) . "\r\n";
                     $flag = true;
                 }
                 echo implode("\t", array_values($row)) . "\r\n";
             }
             exit();

        } else {
            return redirect('/admin/login');
        }
    }


    //view Contacts List 

    public function viewContactsList()
    {
        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $contactsfor = ContactsFor::where('status','=','ACTIVE')->where('school_id', $school_id)->get();
            return view('admin.contacts_list')->with('contactsfor',$contactsfor);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getContactsList(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $status = $request->get('status','');
           if($status != ''){
            $clist = ContactsList::where('school_contacts_list.status','=',$status)
                ->where('school_contacts_list.school_id', $school_id); //->get();
           }else{
            $clist = ContactsList::where('school_contacts_list.school_id', $school_id); //->get();
           } 
           $clist = $clist->leftjoin('contacts_for', 'contacts_for.id', 'school_contacts_list.contact_for')
            ->select('school_contacts_list.*', 'contacts_for.name as contact_for')->get();

            return Datatables::of($clist)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postContactsList(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $contact_for = $request->contact_for; 
            $contact_name = $request->contact_name;
            $contact_mobile = $request->contact_mobile; 
            $contact_email = $request->contact_email;
            $contact_info = $request->contact_info;  
            $status = $request->status; 
            $school_id = Auth::User()->id;

            $validator = Validator::make($request->all(), [
                'contact_for' => 'required',
                'contact_name' => 'required',
                'contact_mobile' => 'required',
                'status' => 'required',
            ]);

            /*if ($id > 0) {
                $exists = DB::table('categories')->where('name', $name)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('categories')->where('name', $name)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Category Name Already Exists.",
                ]);
            }*/


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $cat = ContactsList::find($id);
                $cat->updated_at = date('Y-m-d H:i:s');
                $cat->updated_by = $school_id; 
            } else {
                $cat = new ContactsList;
                $cat->created_at = date('Y-m-d H:i:s');
                $cat->created_by = $school_id; 
            }

            $cat->school_id = $school_id; 
            $cat->contact_for = $contact_for; 
            $cat->contact_name = $contact_name;
            $cat->contact_mobile = $contact_mobile; 
            $cat->contact_email = $contact_email; 
            $cat->contact_info = $contact_info;
            $cat->status = $status; 

            $cat->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Contacts Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editContactsList(Request $request)
    {

        if (Auth::check()) {
            $cat = ContactsList::where('id', $request->code)->get();

            if ($cat->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $cat[0], 'message' => 'Contacts Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Contacts Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    //view Contacts For 

    public function viewContactsFor()
    {
        if (Auth::check()) {

            return view('admin.contacts_for');
        } else {
            return redirect('/admin/login');
        }
    }

    public function getContactsFor(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $status = $request->get('status',0);
           if($status != ''){
            $clist = ContactsFor::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $clist = ContactsFor::where('school_id', $school_id)->get();
           } 

            return Datatables::of($clist)->make(true);
        } else {
            return redirect('/admin/login');
        }

    }

    public function postContactsFor(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name;  
            $status = $request->status; 
            $position = $request->position; 
            $school_id = Auth::User()->id;

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'position' => 'required', 
                'status' => 'required',
            ]);

            if ($id > 0) {
                $exists = DB::table('contacts_for')->where('name', $name)->where('school_id', $school_id)->whereNotIn('id', [$id])->first();
            } else {
                $exists = DB::table('contacts_for')->where('name', $name)->where('school_id', $school_id)->first();
            }

            if (!empty($exists)) {
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Contacts For Already Exists.",
                ]);
            } 


            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $cat = ContactsFor::find($id);
                $cat->updated_at = date('Y-m-d H:i:s'); 
            } else {
                $cat = new ContactsFor;
                $cat->created_at = date('Y-m-d H:i:s'); 
            }

            $cat->school_id = $school_id; 
            $cat->name = $name;
            $cat->position = $position;  
            $cat->status = $status; 

            $cat->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Contacts For Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editContactsFor(Request $request)
    {

        if (Auth::check()) {
            $cat = ContactsFor::where('id', $request->code)->get();

            if ($cat->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $cat[0], 'message' => 'Contacts Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Contacts Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function feeReceiptsPage(Request $request) {
        if (Auth::check()) {

            $school_id = Auth::User()->id;
            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0);
            $studentdetails = Student::where('user_id', $student_id)
                ->select('id', 'school_id', 'user_id', 'class_id', 'section_id', 'admission_no')
                ->first();
            if(!empty($studentdetails)) {
                $studentdetails = $studentdetails->toArray();
            }
            $cancel_reason=FeeCancelReason::where('status','ACTIVE')->orderBy('position','ASC')->get();
            //echo "<pre>"; print_r($studentdetails); exit;
            return view('admin.fee_receipts',compact('batch','student_id', 'studentdetails','cancel_reason'));
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeeReceiptLists(Request $request)  {
        if (Auth::check()) {

            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0);
            $school_id = Auth::User()->id;


            $fee_receipts = FeesReceiptDetail::where('school_id', $school_id)
            ->where('batch', $batch)->where('cancel_status','0')
            ->where('student_id', $student_id)
            ->select('*', \DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as formatted_created_at"))
            ->get();

        return Datatables::of($fee_receipts)->make(true);

        } else {

            return redirect('/admin/login');
        }
    }

    /*public function generateReceiptPDF() {
        $student_id = 1574;
        $school_id = Auth::User()->id;

        $pdfcontent = view('admin.receipt_pdf')->render();
        //echo $pdfcontent;//exit;

        if (!file_exists(public_path('/uploads/receipt_pdf/'.$school_id.'/'.$student_id))) {
            mkdir(public_path('/uploads/receipt_pdf/'.$school_id.'/'.$student_id), 0777, true);
        }  
        
        $filelocation = public_path('/uploads/receipt_pdf/' . $school_id . '/' .$student_id. '/' . '1.pdf');

        $pdf = PDF::loadHTML($pdfcontent)->setPaper('a3', 'portrait')->save($filelocation);


    }*/

    public function CancelFeeReceiptData(Request $request)
     {
         if (Auth::check()) {
             $get_data = FeesReceiptDetail::where('id', $request->code)->get();
             if ($get_data->isNotEmpty()) {
                 return response()->json(['status' => 1, 'data' => $get_data[0], 'message' => 'Fee Receipt Detail']);
             } else {
                 return response()->json(['status' => 0, 'data' => [], 'message' => 'No Details Found']);
             }
         } else {
             return redirect('/admin/login');
         }
     }


     public function postCancelFeeReceipt(Request $request){

        if (Auth::check()) {

       // Auth::User()->id;
     //  date('Y-m-d H:i:s', strtotime($request->enq_date));

       $receipt_id=$request->id;
       $cancel_date=$request->cancel_date;
       $cancel_type=$request->cancel_type;
       $remarks=$request->remarks;
       $cancel_reason=$request->cancel_reason;

        $validator= Validator::make($request->all(),
            [
                'cancel_date' => 'required',
                'cancel_type' => 'required',
                'remarks' => 'required',
                'cancel_reason' => 'required'
            ],[]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => 0,
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            $post_cancel_receipt= FeesReceiptDetail::find($receipt_id);
            $post_cancel_receipt->cancel_status='1';
            $post_cancel_receipt->canceled_by=Auth::User()->id;
            $post_cancel_receipt->cancel_type=$cancel_type;
            $post_cancel_receipt->cancel_date=$cancel_date;
            $post_cancel_receipt->cancel_remark=$remarks;
            $post_cancel_receipt->cancel_reason=$cancel_reason;
            $post_cancel_receipt->save();


            if ($post_cancel_receipt) {

                 // Update the cancel_status for related FeesPaymentDetail records
                FeesPaymentDetail::where('receipt_id', $receipt_id)
                ->update(['cancel_status' => '1']);
            }

            return response()->json(['status'=>1,'message'=>'Fee Receipt Canceled Successfully']);

        } else {
            return redirect('/admin/login');
        }
    }

    public function getCancelFeeReceiptLists(Request $request)
    {
        if (Auth::check()) {

            $batch = $request->get('batch', date('Y'));
            $student_id = $request->get('student_id', 0);
            $school_id = Auth::User()->id;


            $fee_receipts = FeesReceiptDetail::where('school_id', $school_id)
            ->where('batch', $batch)->where('cancel_status','1')
            ->where('student_id', $student_id)
            ->select('*', \DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as formatted_created_at"),\DB::raw("DATE_FORMAT(cancel_date, '%d-%m-%Y') as formatted_cancel_date"))
            ->get();

        return Datatables::of($fee_receipts)->make(true);

        } else {

            return redirect('/admin/login');
        }
    }

        

}
