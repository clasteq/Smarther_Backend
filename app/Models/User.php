<?php

namespace App\Models;
use App\Models\AdminSetting;
use App\Http\Controllers\CommonController;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	
    protected $appends = [ 'is_profile_image', 'enc_id', 'is_country_name','is_state_name','is_district_name' ,'is_country_detail'];

    public static $monthyear;
    public static $class_id;
    public static $section_id;
    public static $exam_id;
    public static $subject_id;  
    
    public function attendance(){
        //if(self::$class_id > 0 && self::$section_id > 0) {
            return $this->hasOne('App\Models\StudentsAttendance','user_id','id')->where('monthyear', self::$monthyear)
                ->where('class_id', self::$class_id)->where('section_id', self::$section_id);
        //}   
    }

    public function dailyattendance(){
        //if(self::$class_id > 0 && self::$section_id > 0) {
            return $this->hasOne('App\Models\StudentsDailyAttendance','user_id','id')->where('monthyear', self::$monthyear)
                ->where('class_id', self::$class_id)->where('section_id', self::$section_id);
        //}   
    }

    public function teacherattendance(){ 
        return $this->hasOne('App\Models\TeacherAttendance','user_id','id')->where('monthyear', self::$monthyear); 
    }

    public function teacherdailyattendance(){ 
        return $this->hasOne('App\Models\TeachersDailyAttendance','user_id','id')->where('monthyear', self::$monthyear); 
    }

    /*public function userdetails() {
        return $this->hasOne('App\UserDetails','user_id','id');
    }*/

    public function userdetails() {
        return $this->hasOne('App\Models\Student','user_id','id');
    }

    public function students() {
        return $this->hasMany('App\Models\Student','user_id','id');
    }

    public function teachers() {
        return $this->hasOne('App\Models\Teacher','user_id','id');
    }

    public function marksentry(){
        //if(self::$class_id > 0 && self::$section_id > 0) {
            return $this->hasOne('App\Models\MarksEntry','user_id','id')->with('marksentryitems')
                ->where('monthyear', self::$monthyear)
                ->where('class_id', self::$class_id)->where('section_id', self::$section_id)
                ->where('exam_id', self::$exam_id);
                //->where('subject_id', self::$subject_id)
        //}   
    }
    

   

    public function getIsCountryNameAttribute()    {
        
        return DB::table('countries')->where('id', $this->country)->value('name');
        
    } 

    public function getIsCountrydetailAttribute()    {
        
        return DB::table('countries')->where('id', $this->country)->value('id','name');
        
    } 


    public function getIsStateNameAttribute()    {
        
        return DB::table('states')->where('id', $this->state_id)->value('state_name');
        
    } 

    public function getIsDistrictNameAttribute()    {
        
        return DB::table('districts')->where('id', $this->city_id)->value('district_name');
        
    } 

	public function getEncIdAttribute()
    {
        $encodeid = $this->id; 
        $encode = base64_encode(json_encode(array('id'=>$encodeid)));
                        
        return $encode;
    }

    public function getJoinedDateAttribute($value) {
        return date('Y-m-d', strtotime($value));
    }

	public function getIsProfileImageAttribute()
    {
       $pictures =$this->profile_image;  
   
        $profile_picture = '';
        if (!empty($pictures)) {           
                $profile_picture = config("constants.APP_IMAGE_URL"). 'uploads/userdocs/' . $this->profile_image;
            
        }else{
            $profile_picture = config("constants.APP_IMAGE_URL"). 'image/default.png';
		} 
        return $profile_picture;
    }

    public static function getUserProfileImageAttribute($user_id)
    {
        $pictures = DB::table('users')->where('id', $user_id)->value('profile_image');  
   
        $profile_picture = '';
        if (!empty($pictures)) {           
                $profile_picture = config("constants.APP_IMAGE_URL"). 'uploads/userdocs/' . $pictures;
            
        }else{
            $profile_picture = config("constants.APP_IMAGE_URL"). 'image/default.png';
        }
        return $profile_picture;
    }

	
	
	/*  Check the API Token Expiry for the User
    Fn Name: updateToken
    return: true / false
    */
    public static function updateToken($user_id) {
		
		
		/*  Check the Admin maintenance status
			Fn Name: getAdminMaintenance
			return: Error Info / empty
			*/
	 
		$maintenance_check = AdminSetting::getAdminMaintenance(); 
		      if (!empty($maintenance_check)) {
                    return $maintenance_check;
                }
				
       $current_date = date('Y-m-d H:i:s');
       $expiry_days = 30;
        
        $user = User::find($user_id);
        $user->api_token_expiry = date('Y-m-d', strtotime($current_date . ' + ' . $expiry_days . ' days'));
        $user->updated_at = $current_date;
        $user->save();
		return array('status' => 1, 'message' => 'Success');
    }
	
	public static function random_strings($length_of_string=0) { 
        if($length_of_string == 0) $length_of_string = 5;
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
        return substr(str_shuffle($str_result), 0, $length_of_string); 
    } 

    /*  Check the API Token Expiry for the User
    Fn Name: checkTokenExpiry
    return: true / false
    */
    public static function checkTokenExpiry($userid, $api_token) {
        $user = User::where('id', $userid)->where('api_token', $api_token)->limit(1)->get();
        if($user->isNotEmpty()) {
            if(isset($user[0])) {
                $user = $user[0];
                $expiry_date = $user->api_token_expiry;
                $date = date('Y-m-d H:i:s');

                $userstatus = $user->status;

                if(strtotime($expiry_date) <= strtotime($date)) {
                    if($user->user_role == 'GUESTUSER') {
                        $def_expiry_after =  CommonController::getDefExpiry();
                        $user->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                        $user->save(); 
                        return array('status' => 1, 'message' => 'Success');                   
                    }
                    //return array('status' => 8, 'message' => 'Token Expired');
                    $def_expiry_after =  CommonController::getDefExpiry();
                    $user->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                    $user->save(); 
                    return array('status' => 1, 'message' => 'Success');
                }   else { 
                    if($userstatus == 'REJECTED') {
                        return array('status' => 4, 'message' => 'Account Rejected by Admin');
                    }
                    if($userstatus == 'INACTIVE') { // 5
                        return array('status' => 3, 'message' => 'Account is In-Activated by Admin');
                    }
                    $def_expiry_after =  CommonController::getDefExpiry();
                    $user->api_token_expiry = date('Y-m-d H:i:s', strtotime('+'.$def_expiry_after.' months'. $date));
                    $user->save(); 
                    return array('status' => 1, 'message' => 'Success');
                }
            }   else {
                return array('status' => 0, 'message' => 'Invalid Details');
            }
        }   else {
            return array('status' => 3, 'message' => 'Invalid User / Token / Device Changed. Logout and Login Again');
        }
    }
}
