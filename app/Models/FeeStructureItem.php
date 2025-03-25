<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class FeeStructureItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'fee_structure_items';

    protected $appends = [ 'is_receivers', 'is_term_name', 'is_for', 'is_gender', 'is_type'];

    public function getIsForAttribute() {
        $is_for = '';
        $fee_post_type = $this->fee_post_type; 
        if($fee_post_type == 1) {
            $is_for = 'Class';
        } else if($fee_post_type == 2) {
            $is_for = 'Sections';
        } else if($fee_post_type == 3) {
            $is_for = 'All';
        } else if($fee_post_type == 4) {
            $is_for = 'Group';
        } else if($fee_post_type == 5) {
            $is_for = 'Scholar';
        } else {
            $is_for = '';
        }
        return $is_for;
    }

    public function getIsGenderAttribute() {
        $is_gender = '';
        $gender = $this->gender; 
        if($gender == 1) {
            $is_gender = 'All';
        } else if($gender == 2) {
            $is_gender = 'Boys';
        } else if($gender == 3) {
            $is_gender = 'Girls';
        } else {
            $is_gender = '';
        }
        return $is_gender;
    }

    public function getIsTypeAttribute() {
        $is_type = '';
        $fee_type = $this->fee_type; 
        if($fee_type == 1) {
            $is_type = 'Mandatory';
        } else if($fee_type == 2) {
            $is_type = 'Variable';
        } else if($fee_type == 3) {
            $is_type = 'Optional';
        } else {
            $is_type = '';
        }
        return $is_type;
    }

    public function getIsTermNameAttribute() {
        return DB::table('fee_terms')->where('id', $this->fee_term_id)->value('name');
    }

    public function feeItem()   {
        return $this->belongsTo('App\Models\FeeItems', 'fee_item_id', 'id')->select('id','item_name','category_id');
    } 

    public function getIsReceiversAttribute() {

        $post  = DB::table('fee_structure_lists')->where('id', $this->fee_structure_id)->first();

        $is_receivers = [];
        if(!empty($post)) {
            $post_type = $post->fee_post_type;
            $receiver_end = $post->class_list;
            if($post_type == 1) { // section ids
                $class_ids = $post->class_list;
                if(!empty($class_ids)) {
                    $class_ids = explode(',', $class_ids);
                    $class_ids = array_unique($class_ids);
                    $class_ids = array_filter($class_ids);
                    if(count($class_ids) > 0) {
                        $is_receivers = DB::table('classes')->where('classes.status','ACTIVE')
                            ->whereIn('classes.id',$class_ids)
                            ->select('classes.class_name as name', DB::RAW('"" as name1'))->get(); 
                    }
                }
            }   else if($post_type == 2) { // user ids
                $section_ids = $post->class_list;
                if(!empty($section_ids)) {
                    $section_ids = explode(',', $section_ids);
                    $section_ids = array_unique($section_ids);
                    $section_ids = array_filter($section_ids);
                    if(count($section_ids) > 0) {
                        $is_receivers = DB::table('sections')
                            ->leftjoin('classes', 'classes.id', 'sections.class_id')
                            ->where('sections.status','ACTIVE')->whereIn('sections.id',$section_ids)
                            ->select('section_name as name', 'classes.class_name as name1')->get(); 
                    }
                }
            }   else if($post_type == 3) { // all user ids 

            }   else if($post_type == 4) { // group ids 
                $group_ids = $post->class_list;
                if(!empty($group_ids)) {
                    $user_ids = [];
                    $group_ids = explode(',', $group_ids);
                    $group_ids = array_unique($group_ids);
                    $group_ids = array_filter($group_ids);
                    if(count($group_ids) > 0) { 
                        $is_receivers = DB::table('communication_groups')->where('status','ACTIVE')->whereIn('id',$group_ids)
                            ->select('group_name as name', DB::RAW('"" as name1'))->get();  
                    } 
                }
            }   else if($post_type == 5) { // student ids 
                $student_ids = $post->class_list;
                if(!empty($student_ids)) {
                    $user_ids = [];
                    $student_ids = explode(',', $student_ids);
                    $student_ids = array_unique($student_ids);
                    $student_ids = array_filter($student_ids);
                    if(count($student_ids) > 0) { 
                        $is_receivers = DB::table('users')->where('status','ACTIVE')->whereIn('id',$student_ids)
                            ->select('name', DB::RAW('"" as name1'))->get();  
                    } 
                }
            }
        }
        if(!empty($is_receivers) && $is_receivers->isNotEmpty()) {
            $is_receivers = $is_receivers->toArray();
        }
        return  $is_receivers;
    }

    public static function getBalance($fee_structure_item_id, $batch, $student_id, $school_id) {

        $item_balance = $class_id  = $section_id = 0;
        $studdetails  = DB::table('students')->where('user_id', $student_id)->select('class_id', 'section_id')->first();
        if(!empty($studdetails)) {
            $class_id  = $studdetails->class_id;
            $section_id  = $studdetails->section_id;
        } 

        $student_user_id = $student_id;

        $fee_structure_id  = DB::table('fee_structure_items')->where('id', $fee_structure_item_id)->value('fee_structure_id');
        $studentId = $student_id;

        FeeStructureList::$fee_structure_item_id = $fee_structure_item_id;
        FeeStructureList::$student_id = $student_id;
        $feeStructures = FeeStructureList::with(['feeItems.feeItem'])
            ->where('school_id', $school_id)
            ->where('batch', $batch)
            ->where('id', $fee_structure_id)
            ->get();
        //echo "<pre>"; print_r($feeStructures->toArray()); //exit;
        // Fetch paid records for the student
        $get_paid_records = FeesPaymentDetail::where('student_id', $studentId)
            ->where('fee_structure_id', $fee_structure_id)->where('fee_structure_item_id', $fee_structure_item_id)
            ->where('cancel_status', 0)->get();

        // Map paid records by fee_structure_item_id
        $paid_records_map = [];
        foreach ($get_paid_records as $record) {
            if (!isset($paid_records_map[$record->fee_structure_item_id])) {
                $paid_records_map[$record->fee_structure_item_id] = [
                    'amount_to_pay' => 0,
                    'total_paid' => 0,
                    'payment_status' => $record->payment_status,
                    'total_concession' => 0,
                ];
            }
            $paid_records_map[$record->fee_structure_item_id]['amount_to_pay'] += $record->amount_to_pay;
            $paid_records_map[$record->fee_structure_item_id]['total_paid'] += $record->amount_paid;
            $paid_records_map[$record->fee_structure_item_id]['total_concession'] += $record->concession_amount;
        }

        // Process fee structures based on fee_post_type
        $studentFeeStructures = [];
        foreach ($feeStructures as $feeStructure) {
            $fee_post_type = $feeStructure->fee_post_type;
            $class_list = explode(',', $feeStructure->class_list);

            $appliesToStudent = false;
            switch ($fee_post_type) {


                case 1: // Class
                    $appliesToStudent = in_array($class_id, $class_list);
                    break;
                case 2: // Section
                    $appliesToStudent = in_array($section_id, $class_list);
                    break;
                case 3: // All
                    $appliesToStudent = true;
                    break;
                case 4: // Group
                    $communicationGroups = CommunicationGroup::all();
                    foreach ($communicationGroups as $group) {
                        $members = explode(',', $group->members);

                     //   dd($members);
                        if (in_array($studentId, $members)) {
                            $appliesToStudent = in_array($group->id, $class_list);
                            if ($appliesToStudent) {
                                break;
                            }
                        }
                    }
                    break;
                case 5: // Student
                    $appliesToStudent = in_array($student_user_id, $class_list);
                    break;
            }

            if ($appliesToStudent) {
                foreach ($feeStructure->feeItems as $feeItem) { 

                    $fee_item_id = $feeItem->id;
                    $fee_amount = $feeItem->amount;
                    $fee_status_flag = 0;
                    $total_paid = 0; $total_concession = 0;
                    $balance_amount = $fee_amount;

                    if (isset($paid_records_map[$fee_item_id])) {

                        if($paid_records_map[$fee_item_id]['amount_to_pay'] > 0)  { 
                            $fee_amount = $paid_records_map[$fee_item_id]['amount_to_pay'];
                        }   else { 
                            $fee_amount = $fee_amount;
                        } 

                        $total_paid = $paid_records_map[$fee_item_id]['total_paid'];
                        $balance_amount = max($fee_amount - $total_paid, 0);
                        $total_concession = $paid_records_map[$fee_item_id]['total_concession']; 

                        if ($balance_amount == 0) {
                            $fee_status_flag = 1; // Fully paid
                        } elseif ($balance_amount < $fee_amount) {
                            $fee_status_flag = 2; // Partially paid
                        }
                    }

                    $feeItem->payment_status_flag = $fee_status_flag;
                    $feeItem->balance_amount = $balance_amount - $total_concession;
                    $feeItem->paid_amount = $total_paid;
                    $feeItem->concession_amount = $total_concession;

                    $item_balance = $balance_amount - $total_concession;
                }
                $studentFeeStructures[] = $feeStructure;
            }
        } 
        return $item_balance;      
    }
   
}
