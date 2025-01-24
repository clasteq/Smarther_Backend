<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command; 
use DB;
use App\Http\Controllers\AdminController; 
use App\Models\User;
use Log;
class updateFeesValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:feesvalues';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the student_class_mappings for updating fees values';
    
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
        \Log::info("Cron for updating fees values!"); 
        $date = date('Y-m-d');
        
        $pending = DB::table('student_class_mappings')->where('update_fees', 1)->where('status', 'ACTIVE')
            ->where('school_id', '>', 0)->skip(0)->take(2)->orderby('id', 'desc')->get();  
        if($pending->isNotEmpty()) { 
            foreach($pending as $ward) {     
                $feestatus = AdminController::getStudentFeeDetails($ward->school_id, $ward->user_id, $ward->academic_year);
                 
                if(!empty($feestatus) && is_array($feestatus) && count($feestatus)>0) {
                    $feedata = $feestatus['feedata'];
                    DB::table('student_class_mappings')->where('id', $ward->id)->update([
                        'total_fees' => $feedata['scholar_fees_total'],
                        'concession_fees' => $feedata['scholar_fees_concession'],
                        'paid_fees' => $feedata['scholar_fees_paid'],
                        'balance_fees' => $feedata['scholar_fees_balance'],
                        'deleted_fees' => $feedata['scholar_fees_deleted'],
                        'update_fees' => 0,
                    ]); 
                }
            }
        }
    }
}