<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command; 
use DB;
use App\Http\Controllers\CommonController; 
use App\Models\User;
use Log;
class sendAttendanceNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:attendanceNotification';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the User notification for send attendance approval';
    
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
        \Log::info("Cron User notification for send attendance approval!"); 
        $date = date('Y-m-d');
        
        $pending = DB::table('attendance_approval_class_section')->where('sent_notification', 0)
            ->whereDate('date', $date)->skip(0)->take(5)->get(); 
 
        if($pending->isNotEmpty()) {
            Log::info(print_r($pending, true)); 
            foreach($pending as $pen) {     
                $class_id = $pen->class_id;
                $section_id = $pen->section_id;

                $users = DB::table('users')->leftjoin('students', 'students.user_id', 'users.id') 
                    ->where('user_type', 'STUDENT')->where('students.delete_status', 0)
                    ->where('users.status','ACTIVE')
                    ->where('students.class_id', $pen->class_id)->where('students.section_id', $pen->section_id)
                    ->select('users.id', 'users.gender') 
                    ->get();
                Log::info(print_r($users, true)); 
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
                        }  /* else {
                            $fn_status = 2;
                            $an_status = 2;
                            $sent_notification = 0;
                            $exid = DB::table('attendance_approval')
                                ->insertGetId(['date'=>$date, 'class_id'=>$class_id, 
                                    'section_id'=>$section_id, 'user_id'=>$user->id,
                                    'fn_status'=>2, 'an_status'=>2, 'admin_status'=>1, 
                                    'sent_notification'=>1,
                                    'created_by'=>1, 
                                    'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }*/
                        if($sent_notification == 0) {
                            $ward = 'Ward';
                            if(strtolower($gender) == 'male') {
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
}