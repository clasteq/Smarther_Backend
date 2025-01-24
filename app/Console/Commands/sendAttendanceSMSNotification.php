<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command; 
use DB;
use App\Http\Controllers\CommonController; 
use App\Models\User;
use Log;
class sendAttendanceSMSNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:attendancesmsNotification';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the User attendance and send sms if absent notification';
    
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
        $posts = DB::table('notifications')
            ->leftjoin('users', 'users.id', 'notifications.user_id') 
            ->leftjoin('users as school', 'school.id', 'users.school_college_id') 
            ->where('users.user_type', 'STUDENT')
            ->where('users.status', 'ACTIVE')->where('users.delete_status', 0)
            ->where('type_no', 1)->where('message', 'like', '%Absent%')
            ->where('is_acknowledged', 0)->where('notify_date', '<=', $date)
            ->select('notifications.*', 'users.name', 'users.code_mobile', 'users.school_college_id', 'school.display_name', 'school.mobile')
            ->orderby('notifications.id', 'desc')
            ->skip(0)->take(50)
            ->get(); 
        if($posts->isNotEmpty()) {
            foreach($posts as $post) {    
                $template_id = '66756ee9d6fc0515d3687e33';  
                $school_name = $post->display_name; 
                $school_id = $post->school_college_id; 
                if(strlen($school_name)>=11) {
                    $school_name = substr($school_name,0,10).'..';
                }
                $name = $post->name; 
                if(strlen($name)>=18) {
                    $name = substr($name,0,15).'..';
                }
                $var1 = $school_name.', '.$name; 
                $mobile = $post->code_mobile; 
                $var2 = $post->mobile;  $var3 = $post->display_name; 

                $recipients = [ 'mobiles'=> $mobile, 'var1' => $var1, 'var2' => $var2, 'var3' => $var3];    
                $recipients_arr = [];
                $recipients_arr[] = $recipients;
                $post_fields = ['template_id' => $template_id, 'short_url' => 0, 'recipients' => $recipients_arr ]; 
                 \Log::info(print_r($post_fields, true));
                CommonController::SendSMS($post_fields, $school_id); 

                DB::table('notifications')->where('id', $post->id)->update(['is_acknowledged'=>1]);
             
            }  
        }
    }
}