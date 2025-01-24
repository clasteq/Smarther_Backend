<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Tests extends Model
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
    protected $table = 'tests';

    protected $appends = ['is_test_write','test_items', 'no_of_questions','test_list','selected_test','notes_file','new_test_items','new_test_list', 'is_section_names', 'is_created_by', 'is_auto_manual', 'attempted_students'];

    public function getAttemptedStudentsAttribute() { 
          //$attempted_students = DB::table('student_tests')->where('test_id', $this->id)->select('id')->groupby('user_id')->get();
          $attemptedstudents = DB::table('student_tests')->leftjoin('users', 'users.id', 'student_tests.user_id')
                ->leftjoin('students', 'students.user_id', 'student_tests.user_id')
                ->leftjoin('terms', 'terms.id', 'student_tests.term_id')
                ->leftjoin('classes', 'classes.id', 'student_tests.class_id')
                ->leftjoin('subjects', 'subjects.id', 'student_tests.subject_id')
                ->leftjoin('tests', 'tests.id', 'student_tests.test_id')
                ->where('tests.id', $this->id)
                ->select('student_tests.id')
                ->groupby('student_tests.user_id')
                ->get(); //if($this->id == 588) {echo "<pre>"; print_r($attemptedstudentsarr);}   
          if($attemptedstudents->isNotEmpty())  {
               $attemptedstudentsarr = $attemptedstudents->toArray();
               $attempted_students = count($attemptedstudentsarr);
          }    else {
               $attempted_students = 0;
          }
          //echo "<pre>"; print_r($attempted_students); exit;
          return $attempted_students;
    }

     public function getTestItemsAttribute() { 

          return TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
               ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
               ->where('test_id', $this->id)
               ->where('test_items.status','ACTIVE')
               ->where('question_banks.deleted_status',0)
               ->where('question_bank_items.deleted_status',0)
               ->select('question_type_id', 'question_type', 'test_id')
               ->orderby('question_type_id', 'asc') 
               ->groupby('question_type_id')->groupby('question_type')
               
               ->get(); 
     }

     public function getNewTestItemsAttribute(){
          return TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
          ->leftjoin('tests', 'tests.id', 'test_items.test_id')
          ->where('test_id', $this->id)
          ->where('test_items.status','ACTIVE')
          ->where('question_bank_items.deleted_status',0)
          ->select('question_type_id', 'question_type', 'test_id','tests.class_id','question_bank_items.question_bank_id')
          ->orderby('question_type_id', 'asc') 
          ->groupby('question_type_id')->groupby('question_type')
          ->get();
     }

     public function getIsSectionNamesAttribute() {
          $is_section_names = ''; $section_id = [];
          if(!empty($this->section_ids)) {
               $section_id = explode(',', $this->section_ids);
          }
          if(is_array($section_id) && count($section_id)>0) {
               $section_ids = implode(',', $section_id);
               $sec = DB::table('sections')->whereIn('id', $section_id)->select('section_name')->get();
               if($sec->isNotEmpty()) {
                    foreach($sec as $sn) {
                        $is_section_names .= $sn->section_name.', ';
                    }
               }
          } 
          return $is_section_names;
     }

     public function getNoOfQuestionsAttribute() {
          $no_of_questions = DB::table('test_items')->where('test_id', $this->id)->where('status','ACTIVE')->count('id');
          return $no_of_questions;
     }
    

     public function getTestListAttribute() {
          $qb_items =TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
          ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
          ->where('test_id', $this->id)
          ->where('test_items.status','ACTIVE')
          ->where('question_banks.deleted_status',0)
          ->where('question_bank_items.deleted_status',0)
          ->select('question_type_id', 'question_type', 'test_id')
          ->orderby('question_type_id', 'asc') 
          ->groupby('question_type_id')->groupby('question_type')
          
          ->get();

          if(!empty($qb_items)) {
               foreach($qb_items as $qk=>$qitem) {
                  $question_type_id = $qitem->question_type_id ;
                  $class_id = $qitem->class_id;
                  $question_type = $qitem->question_type;
                
                 $answer = DB::table('question_bank_items')->leftjoin('question_banks','question_banks.id','question_bank_items.question_bank_id')
                 ->where('question_banks.class_id',$class_id)
                 ->where('question_bank_items.question_type',$question_type)
               ->where('question_bank_items.question_type_id',$question_type_id)->select('question_bank_items.*')->get();

                          
               //   $answer = DB::table('question_bank_items')
               // ->where('question_type_id',$question_type_id)->get();

               $qb_items[$qk]->question_bank = $answer;

               }

               return $qb_items;
          }

     }

     public function getNewTestListAttribute() {
          $qb_items = TestItems::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
          ->leftjoin('tests', 'tests.id', 'test_items.test_id')
          ->where('test_id', $this->id)
          ->where('test_items.status','ACTIVE')
          ->where('question_bank_items.deleted_status',0)
          ->select('question_type_id', 'question_type', 'test_id','tests.class_id','question_bank_items.question_bank_id')
          ->orderby('question_type_id', 'asc') 
          ->groupby('question_type_id')->groupby('question_type')
          ->get();

          if(!empty($qb_items)) {
               foreach($qb_items as $qk=>$qitem) {
                  $question_type_id = $qitem->question_type_id ;
                  $class_id = $qitem->class_id;
                  $question_type = $qitem->question_type;
                
                 $answer = DB::table('question_bank_items')->leftjoin('question_banks','question_banks.id','question_bank_items.question_bank_id')
                 ->where('question_banks.class_id',$class_id)
                 ->where('question_bank_items.question_type',$question_type)
               ->where('question_bank_items.question_type_id',$question_type_id)->select('question_bank_items.*')->get();

                          
               //   $answer = DB::table('question_bank_items')
               // ->where('question_type_id',$question_type_id)->get();

               $qb_items[$qk]->question_bank = $answer;

               }

               return $qb_items;
          }

     }

     public function getSelectedTestAttribute(){
        return  $qb_items = DB::table('test_items')
          ->where('test_id', $this->id)
          ->where('status','ACTIVE')
          ->select('question_bank_item_id','mark','id')
           ->get();

        
     }

     public function getIsTestWriteAttribute(){
          $date = date('Y-m-d');
          $qb_items = DB::table('tests')->whereRaw("'".$date."' BETWEEN from_date and to_date")->where('id',$this->id)->get();
            if($qb_items->isNotEmpty()){
               return 1;
            }else{
               return 0;
            }
  
          
       }

       public function getNotesFileAttribute(){

           $qb_items = DB::table('test_items')->leftjoin('question_bank_items', 'question_bank_items.id', 'test_items.question_bank_item_id')
          ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
          ->where('test_id', $this->id)
          ->where('test_items.status','ACTIVE')
          ->where('question_banks.deleted_status',0)
          ->where('question_bank_items.deleted_status',0)
          ->select('test_id','question_banks.id as question_bank_id')
          ->orderby('question_type_id', 'asc') 
          ->distinct()          
          ->get();

          $testids = array();
          foreach($qb_items as $k=>$v){
            array_push($testids,$v->question_bank_id);
          }

          return  DB::table('question_banks')
          ->whereIn('id', $testids)
          ->where('status','ACTIVE')
          ->select('notes')->get();

          
          
       }

     public function getCreatedAtAttribute($value) {
          return date('d M, Y',strtotime($value));
     }

     public function getIsCreatedByAttribute($value) {
          $is_created_by = '';
          if($this->created_by > 0) {
               $user_type = DB::table('users')->where('id', $this->created_by)->value('user_type');
               if($user_type == 'TEACHER') {
                    $is_created_by = 'Teacher';
               }    else if ($user_type == 'SUPER_ADMIN') {
                    $is_created_by = 'Teacher';
               }    else {
                    $is_created_by = 'Self';
               }
          }
          return $is_created_by;
     }

     public function getIsAutoManualAttribute($value) {
          $is_auto_manual = ' '; 
          if($this->manual_auto == 1) {
               $is_auto_manual = 'Manual';
          }    else if ($this->manual_auto == 2) {
               $is_auto_manual = 'Auto';
          }    else {
               $is_auto_manual = 'Manual';
          } 
          return $is_auto_manual;
     }
    

}