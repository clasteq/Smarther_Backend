<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command; 
use DB;
use App\Http\Controllers\CommonController; 
use App\Models\User;
use Log;
class sendPostNotification_fcmid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:postNotification';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the User communication post notification';
    
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
        \Log::info("Cron User notification for post Communication!"); 
        $date = date('Y-m-d H:i:s');
        $posts = DB::table('communication_posts')->whereIn('status', ["ACTIVE"]) 
            ->where('delete_status', 0)->where('is_mail_sent', 0)->where('notify_datetime', '<=', $date)
            ->select('id', 'title_push', 'message_push', 'batch', 'post_type', 'receiver_end', 'notify_datetime', 'posted_by')
            ->orderby('communication_posts.id', 'desc')
            ->skip(0)->take(1)
            ->get(); 
        if($posts->isNotEmpty()) {
            foreach($posts as $post) {   

                DB::table('communication_posts')->where('id', $post->id)->update(['is_mail_sent'=>1]);

                $users = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id')
                    ->where('users.user_type', 'STUDENT')->where('users.fcm_id', '!=', '')
                    ->where('users.status', 'ACTIVE')->where('users.delete_status', 0) 
                    ->select('users.id', 'users.fcm_id', 'users.name'); 

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
                    DB::table('communication_posts')->where('id', $post->id)->update(['users_count'=>$users_count]);
                    foreach($users as $user) { $pk = $pk + 1;

                        $ex = DB::table('notifications')->where(['post_id' => $post->id, 'user_id' => $user->id, 'type_no' => 4])->first();
                        if(empty($ex)) {
                            //$pk = $pk + 1;

                            $type_no = 4;
                            $title = $user->name.', '.$post->title_push;
                            $message = $post->message_push;
                            $fcmMsg = array("fcm" => array("notification" => array(
                                "title" => $title,
                                "body" => $message,
                                "type" => $type_no,
                              )));

                            CommonController::push_notification($user->id, $type_no, $post->id, $fcmMsg, 0, '', $post->id, $post->notify_datetime); 
                        }
                    }
                    if($pk == 0 || $pk == $users_count) {
                        $exp_count = 0;
                        $exp = DB::table('notifications')->where(['post_id' => $post->id, 'type_no' => 4])->select('id')->get();
                        if($exp->isNotEmpty()) {
                            $exp_count = count($exp);
                        }

                        DB::table('communication_posts')->where('id', $post->id)->update(['is_mail_sent'=>2, 'sent_count' => $exp_count]);
                    }
                } else {
                    $exp_count = 0;
                    $exp = DB::table('notifications')->where(['post_id' => $post->id, 'type_no' => 4])->select('id')->get();
                    if($exp->isNotEmpty()) {
                        $exp_count = count($exp);
                    }
                    DB::table('communication_posts')->where('id', $post->id)->update(['is_mail_sent'=>2, 'sent_count' => $exp_count]);
                }

                /*

                $package_title = DB::table('company_purchased_packages')->where('id', $pen->id)->value('package_title');
                $type_no = 7; 
                $title = 'Package Expire Reminder';
                $message = 'Package '.$package_title.' is about to Expire On '. date('d M, Y', strtotime($pen->package_end_date));
                $fcmMsg = array("fcm" => array("notification" => array(
                        "title" => $title,
                        "body" => $message,
                        "type" => $type_no,
                      ))); 
                
                CommonController::push_notification($pen->company_id, $type_no, $pen->id, $fcmMsg);  */
                 
            }
        }
 
         
    }
}