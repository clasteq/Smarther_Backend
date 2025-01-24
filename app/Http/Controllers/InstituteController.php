<?php
namespace App\Http\Controllers;

use App\Models\Countries;
use App\Models\Districts;
use App\Models\School;
use App\Models\College;
use App\Models\Classes;
use App\Models\User;
use App\Models\States;
use App\Models\FeesReceipts;
use App\Models\SalarySlips;
use App\Models\Student;
use App\Models\Teacher; 

use App\Models\CourseCategories;
use App\Models\Courses;
use App\Models\CourseSubjects;
use App\Models\CourseTeacherSubscriptions;

use App\Models\CourseStudentSubscriptions;
use App\Models\CourseDocuments;
use App\Models\CourseStudentPayment;
use App\Models\OnlineSessions;
use App\Models\OnlineSessionPayment;

use App\Http\Controllers\CommonController;


use Auth;
use DB;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;
use Validator;
use View;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use PDF;

class InstituteController extends Controller
{

	public $accepted_formats = ['jpeg', 'jpg', 'png', 'PNG', 'JPEG', 'JPG'];
    public $accepted_formats_audio = ['mp3', 'mp4', 'MP3', 'MP4'];
    public $accepted_formats_qbt = ['mp3', 'mp4', 'jpeg', 'jpg', 'png', 'doc', 'docx', 'pdf', 'MP3', 'MP4', 'JPEG', 'JPG', 'PNG', 'DOC', 'DOCX', 'PDF'];

    public function index()
    {   //echo Hash::make('thiranDemo@123');exit;
        if (Auth::check()) {
            return redirect('/institute/home');
        } else {
            return view('institute.login');
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

        if (Auth::attempt(['email' => $userEmail, 'password' => $password, 'user_type' => ['SCHOOL', 'COLLEGE']])) {

            $userStatus = User::where('email', $userEmail)->whereIn('user_type', ['SCHOOL', 'COLLEGE'])->first();

            if($userStatus->status != 'ACTIVE') {
                return response()->json(['status' => 'FAILED', 'message' => 'Account is blocked by Admin.']);
            }

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
            $session_country_code = Session::get('session_country_code');
            $user_type = Auth::User()->user_type;
            $school_id = Auth::User()->id; 

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { // Super Admin
                $students_count = User::where('user_type', 'STUDENT')->where('school_college_id', $school_id)->where('delete_status',0)->where('status', 'ACTIVE')->count();
                $teachers_count = User::where('user_type', 'TEACHER')->where('school_college_id', $school_id)->where('status', 'ACTIVE')->count(); 

                $students_pending_count = User::where('user_type', 'STUDENT')
                    ->where('school_college_id', $school_id)
                    ->where('approval_status', 'PENDING')->where('delete_status',0)
                    ->where('status', 'ACTIVE')->count();
                $teachers_pending_count = User::where('user_type', 'TEACHER')
                    ->where('school_college_id', $school_id)
                    ->where('approval_status', 'PENDING')->where('status', 'ACTIVE')->count();
                    
                return view::make('institute.home')->with([
                    'students_count' => $students_count,
                    'teachers_count' => $teachers_count,
                    'students_pending_count' => $students_pending_count,
                    'teachers_pending_count' => $teachers_pending_count
                ]);
            }
        } else {
            return redirect('/institute/login');
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
            return redirect('/institute');
        } else {
            return redirect('/institute');
        }
    }

	// Schools

    public function viewSchools() {
        if (Auth::check()) { 
        	$states = States::where('status', 'ACTIVE')->orderby('state_name', 'asc')->get();
            return view('admin.schools')->with('states', $states); 
        } else {
            return redirect('/admin/login');
        }
    }

    public function getSchools(Request $request)  {

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
                ->leftjoin('schools', 'schools.user_id', 'users.id') 
                ->where('user_type', 'SCHOOL')
                ->where('users.delete_status',0) 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'schools.address');
            $filtered_qry = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('schools', 'schools.user_id', 'users.id') 
                ->where('user_type', 'SCHOOL')
                ->where('users.delete_status',0) 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'schools.address');

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
                ->leftjoin('schools', 'schools.user_id', 'users.id') 
                ->where('user_type', 'SCHOOL')
                ->where('users.delete_status',0) 
                ->select('users.id')->get();

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

    public function postSchools(Request $request) {

        if (Auth::check()) {

            $id = $request->id;
            $name = $request->name;  
            $name_code = $request->name_code;  
            $display_name = $request->display_name;  
            $slug_name = $request->slug_name;  
            $email = $request->email;
            $password = $request->password;
            $mobile = $request->mobile; 
            $joined_date = $request->joined_date;
            $country = $request->country;
            $state_id = $request->state_id;
            $city_id = $request->city_id;
            $image = $request->file('profile_image');
 
            $admission_no = $request->admission_no; 
            $address = $request->address;
            $status = $request->status;  

            if($country == ''){
                $country = 0;
            }
            if($state_id == ''){
                $state_id = 0;
            }
            if($city_id == ''){
                $city_id = 0;
            } 
        	$roll_no = 0; 

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'display_name'  => 'required',
                'slug_name' => 'required|unique:users,slug_name,' . $id,
                'name_code' => 'required',
                'mobile' => 'required',
                'email' => 'required',
                'admission_no' => 'required',
                'address'  => 'required',
                //'image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if(substr( $mobile, 0, 1 ) === "0") {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid mobile']);
            }

            if((strlen($mobile)!=10)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid mobile']);
            }

            $vmobile = (new CommonController())->validate_mobile($mobile); 

            if(!$vmobile) {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid mobile']);
            }     

            if(!empty($email)){
                $email = strtolower($email); 

                $vemail = (new CommonController())->validate_mobile($email); 

                if(!$email) {
                    return response()->json(['status' => 'FAILED', 'message' => 'Invalid email']);
                } 

                if ($id > 0) {
                    $exists = DB::table('users')->where('email', $email)->where('user_type', "SCHOOL")->whereNotIn('id', [$id])->first();
                } else {
                    $exists = DB::table('users')->where('email', $email)->where('user_type', "SCHOOL")->first();
                } 

	            if (!empty($exists)) {
	                return response()->json(['status' => 'FAILED', 'message' => 'Email Already Exists'], 201);
	            }
            }  

            if(!empty($mobile)){
                if ($id > 0) {
                    $exists = DB::table('users')->where('mobile', $mobile)->where('user_type', "SCHOOL")->whereNotIn('id', [$id])->first();
                } else {
                    $exists = DB::table('users')->where('mobile', $mobile)->where('user_type', "SCHOOL")->first();
                }

                if (!empty($exists)) {
	                return response()->json(['status' => 'FAILED', 'message' => 'Mobile Already Exists'], 201);
	            }
    
            }  

            if(!empty($admission_no)){
	            if ($id > 0) {
	                $admission_no_chk = DB::table('users')->where('user_type', "SCHOOL")->where('admission_no', $admission_no)->whereNotIn('id', [$id])->first();
	            } else {
	                $admission_no_chk = DB::table('users')->where('user_type', "SCHOOL")->where('admission_no', $admission_no)->first();
	            }

	            if (!empty($admission_no_chk)) {
	                return response()->json(['status' => 'FAILED', 'message' => 'Reference Number Already Exists'], 201);
	            }
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

            $users->display_name = $display_name;
            $users->name_code = $name_code;
            $users->user_type = "SCHOOL";
            $users->slug_name = $slug_name;

            if(empty($users->reg_no)) {
                $lastjobid = DB::table('users')
                    ->where('created_at', 'like', date('Y-m-d') . '%')
                    ->orderby('id', 'desc')->count();
                $lastjobid = $lastjobid + 1;
                $append = str_pad($lastjobid, 6, "0", STR_PAD_LEFT);
                $reg_no = date('ymd') . $append;

                $users->reg_no = $reg_no;
            }
            
            $users->name = $name; 
            $users->email = $email;
            $users->status = $status;

            if(!empty($password)) {
                $users->password = Hash::make($password);
            }
            $users->passcode = $password;
            $country_code = DB::table('countries')->where('id', $country)->value('phonecode');
            $users->mobile = $mobile; 
            $users->country = $country;
            $users->country_code = $country_code;
            $users->code_mobile = $country_code.$mobile; 
            $users->state_id = $state_id;
            $users->city_id = $city_id;
            $users->admission_no = $admission_no;  

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

            }
            $users->save();

            $userId = $users->id;

            if ($id > 0) {
                $school = School::where('user_id', $id)->first();
                if(empty($school)) {
                    $school = new School;
                    $school->created_at = $date;
                }
                $school->updated_at = $date;
            } else {
                $school = new School;
                $school->created_at = $date;
            }

            $school->user_id = $userId; 
            $school->admission_no = $admission_no; 
            $school->address = $address; 

            $school->save();  

            return response()->json(['status' => 'SUCCESS', 'message' => 'School details updated Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editSchools(Request $request) {

        if (Auth::check()) {
 
            $school = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('schools', 'schools.user_id', 'users.id') 
                ->where('user_type', 'SCHOOL')
                ->where('users.id', $request->code)
                ->where('users.delete_status',0) 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'schools.address')
                ->get();

            if ($school->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $school[0], 'message' => 'School Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No School Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }


    public function deleteSchools(Request $request)
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

    // Colleges

    public function viewColleges() {
        if (Auth::check()) { 
        	$states = States::where('status', 'ACTIVE')->orderby('state_name', 'asc')->get();
            return view('admin.colleges')->with('states', $states); 
        } else {
            return redirect('/admin/login');
        }
    }

    public function getColleges(Request $request)  {

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
                ->leftjoin('colleges', 'colleges.user_id', 'users.id') 
                ->where('user_type', 'COLLEGE')
                ->where('users.delete_status',0) 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'colleges.address');
            $filtered_qry = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('colleges', 'colleges.user_id', 'users.id') 
                ->where('user_type', 'COLLEGE')
                ->where('users.delete_status',0) 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'colleges.address');

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
                ->leftjoin('colleges', 'colleges.user_id', 'users.id') 
                ->where('user_type', 'COLLEGE')
                ->where('users.delete_status',0)  
                ->select('users.id')->get();

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

    public function postColleges(Request $request) {

        if (Auth::check()) {

            $id = $request->id;
            $name = $request->name;  
            $email = $request->email;
            $password = $request->password;
            $mobile = $request->mobile; 
            $joined_date = $request->joined_date;
            $country = $request->country;
            $state_id = $request->state_id;
            $city_id = $request->city_id;
            $image = $request->file('profile_image');
 
            $admission_no = $request->admission_no; 
            $address = $request->address;
            $status = $request->status;  

            if($country == ''){
                $country = 0;
            }
            if($state_id == ''){
                $state_id = 0;
            }
            if($city_id == ''){
                $city_id = 0;
            } 
        	$roll_no = 0; 

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mobile' => 'required',
                'email' => 'required',
                'admission_no' => 'required',
                'address'  => 'required',
                //'image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }

            if(substr( $mobile, 0, 1 ) === "0") {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid mobile']);
            }

            if((strlen($mobile)!=10)) {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid mobile']);
            }

            $vmobile = (new CommonController())->validate_mobile($mobile); 

            if(!$vmobile) {
                return response()->json(['status' => 'FAILED', 'message' => 'Invalid mobile']);
            }     

            if(!empty($email)){
                $email = strtolower($email); 

                $vemail = (new CommonController())->validate_mobile($email); 

                if(!$email) {
                    return response()->json(['status' => 'FAILED', 'message' => 'Invalid email']);
                } 

                if ($id > 0) {
                    $exists = DB::table('users')->where('email', $email)->whereNotIn('id', [$id])->first();
                } else {
                    $exists = DB::table('users')->where('email', $email)->first();
                } 

	            if (!empty($exists)) {
	                return response()->json(['status' => 'FAILED', 'message' => 'Email Already Exists'], 201);
	            }
            }  

            if(!empty($mobile)){
                if ($id > 0) {
                    $exists = DB::table('users')->where('mobile', $mobile)->whereNotIn('id', [$id])->first();
                } else {
                    $exists = DB::table('users')->where('mobile', $mobile)->first();
                }

                if (!empty($exists)) {
	                return response()->json(['status' => 'FAILED', 'message' => 'Mobile Already Exists'], 201);
	            }
    
            }  

            if(!empty($admission_no)){
	            if ($id > 0) {
	                $admission_no_chk = DB::table('users')->where('admission_no', $admission_no)->whereNotIn('id', [$id])->first();
	            } else {
	                $admission_no_chk = DB::table('users')->where('admission_no', $admission_no)->first();
	            }

	            if (!empty($admission_no_chk)) {
	                return response()->json(['status' => 'FAILED', 'message' => 'Admission Number Already Exists'], 201);
	            }
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

            $users->user_type = "COLLEGE";

            $lastjobid = DB::table('users')
                ->where('created_at', 'like', date('Y-m-d') . '%')
                ->orderby('id', 'desc')->count();
            $lastjobid = $lastjobid + 1;
            $append = str_pad($lastjobid, 6, "0", STR_PAD_LEFT);
            $reg_no = date('ymd') . $append;

            $users->reg_no = $reg_no;
            $users->name = $name; 
            $users->email = $email;
            $users->status = $status;

            if(!empty($password)) {
                $users->password = Hash::make($password);
            }
            $users->passcode = $password;
            $country_code = DB::table('countries')->where('id', $country)->value('phonecode');
            $users->mobile = $mobile; 
            $users->country = $country;
            $users->country_code = $country_code;
            $users->code_mobile = $country_code.$mobile; 
            $users->state_id = $state_id;
            $users->city_id = $city_id;
            $users->admission_no = $admission_no;  

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
                $college = College::where('user_id', $id)->first();
                if(empty($college)) {
                    $college = new College;
                    $college->created_at = $date;
                }
                $college->updated_at = $date;
            } else {
                $college = new College;
                $college->created_at = $date;
            }

            $college->user_id = $userId; 
            $college->admission_no = $admission_no; 
            $college->address = $address; 

            $college->save();  

            return response()->json(['status' => 'SUCCESS', 'message' => 'College details updated Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editColleges(Request $request) {

        if (Auth::check()) {
 
            $colleges = User::leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('colleges', 'colleges.user_id', 'users.id') 
                ->where('user_type', 'COLLEGE')
                ->where('users.id', $request->code)
                ->where('users.delete_status',0) 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'colleges.address')
                ->get();

            if ($colleges->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $colleges[0], 'message' => 'College Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No College Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }


    public function deleteColleges(Request $request)
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

    // Fees receipts

    public function viewFeesReceipts() {
        if (Auth::check()) {  
            $school_college_id = Auth::User()->id;
            $classes = Classes::leftjoin('class_master', 'class_master.id', 'classes.class_name')
                ->where('school_id', $school_college_id)->where('classes.status','=','ACTIVE')
                ->orderby('class_master.position','asc')->select('class_master.class_name', 'class_master.id')->get();
            return view('admin.fees')->with('classes',$classes); 
        } else {
            return redirect('/admin/login');
        }
    }

    public function getFeesReceipts(Request $request)  {

        if (Auth::check()) {
            $school_id = Auth::User()->id;
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $status = $request->get('status_id', '');
            $section = $request->get('section_id', '');
            $class_id = $request->get('class_id', '');

            $users_qry = FeesReceipts::leftjoin('users', 'users.id', 'fees_receipts.user_id')
                ->leftjoin('students', 'students.user_id', 'fees_receipts.user_id')
                ->leftjoin('class_master', 'class_master.id', 'fees_receipts.class_id')
                ->leftjoin('sections', 'sections.id', 'fees_receipts.section_id')
                ->leftjoin('terms', 'terms.id', 'fees_receipts.term_id') 
                ->where('user_type', 'STUDENT')
                ->where('users.delete_status',0) 
                ->where('fees_receipts.school_college_id',$school_id) 
                ->select('fees_receipts.*', 'class_master.class_name', 'sections.section_name', 'terms.term_name',
                'users.name', 'students.admission_no');
            $filtered_qry = FeesReceipts::leftjoin('users', 'users.id', 'fees_receipts.user_id')
                ->leftjoin('students', 'students.user_id', 'fees_receipts.user_id')
                ->leftjoin('class_master', 'class_master.id', 'fees_receipts.class_id')
                ->leftjoin('sections', 'sections.id', 'fees_receipts.section_id')
                ->leftjoin('terms', 'terms.id', 'fees_receipts.term_id') 
                ->where('user_type', 'STUDENT')
                ->where('users.delete_status',0) 
                ->where('fees_receipts.school_college_id',$school_id) 
                ->select('fees_receipts.*', 'class_master.class_name', 'sections.section_name', 'terms.term_name',
                'users.name', 'students.admission_no');

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

            if($class_id > 0) {
                $users_qry->where('fees_receipts.class_id', $class_id);
                $filtered_qry->where('fees_receipts.class_id', $class_id);
            }

            if($section > 0) {
                $users_qry->where('fees_receipts.section_id', $section);
                $filtered_qry->where('fees_receipts.section_id', $section);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'fees_receipts.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $totalData = FeesReceipts::leftjoin('users', 'users.id', 'fees_receipts.user_id')
                ->leftjoin('students', 'students.user_id', 'fees_receipts.user_id')
                ->leftjoin('class_master', 'class_master.id', 'fees_receipts.class_id')
                ->leftjoin('sections', 'sections.id', 'fees_receipts.section_id')
                ->leftjoin('terms', 'terms.id', 'fees_receipts.term_id') 
                ->where('user_type', 'STUDENT')
                ->where('users.delete_status',0) 
                ->select('fees_receipts.id')->get();

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

    public function postFeesReceipts(Request $request) {

        if (Auth::check()) {
            $school_college_id = Auth::User()->id;
            $id = $request->id;
            $class_id = $request->class_id;  
            $section_id = $request->section_id;
            $term_id = $request->term_id;
            $student_id = $request->student_id; 
            $fee_amount = $request->fee_amount;
            $paid_date = $request->paid_date;  

            if($fee_amount >0){ } else {
                return response()->json([ 
                    'status' => "FAILED",
                    'message' => "Please enter the valid fees amount",
                ]);
            } 

            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'section_id' => 'required',
                'term_id' => 'required',
                'student_id' => 'required',
                'fee_amount'  => 'required',
                'paid_date' => 'required'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }   
            $date = date('Y-m-d H:i:s');
            if ($id > 0) {
                $fees_receipt = FeesReceipts::find($id);
                $fees_receipt->updated_at = $date;
                $fees_receipt->updated_by = Auth::User()->id;
            } else {
                $fees_receipt = new FeesReceipts(); 
                $fees_receipt->created_at = $date;
                $fees_receipt->created_by = Auth::User()->id;

                $lastjobid = DB::table('fees_receipts')
                    ->where('created_at', 'like', date('Y-m-d') . '%')
                    ->orderby('id', 'desc')->count();
                $lastjobid = $lastjobid + 1;
                $append = str_pad($lastjobid, 4, "0", STR_PAD_LEFT);
                $reg_no = date('ymd') . $student_id . $append;

                $fees_receipt->receipt_no = $reg_no;
            }

            $fees_receipt->school_college_id = $school_college_id;
            
            $fees_receipt->user_id = $student_id; 
            $fees_receipt->class_id = $class_id;
            $fees_receipt->section_id = $section_id; 
            $fees_receipt->term_id = $term_id; 
            $fees_receipt->fee_amount = $fee_amount; 
            $fees_receipt->paid_date = $paid_date;  

            $image = $request->file('image');
            if (!empty($image)) {
                $ext = $image->getClientOriginalExtension();
                if (!in_array($ext, $this->accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg']);
                }

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/feereceipts/'.$student_id);
                if (!file_exists(public_path('/uploads/feereceipts/'.$student_id))) {
                    mkdir(public_path('/uploads/feereceipts/'.$student_id), 0777, true);
                }

                $image->move($destinationPath, $countryimg);

                $fees_receipt->fee_receipt = $countryimg;

            }

            $fees_receipt->save();

            if(empty($fees_receipt->fee_receipt)) {
                $feeId = $fees_receipt->id;
                $feeregno = $fees_receipt->receipt_no.'.pdf';
                $fees_receipt->fee_receipt = $feeregno;  
                $fees_receipt->save();

                $student = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->leftjoin('classes', 'classes.id', 'students.class_id')
                    ->leftjoin('sections', 'sections.id', 'students.section_id')
                    ->where('users.id', $student_id)
                    ->select('users.name', 'users.admission_no', 'students.father_name', 'classes.class_name', 
                        'sections.section_name')->first();
                /* Fees Receipt pdf creation */
                $pdfcontent = view('institute.pdffeesreceipt')->with('fees_receipt', $fees_receipt)->with('student', $student)->render(); 

                if (!file_exists(public_path('/uploads/feereceipts/'.$student_id))) {
                    mkdir(public_path('/uploads/feereceipts/'.$student_id), 0777, true);
                }

                //echo $pdfcontent; exit; 
                $filelocation = public_path('/uploads/feereceipts/'.$student_id.'/').$feeregno;
                PDF::loadHTML($pdfcontent)->setPaper('a4', 'landscape')->setWarnings(false)->save($filelocation);  
            }
            
            if($id > 0) {
                $title = 'Fees receipt Updated';
                $message = 'Fees Receipt #'.$fees_receipt->receipt_no.' with Rs.'.$fee_amount.'/- is Updated on '.date('d-m-Y', strtotime($paid_date)).'. ';
            }   else {
                $title = 'Fees Paid';
                $message = 'Fees Receipt #'.$fees_receipt->receipt_no.' with Rs.'.$fee_amount.'/- is Paid on '.date('d-m-Y', strtotime($paid_date)).'. ';
            }
            $type = 1;

            $fcmMsg = array("fcm" => array("notification" => array(
                    "title" => $title,
                    "body" => $message,
                    "type" => "1",
                  ))); 
            $type_no = 1;
            $type_id = $fees_receipt->id;
            CommonController::push_notification($student_id, $type_no, $type_id, $fcmMsg);

            return response()->json(['status' => 'SUCCESS', 'message' => 'Fees receipt Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editFeesReceipts(Request $request) {

        if (Auth::check()) {
 
            $school = FeesReceipts::leftjoin('users', 'users.id', 'fees_receipts.user_id')
                ->leftjoin('students', 'students.user_id', 'fees_receipts.user_id')
                ->leftjoin('classes', 'classes.id', 'fees_receipts.class_id')
                ->leftjoin('sections', 'sections.id', 'fees_receipts.section_id')
                ->leftjoin('terms', 'terms.id', 'fees_receipts.term_id') 
                ->where('user_type', 'STUDENT')
                ->where('users.delete_status',0) 
                ->where('fees_receipts.id', $request->id)
                ->select('fees_receipts.*')->get();

            if ($school->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $school[0], 'message' => 'School Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No School Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    // Salary slips

    public function viewPayslipsReceipts() {
        if (Auth::check()) {  
            $school_college_id = Auth::User()->id;
            $teachers =  Teacher::leftjoin('users','users.id','teachers.user_id')
                ->where('users.status','=','ACTIVE')->where('users.school_college_id',$school_college_id)
                ->get(["users.name", "users.mobile", "users.id", "teachers.emp_no"]);
            return view('admin.payslips')->with('teachers',$teachers); 
        } else {
            return redirect('/institute/login');
        }
    }

    public function getPayslipsReceipts(Request $request)  {

        if (Auth::check()) {
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');
            $input = $request->all();
            $teacherid = $request->get('teacherid', ''); 

            $users_qry = SalarySlips::leftjoin('users', 'users.id', 'teacher_salaryslips.user_id') 
                ->where('user_type', 'TEACHER')
                ->where('users.delete_status',0) 
                ->select('teacher_salaryslips.*',  'users.name', 'users.mobile');
            $filtered_qry = SalarySlips::leftjoin('users', 'users.id', 'teacher_salaryslips.user_id') 
                ->where('user_type', 'TEACHER')
                ->where('users.delete_status',0) 
                ->select('teacher_salaryslips.*',  'users.name', 'users.mobile');

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

            if($teacherid > 0) {
                $users_qry->where('user_id', $teacherid);
                $filtered_qry->where('user_id', $teacherid);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'teacher_salaryslips.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $users = $users_qry->orderBy($orderby, $dir)->offset($start)->limit($limit)->get();
            $totalData = SalarySlips::leftjoin('users', 'users.id', 'teacher_salaryslips.user_id') 
                ->where('user_type', 'TEACHER')
                ->where('users.delete_status',0) 
                ->select('teacher_salaryslips.id')->get();

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
            return redirect('/institute/login');
        }

    }

    public function postPayslipsReceipts(Request $request) {

        if (Auth::check()) {
            $school_college_id = Auth::User()->id;
            $id = $request->id;
            $teacher_id = $request->teacher_id;  
            $sal_amount = $request->sal_amount;
            $sal_for_month = $request->sal_for_month;
            $sal_paid_on = $request->sal_paid_on;  

            if($sal_amount >0){ } else {
                return response()->json([ 
                    'status' => "FAILED",
                    'message' => "Please enter the valid fees amount",
                ]);
            } 

            $validator = Validator::make($request->all(), [
                'teacher_id' => 'required',
                'sal_amount' => 'required',
                'sal_for_month' => 'required',
                'sal_paid_on' => 'required', 
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs " . implode(', ', $msg),
                ]);
            }   
            $date = date('Y-m-d H:i:s');
            if ($id > 0) {
                $salslips = SalarySlips::find($id);
                $salslips->updated_at = $date;
                $salslips->updated_by = Auth::User()->id;
            } else {
                $salslips = new SalarySlips(); 
                $salslips->created_at = $date;
                $salslips->created_by = Auth::User()->id;

                $lastjobid = DB::table('teacher_salaryslips')
                    ->where('created_at', 'like', date('Y-m-d') . '%')
                    ->orderby('id', 'desc')->count();
                $lastjobid = $lastjobid + 1;
                $append = str_pad($lastjobid, 4, "0", STR_PAD_LEFT);
                $reg_no = date('ymd') . $teacher_id . $append;

                $salslips->receipt_no = $reg_no;
            }

            $salslips->school_college_id = $school_college_id;
            
            $salslips->user_id = $teacher_id;  
            $salslips->sal_amount = $sal_amount; 
            $salslips->sal_for_month = $sal_for_month; 
            $salslips->sal_paid_on = $sal_paid_on;  

            $image = $request->file('sal_receipt_file');
            if (!empty($image)) {
                $ext = $image->getClientOriginalExtension();
                $accepted_formats = ['jpeg', 'jpg', 'png', 'PNG', 'JPEG', 'JPG', 'pdf', 'PDF'];
                if (!in_array($ext, $accepted_formats)) {
                    return response()->json(['status' => "FAILED", 'message' => 'File Format Wrong.Please upload png,jpeg,jpg,pdf']);
                }

                $countryimg = rand() . time() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('/uploads/salaryslips/'.$teacher_id);
                if (!file_exists(public_path('/uploads/salaryslips/'.$teacher_id))) {
                    mkdir(public_path('/uploads/salaryslips/'.$teacher_id), 0777, true);
                }

                $image->move($destinationPath, $countryimg);

                $salslips->sal_receipt_file = $countryimg;

            }    

            $salslips->save(); 

            if(empty($salslips->sal_receipt_file)) {
                $slipId = $salslips->id;
                $salregno = $salslips->receipt_no.'.pdf';
                $salslips->sal_receipt_file = $salregno;  
                $salslips->save();

                $teacher = DB::table('users')->leftjoin('teachers', 'teachers.user_id', 'users.id') 
                    ->leftjoin('users as sc', 'users.school_college_id', 'sc.id') 
                    ->where('users.id', $teacher_id)
                    ->select('users.name', 'teachers.emp_no', 'sc.name as institutename')->first();
                /* Sal Receipt pdf creation */
                $pdfcontent = view('institute.pdfsalreceipt')->with('salslips', $salslips)->with('teacher', $teacher)->render(); 

                if (!file_exists(public_path('/uploads/salaryslips/'.$teacher_id))) {
                    mkdir(public_path('/uploads/salaryslips/'.$teacher_id), 0777, true);
                }

                //echo $pdfcontent; exit; 
                $filelocation = public_path('/uploads/salaryslips/'.$teacher_id.'/').$salregno;
                PDF::loadHTML($pdfcontent)->setPaper('a4', 'landscape')->setWarnings(false)->save($filelocation);  
            }
            
            if($id > 0) {
                $title = 'Salary Slip Updated';
                $message = 'Salary Receipt #'.$salslips->receipt_no.' with Rs.'.$sal_amount.'/- is Updated on '.date('d-m-Y', strtotime($date)).'. ';
            }   else {
                $title = 'Salary Paid';
                $message = 'Salary Receipt #'.$salslips->receipt_no.' with Rs.'.$sal_amount.'/- is Paid on '.date('d-m-Y', strtotime($sal_paid_on)).'. ';
            }
            $type = 4;

            $fcmMsg = array("fcm" => array("notification" => array(
                    "title" => $title,
                    "body" => $message,
                    "type" => "4",
                  ))); 
            $type_no = 4;
            $type_id = $salslips->id;
            CommonController::push_notification($teacher_id, $type_no, $type_id, $fcmMsg);

            return response()->json(['status' => 'SUCCESS', 'message' => 'Salary Slip Saved Successfully']);
        } else {
            return redirect('/institute/login');
        }
    }

    public function editPayslipsReceipts(Request $request) {

        if (Auth::check()) {
 
            $salaryslips = SalarySlips::where('id', $request->id)
                ->select('teacher_salaryslips.*')->get();

            if ($salaryslips->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $salaryslips[0], 'message' => 'Salary Slip Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Salary Slip Detail']);
            }
        } else {
            return redirect('/institute/login');
        }
    }

    //Mentor Courses
    public function viewMentorCourses()
    {
        if (Auth::check()) {
            $categories = CourseCategories::where('status', 'ACTIVE')->get();

            $user_type = Auth::User()->user_type;
            $school_id = Auth::User()->id;

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") {

                $teachers = User::where('user_type', 'TEACHER')->where('school_college_id', $school_id)->where('status', 'ACTIVE')->select('name', 'id')->get();

            }

            return view('institute.institutementorcourses')->with('categories', $categories)->with('teachers', $teachers);
        } else {
            return redirect('/institute/login');
        }
    }

    public function getMentorCourses(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $category_id = $request->get('category_id', 0);
            $teacher_id = $request->get('teacher_id', 0);

            $status = $request->get('status');

            $chaptersqry = Courses::leftjoin('users', 'users.id', 'courses.teacher_id')->leftjoin('coursecategories', 'coursecategories.id', 'courses.category_id')->where('courses.teacher_id', '>', 0)
                ->select('courses.*', 'coursecategories.category_name', 'users.name');

            $filteredqry = Courses::leftjoin('users', 'users.id', 'courses.teacher_id')->leftjoin('coursecategories', 'coursecategories.id', 'courses.category_id')->where('courses.teacher_id', '>', 0)
                ->select('courses.*', 'coursecategories.category_name', 'users.name');

            $user_type = Auth::User()->user_type;

            $school_id = Auth::User()->id;

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") {
                $chaptersqry->where('users.user_type', 'TEACHER')->where('users.school_college_id', $school_id);
                $filteredqry->where('users.user_type', 'TEACHER')->where('users.school_college_id', $school_id);
            }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'courses.status') {
                            $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }

                    }
                }
            }

            if (!empty($status)) {
                $chaptersqry->where('courses.status', $status);
                $filteredqry->where('courses.status', $status);
            }

            if ($category_id > 0) {
                $chaptersqry->where('courses.category_id', $category_id);
                $filteredqry->where('courses.category_id', $category_id);
            }

            if ($teacher_id > 0) {
                $chaptersqry->where('courses.teacher_id', $teacher_id);

                $filteredqry->where('courses.teacher_id', $teacher_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'courses.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $courses = $chaptersqry->skip($start)->take($length)
                ->orderby($orderby, $dir)->get();

            $filters = $filteredqry->select('courses.id')->count();

            $totalDataqry = Courses::orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($courses)) {
                foreach ($courses as $post) {
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
            return redirect('/institute/login');
        }

    }

    public function postMentorCourse(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;

            $status = $request->status;

            $validator = Validator::make($request->all(), [

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
                $subject = Courses::find($id);
                $subject->status = $status;
                $subject->save();

            }

            return response()->json(['status' => 'SUCCESS', 'message' => 'Course Status Updated Successfully']);
        } else {
            return redirect('/institute/login');
        }
    }

    public function editCourses(Request $request)
    {

        if (Auth::check()) {
            $subjects = Courses::where('id', $request->code)->get();

            if ($subjects->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $subjects[0], 'message' => 'Course Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Course Detail']);
            }
        } else {
            return redirect('/institute/login');
        }
    }

    //Course Subjects
    public function viewMentorCourseSubjects()
    {
        if (Auth::check()) {

            $user_type = Auth::User()->user_type;
            $school_id = Auth::User()->id;

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") {

                $teachers = User::where('user_type', 'TEACHER')->where('school_college_id', $school_id)->where('status', 'ACTIVE')->select('name', 'id')->get();

            }

            return view('institute.mentorcoursesubjects')->with('teachers', $teachers);

        } else {
            return redirect('/institute/login');
        }
    }

    public function getMentorCourseSubjects(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $status = $request->get('status');
            $teacher_id = $request->get('teacher_id', 0);

            $chaptersqry = CourseSubjects::leftjoin('users', 'users.id', 'coursesubjects.teacher_id')->where('coursesubjects.id', '>', 0)->where('coursesubjects.teacher_id', '>', 0);
            $filteredqry = CourseSubjects::leftjoin('users', 'users.id', 'coursesubjects.teacher_id')->where('coursesubjects.id', '>', 0)->where('coursesubjects.teacher_id', '>', 0);

            $user_type = Auth::User()->user_type;

            $school_id = Auth::User()->id;

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") {
                $chaptersqry->where('users.user_type', 'TEACHER')->where('users.school_college_id', $school_id);
                $filteredqry->where('users.user_type', 'TEACHER')->where('users.school_college_id', $school_id);
            }

            $status = $request->get('status', '');

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'coursesubjects.status') {
                            $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($status)) {
                $chaptersqry->where('coursesubjects.status', $status);
                $filteredqry->where('coursesubjects.status', $status);
            }

            if ($teacher_id > 0) {
                $chaptersqry->where('coursesubjects.teacher_id', $teacher_id);

                $filteredqry->where('coursesubjects.teacher_id', $teacher_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'coursesubjects.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $courses = $chaptersqry->select('coursesubjects.*', 'users.name')->skip($start)->take($length)
                ->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('coursesubjects.id', 'users.name')->count();

            $totalDataqry = CourseSubjects::orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($courses)) {
                foreach ($courses as $post) {
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
            return redirect('/institute/login');
        }

    }

    public function postMentorCourseSubjects(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
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
                $subject = CourseSubjects::find($id);

                $subject->status = $status;

                $subject->save();

            }
            return response()->json(['status' => 'SUCCESS', 'message' => 'Subject Status Updated Successfully']);
        } else {
            return redirect('/institute/login');
        }
    }

    public function editCourseSubjects(Request $request)
    { 
        if (Auth::check()) {
            $subjects = CourseSubjects::where('id', $request->code)->get();

            if ($subjects->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $subjects[0], 'message' => 'Subjects Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Subjects Detail']);
            }
        } else {
            return redirect('/institute/login');
        }
    }

    //Mentor Course Subscription
    public function viewMentorCourseSubscription()
    {
        if (Auth::check()) {

            $user_type = Auth::User()->user_type;
            $school_id = Auth::User()->id; 

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 

            $teachers = User::where('user_type','TEACHER')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id')->get();

            }
         

            return view('institute.teachersCourseSubscription')->with('teachers', $teachers);
        } else {
            return redirect('/institute/login');
        }
    }

    public function getMentorCourseSubscription(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');

            $status = $request->get('status');
            $teacher_id = $request->get('teacher_id', 0);

            $chaptersqry = CourseTeacherSubscriptions::leftjoin('users', 'users.id', 'teacher_course_subscriptions.teacher_id')->leftjoin('courses', 'courses.id', 'teacher_course_subscriptions.course_id')->where('teacher_course_subscriptions.id', '>', 0);
            $filteredqry = CourseTeacherSubscriptions::leftjoin('users', 'users.id', 'teacher_course_subscriptions.teacher_id')->leftjoin('courses', 'courses.id', 'teacher_course_subscriptions.course_id')->where('teacher_course_subscriptions.id', '>', 0);

            $user_type = Auth::User()->user_type;

            $school_id = Auth::User()->id; 
            
            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
               $chaptersqry->where('users.user_type','TEACHER')->where('users.school_college_id',$school_id);
               $filteredqry->where('users.user_type','TEACHER')->where('users.school_college_id',$school_id);
            }

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'teacher_course_subscriptions.status') {
                            $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if (!empty($status)) {
                $chaptersqry->where('teacher_course_subscriptions.status', $status);
                $filteredqry->where('teacher_course_subscriptions.status', $status);
            }

            if ($teacher_id > 0) {
                $chaptersqry->where('teacher_course_subscriptions.teacher_id', $teacher_id);

                $filteredqry->where('teacher_course_subscriptions.teacher_id', $teacher_id);
            }

            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'teacher_course_subscriptions.id';
            }
            if (empty($dir)) {
                $dir = 'ASC';
            }

            $courses = $chaptersqry->select('teacher_course_subscriptions.*', 'courses.course_name', 'users.name')->skip($start)->take($length)
                ->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('teacher_course_subscriptions.*', 'courses.course_name', 'users.name')->count();

            $totalDataqry = CourseTeacherSubscriptions::orderby('teacher_course_subscriptions.id', 'asc');
            $totalData = $totalDataqry->select('teacher_course_subscriptions.id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($courses)) {
                foreach ($courses as $post) {
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
            return redirect('/institute/login');
        }

    }


       //Course Document
       public function viewCourseDocument()
       {
           if (Auth::check()) {  
  
            $user_type = Auth::User()->user_type;
            $school_id = Auth::User()->id; 

            if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 

            $teachers = User::where('user_type','TEACHER')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id')->get();

            }
               return view('institute.courseDocument')->with('teachers',$teachers);
           } else {
               return redirect('/institute/login');
           }
       }
   
       public function getCourseDocument(Request $request)
       {
   
           if (Auth::check()) {
               $input = $request->all();
               $start = $input['start'];
               $length = $input['length'];
   
               $input = $request->all();
               $columns = $request->get('columns');
               $dir = $request->input('order.0.dir');
               $order = $request->input('order.0.column');
   
               $status = $request->get('status'); 
               $doc_type = $request->get('type'); 
  
               $teacher_id = $request->get('teacher_id',0);
  
   
               $chaptersqry = CourseDocuments::leftjoin('users', 'users.id', 'course_documents.user_id')->leftjoin('courses', 'courses.id', 'course_documents.course_id')->leftjoin('coursesubjects', 'coursesubjects.id', 'course_documents.course_subject_id')->where('course_documents.id', '>', 0);
               $filteredqry = CourseDocuments::leftjoin('users', 'users.id', 'course_documents.user_id')->leftjoin('courses', 'courses.id', 'course_documents.course_id')->leftjoin('coursesubjects', 'coursesubjects.id', 'course_documents.course_subject_id')->where('course_documents.id', '>', 0); 

               $school_id = Auth::User()->id; 
 
               $user_type = Auth::User()->user_type;

                $school_id = Auth::User()->id; 

                if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
                  $chaptersqry->where('users.user_type','TEACHER')->where('users.school_college_id',$school_id);
                  $filteredqry->where('users.user_type','TEACHER')->where('users.school_college_id',$school_id);
                }
   
               if (count($columns) > 0) {
                   foreach ($columns as $key => $value) {
                       if (!empty($value['name']) && !empty($value['search']['value'])) {
                           if ($value['name'] == 'course_documents.status') {
                               $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                               $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                           } else {
                               $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                               $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                           }
                       }
                   }
               }
   
               if(!empty($status)){
                   $chaptersqry->where('course_documents.status',$status);
                   $filteredqry->where('course_documents.status',$status);
               } 
  
               if(!empty($doc_type)){
                  $chaptersqry->where('course_documents.document_type',$doc_type);
                  $filteredqry->where('course_documents.document_type',$doc_type);
              } 
  
               if($teacher_id>0){
                  $chaptersqry->where('course_documents.user_id',$teacher_id);
  
                  $filteredqry->where('course_documents.user_id',$teacher_id);
              }
    
               if (!empty($order)) {
                   $orderby = $columns[$order]['name'];
               } else {
                   $orderby = 'course_documents.id';
               }
               if (empty($dir)) {
                   $dir = 'ASC';
               }
                
               $courses = $chaptersqry->select('course_documents.*','courses.course_name','coursesubjects.subject_name','users.name')->skip($start)->take($length)
                   ->orderby($orderby, $dir)->get();
               $filters = $filteredqry->select('course_documents.id')->count();
   
               $totalDataqry = CourseDocuments::orderby('course_documents.id', 'asc');
               $totalData = $totalDataqry->select('course_documents.id')->count();
   
               $totalFiltered = $totalData;
               if (!empty($filters)) {
                   $totalFiltered = $filters;
               }
   
               $data = [];
               if (!empty($courses)) {
                   foreach ($courses as $post) {
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
               return redirect('/institute/login');
           }
   
       }
   
       public function postCourseDocument(Request $request)
       {
           if (Auth::check()) {
               $id = $request->id;
               $status = $request->status; 
   
               $validator = Validator::make($request->all(), [
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
                   $subject = CourseDocuments::find($id);
  
                   $subject->status = $status; 
   
                   $subject->save();
  
               } 
               return response()->json(['status' => 'SUCCESS', 'message' => 'Subject Status Updated Successfully']);
           } else {
               return redirect('/institute/login');
           }
       }
  
  
       public function editCourseDocument(Request $request)
       { 
           if (Auth::check()) {
               $subjects = CourseDocuments::where('id', $request->code)->get();
   
               if ($subjects->isNotEmpty()) {
                   return response()->json(['status' => 'SUCCESS', 'data' => $subjects[0], 'message' => 'Document Detail']);
               } else {
                   return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Document Detail']);
               }
           } else {
               return redirect('/institute/login');
           }
       }


        //View online session
        public function viewOnlineSession()
        {
            if (Auth::check()) {  
                $user_type = Auth::User()->user_type;
                $school_id = Auth::User()->id; 
    
                if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
   
                $teachers = User::where('user_type','TEACHER')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id')->get();
   
                }

                return view('institute.onineSessions')->with('teachers',$teachers);
            } else {
                return redirect('/institute/login');
            }
        }
    
        public function getOnlineSession(Request $request)
        {
    
            if (Auth::check()) {
                $input = $request->all();
                $start = $input['start'];
                $length = $input['length'];
    
                $input = $request->all();
                $columns = $request->get('columns');
                $dir = $request->input('order.0.dir');
                $order = $request->input('order.0.column');
    
                $status = $request->get('status'); 
                $teacher_id = $request->get('teacher_id',0);
                $student_id = $request->get('student_id',0);

    
                $chaptersqry = OnlineSessions::leftjoin('users', 'users.id', 'online_sessions.mentor_id')->leftjoin('courses', 'courses.id', 'online_sessions.course_id')->leftjoin('coursesubjects', 'coursesubjects.id', 'online_sessions.subject_topic_id')->where('online_sessions.id', '>', 0);
                $filteredqry =OnlineSessions::leftjoin('users', 'users.id', 'online_sessions.mentor_id')->leftjoin('courses', 'courses.id', 'online_sessions.course_id')->leftjoin('coursesubjects', 'coursesubjects.id', 'online_sessions.subject_topic_id')->where('online_sessions.id', '>', 0); 

                $user_type = Auth::User()->user_type;

                $school_id = Auth::User()->id; 

                if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
                   $chaptersqry->where('users.user_type','TEACHER')->where('users.school_college_id',$school_id);
                   $filteredqry->where('users.user_type','TEACHER')->where('users.school_college_id',$school_id);
                }
    
                if (count($columns) > 0) {
                    foreach ($columns as $key => $value) {
                        if (!empty($value['name']) && !empty($value['search']['value'])) {
                            if ($value['name'] == 'online_sessions.status') {
                                $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            } else {
                                $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            }
                        }
                    }
                }
    
                if(!empty($status)){
                    $chaptersqry->where('online_sessions.status',$status);
                    $filteredqry->where('online_sessions.status',$status);
                } 
   
                if($teacher_id>0){
                   $chaptersqry->where('online_sessions.mentor_id',$teacher_id);
   
                   $filteredqry->where('online_sessions.mentor_id',$teacher_id);
               }

             
     
                if (!empty($order)) {
                    $orderby = $columns[$order]['name'];
                } else {
                    $orderby = 'online_sessions.id';
                }
                if (empty($dir)) {
                    $dir = 'ASC';
                }
                 
                $courses = $chaptersqry->select('online_sessions.*','courses.course_name','coursesubjects.subject_name','users.name')->skip($start)->take($length)
                    ->orderby($orderby, $dir)->get();
                    
                $filters = $filteredqry->select('online_sessions.*','courses.course_name','coursesubjects.subject_name','users.name')->count();
    
                $totalDataqry = OnlineSessions::orderby('online_sessions.id', 'asc');
                $totalData = $totalDataqry->select('online_sessions.*','courses.course_name','coursesubjects.subject_name','users.name')->count();
    
                $totalFiltered = $totalData;
                if (!empty($filters)) {
                    $totalFiltered = $filters;
                }
    
                $data = [];
                if (!empty($courses)) {
                    foreach ($courses as $post) {
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
                return redirect('/institute/login');
            }
    
        }


         //Student Course Subscription
         public function viewStudentCourseSubscription()
         {
             if (Auth::check()) {  
                $teachers = User::where('user_type','TEACHER')->where('status','ACTIVE')->select('name','id')->get();

              

                $user_type = Auth::User()->user_type;
             $school_id = Auth::User()->id; 
 
             if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 

             $student = User::where('user_type','STUDENT')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id','reg_no')->get();

             }

                 return view('institute.studentCourseSubscription')->with('teachers',$teachers)->with('student',$student);
             } else {
                 return redirect('/institute/login');
             }
         }
     
         public function getStudentCourseSubscription(Request $request)
         {
     
             if (Auth::check()) {
                 $input = $request->all();
                 $start = $input['start'];
                 $length = $input['length'];
     
                 $input = $request->all();
                 $columns = $request->get('columns');
                 $dir = $request->input('order.0.dir');
                 $order = $request->input('order.0.column');
     
                 $status = $request->get('status'); 
                 $teacher_id = $request->get('teacher_id',0);
                 $student_id = $request->get('student_id',0);
     
                 $chaptersqry = CourseStudentSubscriptions::leftjoin('users as teacher', 'teacher.id', 'course_student_subscriptions.mentor_id')
                 ->leftjoin('users as student', 'student.id', 'course_student_subscriptions.student_id')
                 ->leftjoin('courses', 'courses.id', 'course_student_subscriptions.course_id')->where('course_student_subscriptions.id', '>', 0);

                 $filteredqry = CourseStudentSubscriptions::leftjoin('users as teacher', 'teacher.id', 'course_student_subscriptions.mentor_id')
                 ->leftjoin('users as student', 'student.id', 'course_student_subscriptions.student_id')
                 ->leftjoin('courses', 'courses.id', 'course_student_subscriptions.course_id')->where('course_student_subscriptions.id', '>', 0); 
     
                 $user_type = Auth::User()->user_type;

                 $school_id = Auth::User()->id; 
                 
                 if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
                    $chaptersqry->where('student.user_type','STUDENT')->where('student.school_college_id',$school_id);
                    $filteredqry->where('student.user_type','STUDENT')->where('student.school_college_id',$school_id);
                 }

                 if (count($columns) > 0) {
                     foreach ($columns as $key => $value) {
                         if (!empty($value['name']) && !empty($value['search']['value'])) {
                             if ($value['name'] == 'course_student_subscriptions.status') {
                                 $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                                 $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                             } else {
                                 $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                                 $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                             }
                         }
                     }
                 }
     
                 if(!empty($status)){
                     $chaptersqry->where('course_student_subscriptions.plan_status',$status);
                     $filteredqry->where('course_student_subscriptions.plan_status',$status);
                 } 
    
                 if($teacher_id>0){
                    $chaptersqry->where('course_student_subscriptions.mentor_id',$teacher_id);
    
                    $filteredqry->where('course_student_subscriptions.mentor_id',$teacher_id);
                }

                if($student_id>0){

                    $chaptersqry->where('course_student_subscriptions.student_id',$student_id);
    
                    $filteredqry->where('course_student_subscriptions.student_id',$student_id);
                }
      
                 if (!empty($order)) {
                     $orderby = $columns[$order]['name'];
                 } else {
                     $orderby = 'course_student_subscriptions.id';
                 }
                 if (empty($dir)) {
                     $dir = 'ASC';
                 }
                  
                 $courses = $chaptersqry->select('course_student_subscriptions.*','courses.course_name','teacher.name as teacher_name','student.name as student_name','student.reg_no as student_regno')->skip($start)->take($length)
                     ->orderby($orderby, $dir)->get();
                     
                 $filters = $filteredqry->select('course_student_subscriptions.*','courses.course_name','teacher.name as teacher_name','student.name as student_name','student.reg_no as student_regno')->count();
     
                 $totalDataqry = CourseStudentSubscriptions::orderby('course_student_subscriptions.id', 'asc');
                 $totalData = $totalDataqry->select('course_student_subscriptions.*','courses.course_name')->count();
     
                 $totalFiltered = $totalData;
                 if (!empty($filters)) {
                     $totalFiltered = $filters;
                 }
     
                 $data = [];
                 if (!empty($courses)) {
                     foreach ($courses as $post) {
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
                 return redirect('/institute/login');
             }
     
         }

        //Student Course Subscription
        public function viewStudentCoursePayment()
        {
            if (Auth::check()) {  

                $school_id = Auth::User()->id; 
         
                $user_type = Auth::User()->user_type;

                if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 

                $student = User::where('user_type','STUDENT')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id','reg_no')->get();

                }

                return view('institute.studentCoursePayment')->with('student',$student);
            } else {
                return redirect('/institute/login');
            }
        }

        public function getStudentCoursePayment(Request $request)
        {

            if (Auth::check()) {
                $input = $request->all();
                $start = $input['start'];
                $length = $input['length'];

                $input = $request->all();
                $columns = $request->get('columns');
                $dir = $request->input('order.0.dir');
                $order = $request->input('order.0.column');

                $status = $request->get('status'); 
                $student_id = $request->get('student_id',0);


                $chaptersqry = CourseStudentPayment::leftjoin('users as school', 'school.id', 'course_student_payments.school_college_id')
                 ->leftjoin('users as student', 'student.id', 'course_student_payments.user_id')->where('course_student_payments.id', '>', 0);
                $filteredqry = CourseStudentPayment::leftjoin('users as school', 'school.id', 'course_student_payments.school_college_id')
                ->leftjoin('users as student', 'student.id', 'course_student_payments.user_id')->where('course_student_payments.id', '>', 0); 

                $user_type = Auth::User()->user_type;

                         $school_id = Auth::User()->id; 
                         
                         if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
                            $chaptersqry->where('student.user_type','STUDENT')->where('student.school_college_id',$school_id);
                            $filteredqry->where('student.user_type','STUDENT')->where('student.school_college_id',$school_id);
                         }

                if (count($columns) > 0) {
                    foreach ($columns as $key => $value) {
                        if (!empty($value['name']) && !empty($value['search']['value'])) {
                            if ($value['name'] == 'course_student_payments.status') {
                                $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            } else {
                                $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            }
                        }
                    }
                }

                if(!empty($status)){
                    $chaptersqry->where('course_student_payments.plan_status',$status);
                    $filteredqry->where('course_student_payments.plan_status',$status);
                } 


               if($student_id>0){

                   $chaptersqry->where('course_student_payments.user_id',$student_id);

                   $filteredqry->where('course_student_payments.user_id',$student_id);
               }

                if (!empty($order)) {
                    $orderby = $columns[$order]['name'];
                } else {
                    $orderby = 'course_student_payments.id';
                }
                if (empty($dir)) {
                    $dir = 'ASC';
                }
                 
                $courses = $chaptersqry->select('course_student_payments.*','school.name as school_name','student.reg_no as student_regno','student.name as student_name')->skip($start)->take($length)
                    ->orderby($orderby, $dir)->get();
                    
                $filters = $filteredqry->select('course_student_payments.*','school.name as school_name','student.reg_no as student_regno','student.name as student_name')->count();

                $totalDataqry = CourseStudentPayment::orderby('course_student_payments.id', 'asc');
                $totalData = $totalDataqry->select('course_student_payments.*','school.name as school_name','student.reg_no as student_regno','student.name as student_name')->count();

                $totalFiltered = $totalData;
                if (!empty($filters)) {
                    $totalFiltered = $filters;
                }

                $data = [];
                if (!empty($courses)) {
                    foreach ($courses as $post) {
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
                return redirect('/institute/login');
            }

        }


        //View online session payment
        public function viewOnlineSessionPayment()
        {
            if (Auth::check()) {  

                $school_id = Auth::User()->id; 
         
                $user_type = Auth::User()->user_type;

                if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 

                    $teachers = User::where('user_type','TEACHER')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id')->get();

                $student = User::where('user_type','STUDENT')->where('school_college_id',$school_id)->where('status','ACTIVE')->select('name','id','reg_no')->get();

                }


                return view('institute.onineSessionsPayment')->with('teachers',$teachers)->with('student',$student);
            } else {
                return redirect('/institute/login');
            }
        }

        public function getOnlineSessionPayment(Request $request)
        {

            if (Auth::check()) {
                $input = $request->all();
                $start = $input['start'];
                $length = $input['length'];

                $input = $request->all();
                $columns = $request->get('columns');
                $dir = $request->input('order.0.dir');
                $order = $request->input('order.0.column');

                $status = $request->get('status'); 
                $teacher_id = $request->get('teacher_id',0);
                $student_id = $request->get('student_id',0);


                $chaptersqry = OnlineSessionPayment::leftjoin('users as teacher', 'teacher.id', 'online_sessions_payment.mentor_id')
                        ->leftjoin('users as student', 'student.id', 'online_sessions_payment.user_id')->leftjoin('online_sessions', 'online_sessions.id', 'online_sessions_payment.session_id')->where('online_sessions_payment.id', '>', 0);
                $filteredqry = OnlineSessionPayment::leftjoin('users as teacher', 'teacher.id', 'online_sessions_payment.mentor_id')
                ->leftjoin('users as student', 'student.id', 'online_sessions_payment.user_id')->leftjoin('online_sessions', 'online_sessions.id', 'online_sessions_payment.session_id')->where('online_sessions_payment.id', '>', 0); 

                $school_id = Auth::User()->id; 
         
                $user_type = Auth::User()->user_type;


                if ($user_type == "SCHOOL" || $user_type == "COLLEGE") { 
                $chaptersqry->where('teacher.school_college_id',$school_id)->orWhere('student.school_college_id',$school_id);
                $filteredqry->where('teacher.school_college_id',$school_id)->orWhere('student.school_college_id',$school_id);
                }

                if (count($columns) > 0) {
                    foreach ($columns as $key => $value) {
                        if (!empty($value['name']) && !empty($value['search']['value'])) {
                            if ($value['name'] == 'online_sessions_payment.status') {
                                $chaptersqry->where($value['name'], 'like', $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            } else {
                                $chaptersqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                                $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            }
                        }
                    }
                }

                if(!empty($status)){
                    $chaptersqry->where('online_sessions_payment.status',$status);
                    $filteredqry->where('online_sessions_payment.status',$status);
                } 

                if($teacher_id>0){
                   $chaptersqry->where('online_sessions_payment.mentor_id',$teacher_id);

                   $filteredqry->where('online_sessions_payment.mentor_id',$teacher_id);
               }

               if($student_id>0){
                   $chaptersqry->where('online_sessions_payment.user_id',$student_id);

                   $filteredqry->where('online_sessions_payment.user_id',$student_id);
               }
             

                if (!empty($order)) {
                    $orderby = $columns[$order]['name'];
                } else {
                    $orderby = 'online_sessions_payment.id';
                }
                if (empty($dir)) {
                    $dir = 'ASC';
                }
                 
                $courses = $chaptersqry->select('online_sessions_payment.*','online_sessions.session_title','teacher.name as teacher_name','student.name as student_name','student.reg_no as student_regno')->skip($start)->take($length)
                    ->orderby($orderby, $dir)->get();
                    
                $filters = $filteredqry->select('online_sessions_payment.*','online_sessions.session_title','teacher.name as teacher_name','student.name as student_name','student.reg_no as student_regno')->count();

                $totalDataqry = OnlineSessionPayment::orderby('online_sessions_payment.id', 'asc');
                $totalData = $totalDataqry->select('online_sessions_payment.*','online_sessions.session_title','teacher.name as teacher_name','student.name as student_name','student.reg_no as student_regno')->count();

                $totalFiltered = $totalData;
                if (!empty($filters)) {
                    $totalFiltered = $filters;
                }

                $data = [];
                if (!empty($courses)) {
                    foreach ($courses as $post) {
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
                return redirect('/institute/login');
            }

        }

}