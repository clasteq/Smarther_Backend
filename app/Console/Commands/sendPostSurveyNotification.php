<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command; 
use DB;
use App\Http\Controllers\CommonController; 
use App\Models\User;
use Log;
class sendPostSurveyNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:postSurveyNotification';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the User communication post notification Survey';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() 
    {
        \Log::info("Cron Survey notification for post Communication !"); 
        $date = date('Y-m-d H:i:s');
        $posts = DB::table('survey')->whereIn('status', ["ACTIVE"]) 
            ->where('delete_status', 0)->where('is_cron_sent', 0)->where('expiry_date', '>=', $date)
            ->select('id', 'survey_question', 'scholar_post_type', 'scholar_receiver_end', 'staff_post_type', 
                'staff_receiver_end', 'expiry_date', 'school_id', 'created_by')
            ->orderby('survey.id', 'desc')
            ->skip(0)->take(1)
            ->get(); 
        if($posts->isNotEmpty()) {
            foreach($posts as $post) {   

                DB::table('survey')->where('id', $post->id)->update(['is_cron_sent'=>1]);

                /*  
                    1 - section ids     2 - specific scholar   3 - all  4 - group scholar
                    1 - class teacher   2 - Role, 3 - all , 4 - group , 5 - department , 6 - specific staff

                */

                $type_no = 7;
                $title = 'Survey';
                $message = $post->survey_question;
                $fcmMsg = array("fcm" => array("notification" => array(
                    "title" => $title,
                    "body" => $message,
                    "type" => $type_no,
                  )));

                /*  STAFF */
                if($post->staff_post_type == 1) { // section ids
                    $section_ids = $post->staff_receiver_end;
                    if(!empty($section_ids)) {
                        $section_ids = explode(',', $section_ids);
                        $section_ids = array_unique($section_ids);
                        $section_ids = array_filter($section_ids);
                        if(count($section_ids) > 0) {
                            foreach($section_ids as $sid) {
                                $topicname = CommonController::$topic_section_staffs.$sid;
                                CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id,$date); 
                            }
                        }
                    }
                }   else if($post->staff_post_type == 2) { // role ids
                    $role_ids = $post->staff_receiver_end;
                    if(!empty($role_ids)) {
                        $role_ids = explode(',', $role_ids);
                        $role_ids = array_unique($role_ids);
                        $role_ids = array_filter($role_ids);
                        if(count($role_ids) > 0) { 
                            foreach($role_ids as $uid) {
                                $topicname = CommonController::$topic_role_staffs.$uid;
                                CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id,$date); 
                            }
                        } 
                    }
                }   else if($post->staff_post_type == 3) { // all user ids 
                    $school_id = $post->school_id;
                    if($school_id > 0) { 
                        $topicname = CommonController::$topic_school_staffs.$school_id;
                        CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                         
                    }
                }   else if($post->staff_post_type == 4) { // group ids 
                    $group_ids = $post->staff_receiver_end;
                    if(!empty($group_ids)) {
                        $user_ids = [];
                        $group_ids = explode(',', $group_ids);
                        $group_ids = array_unique($group_ids);
                        $group_ids = array_filter($group_ids);
                        if(count($group_ids) > 0) {
                            if(count($group_ids) > 0) {
                                foreach($group_ids as $gid) {
                                    $topicname = CommonController::$topic_group_staffs.$gid;
                                    CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                                }
                            } 
                        }
                    }
                }   else if($post->staff_post_type == 5) { // department ids
                    $department_ids = $post->staff_receiver_end;
                    if(!empty($department_ids)) {
                        $department_ids = explode(',', $department_ids);
                        $department_ids = array_unique($department_ids);
                        $department_ids = array_filter($department_ids);
                        if(count($department_ids) > 0) { 
                            foreach($department_ids as $uid) {
                                $topicname = CommonController::$topic_department_staffs.$uid;
                                CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                            }
                        }
                    }
                }   else if($post->staff_post_type == 6) { // Specific Staff ids
                    $staff_ids = $post->staff_receiver_end;
                    if(!empty($staff_ids)) {
                        $staff_ids = explode(',', $staff_ids);
                        $staff_ids = array_unique($staff_ids);
                        $staff_ids = array_filter($staff_ids);
                        if(count($staff_ids) > 0) {
                            foreach($staff_ids as $uid) {
                                $topicname = CommonController::$topic_staffs.$uid;
                                CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                            }
                        }
                    }
                } 

                /*  STAFF  */   

                /* SCHOLAR Firebase Topic Push */
                if($post->scholar_post_type == 1) { // section ids
                    $section_ids = $post->scholar_receiver_end;
                    if(!empty($section_ids)) {
                        $section_ids = explode(',', $section_ids);
                        $section_ids = array_unique($section_ids);
                        $section_ids = array_filter($section_ids);
                        if(count($section_ids) > 0) {
                            foreach($section_ids as $sid) {
                                $topicname = CommonController::$topic_section.$sid;
                                CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                            }
                        }
                    }
                }   else if($post->scholar_post_type == 2) { // user ids
                    $user_ids = $post->scholar_receiver_end;
                    if(!empty($user_ids)) {
                        $user_ids = explode(',', $user_ids);
                        $user_ids = array_unique($user_ids);
                        $user_ids = array_filter($user_ids);
                        if(count($user_ids) > 0) {
                            foreach($user_ids as $uid) {
                                $topicname = CommonController::$topic_scholar.$uid;
                                CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                            }
                        }
                    }
                }   else if($post->scholar_post_type == 3) { // all user ids 
                    $school_id = $post->school_id;
                    if($school_id > 0) { 
                        $topicname = CommonController::$topic_school_scholars.$school_id;
                        CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                         
                    }
                }   else if($post->scholar_post_type == 4) { // group ids 
                    $group_ids = $post->scholar_receiver_end;
                    if(!empty($group_ids)) { 
                        $group_ids = explode(',', $group_ids);
                        $group_ids = array_unique($group_ids);
                        $group_ids = array_filter($group_ids);
                        if(count($group_ids) > 0) {
                            if(count($group_ids) > 0) {
                                foreach($group_ids as $gid) {
                                    $topicname = CommonController::$topic_group.$gid;
                                    CommonController::push_notification_topic($topicname, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                                }
                            } 
                        } 
                    }
                }
                /* SCHOLAR Firebase Topic Push */

                /*  STAFF  */   
                $users = DB::table('users')->leftjoin('teachers', 'teachers.user_id', 'users.id')
                    ->whereNotIn('users.user_type', ['SUPER_ADMIN', 'GUESTUSER', 'STUDENT', 'SCHOOL'])
                    //->where('users.fcm_id', '!=', '')
                    ->whereNotNULL('users.topics_subscribed')
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0) 
                    ->select('users.id', 'users.fcm_id', 'users.name'); 

                if($post->staff_post_type == 1) { // section ids
                    $section_ids = $post->staff_receiver_end;
                    if(!empty($section_ids)) {
                        $section_ids = explode(',', $section_ids);
                        $section_ids = array_unique($section_ids);
                        $section_ids = array_filter($section_ids);
                        if(count($section_ids) > 0) {
                            $users->leftjoin('class_teachers', 'class_teachers.teacher_id', 'users.id')
                                ->whereIn('class_teachers.section_id', $section_ids)->where('class_teachers.status', 'ACTIVE');
                        }
                    }
                }   else if($post->staff_post_type == 2) { // role ids
                    $role_ids = $post->staff_receiver_end;
                    if(!empty($role_ids)) {
                        $role_ids = explode(',', $role_ids);
                        $role_ids = array_unique($role_ids);
                        $role_ids = array_filter($role_ids);
                        if(count($role_ids) > 0) { 
                            $users->leftjoin('userroles', 'userroles.ref_code', 'users.user_type')
                                ->whereIn('userroles.id', $role_ids)->where('userroles.status', 'ACTIVE');
                            //$users->whereIn('students.user_id', $user_ids);
                        }
                    }
                }   else if($post->staff_post_type == 3) { // all user ids 

                }   else if($post->staff_post_type == 4) { // group ids 
                    $group_ids = $post->staff_receiver_end;
                    if(!empty($group_ids)) {
                        $user_ids = [];
                        $group_ids = explode(',', $group_ids);
                        $group_ids = array_unique($group_ids);
                        $group_ids = array_filter($group_ids);
                        if(count($group_ids) > 0) {
                            $groups = DB::table('communication_groups')->where('status', 'ACTIVE')->whereIn('id', $group_ids)->get();
                            if($groups->isNotEmpty()) {
                                foreach($groups as $grp) {   
                                    $uids = $grp->staff_members;
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
                            $users->whereIn('users.id', $user_ids);
                        }
                    }
                }   else if($post->staff_post_type == 5) { // department ids
                    $department_ids = $post->staff_receiver_end;
                    if(!empty($department_ids)) {
                        $department_ids = explode(',', $department_ids);
                        $department_ids = array_unique($department_ids);
                        $department_ids = array_filter($department_ids);
                        if(count($department_ids) > 0) { 
                            $users->whereIn('teachers.department_id', $department_ids); 
                        }
                    }
                }   else if($post->staff_post_type == 6) { // Specific Staff ids
                    $staff_ids = $post->staff_receiver_end;
                    if(!empty($staff_ids)) {
                        $staff_ids = explode(',', $staff_ids);
                        $staff_ids = array_unique($staff_ids);
                        $staff_ids = array_filter($staff_ids);
                        if(count($staff_ids) > 0) { 
                            $users->whereIn('users.id', $staff_ids); 
                        }
                    }
                }   

                $users = $users->where('users.school_college_id', $post->school_id)
                    ->groupby('users.id')
                    ->orderby('id', 'asc')->get();  // echo "<pre>"; print_r($users); exit;
                \Log::info(print_r($users, true));
                $pk = 0;
                if($users->isNotEmpty()) {
                    $users_count = count($users);
                    DB::table('survey')->where('id', $post->id)->update(['users_count_staff'=>$users_count]);
                    foreach($users as $user) { $pk = $pk + 1;

                        $ex = DB::table('staff_notifications')->where(['post_id' => $post->id, 'user_id' => $user->id, 'type_no' => $type_no])->first();
                        if(empty($ex)) { 

                            $type_no = 7;
                            $title = 'Survey';
                            $message = $user->name.', '.$post->survey_question; 
                            $fcmMsg = array("fcm" => array("notification" => array(
                                "title" => $title,
                                "body" => $message,
                                "type" => $type_no,
                              )));

                            CommonController::push_notification_staff_table($user->id, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date, $post->created_by); 
                        }
                    }
                    if($pk == 0 || $pk == $users_count) {
                        $exp_count = 0;
                        $exp = DB::table('staff_notifications')->where(['post_id' => $post->id, 'type_no' => $type_no])->select('id')->get();
                        if($exp->isNotEmpty()) {
                            $exp_count = count($exp);
                        }

                        DB::table('survey')->where('id', $post->id)->update(['is_mail_sent_staff'=>2, 'sent_count_staff' => $exp_count]);
                    }
                } else {
                    $exp_count = 0;
                    $exp = DB::table('staff_notifications')->where(['post_id' => $post->id, 'type_no' => $type_no])->select('id')->get();
                    if($exp->isNotEmpty()) {
                        $exp_count = count($exp);
                    }
                    DB::table('survey')->where('id', $post->id)->update(['is_mail_sent_staff'=>2, 'sent_count_staff' => $exp_count]);
                }

                /*  STAFF */


                /* SCHOLAR Notifications Table Push */
                $users = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')//->where('users.fcm_id', '!=', '')
                    ->whereNotNULL('users.topics_subscribed')
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0) 
                    ->select('users.id', 'users.fcm_id', 'users.name'); 

                if($post->scholar_post_type == 1) { // section ids
                    $section_ids = $post->scholar_receiver_end;
                    if(!empty($section_ids)) {
                        $section_ids = explode(',', $section_ids);
                        $section_ids = array_unique($section_ids);
                        $section_ids = array_filter($section_ids);
                        if(count($section_ids) > 0) {
                            $users->whereIn('students.section_id', $section_ids);
                        }
                    }
                }   else if($post->scholar_post_type == 2) { // user ids
                    $user_ids = $post->scholar_receiver_end;
                    if(!empty($user_ids)) {
                        $user_ids = explode(',', $user_ids);
                        $user_ids = array_unique($user_ids);
                        $user_ids = array_filter($user_ids);
                        if(count($user_ids) > 0) {
                            $users->whereIn('students.user_id', $user_ids);
                        }
                    }
                }   else if($post->scholar_post_type == 3) { // all user ids 

                }   else if($post->scholar_post_type == 4) { // group ids 
                    $group_ids = $post->scholar_receiver_end;
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
                            $users->whereIn('students.user_id', $user_ids);
                        }
                    }
                }   

                $users = $users->where('users.school_college_id', $post->school_id)
                    ->groupby('users.id')
                    ->orderby('id', 'asc')->get();  // echo "<pre>"; print_r($users); exit;
                \Log::info(print_r($users, true));
                $pk = 0;
                if($users->isNotEmpty()) {
                    $users_count = count($users);
                    DB::table('survey')->where('id', $post->id)->update(['users_count'=>$users_count]);
                    foreach($users as $user) { $pk = $pk + 1;

                        $ex = DB::table('notifications')->where(['post_id' => $post->id, 'user_id' => $user->id, 'type_no' => $type_no])->first();
                        if(empty($ex)) {
                            //$pk = $pk + 1;

                            $type_no = 7;
                            $title = 'Survey';
                            $message = $user->name.', '.$post->survey_question; 
                            $fcmMsg = array("fcm" => array("notification" => array(
                                "title" => $title,
                                "body" => $message,
                                "type" => $type_no,
                              )));

                            CommonController::push_notification_table($user->id, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $date); 
                        }
                    }
                    if($pk == 0 || $pk == $users_count) {
                        $exp_count = 0;
                        $exp = DB::table('notifications')->where(['post_id' => $post->id, 'type_no' => $type_no])->select('id')->get();
                        if($exp->isNotEmpty()) {
                            $exp_count = count($exp);
                        }

                        DB::table('communication_posts')->where('id', $post->id)->update(['is_mail_sent'=>2, 'sent_count' => $exp_count]);
                    }
                } else {
                    $exp_count = 0;
                    $exp = DB::table('notifications')->where(['post_id' => $post->id, 'type_no' => $type_no])->select('id')->get();
                    if($exp->isNotEmpty()) {
                        $exp_count = count($exp);
                    }
                    DB::table('communication_posts')->where('id', $post->id)->update(['is_mail_sent'=>2, 'sent_count' => $exp_count]);
                }

                /*  SCHOLAR */
                 
            }
        }
 
         
    }
}