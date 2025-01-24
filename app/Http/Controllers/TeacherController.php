<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use DB;
use Input;
use Response;
use Session;
use Validator;
use View;
use Hash;
use Yajra\DataTables\DataTables;

use App\Models\BackgroundTheme;
use App\Models\Category;
use App\Models\Countries;
use App\Models\Classes;
use App\Models\Sections;
use App\Models\Student;
use App\Models\Subjects;
use App\Models\Periodtiming;
use App\Models\SubjectMapping;
use App\Models\ClassTeacher;
use App\Models\Homeworks;
use App\Models\Circulars;
use App\Models\Teacher;
use App\Models\Leaves;
use App\Models\Teacherleave;
use App\Models\StudentAcademics;
use App\Models\Events;
use App\Models\StudentTests;
use App\Models\Tests;
use App\Models\QuestionBanks;
use App\Models\Terms;
use App\Models\QuestionBankItems;
use App\Models\QuestionTypes;
use App\Models\Chapters;
use App\Models\Timetable;
use App\Models\Exams;
use App\Models\StudentsDailyAttendance;
use App\Models\MarksEntry;
use App\Models\MarksEntryItems;
use App\Models\TestPapers;

use App\Models\CommunicationSms;
use App\Models\CommunicationPost;
use App\Models\CommunicationGroup;
use App\Models\DltTemplate;
use App\Models\SMSCredits;

use App\Http\Controllers\AdminController;

class TeacherController extends Controller
{
    public $accepted_formats = ['jpeg', 'jpg', 'png'];
    public $accepted_formats_audio = ['mp3', 'mp4'];
    public $school;
    public $school_id;

    public function __construct()    { 
        $ourl = config("constants.APP_URL"); 
        $url = $ourl; //URL('/'); 
        $curr = url()->full();
        $url = str_replace('/', '', $url);
        $curr = str_replace('/', '', $curr);
        $re = '/'.$url.'(.*)teacher/'; 
        $str = $curr;
        $this->school =  '';  $this->school_id =  0;
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        //print_r($matches); exit;
        if(is_array($matches) && count($matches)>0) {
            $this->school = $matches[0][1];
            $this->school_id = DB::table('users')->where('user_type', 'SCHOOL')->where('slug_name', $this->school)->value('id');
        }

        //echo $school; exit;
    }

    public function index()
    {
        return view('teacher.login');
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
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            $msg = $validator->errors()->all();

            return response()->json([

                'status' => "FAILED",
                'message' => $msg,

            ]);
        }

        if (Auth::attempt(['email' => $userEmail, 'password' => $password, 'user_type' => 'TEACHER','status' => 'ACTIVE', 'school_college_id' => $this->school_id])) {

            $userStatus = User::where('email', $userEmail)->where('user_type', 'TEACHER')
                ->where('school_college_id', $this->school_id)->where('status', 'ACTIVE')->first();

        } else if (Auth::attempt(['mobile' => $userEmail, 'password' => $password, 'user_type' => 'TEACHER','status' => 'ACTIVE', 'school_college_id' => $this->school_id])) {

            $userStatus = User::where('mobile', $userEmail)->where('user_type', 'TEACHER')
                ->where('school_college_id', $this->school_id)->where('status', 'ACTIVE')->first();
        } 

        if(!empty($userStatus)) {

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


     /* Function: homePage
    Loading Admin Home page */
    public function homePage()
    {
        if (Auth::check()) {
            $user_type = Auth::User()->user_type;
          

            if ($user_type == "TEACHER") { // Super Admin
                return view::make('teacher.home');
            }
        } else {
            return redirect('/teacher/login');
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
            if (Auth::check()) {
                DB::table('users_loginstatus')->insert(['user_id' => Auth::User()->id,
                    'session_id' => $current_session_id,
                    'check_in' => date('Y-m-d H:i:s'),
                    'device_id' => $device_id,
                    'api_token_expiry' => Auth::User()->api_token_expiry,
                    'status' => 'LOGIN',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if (Auth::check()) {
            Auth::logout();
            return redirect('/teacher');
        } else {
            return redirect('/teacher');
        }
    }


    //Profile
    public function viewProfile(){
        $teachers = User::leftjoin('teachers','teachers.user_id','users.id')->where('users.id',Auth::user()->id)->select('users.name','users.passcode','users.last_name','teachers.qualification','teachers.exp','teachers.post_details','teachers.father_name','teachers.address','users.email','users.password','users.id','users.country','users.state_id','users.city_id','users.mobile','users.dob', 'users.gender','users.profile_image')->first();

        $countries = Countries::select('id', 'name')->where('status','=','ACTIVE')->get();

        return view('teacher.viewprofile')->with('teachers',$teachers)->with('countries', $countries);
    }



    public function updateProfile(Request $request)
    {

        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name;
            $lastname = $request->last_name;
            $gender = $request->gender;
            $email = $request->email;
            $mobile = $request->mobile;
            $country = $request->country;
            $dob = $request->dob;
            $state_id = $request->state_id;
            $city_id = $request->city_id;
            $image = $request->file('profile_image');

            $qualification = $request->qualification;
            $exp = $request->exp;
            $post_details = $request->post_details;
            $father_name = $request->father_name;

            $address = $request->address;
            $password = $request->password;

            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
                // 'qualification' => 'required',
                // 'exp' => 'required',
                // 'image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if ($id > 0) {
                $exists = DB::table('users')->where('email', $email)->whereNotIn('id', [$id])
                    ->where('school_college_id', $this->school_id)->where('user_type', 'TEACHER')->first();
            } else {
                $exists = DB::table('users')->where('email', $email)->where('school_college_id', $this->school_id)
                    ->where('user_type', 'TEACHER')->first();
            }


            if (!empty($exists)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Email Already Exists'], 201);
            }


               $date = date('Y-m-d H:i:s');
           
                $users = User::find($id);
                $users->updated_at = $date;
                $users->updated_by = Auth::User()->id;
        
            if(!empty($password)) {
                $users->password = Hash::make($password);
            }

            $users->passcode = $password;
            $users->user_type = "TEACHER";
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
           
            if ($id > 0) {
                $mobile_chk = DB::table('users')->where('user_type', 'TEACHER')->where('mobile', $users->mobile)
                    ->where('school_college_id', $this->school_id)->whereNotIn('id', [$id])->first();
            } else {
                $mobile_chk = DB::table('users')->where('user_type', 'TEACHER')->where('mobile', $users->mobile)
                    ->where('school_college_id', $this->school_id)->first();
            }


            if (!empty($mobile_chk)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Mobile Number Already Exists'], 201);
            }

            if (!empty($image)) {

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/userdocs/');

                $image->move($destinationPath, $countryimg);

                $users->profile_image = $countryimg;

            }
            $users->save();

            $userId = $users->id;

            $teachers = Teacher::where('user_id', $id)->first();
           
            $teachers->user_id = $userId;
            $teachers->qualification = $qualification;
            $teachers->exp = $exp;
            $teachers->post_details = $post_details;
            $teachers->father_name = $father_name;
            $teachers->address = $address;
            $teachers->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Teachers Saved Successfully']);
        } else {
            return redirect('/teacher/login');
        }
    }



    //Student List

        public function viewStudent()
    {
        if (Auth::check()) {
           $teacher_id = Auth::user()->id;
           $user_get =  DB::table('class_teachers')->where('teacher_id', $teacher_id)->first();
           if(!empty($user_get)){
           $class_id = $user_get->class_id;
        }
        else{
            $class_id = 0;
        }
           $countries = Countries::select('id', 'name')->where('status','=','ACTIVE')->get();
          
            $classes = Classes::where('status','=','ACTIVE')->where('id',$class_id)->get();
          
            return view('teacher.student')->with('countries', $countries)->with('classes',$classes);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function getStudents(Request $request)
    {

        if (Auth::check()) {
           $teacher_id = Auth::user()->id;
           $user_get =  DB::table('class_teachers')->where('teacher_id', $teacher_id)->first();
           if(!empty($user_get)){
           $class_id = $user_get->class_id;
           $section_id = $user_get->section_id;
           }
           else{
            $class_id = 0;
            $section_id = 0;
           }
        
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $input = $request->all();
            $status = $request->get('status_id', '');

            $users_qry = User::leftjoin('countries', 'countries.id', 'users.country')
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
            $filtered_qry = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('students', 'students.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'students.class_id')
                ->leftjoin('sections', 'sections.id', 'students.section_id')

                // ->leftjoin('teachers','teachers.class_tutor','students.class_id')
                // ->leftJoin('teachers AS c', function($join){
                //     $join->on('students.section_id', '=', 'c.section_id');

                // })
                ->where('user_type', 'STUDENT')
                ->where('students.class_id',$class_id)
                ->where('students.section_id',$section_id)
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

            if($status != ''){
                $users_qry->where('users.status',$status);
                $filtered_qry->where('users.status',$status);
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
            $totalData = $users_qry->select('users.id')->get();

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
            return redirect('/teacher/login');
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
                $admission_no_chk = DB::table('students')->where('admission_no', $admission_no)->whereNotIn('user_id', [$id])->first();
            } else {
                $admission_no_chk = DB::table('students')->where('admission_no', $admission_no)->first();
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
                $users->user_source_from = 'teacher';
                //$users->joined_date = $date;
                $users->created_at = $date;
                $users->created_by = Auth::User()->id;
            }

            $users->user_type = "STUDENT";
            $users->school_college_id = Auth::User()->school_college_id;

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
            $users->passcode = $password;
            $country_code = DB::table('countries')->where('id', $country)->value('phonecode');
            $users->mobile = $mobile;
            $users->mobile1 = $mobile1;
            $users->country = $country;
            $users->country_code = $country_code;
            $users->code_mobile = $country_code.$mobile;
            $users->codemobile1 = $country_code.$mobile1;
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
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }
/*    public function viewStudent()
    {
        if (Auth::check()) {
            $countries = Countries::select('id', 'name')->get();
            $classes = Classes::all();
            return view('teacher.student')->with('countries', $countries)->with('classes',$classes);
        } else {
            return redirect('/teacher/student');
        }
    }

    public function getStudent(Request $request)
    {

        if (Auth::check()) {

            $teacher = DB::table('teachers')->where('user_id', Auth::user()->id)->select('*')->first();

            $students = Student::query()
                ->with(['users' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'email', 'gender', 'dob', 'country', 'state_id', 'city_id', 'profile_image', 'mobile');
                }])->where('class_id',$teacher->class_tutor)->where('section_id',$teacher->section_id)
                ->get();

            return Datatables::of($students)->make(true);
        } else {
            return redirect('/teacher/student');
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
            $mobile = $request->mobile;
            $dob = $request->dob;
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

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mobile' => 'required',
                'roll_no' => 'required',
                'admission_no' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }


            $users = User::find($id);
            $users->name = $name;
            $users->last_name = $lastname;
            $users->gender = $gender;
            $users->dob = $dob;
            $users->email = $email;
            $users->mobile = $mobile;
            $users->country = $country;
            $users->state_id = $state_id;
            $users->city_id = $city_id;
            if (!empty($image)) {

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/userdocs/');

                $image->move($destinationPath, $countryimg);

                $users->profile_image = $countryimg;

            }
            $users->save();

            $userId = $users->id;


                $students = Student::where('user_id', $id)->first();
            $students->user_id = $userId;
            $students->roll_no = $roll_no;
            $students->class_id = $class_id;
            $students->section_id = $section_id;
            $students->admission_no = $admission_no;
            $students->father_name = $father_name;
            $students->address = $address;
            $students->status = $status;
            $students->update();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Student details Updated Successfully']);
        } else {
            return redirect('/teacher/login');
        }
    }


    public function editStudent(Request $request)
    {

        if (Auth::check()) {

                $students = Student::query()
                ->with(['users' => function ($query) {
                    $query->select('id', 'name', 'last_name', 'email', 'gender', 'dob', 'country', 'state_id', 'city_id', 'profile_image', 'mobile');
                }])
                ->where('user_id', $request->id)
                ->get();

            if ($students->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $students[0], 'message' => 'Student Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Student Detail']);
            }
        } else {
            return redirect('/teacher/login');
        }
    }*/

    // Time tables

      //Time tables

      public function fetchClasses(Request $request){
        $getclass_id = Classes::where('id',$request->class_id)->where('status','ACTIVE')->select('class_name','position')->first();
        if(!empty($getclass_id)) {
           $class_name = $getclass_id->class_name;
           $position = $getclass_id->position;
       $data['classes'] = DB::table('classes')->where('position', '>', $position)->where('status','=','ACTIVE')->get();
      
        }
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
      public function fetchSection(Request $request)
      {
           $teacher_id = Auth::user()->id;
           $user_get =  DB::table('class_teachers')->where('teacher_id', $teacher_id)->first();
           
           $section_id = $user_get->section_id;

         $data['section'] = Sections::where('class_id', $request->class_id)->where('id',$section_id)->where('status','=','ACTIVE')
              ->get(["section_name", "id"]);

          return response()->json($data);
      }


      public function fetchTest(Request $request)
      {
  
         $data['tests'] = Tests::where('class_id', $request->class_id)->where('is_self_test',0)->where('subject_id', $request->subject_id)->where('status','=','ACTIVE')
              ->get(["test_name", "id"]);
  
          return response()->json($data);
      }

    
      public function fetchSectionAll(Request $request)
      {
  
          $data['section'] = Sections::where('class_id', $request->class_id)->where('status','=','ACTIVE')
              ->get(["section_name", "id"]);
  
          return response()->json($data);
      }


      public function fetchExams(Request $request)
      {
          $data['exams'] = Exams::whereRAW(' FIND_IN_SET('.$request->class_id.', class_ids) ')
              ->select("exam_name", "id", "exam_startdate", DB::RAW(' DATE_FORMAT(exam_startdate, "%Y-%m") as monthyear'))->get();
  
          return response()->json($data);
      }

      public function fetchClassTerms(Request $request)
      {
          $data['terms'] = Terms::whereRAW(' FIND_IN_SET('.$request->class_id.', class_ids) ')->where('status','ACTIVE')->select("term_name", "id")->get();
          return response()->json($data);
      }

      public function fetchMappedClassTerms(Request $request)
      {
          $data['terms'] = Terms::whereRAW(' FIND_IN_SET('.$request->class_id.', class_ids) ')->where('status','ACTIVE')->select("term_name", "id")->get();
          return response()->json($data);
      }


      public function fetchStudent(Request $request)
      {
  
        $data['student'] = Student::leftjoin('users','users.id','students.user_id')->where('students.class_id', $request->class_id)->where('students.section_id',$request->section_id)->where('users.status','=','ACTIVE')
              ->get(["users.name", "users.id"]);
  
          return response()->json($data);
      }

      
      public function fetchSubjectSection(Request $request)
      {
           $teacher_id = Auth::user()->id;
         $data['section'] = SubjectMapping::leftjoin('sections','sections.id','subject_mapping.section_id')->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.class_id',$request->class_id)->groupby('subject_mapping.section_id')->get(["sections.section_name", "sections.id"]);

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


      public function fetchSubjectMapped(Request $request)
      {
           $teacher_id = Auth::user()->id;
          
         $data['subject'] = SubjectMapping::leftjoin('subjects','subjects.id','subject_mapping.subject_id')->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.section_id',$request->section_id)->get(["subjects.subject_name", "subjects.id"]);

          return response()->json($data);
      }

      public function fetchClassSubjectMapped(Request $request)
      {
           $teacher_id = Auth::user()->id;
          
         $data['subject'] = SubjectMapping::leftjoin('subjects','subjects.id','subject_mapping.subject_id')->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.class_id',$request->class_id)->get(["subjects.subject_name", "subjects.id"]);

          return response()->json($data);
      }

      public function fetchClassChapter(Request $request)
      {
           $teacher_id = Auth::user()->id;
          
         $data['chapter'] = Chapters::where('class_id',$request->class_id)->where('subject_id',$request->subject_id)->get(["id", "chaptername"]);

          return response()->json($data);
      }
     
      public function viewTimetable(Request $request) {
        if (Auth::check()) {

            $class = $periods = $days = $subjects = '';

            $userData = DB::table('class_teachers')->where('teacher_id',Auth::user()->id)->first();
            if(!empty($userData)){
                $class_id = $userData->class_id;
            }
            else{
                $class_id = 0;
            }
            

            $class = Classes::select('*')->get()->where('status','=','ACTIVE')->where('id',$class_id);
            // exit;
           return view('teacher.timetable')->with('class', $class);

        } else {
            return redirect('/login');
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

                    $periods = Periodtiming::where('class_id',$class_id)->select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first();
                    // echo count($periods);
                    // exit;

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
       }
                else{
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'Please Assign Periods For Selected Class']);
                }
                    $idsArr = explode(',', $map_subjects);
                    $subjects = DB::table('subjects')->whereIn('id', $idsArr)->get();
                    // $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first()->toArray();
                    $class = Classes::select('*')->get();
                    $days = DB::table('days')->select('*')->get();
                    $html = view('teacher.loadtimetable')->with('class', $class)->with('periods', $periods)->with('days', $days)->with('subjects', $subjects)->with('class_id', $class_id)->with('section_id', $section_id)->with('timetable', $timetable)->render();
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

    //Home Works
    public function viewHomework()
    {
        if (Auth::check()) {
             

            
             $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            $subjects = Subjects::all();

            $periods = Periodtiming::select('period_1', 'period_2', 'period_3', 'period_4', 'period_5', 'period_6', 'period_7', 'period_8')->first()->toArray();
            return view('teacher.homework')->with('classes', $classes)->with('subjects', $subjects)->with('periods', $periods);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function getHomework(Request $request)
    {

        if (Auth::check()) {
            $status = $request->get('status','0');

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->where('status','ACTIVE')->get();
            $class_ids = array();
            $section_ids = array(); 
            $subject_ids = array();
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    $section_id = $post->section_id;
                    $subject_id = $post->subject_id;
                    array_push($class_ids,$class_id);
                    array_push($section_ids,$section_id);
                    array_push($subject_ids,$subject_id);
                }
            }
 
            if($status != ''){
                $subjects =Homeworks::leftjoin('subject_mapping','subject_mapping.subject_id','homeworks.subject_id')->whereIn('homeworks.class_id',$class_ids)->whereIn('homeworks.section_id',$section_ids)->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.status','ACTIVE')->where('homeworks.status',$status)->select('homeworks.*')->get();

            }else{
                $subjects =Homeworks::leftjoin('subject_mapping','subject_mapping.subject_id','homeworks.subject_id')->whereIn('homeworks.class_id',$class_ids)->whereIn('homeworks.section_id',$section_ids)->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.status','ACTIVE')->select('homeworks.*')->get();
            }
        return Datatables::of($subjects)->make(true);
        } else {
            return redirect('/teacher/login');
        }

    }

    public function postHomework_old(Request $request)
    {

        if (Auth::check()) {
            $id = $request->id;

            $subject_id = $request->subject_id;
            // $period = $request->period;
            $hw_title = $request->hw_title;
            $hw_description = $request->hw_description;
            $hw_date = $request->hw_date;
            $hw_submission_date = $request->hw_submission_date;
            $position = $request->position;
            $status = $request->status;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
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
                //'hw_title' => 'required',
                // 'hw_description' => 'required',
                'hw_date' => 'required',
                'hw_submission_date' => 'required',
                // 'position' => 'required',
                // 'status' => 'required',
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
                $homework->updated_by = Auth::user()->id;

            } else {
                $homework = new Homeworks();
                $homework->created_at = date('Y-m-d H:i:s');
                $homework->created_by = Auth::user()->id;
            }

            $homework->school_id = $this->school_id;
            $homework->class_id = $class_id;
            $homework->section_id =  $section_id;
            $homework->subject_id = $subject_id;
            $homework->test_id = $test;
            // $homework->period = $period;
            $homework->hw_title = $hw_title;
            $homework->hw_description = $hw_description;
            $homework->hw_date = $hw_date;
            $homework->hw_submission_date = $hw_submission_date;
            $homework->position = 1; // $position;
            $homework->status = 'ACTIVE'; //$status;


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
            return redirect('/teacher/login');
        }
    }

    public function postHomework(Request $request) {
        if (Auth::check()) {
            $id = $request->id;
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $sms_alert = $request->has('sms_alert') ? 1 : 0;
            $subject_ids = $request->subject_id; // Array of subject_ids
            $hw_descriptions = $request->hw_description; // Array of hw_descriptions
            $hw_date = $request->hw_date;
            $hw_submission_date = $request->hw_submission_date;
            $position = $request->position;
            $status = $request->status;
            $test_id = $request->test_id;
            $count_subject = count($subject_ids);
            // Convert test_id array to comma-separated string
            $test = !empty($test_id) ? implode(',', $test_id) : '';
            $is_hw_attachment = $request->is_hw_attachment;
            $is_dt_attachment = $request->is_dt_attachment;
            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'section_id' => 'required',
                'subject_id' => 'required|array',
                'subject_id.*' => 'required', // Each subject_id must be present
                'hw_description' => 'required|array',
                'hw_description.*' => 'required', // Each hw_description must be present
                'hw_date' => 'required',
                'hw_submission_date' => 'required',
            ]);
            if ($validator->fails()) {
                $msg = $validator->errors()->all();
                return response()->json([
                    'status' => "FAILED",
                    'message' => "Please check your inputs",
                ]);
            }
            // Check homework submission date validity
            if (strtotime($hw_submission_date) <= strtotime($hw_date)) {
                return response()->json([
                    'status' => 'FAILED',
                    'message' => 'Homework Submission Date must be greater than Homework Date',
                ]);
            }
            // Initialize variables to store filenames from the first loop
            $hw_attachment_filename = null;
            $dt_attachment_filename = null;
            // Process each subject_id and hw_description pair
            foreach ($subject_ids as $index => $subject_id) {
                if ($id > 0) {
                    $homework = Homeworks::find($id);
                    $homework->updated_at = date('Y-m-d H:i:s');
                    $homework->updated_by = Auth::User()->id;
                } else {
                    $homework = new Homeworks();
                    $homework->created_at = date('Y-m-d H:i:s');
                    $homework->created_by = Auth::User()->id;

                    if ($index === 0) {
                        // Last Order id
                        $lastorderid = DB::table('homeworks')
                            ->orderby('id', 'desc')->select('id')->limit(1)->get();

                        if ($lastorderid->isNotEmpty()) {
                            $lastorderid = $lastorderid[0]->id;
                            $lastorderid = $lastorderid + 1;
                        } else {
                            $lastorderid = 1;
                        }

                        $append = str_pad($lastorderid, 3, "0", STR_PAD_LEFT);
                        $ref_no = date("Ymd") . $append;   

                        $homework->ref_no = $ref_no;
                    }

                }

                $homework->school_id = Auth::User()->school_college_id;
                $homework->class_id = $class_id;
                $homework->section_id = $section_id;
                $homework->is_sms_alert = $sms_alert;
                $homework->subject_id = $subject_id;
                $homework->test_id = !empty($test_id[$index]) ? $test_id[$index] : null;
                $homework->hw_description = $hw_descriptions[$index];
                $homework->hw_date = $hw_date;
                $homework->hw_submission_date = $hw_submission_date;
                $homework->position = 1; // You may adjust this based on your form
                $homework->status = 'ACTIVE'; // You may adjust this based on your form
                // Handle homework attachment only for the first loop
                if ($index === 0) {
                    $homeworkfile = $request->file('hw_attachment');
                    if (!empty($homeworkfile)) {
                        $ext = $homeworkfile->getClientOriginalExtension();
                        if (!in_array($ext, $this->accepted_formats)) {
                            return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                        }
                        $topicimg = rand() . time() . '.' . $homeworkfile->getClientOriginalExtension();
                        $destinationPath = public_path('/image/homework');
                        $homeworkfile->move($destinationPath, $topicimg);
                        $hw_attachment_filename = $topicimg;
                    }
                }
                // Handle daily task attachment only for the first loop
                if ($index === 0) {
                    $dailytask = $request->file('dt_attachment');
                    if (!empty($dailytask)) {
                        $ext = $dailytask->getClientOriginalExtension();
                        if (!in_array($ext, $this->accepted_formats)) {
                            return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                        }
                        $topicimg = rand() . time() . '.' . $dailytask->getClientOriginalExtension();
                        $destinationPath = public_path('/image/dailytask');
                        $dailytask->move($destinationPath, $topicimg);
                        $dt_attachment_filename = $topicimg;
                    }
                }
                // Assign filenames from the first loop if available
                $homework->hw_attachment = $hw_attachment_filename;
                $homework->dt_attachment = $dt_attachment_filename;
                // Save the homework object
                
                $homework->save();


                // Update related tests if necessary
                if (!empty($test_id) && count($test_id) > 0) {
                    foreach ($test_id as $tid) {
                        $test_to_date = DB::table('tests')->where('id', $tid)->value('to_date');
                        $hw_submission_date = date('Y-m-d', strtotime($hw_submission_date));
                        if (strtotime($test_to_date) < strtotime($hw_submission_date)) {
                            DB::table('tests')->where('id', $tid)->update([
                                'to_date' => $hw_submission_date,
                                'updated_by' => Auth::User()->id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                } 

                // Fetch the last created homework record to get the filenames for subsequent loops
                if ($index === 0 && $homework->id) {
                    $firstHomework = Homeworks::find($homework->id);
                    if ($firstHomework) {
                        $hw_attachment_filename = $firstHomework->hw_attachment;
                        $dt_attachment_filename = $firstHomework->dt_attachment;
                        $ref_no = $firstHomework->ref_no;
                    }
                }
            }
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Homework Saved Successfully',
            ]);
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
            return redirect('/teacher/login');
        }
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
            return view('teacher.previewtest')->with(['qb'=>$qb]);
        } else {
            return redirect('/teacher/login');
        }
    }


        //Circulars
    /* Function: viewCirculars
     */
    public function viewCirculars()
    {
        if (Auth::check()) {
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            return view('teacher.circulars')->with('classes', $classes);
        } else {
            return redirect('/teacher/login');
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
            $status = $request->get('status','0');
            $cls_id = $request->get('cls_id',0);
            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }

                      
            // $classes = SubjectMapping::leftjoin('classes ','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();

            $sectionsqry = Circulars::where('circular.id', '>', 0)
                ->select('circular.*')
                ->whereIn('class_ids' ,$class_ids);
                if($status != ''){

                    $sectionsqry->where('status','=',$status);
                }
            $filteredqry = Circulars::where('circular.id', '>', 0)
            ->whereIn('class_ids',$class_ids)
                ->select('circular.*');
                if($status != ''){

                    $filteredqry->where('status','=',$status);
                }

                
                if($cls_id != ''){
                    $sectionsqry->where('class_ids','=',$cls_id);
                    $filteredqry->where('class_ids','=',$cls_id);
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

            $totalDataqry = Circulars::orderby('id', 'asc')->whereIn('class_ids', $class_ids);
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
            return redirect('/teacher/login');
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
            $approve_status = 'UNAPPROVED';

            $validator = Validator::make($request->all(), [
                'class_ids' => 'required',
                'circular_title' => 'required',
                'circular_message' => 'required',
                'circular_date' => 'required',
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
            $circular->school_id = $this->school_id;
            $circular->class_ids = $class_ids;
            $circular->circular_title = $circular_title;
            $circular->circular_message = $circular_message;
            $circular->circular_date = $circular_date;
            $circular->approve_status = $approve_status;

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
            $circular->status = $status;

            $circular->save();
            return response()->json(['status' => "SUCCESS", 'message' => 'Circular Saved Successfully']);
        } else {
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }

    //Students leave
    public function viewStudentLeave()
    {
        if (Auth::check()) {
            $teacher =ClassTeacher::where('teacher_id', Auth::user()->id)->select('*')->first();
            if(!empty($teacher)){
             $class_id = $teacher->class_id;
             $section_id = $teacher->section_id;
            }
            else{
            $class_id = 0;
            $section_id = 0;
            }
            $student = User::leftjoin('students', 'students.user_id', 'users.id')
            ->where('users.user_type', 'STUDENT')
            ->where('users.status','ACTIVE')
            ->where('students.class_id',$class_id)
            ->where('students.section_id',$section_id)
            ->select('users.*')->get();
            return view('teacher.studentleave')->with('teacher',$teacher)->with('student',$student);

        } else {
            return redirect('/teacher/login');
        }
    }

    public function getStudentLeave(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
            $student_id = $request->get('student_id','');
            $teacher = DB::table('class_teachers')->where('teacher_id', Auth::user()->id)->select('*')->first();
             if($teacher){
               $class_id = $teacher->class_id;
               $section_id = $teacher->section_id;
             }
             else{
                $class_id = 0;
                $section_id = 0;                
             }
             $leave_qry = Leaves::where('class_id',$class_id)->where('section_id',$section_id);           

            if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $leave_qry->whereRaw('leave_date >= ?', [$mindate]);
               
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime($maxdate));
                $leave_qry->whereRaw('leave_date <= ?', [$maxdate]);
             }
             if($student_id != '' || $student_id != 0){
                $leave_qry->where('student_id',$student_id);
              }
            
              $leave = $leave_qry->get();
            return Datatables::of($leave)->make(true);
        } else {
            return redirect('/teacher/login');
        }

    }


    
    public function editStudentLeave(Request $request)
    {
        if (Auth::check()) {  $id = $request->get('id');
           // $student = Student::leftjoin('users','users.id','students.user_id')->get('students.user_id','users.name as name');
           $leave = Leaves::leftjoin('classes','classes.id','leaves.class_id')->leftjoin('sections','sections.id','leaves.section_id')->leftjoin('users','users.id','leaves.student_id')->where('leaves.id',$id)->select('leaves.*','classes.class_name','sections.section_name','users.name')->first();
           // echo "<pre>";print_r($leave);
               return view('teacher.editstudentleave')->with('qb',$leave);
            } else {
            return redirect('/teacher/login');
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
           $limit = $request->get('length', '10');
           $start = $request->get('start', '0');
           $dir = $request->input('order.0.dir');
           $columns = $request->get('columns');
           $order = $request->input('order.0.column');

           $input = $request->all();
           $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
           $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

           $teacher = DB::table('class_teachers')->where('teacher_id', Auth::user()->id)->select('*')->first();

           $leave_qry = Leaves::where('class_id',$teacher->class_id)->where('section_id',$teacher->section_id);

           
            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'status') {
                            $leave_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $leave_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                          
                        }
                    }
                }
            }

            
            if(!empty(trim($mindate))) {
               $mindate = date('Y-m-d', strtotime($mindate));
               $leave_qry->whereRaw('leave_date >= ?', [$mindate]);
             
           }
           if(!empty(trim($maxdate))) {
               $maxdate = date('Y-m-d', strtotime($maxdate));
               $leave_qry->whereRaw('leave_date <= ?', [$maxdate]);
         }
            if($student_id != '' || $student_id != 0){
               $leave_qry->where('student_id',$student_id);
              
            }
            
         

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $leave_qry->orderBy($orderby, $dir)->get();
         
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
            return redirect('/teacher/login');
        }
}
     //teacher leave
     public function viewTeacherLeave()
     {
         if (Auth::check()) {
             return view('teacher.teacherleave');

         } else {
             return redirect('/teacher/login');
         }
     }

     public function getTeacherLeave(Request $request)
     {

         if (Auth::check()) {
            $input = $request->all();
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $teacherleave_qry = Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->where('teacher_leave.user_id',Auth::user()->id)->select('teacher_leave.id','teacher_leave.title','teacher_leave.duration','teacher_leave.from_date','teacher_leave.to_date','teacher_leave.description','teacher_leave.descriptionfile','teacher_leave.leave_apply_file','teacher_leave.status','users.name');
             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $teacherleave_qry->whereRaw('from_date >= ?', [$mindate]);
              
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime($maxdate));
                $teacherleave_qry->whereRaw('from_date <= ?', [$maxdate]);
              }

              $teacherleave = $teacherleave_qry->get();

             return Datatables::of($teacherleave)->make(true);
         } else {
             return redirect('/teacher/login');
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
              $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
             $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
             $teacherleave_qry = Teacherleave::leftjoin('users','users.id','teacher_leave.user_id')->where('teacher_leave.user_id',Auth::user()->id)->select('teacher_leave.id','teacher_leave.title','teacher_leave.duration','teacher_leave.from_date','teacher_leave.to_date','teacher_leave.description','teacher_leave.descriptionfile','teacher_leave.leave_apply_file','teacher_leave.status','users.name');
           if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['search']['value']) && !empty($value['name'])) {
                        if ($value['name'] == 'status') {
                            $teacherleave_qry->where($value['name'], 'like', $value['search']['value'] . '%');
                     } else {
                            $teacherleave_qry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                     }
                    }
                }
            }

             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $teacherleave_qry->whereRaw('from_date >= ?', [$mindate]);
               
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime($maxdate));
                $teacherleave_qry->whereRaw('from_date <= ?', [$maxdate]);
              
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
            $teacher_leave = $teacherleave_qry->get();

               $teacher_leave_excel = [];

        if (! empty($teacher_leave)) {
            $i = 1;
            foreach ($teacher_leave as $rev) {
             $teacher_leave_excel[] = [
                    "S.No" => $i,
                    "Teacher Name" => $rev->name,
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
             return redirect('/teacher/login');
         }

     }


    public function postTeacherLeave(Request $request)
    {

        if (Auth::check()) {
            $id = $request->id;
            $title = $request->title;
            $duration = $request->duration;
            $from_date = $request->fromdate;
            $to_date = $request->todate;
            $leave_type = $request->leaveType;
            $leave_apply_file = $request->file('LeaveApplyFile');


            $validator = Validator::make($request->all(), [

            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs",
                ]);
            }

            if ($leave_type == 'text') {
                $description = $request->description;
                if (empty($description)) {
                    return response()->json([
                        'status' => "FAILED",
                        'message' => "Please enter leave reason",
                    ]);
                }
            } else if ($leave_type == 'audio') {
                $image = $request->file('descriptionfile');

                if (empty($image)) {
                    return response()->json([
                        'status' => "FAILED",
                        'message' => "Please Upload the audio File",
                    ]);
                }
            }

            if($id>0) {
                $teacherleave = Teacherleave::find($id);
                $teacherleave->updated_at = date('Y-m-d H:i:s');
            } else {
                $teacherleave = new Teacherleave();
                $teacherleave->created_at = date('Y-m-d H:i:s');
            }

            $teacherleave->user_id = Auth::user()->id;
            $teacherleave->title = $title;
            $teacherleave->duration = $duration;
            $teacherleave->from_date = $from_date;
            $teacherleave->to_date = $to_date;
            $teacherleave->leave_type = $leave_type;
            $teacherleave->description = $request->description;


            $image = $request->file('descriptionfile');
            if (!empty($image)) {

                $topicimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/image/teacherleaves/audio');

                $image->move($destinationPath, $topicimg);

                $teacherleave->descriptionfile = $topicimg;
            }

            $leave_apply_file = $request->file('LeaveApplyFile');
            if (!empty($leave_apply_file)) {

                $topicimg = rand() . time() . '.' . $leave_apply_file->getClientOriginalExtension();

                $destinationPath = public_path('/image/teacherleaves');

                $leave_apply_file->move($destinationPath, $topicimg);

                $teacherleave->leave_apply_file = $topicimg;
            }

            $teacherleave->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Leave applied Successfully',
            ]);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function editTeacherLeave(Request $request)  {
        if (Auth::check()) {
            $teacherleave = Teacherleave::where('id', $request->id)->get();
            if ($teacherleave->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $teacherleave[0], 'message' => 'Leave Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Leave Detail']);
            }
        } else {
            return redirect('/teacher/login');
        }
    }

    //Marks Entry Management
    /* Function: viewMarksEntry
     */
    public function viewMarksEntry(Request $request)   {
        if(Auth::check()){
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id');
            $classes = $classes->orderby('classes.position', 'Asc')->get();
            $monthyear = $request->get('monthyear', '');
            $class_id = $request->get('class_id', 0);
            $section_id = $request->get('section_id', 0);
            $exam_id = $request->get('exam_id', 0);
            $subject_id = $request->get('subject_id', 0);
            if(empty($monthyear)) {
                $monthyear = date('Y-m');
            }
            $students = [];

            return view('teacher.marksentry')->with(['students'=>$students,
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
            $grade = $request->grade;
            $student_id = $request->student_id;

            $validator = Validator::make($request->all(), [
                'monthyear' => 'required',
                'class_id' => 'required',
                'section_id' => 'required',
                'exam_id' => 'required',
                // 'subject_id' => 'required',
                'total_marks' => 'required',
                'marks' => 'required',
                'remarks' => 'required',
                'grade' => 'required',
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
                    ->where(['class_id'=>$class_id, 'section_id'=>$section_id, 'exam_id'=>$exam_id,   'monthyear'=>$monthyear])->first();

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
                
               $exentry = DB::table('marks_entry_items')->where('mark_entry_id', $mark_entry_id)
                    ->where('subject_id', $subject)->first();
            
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
                    $avg = $total_marks / $cnt;
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

                DB::table('marks_entry')->where('id', $mark_entry_id)
                    ->update(['total_marks'=>$total_marks, 'marks' => $marks, 'remarks'=>$remarks,
                              'grade'=>$grade, 'pass_type'=>$pass_type,
                              'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id
                    ]);

                return response()->json(['status' => 1, 'message' => 'Saved Successfully']);


                // if(!empty($ex)) {
                //     DB::table('marks_entry')->where('user_id', $student_id)
                //     ->where(['class_id'=>$class_id, 'section_id'=>$section_id, 'exam_id'=>$exam_id,
                //             'subject_id'=>$subject_id, 'monthyear'=>$monthyear])
                //         ->update(['total_marks'=>$total_marks, 'marks'=>$marks,
                //                 'remarks'=>$remarks, 'grade'=>$grade,
                //                 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                // }   else {
                //     DB::table('marks_entry')->insert([
                //         'user_id'=>$student_id,
                //         'class_id'=>$class_id, 'section_id'=>$section_id,
                //         'exam_id'=>$exam_id, 'subject_id'=>$subject_id,
                //         'monthyear'=>$monthyear, 'total_marks'=>$total_marks,
                //         'marks'=>$marks, 'remarks'=>$remarks, 'grade'=>$grade,
                //         'created_at'=>date('Y-m-d H:i:s'),
                //         'created_by'=>Auth::User()->id
                //     ]);
                // }
            }

            return response()->json(['status' => 1, 'message' => 'Saved Successfully']);
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
            $school_id = Auth::User()->school_college_id;

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
            $log = DB::enableQueryLog();
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
                                $gr = DB::table('grades')->where('school_id', '<=', $school_id)
                                    ->where('mark', '<=', $marks)->orderby('mark', 'desc')->first();
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
                                    $avg = ($marks / $total_marks) * 100;

                                    $grade = '';
                                    $gr = DB::table('grades')->where('school_id', $school_id)
                                        ->where('mark', '<=', $avg)->orderby('mark', 'desc')->first();
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
            /*if($subject_id  != '' || $subject_id  != ''){
              User::$subject_id = $subject_id;
            }*/
            // if($subject_id  != '' || $subject_id  != ''){
            //     MarksEntry::$subject_id = $subject_id;
            //     }
            /*$subject_id =  SubjectMapping::where('teacher_id',Auth::user()->id)->select('subject_id')->get();
            $subject_id_get = [];
            foreach ($subject_id as $sub){
                array_push($subject_id_get,$sub->subject_id);
            }
           
            $marked_subject = DB::table('marks_entry_items')->leftjoin('marks_entry','marks_entry.id','marks_entry_items.mark_entry_id')->where('.user_id',$student_id)->where('marks_entry.class_id',$class_id)->where('marks_entry.monthyear', $monthyear)->where('marks_entry.section_id',$section_id)->whereIn('marks_entry_items.subject_id',$subject_id_get)->select('marks_entry_items.*')->get();*/

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



               /*$subject = SubjectMapping::leftjoin('sections','sections.id','subject_mapping.section_id')->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.class_id',$class_id)->where('subject_mapping.section_id',$section_id)->get();*/

              
            if($students->isNotEmpty()) {
                $subjects = DB::table('exams')->leftjoin('exam_sessions', 'exam_sessions.exam_id', 'exams.id')
                        ->leftjoin('subjects', 'subjects.id', 'exam_sessions.subject_id')
                        ->where('exams.id',$exam_id);
                if($subject_id  >0){
                    $subjects->where('exam_sessions.subject_id',$subject_id);
                }
                if($class_id  >0){
                    $subjects->where('exam_sessions.class_id',$class_id);
                }
                $subjects = $subjects->select('exam_sessions.subject_id as is_subject_id', 'subjects.subject_name as is_subject_name')
                        ->get();
                $students = $students->toArray();
                /*$subject = $subject->toArray();
                $marked_subject = $marked_subject->toArray();*/
                // echo "<pre>"; print_r($students);exit;

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

                $html = view('teacher.loadmarksentry')->with(['monthyear'=>$monthyear, 'students'=>$students,'subjects'=> $subjects, 'totalmarks'=>$total_marks])->render();

                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students marks']);

            }   else {
                $students = [];
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'The Selected Student was not Mapped to Class']);
            }
        }else{
            return redirect('/login');
        }
    }

    //Attendance Management
    /* Function: viewStudentAttendance
     */
    public function viewStudentAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $class_id = $section_id = '';
            $lastdate = date('t', strtotime(date('Y-m')));
            $user_get =  DB::table('class_teachers')->where('teacher_id', Auth::user()->id)->first();
            if(!empty($user_get)){
                $class_id = $user_get->class_id;
            }
           else{
            $class_id = 0;
           }
           
            $classes = Classes::where('status', 'ACTIVE')->where('id',$class_id)->orderby('position', 'Asc')->get();
            $students  = ''; 
            return view('teacher.studentsattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate, 'students'=>$students]);
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

                $students = User::with('attendance')
                    ->leftjoin('students', 'students.user_id', 'users.id')
                    ->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                    ->where('user_type', 'STUDENT')
                    ->whereIn('users.id', $userids)
                    /*//->where('academic_year', $monthyear)
                    ->whereRaw("'".$monthyear."' BETWEEN from_month and to_month")
                    ->where('student_class_mappings.class_id', $class_id)
                    ->where('student_class_mappings.section_id', $section_id)*/
                    ->select('users.id', 'name', 'email', 'mobile', 'students.class_id', 'students.section_id', 'students.admission_no')
                    ->get();

                    foreach($students as $k=>$v){
                        list($year, $month) = explode('-', $monthyear);
                     $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                        ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                        ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                        $students[$k]->holidays_list = $holidays;
                    }

            if($students->isNotEmpty()) {
                $students = $students->toArray();
                //echo "<pre>"; print_r($students);exit;
                $html = view('teacher.loadstudentsattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'students'=>$students, 'lastdate'=>$lastdate])->render();

                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students attendance Detail']);

            } 
          }  else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
            }

            return view('teacher.studentsattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
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

            $validator = Validator::make($request->all(), [
                'student_id' => 'required',
                'mode' => 'required',
                'day' => 'required',
                'monthyear' => 'required',
                'class_id' => 'required',
                'section_id' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([
                    'status' => 0,
                    'message' => implode(', ', $msg)
                ]);
            }

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
                    return view('teacher.updateattendance')->with(['players'=>$players, 'monthyear'=>$monthyear,
                        'lastdate'=>$lastdate])->with('class_id',$class_id)->with('section_id',$section_id);
                }  else {
                    return view('teacher.updateattendance')->with(['error'=>1]);
                }
            }   else {
                return view('teacher.updateattendance')->with(['error'=>1]);
            }
        }else{
            return redirect('/login');
        }
    }

    public function viewStudentAcademics(Request $request) {
        if (Auth::check()) {
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array();
            $section_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    $section_id = $post->section_id;
                    array_push($class_ids,$class_id);
                    array_push($section_ids,$section_id);
                }
            }


            $students  = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                ->where('users.status', 'ACTIVE')->where('user_type', 'STUDENT')
                ->select('users.id', 'name', 'last_name', 'admission_no')->orderby('admission_no', 'Asc')->get();
            return view('teacher.studentsacademics')->with('classes', $classes)->with('students', $students);

            $students = DB::table('users')->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                ->where('user_type', 'STUDENT')
                ->select('users.id', 'users.name', 'users.email', 'student_class_mappings.*')->get();

            if ($students->isNotEmpty()) {
                $html = view('teacher.student_academics')->with('students', $students)->render();
                return response()->json(['status' => 'SUCCESS', 'data' => $students, 'message' => 'Student Academics Detail',
                    'data' => $html
                ]);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Student Detail']);
            }
        } else {
            return redirect('/teacher/login');
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

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array();
            $section_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    $section_id = $post->section_id;
                    array_push($class_ids,$class_id);
                    array_push($section_ids,$section_id);
                }
            }

            $sectionsqry = StudentAcademics::leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->leftjoin('students', 'students.user_id', 'student_class_mappings.user_id')
                ->leftjoin('classes', 'classes.id', 'student_class_mappings.class_id')
                ->leftjoin('sections', 'sections.id', 'student_class_mappings.section_id')
                ->whereIn('student_class_mappings.class_id', $class_ids)
                ->whereIn('student_class_mappings.section_id', $section_ids)
                ->select('student_class_mappings.*', 'users.name', 'users.last_name', 'students.admission_no', 'classes.class_name', 'sections.section_name');
            $filteredqry = StudentAcademics::leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->leftjoin('students', 'students.user_id', 'student_class_mappings.user_id')
                ->leftjoin('classes', 'classes.id', 'student_class_mappings.class_id')
                ->leftjoin('sections', 'sections.id', 'student_class_mappings.section_id')
                ->whereIn('student_class_mappings.class_id', $class_ids)
                ->whereIn('student_class_mappings.section_id', $section_ids)
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

            $totalDataqry = StudentAcademics::orderby('id', 'asc')->whereIn('student_class_mappings.class_id', $class_ids)
            ->whereIn('student_class_mappings.section_id', $section_ids);
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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }



    // Promotions
  
    public function viewStudentPromotions(Request $request) {
        if (Auth::check()) {
            $user_get =  DB::table('class_teachers')->where('teacher_id', Auth::user()->id)->first();
            if(!empty($user_get)){
                $class_id = $user_get->class_id;
            }
           else{
            $class_id = 0;
           }
            $classes = Classes::where('status', 'ACTIVE')->where('id',$class_id)->orderby('position', 'Asc')->get();
             return view('teacher.student_promotions')->with('classes', $classes);

      
        } else {
            return redirect('/teacher/login');
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
            if(is_array($qbid) && count($qbid)>0) {
            $qb = User::whereIn('id',$qbid)->get();
            foreach($qb as $k=> $v){
                $user_id = $v->id;
          
                DB::table('students')
                ->where('user_id', $user_id)
                ->update(['class_id'=>$class_id, 'section_id' => $section_id,                  'updated_at'=>date('Y-m-d H:i:s')]);
            }
       
            }   else {
                $err = 'Please select the Students ';
            }
            // echo "<pre>"; print_r($qb); exit;
            // return view('teacher.viewqbfrtest')->with(['qbank'=>$qb, 'err'=>$err]);
        } else {
            return redirect('/teacher/login');
        }
    }
    //Events
    /* Function: viewEvents
     */
    public function viewEvents()
    {
        if (Auth::check()) {
           
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();

            return view('teacher.events')->with('classes', $classes);
        } else {
            return redirect('/teacher/login');
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
            $cls_id = $request->get('cls_id','0');
            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }


            $sectionsqry = Events::where('events.id', '>', 0)->where('school_id', $this->school_id)
                ->select('events.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day'))
                ->whereIn('class_ids',$class_ids);
            if($status != ''){
            $sectionsqry->where('status','=',$status);
                }
            $filteredqry = Events::where('events.id', '>', 0)
            ->whereIn('class_ids',$class_ids)
                ->select('events.*',DB::RAW(' DATE_FORMAT(circular_date, "%Y-%m-%d") as circular_day'));
            if($status != ''){
             $filteredqry->where('status','=',$status);
                }

                if($cls_id != ''){
                    $sectionsqry->where('class_ids','=',$cls_id);
                    $filteredqry->where('class_ids','=',$cls_id);
                       }

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

            $totalDataqry = Events::whereIn('class_ids',$class_ids)->orderby('id', 'asc');
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
            return redirect('/teacher/login');
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
            // $approve_status = $request->approve_status;
            $approve_status = 'UNAPPROVED';
            $youtube_link  = $request->youtube_link;

            $validator = Validator::make($request->all(), [
                'class_ids' => 'required',
                'circular_title' => 'required',
                'circular_message' => 'required',
                'circular_date' => 'required',
                'status' => 'required',
                // 'approve_status' => 'required',
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
            $circular->school_id = $this->school_id;
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
                // $total_count;
                // exit;
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
                    if (!in_array($ext, $this->accepted_formats_audio) && !in_array($ext, $this->accepted_formats)) {
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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }

   //Test list
    /* Function: viewTestlist
     */
    public function viewTestlist()
    {
        if (Auth::check()) {
        
        $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
    //    $subject = Subjects::where('id', '>', 0)->get();
            return view('teacher.testlist')->with('class',$classes);
        } else {
            return redirect('/teacher/login');
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
            $class = $request->get('class_id','');
            $subject= $request->get('subject_id','');
            
            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            $subject_ids = array();
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                    $subject_id = $post->subject_id;
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                    array_push($subject_ids,$subject_id);
                }
            }



            $termsqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                ->leftjoin('classes', 'classes.id', 'tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                ->whereIn('tests.class_id',$class_ids)
                ->whereIn('tests.subject_id',$subject_ids)
                ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name');
            $filteredqry = Tests::leftjoin('terms', 'terms.id', 'tests.term_id')
                ->leftjoin('classes', 'classes.id', 'tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'tests.subject_id')
                ->whereIn('tests.class_id',$class_ids)
                ->whereIn('tests.subject_id',$subject_ids)
                 ->select('tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name');

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

            if(!empty($class)){
                $termsqry->where('tests.class_id',$class);
                $filteredqry->where('tests.class_id',$class);
            }
            if(!empty($subject)){
                $termsqry->where('tests.subject_id',$subject);
                $filteredqry->where('tests.subject_id',$subject);
            }

            
            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('tests.id')->count();

            $totalDataqry = Tests::orderby('id', 'asc')->whereIn('tests.class_id',$class_ids)
            ->whereIn('tests.subject_id',$subject_ids);
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
            return redirect('/teacher/login');
        }
    }

    public function addTest(Request $request)
    {
        if (Auth::check()) {
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            
            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }

            $terms = Terms::select('terms.*')->whereIn('class_ids',$class_ids)->get();
            return view('teacher.addtest')->with('classes', $classes)->with('terms',$terms);
        } else {
            return redirect('/teacher/login');
        }
    }


    public function editTest(Request $request){

     
        if (Auth::check()) {
       
              $qb = [];$id = $request->get('id');
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
            return view('teacher.edittest')->with(['qb'=>$qb]);
        } else {
            return redirect('/teacher/login');
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

            if($test_mark < 10){
                return response()->json(['status' => 'FAILED', 'message' => 'Minimun Mark of Test is 10']);
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


    public function editTestList_bfrcombine(Request $request) {
        if (Auth::check()) {


            $input = $request->all();
            $class_id = $request->get('class_id', 0);
            $subject_id = $request->get('subject_id', 0);
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
            $question_item_id = $request->get('question_item_id', []);
            $question_type = $request->get('question_type',[]);
            // if(count($question_type) > 0){
            if(is_array($question_item_id) && count($question_item_id)>0) {
               DB::table('tests')->where('id',$test_id)->update([
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'status'=>'ACTIVE',
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
          
        // }
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

            
            // $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            // $class_ids = array(); 
            // $subject_ids = array();
            // if (!empty($userData)) {
            //     $users = $userData->toArray();
            //     foreach ($users as $k=>$post) {
                   
            //         $class_id = $post->class_id;
            //         $subject_id = $post->subject_id;
            //         array_push($class_ids,$class_id);
            //         array_push($subject_ids,$subject_id);
            //     }
            // }
            
            $termsqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                 ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                    'terms.term_name');
            $filteredqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                 ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                    'terms.term_name');

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

            $totalDataqry = QuestionBanks::orderby('id', 'asc');
           $totalData = $filteredqry->select('id')->count();

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
            return redirect('/teacher/login');
        }
    }

    
    public function viewQbforTest(Request $request)
    {
        $err = $items = '';  $qb = []; $qb_ids = []; $items = [];
        if (Auth::check()) {
            $input = $request->all();
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
            //echo "<pre>";  print_r($items); exit; //print_r($qb);
            return view('teacher.viewqbfrtest')->with(['qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'section_names'=>$section_names]);
        } else {
            return redirect('/teacher/login');
        }
    }


    public function viewQbforTest_bfrcombine(Request $request)
    {
        $err = '';  $qb = [];
        if (Auth::check()) {
            $input = $request->all();
            $qbid = $request->get('qbid', []);
            if(is_array($qbid) && count($qbid)>0) {

                $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
                $class_ids = array(); 
                if (!empty($userData)) {
                    $users = $userData->toArray();
                    foreach ($users as $k=>$post) {
                       
                        $class_id = $post->class_id;
                        array_push($class_ids,$class_id);
                    }
                }


                $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                    ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                    ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                    ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                    ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                        'terms.term_name')
                    ->whereIn('question_banks.id', $qbid)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                }   else {
                    $qb = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('teacher.viewqbfrtest')->with(['qbank'=>$qb, 'err'=>$err]);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function saveQbTest(Request $request) {
        if (Auth::check()) {


            $input = $request->all();
            $from_date = $request->get('from_date','');
            $to_date = $request->get('to_date','');
            $class_id = $request->get('class_id', 0);
            $subject_id = $request->get('subject_id', 0);
            $term_id = $request->get('term_id', 0);
            $chapter_id = $request->get('chapter_id', []);
            $test_mark = $request->get('test_mark','');
            if(is_array($chapter_id)) {
                $chapter_id = array_unique($chapter_id);
                $chapter_ids = implode(',', $chapter_id);
            }   else {
                $chapter_ids = '';
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
           
            $total = round($total_mark);
            if($total != $test_mark){
                return response()->json(['status' => 'FAILED', 'message' => 'The Total Mark of Test  is'.' '.  $total .' but the Given Test Mark is'.' '. $test_mark .' ..!']);
            }
            
            // if(count($question_type)>0){
            
            if(is_array($question_item_id) && count($question_item_id)>0) {
                $test_id = DB::table('tests')->insertGetId([
                    'school_id'=>$this->school_id,
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'test_mark'=>$test_mark,
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
                   
                }
                else{
                    return response()->json(['status' => 'FAILED', 'message' => 'Please Enter the marks for Selected Questions']);
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

    public function saveQbTest_bfrcombine(Request $request) {
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
            $marks = $request->get('marks', []);
            $question_item_id = $request->get('question_item_id', []);
            $question_type = $request->get('question_type',[]);
           
            // if(count($question_type)>0){
            if(is_array($question_item_id) && count($question_item_id)>0) {
             
                $test_id = DB::table('tests')->insertGetId([
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
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
                }
                    else{
                        return response()->json(['status' => 'FAILED', 'message' => 'Please Enter the marks for Selected Questions']);
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
                    'school_id'=>$this->school_id,
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'test_mark'=>$test_mark,
                    'status'=>'ACTIVE',
                    'from_date' => $from_date,
                    'to_date' => $to_date,
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



    public function saveQbAutoTest_bfcombine(Request $request) {
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

        //   echo "question".  $question_item_id = $request->get('question_item_id', []);

            if(is_array($noofquest) && count($noofquest)>0) {
                $test_id = DB::table('tests')->insertGetId([
                    'term_id'=>$term_id,
                    'class_id'=>$class_id,
                    'subject_id'=>$subject_id,
                    'chapter_ids'=>$chapter_ids,
                    'test_name'=>$test_name,
                    'status'=>'ACTIVE',
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'created_by'=>Auth::User()->id,
                    'created_at'=>date('Y-m-d H:i:s')
                ]);

                foreach($noofquest as $q=>$v) {
                    // if(!empty($noofquest[$q]) || $noofquest[$q] != 0 ){
                    // if(!empty($marksperquest[$q]) || $marksperquest[$q] != 0){  
                    $limit = $v;
                    $explode_ques = explode('_',$q);
                    $question_type_id = $explode_ques[0];
                    $question_bank_id = $explode_ques[1];
                    $mark_per_ques = $marksperquest[$q];

     $get_question = DB::table('question_bank_items')->select('question_bank_items.*')->where('question_bank_id',$question_bank_id)->where('question_type_id',$question_type_id)->orderBy(DB::raw('RAND()'))->limit($limit)->get()->toArray();

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
                // }
                // else{
                //     return response()->json(['status' => 'FAILED', 'message' => 'Please Enter the Marks']);
                // }
                // }
                // else{
             
                //     return response()->json(['status' => 'FAILED', 'message' => 'Please Enter the No.of.Questions for Test']);
                // }
                }


            }


            return response()->json(['status' => 'SUCCESS', 'message' => 'Test Saved Successfully']);
        } else {
            return response()->json(['status' => 'FAILED', 'message' => 'Invalid Login Credential']);
        }
    }

    // Preview Test
    /* Function: previewTest
     */
    public function previewTest(Request $request)
    {
        if (Auth::check()) {
            $qb = [];$id = $request->get('id');
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
            return view('teacher.previewtest')->with(['qb'=>$qb]);
        } else {
            return redirect('/teacher/login');
        }
    }

    //Test list
    /* Function: viewTestlistPapers
     */
    public function viewTestlistPapers()
    {
        if (Auth::check()) {
            //$classes = Classes::where('id', '>', 0)->where('status','=','ACTIVE')->orderby('position','asc')->get();
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            $subject = Subjects::where('id', '>', 0)->orderby('position','asc')->get();
            return view('teacher.testlistpapers')->with('class',$classes)->with('subject',$subject)->with('class_id','')->with('subject_id','');
        } else {
            return redirect('/teacher/login');
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

            $userData = DB::table('subject_mapping')
                ->leftjoin('subjects','subjects.id','subject_mapping.subject_id')
                ->leftjoin('classes','classes.id','subject_mapping.class_id')
                ->where('teacher_id',Auth::user()->id)->where('subject_mapping.status','ACTIVE')
                ->where('classes.status','ACTIVE')
                ->where('subjects.status','ACTIVE')->get();
            $class_ids = array(); 
            $subject_ids = array();
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                    $subjectid = $post->subject_id;
                    $classid = $post->class_id;
                    array_push($class_ids,$classid);
                    array_push($subject_ids,$subjectid);
                }
            }
            //echo "<pre>"; print_r($subject_ids); exit;
            /*SubjectMapping::leftjoin('subjects','subjects.id','subject_mapping.subject_id')->where('subject_mapping.teacher_id',Auth::user()->id)->where('subject_mapping.class_id',$request->class_id)->get(["subjects.subject_name", "subjects.id"]);*/
           
            $termsqry = TestPapers::leftjoin('terms', 'terms.id', 'test_papers.term_id')
                ->leftjoin('classes', 'classes.id', 'test_papers.class_id')
                ->leftjoin('subjects', 'subjects.id', 'test_papers.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                /*->whereIn('test_papers.class_id',$class_ids)
                ->whereIn('test_papers.subject_id',$subject_ids)*/
                ->select('test_papers.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name');
            $filteredqry = TestPapers::leftjoin('terms', 'terms.id', 'test_papers.term_id')
                ->leftjoin('classes', 'classes.id', 'test_papers.class_id')
                ->leftjoin('subjects', 'subjects.id', 'test_papers.subject_id')
                //->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                /*->whereIn('test_papers.class_id',$class_ids)
                ->whereIn('test_papers.subject_id',$subject_ids)*/
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

            if(!empty($class_id)){
                $termsqry->where('class_id',$class_id);
                $filteredqry->where('class_id',$class_id);
            } else {
                $termsqry->whereIn('test_papers.class_id',$class_ids);
                $filteredqry->whereIn('test_papers.class_id',$class_ids);
            }
            if(!empty($subject_id)){
                $termsqry->where('subject_id',$subject_id);
                $filteredqry->where('subject_id',$subject_id);
            } else {
                $termsqry->whereIn('test_papers.subject_id',$subject_ids);
                $filteredqry->whereIn('test_papers.subject_id',$subject_ids);
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

            $totalDataqry = TestPapers::orderby('id', 'asc');
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
            return redirect('/teacher/login');
        }
    }

    public function addAutoTestPapers(Request $request)
    {
        if (Auth::check()) {
            //$classes = Classes::where('status', 'ACTIVE')->orderby('position','asc')->get();
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            return view('teacher.autoaddtestpapers')->with('classes', $classes);
        } else {
            return redirect('/teacher/login');
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
            return view('teacher.viewqbfrautotestpapers')->with(['qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'qb_id'=> $qbid]);
        } else {
            return redirect('/teacher/login');
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
            return view('teacher.previewtestpapers')->with(['qb'=>$qb]);
        } else {
            return redirect('/teacher/login');
        }
    }



      //Students Test list
    /* Function: viewStudentsTestlist
     */
    public function viewStudentsTestlist()
    {
        if (Auth::check()) {

            // $student = User::leftjoin('students', 'students.user_id', 'users.id')
            // ->where('users.user_type', 'STUDENT')
            // ->select('users.*')->get();
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            return view('teacher.studenttestlist')->with('class',$classes);
        } else {
            return redirect('/teacher/login');
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
            $class = $request->get('class_id','');
            $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
            $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }


            $termsqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->whereIn('student_tests.class_id',$class_ids)
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');
            $filteredqry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->whereIn('student_tests.class_id',$class_ids)
                ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                    'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');

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

           
             if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $termsqry->whereRaw('student_tests.test_date >= ?', [$mindate]);
                $filteredqry->whereRaw('student_tests.test_date >= ?', [$mindate]);
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '.$maxdate));
                $termsqry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
                $filteredqry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
            }
            
            if($class != '' || $class != 0){
                $termsqry->where('student_tests.class_id',$class);
                $filteredqry->where('student_tests.class_id',$class);
             }
            

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'student_tests.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $terms = $termsqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('student_tests.id')->count();

            $totalDataqry = StudentTests::orderby('id', 'asc')->whereIn('student_tests.class_id',$class_ids);
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
            return redirect('/teacher/login');
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
            $class = $request->get('class_id','');
            $section_id = $request->get('section_id','');
            $student_id = $request->get('student_id','');
           $mindate = isset($input['minDateFilter']) ? $input['minDateFilter'] : '';
           $maxdate = isset($input['maxDateFilter']) ? $input['maxDateFilter'] : '';
           $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
           $class_ids = array(); 
           if (!empty($userData)) {
               $users = $userData->toArray();
               foreach ($users as $k=>$post) {
                  
                   $class_id = $post->class_id;
                   array_push($class_ids,$class_id);
               }
           }


           $users_qry = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
               ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
               ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
               ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
               ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
               ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
               ->whereIn('student_tests.class_id',$class_ids)
               ->select('student_tests.*', 'classes.class_name', 'subjects.subject_name',
                   'terms.term_name', 'users.name as student_name', 'students.admission_no', 'tests.test_name');
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

           if(!empty(trim($mindate))) {
                $mindate = date('Y-m-d', strtotime($mindate));
                $users_qry->whereRaw('student_tests.test_date >= ?', [$mindate]);
              
    
            }
            if(!empty(trim($maxdate))) {
                $maxdate = date('Y-m-d', strtotime('+1 day '.$maxdate));
                $users_qry->whereRaw('student_tests.test_date <= ?', [$maxdate]);
              
            }
            
            if($class != '' || $class != 0){
                $users_qry->where('student_tests.class_id',$class);
         
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
            return redirect('/teacher/login');
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

                $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
                $class_ids = array(); 
                if (!empty($userData)) {
                    $users = $userData->toArray();
                    foreach ($users as $k=>$post) {
                       
                        $class_id = $post->class_id;
                        array_push($class_ids,$class_id);
                    }
                }

                
                $qb = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                        ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                        ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                        ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                        ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                        ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                        ->whereIn('student_tests.class_id',$class_ids)
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
            return view('teacher.previewstudentstest')->with(['qb'=>$qb, 'id'=>$id]);
        } else {
            return redirect('/teacher/login');
        }
    }


    public function editStudentsTest(Request $request)
    {
        if (Auth::check()) {
            $qb = []; $id = $request->get('id');
            if($id> 0) {

                $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
                $class_ids = array(); 
                if (!empty($userData)) {
                    $users = $userData->toArray();
                    foreach ($users as $k=>$post) {
                       
                        $class_id = $post->class_id;
                        array_push($class_ids,$class_id);
                    }
                }

                
                $qb = StudentTests::leftjoin('users', 'users.id', 'student_tests.user_id')
                        ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                        ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                        ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                        ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                        ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                        ->whereIn('student_tests.class_id',$class_ids)
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
            return view('teacher.editstudenttestlist')->with(['qb'=>$qb, 'id'=>$id]);
        } else {
            return redirect('/teacher/login');
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
            // $classes = Classes::where('status', 'ACTIVE')->get();
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();
            
            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }

            $terms = Terms::select('terms.*')->whereIn('class_ids',$class_ids)->get();
            return view('teacher.autoaddtest')->with('classes', $classes)->with('terms',$terms);
        } else {
            return redirect('/teacher/login');
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
            return view('teacher.viewqbfrautotest')->with(['qbank'=>$qb, 'err'=>$err, 'items'=>$items, 'qb_id'=> $qbid]);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function viewQbforAutoTest_bfrcombine(Request $request)
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
                    ->whereIn('question_banks.id', $qbid)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();
                }   else {
                    $qb = [];
                }
            }   else {
                $err = 'Please select the Question Banks for the Test';
            }
            //echo "<pre>"; print_r($qb); exit;
            return view('teacher.viewqbfrautotest')->with(['qbank'=>$qb, 'err'=>$err]);
        } else {
            return redirect('/teacher/login');
        }
    }


     //Question Banks
    /* Function: viewQuestionbank
     */
    public function viewQuestionbank()
    {
        if (Auth::check()) {
            return view('teacher.questionbank');
        } else {
            return redirect('/teacher/login');
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

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            $subject_ids = array();
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    $subject_id = $post->subject_id;
                    array_push($class_ids,$class_id);
                    array_push($subject_ids,$subject_id);
                }
            }

            $termsqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->whereIn('question_banks.subject_id',$subject_ids)
                ->whereIn('question_banks.class_id',$class_ids)
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                    'terms.term_name');
            $filteredqry = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->whereIn('question_banks.subject_id',$subject_ids)
                ->whereIn('question_banks.class_id',$class_ids)
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername',
                    'terms.term_name');
                    

            // if($class_id>0) {
            //     $termsqry->where('question_banks.class_id', $class_id);
            // }
            // if($subject_id>0) {
            //     $termsqry->where('question_banks.subject_id', $subject_id);
            // }
            // if($term_id>0) {
            //     $termsqry->where('question_banks.term_id', $term_id);
            // }

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

            $totalDataqry = QuestionBanks::orderby('id', 'asc')
            ->whereIn('question_banks.subject_id',$subject_ids)
            ->whereIn('question_banks.class_id',$class_ids);
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
            return redirect('/teacher/login');
        }
    }

    public function addQuestionbank(Request $request)
    {
        if (Auth::check()) {
          
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();            
            $question_types = QuestionTypes::with('questiontype_settings')
                ->where('status','ACTIVE')->orderby('position', 'Asc')->get();
            if($question_types->isNotEmpty()) {
                $question_types = $question_types->toArray();
            }

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }

            // $terms = Terms::select('terms.*')->whereIn('class_ids',$class_ids)->get();
            //echo "<pre>"; print_r($question_types); exit;
            return view('teacher.addquestionbank')->with('classes', $classes)->with('question_types', $question_types);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function editQuestionbank(Request $request)
    {
        if (Auth::check()) {   $id = $request->get('id');
            $classes = SubjectMapping::leftjoin('classes','classes.id','subject_mapping.class_id')->select('classes.id','classes.class_name')->where('classes.status','ACTIVE')->where('subject_mapping.teacher_id',Auth::user()->id)->orderby('classes.id','asc')->groupby('subject_mapping.class_id')->get();  
            $question_types = QuestionTypes::with('questiontype_settings')
                ->where('status','ACTIVE')->orderby('position', 'Asc')->get();
            if($question_types->isNotEmpty()) {
                $question_types = $question_types->toArray();
            }

            $userData = DB::table('subject_mapping')->where('teacher_id',Auth::user()->id)->get();
            $class_ids = array(); 
            if (!empty($userData)) {
                $users = $userData->toArray();
                foreach ($users as $k=>$post) {
                   
                    $class_id = $post->class_id;
                    array_push($class_ids,$class_id);
                }
            }

            $terms = Terms::select('terms.*')->whereIn('class_ids',$class_ids)->get();
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
            return view('teacher.editquestionbank')->with('classes', $classes)->with('terms',$terms)
                ->with('question_types', $question_types)->with('qb', $qb)->with('id', $id);
        } else {
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
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
                    foreach($question_types as $qtype) {
                        if(isset($qtype['questiontype_settings'])) {
                            $html = view('teacher.loadquestiontype')->with('qtype', $qtype)->with('i', $i)->render();
                            return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Clone Detail']);
                        }   else {
                            $html = view('teacher.loadquestiontype')->with('qtype', $qtype)->with('i', $i)->render();
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
    // public function postQuestionbank(Request $request)
    // {
    //     if (Auth::check()) {
    //         $id = $request->id;
    //         $class_id = $request->class_id;
    //         $subject_id = $request->subject_id;
    //         $chapter_id = $request->chapter_id;
    //         $term_id = $request->term_id;
    //         $notes = $request->file('notes');
    //         $qb_notes = $request->get('notes_file'); 
    //         if (!empty($notes)) {

    //             $notes = rand() . time() . '.' . $notes->getClientOriginalExtension();

    //             $request->notes->move(public_path('/image/notes'), $notes);

    //             }
    //             else{
    //                 $notes = $qb_notes;
    //               }
         
          
    //         $validator = Validator::make($request->all(), [
    //             'class_id' => 'required',
    //             'subject_id' => 'required',
    //             'chapter_id' => 'required',
    //             'term_id' => 'required',
    //             'question' => 'required'
    //         ]);

    //         if ($validator->fails()) {

    //             $msg = $validator->errors()->all();

    //             return response()->json([

    //                 'status' => "FAILED",
    //                 'message' => "Please check inputs " . implode(', ', $msg),
    //             ]);
    //         }
            
    //         $input = $request->all();  
    //         //  echo "<pre>"; print_r($input); exit;
    //         $question_bank_id = $request->get('question_bank_id', 0);
    //         $qb_item_id = $request->get('qb_item_id', []);
    //         $question = $request->get('question', []);
    //         $answer = $request->get('answer', []);
    //         $display_answer = $request->get('display_answer', []);
    //         $option_1 = $request->get('option_1', []);
    //         $option_2 = $request->get('option_2', []);
    //         $option_3 = $request->get('option_3', []);
    //         $option_4 = $request->get('option_4', []);
    //         $choose_1 = $request->file('choose_1', []);
    //         $choose_2 = $request->file('choose_2', []);
    //         $choose_3 = $request->file('choose_3', []);
    //         $choose_4 = $request->file('choose_4', []);
    //         $oquestion_type = $request->get('oquestion_type', []);
    //         $oquestion = $request->get('oquestion', []);
    //         $oanswer = $request->get('oanswer', []);
          
    //         $question_file = $request->file('question_file');  //echo "<pre>"; print_r($question_file); exit;

    //         $qb_data = ['term_id' => $term_id, 'class_id' => $class_id, 'subject_id' => $subject_id, 'chapter_id' => $chapter_id,'notes' => $notes, 'status' => 'ACTIVE'];
    //         if($question_bank_id > 0) {
    //             $qb_data['updated_at'] = date('Y-m-d H:i:s');
    //             $qb_data['updated_by'] = Auth::User()->id;

    //             DB::table('question_banks')->where('id', $question_bank_id)->update($qb_data);
    //         }   else {
    //             $qb_data['created_at'] = date('Y-m-d H:i:s');
    //             $qb_data['created_by'] = Auth::User()->id;

    //             $question_bank_id = DB::table('question_banks')->insertGetId($qb_data);
    //         }


    //         if(is_array($question) && count($question)> 0) {
    //             foreach($question as $qtype=>$qtn) {
    //                 if(is_array($qtn) && count($qtn)> 0) {
    //                     foreach($qtn as $kq=>$quest) {
    //                         // echo $qtype;
    //                         // echo $kq;
    //                         // echo $quest;
    //                         // exit;
    //                         if(!empty($quest))  {
                               
    //                         //    if(!empty($question[$qtype][$kq])){
    //                         //     if(!empty($answer[$qtype][$kq])){
                             
    //                             $row = [];
    //                             $row['question_type'] = $qtype;
    //                             $row['question'] = $quest;
    //                             //$row['answer'] = $answer[$qtype][$kq];
    //                             if(isset($answer[$qtype]) && isset($answer[$qtype][$kq]) && !empty($answer[$qtype][$kq])) {
    //                                 $row['answer'] = $answer[$qtype][$kq];
    //                             }   else {
    //                                 $arr = [8,9,10,11];
    //                                 // Missing letters, jumbled words, jumbled letters, Dictation
    //                                 if(in_array($qtype, $arr)) {
    //                                     $row['answer'] = $quest;
    //                                 }   else {
    //                                     $row['answer'] = '';
    //                                 }
    //                             }
    //                             if($qtype != 16){
    //                             if(isset($option_1[$qtype]) && isset($option_1[$qtype][$kq]) && !empty($option_1[$qtype][$kq])) {
    //                                 $row['option_1'] = $option_1[$qtype][$kq];
    //                             }   else {
    //                                 $row['option_1'] = '';
    //                             }
    //                             if(isset($option_2[$qtype]) && isset($option_2[$qtype][$kq]) && !empty($option_2[$qtype][$kq])) {
    //                                 $row['option_2'] = $option_2[$qtype][$kq];
    //                             }   else {
    //                                 $row['option_2'] = '';
    //                             }
    //                             if(isset($option_3[$qtype]) && isset($option_3[$qtype][$kq]) && !empty($option_3[$qtype][$kq])) {
    //                                 $row['option_3'] = $option_3[$qtype][$kq];
    //                             }   else {
    //                                 $row['option_3'] = '';
    //                             }
    //                             if(isset($option_4[$qtype]) && isset($option_4[$qtype][$kq]) && !empty($option_4[$qtype][$kq])) {
    //                                 $row['option_4'] = $option_4[$qtype][$kq];
    //                             }   else {
    //                                 $row['option_4'] = '';
    //                             }

    //                         }
    //                         else if($qtype == 16){

    //                             if($question_bank_id > 0) {
    //                                 $qb_data['updated_at'] = date('Y-m-d H:i:s');
    //                                 $qb_data['updated_by'] = Auth::User()->id;
                    
    //                                 DB::table('question_banks')->where('id', $question_bank_id)->update($qb_data);
    //                             }   else {
    //                                 $qb_data['created_at'] = date('Y-m-d H:i:s');
    //                                 $qb_data['created_by'] = Auth::User()->id;
                    
    //                                 $question_bank_id = DB::table('question_banks')->insertGetId($qb_data);
    //                             }
                           
    //                             if(isset($choose_1[$qtype]) && isset($choose_1[$qtype][$kq]) && !empty($choose_1[$qtype][$kq])) {
    //                             $countryimg = rand() . time() . '.' . $choose_1[$qtype][$kq]->getClientOriginalExtension();
                        
    //                             $destinationPath = public_path('/image/questionbank');
                        
    //                             $choose_1[$qtype][$kq]->move($destinationPath, $countryimg);
                        
    //                               $row['option_1']= $countryimg;
    //                              }  
                               

                                
    //                             if(isset($choose_2[$qtype]) && isset($choose_2[$qtype][$kq]) && !empty($choose_2[$qtype][$kq])) {
                                    
    //                            $countryimg = rand() . time() . '.' . $choose_2[$qtype][$kq]->getClientOriginalExtension();
                        
    //                            $destinationPath = public_path('/image/questionbank');
                       
    //                            $choose_2[$qtype][$kq]->move($destinationPath, $countryimg);
                       
    //                             $row['option_2']= $countryimg;
    //                             } 
    //                             if(isset($choose_3[$qtype]) && isset($choose_3[$qtype][$kq]) && !empty($choose_3[$qtype][$kq])) {
    //                                 $countryimg = rand() . time() . '.' . $choose_3[$qtype][$kq]->getClientOriginalExtension();
                        
    //                                 $destinationPath = public_path('/image/questionbank');
                            
    //                                 $choose_3[$qtype][$kq]->move($destinationPath, $countryimg);
                            
    //                                 $row['option_3']= $countryimg;
    //                             }   
    //                             if(isset($choose_4[$qtype]) && isset($choose_4[$qtype][$kq]) && !empty($choose_4[$qtype][$kq])) {
    //                                 $countryimg = rand() . time() . '.' . $choose_4[$qtype][$kq]->getClientOriginalExtension();
                        
    //                                 $destinationPath = public_path('/image/questionbank');
                            
    //                                 $choose_4[$qtype][$kq]->move($destinationPath, $countryimg);
    //                                 $row['option_4']= $countryimg;
    //                             } 
    //                         }
                            
    //                             if(isset($display_answer[$qtype]) && isset($display_answer[$qtype][$kq]) && !empty($display_answer[$qtype][$kq])) {
    //                                 // $row['display_answer'] = $display_answer[$qtype][$kq];
    //                                 $row['display_answer'] = $answer[$qtype][$kq];
    //                             }   else {
    //                                 $row['display_answer'] = '';
    //                             }

    //                             $row['question_bank_id'] = $question_bank_id;
    //                             $row['question_type_id'] = $qtype;
    //                             $row['question_type'] = DB::table('question_types')->where('id', $qtype)->value('question_type');

    //                             if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
    //                                 $row['updated_by'] = Auth::User()->id;
    //                                 $row['updated_at'] = date('Y-m-d H:i:s');

    //                                 DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
    //                             }   else {
    //                                 $row['created_by'] = Auth::User()->id;
    //                                 $row['created_at'] = date('Y-m-d H:i:s');

    //                                 DB::table('question_bank_items')->insert($row);
    //                             }

                           
    //                             // return response()->json(['status' => "SUCCESS", 'message' => 'Saved Successfully']);
    //                     //     }
                       
    //                     // else{
    //                     //     return response()->json(['status' => "FAILED", 'message' => 'Answers are must be not Empty']);

    //                     // }
    //                     //     }
    //                     //     else{
    //                     //         return response()->json(['status' => "FAILED", 'message' => 'Questions are must be not Empty']);
       
    //                     //        }
                            
    //                     }
                        
    //                 //         else {
                    
    //                 //         }
    //                     }
    //                 }
    //             }
    //         }

    //         if(is_array($question_file) && count($question_file)> 0) {
    //             foreach($question_file as $qtype=>$qtn) {
    //                 if(is_array($qtn) && count($qtn)> 0) {
    //                     foreach($qtn as $kq=>$quest) {
    //                         if(!empty($quest)) {
    //                         //  if(!empty($question[$qtype][$kq])){
    //                         //       if(!empty($answer[$qtype][$kq])){
                              
    //                             if (!empty($quest)) {
    //                                 $ext = $quest->getClientOriginalExtension();
    //                                 if (!in_array($ext, $this->accepted_formats_qbt)) {
    //                                     return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg,doc,docx,mp3,mp4,pdf']);
    //                                 }

    //                                 $topicimg = rand() . time() . '.' . $quest->getClientOriginalExtension();

    //                                 $destinationPath = public_path('/image/qb');

    //                                 $quest->move($destinationPath, $topicimg);

    //                                 $row = [];
    //                                 $row['question_type'] = $qtype;
    //                                 $row['question_file'] = $topicimg;
    //                                 $row['answer'] = !empty($answer[$qtype][$kq]) ? $answer[$qtype][$kq] : '';

    //                                 $row['question_bank_id'] = $question_bank_id;
    //                                 $row['question_type_id'] = $qtype;
    //                                 $row['question_type'] = DB::table('question_types')->where('id', $qtype)->value('question_type');

    //                                 if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
    //                                     $row['updated_by'] = Auth::User()->id;
    //                                     $row['updated_at'] = date('Y-m-d H:i:s');

    //                                     DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
    //                                 }   else {
    //                                     $row['created_by'] = Auth::User()->id;
    //                                     $row['created_at'] = date('Y-m-d H:i:s');

    //                                     DB::table('question_bank_items')->insert($row);
    //                                 }
    //                             }
    //                             // return response()->json(['status' => "SUCCESS", 'message' => 'Saved Successfully']);
    //                         // }
                       
    //                         // else{
    //                         //     return response()->json(['status' => "FAILED", 'message' => 'Answers are must be not Empty']);
    
    //                         // }
    //                         // }

    //                         // else {
    //                         //     return response()->json(['status' => "FAILED", 'message' => 'Questions are must be not Empty']);
    //                         //             }
                           
    //                     }
                           
    //                     }
    //                 }
    //             }
    //         }

    //         if(is_array($oquestion_type) && count($oquestion_type)> 0) {
    //             foreach($oquestion_type as $qtype=>$qtn) {
    //                 if(is_array($qtn) && count($qtn)> 0) {
    //                     foreach($qtn as $kq=>$quest) {
    //                         if(!empty($quest)) {
    //                         //   if(!empty($question[$qtype][$kq])){
    //                         //     if(!empty($answer[$qtype][$kq])){
    //                             $row = [];
    //                             $row['question_type'] = $quest;
    //                             if(isset($oquestion[$qtype]) && isset($oquestion[$qtype][$kq]) && !empty($oquestion[$qtype][$kq])) {
    //                                 $row['question'] = $oquestion[$qtype][$kq];
    //                             }   else {
    //                                 $row['question'] = '';
    //                             }
    //                             if(isset($oanswer[$qtype]) && isset($oanswer[$qtype][$kq]) && !empty($oanswer[$qtype][$kq])) {
    //                                 $row['answer'] = $oanswer[$qtype][$kq];
    //                             }   else {
    //                                 $row['answer'] = '';
    //                             }

    //                             $row['question_bank_id'] = $question_bank_id;
    //                             $row['question_type_id'] = 0;

    //                             if(isset($qb_item_id[$qtype]) && isset($qb_item_id[$qtype][$kq]) && ($qb_item_id[$qtype][$kq]>0)){
    //                                 $row['updated_by'] = Auth::User()->id;
    //                                 $row['updated_at'] = date('Y-m-d H:i:s');

    //                                 DB::table('question_bank_items')->where('id', $qb_item_id[$qtype][$kq])->update($row);
    //                             }   else {
    //                                 $row['created_by'] = Auth::User()->id;
    //                                 $row['created_at'] = date('Y-m-d H:i:s');

    //                                 DB::table('question_bank_items')->insert($row);
    //                             }
    //                             // return response()->json(['status' => "SUCCESS", 'message' => 'Saved Successfully']);
    //                         // }
                       
    //                         // else{
    //                         //     return response()->json(['status' => "FAILED", 'message' => 'Answers are must be not Empty']);
    
    //                         // }
    //                         // }
    //                         // else {
    //                         //     return response()->json(['status' => "FAILED", 'message' => 'Questions are must be not Empty']);
    //                         //             }

                          
    //                     }
                           
    //                     }
    //                 }
    //             }
    //         }
            
    //         //echo "<pre>"; print_r($input);
    //         // return response()->json(['status' => "SUCCESS", 'message' => 'Saved Successfully']);
           
    //     } else {
    //         return redirect('/teacher/login');
    //     }
    // }

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
            //   echo "<pre>"; print_r($input); exit;
            $question_bank_id = $request->get('question_bank_id', 0);
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
            $oquestion_type = $request->get('oquestion_type', []);
          //    print_r($choose_question1);
          //    print_r($choose_question);
          //    exit;
            $accepted_formats = ['jpeg', 'jpg', 'png'];
            $oquestion = $request->get('oquestion', []);
            $oanswer = $request->get('oanswer', []);

            $question_file = $request->file('question_file');  //echo "<pre>"; print_r($question_file); exit;

            $qb_data = ['term_id' => $term_id, 'class_id' => $class_id, 'subject_id' => $subject_id, 'chapter_id' => $chapter_id,'qb_name'=>$qb_name,'notes' => $notes,'status' => 'ACTIVE'];
            if($question_bank_id > 0) {
                $qb_data['updated_at'] = date('Y-m-d H:i:s');
                $qb_data['updated_by'] = Auth::User()->id;

                DB::table('question_banks')->where('id', $question_bank_id)->update($qb_data);
            }   else {
                $qb_data['created_at'] = date('Y-m-d H:i:s');
                $qb_data['created_by'] = Auth::User()->id;

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
            return redirect('/teacher/login');
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
            return view('teacher.previewquestionbank')->with(['qb'=>$qb]);
        } else {
            return redirect('/teacher/login');
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
                    ->whereIn('question_banks.id', $checkedqb)->get();

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
                    header("Content-Disposition: attachment; filename=\"qb.xls\"");
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


    public function viewStudentDailyAttendance(Request $request)   {
        if(Auth::check()){
            $monthyear = $class_id = $section_id = '';
            $lastdate = date('t', strtotime(date('Y-m')));
            $teacher_id = Auth::user()->id;
            $user_get =  DB::table('class_teachers')->where('teacher_id', $teacher_id)->first();
            if(!empty($user_get)){
            $class_id = $user_get->class_id;
            $section_id = $user_get->section_id;
         }
         else{
             $class_id = 0; $section_id = 0;
         }
            $classes = Classes::where('status', 'ACTIVE')->where('id',$class_id)->orderby('position', 'Asc')->get();
            $students  = '';
            $new_date = date('Y-m-d');
            $monthyear = date('Y-m');
           
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
        return view('teacher.students_dailyattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate, 'students'=>$students,'new_date' => $new_date,'attendance_chk' =>0,'attendance_chk2'=>0])->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays);
        }else{
            return redirect('/login');
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
         $students = Student::leftjoin('users','users.id','students.user_id')->where('students.class_id', $class_id)->where('students.section_id',$section_id)->where('users.status','=','ACTIVE')->get();
                foreach($students as $key=>$value) {
               $date = 'day_'.$day;
             
               if(isset($fn_section)){
                
                  if(in_array($value->id,$fn_section)){
                
                    $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();

                       
                        //  exit;
                            if(!empty($ex)) {
                                $mode = 1;
                                $date = 'day_'.$day;
                                DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                                    ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                                    ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);

                                    DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->update(['fn_status'=>$mode, 'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                                    ]);
    
                            }   else {
                                $mode = 1;
                               $date = 'day_'.$day;
                                DB::table('studentsdaily_attendance')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);

                                DB::table('attendance_approval')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'date'=>$new_date, 'fn_status'=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);

                            }
                             
                            (new AdminController())->updateAttendanceLeave($new_date, $value->id, 'HALF MORNING','CANCELLED'); 
                        }
                        else{
                            $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();

                         
                         //    exit;
                             if(!empty($ex)) {
                                $mode = 2;
                                 $date = 'day_'.$day;
                                 DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                                     ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                                     ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);
                                     
                                    DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->update(['fn_status'=>$mode, 'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                                ]);
                             }   else {
                                $mode = 2;
                                 $date = 'day_'.$day;
                                 DB::table('studentsdaily_attendance')->insert([
                                     'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                     'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                     'created_by'=>Auth::User()->id
                                 ]);

                                 DB::table('attendance_approval')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'date'=>$new_date, 'fn_status'=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);
                             }

                            (new AdminController())->updateAttendanceLeave($new_date, $value->id, 'HALF MORNING','PENDING'); 
                         }
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

                                        
                                    DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->update(['an_status'=>$mode, 'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                                ]);

                            }   else {
                                $mode = 1;
                                $date = 'day_'.$day.'_an';
                                DB::table('studentsdaily_attendance')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);

                                DB::table('attendance_approval')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'date'=>$new_date, 'an_status'=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);

                            }

                            (new AdminController())->updateAttendanceLeave($new_date, $value->id, 'HALF AFTERNOON','CANCELLED'); 
                        }
                        else{
                            $ex = DB::table('studentsdaily_attendance')->where('user_id', $value->id)->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)->first();
                           if(!empty($ex)) {
                                $mode = 2;
                                $date = 'day_'.$day.'_an';
                                DB::table('studentsdaily_attendance')->where('user_id', $value->id)
                                    ->where('monthyear', $monthyear)->where('class_id', $class_id)->where('section_id', $section_id)
                                    ->update([$date=>$mode, 'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>Auth::User()->id]);

                                        
                                    DB::table('attendance_approval')->where('class_id',$class_id)->where('section_id',$section_id)->where('user_id',$value->id)->where('date',$new_date)->update(['an_status'=>$mode, 'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>Auth::User()->id
                                ]);

                            }   else {
                                $mode = 2;
                                $date = 'day_'.$day.'_an';
                                DB::table('studentsdaily_attendance')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'monthyear'=>$monthyear, $date=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);

                                DB::table('attendance_approval')->insert([
                                    'user_id'=>$value->id, 'class_id'=>$class_id, 'section_id'=>$section_id,
                                    'date'=>$new_date, 'an_status'=>$mode, 'created_at'=>date('Y-m-d H:i:s'),
                                    'created_by'=>Auth::User()->id
                                ]);
                            }
                            (new AdminController())->updateAttendanceLeave($new_date, $value->id, 'HALF AFTERNOON','PENDING'); 
                        }
                    }
                       
                        
                }
               
                return response()->json(['status' => 'SUCCESS', 'message' => 'Attendance saved successfully']);
            
        // }   else {
        //     return response()->json(['status' => 'FAILED', 'message' => 'Invalid Class and Section']);
        // }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Attendance saved successfully'], 201);

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
            
            $orderdate = explode('-', $new_date);
            $o_year = $orderdate[0];
            $o_month   = $orderdate[1];
            $fin_month = $o_year.'-'.$o_month;
            $users = User::leftjoin('students', 'students.user_id', 'users.id')
            //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
            ->where('user_type', 'STUDENT')
            ->where('users.status','ACTIVE')
            // ->whereRaw("'".$check_month."' BETWEEN from_month and to_month")
            ->where('students.class_id', $class_id)
            ->where('students.section_id', $section_id)
            ->select('students.*','users.id', 'name', 'email', 'mobile','profile_image', 'students.class_id', 'students.section_id', 'students.admission_no')
            ->get();


            // $users = DB::select("select student_class_mappings.*, `users`.`id`, `name`, `email`, `mobile`, `students`.`class_id`, `students`.`section_id`, `students`.`admission_no` from `student_class_mappings` left join `users` on `student_class_mappings`.`user_id` = `users`.`id` left join `students` on `students`.`user_id` = `users`.`id` where `users `.`user_type` = 'STUDENT' and `student_class_mappings`.`class_id` = '".$class_id."' and `student_class_mappings`.`section_id` = '".$section_id."'");

            if(!empty($users)) {
                foreach($users as $user) {
                    $userids[] = $user->user_id;
                }
                $userids = array_unique($userids);
                list($year, $month) = explode('-', $monthyear);
                 $students = User::with('dailyattendance')
                    ->leftjoin('students', 'students.user_id', 'users.id')
                    //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                    ->where('user_type', 'STUDENT')
                    ->where('users.status','ACTIVE')
                    ->whereIn('users.id', $userids)
                    //->whereRaw("'".$fin_month."' BETWEEN from_month and to_month")
                    /* ->where('student_class_mappings.class_id', $class_id)
                    ->where('student_class_mappings.section_id', $section_id)*/
                    ->select('users.id', 'name', 'email', 'mobile','profile_image', 'students.class_id', 'students.section_id', 'students.admission_no')
                    ->get();
                     $date = 'day_'.$day;
                    $fn_chk = StudentsDailyAttendance::whereIn('user_id', $userids)->where($date,1)->where('monthyear', $monthyear)->select('id')->get()->count();
                    $date2 = 'day_'.$day.'_an';
                    $an_chk = StudentsDailyAttendance::whereIn('user_id', $userids)->where($date2,1)->where('monthyear', $monthyear)->select('id')->get()->count();
                   
                   list($year, $month) = explode('-', $monthyear);
                   $sundays = CommonController::getSundays($year, $month); 
                   $saturdays = CommonController::getSaturdays($year, $month); 
                  $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                        ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                        ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
                        if($holidays->isNotEmpty()){
                            $holidays = $holidays->toArray();
                        }

                if($students->isNotEmpty()) {
                    $students = $students->toArray();
                //   echo "<pre>";print_r($students);exit;
                    $html = view('teacher.loadstudentsdailyattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                    'section_id'=>$section_id, 'students'=>$students, 'lastdate'=>$lastdate])->with('fn_chk',$fn_chk)->with('an_chk',$an_chk)->with('new_date',$new_date)->with('sundays',$sundays)->with('saturdays',$saturdays)->with('holidays',$holidays)->render();

                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students attendance Detail']);

                }   else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
                }
            }   else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
            }


            return view('teacher.studentsdailyattendance')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                'section_id'=>$section_id, 'classes'=>$classes, 'lastdate'=>$lastdate]);
        }else{
            return redirect('/login');
        }
    }


    public function viewStudentAttenReport(){
        if(Auth::check()){
        $monthyear = $class_id = $section_id = '';
        $lastdate = date('t', strtotime(date('Y-m')));
        $teacher_id = Auth::user()->id;
        $user_get =  DB::table('class_teachers')->where('teacher_id', $teacher_id)->first();
        if(!empty($user_get)){
        $class_id = $user_get->class_id;
     }
     else{
         $class_id = 0;
     }
        $classes = Classes::where('status', 'ACTIVE')->where('id',$class_id)->orderby('position', 'Asc')->get();
        $students  = '';
        $new_date = date('Y-m-d');
        $monthyear = date('Y-m');
       
        list($year, $month) = explode('-', $monthyear);
    $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
    ->select(DB::RAW(' DATE_FORMAT(holiday_date, "%d") as holiday'))->get();
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
    return view('teacher.studentattenreport')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
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
            
            $users = DB::select("select students.*, `users`.`id`, `name`, `email`, `mobile`, `students`.`class_id`, `students`.`section_id`, `students`.`admission_no` from `students` left join `users` on `students`.`user_id` = `users`.`id`   where `user_type` = 'STUDENT' and `students`.`class_id` = '".$class_id."' and `students`.`section_id` = '".$section_id."'");
            $userids = []; $students = '';
            if(!empty($users)) {
                
                foreach($users as $user) {
                    $userids[] = $user->user_id;
                }
                $userids = array_unique($userids);
                list($year, $month) = explode('-', $monthyear);
                $students = User::with('dailyattendance')
                    ->leftjoin('students', 'students.user_id', 'users.id')
                    //->leftjoin('student_class_mappings', 'student_class_mappings.user_id', 'users.id')
                    ->where('user_type', 'STUDENT')
                    ->where('users.status','ACTIVE')
                    ->whereIn('users.id', $userids)
                    // ->where('academic_year', $year)
                    //->whereRaw("'".$fin_month."' BETWEEN from_month and to_month")
                    /* ->where('student_class_mappings.class_id', $class_id)
                    ->where('student_class_mappings.section_id', $section_id)*/
                    ->select('users.id', 'name', 'email', 'mobile', 'students.class_id', 'students.section_id', 'students.admission_no','users.profile_image')
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
                       }
                if($students->isNotEmpty()) {
                    $students = $students->toArray();
                //   echo "<pre>";print_r($students);exit;
                    $html = view('teacher.loadstudentsattendancerep')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
                    'section_id'=>$section_id, 'students'=>$students, 'lastdate'=>$lastdate,'saturdays'=>$saturdays,'sundays'=>$sundays])->render();

                    return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Students attendance Detail']);

                }   else {
                    return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
                }
            }  
             else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Students attendance Detail']);
            }


            return view('teacher.studentattenreport')->with(['monthyear'=>$monthyear, 'class_id'=>$class_id,
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


    // COMMUNICATION
    //view Categories
    public function viewCategories()
    {
        if (Auth::check()) {

            return view('teacher.categories');
        } else {
            return redirect('/teacher/login');
        }
    }

    public function getCategories(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->school_college_id;
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = Category::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $mclass = Category::where('school_id', $school_id)->get();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/teacher/login');
        }

    }

    public function postCategories(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name; 
            $position = $request->position;
            $status = $request->status; 
            $school_id = Auth::User()->school_college_id;

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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }

    //view Background Themes
    public function viewBackgroundThemes()
    {
        if (Auth::check()) {

            return view('teacher.background_themes');
        } else {
            return redirect('/teacher/login');
        }
    }

    public function getBackgroundThemes(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->school_college_id;
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = BackgroundTheme::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $mclass = BackgroundTheme::where('school_id', $school_id)->get();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/teacher/login');
        }

    }

    public function postBackgroundThemes(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $name = $request->name; 
            $position = $request->position;
            $status = $request->status; 
            $school_id = Auth::User()->school_college_id;

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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }  

    //view Group
    public function viewGroup()
    {
        if (Auth::check()) {
            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->school_college_id)
                    ->get(); 
            return view('teacher.group')->with('get_student',$get_student);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function getGroup(Request $request)
    {

        if (Auth::check()) {
            $school_id = Auth::User()->school_college_id;
            $status = $request->get('status',0);
           if($status != ''){
            $mclass = CommunicationGroup::where('status','=',$status)->where('school_id', $school_id)->get();
           }else{
            $mclass = CommunicationGroup::where('school_id', $school_id)->get();
           }


            return Datatables::of($mclass)->make(true);
        } else {
            return redirect('/teacher/login');
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
            $school_id = Auth::User()->school_college_id;

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
            return redirect('/teacher/login');
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
            return redirect('/teacher/login');
        }
    }  

    // Communication posts 
    public function viewPosts(Request $request)  {
        if (Auth::check()) { 
            $limit = 50;  $page_no = 0;  $school_id = Auth::User()->school_college_id;
            $categories = Category::where('status', 'ACTIVE')->where('school_id', $school_id)->orderby('position', 'asc')->get(); 
            $posts = CommunicationPost::where('delete_status', 0)
                            ->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id)
            ->orderby('id', 'desc')
            ->paginate($limit, ['communication_posts.*'], 'page', $page_no);
 
            return view('teacher.posts')->with('categories', $categories)->with('posts', $posts);
        } else {
            return redirect('/teacher/login');
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

            $school_id = Auth::User()->school_college_id;

            $limit = 50;
            $filter_pagename = trim($filter_pagename);
            if(!empty($filter_pagename)) {
                switch ($filter_pagename) {
                    case 'communcation_posts':  
                        $page_no = $filter_page;  
                        $posts = CommunicationPost::where('delete_status', 0)
                            ->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id);

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
                        $content =  view('teacher.posts_list')->with('posts',$posts)->render(); 

                        return response()->json(['status'=>1, 'message' => 'Posts list','data'=>$content]);


                    break;

                    case 'communcation_postsms':  
                        $page_no = $filter_page;  
                        $posts = CommunicationSms::where('delete_status', 0)
                            ->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id);

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
                        $content =  view('teacher.postsms_list')->with('posts',$posts)->render(); 

                        return response()->json(['status'=>1, 'message' => 'Posts list','data'=>$content]);


                    break; 
                }
            }

        } else{
            return response()->json([ 'status' => 0, 'message' => 'Session Logged Out']); 
        }
    }

    public function postLoadModalContents(Request $request) {
        $batch = $request->get('batch'); 
        $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
            ->leftjoin('student_class_mappings', 'students.user_id', 'student_class_mappings.user_id')
            ->where('users.status','ACTIVE')->where('users.delete_status',0)
            ->where('student_class_mappings.academic_year', $batch)
            ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->school_college_id)
            ->where('student_class_mappings.school_id',Auth::User()->school_college_id)
            ->select('students.*')->get(); 

        if($get_student->isNotEmpty()) {
            return response()->json([ 'status' => 1, 'message' => 'Students', 'data' => $get_student]); 
        } else {
            return response()->json([ 'status' => 0, 'message' => 'No Students']); 
        }

    }

    public function addPosts()
    {
        if (Auth::check()) {

            $get_category=Category::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_background=BackgroundTheme::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','name','theme','image')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->school_college_id)
                    ->get(); 


            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->get(); 

            $classes = Classes::where('status', 'ACTIVE')->where('school_id', Auth::User()->school_college_id)->orderby('position', 'Asc')->get();

            $get_batches = $this->getBatches(); 

            $acadamic_year = date('Y');
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->school_college_id)->orderby('id', 'asc')->first();
            if(!empty($settings)) {
                $acadamic_year = trim($settings->acadamic_year);
            }
            if(empty($acadamic_year)) {  $acadamic_year = date('Y'); }

            return view('teacher.communicationscholar',compact('get_category','get_background','get_groups','get_student','get_sections', 'classes', 'get_batches', 'acadamic_year'));
        } else {
            return redirect('/teacher/login');
        }
    }

    public function editPosts(Request $request)
    {
        if (Auth::check()) {

            $get_category=Category::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_background=BackgroundTheme::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','name','theme','image')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->school_college_id)->get();  

            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->get(); 

            $post = CommunicationPost::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
            }
            return view('teacher.communicationscholaredit',compact('get_category','get_background','get_groups','get_student',
                    'get_sections', 'post'));
        } else {
            return redirect('/teacher/login');
        }
    }

    public function viewPostStatus(Request $request) {
        if (Auth::check()) {
            $post = CommunicationPost::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
                $post_receivers = CommunicationPost::getIsReceiversAttribute($request->get('id'));
            }  //echo "<pre>"; print_r($post_receivers); exit;
            return view('teacher.post_status',compact('post', 'post_receivers'));
        } else {
            return redirect('/teacher/login');
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
                    ->leftjoin('classes', 'students.class_id', 'classes.id')
                    ->leftjoin('sections', 'students.section_id', 'sections.id')
                    //->leftjoin('notifications', 'notifications.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->school_college_id)
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                    //->where('notifications.type_no', 4)->where('notifications.post_id', $post_id)   
                    ->select('users.id', 'users.name', 'users.mobile',  'users.fcm_id',  'users.is_app_installed',
                            'classes.class_name', 'sections.section_name'); 

            $filtered_qry = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->leftjoin('classes', 'students.class_id', 'classes.id')
                    ->leftjoin('sections', 'students.section_id', 'sections.id')
                    //->leftjoin('notifications', 'notifications.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->school_college_id)
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                    //->where('notifications.type_no', 4)->where('notifications.post_id', $post_id)   
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
                $totalData->where('users.school_college_id', Auth::User()->school_college_id); 
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
            return redirect('/teacher/login');
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
                        ->where('school_id',Auth::User()->school_college_id)->select('id')->get();
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
            $post_new->posted_by=Auth::User()->school_college_id;
            $post_new->created_by=Auth::User()->id;
            $post_new->youtube_link=$youtube_link;
            $post_new->status='PENDING';

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
            return redirect('/teacher/login');
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
            $school_id = Auth::User()->school_college_id;
            $categories = Category::where('status', 'ACTIVE')->where('school_id', $school_id)->orderby('position', 'asc')->get(); 
            $posts = CommunicationSms::where('delete_status', 0)->whereIn('status', ['PENDING', "ACTIVE"])->where('posted_by', $school_id)
            ->orderby('id', 'desc')
            ->paginate($limit, ['communication_sms.*'], 'page', $page_no);

            return view('teacher.postsms')->with('categories', $categories)->with('posts', $posts);
        } else {
            return redirect('/teacher/login');
        }
    }

    public function addPostSms()
    {
        if (Auth::check()) {

            $get_category=Category::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_template=DltTemplate::where('status','ACTIVE')->select('id','name','content','type')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->school_college_id)->get();  


            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->get(); 

            $available_credits = 0;
            $get_available_credits = SMSCredits::where('status','YES')->where('school_id',Auth::User()->school_college_id)
                ->orderby('id', 'desc')->first(); 
            if(!empty($get_available_credits)) {
                $available_credits = $get_available_credits->available_credits;
            }

            $get_batches = $this->getBatches(); 

            $acadamic_year = date('Y');
            $settings = DB::table('admin_settings')->where('school_id', Auth::User()->school_college_id)->orderby('id', 'asc')->first();
            if(!empty($settings)) {
                $acadamic_year = trim($settings->acadamic_year);
            }
            if(empty($acadamic_year)) {  $acadamic_year = date('Y'); }

            return view('teacher.communication_sms_scholar',compact('get_category','get_template','get_groups','get_student','get_sections', 'available_credits', 'get_batches', 'acadamic_year'));
        } else {
            return redirect('/teacher/login');
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

       $input = $request->all(); //  echo "<pre>"; print_r($input); exit;
       $vars = $input['vars'];
       $content_vars = '';
       if(count($vars) > 0) {
            $content_vars = serialize($vars);
       }

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
            $post_new->content_vars=$content_vars;
            $post_new->notify_datetime=$schedule_date;
            $post_new->posted_by=Auth::User()->school_college_id;
            $post_new->created_by=Auth::User()->id;
            $post_new->status='PENDING';

            $post_new->save();

            return response()->json(['status'=>1,'message'=>'Sms Created Successfully']);

        } else {
            return redirect('/teacher/login');
        }
    }

    public function editPostSms(Request $request)
    {
        if (Auth::check()) {

            $school_id = Auth::User()->id;

            $get_category=Category::where('status','ACTIVE')->where('school_id', $school_id)->select('id','name')->orderBy('position',"ASC")->get();

            $get_template=DltTemplate::where('status','ACTIVE')->select('id','name','content','type')->get();

            $get_groups=CommunicationGroup::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->select('id','group_name')->get();

            $get_student = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')->where('users.school_college_id',Auth::User()->school_college_id)->get();  

            $get_sections=Sections::where('status','ACTIVE')->where('school_id',Auth::User()->school_college_id)->get(); 

            $post = CommunicationSms::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
            }
            return view('teacher.communicationsmsscholaredit',compact('get_category','get_template','get_groups','get_student',
                    'get_sections', 'post'));
        } else {
            return redirect('/teacher/login');
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


    public function viewPostSmsStatus(Request $request) {
        if (Auth::check()) {
            $post = CommunicationSms::where('id', $request->get('id'))->get();
            if($post->isNotEmpty()) {
                $post = $post[0]->toArray();
                $post_receivers = CommunicationSms::getIsReceiversAttribute($request->get('id'));
            }  //echo "<pre>"; print_r($post_receivers); exit;
            return view('teacher.post_sms_status',compact('post', 'post_receivers'));
        } else {
            return redirect('/teacher/login');
        }
    }

    public function getPostSmsStatus(Request $request) {

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
            $post  = DB::table('communication_sms')->where('id', $post_id)->first();

            $users_qry = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->leftjoin('classes', 'students.class_id', 'classes.id')
                    ->leftjoin('sections', 'students.section_id', 'sections.id')
                    //->leftjoin('notifications', 'notifications.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->school_college_id)
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                    //->where('notifications.type_no', 4)->where('notifications.post_id', $post_id)   
                    ->select('users.id', 'users.name', 'users.mobile',  'users.fcm_id', 'users.is_app_installed',
                        'classes.class_name', 'sections.section_name'); 

            $filtered_qry = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->leftjoin('classes', 'students.class_id', 'classes.id')
                    ->leftjoin('sections', 'students.section_id', 'sections.id')
                    //->leftjoin('notifications', 'notifications.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.school_college_id', Auth::User()->school_college_id)
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
                    //->where('notifications.type_no', 4)->where('notifications.post_id', $post_id)   
                    ->select('users.id', 'users.name', 'users.mobile',  'users.fcm_id', 'users.is_app_installed', 
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
                    $notify = DB::table('notifications')->where('type_no', 5)->where('post_id', $post_id)
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
            return redirect('/teacher/login');
        }
    }

    public function getBatches() {
        $batches = [];
        $academic_year = DB::table('student_class_mappings')->min('academic_year'); 
        $start = $academic_year;
        $current = date('Y');
        $next = $current; // + 1;

        for($i=$start; $i<=$next; $i++) { 
            $plus = $i + 1;
            $display_academic_year = $i .' - '. $plus;
            $batches[] = ['academic_year' => $i, 'display_academic_year' => $display_academic_year];
        }
        return $batches;
    }
}





