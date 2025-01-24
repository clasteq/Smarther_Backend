<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command; 
use DB;
use App\Http\Controllers\CommonController; 
use App\Models\User;
use Log;
class sendPostSMSNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:postSMSNotification';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the User communication post Smart SMS notification';
    
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
        \Log::info("Cron User notification for post Smart Sms Communication!"); 
        $date = date('Y-m-d H:i:s');
        $posts = DB::table('communication_sms')->leftjoin('categories', 'categories.id', 'communication_sms.category_id') 
            ->leftjoin('dlt_templates', 'dlt_templates.id', 'communication_sms.template_id') 
            ->where('communication_sms.delete_status', 0)->whereIn('communication_sms.status', ["ACTIVE"])
            ->where('is_mail_sent', 0)->where('notify_datetime', '<=', $date)
            ->select('communication_sms.id', 'communication_sms.template_id as sms_template_id', 'communication_sms.content', 
                'communication_sms.content_vars', 'communication_sms.category_id', 'communication_sms.batch', 
                'communication_sms.post_type',  'receiver_end', 'notify_datetime', 'communication_sms.posted_by', 
                'smart_sms', 'categories.name as category_name',
                'dlt_templates.template_id', 'dlt_templates.content as dlt_content', 'dlt_templates.no_of_variables')
            ->orderby('communication_sms.id', 'desc')
            ->skip(0)->take(1)
            ->get(); 
        if($posts->isNotEmpty()) {

            foreach($posts as $post) {   

                DB::table('communication_sms')->where('id', $post->id)->update(['is_mail_sent'=>1]);

                $template_content = $is_name_replace_var = '';          $is_name_replace = 0;
                $dlt_templates = DB::table('dlt_templates')->where('id', $post->sms_template_id)->get(); 
                if($dlt_templates->isNotEmpty()) {
                    $tplate = $dlt_templates[0];
                    $template_content = $tplate->content;
                    $is_name_replace = $tplate->is_name_replace;
                    $is_name_replace_var = $tplate->is_name_replace_var; 
                }

                $users = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0) 
                    ->select('users.id', 'users.fcm_id', 'users.mobile', 'users.code_mobile', 'users.is_app_installed', 
                        'users.name', 'students.father_name', 'users.admission_no'
                    ); 

                if($post->post_type == 1) { // section ids
                    $section_ids = $post->receiver_end;
                    if(!empty($section_ids)) {
                        $section_ids = explode(',', $section_ids);
                        $section_ids = array_unique($section_ids);
                        $section_ids = array_filter($section_ids);
                        if(count($section_ids) > 0) {
                            $users->whereIn('students.section_id', $section_ids);
                        }
                    }
                }   else if($post->post_type == 2) { // user ids
                    $user_ids = $post->receiver_end;
                    if(!empty($user_ids)) {
                        $user_ids = explode(',', $user_ids);
                        $user_ids = array_unique($user_ids);
                        $user_ids = array_filter($user_ids);
                        if(count($user_ids) > 0) {
                            $users->whereIn('students.user_id', $user_ids);
                        }
                    }
                }   else if($post->post_type == 3) { // all user ids 

                }   else if($post->post_type == 4) { // group ids 
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
                            $users->whereIn('students.user_id', $user_ids);
                        }
                    }
                }   

                $users = $users->where('users.school_college_id', $post->posted_by)
                ->groupby('users.id')
                ->orderby('id', 'asc')->get();  // echo "<pre>"; print_r($users); exit;
                \Log::info(print_r($users, true));
                $pk = 0;
                if($users->isNotEmpty()) {
                    $users_count = count($users);
                    DB::table('communication_sms')->where('id', $post->id)->update(['users_count'=>$users_count]);

                    $exsentcount = DB::table('communication_sms')->where('id', $post->id)->value('sent_count');
                    if($exsentcount > 0) {} else { $exsentcount = 0; }

                    foreach($users as $user) {  $pk = $pk + 1;
                        $subst = 'Parent';
                        if(!empty($user->father_name)) {
                            $subst = $user->father_name;
                        } elseif(!empty($user->name)) {
                            $subst = $user->name;
                        } else {
                            $subst = $user->admission_no;
                        } 

                        $ex = DB::table('notifications')->where(['post_id' => $post->id, 'user_id' => $user->id, 'type_no' => 5])->first();
                        if(empty($ex)) {
                            //$pk = $pk + 1;

                            $type_no = 5;
                            $title = $post->category_name;
                            $message = $post->content;  

                            if($is_name_replace == 1) {
                                $str = $is_name_replace_var;
                                $re = '/'.$str.'/mi';  

                                $message = preg_replace($re, $subst, $message);
                            }
 
                            $fcmMsg = array("fcm" => array("notification" => array(
                                "title" => $title,
                                "body" => $message,
                                "type" => $type_no,
                              )));

                            CommonController::push_notification($user->id, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $post->notify_datetime);  

                            if($post->smart_sms == 1) { 

                                $is_app_installed = $user->is_app_installed;
                                if($is_app_installed == 0) {

                                    $exsentcount++;

                                    $template_id = $post->template_id;
                                    $dlt_content = $post->dlt_content;
                                    $no_of_variables  = $post->no_of_variables;
                                    $content_vars = $post->content_vars;
                                    
                                    $recipients = [ 'mobiles'=> $user->code_mobile ]; 
                                    if(!empty($content_vars)) {
                                        $content_vars = unserialize($content_vars);  
                                        if($no_of_variables > 0) {

                                            for($i=0; $i< $no_of_variables; $i++) {
                                                $j = $i + 1;   
                                                if($post->sms_template_id == 1 && $i == 1) {
                                                    $content_vars[$i] = $content_vars[$i].'?id='.$user->id;
                                                }
                                                if($is_name_replace == 1) {
                                                    $str = $is_name_replace_var;
                                                    if($str == '##var'.$j.'##') {
                                                        $recipients['var'.$j] = $subst;
                                                    } else {
                                                        $recipients['var'.$j] = $content_vars[$i];
                                                    }
                                                } else {
                                                    $recipients['var'.$j] = $content_vars[$i];
                                                }
                                            }
                                        }

                                    }
                                    $recipients_arr = [];
                                    $recipients_arr[] = $recipients;
                                    $post_fields = ['template_id' => $template_id, 'short_url' => 0, 'recipients' => $recipients_arr ]; 
                                     \Log::info(print_r($post_fields, true));
                                    CommonController::SendSMS($post_fields, $post->posted_by);

                                }
                            } else if($post->smart_sms == 0) { 
                                $exsentcount++;

                                $template_id = $post->template_id;
                                $dlt_content = $post->dlt_content;
                                $no_of_variables  = $post->no_of_variables;
                                $content_vars = $post->content_vars;

                                $recipients = [ 'mobiles'=> $user->code_mobile ]; 

                                if(!empty($content_vars)) {
                                    $content_vars = unserialize($content_vars);  
                                    if($no_of_variables > 0) {

                                        for($i=0; $i< $no_of_variables; $i++) {
                                            $j = $i + 1;
                                            if($post->sms_template_id == 1 && $i == 1) {
                                                $content_vars[$i] = $content_vars[$i].'?id='.$user->id;
                                            }
                                            if($is_name_replace == 1) {
                                                $str = $is_name_replace_var;
                                                if($str == '##var'.$j.'##') {
                                                    $recipients['var'.$j] = $subst;
                                                } else {
                                                    $recipients['var'.$j] = $content_vars[$i];
                                                }
                                            } else {
                                                $recipients['var'.$j] = $content_vars[$i];
                                            } 
                                        }
                                    }

                                }
                                $recipients_arr = [];
                                $recipients_arr[] = $recipients;
                                $post_fields = ['template_id' => $template_id, 'short_url' => 0, 'recipients' => $recipients_arr ]; 
                                 \Log::info(print_r($post_fields, true));
                                CommonController::SendSMS($post_fields, $post->posted_by);
                            }
                            DB::table('communication_sms')->where('id', $post->id)->update(['sent_count' => $exsentcount]);

                        }
                        /*if($post->smart_sms == 1) {
                            $ex = DB::table('notifications')->where(['post_id' => $post->id, 'user_id' => $user->id, 'type_no' => 5])->first();
                            if(empty($ex)) {
                                $pk = $pk + 1;

                                $type_no = 5;
                                $title = $post->category_name;
                                $message = $post->content;
                                $fcmMsg = array("fcm" => array("notification" => array(
                                    "title" => $title,
                                    "body" => $message,
                                    "type" => $type_no,
                                  )));

                                CommonController::push_notification($user->id, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $post->notify_datetime); 
                            }
                        } else {
                            $ex = DB::table('notifications')->where(['post_id' => $post->id, 'user_id' => $user->id, 'type_no' => 6])->first();
                            if(empty($ex)) {
                                $pk = $pk + 1;

                                $type_no = 6;
                                $title = $post->category_name;
                                $message = $post->content;
                                $fcmMsg = array("fcm" => array("notification" => array(
                                    "title" => $title,
                                    "body" => $message,
                                    "type" => $type_no,
                                  )));

                                CommonController::push_notification($user->id, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $post->notify_datetime); 
                            }
                        }*/
                    }
                    if($pk == 0 || $pk == $users_count) {
                        $exp_count = 0;
                        $exp = DB::table('notifications')->where(['post_id' => $post->id, 'type_no' => 5])->select('id')->get();
                        if($exp->isNotEmpty()) {
                            $exp_count = count($exp);
                        }

                        DB::table('communication_sms')->where('id', $post->id)->update(['is_mail_sent'=>2, 
                            'sent_count' => $exsentcount]);

                        /*DB::table('communication_sms')->where('id', $post->id)->update(['is_mail_sent'=>2, 'sent_count' => $exp_count]);*/
                    }
                } else {
                    $exp_count = 0;
                    $exp = DB::table('notifications')->where(['post_id' => $post->id, 'type_no' => 5])->select('id')->get();
                    if($exp->isNotEmpty()) {
                        $exp_count = count($exp);
                    }

                    DB::table('communication_sms')->where('id', $post->id)->update(['is_mail_sent'=>2, 'sent_count' => $exp_count]);
                }
 
                 
            }
        }
 
         
    }
}