<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TestPapers extends Model
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
    protected $table = 'test_papers';

    protected $appends = ['is_test_write','test_items', 'no_of_questions','test_list','selected_test','notes_file','new_test_items','new_test_list', 'is_section_names'];

     public function getTestItemsAttribute() { 

          return TestItemsPapers::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
               ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
               ->where('test_id', $this->id)
               ->where('test_items_papers.status','ACTIVE')
               ->where('question_banks.deleted_status',0)
               ->where('question_bank_items.deleted_status',0)
               ->select('question_type_id', 'question_type', 'test_id')
               ->orderby('question_type_id', 'asc') 
               ->groupby('question_type_id')->groupby('question_type')
               ->get();

      

     }

     public function getNewTestItemsAttribute(){
          $no_of_papers = DB::table('test_papers')->where('id', $this->id)->value('no_of_papers');
          if($no_of_papers > 1) {
               $new_test_items = [];
               for($no=1; $no <= $no_of_papers; $no++) {
                    $new_test_items[] = TestItemsPapers::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
                    ->leftjoin('tests', 'tests.id', 'test_items_papers.test_id')
                    ->where('test_id', $this->id)->where('test_items_papers.test_no', $no)
                    ->where('test_items_papers.status','ACTIVE')
                    ->where('question_bank_items.deleted_status',0)
                    ->select('question_type_id', 'question_type', 'test_id','tests.class_id','question_bank_items.question_bank_id', 'test_no')
                    ->orderby('question_type_id', 'asc') 
                    ->groupby('question_type_id')->groupby('question_type')
                    ->get()->toArray();
               }
               return $new_test_items;
          }    else {
               return TestItemsPapers::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
                    ->leftjoin('tests', 'tests.id', 'test_items_papers.test_id')
                    ->where('test_id', $this->id)
                    ->where('test_items_papers.status','ACTIVE')
                    ->where('question_bank_items.deleted_status',0)
                    ->select('question_type_id', 'question_type', 'test_id','tests.class_id','question_bank_items.question_bank_id', 'test_no')
                    ->orderby('question_type_id', 'asc') 
                    ->groupby('question_type_id')->groupby('question_type')
                    ->get();
          }
          
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
          $no_of_questions = DB::table('test_items_papers')->where('test_id', $this->id)
               ->where('status','ACTIVE')->count('id');
          return $no_of_questions;
     }
    

     public function getTestListAttribute() {
          $qb_items = TestItemsPapers::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
          ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
          ->where('test_id', $this->id)
          ->where('test_items_papers.status','ACTIVE')
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
          $qb_items = TestItemsPapers::leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
          ->leftjoin('tests', 'tests.id', 'test_items_papers.test_id')
          ->where('test_id', $this->id)
          ->where('test_items_papers.status','ACTIVE')
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
        return  $qb_items = DB::table('test_items_papers')
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

           $qb_items = DB::table('test_items_papers')->leftjoin('question_bank_items', 'question_bank_items.id', 'test_items_papers.question_bank_item_id')
          ->leftjoin('question_banks', 'question_banks.id', 'question_bank_items.question_bank_id')
          ->where('test_id', $this->id)
          ->where('test_items_papers.status','ACTIVE')
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
    

}