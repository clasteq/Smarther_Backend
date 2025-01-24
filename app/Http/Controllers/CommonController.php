<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Mail;
use DateTime;
use DatePeriod;
use DateInterval;
use Log;

use App\Jobs\UserOTPEmailSender;
use App\Models\SMSCredits;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class CommonController extends Controller
{
    public static $code_prefix = 'SC';
    public static $prefix = "SP";
    public static $page_limit = 15;   

    public static $SMS_AUTH_KEY = "421723A6qHzgsvk6657134fP1";

    public function __construct() {
      $admin_settings = DB::table('admin_settings')->where('id',1)->get();
      if($admin_settings->isNotEmpty() && isset($admin_settings[0])) {
        self::$page_limit = (!empty(trim($admin_settings[0]->def_pagination_limit))) ? $admin_settings[0]->def_pagination_limit : 10; 
      }
    }

      /*
     * To check all the mandatory parameters are given as the input for the api call
     * Fn Name: checkParams
     * return: Error Info / empty
     */
    public static function checkParams($input = [], $requiredParams = [], $request = [])
    {
        $error = '';

        if (empty($input) || empty($requiredParams)) {
            $error = "Required parameters are missing 1";
            return $error;
        }
       
        if (count($input) > 0 && count($requiredParams) > 0) {
            foreach ($requiredParams as $key => $value) {
                if ($value == 'api_token') {
                    $api_token = $request->header('x-api-key');
                    if (empty($api_token)) {
                        $error .= ' Api key' . ', ';
                    }
                } else if (! isset($input[$value])) {
                    $error .= $value . ', ';
                }
            }
            if (! empty($error)) {
                $error .= ' parameters are missing';
            }
        } else {
            $error = "Required parameters are missing";
        }
        return $error;
    }

    // Get the Default Expiry time for the user in Months
    public static function getDefExpiry() {
        $def_expiry_after = '';
        $admin_settings = DB::table('admin_settings')->where('id',1)->get();
        if($admin_settings->isNotEmpty() && isset($admin_settings[0])) {
            $def_expiry_after = $admin_settings[0]->def_expiry_after;
        }
        if(empty($def_expiry_after) || ($def_expiry_after == 0)) {
            $def_expiry_after = 1;
        }

        return $def_expiry_after;
    }

    // Get the Site ON / OFF Status
    public static function getSiteStatus() {
        $site_on_off = '';
        $admin_settings = DB::table('admin_settings')->where('id',1)->get();
        if($admin_settings->isNotEmpty() && isset($admin_settings[0])) {
            $site_on_off = $admin_settings[0]->site_on_off;
        }
        if(empty($site_on_off) || empty($site_on_off)) {
            $site_on_off = "OFF";
        }

        return $site_on_off;
    }

    public static function getUserDetails($user_id) { 
        $user = User::with('userdetails')->where('id', $user_id)->select('id', 'reg_no', 'user_type', 'state_id', 'city_id', 
            'country','admission_no','name', 'last_name', 'email', 'gender', 'dob', 'country_code', 'mobile','code_mobile', 
            'mobile1','codemobile1', 'emergency_contact_no', 'last_login_date', 'last_app_opened_date', 'user_source_from', 'api_token', 'api_token_expiry', 'is_password_changed',
            'notification_status', 'joined_date', 'profile_image', 'status')->first(); 
        
        return $user;
    }

    public static function push_notification($user_id, $type_no, $type_id, $fcmMsg, $no_notify=0, $fcm_id='', $post_id=0, $notify_datetime='')    {
         
      $user = User::find($user_id);  
      if(empty($notify_datetime)) {
        $notify_datetime = date('Y-m-d H:i:s');
      }
        /*$notification_status = $user->notification_status;
                    
        $type_no :  1 : Attendance to User 
                    2 : Homework given
                    3 : Test given
                    4 : post communication
                    5 : post sms communication
        */

        $ex_chk = 0;
      //if($no_notify == 0) {

        $ex_chk = DB::table('notifications')->where(['user_id'=>$user_id, 'type_no'=>$type_no, 
                'type_id'=>$type_id,'post_id'=>$post_id])->get();
        if($ex_chk->isNotEmpty()) {
            $ex_chk = 1;
        } else {
            $ex_chk = 0;
        }
        if($ex_chk == 0) {
            DB::table('notifications')->insert([
              'user_id'=>$user_id,
              'type_no'=>$type_no,
              'type_id'=>$type_id,
              'fcm_id'=>$user->fcm_id,
              'post_id'=>$post_id,
              'title'=>$fcmMsg['fcm']['notification']['title'],
              'message'=>$fcmMsg['fcm']['notification']['body'],
              'created_at'=>date('Y-m-d H:i:s'),
              'notify_date'=>$notify_datetime,
            ]);
        }
        
      //}

      $message = $fcmMsg['fcm']['notification']['body'];
      $title = $fcmMsg['fcm']['notification']['title'];

      $fcmMsg['fcm']['notification']['body'] = $message;
      $fcmMsg['fcm']['notification']['title'] = $title;
      $fcmMsg['fcm']['notification']['sound'] = "default";
      $fcmMsg['fcm']['notification']['color'] = "#203E78";
      $fcmMsg['fcm']['notification']['type'] = $type_no;

      $fcmMsgSend = $fcmMsg['fcm']['notification'];

      if(empty($fcm_id)) {
        $fcm_id = $user->fcm_id;
      }

      //self::pushSendUserNotification($fcm_id, $message, $title, $fcmMsgSend, $user_id, $type_no);
      if($ex_chk == 0) {
        self::pushSendUserNotificationMessage($fcm_id,$message,$title,$type_no);
      }
      /*if($notification_status == 'ON') {

        $user_id = strval($user_id);
        require 'push_notifications/vendor/autoload.php';
        $pushNotifications = new \Pusher\PushNotifications\PushNotifications(array(
          "instanceId" => "70c22fae-bd53-4d22-8aae-e8bdb97fbe91",
          //"secretKey" => "94FC307945E44E5AB1EA32AB007D2431DF3AB698B77AB0ADACE994B9B327A851",
          "secretKey" => "29C4BCCC639A2A794D4125A9E1DC0539A1C2739D12499C7213523BE7B39E6963",
        ));
 
        $publishResponse = $pushNotifications->publishToUsers(
          array($user_id),
          array(
            "fcm" => array(
              "notification" => $fcmMsg,
               "data" => $fcmMsg
            ),
            "apns" => array(
              "aps" => array(
              "alert" => $fcmMsg,
              "data" => $fcmMsg
            ))
        ));
      }*/
        return true;
    }

    public static function pushSendUserNotification($fcmid, $message, $title, $fcmMsg=[], $userid, $type_no)   {

        if (!defined('FIREBASE_KEY')) {
            define('FIREBASE_KEY', 'AAAAACB6stY:APA91bEOHE7urfjDR3ihlaG_13StzDH0OuBvjjsXjUbUN1c_tWUc1r2NbLYzzER19yFF4apU6nQbLD_9Xohz9kmYlmy2SAK2QeRhJ_Zinn5ciGMVdp-PQdKdRgjpw292Ds5lwkjo3Mdo');
        }

        //$type = isset($fcmMsg["fcm"]["notification"]["type"]) ? $fcmMsg["fcm"]["notification"]["type"] : "100"; 

        $fcmMsg['fcm']['notification']['body'] = $message;
        $fcmMsg['fcm']['notification']['title'] = $title;
        $fcmMsg['fcm']['notification']['sound'] = "default";
        $fcmMsg['fcm']['notification']['color'] = "#203E78";
        $fcmMsg['fcm']['notification']['type'] = $type_no; 

        $fcmMsgSend = $fcmMsg['fcm']['notification'];
 
         

        if ($fcmid) {
            /*$fcmMsg = array(
                'title' => $title,
                'body' => $message,
                'sound' => "default",
                'color' => "#203E78",
                'type' => $type,
                'order_id' => $order_id
            );  echo "<pre>"; print_r($fcmMsgSend); exit;*/
            $fcmMsg = $fcmMsgSend;

            $fcmFields = array(
                'to' => $fcmid,
                'priority' => 'high',
                'notification' => $fcmMsg,
                'data' => $fcmMsg,
                'content_available'=>true,
                'aps' =>array('content-available'=>1,'content_available'=>true)
            );

            $headers = array(
                'Authorization: key=' . FIREBASE_KEY,
                'Content-Type: application/json'
            );

           /* $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
            $result = curl_exec($ch);
            $err = curl_error($ch); 
            if ($err) {
                \Log::info("Notification cURL Error #:" . $err); 
                //return "cURL Error #:" . $err;
            } else {
                \Log::info("Notification Curl". $result); 
                //return $response;
            }

            curl_close($ch);*/

        }
        return true;
    }

    public static function SendSMS($postfields=[], $school_id) {
        $curl = curl_init();
        $response = '';
        $err = ''; 

        $curl = curl_init();
        $curl_data =  [
              CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($postfields),
              CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authkey: ".CommonController::$SMS_AUTH_KEY,
                "content-type: application/JSON"
              ],
        ];

        curl_setopt_array($curl,$curl_data);

        \Log::info(print_r($curl_data, true));
        /*\Log::info(print_r(json_encode($postfields), true));*/
        $response = curl_exec($curl);   \Log::info(print_r($response, true));
        $err = curl_error($curl); 
        curl_close($curl); 

        if ($err) {
          return "cURL Error #:" . $err;
        } else {

            DB::table('sms_credit_log')->insert(['post_fields'=>json_encode($postfields), 'school_id'=>$school_id, 'created_at' => date('Y-m-d H:i:s')]);

            $get_available_credits = SMSCredits::where('status','YES')->where('school_id',$school_id)
                ->orderby('id', 'desc')->first(); 
            if(!empty($get_available_credits)) {
                $available_credits = $get_available_credits->available_credits;
                if($available_credits > 0) {
                    $available_credits = $available_credits - 1;
                    SMSCredits::where('id', $get_available_credits->id)
                        ->update(['available_credits'=>$available_credits, 'updated_by' => 1, 
                                'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
          return $response;
        }
 
    }

    public static function SendOTPSMS($mobile, $otpGeneration, $school_id) {
        $template_id = '66757037d6fc05157a51df02';  
        $school_name = DB::table('users')->where('id', $school_id)->value('display_name'); 
        $recipients = [ 'mobiles'=> $mobile, 'var1' => $school_name, 'var2' => $otpGeneration];    
        $recipients_arr = [];
        $recipients_arr[] = $recipients;
        $post_fields = ['template_id' => $template_id, 'short_url' => 0, 'recipients' => $recipients_arr ]; 
         \Log::info(print_r($post_fields, true));
        CommonController::SendSMS($post_fields, $school_id);
    }

    public static function SendSMSHW($mobile, $var2, $var3, $school_id) {
        $template_id = '6673f209d6fc051a4304a653';  
        $school_name = DB::table('users')->where('id', $school_id)->value('display_name'); 
        $recipients = [ 'mobiles'=> $mobile, 'var1' => 'Parent', 'var2' => $otpGeneration];    
        $recipients_arr = [];
        $recipients_arr[] = $recipients;
        $post_fields = ['template_id' => $template_id, 'short_url' => 0, 'recipients' => $recipients_arr ]; 
         \Log::info(print_r($post_fields, true));
        CommonController::SendSMS($post_fields, $school_id);
    }

    public static function SendSMSHW1($mobile, $otpGeneration, $school_id) {
        $template_id = '667570fad6fc0515971ffdb2';  
        $school_name = DB::table('users')->where('id', $school_id)->value('display_name'); 
        $recipients = [ 'mobiles'=> $mobile, 'var1' => $school_name, 'var2' => $otpGeneration];    
        $recipients_arr = [];
        $recipients_arr[] = $recipients;
        $post_fields = ['template_id' => $template_id, 'short_url' => 0, 'recipients' => $recipients_arr ]; 
         \Log::info(print_r($post_fields, true));
        CommonController::SendSMS($post_fields, $school_id);
    }
 

    public static function SendOTPEmail($user_id, $email) { 
        $user = User::find($user_id);  
        dispatch(new UserOTPEmailSender($user, $email));
    }

    public static function generateNumericOTP($n) {

        // Take a generator string which consist of
        // all numeric digits
        $generator = "1357902468";
        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
        //$result = "1234";
        // Return result
        return $result;
    }

    public static function getSundays($y,$m){ 
        $date = "$y-$m-01";
        $first_day = date('N',strtotime($date));
        $first_day = 7 - $first_day + 1;
        $last_day =  date('t',strtotime($date));
        $days = array();
        for($i=$first_day; $i<=$last_day; $i=$i+7 ){
            $dt = "$y-$m-$i";
            $days[] = date('Y-m-d', strtotime($dt));
        }
        return  $days;
    }

    public static function getMonthsInRange($start_date,$end_date){ 
        $months = array();
        $start    = new DateTime($start_date);
        $start->modify('first day of this month');
        $end      = new DateTime($end_date);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $months[] = $dt->format("Y-m");
        }
        return $months;
    }

    
    public static function getSaturdays($y,$m){ 
        $date = "$y-$m-01";
        $first_day = date('N',strtotime($date));
        $first_day = 6 - $first_day + 1;
        $last_day =  date('t',strtotime($date));
        $days = array();
        for($i=$first_day; $i<=$last_day; $i=$i+7 ){
            $days[] = "$y-$m-$i";
        }
        return  $days;
    }

    public static function countDays($year, $month, $ignore) {
        if(count($ignore) > 0) {
            foreach($ignore as $ik=>$ig) {
                $ignore[$ik] = date('Y-m-d', strtotime($ig));
            }
        }
        $count = 0;   
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {   
            if (in_array(date("Y-m-d", $counter), $ignore) == false) {   
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }  
        return $count;
    }  
    
    // Function to get all the dates in given range
    public static function  getDatesFromRange($start, $end, $format = 'Y-m-d', $month='', $year='') {
        
        // Declare an empty array
        $array = array();
        
        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach($period as $date) {    
            if(!empty($month) && !empty($year)) {  
                if(($month == $date->format('m')) && ($year == $date->format('Y'))) {
                    $array[] = $date->format($format);
                }
            }   else   {       
                $array[] = $date->format($format);
            }
        }

        // Return the array elements
        return $array;
    } 

    /*Get Total number of working days for the academic year for which the given monthyr belongs */
    public static function  getTotalWorkingDays($monthyear) {

        $academic_start_date = $academic_end_date = '';  $total_working_days = 0; 
        $total_leave_days = 0; $total_leave_days_months = [];  $total_from_start = []; 
        $totstart_days = 0;     $totstart_leave_days = 0;   $totstart_working_days = 0;

        $first_date = $monthyear.'-01';
        $check_acadamic =  DB::table('year_start_end')->whereRaw("'".$first_date."' BETWEEN academic_start_date  and academic_end_date")->orderby('id','desc')->first();
        if(!empty($check_acadamic)) {
            $academic_start_date = $check_acadamic->academic_start_date;
            $academic_end_date = $check_acadamic->academic_end_date;

            $startdate = $academic_start_date;
            $firstdate = date('Y-m-01', strtotime($startdate));

            $begin = new DateTime( $academic_start_date );
            $end = new DateTime( $academic_end_date ); 

            $interval = DateInterval::createFromDateString('1 month');

            $period = new DatePeriod($begin, $interval, $end);
            $counter = [];
            foreach($period as $dt) {
                $counter[] = $dt->format('Y-m-01');
            } 
            
            if(count($counter)>0) {
                foreach($counter as $fds) {
                    $ld = [];
                    if($startdate == $fds) {} elseif(date('Y-m', strtotime($startdate)) == date('Y-m', strtotime($fds))) { 
                        $startdate = date('Y-m-d', strtotime('-1 day '.$startdate));
                        $ld = CommonController::getDatesFromRange($fds, $startdate);
                    } 
                    list($year, $month, $day) = explode('-', $fds);
                    $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                                    ->select('holiday_date', 'holiday_description')->get();
                    $hd = [];
                    if($holidays->isNotEmpty()) {
                        foreach($holidays as $holi) {
                            $hd[] = $holi->holiday_date;
                        }
                    }

                    $leave_days = [];
                    $leave_days = CommonController::getSundays($year, $month); 
                    $leave_days = array_merge($hd, $leave_days);
                    $leave_days = array_merge($ld, $leave_days); 

                    $leave_days = array_unique($leave_days);
                    $leave_days = array_filter($leave_days);

                    if($academic_end_date == $fds) {} elseif(date('Y-m', strtotime($academic_end_date)) == date('Y-m', strtotime($fds))) { 
                        foreach($leave_days as $lk=>$lv) {
                            if(strtotime($lv) > strtotime($academic_end_date)) {
                                unset($leave_days[$lk]);
                            }
                        }
                    }

                    //echo "<pre>"; print_r($leave_days); 
                    $total_leave_days += count($leave_days); 
                    $tot_days = date('t', strtotime($fds));
                    $total_leave_days_months[$fds]['total_days'] = $tot_days;
                    $total_leave_days_months[$fds]['total_leave_days'] = count($leave_days);
                    $total_leave_days_months[$fds]['total_working_days'] = $tot_days - count($leave_days);
                }
            }  


            $begin = new DateTime( $academic_start_date );
            $enddate = date('Y-m-d');
            $end = new DateTime( date('Y-m-d', strtotime('+1 month')) ); 

            $interval = DateInterval::createFromDateString('1 month');

            $period = new DatePeriod($begin, $interval, $end);
            $counter = [];
            foreach($period as $dt) {
                $counter[] = $dt->format('Y-m-01');
            } 
            
            if(count($counter)>0) {
                foreach($counter as $fds) {
                    $ld = [];
                    if($startdate == $fds) {} elseif(date('Y-m', strtotime($startdate)) == date('Y-m', strtotime($fds))) { 
                        $startdate = date('Y-m-d', strtotime($startdate));
                        $ld = CommonController::getDatesFromRange($fds, $startdate);
                    } 
                    list($year, $month, $day) = explode('-', $fds);
                    $holidays = DB::table('holidays')->whereRAW('YEAR(holiday_date) = "'.$year.'" ')
                                    ->whereRAW('MONTH(holiday_date) = "'.$month.'" ')
                                    ->select('holiday_date', 'holiday_description')->get();
                    $hd = [];
                    if($holidays->isNotEmpty()) {
                        foreach($holidays as $holi) {
                            $hd[] = $holi->holiday_date;
                        }
                    }

                    $leave_days = [];
                    $leave_days = CommonController::getSundays($year, $month); 
                    $leave_days = array_merge($hd, $leave_days);
                    $leave_days = array_merge($ld, $leave_days); 

                    $leave_days = array_unique($leave_days);
                    $leave_days = array_filter($leave_days); 

                    $tot_days = date('t', strtotime($fds));
                    if($enddate == $fds) {} elseif(date('Y-m', strtotime($enddate)) == date('Y-m', strtotime($fds))) { 
                        foreach($leave_days as $lk=>$lv) {
                            if(strtotime($lv) > strtotime($enddate)) {
                                unset($leave_days[$lk]);
                            }
                        }
                        $tot_days = date('d');
                    }

                    //echo "<pre>"; print_r($leave_days); 
                    $totstart_days += $tot_days;
                    $totstart_leave_days += count($leave_days);  
                    $totstart_working_days = $totstart_days - $totstart_leave_days;
  
                    $total_from_start[$fds]['total_days'] = $tot_days;
                    $total_from_start[$fds]['total_leave_days'] = count($leave_days);
                    $total_from_start[$fds]['total_working_days'] = $tot_days - count($leave_days);
                }
            }  
            
            $total_working_days = 365 - $total_leave_days;
            $data = ['total_days' => 365, 'total_leave_days' => $total_leave_days, 
                'total_working_days' => $total_working_days,
                'total_leave_days_months' => $total_leave_days_months,
                'total_from_start' => $total_from_start,
                'totstart_days'=>$totstart_days,
                'totstart_leave_days'=>$totstart_leave_days,
                'totstart_working_days'=>$totstart_working_days,
            ];
 
            return $data;
        }
    }

    /*Get Total number of Attendance days for the academic year for which the given monthyr belongs for the student */
    public static function getStudentAttendance($year, $month, $userid) {
        $data = []; 

        $user_details = DB::table('students') 
                            ->select('students.class_id', 'students.section_id')
                            ->where('students.user_id', $userid)->first();  

        if(!empty($user_details)) {
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
            //echo "<pre>"; print_r($slvs); 
            $student_leaves = DB::table('leaves')->where('student_id', $userid)
            ->where('class_id', $user_details->class_id)
            ->where('section_id', $user_details->section_id)
            ->where('status', 'APPROVED')
            ->whereRAW('(( YEAR(leave_date) = "'.$year.'" AND MONTH(leave_date) = "'.$month.'" ) OR ( YEAR(leave_end_date) = "'.$year.'" AND MONTH(leave_end_date) = "'.$month.'" ))') 
            ->select('leave_date as holiday_date', 'leave_end_date','leave_reason as holiday_description')
            ->get();
            $leave_list = [];
            if($student_leaves->isNotEmpty()) {
                foreach($student_leaves as $v) {
                    $leave_list[] = $v->holiday_date;
                }
            }

            $data['student_leaves'] = $slvs;

            $data['student_leaves_list'] = $student_leaves;

            $data['holidays'] = $holidays;
            //$data['leave_days'] = $leave_days;
            $data['leave_days'] = array_merge($hd, $leave_days);

            $noof_working_days = CommonController::countDays($year, $month, array_merge($hd, $leave_days));
            if($noof_working_days > 0) {}
            else $noof_working_days = 0;

            $data['noof_working_days'] = $noof_working_days;

            $data['student_leaves_count'] = count($slvs); //$student_leaves_count;
            $student_leaves_count = count($slvs);
            $present_days = $noof_working_days - $student_leaves_count;

            //$att_percentage = $present_days * ( 100 / $noof_working_days);

            $att_percentage = (($noof_working_days - count($slvs)) / $noof_working_days ) * 100;

            $data['att_percentage'] = number_format($att_percentage,2);

            $data['present_days'] = $present_days;

            $data['absent_days'] = $student_leaves_count;

        } 
        //echo "<pre>"; print_r($data); 
        return $data;              
    }

    // Missing letters for the string
    public static function missingletters($str) { 
        preg_match_all('/\pL\pM*|./u', $str, $results); 
        $re = $results[0];   
        $res = implode($re);
        // $len = strlen($str);
        $len = count($re);
        $num_to_remove = ceil($len * .4); // 50% removal
        for($i = 0; $i < $num_to_remove; $i++) {
            $k = 0;
            do {
              $k = rand(1, $len);
            } while($re[$k-1] == "_");
            $re[$k-1] = " _ ";
            //echo $str = str_replace($str[$k-1], "_", $str); 
            
            //echo $str[$k-1];
           
        }
            //print_r($re);

        /*$num_to_remove = ceil($len * .5); // 50% removal
        for($i = 0; $i < $num_to_remove; $i++) {
            $k = 0;
            do {
              $k = rand(1, $len);
            } while($str[$k-1] == "_");
            $str[$k-1] = "_";
            //echo $str = str_replace($str[$k-1], "_", $str); 
            
            //echo $str[$k-1];
           
        }*/
        //return $str; 

        return implode($re);
    }

    
    public static function jumbledletters($str) { 
        preg_match_all('/\pL\pM*|./u', $str, $results); 
        $re = $results[0]; 
        shuffle($re);
        $res = implode($re);  
        return $res;
    }

    public static function jumbledwords($str) { 
        preg_match_all('/\pL\pM*|./u', $str, $results); 
        $re = $results[0]; 
        $res = implode($re); 
        $re = explode(' ', $res);
        if(count($re) == 2) {
            $sap = $re[0];
            $re[0] = $re[1];
            $re[1] = $sap;
        }   else {
            shuffle($re); 
        }
        $res = implode(' ',$re); 
        /*$str = str_shuffle($str); */
        return $res;
    }

    public static function splitwords($str) { 
        $arr = explode(' ', $str); 
        $arr = array_filter($arr);
        shuffle($arr);  
        return $arr;
    }

    public static function utf8ize( $mixed ) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }
        return $mixed;
    }


    public static function str_parse($str) {
        preg_match_all('/\pL\pM*|./u', $str, $results); 
        $re = $results[0];
        shuffle($re);
        print_r($re);

    }

    public function validate_mobile($mobile) {
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }

    public function validate_email($email) {
        return preg_match('/^[A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/', $email);
    }

    public function validate_mobileemail($mobileemail) {
        $errformat = 0; $errformatmob = 0; $errformatemail = 0; $format = '';
        //if (!empty($mobileemail) && !filter_var($mobileemail, FILTER_VALIDATE_MOBILE)) {
        $match = preg_match('/^[0-9]{10}+$/', $mobileemail);

        /*if (!empty($mobileemail) && !preg_match('/^[0-9]{10}+$/', $mobileemail)) {
            $errformatmob = 1;
        }   else {
            $format = 'MOBILE';
        }*/

        //if (!empty($mobileemail) && !filter_var($mobileemail, FILTER_VALIDATE_EMAIL)) {
        /*if (!empty($mobileemail) && !preg_match('/^[A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/', $mobileemail)) {
          $errformatemail = 1;
        }   else {
            $format = 'EMAIL';
        }*/

        if (!empty($mobileemail)) {
            if(!preg_match('/^[0-9]{10}+$/', $mobileemail)) {
                $format = 'EMAIL';
            }   else {
                $format = 'MOBILE';
            }
        } else {
            $errformat = 1;
        } 

        if($errformatmob == 1 && $errformatemail == 1) {
            $errformat = 1;
        }
         
        return array($errformat, $format);
    }

    public static function gettime_ago($tm,$rcs = 0) {
       $cur_tm = time(); $dif = $cur_tm-$tm;
       $pds = array('second','minute','hour','day','week','month','year','decade');
       $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
       for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

       $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
       if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= self::gettime_ago($_tm);
       return $x;
    }

    public static function getIndianCurrency(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
    }

    public static function getDueDays($start_date) { 
        $end_date = date('Y-m-d');
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $diff = $end_ts - $start_ts;
        return  round($diff / 86400); 
    }

    public static function pushSendUserNotificationMessage($fcmid,$message,$title,$type_no){

        if($fcmid){
            $fcmMsg = [
              'title' => $title,
              'body' => $message,
            ];

            // APNs payload
            $apnsPayload = [
              'aps' => [
                'alert' => [
                  'title' => $title,
                  'body' => $message,
                ],
                'sound' => 'default',
                'type' => $type_no,
              ]
            ];

            $fcmFields = [
              'message' => [
                'token' => $fcmid,
                'notification' => $fcmMsg,
                'data' => [
                    'title' => (string)$title,
                    'body' => (string)$message,
                    'type' => (string)$type_no,
                ],
                'apns' => [
                  'payload' => $apnsPayload,
                  'headers' => [
                    'apns-priority' => '10', // High priority for APNs
                  ],
                ],
              ],
            ];

            // Adjust the path to your service account JSON file
            $credentialsPath = '/var/www/html/multischool/classtech-72e27-0feb211381de.json';

            // Create a Guzzle HTTP client instance
            $httpClient = new Client();

            // Define a callable HTTP handler using Guzzle
            $httpHandler = function ($request) use ($httpClient) {
                return $httpClient->send($request);
            };

            // Initialize credentials with the service account JSON file path and HTTP handler
            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credentialsPath,
                $httpHandler
            );

            // Fetch authentication token
            $token = $credentials->fetchAuthToken();

            if (!isset($token['access_token'])) {
                throw new \Exception('Failed to fetch access token');
            }

            $accessToken = $token['access_token'];

            // Prepare headers for FCM request
            $headers = [
              'Authorization' => 'Bearer ' . $accessToken,
              'Content-Type' => 'application/json',
            ];

            try {
                // Send notification using Guzzle HTTP client
                $response = $httpClient->post('https://fcm.googleapis.com/v1/projects/classtech-72e27/messages:send', [
                    'headers' => $headers,
                    'json' => $fcmFields,
                    'verify' => true, // Enable SSL verification in production
                ]);
                 \Log::info(print_r($response, true));
                // Output response
            } catch (\Exception $e) { \Log::info(print_r($e->getMessage(), true));
                // Handle Guzzle HTTP client exception
                //  echo 'Error: ' . $e->getMessage();
            }
        }

         

          
        return true;
    }



    public static function price_format($num,$type = 1){
        $num_full = number_format((float)$num,2,'.','');
        if (strpos($num, '.') !== false) {
        $num = substr($num_full, 0, strpos($num_full, "."));
        }
        
        if($type == 1){ // '₹10,00,000.00/-'
            $explrestunits = "" ;
        if(strlen($num)>3) {
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++) {
                // creates each of the 2's group and adds a comma to the end
                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            //$thecash = "₹".$explrestunits.$lastthree.substr($num_full, -3)."/-";

            $thecash =  $explrestunits.$lastthree; //.substr($num_full, -3);
        } else {
            //$thecash = "₹".$num.substr($num_full, -3)."/-";

            $thecash = $num; //.substr($num_full, -3);
        }
        return $thecash; // writes the final format where $currency is the currency symbol.
        }
    }
}