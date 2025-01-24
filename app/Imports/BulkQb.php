<?php
namespace App\Imports;
use App\Bulk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
use DB;
use App\User;
use App\QuestionBanks; 
 
use Hash;
use Auth;

class BulkQb implements ToModel,WithHeadingRow
{   

    public function model(array $row) {  
        // dd($row); 
        try{
        
           
            if($row['qb_id'] > 0) { 
                $data = [];
                $data['question_bank_id'] = $row['qb_id']; 
                $data['question_type_id'] = $row['question_type_id']; 
                $data['question_type'] = $row['question_type'];
                if($row['question'] != ''){
                $data['question'] = $row['question'];
                }
                else{
                    $data['question'] = '';
                }
                if($row['answer'] != ''){
                    $data['answer'] = $row['answer'];
                    }
                    else{
                        $data['answer'] = '';
                    }
                // $data['answer'] = $row['answer'];
                $data['option_1'] = $row['option_1'];
                $data['option_2'] = $row['option_2'];
                $data['option_3'] = $row['option_3'];
                $data['option_4'] = $row['option_4'];
                $data['question_file'] =  $row['question_file']; 

                if($row['question_bank_item_id'] > 0) { 

                    $data['updated_by'] = Auth::User()->id;   
                    $data['updated_at'] = date('Y-m-d H:i:s');  

                    DB::table('question_bank_items')->where('id', $row['question_bank_item_id'])->update($data);
                
                }   elseif($row['question_bank_item_id'] == 0) {
                
                    $data['created_by'] = Auth::User()->id;   
                    $data['created_at'] = date('Y-m-d H:i:s');   

                    DB::table('question_bank_items')->insert($data);
                    
                }
            }

        } catch(\Illuminate\Database\QueryException $ex){ 
            return response()->json(['status' => 'FAILED', 'message' => dd($ex->getMessage())]);//dd();  
        }
    }

 
}