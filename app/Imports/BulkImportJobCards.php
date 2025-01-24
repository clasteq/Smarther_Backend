<?php
namespace App\Imports;
use App\Bulk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use DB;
use App\User;
use App\Employees;
use App\Models;
use App\Ratings;
use App\Jobcards;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\QRController;
use Hash;
use Auth;

class BulkImportJobCards implements ToModel,WithHeadingRow
{   

    public function model(array $row) {   
        try{
        if(!empty($row['workorder_number'])) { 
            $data = [];   
            if(Auth::check()){
                $job_creator_id = Auth::User()->id; 

                $creator = Employees::where('user_id', $job_creator_id)->first();

                $branch_id = $creator->branch_id;
                $department_id = $creator->department_id;

                $sfg_code = $row['sfg_code'];  
                $revision = $row['revision']; 
                if(empty($revision)) {
                    $revision = 0;
                }
                $system_serial_number = $row['system_serial_number']; 
                $workorder_number = $row['workorder_number']; 
                $model_code = $row['model_code']; 
                $model_id = Models::where('model_code', $model_code)->value('id');

                $rating_code = $row['rating_code']; 
                $rating_id = Ratings::where('rating_code', $rating_code)->value('id'); 

                $specification = $row['specification']; 
                $job_description = $row['job_description']; 

                $kitting_date = $row['kitting_date']; 
                $inspection_date = $row['inspection_date']; 
                $delivery_date = $row['delivery_date']; 

                /*if(empty($kitting_date)) { $kitting_date = NULL; }
                if(empty($inspection_date)) { $inspection_date = NULL; }
                if(empty($delivery_date)) { $delivery_date = NULL; } */
                $status = $row['status']; 
                if($status == 'MOVED_TO_STORE') {
                    if(empty($kitting_date) || ($kitting_date == NULL)) {
                        $status = 'CREATED'; 
                    }
                }

                if($status == 'CREATED') {
                    $status = 1;
                }   else if($status == 'MOVED_TO_STORE') {
                    $status = 2;
                } 

                $date = date('Y-m-d H:i:s'); 

                $emptyjob = 0; 
                $job = Jobcards::where('system_serial_number', $system_serial_number)->first();
                
                if(empty($job)) {
                    $emptyjob = 1;

                    $job = new Jobcards(); 
                    $job->created_at = date('Y-m-d H:i:s');
                    $job->created_by = Auth::User()->id;

                    $lastjobid = DB::table('jobcards')
                        ->where('created_at', 'like', date('Y-m-d').'%')
                        ->orderby('id', 'desc')->count();
     
                    $lastjobid = $lastjobid + 1; 

                    $append = str_pad($lastjobid,4,"0",STR_PAD_LEFT);

                    $job->ref_no = date('Ymd').$append; 

                    $job->job_creator_id = $job_creator_id;

                    $job->qr_image = $job->ref_no.'.png';
                    if($_SERVER['HTTP_HOST'] != "localhost") {
                        (new QRController())->generateQrCode($job->ref_no);
                    };   

                    $job->created_by = $job_creator_id;
                    $job->created_at = date('Y-m-d H:i:s'); 
                } 
                 
                $job->branch_id = $branch_id;
                $job->department_id = $department_id;  
                $job->sfg_code = $sfg_code; 
                $job->revision = $revision;   
                $job->system_serial_number = $system_serial_number;
                $job->workorder_number = $workorder_number;
                $job->model_id = $model_id;
                $job->rating_id = $rating_id;
                $job->status = $status;
                $job->specification = $specification;
                $job->job_description = $job_description; 

                if(!empty($kitting_date) ) {
                    $unix_date = ($kitting_date - 25569) * 86400;
                    $kitting_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($kitting_date - 25569) * 86400;
                    $kitting_date = gmdate("Y-m-d", $unix_date);
                }
                 
                if(!empty($inspection_date) ) {
                    $unix_date = ($inspection_date - 25569) * 86400;
                    $inspection_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($inspection_date - 25569) * 86400;
                    $inspection_date = gmdate("Y-m-d", $unix_date);
                } 
                if(!empty($delivery_date) ) {
                    $unix_date = ($delivery_date - 25569) * 86400;
                    $delivery_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($delivery_date - 25569) * 86400;
                    $delivery_date = gmdate("Y-m-d", $unix_date);
                }   
 
                $job->kitting_date = $kitting_date;
                $job->inspection_date = $inspection_date;
                $job->delivery_date = $delivery_date; 
                $job->status_date = date('Y-m-d H:i:s');  
                $job->currently_assigned = 0;
                $job->updated_at = date('Y-m-d H:i:s'); 

                $job->save();              
                if(Auth::User()->user_type == 'FUJIROLE004') {
                    $team_leader_id = $creator->team_leader_id;
                    $employee_id = $job_creator_id;
                }   elseif(Auth::User()->user_type == 'FUJIROLE003') {
                    $team_leader_id = 0;
                    $employee_id = $job_creator_id;
                }   else {
                    $team_leader_id = 0;
                    $employee_id = $job_creator_id;
                }

                if($status == 1) {
                    $employee_status = 'Created';
                    $worked_time = '';
                } else if($status == 2) {
                    $worked_time = 1;
                    $employee_status = 'Moved to Store'; 
                } 

                if($emptyjob == 1) {
                    DB::table('jobcard_logs')->insert([
                        'branch_id' => $branch_id,
                        'department_id' => $department_id,
                        'team_leader_id' => $team_leader_id,
                        'employee_id' => $employee_id,
                        'jobcard_id' => $job->id,
                        'status' => $status,
                        'status_date' => date('Y-m-d H:i:s'),
                        'employee_status' => $employee_status,
                        'worked_time' => $worked_time,
                        'created_by' => $job_creator_id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);  

                    $currently_assigned = 0;  
                    $updated_by = 0;   

                    DB::table('jobcards_history')->insert([
                        'jobcard_id' => $job->id,
                        'ref_no' => $job->ref_no,
                        'branch_id' => $job->branch_id,
                        'department_id' => $job->department_id,
                        'job_creator_id' => $job->job_creator_id,
                        'sfg_code' => $job->sfg_code, 
                        'revision' => $job->revision,
                        'system_serial_number' =>  $job->system_serial_number,
                        'workorder_number' =>  $job->workorder_number,
                        'model_id' =>  $job->model_id,
                        'rating_id' =>  $job->rating_id,
                        'specification' =>  $job->specification,
                        'job_description' =>  $job->job_description,
                        'status' =>  $job->status,
                        'status_date' =>  $job->status_date,
                        'currently_assigned' =>  $currently_assigned,
                        'qr_image' =>  $job->qr_image,
                        'kitting_date' =>  $job->kitting_date,
                        'inspection_date' =>  $job->inspection_date,
                        'delivery_date' =>  $job->delivery_date,
                        'created_by' =>  $job->created_by,
                        'updated_by' =>  $updated_by,
                        'created_at' =>  $job->created_at,
                        'updated_at' => date('Y-m-d H:i:s'), 
                    ]);
                }

            } 
        }
        } catch(\Illuminate\Database\QueryException $ex){ 
            return response()->json(['status' => 'FAILED', 'message' => dd($ex->getMessage())]);//dd();  
        }
    }

 
}