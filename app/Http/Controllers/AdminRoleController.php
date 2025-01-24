<?php
namespace App\Http\Controllers;
 
use App\Http\Controllers\CommonController;
use App\Models\User;
use App\Models\UserRoles; 
use App\Models\Module;
use App\Models\RoleModuleMapping;
use App\Models\Countries;
use App\Models\Teacher;
use App\Models\RoleClasses;
use App\Models\Classes;
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


class AdminRoleController extends Controller
{

    public $accepted_formats = ['jpeg', 'jpg', 'png'];

    public function getSchoolId() {
        $school_id = 0;
        if (Auth::check()) {
            if(Auth::User()->user_type == 'SCHOOL') {
                $school_id = Auth::User()->id;
            }   else {
                $school_id = Auth::User()->school_college_id;
            }
        }
        return $school_id;
    }

    public function getSchoolRoleClasses() {
        $classids = [];
        if(Auth::User()->user_type == 'SCHOOL') {
            // All classes
        } else if(Auth::User()->user_type == 'TEACHER') {
            // Teacher mapped classes
            $mapped_classes = DB::table('subject_mapping')->where('teacher_id', Auth::User()->id)
                ->where('status', 'ACTIVE')->select('class_id')->get();
            if($mapped_classes->isNotEmpty()) {
                foreach($mapped_classes as $mc) {
                    $classids[] = $mc->class_id;
                }
            }
        } else {
            // role mapped classes
            $role_id = DB::table('userroles')->where('ref_code', Auth::User()->user_type)->value('id');
            $mapped_classes = DB::table('role_classes')->where('role_id', $role_id)
                ->where('status', 'ACTIVE')->select('class_ids')->get();
            if($mapped_classes->isNotEmpty()) {
                foreach($mapped_classes as $mc) {
                    $class_ids = $mc->class_ids;
                    if(!empty($class_ids)) {
                        $classids = explode(',', $class_ids);
                    }
                }
            }
        }
        $classids = array_filter($classids);
        $classids = array_unique($classids);

        return $classids;
    }

    public function getSchoolTeacherSections($teacher_id) {
        $sectionids = [];
        if(Auth::User()->user_type == 'TEACHER') {
            // Teacher mapped classes
            $mapped_section = DB::table('subject_mapping')->where('teacher_id', Auth::User()->id)
                ->where('status', 'ACTIVE')->select('section_id')->groupby('section_id')->get();
            if($mapped_section->isNotEmpty()) {
                foreach($mapped_section as $mc) {
                    $sectionids[] = $mc->section_id;
                }
            }
        } 
        $sectionids = array_filter($sectionids);
        $sectionids = array_unique($sectionids);

        return $sectionids;
    }

    public function getApiSchoolRoleClasses($user_id) {
        $user = DB::table('users')->where('id', $user_id)->first();
        $classids = [];
        if($user->user_type == 'SCHOOL') {
            // All classes
        } else if($user->user_type == 'TEACHER') {
            // Teacher mapped classes
            $mapped_classes = DB::table('subject_mapping')->where('teacher_id', $user->id)
                ->where('status', 'ACTIVE')->select('class_id')->get();
            if($mapped_classes->isNotEmpty()) {
                foreach($mapped_classes as $mc) {
                    $classids[] = $mc->class_id;
                }
            }
        } else {
            // role mapped classes
            $role_id = DB::table('userroles')->where('ref_code', $user->user_type)->value('id');
            $mapped_classes = DB::table('role_classes')->where('role_id', $role_id)
                ->where('status', 'ACTIVE')->select('class_ids')->get();
            if($mapped_classes->isNotEmpty()) {
                foreach($mapped_classes as $mc) {
                    $class_ids = $mc->class_ids;
                    if(!empty($class_ids)) {
                        $classids = explode(',', $class_ids);
                    }
                }
            }
        }
        $classids = array_filter($classids);
        $classids = array_unique($classids);

        return $classids;
    }

	// User Roles
    /*
     * Function: viewUserRoles
     */
    public function viewUserRoles()
    {
        if (Auth::check()) {
            return view('admin.roles');
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getUserRoles
     * Datatable Load
     */
    public function getUserRoles(Request $request)
    {
        if (Auth::check()) {
            $school_id = $this->getSchoolId(); 
            $roles = UserRoles::where('school_id', $school_id)->where('ref_code','!=','TEACHER')->get();
            return Datatables::of($roles)->make(true);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: postUserRoles
     * Save into sc_userroles table
     */
    public function postUserRoles(Request $request)
    {
        if (Auth::check()) { 
            $school_id = $this->getSchoolId(); 
            $id = $request->id;
            $user_role = $request->user_role;
            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'user_role' => 'required',
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs"
                ]);
            } 

            $exroles = ['USER', 'SUPER_ADMIN', 'GUESTUSER', 'SCHOOL', 'STUDENT', 'TEACHER'];
            if(!empty($user_role)) { 
                $rolename = strtoupper($user_role);
                if (in_array($rolename, $exroles)) {
                    return response()->json([ 'status' => "FAILED", 'message' => "Role Name Already Exists."
                    ]);
                } 
            }

            if ($id > 0) {
                $exroles = UserRoles::where('id', '!=', $id)->where('school_id', $school_id)
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

            if ($id > 0) {
                $role = UserRoles::find($id);
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
            $role->status = $status; 

            $role->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'User Role Saved Successfully'
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editUserRoles(Request $request)
    {
        if (Auth::check()) {
            $role = UserRoles::where('id', $request->code)->get();
            if ($role->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $role[0],
                    'message' => 'User Role Detail'
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No User Role Detail'
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    // Role Users
    /*
     * Function: viewRoleUsers
     */
    public function viewRoleUsers()
    {
        if (Auth::check()) {
            $school_id = $this->getSchoolId(); 
            $roles = UserRoles::where('status', 'ACTIVE')->where('school_id', $school_id)->get();
            $countries = Countries::select('id', 'name')->where('status','=','ACTIVE')->get();
            return view('admin.roleusers')->with('roles', $roles)->with('countries', $countries);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getRoleUsers
     * Datatable Load
     */
    public function getRoleUsers(Request $request)
    {
        if (Auth::check()) {
            $school_id = $this->getSchoolId(); 
            $limit = $request->get('length', '10');
            $start = $request->get('start', '0');
            $dir = $request->input('order.0.dir');
            $columns = $request->get('columns');
            $order = $request->input('order.0.column');

            $users_qry = DB::table('users')->leftjoin('userroles', 'userroles.ref_code', 'users.user_type')
                ->whereNotIn('user_type', ['USER', 'SUPER_ADMIN', 'GUESTUSER', 'SCHOOL', 'STUDENT', 'TEACHER'])
                ->where('school_college_id', $school_id);

            //                ->select('users.*', 'sc_class_exam.class_exam')->orderby('users.id', 'desc')->get();
            //return Datatables::of($users)->make(true);

            if(count($columns)>0) { 
                foreach ($columns as $key => $value) { 
                    if(!empty($value['search']['value']) && !empty($value['name'])) {
                        $users_qry->where($value['name'], 'like', '%'.$value['search']['value'].'%');
                    }
                }
            }
            if(!empty($order)) {
                $orderby = $columns[$order]['name'];
            }   else {
                $orderby = 'users.id';
            }
            if(empty($dir)) {
                $dir = 'DESC';
            }
            

            $totalData = $users_qry->select('users.id')->get();
 
            if(!empty($totalData)) {
                $totalData = count($totalData);
            }
        
            $users = $users_qry->select('users.*', 'userroles.user_role')->orderBy($orderby,$dir)->offset($start)->limit($limit)->get();
            foreach ($users as $key => $value) {
                $created_date = $value->created_at;
                $my_date = strtotime($created_date);
                $created_date = date("Y-m-d h:i:a", $my_date);
                $users[$key]->created_date = $created_date;
            }

            $data = [];
            if(!empty($users))    {
                $users = $users->toArray();
                foreach ($users as $post)
                {   
                    $nestedData = [];
                    foreach($post as $k=>$v) { 
                        $nestedData[$k] = $v;
                    }
                    $data[] = $nestedData;
                }
            }
       // echo "<pre>"; print_r($data); exit;

            $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),   
                    "data"            => $data, 
                    "recordsFiltered" => intval($totalData),   
                    );
            
            echo json_encode($json_data); 

        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: postRoleUsers
     * Save into users table
     */
    public function postRoleUsers(Request $request)
    {
        if (Auth::check()) {
            $school_id = $this->getSchoolId(); 

            $id = $request->id;
            $userrole = $request->userrole;
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
            $subject = $request->get('subject_id', 0);
            $class = $request->get('class_id', 0);
            $class_tutor = $request->get('class_tutor', 0);
            $section_id = $request->get('section_id', 0);
            $father_name = $request->father_name;

            $subjectId = 0;
            $classId = 0;

            $address = $request->address;
            $password = $request->password;

            $status = $request->status;

            $validator = Validator::make($request->all(), [
                'userrole' => 'required',
                'name' => 'required',
                'mobile' => 'required', 
                'emp_no' => 'required',
                'date_of_joining' => 'required',
                'gender' => 'required',
                'dob' => 'required',
                'status' => 'required',
                'emp_no' => 'required',
                'date_of_joining' => 'required', 
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
                    'message' => "Please check your all inputs" . implode(', ', $msg)
                ]);
            }

            if (!empty($mobile)) {
                if ($id > 0) {
                    $exists = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)->whereNotIn('id', [$id])->first();
                } else {
                    $exists = DB::table('users')->where('mobile', $mobile)->where('school_college_id', $school_id)->first();
                }
            }

            if ($id > 0) {
                $emp_no_chk = DB::table('teachers')->where('emp_no', $emp_no)->where('school_id', $school_id)->whereNotIn('user_id', [$id])->first();
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
            if ($id > 0) {
                $users = User::find($id);
                $users->updated_at = date('Y-m-d H:i:s');
                $users->updated_by = Auth::User()->id;
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
                $users->created_by = Auth::User()->id;  
            }

            if(!empty($password)) {
                $users->passcode = $password;
                $password = Hash::make($password);
                $users->password = $password;
            }
            $users->school_college_id = $school_id;
            $users->user_type = $userrole;
            $users->name = $name;
            $users->email = $email;
            $users->mobile = $mobile;
            $users->last_name = $lastname;
            $users->gender = $gender;
            $users->dob = $dob;
            $country_code = DB::table('countries')->where('id', $country)->value('phonecode');
            $users->country = $country;
            $users->country_code = $country_code;
            $users->code_mobile = $country_code.$mobile;
            $users->state_id = $state_id;
            $users->city_id = $city_id;
            $users->status = $status;
 
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
                $teachers = Teacher::where('user_id', $id)->first();
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
            $teachers->status = $status;
            $teachers->save();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'User Saved Successfully'
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editRoleUsers(Request $request)
    {
        if (Auth::check()) {
            $user = User::with('teachers')->leftjoin('countries', 'countries.id', 'users.country')
                ->leftjoin('states', 'states.id', 'users.state_id')
                ->leftjoin('districts', 'districts.id', 'users.city_id')
                ->leftjoin('teachers', 'teachers.user_id', 'users.id')
                ->leftjoin('classes', 'classes.id', 'teachers.class_tutor')
                ->leftjoin('sections', 'sections.id', 'teachers.section_id') 
                ->select('users.*', 'countries.name as country_name', 'states.state_name', 'districts.district_name',
                'teachers.emp_no', 'teachers.date_of_joining', 'teachers.qualification', 'teachers.exp', 'teachers.post_details',
                'teachers.subject_id', 'teachers.class_id', 'teachers.class_tutor',  'teachers.section_id', 'teachers.father_name',
                'teachers.address')->where('user_id', $request->code)->get();
            if ($user->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $user[0],
                    'message' => 'User Detail'
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No User Detail'
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

     // viewRoleModuleMapping
    /*
     * Function: viewRoleModuleMapping
     */
    public function viewRoleModuleMapping()
    {
        if (Auth::check()) {

            return view('admin.role_module_mapping');
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getUserRolesMapping
     * Datatable Load
     */
    public function getUserRolesMapping(Request $request)
    { 
        if (Auth::check()) {
            $school_id = $this->getSchoolId(); 
            $roles = UserRoles::where('school_id', $school_id)->where('status', 'ACTIVE')->get();
            return Datatables::of($roles)->make(true);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getRoleModuleMapping
     * Datatable Load
     */
    public function getRoleModuleMapping(Request $request)
    { 
        if (Auth::check()) { 
            $mappings = RoleModuleMapping::where('ra_role_fk', '<>', 1)->groupby('ra_role_fk');
            return Datatables::of($mappings)->make(true);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function:post postRoleModuleMapping
     * Save into postRoleModuleMapping table
     */
    public function postRoleModuleMapping(Request $request)
    {
        if (Auth::check()) {

            $input = $request->all();//echo "<pre>"; print_r($input); exit;

            $role_fk = $request->role_fk;

            $validator = Validator::make($request->all(), [
                'role_fk' => 'required'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs"
                ]);
            }

            $role_fk = $request->role_fk;

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
                    $rolemodule->modified_by = Auth::User()->id;
                } else {
                    $rolemodule = new RoleModuleMapping();
                    $rolemodule->created_at = date('Y-m-d');
                    $rolemodule->created_by = Auth::User()->id;
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

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Role Saved Successfully'
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editRoleModuleMapping(Request $request)
    {
        if (Auth::check()) {
            $mapping = RoleModuleMapping::where('id', $request->code)->get();
            if ($mapping->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $mapping[0],
                    'message' => 'Mapping Detail'
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Mapping Detail'
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function viewRoleAccess(Request $request)
    {
        if (Auth::check()) {

            $modules = Module::where('parent_module_fk', '=', 0)->where('status', 1)->get();
            $role_id = $request->get('id');
            $allmodules = Module::where('status', 1)->pluck('module_name', 'id')->all();
            $role = UserRoles::find($role_id);
            $role_name = '';
            if(!empty($role)) { 
                $role_name = $role->user_role;
            } 
            return view('admin.update_role_access')->with('role_fk', $role_id)->with('role_name', $role_name);
        } else {
            return redirect('/admin/login');
        }
    }

    public function ViewTeacherRoleAccess(Request $request)
    {
        if (Auth::check()) {

            $modules = Module::where('parent_module_fk', '=', 0)->where('status', 1)->get(); 
            $allmodules = Module::where('status', 1)->pluck('module_name', 'id')->all();
            $role_name = 'TEACHER';

            $school_id = (new AdminRoleController())->getSchoolId(); 
            $role = UserRoles::where('school_id', $school_id)->where('ref_code', $role_name)->first(); 
            if(!empty($role)) { 
                $role_name = $role->user_role;
                $role_id = $role->id;
            }  else {
                $role = new UserRoles();
                $role->created_at = date('Y-m-d H:i:s'); 
                $role->ref_code = $role_name; 
                $role->school_id = $school_id;
                $role->user_role = $role_name;
                $role->status = 'INACTIVE'; 

                $role->save();

                $role_id = $role->id;
            }
            return view('admin.update_teacher_role_access')->with('role_fk', $role_id)->with('role_name', $role_name);
        } else {
            return redirect('/admin/login');
        }
    }

    // viewModules
    /*
     * Function: View All the Modules
     */
    public function viewModules()
    {
        if (Auth::check()) {
            $modules = Module::where('status', 1)->get();
            return view('admin.modules')->with('module', $modules);
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function: getModules
     * Datatable Load
     */
    public function getModules(Request $request)
    {
        if (Auth::check()) {
            //$modules = Module::where('status', '<>', 0);
            $modules = Module::leftjoin('modules as pf', 'pf.id', 'modules.parent_module_fk')
                ->where('modules.status', '<>', 0)
                ->select('modules.*', 'pf.module_name as parent_module_name');
            return Datatables::of($modules)->make(true);
        } else {
            return redirect('/admin/login');
        }
    }

    public function getModule(Request $request)
    {
        if (Auth::check()) {
            $modules = Module::where('parent_module_fk', $request->parent_id)->select(DB::raw('group_concat(id) as ids'))->first()->ids;
            return $modules;
        } else {
            return redirect('/admin/login');
        }
    }

    /*
     * Function:post postModule
     * Save into  Module table
     */
    public function postModule(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;

            $name = $request->name;
            $parent_module_fk = $request->module_id;
            $status = $request->status;
            $rank = $request->rank;
            $menu_item = $request->menu_item;
            $url = $request->url;

            $module_add = $request->get('module_add', 0);
            $module_edit = $request->get('module_edit', 0);
            $module_delete = $request->get('module_delete', 0);
            $module_view = $request->get('module_view', 0);
            $module_list = $request->get('module_list', 0);
            $module_status_update = $request->get('module_status_update', 0);

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'rank' => 'required',
                'url' => 'required',
                'menu_item' => 'required',
                'status' => 'required'
            ]);

            if ($validator->fails()) {

                $msg = $validator->errors()->all();

                return response()->json([

                    'status' => "FAILED",
                    'message' => "Please check your all inputs"
                ]);
            }

            if ($id > 0) {
                $module = Module::find($id);
            } else {
                $module = new Module();
                $module->created_at = date('Y-m-d H:i:s');
            }

            $module->module_name = $name;
            $module->url_name = $name;
            $module->menu_rank = $rank;
            $module->status = $status;
            $module->icon = '';//$icon;
            $module->url = $url;
            $module->menu_item = $menu_item;
            $module->parent_module_fk = $parent_module_fk;

            $module->module_add = $module_add;
            $module->module_edit = $module_edit;
            $module->module_delete = $module_delete;
            $module->module_view = $module_view; 
            $module->module_list = $module_list;
            $module->module_status_update = $module_status_update;

            $module->save();
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Module Saved Successfully'
            ]);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editModule(Request $request)
    {
        if (Auth::check()) {
            $module = Module::where('id', $request->code)->get();
            if ($module->isNotEmpty()) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'data' => $module[0],
                    'message' => 'Module Detail'
                ]);
            } else {
                return response()->json([
                    'status' => 'FAILED',
                    'data' => [],
                    'message' => 'No Module Detail'
                ]);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public static function getRights() {

        $url_name = $_SERVER['REQUEST_URI'];
        $dd = explode('/admin/', $url_name); //echo "<pre>"; print_r($dd);  exit; 
        if (isset($dd[1]) && !empty($dd[1])) {  

            $search = session()->get('module');  
            if(empty($search)) { $search = []; }

            $act_page = str_replace("_", " ", $dd[1]); 
            /*if($dd[3] == 'view' && isset($dd[4]) && $dd[4] == 'course') {
                if(isset($dd[6]) && !empty($dd[6])) {
                    $act_page = str_replace("_", " ", $dd[6]); 
                }   else {
                    $act_page = str_replace("_", " ", 'subjects'); 
                }
                /*   get list access for all the buttons -> packages, chapters, topics, docs, videos, tests   * /
                 
                foreach ($course_pages_access as $key => $value) { 
                    $key_page = ucwords($key); 
                    if (array_key_exists($key_page, $search)) {
                        $rights = $search[$key_page];
                        $list = $rights['list'];
                        $listclass = 'display:none';
                        if ($list == 1) {
                            $listclass = 'display:flex';
                        }  
                        $course_pages_access[$key]['list'] =  $list;
                    }
                } 
                /*  End * /   
            }  else if($dd[3] == 'users') {
                $act_page = str_replace("_", " ", 'App Users'); 
            }    else {
                $act_page = str_replace("_", " ", $dd[3]); 
            }*/
            $active_page = ucwords($act_page);  
            
            $rights = array();
            $display = '';
            $add = 0;
            $view = 0;
            $edit = 0;
            $delete = 0;
            $list = 0;
            $status_update = 0; 
            $addclass = 'display:none';
            $editclass = 'display:none';
            $viewclass = 'display:none';
            $deleteclass = 'display:none';
            $listclass = 'display:none';
            $statusupdateclass = 'display:none'; 
            $rights = ['add'=>0, 'view'=>0, 'edit'=>0, 'delete'=>0, 'list'=>0, 'status_update'=>0 ];
            if (array_key_exists($active_page, $search)) {
                $rights = $search[$active_page];
                
                $add = $rights['add'];
                $view = $rights['view'];
                $edit = $rights['edit'];
                $delete = $rights['delete'];
                $list = $rights['list'];
                $status_update = $rights['status_update']; 
                if ($add == 1) {
                    $addclass = 'display:flex';
                }
                if ($edit == 1) {
                    $editclass = 'display:flex';
                }
                if ($view == 1) {
                    $viewclass = 'display:flex';
                }
                if ($delete == 1) {
                    $deleteclass = 'display:flex';
                }
                if ($list == 1) {
                    $listclass = 'display:flex';
                }
                if ($status_update == 1) {
                    $statusupdateclass = 'display:flex';
                } 
                
            }    
        }

        $user_role = Auth::User()->user_type; // session()->get('user_role');  
        if($user_role == 'SUPER_ADMIN' || $user_role == 'SCHOOL') {
            $addclass = 'display:flex';
            $editclass = 'display:flex';
            $viewclass = 'display:flex';
            $deleteclass = 'display:flex';
            $listclass = 'display:flex';
            $statusupdateclass = 'display:flex';

            $rights = ['add'=>1, 'view'=>1, 'edit'=>1, 'delete'=>1, 'list'=>1, 'status_update'=>1 ]; 
        } 

        return array('addclass'=>$addclass, 'editclass'=>$editclass, 'viewclass'=>$viewclass, 'deleteclass'=>$deleteclass, 'listclass'=>$listclass, 'statusupdateclass'=>$statusupdateclass, 'rights'=>$rights );
    }

    // viewRoleClassMapping
    /*
     * Function: viewRoleClassMapping
     */
    public function viewRoleClassMapping()
    {
        if (Auth::check()) {
            $school_id = (new AdminRoleController())->getSchoolId(); 
            $roles = UserRoles::where('status', 'ACTIVE')->where('userroles.school_id', $school_id)->get();
            $classes = Classes::where('id', '>', 0)->where('status','ACTIVE')->orderby('position','asc')
                ->where('school_id', $school_id)->get();
            return view('admin.role_class_mapping')->with(['roles' => $roles, 'classes' => $classes]);
        } else {
            return redirect('/admin/login');
        }
    }

    /* Function: getRoleClassMapping
    Datatable Load
     */
    public function getRoleClassMapping(Request $request)
    {
        if (Auth::check()) {

            $school_id = (new AdminRoleController())->getSchoolId(); 

            $input = $request->all();
            $start = $input['start'];
            $length = $input['length']; 
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');
            $roleid = $request->get('roleid',0);
            $classid = $request->get('classid',0);

            $countriesqry = RoleClasses::leftjoin('userroles', 'userroles.id', 'role_classes.role_id')
                ->where('userroles.school_id', $school_id)
                ->select('role_classes.*', 'userroles.user_role');
            $filteredqry = RoleClasses::leftjoin('userroles', 'userroles.id', 'role_classes.role_id')
                ->where('userroles.school_id', $school_id);

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

            if($roleid > 0) {
                $countriesqry->where('role_classes.role_id',$roleid);
                $filteredqry->where('role_classes.role_id',$roleid);
            }

            if($classid > 0) {
                $countriesqry->whereRAW(' FIND_IN_SET('.$classid.', role_classes.class_ids) ');
                $filteredqry->whereRAW(' FIND_IN_SET('.$classid.', role_classes.class_ids) ');
            }

            if(!empty($status)){
                $countriesqry->where('userroles.status',$status);
                $filteredqry->where('userroles.status',$status);
            }
            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'userroles.id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            } 

            $countries = $countriesqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('userroles.id')->count();

            $totalDataqry = RoleClasses::leftjoin('userroles', 'userroles.id', 'role_classes.role_id')
                ->where('userroles.school_id', $school_id)->orderby('userroles.id', 'asc');
            $totalData = $totalDataqry->select('userroles.id')->count();

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

    /* Function: postRoleClassMapping
    Save into role_classes table
     */
    public function postRoleClassMapping(Request $request)
    {
        if (Auth::check()) {
            $id = $request->id;
            $role_id = $request->role_id;
            $class_ids = $request->class_ids; 
            $status = $request->status; 

            $validator = Validator::make($request->all(), [
                'role_id' => 'required',
                'role_id' => 'unique:role_classes,id,' . $id, 
                'class_ids' => 'required',
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
                $exists = DB::table('role_classes')->where('role_id', $role_id)->whereNotIn('id', [$id])->first();
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

            if ($id > 0) {
                $role_classes = RoleClasses::find($id);
            } else {
                $role_classes = new RoleClasses;
            } 

            $role_classes->role_id = $role_id;
            $role_classes->class_ids = $class_ids; 
            $role_classes->status = $status;

            $role_classes->save();
            return response()->json(['status' => 'SUCCESS', 'message' => 'Role Classes Saved Successfully']);
        } else {
            return redirect('/admin/login');
        }
    }

    public function editRoleClassMapping(Request $request)
    {
        if (Auth::check()) {
            $role_classes = RoleClasses::where('id', $request->code)->get();
            if ($role_classes->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $role_classes[0], 'message' => 'Role Class Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Role Class Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }
}